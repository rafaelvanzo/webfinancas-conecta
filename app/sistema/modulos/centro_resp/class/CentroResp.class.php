<?php

class CentroResp{

	var $centro_resp_dados = array(
		//"cod_centro" => "",
		"cod_ref" => "",
		"nome" => "",
		"centro_pai_id" => "",
		"tp_centro" => "",
		"descricao" => "",
		"nivel" => "",
		"posicao" => "",
		"situacao" => "",
	);

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($array_dados=""){
		if($array_dados!=""){
			foreach($this->centro_resp_dados as $chave => $valor){
				$this->centro_resp_dados[$chave] = $array_dados[$chave];
			}
		}
	}


/*
===========================================================================================
INCLUÍR
===========================================================================================
*/

	function centroRespIncluir($db){
		
			$cod_ref_validar = self::codRefValidar($this->centro_resp_dados[cod_ref]);

			if($cod_ref_validar){
		
				$contaPai = $this->centro_resp_dados[centro_pai_id];
				
				if(empty($contaPai)){
					$posicao = $db->fetch_assoc('select IFNULL(Max(posicao),0) posicao from centro_resp where nivel = 1');
					
					//Acrescenta um novo registro cod_centro e posicao somando +1 ao valor do ultimo cod_centro do nível 1 registrado no db.
					$posicao_centro_resp = $posicao['posicao'] + 1; 
					$this->centro_resp_dados['posicao'] = $posicao_centro_resp;
					$this->centro_resp_dados['cod_centro'] = $posicao_centro_resp;
					$this->centro_resp_dados['nivel'] = 1;
					$this->centro_resp_dados['situacao'] = 1;
					$this->centro_resp_dados['dt_cadastro'] = date('Y-m-d');
					// Incluir no Banco
					$centro_id = $db->query_insert('centro_resp',$this->centro_resp_dados);
					$db->query("update centro_resp set hierarquia = '".$centro_id."' where id = ".$centro_id);
				}else{
					
					$centro_pai = $db->fetch_assoc('select cod_centro, nivel, hierarquia from centro_resp where id ='.$this->centro_resp_dados[centro_pai_id]);
	
					$nivel = $centro_pai['nivel'] + 1;
	
					$cod_centro = $db->fetch_assoc('select IFNULL(Max(posicao),0) posicao from centro_resp where centro_pai_id ='.$this->centro_resp_dados[centro_pai_id].' and nivel = '.$nivel);
					
					$posicao = $cod_centro['posicao'] + 1;
					
					$cod_centro = $centro_pai['cod_centro'].'.'.$posicao;
					
					$this->centro_resp_dados[posicao] = $posicao;
					$this->centro_resp_dados[cod_centro] = $cod_centro;
					$this->centro_resp_dados[nivel] = $nivel;
					$this->centro_resp_dados[situacao] = 1;
					$this->centro_resp_dados[dt_cadastro] = date('Y-m-d');				
					// Incluir no Banco
					$centro_id = $db->query_insert('centro_resp',$this->centro_resp_dados);
					$hierarquia = $centro_pai[hierarquia].','.$centro_id;
					$db->query("update centro_resp set hierarquia = '".$hierarquia."' where id = ".$centro_id);
					$db->query("update centro_resp set tp_centro = 2 where id = ".$this->centro_resp_dados[centro_pai_id]);
			}
			
			$retorno = array("situacao"=>1,"notificacao"=>"Centro cadastrado com sucesso.");
			
		}else{
			$retorno = array("situacao"=>2,"notificacao"=>"<br> Código de referência já cadastrado. Favor informar outro código.");
		}
		
		return $retorno;
		
	}

/*
===========================================================================================
EDITAR
===========================================================================================
*/

