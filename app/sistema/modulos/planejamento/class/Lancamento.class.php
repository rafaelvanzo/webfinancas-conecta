<?php

class Lancamento{

	public $tipo;
	public $descricao;
	public $valor;
	public $dt_vencimento;
	public $observacao;

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/

	function __construct($db="",$dados=""){
		if($db!=""&&$dados!=""){
			$dados['dt_vencimento'] = $db->data_to_sql($dados['dt_vencimento']);
			$dados['valor'] = $db->valorToDouble($dados['valor']);
			$vars = get_class_vars(get_class($this));
			foreach($vars as $key => $value){
				if(array_key_exists($key,$dados)){
					$this->$key = $dados[$key];
				}
			}
		}
	}

/*
===========================================================================================
PEGAR VALOR DAS VARIÁVEIS
===========================================================================================
*/

	function getValues(){
		$dados = array();
		$vars = get_class_vars(get_class($this));
		foreach($vars as $key => $value){
			$dados[$key] = $this->$key;
		}
		return $dados;
	}

/*
===========================================================================================
INCLUÍR RECEBIMENTO
===========================================================================================
*/

	function recebimentosIncluir($db,$dados){
		$lancamento = self::getValues();
		$lancamento_id = $db->query_insert('lancamentos_plnj',$lancamento);
		if($dados['ct_resp_lancamentos']!=""){
			self::ctrPlcLancamentosAtualizar($db,$dados['ct_resp_lancamentos'],$lancamento_id,"R");
		}
		return $lancamento_id;
	}

/*
===========================================================================================
EDITAR RECEBIMENTO
===========================================================================================
*/

	function recebimentosEditar($db,$dados){
		$lancamento = self::getValues();
		$db->query_update('lancamentos_plnj',$lancamento,'id = '.$dados['lancamento_id']);
		self::ctrPlcLancamentosAtualizar($db,$dados['ct_resp_lancamentos'],$dados['lancamento_id'],"R");
		return 1;
	}	

/*
===========================================================================================
EXCLUIR RECEBIMENTO
===========================================================================================
*/	

	function recebimentosExcluir($db,$lancamento_id){
		$db->query("delete from lancamentos_plnj where id = ".$lancamento_id);
		$db->query("delete from ctr_plc_lancamentos_plnj where lancamento_plnj_id = ".$lancamento_id);
		return 1;
	}

/*
===========================================================================================
INCLUÍR PAGAMENTO
===========================================================================================
*/

	function pagamentosIncluir($db,$dados){
		$lancamento = self::getValues();
		$lancamento_id = $db->query_insert('lancamentos_plnj',$lancamento);
		if($dados['ct_resp_lancamentos']!=""){
			self::ctrPlcLancamentosAtualizar($db,$dados['ct_resp_lancamentos'],$lancamento_id,"P");
		}
		return $lancamento_id;
	}

/*
===========================================================================================
EDITAR PAGAMENTO
===========================================================================================
*/

	function pagamentosEditar($db,$dados){
		$lancamento = self::getValues();
		$db->query_update('lancamentos_plnj',$lancamento,'id = '.$dados['lancamento_id']);
		self::ctrPlcLancamentosAtualizar($db,$dados['ct_resp_lancamentos'],$dados['lancamento_id'],"P");
		return 1;
	}	

/*
===========================================================================================
EXCLUIR PAGAMENTO
===========================================================================================
*/	

	function pagamentosExcluir($db,$lancamento_id){
		$db->query("delete from lancamentos_plnj where id = ".$lancamento_id);
		$db->query("delete from ctr_plc_lancamentos_plnj where lancamento_plnj_id = ".$lancamento_id);
		return 1;
	}

/*
===========================================================================================
EXIBIR
===========================================================================================
*/

