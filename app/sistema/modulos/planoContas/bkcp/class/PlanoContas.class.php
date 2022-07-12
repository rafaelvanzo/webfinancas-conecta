<?php

class PlanoContas{

	var $planoContas_dados = array(
		//"cod_conta" => "",
		"cod_ref" => "",
		"nome" => "",
		"conta_pai_id" => "",
		"tp_conta" => "",
		"descricao" => "",
		"nivel" => "",
		"posicao" => "",
		"situacao" => "",
		"clfc_fc" => "",
		"clfc_dre" => "",
        "dedutivel" => ""
	);

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($array_dados=""){
		if($array_dados!=""){
			foreach($this->planoContas_dados as $chave => $valor){
				$this->planoContas_dados[$chave] = $array_dados[$chave];
			}
		}
	}


/*
===========================================================================================
INCLUÍR
===========================================================================================
*/

	function planoContasIncluir($db){

			$cod_ref_validar = self::codRefValidar($this->planoContas_dados[cod_ref]);

			if($cod_ref_validar){
		
				$contaPai = $this->planoContas_dados[conta_pai_id];
				
				if(empty($contaPai)){
					$posicao = $db->fetch_assoc('select Max(posicao) posicao from plano_contas where nivel = 1');
					
					//Acrescenta um novo registro cod_conta e posicao somando +1 ao valor do ultimo cod_conta do nível 1 registrado no db.
					$posicao_codConta = $posicao['posicao'] + 1; 
					$this->planoContas_dados['posicao'] = $posicao_codConta;
					$this->planoContas_dados['cod_conta'] = $posicao_codConta;
					$this->planoContas_dados['nivel'] = 1;
					$this->planoContas_dados['situacao'] = 0;
					$this->planoContas_dados['dt_cadastro'] = date('Y-m-d');	
					// Incluir no Banco
					$conta_id = $db->query_insert('plano_contas',$this->planoContas_dados);
					$db->query("update plano_contas set hierarquia = '".$conta_id."' where id = ".$conta_id);
				}else{
					
					$conta_pai = $db->fetch_assoc('select cod_conta, nivel, hierarquia from plano_contas where id ='.$this->planoContas_dados['conta_pai_id']);
	
					$nivel = $conta_pai['nivel'] + 1;
	
					$codConta = $db->fetch_assoc('select Max(posicao) posicao from plano_contas where conta_pai_id ='.$this->planoContas_dados['conta_pai_id'].' and nivel = '.$nivel);
					
					$posicao = $codConta['posicao'] + 1;
					
					$codConta = $conta_pai['cod_conta'].'.'.$posicao;
					
					$this->planoContas_dados['posicao'] = $posicao;
					$this->planoContas_dados['cod_conta'] = $codConta;
					$this->planoContas_dados['nivel'] = $nivel;
					$this->planoContas_dados['situacao'] = 0;
					$this->planoContas_dados['dt_cadastro'] = date('Y-m-d');
					// Incluir no Banco
					$conta_id = $db->query_insert('plano_contas',$this->planoContas_dados);
					$hierarquia = $conta_pai['hierarquia'].','.$conta_id;
					$db->query("update plano_contas set hierarquia = '".$hierarquia."' where id = ".$conta_id);
					$db->query("update plano_contas set tp_conta = 2 where id = ".$this->planoContas_dados['conta_pai_id']);
			}
			
			$retorno = array("situacao"=>1,"notificacao"=>"Conta cadastrada com sucesso.");
			
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

	function planoContasEditar($db,$array_dados){

		$cod_ref_validar = self::codRefValidar($array_dados['cod_ref'],$array_dados['cod_ref_ini']);

		if($cod_ref_validar){
			
			if($array_dados['conta_pai_id'] == $array_dados['plano_contas_id']){ //se a conta sintética e a conta editada forem a mesma, a conta_pai_id recebe zero
				$array_dados['conta_pai_id'] = 0;
				$this->planoContas_dados['conta_pai_id'] = 0;
			}else{ // verifica se a nova conta pai é um nível interno à conta editada
				$opr_valida = true;
				$hierarquia = $db->fetch_assoc('select hierarquia from plano_contas where id = '.$array_dados['conta_pai_id']);
				$hierarquia = explode(',',$hierarquia['hierarquia']);
				if( in_array($array_dados['plano_contas_id'],$hierarquia) ){
					$opr_valida = false;
				}
			}
			
			if($opr_valida){

				$plc_pai_id = $array_dados['conta_pai_id'];
				$plc_pai_id_ini = $array_dados['conta_pai_id_ini'];
				
				if($plc_pai_id_ini!=0 && $plc_pai_id==0){ //quando o centro é movido para o primeiro nível
	
					//retira o gap se houver
					self::gapExcluir($db,$array_dados['nivel'],$array_dados['posicao'],$plc_pai_id_ini);
								
					//atualiza a conta editada
					$max_pos = $db->fetch_assoc("select IFNULL(Max(posicao),0) posicao from plano_contas where conta_pai_id = 0");
					$pos = $max_pos['posicao'] + 1;
		
					$plc_editar['nome'] = $array_dados['nome'];
					$plc_editar['cod_conta'] = $pos;
					$plc_editar['hierarquia'] = $array_dados['plano_contas_id'];
					$plc_editar['conta_pai_id'] = $array_dados['conta_pai_id'];
					$plc_editar['nivel'] = 1;
					$plc_editar['posicao'] = $pos;
					$plc_editar['clfc_fc'] = $array_dados['clfc_fc'];
					$plc_editar['clfc_dre'] = $array_dados['clfc_dre'];
					$db->query_update('plano_contas',$plc_editar,'id='.$array_dados['plano_contas_id']);
					//fim atualiza a conta editada
					
					//atualiza filhos da conta editada
					self::plcFilhosAtualizar($db,$array_dados['plano_contas_id'],$plc_editar['cod_conta'],$plc_editar['hierarquia'],$plc_editar['nivel']);
				
				}elseif($plc_pai_id!=0 && $plc_pai_id!=$plc_pai_id_ini){ //quando o centro é movido para um nível que não seja o primeiro (centro_pai_id = 0)
	
					//retira o gap se houver
					self::gapExcluir($db,$array_dados['nivel'],$array_dados['posicao'],$plc_pai_id_ini);
	
					//atualiza a conta editada
					$plc_pai = $db->fetch_assoc("select cod_conta, hierarquia, nivel, posicao from plano_contas where id = ".$plc_pai_id);
					$max_pos = $db->fetch_assoc("select IFNULL(Max(posicao),0) posicao from plano_contas where conta_pai_id = ".$plc_pai_id);
					$pos = $max_pos['posicao'] + 1;
		
					$plc_editar['nome'] = $array_dados['nome'];
					$plc_editar['cod_conta'] = $plc_pai['cod_conta'].'.'.$pos;
					$plc_editar['hierarquia'] = $plc_pai['hierarquia'].','.$array_dados['plano_contas_id'];
					$plc_editar['conta_pai_id'] = $array_dados['conta_pai_id'];
					$plc_editar['nivel'] = $plc_pai['nivel'] + 1;
					$plc_editar['posicao'] = $pos;
					$plc_editar['clfc_fc'] = $array_dados['clfc_fc'];
					$plc_editar['clfc_dre'] = $array_dados['clfc_dre'];
					$db->query_update('plano_contas',$plc_editar,'id='.$array_dados['plano_contas_id']);
					//fim atualiza a conta editada
					
					//atualiza filhos da conta editada
					self::plcFilhosAtualizar($db,$array_dados['plano_contas_id'],$plc_editar['cod_conta'],$plc_editar['hierarquia'],$plc_editar['nivel']);
	
                    //atualiza novo centro pai para tipo sintético
                    $db->query("update plano_contas set tp_conta = 2 where id = ".$plc_pai_id);

				}else{
					$db->query_update('plano_contas',$this->planoContas_dados,'id='.$array_dados['plano_contas_id']);
				}
				
				//atualiza centro pai para tipo analtico se não houver mais filhos
				$qtd_filhos = $db->numRows("select id from plano_contas where conta_pai_id = ".$plc_pai_id_ini." limit 0,1");
				if($qtd_filhos==0){
					$db->query("update plano_contas set tp_conta = 1 where id = ".$plc_pai_id_ini);
				}

				$retorno = array("situacao"=>1,"notificacao"=>"Conta atualizada com sucesso.");

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

	function planoContasExcluir($db,$planoContas_id){
		$qtd_lancamentos = $db->numRows("select id from ctr_plc_lancamentos where plano_contas_id = ".$planoContas_id." limit 0,1");
		$qtd_lancamentos_rcr = $db->numRows("select id from ctr_plc_lancamentos_rcr where plano_contas_id = ".$planoContas_id." limit 0,1");
		$qtd_planos = $db->numRows("select id from plano_contas where conta_pai_id = ".$planoContas_id." limit 0,1");
		if($qtd_lancamentos == 0 && $qtd_lancamentos_rcr == 0 && $qtd_planos == 0){ 
			//exclui conta
			$plc = $db->fetch_assoc('select nivel, posicao, conta_pai_id from plano_contas where id = '.$planoContas_id);
			self::gapExcluir($db,$plc['nivel'],$plc['posicao'],$plc['conta_pai_id']);
			$db->query("DELETE from plano_contas where id = ".$planoContas_id);

			//atualiza conta pai para tipo analtico se não houver mais filhos
			$qtd_filhos = $db->numRows("select id from plano_contas where conta_pai_id = ".$plc['conta_pai_id']." limit 0,1");
			if($qtd_filhos==0){
				$db->query("update plano_contas set tp_conta = 1 where id = ".$plc['conta_pai_id']);
			}

			$retorno = array("situacao" => 1,"notificacao"=>"Conta excluída com sucesso.");
		}else{
			$retorno = array("situacao" => 2,"notificacao"=>"A exclusão não é possível. Existem associações a lançamentos ou a outro plano de contas.");
		}
		return $retorno;
	}


/*
===========================================================================================
EXIBIR
===========================================================================================
*/

	function planoContasExibir($db,$planoContas_id){
		$plano_contas_exibir = $db->fetch_array($db->query("select * from plano_contas where id = ".$planoContas_id));
		if($plano_contas_exibir['conta_pai_id']!=0){
			$conta_pai_nome = $db->fetch_assoc("select concat(cod_conta,' - ',nome) nome from plano_contas where id = ".$plano_contas_exibir['conta_pai_id']);
			$plano_contas_exibir['conta_pai_nome'] = $conta_pai_nome['nome'];
		}else{
			$plano_contas_exibir['conta_pai_nome'] = "";
		}
		$plano_contas_exibir['qtd_sub_contas'] = $db->numRows("select id from plano_contas where conta_pai_id = ".$planoContas_id." limit 0,1");
		$plano_contas_exibir['qtd_lancamentos'] = $db->numRows("select id from ctr_plc_lancamentos where plano_contas_id = ".$planoContas_id." limit 0,1");
		return $plano_contas_exibir;
	}

/*
===========================================================================================
LISTAR
===========================================================================================
*/

	//Lista dos planoContas
	function planoContasListar($db){
		//busca valores de cada conta
		/*
		$array_valores = array();
		$array_contas_analitias = $db->fetch_all_array("
			select hierarquia, sum(IFNULL(cpl.valor,0)) valor
			from plano_contas pc
			left join ctr_plc_lancamentos cpl on pc.id = cpl.plano_contas_id
			where tp_conta = 1
			group by pc.id
		");
		foreach($array_contas_analitias as $conta_analitica){
			$array_hierarquia = explode(',',$conta_analitica[hierarquia]);
			foreach($array_hierarquia as $conta_id){
				if(array_key_exists($conta_id,$array_valores)){
					$array_valores[$conta_id] += $conta_analitica[valor];
				}else{
					$array_valores[$conta_id] = $conta_analitica[valor];
				}
			}
		}
		*/
		//fim busca valores de cada conta

        //start: busca plano de contas ordenado
        $maiorNivel = $db->fetch_assoc('select max(nivel) nivel from plano_contas');
        $maiorNivel = $maiorNivel['nivel'];

        $ordem = '';
        $orderBy = '';

        if($maiorNivel>1){
            
            $arrayOrderBy = array();
            $arrayOrdem = array();

            for($i=2;$i<$maiorNivel;$i++){
                array_push($arrayOrderBy,'ordem'.$i);
                array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_conta,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
            }

            array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_conta,".",-1) as decimal),0) as ordem'.$i);
            array_push($arrayOrderBy,'ordem'.$i);

            $ordem = ','.join(',',$arrayOrdem);
            $orderBy = ','.join(',',$arrayOrderBy);
        }

        //$array_plano_contas = $db->fetch_all_array('select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as unsigned) as ordem from plano_contas where cod_conta > 0 order by ordem, nivel, posicao');

        $array_plano_contas = $db->fetch_all_array('
            select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as decimal) as ordem1'.$ordem.'
            from plano_contas 
            where cod_conta > 0 
            order by ordem1'.$orderBy);
        //end: busca plano de contas ordenado

        $cont = 0;
		$planoContas_listar = '';
        foreach($array_plano_contas as $planoContas){
            $espc = $planoContas['nivel'] * 10;  $espc = $espc.'px';
            if($planoContas['nivel'] == 1){ $strong = 'bold;'; $fontN = '16px;';  }else{ $strong = 'normal;'; $fontN = '12px;'; }
            
            //$valor = $array_valores[$planoContas[id]];
            //Tipo de Conta
            //if($planoContas[tp_conta] == '1'){ $tpConta = '<spam class="tipN" original-title="Analítico">(A)</span>'; }else{ $tpConta = '<span class="tipN" original-title="Sintético">(S)</span>'; }
            $s = $db->fetch_assoc("select situacao from plano_contas where id = ".$planoContas[id]); $sit = $s[situacao];
            if($sit == '0'){ $situacao = '<span class="green">Ativo</span>'; $cor = '';}else{ $situacao = '<span class="red">Inativo</span>'; $cor = 'color:#999;';}
            
            $planoContas_listar .= '
					<tr class="gradeA">
									<td style="display:none;">'.$cont.'</td>
									<td class="updates newUpdate" >
													
											<div class="uDate tbWF tipS" original-title="Nível" align="center" style="width:auto; padding-left:'.$espc.'"> <span class="uDay '.$atrasado.'" style="font-size:'.$fontN.$cor.'">'.$planoContas['cod_conta'].' - </span></div>
												<span class="lDespesa tbWF" style="margin-top:2px; font-size:14px;">
													<a href="javascript://void(0);" style="cursor: default; font-size:'.$fontN.' font-weight:'.$strong.$cor.'" original-title="Descrição" class="tipS " >'.$planoContas['nome'].'</a>													
												</span>											
																
											<div class="tbWFoption">										
													<a href="'.$planoContas[id].'" title="Excluir" class="smallButton redB btTBwf planoContasExcluir"><img src="images/icons/light/close.png" width="10"></a>											
													<a href="javascript://void(0);" title="Editar" class="smallButton btTBwf greyishB" onClick="planoContasExibir('.$planoContas['id'].','.$planoContas['tp_conta'].')"><img src="images/icons/light/pencil.png" width="10"></a> 
											'.$situacao.'
												</div>
												
									</td> 
									</tr>
				';
            $cont++;
        }
		
		$planoContas_listar = '
			<table cellpadding="0" cellspacing="0" border="0" class="display tblplanoContas">
				<thead>
				 <tr style="border-bottom: 1px solid #e7e7e7; font-weight:">
							<th style="display:none;"></th>
								<th> 
								<table width="100%"><tr>
									<td>Descrição</td>
									<td width="60">Opções</td>
									</td></tr></table>
								</th> 
							</tr>
				</thead>
				<tbody>
					'.$planoContas_listar.'
				</tbody>
			</table>
		';

		return $planoContas_listar;
	}

	/*
	===========================================================================================
	VALIDAR CÓDIGO DE REFERÊNCIA
	===========================================================================================
	*/
	
	function codRefValidar($cod_ref,$cod_ref_ini=''){
		if( ($cod_ref!='') && ($cod_ref!=$cod_ref_ini) ){
			$qtd_registros = mysql_num_rows(mysql_query('select id from plano_contas where cod_ref = "'.$cod_ref.'"'));
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
	
	function gapExcluir($db,$nivel,$posicao,$conta_pai_id){
		//retira o gap se houver
		$plc_gap = $db->fetch_all_array("select id, cod_conta, posicao from plano_contas where nivel = ".$nivel." and posicao > ".$posicao." and conta_pai_id = ".$conta_pai_id);
		foreach($plc_gap as $gap){
			$cod_conta = explode('.',$gap['cod_conta']);
			$cod_conta_pos = count($cod_conta)-1;
			$cod_conta_novo = $cod_conta[$cod_conta_pos]-1;
			$cod_conta[$cod_conta_pos] = $cod_conta_novo;
			$cod_conta = join('.',$cod_conta);
			$plc_gap_editar['cod_conta'] = $cod_conta;
			$plc_gap_editar['posicao'] = $gap['posicao'] - 1;
			$db->query_update('plano_contas',$plc_gap_editar,'id='.$gap['id']);
	
			//atualiza filhos
			//self::ctrFilhosGapAtualizar($db,$array_dados['centro_resp_id'],$ctr_editar['cod_centro'],$ctr_editar['hierarquia'],$ctr_editar['nivel']);

			$arr_aux[$gap['id']] = array('cod_conta_pai'=>$cod_conta);
	
			$plc_filhos1 = $db->fetch_all_array("select id, posicao, conta_pai_id from plano_contas where conta_pai_id = ".$gap['id']);
	
			$filhos_exist = true;
	
			while($filhos_exist){
	
				$arr_aux2 = array();
	
				$qtd_filhos = 0;
				
				foreach($plc_filhos1 as $filho){
			
					$i = $filho['conta_pai_id'];
					$cod_conta = $arr_aux[$i]['cod_conta_pai'];
	
					$plc_filho_editar['cod_conta'] = $cod_conta.'.'.$filho['posicao'];
					$db->query_update('plano_contas',$plc_filho_editar,'id='.$filho['id']);
					
					$plc_filhos2 = $db->fetch_all_array("select id, posicao, conta_pai_id from plano_contas where conta_pai_id = ".$filho['id']);
					foreach($plc_filhos2 as $filho2){
						$arr_aux2[] = array('id'=>$filho2['id'],'posicao'=>$filho2['posicao'],'conta_pai_id'=>$filho2['conta_pai_id']);
						$qtd_filhos ++;
					}
					
					$conta_pai_id = $filho['id'];
					$cod_conta_pai = $cod_conta.'.'.$filho['posicao'];
					
					$arr_aux[$conta_pai_id] = array('cod_conta_pai'=>$cod_conta_pai);
		
				}
				
				if($qtd_filhos==0){
					$filhos_exist = false;
				}else{
					$plc_filhos1 = $arr_aux2;
				}
			}
	
		}
		//fim retira gap
	}


	/*
	===========================================================================================
	ATUALIZAR FILHOS DA CONTA REMANEJADA
	===========================================================================================
	*/
	
	function plcFilhosAtualizar($db,$plc_id,$cod_conta,$hierarquia,$nivel){

		$arr_aux[$plc_id] = array('cod_conta_pai'=>$cod_conta,'hierarquia_pai'=>$hierarquia,'nivel_pai'=>$nivel);

		$plc_filhos1 = $db->fetch_all_array("select id, posicao, conta_pai_id from plano_contas where conta_pai_id = ".$plc_id);

		$filhos_exist = true;

		while($filhos_exist){

			$arr_aux2 = array();

			$qtd_filhos = 0;
			
			foreach($plc_filhos1 as $filho){
		
				$i = $filho['conta_pai_id'];
				$cod_conta = $arr_aux[$i]['cod_conta_pai'];
				$hierarquia = $arr_aux[$i]['hierarquia_pai'];
				$nivel = $arr_aux[$i]['nivel_pai'];

				$plc_filho_editar['cod_conta'] = $cod_conta.'.'.$filho['posicao'];
				$plc_filho_editar['hierarquia'] = $hierarquia.','.$filho['id'];
				$plc_filho_editar['nivel'] = $nivel + 1;
				$db->query_update('plano_contas',$plc_filho_editar,'id='.$filho['id']);
				
				$plc_filhos2 = $db->fetch_all_array("select id, posicao, conta_pai_id from plano_contas where conta_pai_id = ".$filho['id']);
				foreach($plc_filhos2 as $filho2){
					$arr_aux2[] = array('id'=>$filho2['id'],'posicao'=>$filho2['posicao'],'conta_pai_id'=>$filho2['conta_pai_id']);
					$qtd_filhos ++;
				}
				
				$conta_pai_id = $filho['id'];
				$cod_conta_pai = $cod_conta.'.'.$filho['posicao'];
				$hierarquia_pai = $hierarquia.','.$filho['id'];
				$nivel_pai = $nivel + 1;
				
				$arr_aux[$conta_pai_id] = array('cod_conta_pai'=>$cod_conta_pai,'hierarquia_pai'=>$hierarquia_pai,'nivel_pai'=>$nivel_pai);
	
			}
			
			if($qtd_filhos==0){
				$filhos_exist = false;
			}else{
				$plc_filhos1 = $arr_aux2;
			}
			
		}

	}

    //CARREGAR PLANO DE CONTAS
    //======================================================================================================================================================================================

    function CarregarPlanoContas($db,$params){

        //As abas do excel iniciam no índice zero
        //As colunas e linhas iniciam no índice um

        require_once("../../../php/reader.php");
        
        //start: Lê excel com o plano de contas selecionado
        $arquivo = "../modelos_plc/".$params['modelo'].'.xls';
        
        $xls = new Spreadsheet_Excel_Reader($arquivo);
        $xls->read($arquivo);
        $linhas = $xls->sheets[0]['numRows'];
        //end: Lê excel com o plano de contas selecionado

        //start: Inclui categorias no plano de contas
        //VERIFICAR SE O PRIMEIRO NÍVEL DE TODOS OS MODELOS SEMPRE TERÃO A MAIOR PROFUNDIDADE IGUAL
        if($params['modelo']=='engenharia')
            $maiorNivel = 4;
        elseif($params['modelo']=='odontologico')
            $maiorNivel = 2;

        $dtCadastro = date('Y-m-d');
        
        for($i = 2; $i <= $linhas; $i++){

            $codigo = $xls->sheets[0]['cells'][$i][1];

            $arrayCodigo = explode('.',$codigo);
            $nivel = count($arrayCodigo);

            $arrayInsert = array();

            $arrayInsert['cod_conta'] = $codigo;
            $arrayInsert['nome'] = utf8_encode($xls->sheets[0]['cells'][$i][2]);
            $arrayInsert['clfc_fc'] = $xls->sheets[0]['cells'][$i][3];
            $arrayInsert['clfc_dre'] = $xls->sheets[0]['cells'][$i][4];
            $arrayInsert['nivel'] = $nivel;
            $arrayInsert['tp_conta'] = ($nivel == $maiorNivel)? 1 : 2; //1: Analítico; 2: Sintético
            $arrayInsert['posicao'] = $arrayCodigo[$nivel-1];
            $arrayInsert['situacao'] = 0; //0: Ativo; 1:Inativo
            $arrayInsert['dt_cadastro'] = $dtCadastro;
            $arrayInsert['dedutivel'] = $xls->sheets[0]['cells'][$i][5];

            $db->query_insert('plano_contas',$arrayInsert);
        }
        
        //Inclui categoria Geral
        $arrayInsert = array();
        $arrayInsert['cod_conta'] = '0';
        $arrayInsert['hierarquia'] = '0';
        $arrayInsert['nome'] = 'Não alocado';
        $arrayInsert['situacao'] = 0; //0: Ativo; 1:Inativo
        $arrayInsert['dt_cadastro'] = $dtCadastro;
        $db->query_insert('plano_contas',$arrayInsert);
        $db->query('update plano_contas set id = 0 where cod_conta = "0"');
        //end: Inclui categorias no plano de contas
        
        //start: Atualiza conta_pai_id e hierarquia das categorias incluídas
        $query = mysql_query('select * from plano_contas where id > 0 order by id',$db->link_id);
        while($categoria = mysql_fetch_assoc($query)){

            $arrayUpdate = array();
            
            if($categoria['nivel']==1){
                $arrayUpdate['conta_pai_id'] = 0;
                $arrayUpdate['hierarquia'] = $categoria['id'];
            }else{
                
                $qtdCaracteres = strrpos($categoria['cod_conta'],'.');
                $codigoPai = substr($categoria['cod_conta'],0,$qtdCaracteres);

                $categoriaPai = $db->fetch_assoc('select id, hierarquia from plano_contas where cod_conta = "'.$codigoPai.'"');
                $arrayUpdate['hierarquia'] = $categoriaPai['hierarquia'].','.$categoria['id'];

                $arrayUpdate['conta_pai_id'] = $categoriaPai['id'];
            }

            $db->query_update('plano_contas',$arrayUpdate,'id = '.$categoria['id']);
        }
        //end: Atualiza conta_pai_id e hierarquia das categorias incluídas
    }

}

?>