	function centroRespEditar($db,$array_dados){
		$cod_ref_validar = self::codRefValidar($array_dados['cod_ref'],$array_dados['cod_ref_ini']);
		if($cod_ref_validar){
			
			if($array_dados['centro_pai_id']==$array_dados['centro_resp_id']){ //se o centro sintético e o centro editado forem o mesmo, o centro_pai_id recebe zero
				$array_dados['centro_pai_id'] = 0;
				$this->centro_resp_dados['centro_pai_id'] = 0;
			}else{ // verifica se o novo centro pai é um nível interno ao centro editado
				$opr_valida = true;
				$hierarquia = $db->fetch_assoc('select hierarquia from centro_resp where id = '.$array_dados['centro_pai_id']);
				$hierarquia = explode(',',$hierarquia['hierarquia']);
				if( in_array($array_dados['centro_resp_id'],$hierarquia) ){
					$opr_valida = false;
				}
			}
			
			if($opr_valida){

				$ctr_pai_id = $array_dados['centro_pai_id'];
				$ctr_pai_id_ini = $array_dados['centro_pai_id_ini'];
				
				if($ctr_pai_id==0 && $ctr_pai_id_ini!=0){ //quando o centro é movido para o primeiro nível
	
					//retira o gap se houver
					self::gapExcluir($db,$array_dados['nivel'],$array_dados['posicao'],$ctr_pai_id_ini);
								
					//atualiza a conta editada
					$max_pos = $db->fetch_assoc("select IFNULL(Max(posicao),0) posicao from centro_resp where centro_pai_id = 0");
					$pos = $max_pos['posicao'] + 1;
		
					$ctr_editar['nome'] = $array_dados['nome'];
					$ctr_editar['cod_centro'] = $pos;
					$ctr_editar['hierarquia'] = $array_dados['centro_resp_id'];
					$ctr_editar['centro_pai_id'] = $array_dados['centro_pai_id'];
					$ctr_editar['nivel'] = 1;
					$ctr_editar['posicao'] = $pos;
					$db->query_update('centro_resp',$ctr_editar,'id='.$array_dados['centro_resp_id']);
					//fim atualiza a conta editada
					
					//atualiza filhos da conta editada
					self::ctrFilhosAtualizar($db,$array_dados['centro_resp_id'],$ctr_editar['cod_centro'],$ctr_editar['hierarquia'],$ctr_editar['nivel']);
				
				}elseif($ctr_pai_id!=0 && $ctr_pai_id!=$ctr_pai_id_ini){ //quando o centro é movido para um nível que não seja o primeiro (centro_pai_id = 0)
	
					//retira o gap se houver
					self::gapExcluir($db,$array_dados['nivel'],$array_dados['posicao'],$ctr_pai_id_ini);
	
					//atualiza a conta editada
					$ctr_pai = $db->fetch_assoc("select cod_centro, hierarquia, nivel, posicao from centro_resp where id = ".$ctr_pai_id);
					$max_pos = $db->fetch_assoc("select IFNULL(Max(posicao),0) posicao from centro_resp where centro_pai_id = ".$ctr_pai_id);
					$pos = $max_pos['posicao'] + 1;
		
					$ctr_editar['nome'] = $array_dados['nome'];
					$ctr_editar['cod_centro'] = $ctr_pai['cod_centro'].'.'.$pos;
					$ctr_editar['hierarquia'] = $ctr_pai['hierarquia'].','.$array_dados['centro_resp_id'];
					$ctr_editar['centro_pai_id'] = $array_dados['centro_pai_id'];
					$ctr_editar['nivel'] = $ctr_pai['nivel'] + 1;
					$ctr_editar['posicao'] = $pos;
					$db->query_update('centro_resp',$ctr_editar,'id='.$array_dados['centro_resp_id']);
					//fim atualiza a conta editada
					
					//atualiza filhos da conta editada
					self::ctrFilhosAtualizar($db,$array_dados['centro_resp_id'],$ctr_editar['cod_centro'],$ctr_editar['hierarquia'],$ctr_editar['nivel']);
	
				}else{
					$db->query_update('centro_resp',$this->centro_resp_dados,'id='.$array_dados['centro_resp_id']);
				}
				
				//atualiza centro pai para tipo analtico se não houver mais filhos
				$qtd_filhos = $db->numRows("select id from centro_resp where centro_pai_id = ".$ctr_pai_id_ini." limit 0,1");
				if($qtd_filhos==0){
					$db->query("update centro_resp set tp_centro = 1 where id = ".$ctr_pai_id_ini);
				}

				$retorno = array("situacao"=>1,"notificacao"=>"Centro atualizado com sucesso.");

			}else{
				$retorno = array("situacao"=>2,"notificacao"=>"Operação inválida.");
			}
		
		}else{
			$retorno = array("situacao"=>2,"notificacao"=>"<br> Código de referência já cadastrado. Favor informar outro código.");
		}

		return $retorno;
	}

/*
===========================================================================================
EXCLUIR
===========================================================================================
*/	