	function lancamentosExibir($db,$array_dados){

		$query = "
			select id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, descricao, valor
			from lancamentos_plnj
			where id = ".$array_dados['lancamento_id']."
		";

		//monta a lista de lannçamentos do centro de responsabilidade
		$query_ctr_plc_lancamentos = "
			select pc.cod_conta, cr.cod_centro, crl.id ctr_plc_lancamento_plnj_id, cr.id centro_resp_id, pc.id plano_contas_id, pc.nome conta, cr.nome centro, crl.valor, crl.porcentagem
			from ctr_plc_lancamentos_plnj crl
			left join centro_resp cr on crl.centro_resp_id = cr.id
			left join plano_contas pc on crl.plano_contas_id = pc.id
			where crl.lancamento_plnj_id = ".$array_dados['lancamento_id'];

		$array_ctr_plc_lancamentos = $db->fetch_all_array($query_ctr_plc_lancamentos);

		$ctr_plc_lancamentos = "";

		foreach($array_ctr_plc_lancamentos as $lancamento){
			$valor = number_format($lancamento['valor'],2,',','.');
			$porcentagem = number_format($lancamento['porcentagem'],2,',','.');
			$ctr_plc_lancamentos .= '{"ctr_plc_lancamento_plnj_id":"'.$lancamento['ctr_plc_lancamento_plnj_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'"},';
		}
		
		$ctr_plc_lancamentos = substr($ctr_plc_lancamentos,0,-1); //retira a ultima virgula

		$jsonText = '
			['.$ctr_plc_lancamentos.']
		';
		//fim da montagem da lista de lannçamentos do centro de responsabilidade
			

		$lancamentos_visualizar = $db->fetch_array($db->query($query));
		$lancamentos_visualizar['valor'] = number_format($lancamentos_visualizar['valor'],2,',','.');
		$retorno = array("lancamento"=>$lancamentos_visualizar,"ctr_plc_lancamentos"=>$jsonText);
		return $retorno;
		
	}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA PARA RECEBIMENTO
===========================================================================================
*/

function atualizarSaldoContaRcbt($db,$valor,$conta_id){
	$array_conta = $db->fetch_assoc("select vl_credito, limite_credito from contas where id = ".$conta_id);
	if($array_conta['vl_credito'] == $array_conta['limite_credito']){
		$db->query("update contas set vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id);
	}else{
		$credito_usado = $array_conta['limite_credito'] - $array_conta['vl_credito'];
		if($valor <= $credito_usado){
			$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id); //repoem somente o cheque especial usado
		}else{
			$valor -= $credito_usado;
			$db->query("update contas set vl_saldo = vl_saldo + ".$valor.", vl_credito = vl_credito + ".$credito_usado." where id = ".$conta_id); //repoem o cheque especial usado e o saldo da conta
		}
	}
}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA PARA PAGAMENTO
===========================================================================================
*/

function atualizarSaldoContaPgto($db,$valor,$conta_id,$conta_saldo){
	if($conta_saldo>=$valor){ //desconta o debito inteiro do saldo da conta
		$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$conta_id);
	}else{ //desconta uma parte no saldo da conta, se houver credito, e o restante do saldo do cheque especial
		$valor -= $conta_saldo; //abate do debito o restante de saldo que havia na conta
		$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$conta_id); //abate o restante do debito do saldo do cheque especial
	}
}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA PARA TRANSFERÊNCIA
===========================================================================================
*/

function atualizarSaldoContaTrans($db,$valor,$conta_id_origem,$conta_id_destino,$conta_saldo){
	//debita a conta de origem
	if($conta_saldo>=$valor){ //desconta o debito inteiro do saldo da conta
		$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$conta_id_origem);
	}else{ //desconta uma parte no saldo da conta, se houver credito, e o restante do saldo do cheque especial
		$debito = $valor - $conta_saldo; //abate do debito o restante de saldo que havia na conta
		$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$debito." where id = ".$conta_id_origem); //abate o restante do debito do saldo do cheque especial
	}
	//fim debita a conta de origem
	//credita conta de destino
	$conta_destino = $db->fetch_assoc("select vl_credito, limite_credito from contas where id = ".$conta_id_destino);
	if($conta_destino['vl_credito'] == $conta_destino['limite_credito']){
		$db->query("update contas set vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id_destino);
	}else{
		$credito_usado = $conta_destino['limite_credito'] - $conta_destino['vl_credito'];
		if($vl_transferencia <= $credito_usado){
			$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id_destino); //repoem somente o cheque especial usado
		}else{
			$db->query("update contas set vl_saldo = vl_saldo + ".$valor." - ".$credito_usado.", vl_credito = ".$conta_destino['limite_credito']." where id = ".$conta_id_destino); //repoem o cheque especial usado e o saldo da conta
		}
	}
	//fim credita conta de destino
}

