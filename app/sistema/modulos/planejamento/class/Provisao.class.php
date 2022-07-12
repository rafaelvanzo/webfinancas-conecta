<?php

class Provisao{

	//public $descricao;//,$jan,$fev,$mar,$abr,$mai,$jun,$jul,$ago,$set,$out,$nov,$dez;

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($dados=""){
		if($dados!=""){
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
INCLUÍR VALORES POR MÊS
===========================================================================================
*/

function vl_mes_incluir($db,$tipo,$dados){
	$arr_provisao['tipo'] = $tipo;
	$arr_provisao['vl_unico'] = $dados['vl_unico'];
	$arr_provisao['ano'] = $dados['ano'];

	$valores = str_replace('\"','"',$dados['valores']);
	$valores = json_decode($valores, true);
	foreach($valores as $chave=>$valor){
		$arr_provisao[$chave] = $db->valorToDouble($valor);
	}

	$id_provisao = $db->fetch_assoc('select id from provisao where tipo = '.$tipo.' and ano = '.$arr_provisao['ano']);
	
	if(empty($id_provisao)){
		$db->query_insert('provisao',$arr_provisao);
	}else{
		$db->query_update('provisao',$arr_provisao,'id = '.$id_provisao['id']);
	}

}

/*
===========================================================================================
INCLUÍR DEPRECIAÇÃO
===========================================================================================
*/

function dpreIncluir($db,$dados){
	self::vl_mes_incluir($db,1,$dados);
}

/*
===========================================================================================
INCLUÍR AMORTIZAÇÃO
===========================================================================================
*/

function amrtIncluir($db,$dados){
	self::vl_mes_incluir($db,2,$dados);
}

/*
===========================================================================================
INCLUÍR PROVISÕES TRABALHISTAS
===========================================================================================
*/

function trbtIncluir($db,$dados){
	self::vl_mes_incluir($db,3,$dados);
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
EXIBIR PROVISÃO
===========================================================================================
*/

function provisaoExibir($db,$dados){
	$provisao = $db->fetch_assoc('select * from provisao where tipo = '.$dados['tipo'].' and ano = '.$dados['ano']);
	$str_valores = "";
	$vl_mes_format = array();

	//$vl_anual = $provisao['jan'] + $provisao['fev'] + $provisao['mar'] + $provisao['abr'] + $provisao['mai'] + $provisao['jun'] + $provisao['jul'] + $provisao['ago'] + $provisao['sete'] + $provisao['outu'] + $provisao['nov'] + $provisao['dez'];
	//$vl_anual = self::valorFormat($vl_anual);
	$ano = $provisao['ano'];
	$vl_mes_format['jan'] = self::valorFormat($provisao['jan']);
	$vl_mes_format['fev'] = self::valorFormat($provisao['fev']);
	$vl_mes_format['mar'] = self::valorFormat($provisao['mar']);
	$vl_mes_format['abr'] = self::valorFormat($provisao['abr']);
	$vl_mes_format['mai'] = self::valorFormat($provisao['mai']);
	$vl_mes_format['jun'] = self::valorFormat($provisao['jun']);
	$vl_mes_format['jul'] = self::valorFormat($provisao['jul']);
	$vl_mes_format['ago'] = self::valorFormat($provisao['ago']);
	$vl_mes_format['sete'] = self::valorFormat($provisao['sete']);
	$vl_mes_format['outu'] = self::valorFormat($provisao['outu']);
	$vl_mes_format['nov'] = self::valorFormat($provisao['nov']);
	$vl_mes_format['dez'] = self::valorFormat($provisao['dez']);
	$str_valores .= '{"vl_unico_check":"'.$provisao['vl_unico'].'","jan":"'.$vl_mes_format['jan'].'","fev":"'.$vl_mes_format['fev'].'","mar":"'.$vl_mes_format['mar'].'","abr":"'.$vl_mes_format['abr'].'","mai":"'.$vl_mes_format['mai'].'","jun":"'.$vl_mes_format['jun'].'","jul":"'.$vl_mes_format['jul'].'","ago":"'.$vl_mes_format['ago'].'","sete":"'.$vl_mes_format['sete'].'","outu":"'.$vl_mes_format['outu'].'","nov":"'.$vl_mes_format['nov'].'","dez":"'.$vl_mes_format['dez'].'","ano":"'.$ano.'"}';

	//$jsonText = '
		//['.$str_valores.']
	//';

	return $str_valores;
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