<?php
/**
ESTUDAR VIABILIDADE DE JUNTAR TODAS AS FUNÇÕES DE QUITAÇÃO
*/

require('Ofx.class.php');
require('reader.php');

class Conciliacao extends Lancamento{

    //CONSTRUTOR
    //===========================================================================================
    
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

    /**
     * Ler arquivo do extrato bancário e incluir na tabela lancamentos_cnlc
     * para disponibilizar para conciliação
     * @param mixed $db 
     * @param mixed $array_dados 
     */
    function extratoIncluir($db,$array_dados){

        $importado = false;
		$cliente_id = $array_dados['cliente_id'];
		$usuario_id = $array_dados['usuario_id'];
		
		if($array_dados['tp_arq']=='ofx'){

			$caminho_arquivos = "../conciliacao/".$cliente_id.'_'.$usuario_id.'_*.OFX';

			$array_arquivos = glob($caminho_arquivos);
			
            foreach($array_arquivos as $arquivo){
			
                $ofx = new Ofx($arquivo);

                $contaImportada = $ofx->getAccount();
                
                $contaSelecionada = $db->fetch_assoc('select codigo, c.agencia, c.numero, c.numero_dv, c.cpf_cnpj from bancos b join contas c on b.id = c.banco_id where c.id = '.$array_dados['conta_id_import']);
                
                if(
                    $contaSelecionada['codigo'] == $contaImportada['bankId'] 
                    && ($contaSelecionada['numero'].$contaSelecionada['numero_dv'] == $contaImportada['accountId'] 
                        || $contaSelecionada['agencia'].$contaSelecionada['numero'].$contaSelecionada['numero_dv'] == $contaImportada['accountId'])
                    ){
                    
                    $array_lancamentos = $ofx->getTransactions();
                    
                    foreach ($array_lancamentos as $lancamento){
                        $array_insert['conta_id'] = $array_dados['conta_id_import'];
                        $array_insert['data'] = date("Y-m-d", strtotime(substr($lancamento->DTPOSTED, 0, 8)));
                        $array_insert['descricao'] = trim($lancamento->MEMO);
                        $array_insert['valor'] = $lancamento->TRNAMT;
                        $array_insert['fit_id'] = $lancamento->FITID;
                        $db->query_insert('lancamentos_cnlc',$array_insert);
                    }
                    $arq_saldo = $ofx->getBalance();
                    $arq_vl_saldo_banco = $arq_saldo['balance'];
                    $arq_dt_saldo_banco = $arq_saldo['date'];

                    $importado = true;
                }
				
                unlink($arquivo);
			}

		}else{

			$caminho_arquivos = "../conciliacao/".$cliente_id.'_'.$usuario_id.'_*.XLS';
			$array_arquivos = glob($caminho_arquivos);
			foreach($array_arquivos as $arquivo){
				
				$xls = new Spreadsheet_Excel_Reader($arquivo);
				$xls->read($arquivo);
				$linhas = $xls->sheets[0]['numRows'];
				$saldo_inicial = $db->valorToDouble($xls->sheets[0]['cells'][2][3]);
				$arq_vl_saldo_banco = $saldo_inicial;
				
				for($i = 3; $i <= $linhas; $i++){
					$valor = $xls->sheets[0]['cells'][$i][3];
					if(is_numeric($valor)){
						$data = $xls->sheets[0]['cells'][$i][1];
						$data = $db->data_to_sql($data);
						$descricao = $xls->sheets[0]['cells'][$i][2];
						$descricao = utf8_encode($descricao);
						$array_insert['conta_id'] = $array_dados['conta_id_import'];
						$array_insert['data'] = $data;
						$array_insert['descricao'] = $descricao;
						$array_insert['valor'] = $valor;
						$db->query_insert('lancamentos_cnlc',$array_insert);
						$arq_vl_saldo_banco += $valor;
						$arq_dt_saldo_banco = $data;
					}
				}
				unlink($arquivo);
			}

            $importado = true;
		}

		//atualiza data e saldo do banco
		$cf = $db->fetch_assoc('select dt_saldo_banco from contas where id = '.$array_dados['conta_id_import']);
		$cf_dt_saldo_banco = $cf['dt_saldo_banco'];
		
		$cf_dt_saldo_banco_ts = strtotime($cf_dt_saldo_banco);
		$arq_dt_saldo_banco_ts = strtotime($arq_dt_saldo_banco);
		
		if( $arq_dt_saldo_banco_ts > $cf_dt_saldo_banco_ts )
			$db->query('update contas set vl_saldo_banco = '.$arq_vl_saldo_banco.', dt_saldo_banco = "'.$arq_dt_saldo_banco.'" where id = '.$array_dados['conta_id_import']);
		
        return $importado;
	}

    /**
     * Excluír lançamentos do extrato bancário
     * @param mixed $db 
     * @param mixed $array_dados 
     */
    function lancamentosCnlcExcluir($db,$array_dados){
	    $db->query("delete from lancamentos_cnlc where id = ".$array_dados['lnct_cnlc_id']);
    }

    /**
     * Excluír em lote os lançamento do extrato bancário
     * @param mixed $db 
     * @param mixed $array_dados 
     */
    function lancamentosCnlcExcluirLote($db,$array_dados){
	    $array_lnct_id = explode(',',$array_dados['lncts_id']);
	    foreach($array_lnct_id as $lnct_id){
		    $db->query("delete from lancamentos_cnlc where id = ".$lnct_id);
	    }
    }