/*
===========================================================================================
LISTAR LANÇAMENTOS
===========================================================================================
*/

function lancamentosListar($db,$lancamento_id){

	/*
	if($tp_busca==""){
		$dt_ini = date('Y').'-'.date('m').'-01';
		$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
		$dt_fim = date('Y-m-d',$dt_fim);
	}elseif($tp_busca=="periodo"){
		$dt_ini = $db->data_to_sql($array_dados['dt_ini']);
		$dt_fim = $db->data_to_sql($array_dados['dt_fim']);
	}else{
		$dt_ini = date('Y').'-'.$array_dados['mes'].'-01';
		$dt_fim = mktime(0,0,0,$array_dados['mes']+1,'00',date('Y'));
		$dt_fim = date('Y-m-d',$dt_fim);
	}
	*/

	$query_lancamento = "
		select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, descricao, valor
		from lancamentos_plnj
		where id = ".$lancamento_id."
	";
	
	$array_lancamento = $db->fetch_assoc($query_lancamento);

	if($array_lancamento['tipo']=="R"){
		$classe_excluir = "recebimentosExcluir";
		$valor = 'R$ '.number_format($lancamento['valor'],2,',','.');
		$title_pgto_recb = 'Recebimento';
		$cor = 'blue';
	}elseif($array_lancamento['tipo']=="P"){
		$classe_excluir = "pagamentosExcluir";
		$valor = 'R$ '.number_format($lancamento['valor'],2,',','.');
		$title_pgto_recb = 'Pagamento';
		$cor = 'red';
	}
	
	// ============ data ============
		$dt_compensar = explode("/", $array_lancamento['dt_vencimento']);
		$dia = $dt_compensar[0];
		$m = $dt_compensar[1];
		if($m == 01){ $mes = 'Jan';}
							elseif($m == 02){ $mes = 'Fev';}
							elseif($m == 03){ $mes = 'Mar';}
							elseif($m == 04){ $mes = 'Abr';}
							elseif($m == 05){ $mes = 'Mai';}
							elseif($m == 06){ $mes = 'Jun';}
							elseif($m == 07){ $mes = 'Jul';}
							elseif($m == 08){ $mes = 'Ago';}
							elseif($m == 09){ $mes = 'Set';}
							elseif($m == 10){ $mes = 'Out';}
							elseif($m == 11){ $mes = 'Nov';}
							else{ $mes = 'Dez';}
		$ano = substr($dt_compensar[2], -2);
		// ==============================

	/*
		$opcoes = '
			<a href="javascript://void(0);" original-title="Editar" class="smallButton tipS" style="margin: 5px;" onClick="lancamentosExibir('.$lancamento_id.',\''.$array_lancamento['tipo'].'\')"><img src="images/icons/dark/pencil.png" alt=""></a>
			<a href="'.$lancamento_id.'" original-title="Excluír" class="smallButton tipS '.$classe_excluir.'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
		';
	*/
	
	/*	$valor = 'R$ '.number_format($array_lancamento['valor'],2,',','.');
	
		$lancamento = '
			[{"dt_ordem":"'.$array_lancamento['dt_ordem'].'","dt_vencimento":"'.$array_lancamento['dt_vencimento'].'","descricao":"'.$array_lancamento['descricao'].'","valor":"'.$valor.'","lancamento_id":"'.$lancamento_id.'","tipo":"'.$array_lancamento['tipo'].'","classe_excluir":"'.$classe_excluir.'"}]
		';
	*/

	$conteudo_lnct2 = '<div class="updates newUpdate">
												<div class="uDate tbWF tipS" original-title="Compensação" align="center"> <span class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
												<span class="lDespesa tbWF" >
													<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$array_lancamento['descricao'].'</strong></a>
													<span original-title="'.$title_pgto_recb.'" class="tipN '.$cor.'">'.$title_pgto_recb.'</span>
												</span>	
												<div class="tbWFoption">										
													<a href="'.$array_lancamento['id'].'" original-title="Excluir" class="smallButton btTBwf redB tipS '.$classe_excluir.'"><img src="images/icons/light/close.png" width="10"></a>		
													<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS exibir" onClick="lancamentosExibir('.$array_lancamento['id'].',\''.$array_lancamento['tipo'].'\')"><img src="images/icons/light/pencil.png" width="10"></a>
												</div>																													
												<div class="tbWFvalue '.$cor.'">R$ '.number_format($array_lancamento['valor'],2,',','.').' </div>
										</div>
	';
	
	//$lancamento = '[{"dt_ordem":"'.$array_lancamento['dt_ordem'].'","conteudo_lnct":"'.$conteudo_lnct2.'"}]';
	$lancamento = array("dt_ordem"=>$array_lancamento['dt_ordem'],"conteudo_lnct"=>$conteudo_lnct2);
	return $lancamento;
}

/*
function lancamentosListar($db,$array_dados,$tp_busca=""){

	$nome_conta = $db->fetch_assoc("
		select c.id, b.nome, c.descricao, c.vl_saldo, c.vl_credito
		from contas c
		left join bancos b on c.banco_id = b.id
		where c.id = ".$array_dados['conta_id']."
		order by c.descricao
	");

	$nome_conta = $nome_conta['descricao'].' - '.$nome_conta['nome'];

	if($tp_busca==""){
		$dt_ini = date('Y').'-'.date('m').'-01';
		$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
		$dt_fim = date('Y-m-d',$dt_fim);
	}elseif($tp_busca=="periodo"){
		$dt_ini = $db->data_to_sql($array_dados['dt_ini']);
		$dt_fim = $db->data_to_sql($array_dados['dt_fim']);
	}else{
		$dt_ini = date('Y').'-'.$array_dados['mes'].'-01';
		$dt_fim = mktime(0,0,0,$array_dados['mes']+1,'00',date('Y'));
		$dt_fim = date('Y-m-d',$dt_fim);
	}
	
	$lancamentos_listar ='
		<table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos">
		<thead>
		<tr>
 				<th>Ordem</th>
        <th width="80">Compensação</th>
        <th>Descrição</th>
        <th>Valor</th>
        <th width="100">Opções</th>
		</tr>
		</thead>
		<tbody>
	';

	$query_lancamentos = "
		(select dt_compensacao dt_ordem, id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
		 from lancamentos
		 where conta_id = ".$array_dados['conta_id']."
		 	and tipo <> 'T'
			and dt_compensacao >= '".$dt_ini."'
			and dt_compensacao <= '".$dt_fim."')
	
		 union all
	
		(select dt_compensacao dt_ordem, id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
		 from lancamentos
		 where conta_id_origem = ".$array_dados['conta_id']."
			and dt_compensacao >= '".$dt_ini."'
			and dt_compensacao <= '".$dt_fim."')
			
		 union all
	
		(select dt_compensacao dt_ordem, id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
		 from lancamentos
		 where conta_id_destino = ".$array_dados['conta_id']."
			and dt_compensacao >= '".$dt_ini."'
			and dt_compensacao <= '".$dt_fim."')
	";
	
	$array_lancamentos = $db->fetch_all_array($query_lancamentos);

	foreach($array_lancamentos as $lancamento){
		if($lancamento['tipo']=="R"){
			$classe_excluir = "recebimentosExcluir";
			$valor = '<font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
		}elseif($lancamento['tipo']=="P"){
			$classe_excluir = "pagamentosExcluir";
			$valor = '<font color="#FF0000">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
		}else{
			$classe_excluir = "transferenciasExcluir";
			if($lancamento['conta_id_destino']==$array_dados['conta_id']){
				$valor = '<font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
			}else{
				$valor = '<font color="#FF0000">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';				
			}
		}
		$lancamentos_listar .= '
			<tr class="gradeA">
				<td>'.$lancamento['dt_ordem'].'</td>
				<td align="center">'.$lancamento['dt_compensacao'].'</td>
				<td>'.$lancamento['descricao'].'</td>
				<td>'.$valor.'</td>
				<td class="center">
					<a href="javascript://void(0);" original-title="Editar" class="smallButton tipS" style="margin: 5px;" onClick="lancamentosExibir('.$lancamento['id'].',\''.$lancamento['tipo'].'\')"><img src="images/icons/dark/pencil.png" alt=""></a>
					<a href="'.$lancamento['id'].'" original-title="Excluír" class="smallButton tipS '.$classe_excluir.'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
				</td>
			</tr>
		';
	}
	
	$lancamentos_listar .= '</tbody></table>';
	$retorno = array("lancamentos"=>$lancamentos_listar,"nome_conta"=>$nome_conta);
	return $retorno;
}
*/

/*
===========================================================================================
LISTAR SALDO DAS CONTAS
===========================================================================================
*/

function contasSaldoListar($db,$conta_id,&$sessao=false){
	$saldo_total = 0;
	$array_contas = $db->fetch_all_array("
		select c.id, b.nome, b.logo, c.descricao, c.vl_saldo, c.vl_credito
		from contas c
		left join bancos b on c.banco_id = b.id
		order by c.descricao
	");
	$contas_saldo = "";
	foreach($array_contas as $conta){
		if($conta['id']==$conta_id){
			$checked = 'checked="checked"';
		}else{
			$checked = "";
		}
		
		 if(!empty($conta['logo'])){ $banco_logo = $conta['logo']; }else{ $banco_logo = "bank.png"; }
		
		$contas_saldo .= '
			<div class="userRow">
						<img src="images/bancos/'.$banco_logo.'" alt="" class="floatL">
						<ul class="leftList">
								<li><a href="javascript://void(0);" class="tipW" original-title="Clique para visualizar a conta" onClick="lancamentosListar(\''.$conta['id'].'\')"><strong>'.$conta['descricao'].'</strong> </a></li>
								<li style="font-size: 9px;">'.$conta['nome'].'</li>
						</ul>
						<div class="rightList"><!-- <img src="images/icons/icon-checked-conta.png" border="0"/> --> <input type="radio" name="visualizarConta" id="cId'.$conta['id'].'" class="tipE" original-title="Clique para visualizar a conta" value="'.$conta['id'].'" onClick="lancamentosListar(\''.$conta['id'].'\')" '.$checked.'> </div>
				</div>
			<div class="orderRow">
					<ul class="leftList">
							<li>Saldo:</li>
							<li>C. Especial:</li>
							<li><b>Total:</b></li>
					</ul>
					<ul class="rightList">
							<li>R$ '.number_format($conta['vl_saldo'],2,',','.').'</li>
							<li>R$ '.number_format($conta['vl_credito'],2,',','.').'</li>
							<li><strong class="grey">R$ '.number_format($conta['vl_saldo']+$conta['vl_credito'],2,',','.').'</strong></li>
					</ul>
			</div>
			<div class="cLine"></div>
		';
		$saldo_total += $conta['vl_saldo']+$conta['vl_credito'];
	}
	if($sessao)
		$sessao = "R$ ".number_format($saldo_total,2,',','.');
	$contas = array("contas_saldo"=>$contas_saldo,"saldo_total"=>"R$ ".number_format($saldo_total,2,',','.'));
	return $contas;
}

/*
===========================================================================================
ATUALIZAR LANÇAMENTOS NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function ctrPlcLancamentosAtualizar($db,$lancamentos,$lancamento_id,$tp_lancamento){
	$jsonTxt = str_replace('\"','"',$lancamentos);
	$jsonObj = json_decode($jsonTxt, true);
	$array_lancamentos = $jsonObj;
	$query = $db->query("delete from ctr_plc_lancamentos_plnj where lancamento_plnj_id = ".$lancamento_id);
	if(count($array_lancamentos)>0){
		foreach($array_lancamentos as $lancamento){
			if($lancamento["operacao"]=="1"){ //inclui um novo lançamento
				$array_insert["lancamento_plnj_id"] = $lancamento_id;
				$array_insert["centro_resp_id"] = $lancamento["centro_resp_id"];
				$array_insert["plano_contas_id"] = $lancamento["plano_contas_id"];
				$array_insert["tp_lancamento"] = $tp_lancamento;
				$array_insert["valor"] = $db->valorToDouble($lancamento["valor"]);
				$array_insert["porcentagem"] = 1;
				$db->query_insert("ctr_plc_lancamentos_plnj",$array_insert);
			}
		}
	}
	return 1;
}

}


?>