	function centroRespExcluir($db,$centro_resp_id){
		$qtd_lancamentos = $db->numRows("select id from ctr_plc_lancamentos where centro_resp_id = ".$centro_resp_id." limit 0,1");
		$qtd_lancamentos_rcr = $db->numRows("select id from ctr_plc_lancamentos_rcr where centro_resp_id = ".$centro_resp_id." limit 0,1");
		$qtd_centros = $db->numRows("select id from centro_resp where centro_pai_id = ".$centro_resp_id." limit 0,1");
		if($qtd_lancamentos == 0 && $qtd_lancamentos_rcr == 0 && $qtd_centros == 0){ 
			//exclui centro
			$ctr = $db->fetch_assoc('select nivel, posicao, centro_pai_id from centro_resp where id = '.$centro_resp_id);
			self::gapExcluir($db,$ctr['nivel'],$ctr['posicao'],$ctr['centro_pai_id']);
			$db->query("DELETE from centro_resp where id = ".$centro_resp_id);

			//atualiza centro pai para tipo analtico se não houver mais filhos
			$qtd_filhos = $db->numRows("select id from centro_resp where centro_pai_id = ".$ctr['centro_pai_id']." limit 0,1");
			if($qtd_filhos==0){
				$db->query("update centro_resp set tp_centro = 1 where id = ".$ctr['centro_pai_id']);
			}

			$retorno = array("situacao" => 1,"notificacao"=>"Centro excluído com sucesso.");
		}else{
			$retorno = array("situacao" => 2,"notificacao"=>"A exclusão não é possível. Existem associações a lançamentos ou a outros centros.");
		}
		return $retorno;
	}


/*
===========================================================================================
EXIBIR
===========================================================================================
*/

	function centroRespExibir($db,$centro_resp_id){
		$centro_resp_exibir = $db->fetch_array($db->query("select * from centro_resp where id = ".$centro_resp_id));
		if($centro_resp_exibir['centro_pai_id']!=0){
			$centro_pai_nome = $db->fetch_assoc("select concat(cod_centro,' - ',nome) nome from centro_resp where id = ".$centro_resp_exibir['centro_pai_id']);
			$centro_resp_exibir['centro_pai_nome'] = $centro_pai_nome['nome'];
		}else{
			$centro_resp_exibir['centro_pai_nome'] = "";
		}
		$centro_resp_exibir['qtd_sub_centros'] = $db->numRows("select id from centro_resp where centro_pai_id = ".$centro_resp_id." limit 0,1");
		$centro_resp_exibir['qtd_lancamentos'] = $db->numRows("select id from ctr_plc_lancamentos where centro_resp_id = ".$centro_resp_id." limit 0,1");
		return $centro_resp_exibir;
	}

/*
===========================================================================================
LISTAR
===========================================================================================
*/

