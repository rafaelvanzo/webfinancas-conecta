<?php
/**
ESTUDAR VIABILIDADE DE JUNTAR TODAS AS FUNÇÕES DE QUITAÇÃO
*/

require("Lancamento.class.php");
//require('Ofx.class.php');
require('reader.php');

class Importacao extends Lancamento{
	
/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($db="",$array_dados=""){
		if($array_dados!=""){
			foreach($this->lancamento_dados as $chave => $valor){
				if(array_key_exists($chave,$array_dados))
					$this->lancamento_dados[$chave] = $array_dados[$chave];
			}
			$this->lancamento_dados['valor'] = $db->valorToDouble($array_dados['valor']);
			$this->lancamento_dados['dt_competencia'] = $db->data_to_sql('01/'.$array_dados['dt_competencia']);
			$this->lancamento_dados['dt_emissao'] = $db->data_to_sql($array_dados['dt_emissao']);
			$this->lancamento_dados['dt_vencimento'] = $db->data_to_sql($array_dados['dt_vencimento']);
			$this->lancamento_dados['dt_compensacao'] = $db->data_to_sql($array_dados['dt_compensacao']);
		}
	}

/*
===========================================================================================
INCLUÍR LANÇAMENTOS
===========================================================================================
*/

	function lancamentosIncluir($db,$array_dados){
		$cliente_id = $array_dados['cliente_id'];
		$usuario_id = $array_dados['usuario_id'];
		
		$caminho_arquivos = "../importacao/".$cliente_id.'_'.$usuario_id.'_*.xls';
		$array_arquivos = glob($caminho_arquivos);
		foreach($array_arquivos as $arquivo){
			
			$xls = new Spreadsheet_Excel_Reader($arquivo);
			$xls->read($arquivo);
			$linhas = $xls->sheets[0]['numRows'];
			
			for($i = 2; $i <= $linhas; $i++){
				$valor = $xls->sheets[0]['cells'][$i][4];
				if(is_numeric($valor)){
					$dt_competencia = $xls->sheets[0]['cells'][$i][1];
					$dt_competencia = $db->data_to_sql($dt_competencia);
					$dt_vencimento = $xls->sheets[0]['cells'][$i][2];
					$dt_vencimento = $db->data_to_sql($dt_vencimento);
					$descricao = $xls->sheets[0]['cells'][$i][3];
					$descricao = utf8_encode($descricao);
					$array_insert['competencia'] = $dt_competencia;
					$array_insert['vencimento'] = $dt_vencimento;
					$array_insert['descricao'] = $descricao;
					$array_insert['valor'] = $valor;
					$db->query_insert('lancamentos_import',$array_insert);
				}
			}
			unlink($arquivo);
		}

	}


/*
===========================================================================================
EXCLUÍR LANÇAMENTOS IMPORTADOS
===========================================================================================
*/

function lancamentosImportExcluir($db,$array_dados){
	$db->query("delete from lancamentos_import where id = ".$array_dados['lnct_import_id']);
}

/*
===========================================================================================
EXCLUÍR LANÇAMENTOS IMPORTADOS EM LOTE
===========================================================================================
*/

function lancamentosImportExcluirLote($db,$array_dados){
	$array_lnct_id = explode(',',$array_dados['lncts_id']);
	foreach($array_lnct_id as $lnct_id){
		$db->query("delete from lancamentos_import where id = ".$lnct_id);
	}
}

/*
===========================================================================================
LISTAR LANÇAMENTOS IMPORTADOS
===========================================================================================
*/

function lancamentosListar($db){
	$query_lnct_import = "
		select vencimento, id, descricao, valor, date_format(competencia, '%d/%m/%Y') dt_competencia_format, date_format(vencimento, '%d/%m/%Y') dt_vencimento_format
		from lancamentos_import
		order by vencimento
	";
	$array_lnct = $db->fetch_all_array($query_lnct_import);
	$lancamentos = "";
	foreach($array_lnct as $lnct){
		if($lnct['valor']>0){
			$cor = 'blue'; /* color="#009900" */
			$tp_lnct = "R";
		}else{
			$cor = 'red'; /* color="#FF0000 */
			$tp_lnct = "P";
			$lnct['valor'] = $lnct['valor']*(-1);
		}

	// ============ data ============
	$dt_compensar = explode("/", $lnct['dt_vencimento_format']);
	$dia = $dt_compensar[0];
	$m = $dt_compensar[1];
	if($m == 1){ $mes = 'Jan';}
						elseif($m == 2){ $mes = 'Fev';}
						elseif($m == 3){ $mes = 'Mar';}
						elseif($m == 4){ $mes = 'Abr';}
						elseif($m == 5){ $mes = 'Mai';}
						elseif($m == 6){ $mes = 'Jun';}
						elseif($m == 7){ $mes = 'Jul';}
						elseif($m == 8){ $mes = 'Ago';}
						elseif($m == 9){ $mes = 'Set';}
						elseif($m == 10){ $mes = 'Out';}
						elseif($m == 11){ $mes = 'Nov';}
						else{ $mes = 'Dez';}
	$ano = substr($dt_compensar[2], -2);
	// ==============================				
		
		$lancamentos .= '
					<tr class="gradeA">
							<td>'.$lnct['data'].'</td>
							<td class="updates newUpdate">
										
										<div class="lnctCheckbox" style="float:left; padding-top:12px; padding-bottom:-12px;"><input type="checkbox" value="'.$lnct['id'].'" id="check_'.$lnct['id'].'" class="'.$tp_lnct.'"/></div>	
											
										<div class="uDate tbWF tipS"  style="margin-left:15px;" original-title="Vencimento" align="center"> 
											
											<span id="data_'.$lnct['id'].'" style="display:none">'.$lnct['dt_vencimento_format'].'</span>
											<span id="data_c_'.$lnct['id'].'" style="display:none">'.$lnct['dt_competencia_format'].'</span>
																				
										<span  class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
										
											<span class="lDespesa tbWF" style="width:65%;">
												<a href="javascript://void(0);"  style="cursor: default;" original-title="Descrição" class="tipS" >	<strong id="dscr_'.$lnct['id'].'">'.$lnct['descricao'].'</strong></a>
													<span original-title="'.$title_fav.'" class="tipN">'.$nome_desc.'</span>
											</span>											
															
										 <div class="tbWFoption" id="link_excluir_'.$lnct['id'].'">		
										 		<a href="javascript://void(0);" original-title="Excluir" class="smallButton btTBwf redB tipS excluir" onClick="alertaExcluir('.$lnct['id'].');"><img src="images/icons/light/close.png" width="10"></a>								
												<a href="javascript://void(0);" original-title="Novo lançamento" class="smallButton btTBwf tipS" id="link_quitar_'.$lnct['id'].'" onClick="novoLancamento(\''.$tp_lnct.'\','.$lnct['id'].')"><img src="images/icons/dark/add.png" width="10"></a>
												<a href="javascript://void(0);" original-title="Transferência" class="smallButton btTBwf tipN" id="link_trans_'.$lnct['id'].'" onClick="novoLancamento(\'T'.$tp_lnct.'\','.$lnct['id'].')"><img src="images/icons/dark/transfer.png" width="10"></a>
                				
             				</div> 
																																													
										<div class="tbWFvalue '.$cor.'" >R$ <b id="vl_'.$lnct['id'].'">'.$db->valorFormat($lnct['valor']).' </b></div>
				
							</td> 
						</tr>';
	}
	$lancamentos = '
          <table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos">
          <thead>
          <tr style="border-bottom: 1px solid #e7e7e7;">
            <th>Ordem</th>
						<th class="ckbHeaderCell" style="padding:4px 0px 5px 15px" >
							<div class="sItem" style="float:left; width:20px; margin-left:-7px; margin-top:2px; padding-left:7px; padding-right:9px; padding-top:1px; padding-bottom:2px; border:1px solid #CCC; background:#F9F9F9;">
								<input type="checkbox" id="ckbTblHeader" onclick="lnctChecarTodos(\'\');" style="padding-left:20px; padding-bottom:10px;">
								<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s btnDropDownCk" id="listItens" style="margin-left:7px;"></span>
								<div class="statsDetailed" id="dropDownMenuCk" style="margin-top:11px;">											
										<div class="statsContent" align="left" >
												<div class="statsUpdate statsUpdateCk">
														<input type="checkbox" id="ckbDropDownHeader"> <label for="ckbDropDownHeader">Todos</label> <div class="clear"></div>
												</div>
																														 
												<div id="ckbDropDownList">
													<div class="statsUpdate statsUpdateCk">
															<input type="checkbox" value="R" class="ckbListItem" id="tpLnctCk01"> <label for="tpLnctCk01">Recebimentos</label> <div class="clear"></div>
													</div>
												 
													<div class="statsUpdate statsUpdateCk">
															<input type="checkbox" value="P" class="ckbListItem" id="tpLnctCk02"> <label for="tpLnctCk02">Pagamentos</label> <div class="clear"></div>
													</div>
												</div>
										</div>
								</div>
							</div>
						</th>
          </tr>
          </thead>
          <tbody>
            '.$lancamentos.'
          </tbody>
          </table>
	';
	return $lancamentos;
}

/*
===========================================================================================
INCLUÍR LANÇAMENTOS EM LOTE
===========================================================================================
*/

function lnctLoteIncluir($db,$array_dados){
	
	//validação
	$incluir = 1;
	if($array_dados['tp_lnct']=='P'){
		$vl_total_lncts = $db->fetch_assoc('select sum(valor) total_lncts from lancamentos_import where id in ('.$array_dados['lncts_id'].')');
		$saldo_conta = $db->fetch_assoc('select (vl_saldo + vl_credito) saldo_conta from contas where id = '.$array_dados['conta_id']);
		if( $saldo_conta['saldo_conta']< ( $vl_total_lncts['total_lncts']*(-1) ) )
			$incluir = 0;
	}

	//incluír lançamentos
	if($incluir){

		$lncts_id = explode(',',$array_dados['lncts_id']);
	
		foreach($lncts_id as $lnct_id){
			$lnct = $db->fetch_assoc('select * from lancamentos_import where id = '.$lnct_id);
	
			$tipo = ($lnct['valor']>=0)?'R':'P';
			$descricao = ($array_dados['descricao']=='')?$lnct['descricao']:$array_dados['descricao'];
			$favorecido_id = $array_dados['favorecido_id'];
			$forma_pgto_id = ($array_dados['forma_pgto_id']=='')?'':$array_dados['forma_pgto_id'];
			$conta_id = $array_dados['conta_id'];
			$documento_id = ($array_dados['documento_id']=='')?'':$array_dados['documento_id'];
			$observacao = $array_dados['observacao'];
	
			$valor = $array_dados['valor'];
			$valor = ($valor=='' || $valor=='0,00')? $lnct['valor'] : $db->valorToDouble($valor);
			if($array_dados['tp_lnct']==='P')
				$valor *= -1;
	
			$dt_competencia = ($array_dados['dt_competencia']=='')? $lnct['competencia'] : $db->data_to_sql($array_dados['dt_competencia']);
			$dt_emissao = ($array_dados['dt_emissao']=='')? $lnct['competencia'] : $db->data_to_sql($array_dados['dt_emissao']);
			$dt_vencimento = ($array_dados['dt_vencimento']=='')? $lnct['vencimento'] : $db->data_to_sql($array_dados['dt_vencimento']);
			$dt_compensacao = ($array_dados['dt_compensacao']=='')? $lnct['vencimento'] : $db->data_to_sql($array_dados['dt_compensacao']);
			
			$this->lancamento_dados = array(
				'tipo'=>$tipo,
				'descricao'=>$descricao,
				'lancamento_pai_id'=>0,
				'lancamento_recorrente_id'=>0,
				'parcela_numero'=>1,
				'qtd_parcelas'=>1,
				'favorecido_id'=>$favorecido_id,
				'forma_pgto_id'=>$forma_pgto_id,
				'conta_id'=>$conta_id,
				'conta_id_origem'=>0,
				'conta_id_destino'=>0,
				'documento_id'=>$documento_id,
				'valor'=>$valor,
				'frequencia'=>0,
				'auto_lancamento'=>'',
				'observacao'=>$observacao,
				'dt_competencia'=>$dt_competencia,
				'dt_emissao'=>$dt_emissao,
				'dt_vencimento'=>$dt_vencimento,
				'dt_venc_ref'=>'0000-00-00',
				'dt_compensacao'=>$dt_compensacao,
				'compensado'=>1
			);
	
			if($array_dados['plc_id']!=0 || $array_dados['ctr_id']!=0){
				$arr_ctr_plc = array(
					"plano_contas_id" => $array_dados['plc_id'],
					"centro_resp_id" => $array_dados['ctr_id'],
					"valor" => $db->valorFormat($valor),
					"porcentagem" => '100,00',
					"operacao" => 1
				);
				$array_dados['ct_resp_lancamentos'] = '['.json_encode($arr_ctr_plc).']';
			}
	
			($tipo == 'R')? $this->recebimentosIncluir($db,$array_dados) : $this->pagamentosIncluir($db,$array_dados);
	
		}

	}

	return $incluir;

}

}

?>