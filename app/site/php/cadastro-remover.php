<?php
//conexão com banco de dados da w2b e do web finanças
$link_w2b = mysqli_connect('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
$link_wf = mysqli_connect('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
$link_wf_w2b = mysqli_connect('mysql.webfinancas.com','webfinancas02','W2BSISTEMAS','webfinancas02');

//======================================================================================================
//Exclui usuário inativo por mais de 24h após ter realizado cadastro.
//Clientes com situação zero também serão excluídos caso não tenham usuários associados.
//======================================================================================================

//busca todos os clientes da w2b
//***futuramente criar tabela na w2b para indicar quais clientes possuem contas no web finanças
$query = mysqli_query($link_w2b,'select id, situacao, dt_cadastro from clientes');
$clientes = array();
while($cliente = mysqli_fetch_assoc($query)){
	$dt_cadastro = $cliente['dt_cadastro'];
	$dt_cadastro = strtotime($dt_cadastro);
	$hoje = date('Y-m-d H:i:s');
	$hoje = strtotime($hoje);
	$diferenca = (int)floor( ($hoje - $dt_cadastro) / (60 * 60 * 24));
	if($diferenca>=1){
		$clientes[] = $cliente;
	}
}

//busca usuários do web finaças dos clientes buscados
foreach($clientes as $cliente){
	mysqli_query($link_wf,'start transaction');
	$query = mysqli_query($link_wf,'select id, cliente_id, situacao, dt_cadastro from usuarios where cliente_id = '.$cliente['id']);
	$usuarios_ativos = 0;
	while($usuario = mysqli_fetch_assoc($query)){
		if($usuario['situacao']==0){
			$dt_cadastro = $usuario['dt_cadastro'];
			$dt_cadastro = strtotime($dt_cadastro);
			$hoje = date('Y-m-d H:i:s');
			$hoje = strtotime($hoje);
			$diferenca = (int)floor( ($hoje - $dt_cadastro) / (60 * 60 * 24));
			if($diferenca>=1){
				//exclui usuário se tiver situacao zero por mais de 24h
				mysqli_query($link_wf,'delete from usuarios where id = '.$usuario['id']);
			}
		}else{
			++ $usuarios_ativos;
		}
	}

	if($usuarios_ativos==0 && $cliente['situacao']==0){
		//exclui cliente
		mysqli_query($link_w2b,'delete from clientes where id = '.$cliente['id']);
		//libera banco de dados caso o cliente tenha situação zero
		$cliente_db_id = mysqli_fetch_assoc(mysqli_query($link_wf,'select id from clientes_db where cliente_id = '.$cliente['id']));
		mysqli_query($link_wf,'update clientes_db set cliente_id = 0, situacao = 0 where id = '.$cliente_db_id['id']);
		//remove situação de acesso do cliente
		mysqli_query($link_wf,'delete from cli_acesso_situacao where cliente_id = '.$cliente['id']);
		//remove cliente do array para reaproveitá-lo na exclusão de clientes e usuários com mais de 45 dias sem contratar o web finanças
		$key = key($clientes);
		unset($clientes[$key]);
	}
	mysqli_query($link_wf,'commit');
}

//======================================================================================================
//Exclui usuários com mais de 45 dias sem contratar o web finanças.
//Clientes com situação zero também serão excluídos.
//======================================================================================================
//verifica os clientes que já tenham utilizado o web finanças por 45 dias sem contratá-lo
foreach($clientes as $cliente){
	mysqli_query($link_wf,'start transaction');
	//$cli_acesso_situacao = mysqli_fetch_assoc(mysqli_query($link_wf,'select situacao from cli_acesso_situacao where cliente_id = '.$cliente['id']));
	$periodo_trial = mysqli_fetch_assoc(mysqli_query($link_wf,'select dt_cadastro from clientes_trial where cliente_id = '.$cliente['id']));
	if(!empty($periodo_trial)){
		$dt_cadastro = $periodo_trial['dt_cadastro'];
		$dt_cadastro = strtotime($dt_cadastro);  
		$hoje = date('Y-m-d H:i:s');
		$hoje = strtotime($hoje);	
		$diferenca = (int)floor( ($hoje - $dt_cadastro) / (60 * 60 * 24)); echo $cliente['id'].'= '.$periodo_trial['dt_cadastro'].'='.$diferenca.'-';
		if($diferenca>120){
			//exclui todos usuários do cliente
			mysqli_query($link_wf,'delete from usuarios where cliente_id = '.$cliente['id']);
			//exclui registro do período de teste do cliente
			mysqli_query($link_wf,'delete from clientes_trial where cliente_id = '.$cliente['id']);
			//exclui registro da situação de acesso do cliente
			mysqli_query($link_wf,'delete from cli_acesso_situacao where cliente_id = '.$cliente['id']);
			//libera banco de dados caso o cliente tenha situação zero
			$cliente_db = mysqli_fetch_assoc(mysqli_query($link_wf,'select id, db, db_senha from clientes_db where cliente_id = '.$cliente['id']));
			mysqli_query($link_wf,'update clientes_db set cliente_id = 0, situacao = 0 where id = '.$cliente_db['id']);
			//conecta ao banco de dados antes utilizado pelo cliente excluído
			$link_wf_cli = mysqli_connect('mysql.webfinancas.com',$cliente_db['db'],$cliente_db['db_senha'],$cliente_db['db']);
			//limpa as tabelas do banco de dados liberado
			$arr_tabelas = array("centro_resp","contas","ctr_plc_lancamentos","ctr_plc_lancamentos_rcr","favorecidos","lancamentos","lancamentos_recorrentes","lnct_anexos","plano_contas","recibos", "custeio_lancamentos", "custeio_material", "custeio_nome", "agenda", "agendaReagendar", "arq_agenda_envio", "boletos_remessa", "boletos", "clientes_cf", "clientes_favorecidos", "clientes_pl_config", "configConsultaProc", "contador", "honorarios");
			foreach($arr_tabelas as $tabela){
				$query_truncate = 'truncate table '.$tabela;
				mysqli_query($link_wf_cli,$query_truncate);
			}
			mysqli_close($link_wf_cli);
			if($cliente['situacao']==0){
				//exclui cliente
				mysqli_query($link_w2b,'delete from clientes where id = '.$cliente['id']);
			}
			//exclui lançamento recorrente do wf da w2b
			$lancamento_rcr_id =  mysqli_fetch_assoc(mysqli_query($link_w2b,"select lancamento_recorrente_id from faturas_recorrentes where cliente_id = ".$cliente['id']." and sistema_id = 1"));
			mysqli_query($link_wf_w2b,'delete from lancamentos_recorrentes where id = '.$lancamento_rcr_id["lancamento_recorrente_id"]);
			//exclui fatura recorrente do adm da w2b
			mysqli_query($link_w2b,'delete from faturas_recorrentes where cliente_id = '.$cliente['id'].' and sistema_id = 1');
			//exclui associação do cliente ao sistema contratado na w2b
			mysqli_query($link_w2b,'delete from sistemas_clientes where cliente_id = '.$cliente['id'].' and sistema_id = 1');
		}elseif($diferenca>30){ //Verifica quantidade tempo que o usuário esta utilizando o período de teste.
			mysqli_query($link_wf,'update cli_acesso_situacao set situacao = 3 where cliente_id = '.$cliente['id']);
		}
	}
	mysqli_query($link_wf,'commit');
}

mysqli_close($link_wf);
mysqli_close($link_w2b);
mysqli_close($link_wf_w2b);
?>