	function centroRespListar($db){
		//busca valores de cada centro
		/*
		$array_valores = array();
		$array_centros_analiticos = $db->fetch_all_array("
			select hierarquia, sum(IFNULL(cpl.valor,0)) valor
			from centro_resp pc
			left join ctr_plc_lancamentos cpl on pc.id = cpl.centro_resp_id
			where tp_centro = 1
			group by pc.id
		");
		foreach($array_centros_analiticos as $centro_analitico){
			$array_hierarquia = explode(',',$centro_analitico[hierarquia]);
			foreach($array_hierarquia as $centro_id){
				if(array_key_exists($centro_id,$array_valores)){
					$array_valores[$centro_id] += $centro_analitico[valor];
				}else{
					$array_valores[$centro_id] = $centro_analitico[valor];
				}
			}
		}
		*/
		//fim busca valores de cada centro

		//ordena centro de responsabilidade
		$nivel = $db->fetch_assoc("select max(nivel) nivel from centro_resp");
		$nivel = $nivel['nivel'];
		if($nivel!=''){
			$col_ordem = "";
			$i=1;
			while($i<=$nivel){
				$col_ordem.= 'ordem'.$i.' smallint(2),';
				$i++;
			}
			$db->query("
				CREATE TEMPORARY TABLE ctr_temp (
					id int(11),
					cod_centro varchar(20),
					nome varchar(50),
					".$col_ordem."
					nivel smallint(2),
					posicao smallint(4)
				) ENGINE=MEMORY
			");
		
			$array_centro_resp = array();
			$arr_ctr_n1 = $db->fetch_all_array('select id, cod_centro, nome, nivel, posicao from centro_resp where nivel = 1 and cod_centro > 0');
			foreach($arr_ctr_n1 as $ctr_n1){
				$arr_cod_centro = explode('.',$ctr_n1['cod_centro']);
				$i=1;
				while($i<=count($arr_cod_centro)){
					$ctr_n1['ordem'.$i] = $arr_cod_centro[$i-1];
					$i++;
				}
				$db->query_insert('ctr_temp',$ctr_n1);
				$arr_filhos = $db->fetch_all_array("select id, cod_centro, nome, nivel, posicao from centro_resp where centro_pai_id = ".$ctr_n1['id']);
				$hasFilho = count($arr_filhos);
				while($hasFilho){
					$arr_centro_filho_id = array();
					foreach($arr_filhos as $filho){
						$arr_cod_centro = explode('.',$filho['cod_centro']);
						$i=1;
						while($i<=count($arr_cod_centro)){
							$filho['ordem'.$i] = $arr_cod_centro[$i-1];
							$i++;
						}
						$db->query_insert('ctr_temp',$filho);
						$arr_centro_filho_id[] = $filho['id'];
					}
					$str_centro_filho_id = join(',',$arr_centro_filho_id);
					$arr_filhos = $db->fetch_all_array("select id, cod_centro, nome, nivel, posicao from centro_resp where centro_pai_id in (".$str_centro_filho_id.")");
					$hasFilho = count($arr_filhos);
				}
			}
		
			$col_ordem = "";
			$i=1;
			while($i<=$nivel){
				$col_ordem.= 'ordem'.$i.',';
				$i++;
			}
			$col_ordem = substr($col_ordem,0,strlen($col_ordem)-1);
			$query = 'select * from ctr_temp order by '.$col_ordem;
			$arr_ctr_ordem = $db->fetch_all_array('select * from ctr_temp order by '.$col_ordem);
			foreach($arr_ctr_ordem as $centro){
				$array_centro_resp[] = $centro;
			}
			$db->query('drop table ctr_temp');
			//fim ordena centro de responsabilidade
	
			$centro_resp_listar = '';
			$cont = 0;
			foreach($array_centro_resp as $centro_resp){
				$espc = $centro_resp['nivel'] * 10;  $espc = $espc.'px';
				if($centro_resp['nivel'] == 1){ $strong = 'bold;'; $fontN = '16px;';  }else{ $strong = 'normal;'; $fontN = '12px;'; }
				//$valor = $array_valores[$centro_resp[id]];
				//Tipo de Conta
				if($centro_resp['tp_centro'] == '1'){ $tp_centro = '<spam class="tipN" original-title="Analítico">(A)</span>'; }else{ $tp_centro = '<span class="tipN" original-title="Sintético">(S)</span>'; }
				$s = $db->fetch_assoc("select situacao from centro_resp where id = ".$centro_resp['id']); $sit = $s['situacao'];
				if($sit == '1'){ $situacao = '<span class="green">Ativo</span>'; $cor = ''; }else{ $situacao = '<span class="red">Inativo</span>'; $cor = 'color:#999;'; }
				
				$centro_resp_listar .= '
					<tr class="gradeA">
									<td>'.$cont.'</td>
									<td class="updates newUpdate" >
													
											<div class="uDate tbWF tipS" original-title="Nível" align="center" style="width:auto; padding-left:'.$espc.'"> <span class="uDay '.$atrasado.'" style="font-size:'.$fontN.$cor.';">'.$centro_resp['cod_centro'].' - </span></div>
												<span class="lDespesa tbWF" style="margin-top:2px; font-size:14px;">
													<a href="javascript://void(0);" style="cursor: default; font-size:'.$fontN.' font-weight:'.$strong.$cor.';" original-title="Descrição" class="tipS" >'.$centro_resp['nome'].'</a>													
												</span>											
																
											<div class="tbWFoption">										
													<a href="'.$centro_resp[id].'" original-title="Excluir" class="smallButton tipS redB btTBwf CentroRespExcluir"><img src="images/icons/light/close.png" width="10"></a>											
													<a href="javascript://void(0);" original-title="Editar" class="smallButton tipS btTBwf greyishB" onClick="centroRespExibir('.$centro_resp['id'].')"><img src="images/icons/light/pencil.png" width="10"></a> 
												'.$situacao.'
												</div>
																																																		
									</td> 
									</tr>
										
				';
				$cont++;
			}
		}else{
			$centro_resp_listar = '';
		}
	
		$centro_resp_listar ='
			<table cellpadding="0" cellspacing="0" border="0" class="display tblCentroResp">
				<thead>
				 <tr style="border-bottom: 1px solid #e7e7e7; font-weight:">
							<th></th>
								<th> 
								<table width="100%"><tr>
									<td>Descrição</td>
									<td width="60">Opções</td>
								</td></tr></table>
							</th> 
						</tr>
				</thead>
				<tbody>
					'.$centro_resp_listar.'
				</tbody>
			</table>
		';

		return $centro_resp_listar;
	}

	/*
	===========================================================================================
	VALIDAR CÓDIGO DE REFERÊNCIA
	===========================================================================================
	*/
	
	function codRefValidar($cod_ref,$cod_ref_ini=''){
		if( ($cod_ref!='') && ($cod_ref!=$cod_ref_ini) ){
			$qtd_registros = mysql_num_rows(mysql_query('select id from centro_resp where cod_ref = "'.$cod_ref.'"'));
			if($qtd_registros==0){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}

	/*
	===========================================================================================
	EXCLUÍR GAP DA POSIÇÃO DO CENTRO REMANEJADO
	===========================================================================================
	*/
	
	function gapExcluir($db,$nivel,$posicao,$centro_pai_id){
		//retira o gap se houver
		$ctr_gap = $db->fetch_all_array("select id, cod_centro, posicao from centro_resp where nivel = ".$nivel." and posicao > ".$posicao." and centro_pai_id = ".$centro_pai_id);
		foreach($ctr_gap as $gap){
			$cod_centro = explode('.',$gap['cod_centro']);
			$cod_centro_pos = count($cod_centro)-1;
			$cod_centro_novo = $cod_centro[$cod_centro_pos]-1;
			$cod_centro[$cod_centro_pos] = $cod_centro_novo;
			$cod_centro = join('.',$cod_centro);
			$ctr_gap_editar['cod_centro'] = $cod_centro;
			$ctr_gap_editar['posicao'] = $gap['posicao'] - 1;
			$db->query_update('centro_resp',$ctr_gap_editar,'id='.$gap['id']);
	
			//atualiza filhos
			//self::ctrFilhosGapAtualizar($db,$array_dados['centro_resp_id'],$ctr_editar['cod_centro'],$ctr_editar['hierarquia'],$ctr_editar['nivel']);

			$arr_aux[$gap['id']] = array('cod_centro_pai'=>$cod_centro);
	
			$ctr_filhos1 = $db->fetch_all_array("select id, posicao, centro_pai_id from centro_resp where centro_pai_id = ".$gap['id']);
	
			$filhos_exist = true;
	
			while($filhos_exist){
	
				$arr_aux2 = array();
	
				$qtd_filhos = 0;
				
				foreach($ctr_filhos1 as $filho){
			
					$i = $filho['centro_pai_id'];
					$cod_centro = $arr_aux[$i]['cod_centro_pai'];
	
					$ctr_filho_editar['cod_centro'] = $cod_centro.'.'.$filho['posicao'];
					$db->query_update('centro_resp',$ctr_filho_editar,'id='.$filho['id']);
					
					$ctr_filhos2 = $db->fetch_all_array("select id, posicao, centro_pai_id from centro_resp where centro_pai_id = ".$filho['id']);
					foreach($ctr_filhos2 as $filho2){
						$arr_aux2[] = array('id'=>$filho2['id'],'posicao'=>$filho2['posicao'],'centro_pai_id'=>$filho2['centro_pai_id']);
						$qtd_filhos ++;
					}
					
					$centro_pai_id = $filho['id'];
					$cod_centro_pai = $cod_centro.'.'.$filho['posicao'];
					
					$arr_aux[$centro_pai_id] = array('cod_centro_pai'=>$cod_centro_pai);
		
				}
				
				if($qtd_filhos==0){
					$filhos_exist = false;
				}else{
					$ctr_filhos1 = $arr_aux2;
				}
			}
	
		}
		//fim retira gap
	}

	/*
	===========================================================================================
	ATUALIZAR FILHOS DO CENTRO REMANEJADO
	===========================================================================================
	*/
	
	function ctrFilhosAtualizar($db,$ctr_id,$cod_centro,$hierarquia,$nivel){

		$arr_aux[$ctr_id] = array('cod_centro_pai'=>$cod_centro,'hierarquia_pai'=>$hierarquia,'nivel_pai'=>$nivel);

		$ctr_filhos1 = $db->fetch_all_array("select id, posicao, centro_pai_id from centro_resp where centro_pai_id = ".$ctr_id);

		$filhos_exist = true;

		while($filhos_exist){

			$arr_aux2 = array();

			$qtd_filhos = 0;
			
			foreach($ctr_filhos1 as $filho){
		
				$i = $filho['centro_pai_id'];
				$cod_centro = $arr_aux[$i]['cod_centro_pai'];
				$hierarquia = $arr_aux[$i]['hierarquia_pai'];
				$nivel = $arr_aux[$i]['nivel_pai'];

				$ctr_filho_editar['cod_centro'] = $cod_centro.'.'.$filho['posicao'];
				$ctr_filho_editar['hierarquia'] = $hierarquia.','.$filho['id'];
				$ctr_filho_editar['nivel'] = $nivel + 1;
				$db->query_update('centro_resp',$ctr_filho_editar,'id='.$filho['id']);
				
				$ctr_filhos2 = $db->fetch_all_array("select id, posicao, centro_pai_id from centro_resp where centro_pai_id = ".$filho['id']);
				foreach($ctr_filhos2 as $filho2){
					$arr_aux2[] = array('id'=>$filho2['id'],'posicao'=>$filho2['posicao'],'centro_pai_id'=>$filho2['centro_pai_id']);
					$qtd_filhos ++;
				}
				
				$centro_pai_id = $filho['id'];
				$cod_centro_pai = $cod_centro.'.'.$filho['posicao'];
				$hierarquia_pai = $hierarquia.','.$filho['id'];
				$nivel_pai = $nivel + 1;
				
				$arr_aux[$centro_pai_id] = array('cod_centro_pai'=>$cod_centro_pai,'hierarquia_pai'=>$hierarquia_pai,'nivel_pai'=>$nivel_pai);
	
			}
			
			if($qtd_filhos==0){
				$filhos_exist = false;
			}else{
				$ctr_filhos1 = $arr_aux2;
			}
			
		}

	}
	
}

?>









