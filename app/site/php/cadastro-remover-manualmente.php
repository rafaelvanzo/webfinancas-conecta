<?php

/*
if(isset($_GET['cliente_id'])){

//conexão com banco de dados da w2b e do web finanças
$link_w2b = mysqli_connect('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
$link_wf = mysqli_connect('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');


// Configura a id do cliente no db web 2 business

$cliente_id = $_GET['cliente_id'];


	mysqli_query($link_wf,'start transaction');
	//$cli_acesso_situacao = mysqli_fetch_assoc(mysqli_query($link_wf,'select situacao from cli_acesso_situacao where cliente_id = '.$cliente_id));
	

			$dados = mysqli_fetch_assoc(mysqli_query($link_w2b,'SELECT * from clientes where id = '.$cliente_id));
			
				$data = ' (tp_cadastro, inscricao, cpf_cnpj, nome, email, email_fin, logradouro, numero, complemento, bairro, cidade, uf, cep, telefone, celular, observacao) value ("'.$dados['tp_cadastro'].'", "'.$dados['inscricao'].'", "'.$dados['cpf_cnpj'].'", "'.$dados['nome'].'", "'.$dados['email'].'", "'.$dados['email_fin'].'", "'.$dados['logradouro'].'", "'.$dados['numero'].'", "'.$dados['complemento'].'", "'.$dados['bairro'].'", "'.$dados['cidade'].'", "'.$dados['uf'].'", "'.$dados['cep'].'", "'.$dados['telefone'].'", "'.$dados['celular'].'", "'.$dados['observacao'].'") ';

				mysqli_query($link_w2b, 'INSERT INTO clientes_cancelados '.$data);

			//exclui todos usuários do cliente
			mysqli_query($link_wf,'delete from usuarios where cliente_id = '.$cliente_id);
			//exclui registro do período de teste do cliente
			mysqli_query($link_wf,'delete from clientes_trial where cliente_id = '.$cliente_id);
			//exclui registro da situação de acesso do cliente
			mysqli_query($link_wf,'delete from cli_acesso_situacao where cliente_id = '.$cliente_id);
			//libera banco de dados caso o cliente tenha situação zero
			$cliente_db = mysqli_fetch_assoc(mysqli_query($link_wf,'select id, db, db_senha from clientes_db where cliente_id = '.$cliente_id));
			mysqli_query($link_wf,'update clientes_db set cliente_id = 0, situacao = 0 where id = '.$cliente_db['id']);
			//conecta ao banco de dados antes utilizado pelo cliente excluído
			$link_wf_cli = mysqli_connect('mysql.webfinancas.com',$cliente_db['db'],$cliente_db['db_senha'],$cliente_db['db']);
			//limpa as tabelas do banco de dados liberado
			$arr_tabelas = array("centro_resp","contas","ctr_plc_lancamentos","ctr_plc_lancamentos_rcr","favorecidos","lancamentos","lancamentos_recorrentes","lnct_anexos","plano_contas","recibos", "custeio_lancamentos", "custeio_material", "custeio_nome", "agenda", "agendaReagendar", "arq_agenda_envio", "boletos_remessa", "boletos", "clientes_cf", "clientes_favorecidos", "clientes_pl_config", "configConsultaProc", "contador", "honorarios", "lancamentos_historico");
			foreach($arr_tabelas as $tabela){
				$query_truncate = 'truncate table '.$tabela;
				mysqli_query($link_wf_cli,$query_truncate);
			}
			mysqli_close($link_wf_cli);
			if($cliente['situacao']==0){
				//exclui cliente
				mysqli_query($link_w2b,'delete from clientes where id = '.$cliente_id);
			}
			//exclui lançamento recorrente do wf da w2b
			$lancamento_rcr_id =  mysqli_fetch_assoc(mysqli_query($link_w2b,"select lancamento_recorrente_id from faturas_recorrentes where cliente_id = ".$cliente_id." and sistema_id = 1"));
			mysqli_query($link_wf_w2b,'delete from lancamentos_recorrentes where id = '.$lancamento_rcr_id["lancamento_recorrente_id"]);
			//exclui fatura recorrente do adm da w2b
			mysqli_query($link_w2b,'delete from faturas_recorrentes where cliente_id = '.$cliente_id.' and sistema_id = 1');
			//exclui associação do cliente ao sistema contratado na w2b
            mysqli_query($link_w2b,'delete from sistemas_clientes where cliente_id = '.$cliente_id.' and sistema_id = 1');
            
            $db = $cliente_db['db'];
		
	
	mysqli_query($link_wf,'commit');


mysqli_close($link_wf);
mysqli_close($link_w2b);


echo "Foi Excluido o cliente n.".$cliente_id." e zerado o banco ".$db;

}else{

	echo 'Não foi possível executar a operação.';
}
*/
?>