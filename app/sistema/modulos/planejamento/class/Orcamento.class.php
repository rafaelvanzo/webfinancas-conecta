<?php

class Orcamento{

	public $descricao;//,$jan,$fev,$mar,$abr,$mai,$jun,$jul,$ago,$set,$out,$nov,$dez;

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($dados){
		$vars = get_class_vars(get_class($this));
		foreach($vars as $key => $value){
			if(array_key_exists($key,$dados)){
				$this->$key = $dados[$key];
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
INCLUÍR VALORES POR MÊS
===========================================================================================
*/

function vl_mes_incluir($db,$orcamento_id,$valores,$update=false){
	$jsonTxt = str_replace('\"','"',$valores);
	$jsonObj = json_decode($jsonTxt, true);
	$arr_insert['orcamento_id'] = $orcamento_id;
	foreach($jsonObj as $valor){
		$arr_insert['plano_contas_id'] = $valor['plano_contas_id'];
		$arr_insert['vl_unico'] = $valor['vl_unico_check'];
		$arr_insert['jan'] = $db->valorToDouble($valor['jan']);
		$arr_insert['fev'] = $db->valorToDouble($valor['fev']);
		$arr_insert['mar'] = $db->valorToDouble($valor['mar']);
		$arr_insert['abr'] = $db->valorToDouble($valor['abr']);
		$arr_insert['mai'] = $db->valorToDouble($valor['mai']);
		$arr_insert['jun'] = $db->valorToDouble($valor['jun']);
		$arr_insert['jul'] = $db->valorToDouble($valor['jul']);
		$arr_insert['ago'] = $db->valorToDouble($valor['ago']);
		$arr_insert['sete'] = $db->valorToDouble($valor['sete']);
		$arr_insert['outu'] = $db->valorToDouble($valor['outu']);
		$arr_insert['nov'] = $db->valorToDouble($valor['nov']);
		$arr_insert['dez'] = $db->valorToDouble($valor['dez']);
		$arr_insert['ano'] = $valor['ano'];
		if($update){
			$busca = $db->numRows('select id from orcamentos_plnj_vl where orcamento_id = '.$orcamento_id.' and plano_contas_id='.$valor['plano_contas_id'].' and ano = '.$valor['ano']);
			if($busca){
				$db->query_update('orcamentos_plnj_vl',$arr_insert,'orcamento_id='.$orcamento_id.' and plano_contas_id='.$valor['plano_contas_id'].' and ano = '.$valor['ano']);
			}else{
				$db->query_insert('orcamentos_plnj_vl',$arr_insert);
			}
		}else{
			$db->query_insert('orcamentos_plnj_vl',$arr_insert);
		}
	}
	return 1;
}

/*
===========================================================================================
INCLUÍR ORÇAMENTO
===========================================================================================
*/

function orcamentosIncluir($db,$dados){
	$orcamento = self::getValues();
	$descricao = strtoupper($orcamento['descricao']);
	$numRows = $db->numRows('select id from orcamentos_plnj where descricao = "'.$descricao.'"');
	if($numRows==0){
		$orcamento['dt_cadastro'] = date('Y-m-d H:i:s');
		$orcamento_id = $db->query_insert('orcamentos_plnj',$orcamento);
		if($dados['valores']!=''){
			self::vl_mes_incluir($db,$orcamento_id,$dados['valores']);
		}
		return 1;
	}else{
		return 0;
	}
}

/*
===========================================================================================
EDITAR ORÇAMENTO
===========================================================================================
*/

function orcamentosEditar($db,$dados){
	$orcamento = self::getValues();
	$orcamento_id = $dados['orcamento_id'];
	$dscr_ini = strtoupper($dados['dscr_ini']);
	$descricao = strtoupper($orcamento['descricao']);
	if($dscr_ini==$descricao){
		$db->query('update orcamentos_plnj set descricao = "'.$dados['descricao'].'" where id = '.$orcamento_id);
		if($dados['valores']!=''){
			self::vl_mes_incluir($db,$orcamento_id,$dados['valores'],true);
		}
		return 1;
	}else{
		$numRows = $db->numRows('select id from orcamentos_plnj where descricao = "'.$descricao.'"');
		if($numRows==0){
			$db->query('update orcamentos_plnj set descricao = "'.$dados['descricao'].'" where id = '.$orcamento_id);
			if($dados['valores']!=''){
				self::vl_mes_incluir($db,$orcamento_id,$dados['valores'],true);
			}
			return 1;
		}else{
			return 0;
		}
	}
}

/*
===========================================================================================
EXCLUÍR ORÇAMENTO
===========================================================================================
*/

function orcamentosExcluir($db,$dados){
	$db->query("delete from orcamentos_plnj where id = ".$dados['orcamento_id']);
	$db->query("delete from orcamentos_plnj_vl where orcamento_id = ".$dados['orcamento_id']);
	return 1;
}

/*
===========================================================================================
EXIBIR ORÇAMENTO
===========================================================================================
*/

function orcamentosExibir($db,$dados){
	$query = 'select * from orcamentos_plnj_vl where orcamento_id = '.$dados['orcamento_id'];
	$valores = $db->fetch_all_array($query);
	$str_valores = "";
	$vl_mes_format = array();
	foreach($valores as $vl_mes){
		$vl_anual = $vl_mes['jan'] + $vl_mes['fev'] + $vl_mes['mar'] + $vl_mes['abr'] + $vl_mes['mai'] + $vl_mes['jun'] + $vl_mes['jul'] + $vl_mes['ago'] + $vl_mes['sete'] + $vl_mes['outu'] + $vl_mes['nov'] + $vl_mes['dez'];
		$vl_anual = self::valorFormat($vl_anual);
		$ano = $vl_mes['ano'];
		$vl_mes_format['jan'] = self::valorFormat($vl_mes['jan']);
		$vl_mes_format['fev'] = self::valorFormat($vl_mes['fev']);
		$vl_mes_format['mar'] = self::valorFormat($vl_mes['mar']);
		$vl_mes_format['abr'] = self::valorFormat($vl_mes['abr']);
		$vl_mes_format['mai'] = self::valorFormat($vl_mes['mai']);
		$vl_mes_format['jun'] = self::valorFormat($vl_mes['jun']);
		$vl_mes_format['jul'] = self::valorFormat($vl_mes['jul']);
		$vl_mes_format['ago'] = self::valorFormat($vl_mes['ago']);
		$vl_mes_format['sete'] = self::valorFormat($vl_mes['sete']);
		$vl_mes_format['outu'] = self::valorFormat($vl_mes['outu']);
		$vl_mes_format['nov'] = self::valorFormat($vl_mes['nov']);
		$vl_mes_format['dez'] = self::valorFormat($vl_mes['dez']);
		$str_valores .= '{"plano_contas_id":"'.$vl_mes['plano_contas_id'].'","vl_unico_check":"'.$vl_mes['vl_unico'].'","vl_anual":"'.$vl_anual.'","jan":"'.$vl_mes_format['jan'].'","fev":"'.$vl_mes_format['fev'].'","mar":"'.$vl_mes_format['mar'].'","abr":"'.$vl_mes_format['abr'].'","mai":"'.$vl_mes_format['mai'].'","jun":"'.$vl_mes_format['jun'].'","jul":"'.$vl_mes_format['jul'].'","ago":"'.$vl_mes_format['ago'].'","sete":"'.$vl_mes_format['sete'].'","outu":"'.$vl_mes_format['outu'].'","nov":"'.$vl_mes_format['nov'].'","dez":"'.$vl_mes_format['dez'].'","ano":"'.$ano.'"},';
	}
	$str_valores = substr($str_valores,0,-1); //retira a ultima virgula
	$jsonText = '
		['.$str_valores.']
	';
	$retorno = array("valores"=>$jsonText);
	return $retorno;
}

/*
===========================================================================================
FORMATAÇÃO
===========================================================================================
*/
	
	//converte valores para inserir no banco de dados
	function valorToDouble($valor){
		$double = str_replace('.', '', $valor);
		$double = str_replace(',', '.', $double);
		return $double * 1;
	}
	
	//formata os valores retornados do banco de dados
	function valorFormat($valor){
		$format = number_format($valor,2,',','.');
		return $format;
	}
	

}

?>