    /**
     * Listar lançamentos do extrato bancário
     * @param mixed $db 
     * @param mixed $cf_id 
     * @return array
     */
    function lancamentosListar($db,$cf_id){
	
	    $query_lnct_cnlc = "
		    select l.data, l.id, l.descricao, l.valor, date_format(l.data, '%d/%m/%Y') data_format, l.fit_id, c.id cf_id, c.descricao conta, b.nome banco
		    from lancamentos_cnlc l
		    left join contas c on l.conta_id = c.id
		    left join bancos b on c.banco_id = b.id
		    where is_boleto = 0
			    and l.conta_id = ".$cf_id."
		    order by data
	    ";
	
	    $array_lnct = $db->fetch_all_array($query_lnct_cnlc);
	
	    $lancamentos = "";

	    $qtd_lnct = 0;

        $lancamentosSugeridos = array(); //array de sugestões já realizadas para evitar sugestão duplicada

	    foreach($array_lnct as $lnct){
		
            //start: formatação da cor do lançamento
		    if($lnct['valor']>0){
			    $cor = 'blue'; /* color="#009900" */
			    $tp_lnct = "R";
		    }else{
			    $cor = 'red"'; /* color="#FF0000 */
			    $tp_lnct = "P";
			    $lnct['valor'] = $lnct['valor']*(-1);
		    }
            //end: formatação da cor do lançamento
		
		    if($lnct['banco']){
			    $banco_nome = ' - '.$lnct['banco'];
		    }else{
			    $banco_nome = "";
		    }

		    //start: busca sugestão de lançamentos no Web Finanças para conciliar

		    $array_dados = array(
			    'dt_vencimento' => $lnct['data_format'],
			    'valor' => $db->valorFormat($lnct['valor']),
			    'cf_id' => $lnct['cf_id'],
			    'tp_lnct' => $tp_lnct,
                'fit_id' => $lnct['fit_id'],
                'descricao' => $lnct['descricao']
		    );

		    $lnct_sugest = self::lnctSugest($db,$array_dados,'array');
		    
            //start: identifica se sugestão já foi feita para outro lançamento
            $i = 0;
            while( $i < count($lancamentosSugeridos) && array_key_exists($lnct_sugest[$i]['id'],$lancamentosSugeridos) && $lancamentosSugeridos[$lnct_sugest[$i]['id']]['is_rcr'] == $lnct_sugest[$i]['is_rcr']){
                $i++;
            }

            if($i == count($lancamentosSugeridos) && count($lancamentosSugeridos) > 0){

                $lnct_sugest = false;

            }else{

                $lancamentosSugeridos[$lnct_sugest[$i]['id']] = $lnct_sugest[$i];

                $lnct_sugest = $lnct_sugest[$i]; //futuramente utilizar todas as sugestões retorndas e escolher por input radio para conciliar
            }
            //end: identifica se sugestão já foi feita para outro lançamento

		    if(!empty($lnct_sugest)){

                if($lnct_sugest['compensado'] == 0){
                     
                    $lnct_sugest_dscr = '
				    <span style="cursor:pointer;color:;display:inline-block" id="ext_lnct_exist_dscr_'.$lnct_sugest['id'].'" data-lnct-id="'.$lnct_sugest['id'].'" onClick="lancamentosExibir(\''.$lnct_sugest['tipo'].'\','.$lnct['id'].','.$lnct_sugest['id'].','.$lnct_sugest['is_rcr'].',\'ext_\')">
					    Sugestão: '.$lnct_sugest['dt_vencimento'].' - '.$lnct_sugest['descricao'].' - '.$lnct_sugest['nome'].' - R$ '.$db->valorFormat($lnct_sugest['valor']).'
				    </span>
				    <span class="num" style="cursor:pointer;float:none;margin:auto">
					    <a href="javascript://" class="blueNum tipN" original-title="Conciliar com lançamento encontrado" onClick="lnctSugestQtr('.$lnct_sugest['id'].','.$lnct['id'].','.$lnct['cf_id'].',\''.$tp_lnct.'\',\''.$db->valorFormat($lnct['valor']).'\',\''.$lnct['data_format'].'\',\'checkbox\',\''.$lnct_sugest['is_rcr'].'\',\''.$lnct_sugest['conta_id_origem'].'\',\''.$lnct['fit_id'].'\');">Conciliar</a>
				    </span>									
				    ';

                    //Botões de ação
                    $btnAcao = '
                        <a href="javascript://void(0);" original-title="Lançamento existente" class="smallButton btTBwf greyishB tipS" id="link_buscar_'.$lnct['id'].'" onClick="lnctExist(\''.$lnct['cf_id'].'\','.$lnct['id'].',\''.$tp_lnct.'\',\'checkbox\');"><img src="images/icons/light/magnify.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Transferência" class="smallButton btTBwf tipS" id="link_trans_'.$lnct['id'].'" onClick="novoLancamento(\'T'.$tp_lnct.'\','.$lnct['id'].',\''.$lnct['fit_id'].'\')"><img src="images/icons/dark/transfer.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Novo lançamento" class="smallButton btTBwf tipS" id="link_quitar_'.$lnct['id'].'" onClick="novoLancamento(\''.$tp_lnct.'\','.$lnct['id'].',\''.$lnct['fit_id'].'\')"><img src="images/icons/dark/add.png" width="10"></a>
                    ';

                }else{

                    $lnct_sugest_dscr = '
				    <span style="cursor:pointer;color:;display:inline-block" id="ext_lnct_exist_dscr_'.$lnct_sugest['id'].'" data-lnct-id="'.$lnct_sugest['id'].'" onClick="lancamentosExibir(\''.$lnct_sugest['tipo'].'\','.$lnct['id'].','.$lnct_sugest['id'].','.$lnct_sugest['is_rcr'].',\'ext_\')">
					    Sugestão: '.$lnct_sugest['dt_vencimento'].' - '.$lnct_sugest['descricao'].' - '.$lnct_sugest['nome'].' - R$ '.$db->valorFormat($lnct_sugest['valor']).'
				    </span>
				    <span class="num" style="cursor:pointer;float:none;margin:auto">
					    <a href="javascript://" class="greenNum tipN" original-title="Lançamento conciliado">Conciliado</a>
				    </span>
				    ';

                    //Botões de ação
                    $btnAcao = '';
                }
			    
		    }else{

			    $lnct_sugest_dscr = '';

                //Botões de ação
                $btnAcao = '
                        <a href="javascript://void(0);" original-title="Lançamento existente" class="smallButton btTBwf greyishB tipS" id="link_buscar_'.$lnct['id'].'" onClick="lnctExist(\''.$lnct['cf_id'].'\','.$lnct['id'].',\''.$tp_lnct.'\',\'checkbox\');"><img src="images/icons/light/magnify.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Transferência" class="smallButton btTBwf tipS" id="link_trans_'.$lnct['id'].'" onClick="novoLancamento(\'T'.$tp_lnct.'\','.$lnct['id'].',\''.$lnct['fit_id'].'\')"><img src="images/icons/dark/transfer.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Novo lançamento" class="smallButton btTBwf tipS" id="link_quitar_'.$lnct['id'].'" onClick="novoLancamento(\''.$tp_lnct.'\','.$lnct['id'].',\''.$lnct['fit_id'].'\')"><img src="images/icons/dark/add.png" width="10"></a>
                    ';
		    }

            //end: busca sugestão de lançamentos no Web Finanças para conciliar

		    // ============ data ============
		    $dt_compensar = explode("/", $lnct['data_format']);
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
			    <tr class="gradeA" id="tbl-lnct-row-'.$lnct['id'].'">
				    <td style="display:none;">'.$lnct['data'].'</td>
				    <td class="updates newUpdate">
							
							    <div class="lnctCheckbox" style="float:left; padding-top:12px; padding-bottom:-12px;">
								    <input type="checkbox" value="'.$lnct['id'].'" id="check_'.$lnct['id'].'" class="'.$tp_lnct.'"/>
							    </div>
								
							    <div class="uDate tbWF tipS" style="margin-left:15px;" original-title="Compensação" align="center">
								
								    <span id="data_'.$lnct['id'].'" style="display:none">'.$lnct['data_format'].'</span>
								    <span  class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br>
								
							    </div>
							
							    <span class="lDespesa tbWF" style="width:65%;">
								    <a href="javascript://void(0);" style="cursor:default;display:block;" original-title="Descrição" class="tipS" ><strong id="dscr_'.$lnct['id'].'">'.$lnct['descricao'].'</strong></a>
								    '.$lnct_sugest_dscr.'
								    <span id="cf_id_'.$lnct['id'].'" style="display:none;">'.$lnct['cf_id'].'</span>
								    <span id="cf_dscr_'.$lnct['id'].'" style="display:none;">'.$lnct['conta'].$banco_nome.'</span>
							    </span>											
												
							     <div class="tbWFoption" id="link_excluir_'.$lnct['id'].'">
									    <a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS excluir" onClick="alertaExcluir('.$lnct['id'].',\'dTableExtratoBanco\');"><img src="images/icons/light/close.png" width="10"></a>
									    '.$btnAcao.'
							    </div> 
	
							    <div class="tbWFvalue '.$cor.'" >R$ <b id="vl_'.$lnct['id'].'">'.$db->valorFormat($lnct['valor']).' </b></div>
	
				    </td> 
			    </tr> 
		    ';

		    $qtd_lnct ++;

	    }
	
	    $lancamentos = '
		    <table cellpadding="0" cellspacing="0" border="0" class="display dTableExtratoBanco">
		
		    <thead>
		
		    <tr style="border-bottom: 1px solid #e7e7e7;" role="row">
		
			    <th style="display:none;">Ordem</th>
			
			    <th class="ckbHeaderCell" style="padding:1px 0px 1px 15px" role="columnheader" rowspan="1" colspan="1">
			
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

	    return array('lancamentos'=>$lancamentos,'qtd_lnct'=>$qtd_lnct);

    }

	/**
	 * Exibir lançamento retornado na janela de pesquisa
     * Verificar se é possível utilizar a função da classe Lancamento
	 * @param mixed $db 
	 * @param mixed $array_dados 
	 * @return array
	 */
	function lancamentosExibir($db,$array_dados){

		if($array_dados['rcr']){
			$tp_lancamento = $db->fetch_assoc("select tipo from lancamentos_recorrentes where id = ".$array_dados['lancamento_id']);
		}else{
			$tp_lancamento = $db->fetch_assoc("select tipo from lancamentos where id = ".$array_dados['lancamento_id']);
		}
		
		if($tp_lancamento['tipo']=="T"){
			$query = "
				select l.id, l.tipo, l.descricao, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta_origem, IFNULL(concat(c2.descricao,' - ',b2.nome),c2.descricao) conta_destino,
				l.conta_id_origem, l.conta_id_destino, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento,
				date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, l.observacao
				from lancamentos l
				left join contas c on l.conta_id_origem = c.id
				left join bancos b on c.banco_id = b.id
				left join contas c2 on l.conta_id_destino = c2.id
				left join bancos b2 on c2.banco_id = b2.id
				where l.id = ".$array_dados['lancamento_id']."
			";
			$jsonText = '';
		}else{
			
			if($array_dados['rcr']){

				$query = "
					select l.id, l.tipo, l.descricao, f.nome favorecido, f.id favorecido_id, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, c.id conta_id,
					date_format(l.dt_competencia, '%m/%Y') dt_competencia, date_format(l.dt_vencimento, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento,
					date_format(l.dt_vencimento, '%d/%m/%Y') dt_compensacao, l.valor, l.documento_id, l.forma_pgto_id, l.observacao, 1 rcr
					from lancamentos_recorrentes l
					left join contas c on l.conta_id = c.id
					left join favorecidos f on l.favorecido_id = f.id
					left join bancos b on c.banco_id = b.id
					where l.id = ".$array_dados['lancamento_id']."
				";
				
				//monta a lista de lannçamentos do centro de responsabilidade
				$query_ctr_plc_lancamentos = "
					select pc.cod_conta, cr.cod_centro, crl.id ctr_plc_lancamento_id, cr.id centro_resp_id, pc.id plano_contas_id, pc.nome conta, cr.nome centro, crl.valor, crl.porcentagem
					from ctr_plc_lancamentos_rcr crl
					left join centro_resp cr on crl.centro_resp_id = cr.id
					left join plano_contas pc on crl.plano_contas_id = pc.id
					where crl.lancamento_rcr_id = ".$array_dados['lancamento_id'];
	
				$array_ctr_plc_lancamentos = $db->fetch_all_array($query_ctr_plc_lancamentos);
	
				$ctr_plc_lancamentos = "";
	
				foreach($array_ctr_plc_lancamentos as $lancamento){
					$valor = number_format($lancamento['valor'],2,',','.');
					$porcentagem = $lancamento['porcentagem'] * 100;
					$ctr_plc_lancamentos .= '{"ctr_plc_lancamento_id":"'.$lancamento['ctr_plc_lancamento_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'"},';
				}
				
				$ctr_plc_lancamentos = substr($ctr_plc_lancamentos,0,-1); //retira a ultima virgula
	
				$jsonText = '
					['.$ctr_plc_lancamentos.']
				';
				//fim da montagem da lista de lançamentos do centro de responsabilidade

			}else{
			
				$query = "
					select l.id, l.tipo, l.descricao, f.nome favorecido, f.id favorecido_id, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, c.id conta_id,
					date_format(l.dt_competencia, '%m/%Y') dt_competencia, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, 
					date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, l.documento_id, l.forma_pgto_id, l.observacao, 0 rcr
					from lancamentos l
					left join contas c on l.conta_id = c.id
					left join favorecidos f on l.favorecido_id = f.id
					left join bancos b on c.banco_id = b.id
					where l.id = ".$array_dados['lancamento_id']."
				";
	
				//monta a lista de lannçamentos do centro de responsabilidade
				$query_ctr_plc_lancamentos = "
					select pc.cod_conta, cr.cod_centro, crl.id ctr_plc_lancamento_id, cr.id centro_resp_id, pc.id plano_contas_id, pc.nome conta, cr.nome centro, crl.valor, crl.porcentagem
					from ctr_plc_lancamentos crl
					left join centro_resp cr on crl.centro_resp_id = cr.id
					left join plano_contas pc on crl.plano_contas_id = pc.id
					where crl.lancamento_id = ".$array_dados['lancamento_id'];
	
				$array_ctr_plc_lancamentos = $db->fetch_all_array($query_ctr_plc_lancamentos);
	
				$ctr_plc_lancamentos = "";
	
				foreach($array_ctr_plc_lancamentos as $lancamento){
					$valor = number_format($lancamento['valor'],2,',','.');
					$porcentagem = $lancamento['porcentagem'] * 100;
					$ctr_plc_lancamentos .= '{"ctr_plc_lancamento_id":"'.$lancamento['ctr_plc_lancamento_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'"},';
				}
				
				$ctr_plc_lancamentos = substr($ctr_plc_lancamentos,0,-1); //retira a ultima virgula
	
				$jsonText = '
					['.$ctr_plc_lancamentos.']
				';
				//fim da montagem da lista de lannçamentos do centro de responsabilidade

			}
			
		}

		$lancamentos_visualizar = $db->fetch_array($db->query($query));
		$lancamentos_visualizar['valor'] = number_format($lancamentos_visualizar['valor'],2,',','.');
		$retorno = array("lancamento"=>$lancamentos_visualizar,"ctr_plc_lancamentos"=>$jsonText);
		return $retorno;
		
	}

	/**
	 * Sugestão de lançamentos do Web Finanças para conciliar
     * com lançamentos do extrato bancário
	 * @param mixed $db 
	 * @param mixed $array_dados 
	 * @param mixed $tp_retorno 
	 * @return array|string
	 */
	function lnctSugest($db,$array_dados,$tp_retorno=''){

		if($array_dados['descricao'] == 'REM BASICA' || $array_dados['descricao'] == 'CRED JUROS'){ //se for crédito de juros verifica exatamente pelo mesmo valor e vencimento
            
            $dt_vencimento_ini = $db->data_to_sql($array_dados['dt_vencimento']);
            $dt_vencimento_fim = $db->data_to_sql($array_dados['dt_vencimento']);
            $valor_ini = $db->valorToDouble($array_dados['valor']);
            $valor_fim = $db->valorToDouble($array_dados['valor']);

        }else{
            
            $dt_vencimento = $db->data_to_sql($array_dados['dt_vencimento']);
            $ts_dt_vencimento = strtotime($dt_vencimento);
            $dt_vencimento_ini = strtotime("-2 days",$ts_dt_vencimento);
            $dt_vencimento_ini = date('Y-m-d',$dt_vencimento_ini);
            $dt_vencimento_fim = strtotime("+2 days",$ts_dt_vencimento);
            $dt_vencimento_fim = date('Y-m-d',$dt_vencimento_fim);
            $valor = $db->valorToDouble($array_dados['valor']);
            $valor_ini = $valor-(0.1*$valor);
            $valor_fim = $valor+(0.1*$valor);
        }

		$cf_id = $array_dados['cf_id'];
		$tp_lnct = $array_dados['tp_lnct'];
        $fitId = $array_dados['fit_id'];

		if($tp_lnct=='R'){
			$tp_conta_id_transf = 'and conta_id_destino = ';
		}else{
			$tp_conta_id_transf = 'and conta_id_origem = ';
		}

		if($tp_retorno=='array'){ //chamado quando a função é requisitada pela função lancamentosListar

            $query_lnct = "
			(select l.id, l.tipo, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, l.descricao, l.valor, 0 as is_rcr, 0 as conta_id_origem, f.nome, l.fit_id, l.compensado
			from lancamentos l, favorecidos f
			where tipo = '".$tp_lnct."'
				and dt_vencimento >= '".$dt_vencimento_ini."'
				and dt_vencimento <= '".$dt_vencimento_fim."'
				and valor >= ".$valor_ini."
				and valor <= ".$valor_fim."
				and conta_id = ".$cf_id." 
				and (compensado = 0 || (compensado = 1 && fit_id = ".$fitId." && fit_id <> 0))
				and l.favorecido_id = f.id)

			union
		
			(select id, 'T' as tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, descricao, valor, 0 as is_rcr, conta_id_origem, '' as nome, fit_id, compensado
			from lancamentos
			where tipo = 'T'
				".$tp_conta_id_transf." ".$cf_id."
				and dt_vencimento >= '".$dt_vencimento_ini."'
				and dt_vencimento <= '".$dt_vencimento_fim."'
				and valor >= ".$valor_ini."
				and valor <= ".$valor_fim."
				and (compensado = 0 || (compensado = 1 && fit_id = ".$fitId." && fit_id <> 0)))

			union
				
			(select lr.id, lr.tipo, date_format(lr.dt_vencimento, '%d/%m/%Y') as dt_vencimento, lr.descricao, lr.valor, 1 as is_rcr, 0 as conta_id_origem, f.nome, '' as fit_id, '' as compensado
			from lancamentos_recorrentes lr, favorecidos f 
			where tipo = '".$tp_lnct."'
				and dt_vencimento >= '".$dt_vencimento_ini."'
				and dt_vencimento <= '".$dt_vencimento_fim."'
				and valor >= ".$valor_ini."
				and valor <= ".$valor_fim."
				and conta_id = ".$cf_id."
				and lr.favorecido_id = f.id)
		    ";

            $array_lancamentos = $db->fetch_all_array($query_lnct);

            $qtd_lnct = count($array_lancamentos);

			$retorno = array();

			if($qtd_lnct>0){
				foreach($array_lancamentos as $lnct){
					$retorno[] = array('id'=>$lnct['id'],'tipo'=>$lnct['tipo'],'dt_vencimento'=>$lnct['dt_vencimento'],'descricao'=>$lnct['descricao'],'nome'=>$lnct['nome'],'valor'=>$lnct['valor'],'is_rcr'=>$lnct['is_rcr'],'conta_id_origem'=>$lnct['conta_id_origem'],'compensado'=>$lnct['compensado']);
				}
			}
			
			return $retorno;
	
		}else{ //chamado quando a janela Lançamento Existente é aberta

            $query_lnct = "
			(select l.id, l.tipo, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, l.descricao, l.valor, 0 as is_rcr, 0 as conta_id_origem, f.nome
			from lancamentos l, favorecidos f
			where tipo = '".$tp_lnct."'
				and dt_vencimento >= '".$dt_vencimento_ini."'
				and dt_vencimento <= '".$dt_vencimento_fim."'
				and valor >= ".$valor_ini."
				and valor <= ".$valor_fim."
				and conta_id = ".$cf_id." 
				and compensado = 0
				and l.favorecido_id = f.id)

			union
		
			(select id, 'T' as tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, descricao, valor, 0 as is_rcr, conta_id_origem, '' as nome
			from lancamentos
			where tipo = 'T'
				".$tp_conta_id_transf." ".$cf_id."
				and dt_vencimento >= '".$dt_vencimento_ini."'
				and dt_vencimento <= '".$dt_vencimento_fim."'
				and valor >= ".$valor_ini."
				and valor <= ".$valor_fim."
				and compensado = 0)

			union
				
			(select lr.id, lr.tipo, date_format(lr.dt_vencimento, '%d/%m/%Y') as dt_vencimento, lr.descricao, lr.valor, 1 as is_rcr, 0 as conta_id_origem, f.nome
			from lancamentos_recorrentes lr, favorecidos f 
			where tipo = '".$tp_lnct."'
				and dt_vencimento >= '".$dt_vencimento_ini."'
				and dt_vencimento <= '".$dt_vencimento_fim."'
				and valor >= ".$valor_ini."
				and valor <= ".$valor_fim."
				and conta_id = ".$cf_id."
				and lr.favorecido_id = f.id)
		    ";

            $array_lancamentos = $db->fetch_all_array($query_lnct);

            $qtd_lnct = count($array_lancamentos);

			$qtd_add = 0;
			
			if($qtd_lnct>0){
				$lancamentos = '';
		
				foreach($array_lancamentos as $lancamento){
					$valor = number_format($lancamento['valor'],2,',','.');
					$lancamentos .= '<li id="li_lnct_sugest_'.$lancamento['id'].'"><div class="floatL"><input type="'.$array_dados['input_type'].'" name="lnct_sugest_id" class="lnctSugest" data-is_rcr="'.$lancamento['is_rcr'].'" data-tp_lnct="'.$lancamento['tipo'].'" data-conta_id_origem="'.$lancamento['conta_id_origem'].'" id="ckb_'.$lancamento['id'].'" value="'.$lancamento['id'].'"/> <span id="lnct_exist_dscr_'.$lancamento['id'].'">'.$lancamento['dt_vencimento'].' - '.$lancamento['descricao'].'</span></div> <div class="floatR"> R$ <span id="vl_sugest_'.$lancamento['id'].'">'.$valor.'</span> <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf tipS" id="link_sugest_edit_'.$lancamento['id'].'" onclick="lancamentosExibir(\''.$lancamento['tipo'].'\','.$array_dados['lnct_cnlc_id'].','.$lancamento['id'].','.$lancamento['is_rcr'].')" style="width:10px;margin-top:0px;display:none;"><img src="images/icons/dark/pencil.png" width="10"></a> </div> <div class="floatL" style="margin-left:15px;">'.$lancamento['nome'].'<div/></li>';
					$qtd_add++;
				}
				
				if($qtd_add){
					$lancamentos = '<ul class="partners">'.$lancamentos.'</ul>';
				}

			}else{
				$lancamentos = '<div style="padding:10px">Nenhum registro encontrado</div>';
			}

			return $lancamentos;
		}
	}

/*
===========================================================================================
BUSCAR LANÇAMENTOS EXISTENTES
===========================================================================================
*/

function lnctExistBuscar($db,$array_dados){
	
	$dt_ini = $db->data_to_sql($array_dados['dt_ini']);
	$dt_fim = $db->data_to_sql($array_dados['dt_fim']);
	$cf_id = $array_dados['cf_id'];
	$tp_lnct = $array_dados['tp_lnct'];
	$lncts_exist_id = explode(',',$array_dados['lncts_exist_id']);
	
	if($tp_lnct=='R'){
		$tp_conta_id_transf = 'and conta_id_destino = ';
	}else{
		$tp_conta_id_transf = 'and conta_id_origem = ';
	}
	
	$query_lnct = "
		(select l.id, l.tipo, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, l.descricao, l.valor, 0 as is_rcr, 0 as conta_id_origem, f.nome
		from lancamentos l
        join favorecidos f on l.favorecido_id = f.id
		where tipo = '".$tp_lnct."'
			and conta_id = ".$cf_id." 
			and compensado = 0
			and dt_vencimento >= '".$dt_ini."'
			and dt_vencimento <= '".$dt_fim."')

		union

		(select id, 'T' as tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, descricao, valor, 0 as is_rcr, conta_id_origem, '' nome
		from lancamentos 
		where tipo = 'T'
			".$tp_conta_id_transf." ".$cf_id."
			and compensado = 0
			and dt_vencimento >= '".$dt_ini."'
			and dt_vencimento <= '".$dt_fim."')

			union
			
		(select lr.id, lr.tipo, date_format(lr.dt_vencimento, '%d/%m/%Y') dt_vencimento, lr.descricao, lr.valor, 1 as is_rcr, 0 as conta_id_origem, f.nome
		from lancamentos_recorrentes lr
        join favorecidos f on lr.favorecido_id = f.id
		where tipo = '".$tp_lnct."'
			and conta_id = ".$cf_id." 
			and dt_vencimento >= '".$dt_ini."'
			and dt_vencimento <= '".$dt_fim."')

			order by dt_vencimento
	";
	
	$array_lancamentos = $db->fetch_all_array($query_lnct);
	
	$qtd_lnct = count($array_lancamentos);
	
	$qtd_add = 0;
	
	if($qtd_lnct>0){
		$lancamentos = '';

		foreach($array_lancamentos as $lancamento){
			$valor = number_format($lancamento['valor'],2,',','.');
			$lancamentos .= '<li id="li_lnct_sugest_'.$lancamento['id'].'"><div class="floatL"><input type="'.$array_dados['input_type'].'" name="lnct_sugest_id" class="lnctSugest" data-is_rcr="'.$lancamento['is_rcr'].'" data-tp_lnct="'.$lancamento['tipo'].'" data-conta_id_origem="'.$lancamento['conta_id_origem'].'" id="ckb_'.$lancamento['id'].'" value="'.$lancamento['id'].'"/> <span id="lnct_exist_dscr_'.$lancamento['id'].'">'.$lancamento['dt_vencimento'].' - '.$lancamento['descricao'].'</span></div> <div class="floatR"> R$ <span id="vl_sugest_'.$lancamento['id'].'">'.$valor.'</span> <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf tipS" id="link_sugest_edit_'.$lancamento['id'].'" onclick="lancamentosExibir(\''.$lancamento['tipo'].'\','.$array_dados['lnct_cnlc_id'].','.$lancamento['id'].','.$lancamento['is_rcr'].')" style="width:10px;margin-top:0px;display:none;"><img src="images/icons/dark/pencil.png" width="10"></a> </div><div class="floatL" style="margin-left:15px;">'.$lancamento['nome'].'<div/></li>';
			$qtd_add++;
		}
		
		if($qtd_add){
			$lancamentos = '<ul class="partners">'.$lancamentos.'</ul>';
		}

	}else{
		$lancamentos = '<div style="padding:10px">Nenhum registro encontrado</div>';
	}
	
	return $lancamentos;
	
}

//VALIDAR SE CÓDIGO DO BANCO DA CONTA FINANCEIRA É O MESMO DO ARQUIVO DE RETORNO
//===========================================================================================

function ValidaCodBanco($arquivo, $codBanco, $cpfCnpj){

    $cpfCnpj = str_replace(array('.','/','-'),'',$cpfCnpj);

    $arquivo->seek(0);
    
    $linha = $arquivo->current();

    $codBancoArquivo = substr($linha,0,strlen($codBanco));

    $cpfCnpjArquivo = substr($linha,18,14);
    
    if($codBanco == $codBancoArquivo && str_pad($cpfCnpj,14,'0',STR_PAD_LEFT) == $cpfCnpjArquivo)
        return true;
    else
        return false;
    
}

//INCLUÍR BOLETOS DO ARQUIVO DE RETORNO IMPORTADO
//===========================================================================================

	function boletosIncluir($db,$array_dados){ 

        //dados para teste
        /*
        $array_dados = array(
            'cliente_id'=> 191,
            'usuario_id'=> 162,
            'conta_id_import'=> 2,
        );
        */

        $cliente_id = $array_dados['cliente_id'];
		$usuario_id = $array_dados['usuario_id'];
        
        $banco = $db->fetch_assoc('select codigo, c.numero, c.cpf_cnpj from bancos b join contas c on b.id = c.banco_id where c.id = '.$array_dados['conta_id_import']);

        if($banco['codigo']=='001'){ //Banco Do Brasil
            $nosso_numero_caracter_ini = 37; 
            $nosso_numero_qtd_caracter = 20;
        }elseif($banco['codigo']=='033'){ //Santander
            $nosso_numero_caracter_ini = 40; 
            $nosso_numero_qtd_caracter = 13;
        }else if($banco['codigo']=='104'){ //Caixa Econômica
            $nosso_numero_caracter_ini = 39;
            $nosso_numero_qtd_caracter = 18;
        }else if($banco['codigo']=='756'){ //SICOOB
            $nosso_numero_caracter_ini = 38;
            $nosso_numero_qtd_caracter = 9;
        }else{
            $nosso_numero_caracter_ini = 37; 
            $nosso_numero_qtd_caracter = 20;
        }

		$array_arquivos = array();

		$caminho_arquivos_txt = "../conciliacao/".$cliente_id.'_'.$usuario_id.'_*.TXT';
		$array_arquivos_txt = glob($caminho_arquivos_txt);
		if($array_arquivos_txt)
			array_push($array_arquivos,$array_arquivos_txt);
		
		$caminho_arquivos_ret = "../conciliacao/".$cliente_id.'_'.$usuario_id.'_*.RET';
		$array_arquivos_ret = glob($caminho_arquivos_ret);
		if($array_arquivos_ret)
			array_push($array_arquivos,$array_arquivos_ret);
        
		foreach($array_arquivos as $key => $arquivo){

			//abre arquivo
			$file =  new SplFileObject($arquivo[0]);

            //Valida código do banco
            if(self::ValidaCodBanco($file,$banco['codigo'],$banco['cpf_cnpj'])){
            
                //posiciona na linha informada
                $loop = true;
                $file->seek(2);
                
                while($loop){

                    $line = $file->current(); //pega o conteúdo da linha corrente
                    $cod_movimento = substr($line,15,2);

                    if($cod_movimento == '06' || $cod_movimento == '17' || $cod_movimento == '09'){ //06 - liquidação de boleto com registro; 17 - liquidação de boleto sem registro; 09 - Baixa/Cancelamento;

                        $nosso_numero = trim(substr($line,$nosso_numero_caracter_ini,$nosso_numero_qtd_caracter));
                        $nosso_numero = ltrim($nosso_numero,'0'); //elimina os zeros que sobram quando o nosso número não utiliza todos os campos
                        $dt_vencimento = substr($line,73,8);
                        $pagador = substr($line,148,40);
                        $file->next();
                        $line = $file->current();
                        $vl_pago = substr($line,77,15);
                        $dt_compensacao = substr($line,145,8); //Coluna 138 -> Data da ocorrência | coluna 145 -> Data do crédito 
                        if($dt_compensacao == '00000000'){ $dt_compensacao = substr($line,137,8); }
                        $file->next();
                        $line = $file->current();
                        $array_insert = array(
                            "conta_id" => $array_dados['conta_id_import'],
                            "descricao" => $nosso_numero,
                            "valor" => number_format(substr($vl_pago,0,13).'.'.substr($vl_pago,13,2),2,'.',''), //$db->valorToDouble
                            "data" => substr($dt_compensacao,4,4).'-'.substr($dt_compensacao,2,2).'-'.substr($dt_compensacao,0,2),
                            "vencimento" => substr($dt_vencimento,4,4).'-'.substr($dt_vencimento,2,2).'-'.substr($dt_vencimento,0,2),
                            "pagador" => utf8_encode($pagador),
							"is_boleto" => 1,
							"cod_retorno" => $cod_movimento
                        );
                        $db->query_insert('lancamentos_cnlc',$array_insert);
                        
                    }else{
                        $file->next();
                    }
                    
                    $tp_registro = substr($line,7,1);
                    if($tp_registro!=3)
                        $loop = false;
                    
                }
                
                $file = null;

                unlink($arquivo[0]);

            }else{
                
                unlink($arquivo[0]);

                return false;
            }
		}

        return true;
	}

/*
===========================================================================================
FORMATAR NOSSO NÚMERO
===========================================================================================
*/

	function nossoNumeroFormat($cod_banco,$nosso_numero){
		if($cod_banco=='021'){ //banestes
			$pos = strpos($nosso_numero,'-');
			$prefix = ltrim(substr($nosso_numero,0,$pos),'0');
			$sufix = ltrim(substr($nosso_numero,$pos+1),'0');
			$nosso_numero = $prefix.'-'.$sufix;
		}else{
			$nosso_numero = ltrim($nosso_numero,'0');
		}
		return $nosso_numero;
	}

/*
===========================================================================================
LISTAR BOLETOS
===========================================================================================
*/

	function boletosListar($db,$cf_id){
		$query_lnct_cnlc = "
			select l.cod_retorno ,l.data, l.id, l.descricao, l.valor, date_format(l.data, '%d/%m/%Y') data_format, c.id cf_id, c.descricao conta, b.nome banco, b.codigo cod_banco, date_format(l.vencimento, '%d/%m/%Y') vencimento_format, l.pagador
			from lancamentos_cnlc l
			left join contas c on l.conta_id = c.id
			left join bancos b on c.banco_id = b.id
			where is_boleto = 1
				and l.conta_id = ".$cf_id."
			order by data
		";
		$array_lnct = $db->fetch_all_array($query_lnct_cnlc);
		$arq_ret = "";
		$qtd_blt = 0;
		foreach($array_lnct as $lnct){


			//busca boletos emitidos pelo Web Finanças
			$boleto_lnct = self::boletosBuscar($db,$lnct['descricao'],$lnct['cf_id']); 

				if($boleto_lnct){


					if($lnct['cod_retorno']==9){
			
						//Exibe boleto com o qual o lançamento foi conciliado
						$boleto_lnct_dscr = '
						<span original-title="Boleto encontrado no Web Finanças" class="tipN red" style="cursor:pointer;color:;display:inline-block;" id="ret_lnct_exist_dscr_'.$boleto_lnct['id'].'" data-lnct-id="'.$boleto_lnct['id'].'" onClick="lancamentosExibir(\''.$boleto_lnct['tipo'].'\','.$lnct['id'].','.$boleto_lnct['id'].',0,\'ret_\')">
							'.$db->sql_to_data($boleto_lnct['dt_vencimento']).' - '.$boleto_lnct['descricao'].' - '.$boleto_lnct['nome'].' - R$ '.$db->valorFormat($boleto_lnct['valor']).'
						</span>
						<span class="num" style="cursor:pointer;float:none;margin:auto; font-size:12px;">
							<a href="javascript://" class="redNum tipN" original-title="Boleto cancelado">Cancelado</a>
						</span>
						';
		
						$btnAcao = '';
		
						

					}elseif($boleto_lnct['compensado']==0){
					
						//Exibe boleto para conciliar com o lançamento
						$boleto_lnct_dscr = '
						<span original-title="Boleto encontrado no Web Finanças" class="tipN" style="cursor:pointer;color:;display:inline-block" id="ret_lnct_exist_dscr_'.$boleto_lnct['id'].'" data-lnct-id="'.$boleto_lnct['id'].'" onClick="lancamentosExibir(\''.$boleto_lnct['tipo'].'\','.$lnct['id'].','.$boleto_lnct['id'].',0,\'ret_\')">
							'.$db->sql_to_data($boleto_lnct['dt_vencimento']).' - '.$boleto_lnct['descricao'].' - '.$boleto_lnct['nome'].' - R$ '.$db->valorFormat($boleto_lnct['valor']).'
						</span>
						<span class="num" style="cursor:pointer;float:none;margin:auto">
							<a href="javascript://" class="blueNum tipN" original-title="Conciliar com boleto encontrado" onClick="lnctSugestQtr('.$boleto_lnct['id'].','.$lnct['id'].','.$lnct['cf_id'].',\'R\',\''.$db->valorFormat($lnct['valor']).'\',\''.$lnct['data_format'].'\',\'radio\',\'0\',\'0\',\'0\');">Conciliar</a>
						</span>
						';

						//Botões de ação
						$btnAcao = '
							<a href="javascript://void(0);" original-title="Lançamento existente" class="smallButton btTBwf greyishB tipS" id="link_buscar_'.$lnct['id'].'" onClick="lnctExist('.$lnct['cf_id'].','.$lnct['id'].',\'R\',\'radio\');"><img src="images/icons/light/magnify.png" width="10"></a>
							<a href="javascript://void(0);" original-title="Novo lançamento" class="smallButton btTBwf tipS" id="link_quitar_'.$lnct['id'].'" onClick="novoLancamento(\'R\','.$lnct['id'].',0)"><img src="images/icons/dark/add.png" width="10"></a>
						';

					}else{
					
						//Exibe boleto com o qual o lançamento foi conciliado
						$boleto_lnct_dscr = '
						<span original-title="Boleto encontrado no Web Finanças" class="tipN" style="cursor:pointer;color:;display:inline-block" id="ret_lnct_exist_dscr_'.$boleto_lnct['id'].'" data-lnct-id="'.$boleto_lnct['id'].'" onClick="lancamentosExibir(\''.$boleto_lnct['tipo'].'\','.$lnct['id'].','.$boleto_lnct['id'].',0,\'ret_\')">
							'.$db->sql_to_data($boleto_lnct['dt_vencimento']).' - '.$boleto_lnct['descricao'].' - '.$boleto_lnct['nome'].' - R$ '.$db->valorFormat($boleto_lnct['valor']).'
						</span>
						<span class="num" style="cursor:pointer;float:none;margin:auto">
							<a href="javascript://" class="greenNum tipN" original-title="Boleto conciliado">Conciliado</a>
						</span>
						';

						$btnAcao = '';
					}
					
				}else{
					$boleto_lnct_dscr = '';

					//Botões de ação
					$btnAcao = '
							<a href="javascript://void(0);" original-title="Lançamento existente" class="smallButton btTBwf greyishB tipS" id="link_buscar_'.$lnct['id'].'" onClick="lnctExist('.$lnct['cf_id'].','.$lnct['id'].',\'R\',\'radio\');"><img src="images/icons/light/magnify.png" width="10"></a>
							<a href="javascript://void(0);" original-title="Novo lançamento" class="smallButton btTBwf tipS" id="link_quitar_'.$lnct['id'].'" onClick="novoLancamento(\'R\','.$lnct['id'].',0)"><img src="images/icons/dark/add.png" width="10"></a>
						';
				}
				


		//	} //Fim else do cod_retorno = 9



				if($lnct['banco']){
					$banco_nome = ' - '.$lnct['banco'];
				}else{
					$banco_nome = "";
				}

				// ============ data ============
				$dt_compensar = explode("/", $lnct['data_format']);
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
				

				//Cor da linha dos boletos cancelados
				$cor_linha = ($lnct['cod_retorno']==9)? 'color: #A73939;' : '';

						
				$nosso_numero = self::nossoNumeroFormat($lnct['cod_banco'],$lnct['descricao']);
				
				$arq_ret .= '
					<tr class="gradeA" id="tbl-lnct-row-'.$lnct['id'].'">
						<td style="display:none;">'.$lnct['data'].'</td>
						<td class="updates newUpdate">
									
									<div class="lnctCheckbox02" style="float:left; padding-top:12px; padding-bottom:-12px;">
										<input type="checkbox" value="'.$lnct['id'].'" id="check_'.$lnct['id'].'" class="B"/>
									</div>
									
									<div class="uDate tbWF tipS"  style="margin-left:15px; '.$cor_linha.'" original-title="Compesação" align="center">
										<span id="data_'.$lnct['id'].'" style="display:none">'.$lnct['data_format'].'</span>
										<span class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br>
									</div>
									
									<span class="lDespesa tbWF" style="width:60%;">
										<a href="javascript://void(0);" style="cursor:default;display:block; '.$cor_linha.'"><strong original-title="Descrição" class=" tipS" id="dscr_'.$lnct['id'].'">Boleto nº '.$nosso_numero.' - Vencimento: '.$lnct['vencimento_format'].' - Pagador: '.$lnct['pagador'].'</strong></a>
										'.$boleto_lnct_dscr.'
										<span id="cf_id_'.$lnct['id'].'" style="display:none;">'.$lnct['cf_id'].'</span>
										<span id="cf_dscr_'.$lnct['id'].'" style="display:none;">'.$lnct['conta'].$banco_nome.'</span>
									</span>

									<div class="tbWFoption" id="link_excluir_'.$lnct['id'].'">
										<a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS excluir" onClick="alertaExcluir('.$lnct['id'].',\'dTableBoletos\');"><img src="images/icons/light/close.png" width="10"></a>
										'.$btnAcao.'
									</div>

									<div class="tbWFvalue blue" >R$ <b id="vl_'.$lnct['id'].'">'.$db->valorFormat($lnct['valor']).' </b></div>

						</td> 
					</tr> 
				';
				$qtd_blt ++;
			}


		$arq_ret = '
			<table cellpadding="0" cellspacing="0" border="0" class="display dTableBoletos">
			<thead>
			<tr style="border-bottom: 1px solid #e7e7e7;">
				<th style="display:none;">Ordem</th>
				<th class="ckbHeaderCell02" style="padding:4px 0px 5px 15px">
					<input type="checkbox" id="ckbTblHeader02" onclick="lnctChecarTodos(\'\');" style="padding-left:20px; padding-bottom:10px;">
				</th>
			</tr>
			</thead>
			<tbody>
				'.$arq_ret.'
			</tbody>
			</table>
		';
		
		return array('boletos'=>$arq_ret,'qtd_blt'=>$qtd_blt);

	}

/*
===========================================================================================
BUSCAR BOLETO EMITIDO PELO WEB FINANÇAS PARA CONCILIAR COM O ARQUIVO DE RETORNO
===========================================================================================
*/

	function boletosBuscar($db,$nosso_numero,$cf_id){

		$query_boleto = '
			select l.id, l.tipo, l.descricao, dt_vencimento, valor, f.nome, compensado
			from lancamentos l, boletos b, favorecidos f
			where b.nosso_numero = "'.$nosso_numero.'" 
				and l.id = b.lancamento_id
                and l.conta_id = '.$cf_id.'
				and l.favorecido_id = f.id';

		$boleto = $db->fetch_assoc($query_boleto);
		
		if(!empty($boleto)){
			return $boleto;
		}else{
			return false;
		}

	}

/*
===========================================================================================
EXCLUÍR BOLETOS DE CONCILIAÇÃO
===========================================================================================
*/

	function boletosCnlcExcluir($db,$array_dados){
		$db->query("delete from lancamentos_cnlc where id = ".$array_dados['boleto_cnlc_id']);
	}

/*
===========================================================================================
EXCLUÍR BOLETOS DE CONCILIAÇÃO EM LOTE
===========================================================================================
*/

	function boletoExcluirLote($db,$array_dados){
		$array_boletos_id = explode(',',$array_dados['boletos_id']);
		foreach($array_boletos_id as $boleto_id){
			$db->query("delete from lancamentos_cnlc where id = ".$boleto_id);
		}
	}

/*
========================================================================================================================
INICIALIZAR SALDOS E EXTRATOS
========================================================================================================================
*/

	function getCfSaldo($db,$cf_id){
		$saldo = $db->fetch_assoc('select vl_saldo, vl_saldo_banco, dt_saldo_banco from contas where id = '.$cf_id);
		$saldo['vl_saldo'] = $db->valorFormat($saldo['vl_saldo']);
		$saldo['vl_saldo_banco'] = $db->valorFormat($saldo['vl_saldo_banco']);
		$saldo['dt_saldo_banco'] = $db->sql_to_data($saldo['dt_saldo_banco']);
		return $saldo;
	}

/*
========================================================================================================================
INICIALIZAR SALDOS E EXTRATOS
========================================================================================================================
*/
	
	function cnlcIniciar($db,$cf_id){
		$extrato_bancario = self::lancamentosListar($db,$cf_id);
		$arq_retorno = self::boletosListar($db,$cf_id);
		$saldo = self::getCfSaldo($db,$cf_id);
		return array('extrato_bancario'=>$extrato_bancario['lancamentos'],'qtd_lnct'=>$extrato_bancario['qtd_lnct'],'arq_retorno'=>$arq_retorno['boletos'],'qtd_blt'=>$arq_retorno['qtd_blt'],'vl_saldo'=>$saldo['vl_saldo'],'vl_saldo_banco'=>$saldo['vl_saldo_banco'],'dt_saldo_banco'=>$saldo['dt_saldo_banco']);
	}

}

?>