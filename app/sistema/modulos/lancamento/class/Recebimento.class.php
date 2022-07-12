<?php

class Recebimento extends Lancamento{

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($db="",$array_dados=""){
		parent::__construct($db,$array_dados);
	}

    //INCLUÍR RECEBIMENTO
    //===========================================================================================

    function lancamentoIncluir($db,$array_dados){

		//Verificação se o input beneficiário esta preenchido. Se não estiver, atribui 0 ao valor.
		$this->lancamento_dados['favorecido_id_dep'] = ($this->lancamento_dados['favorecido_id_dep'] == 0 || $this->lancamento_dados['favorecido_id_dep'] == "")? 0 : $this->lancamento_dados['favorecido_id_dep'];



        $lancamentosHistorico = array();

        if($array_dados['qtd_parcelas'] == 1){

            if($array_dados['compensado'] == 1){

                $lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
                self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id'],'add');
                //if($array_dados['ct_resp_lancamentos']!=""){
                    parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"R",$this->lancamento_dados['valor']);
                    $db->query('update ctr_plc_lancamentos set situacao = 1 where lancamento_id = '.$lancamento_id);
                //}

            }else{
                
                $lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
                //if($array_dados['ct_resp_lancamentos']!=""){
                    parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"R",$this->lancamento_dados['valor']);
                //}
            }

            $historico = $this->lancamento_dados;
            $historico['id'] = $lancamento_id;
            array_push($lancamentosHistorico,$historico);

        }else{
            
            $total_parcelas = $array_dados['qtd_parcelas'];
            $this->lancamento_dados['valor'] = $db->valorToDouble($array_dados['valor_parcela']);
            
            //separa o dia, mes e ano da data de vencimento para incrementar nas parcelas subsequentes
            $dt_vencimento = $this->lancamento_dados['dt_vencimento'];
            $dt_vencimento = explode('-',$dt_vencimento);
            $dia = $dt_vencimento[2];
            $mes = $dt_vencimento[1];
            $ano = $dt_vencimento[0];
            
            //separa o mes e ano da data de competência para incrementar nas parcelas subsequentes
            $dt_competencia = $this->lancamento_dados['dt_competencia'];
            $dt_competencia = explode('-',$dt_competencia);
            $mes_c = $dt_competencia[1];
            $ano_c = $dt_competencia[0];
            
            //calcula a diferença em escala de meses entre a data de vencimento e data competência
            $ano_dif = $ano - $ano_c;
            $mes_dif = 0;
            if( $ano_dif == 0 ){
                $mes_dif = $mes - $mes_c;
            }elseif( $ano_dif == 1 ){
                $mes_dif = 12 - $mes_c + $mes;
            }else{
                $mes_dif = ($ano - $ano_c - 1) * 12 + 12 - $mes_c + $mes;
            }
            
            $frequencia = $array_dados['frequencia'];
            
            if($frequencia=='P'){
                
                $frequencia_mes = 0;
                $frequencia_dia = $array_dados['qtd_dias'];
                
            }else{
                
                if($frequencia < 30){
                    
                    $frequencia_mes = 0;
                    $frequencia_dia = $frequencia;
                    
                }else{
                    
                    switch($frequencia){
                        
                        case '30':	
                            $frequencia_mes = 1;
                            break;
                        
                        case '60':	
                            $frequencia_mes = 2;
                            break;
                        
                        case '90':	
                            $frequencia_mes = 3;					
                            break;
                        
                        case '120':	
                            $frequencia_mes = 4;
                            break;
                        
                        case '180':	
                            $frequencia_mes = 6;
                            break;
                        
                        case '360':
                            $frequencia_mes = 12;
                            break;
                    }
                    
                    $frequencia_dia = 0;
                }
            }
            
            //start: insere o lançamento pai
            $lnct_dscr = $this->lancamento_dados['descricao'];
            
            $this->lancamento_dados['descricao'] = $lnct_dscr.' - 1 de '.$total_parcelas;
            
            $this->lancamento_dados['parcela_numero'] = 1;
            
            $lancamento_pai_id = $db->query_insert('lancamentos',$this->lancamento_dados);

            $historico = $this->lancamento_dados;
            $historico['id'] = $lancamento_pai_id;
            array_push($lancamentosHistorico,$historico);

            $db->query("update lancamentos set lancamento_pai_id = ".$lancamento_pai_id." where id = ".$lancamento_pai_id);
            
            if($array_dados['compensado'] == 1)
                self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id'],'add');

            //if($array_dados['ct_resp_lancamentos']!=""){
                
                parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_pai_id,"R",$this->lancamento_dados['valor']);

                if($array_dados['compensado'] == 1)
                    $db->query('update ctr_plc_lancamentos set situacao = 1 where lancamento_id = '.$lancamento_pai_id);
            //}
            
            $this->lancamento_dados['lancamento_pai_id'] = $lancamento_pai_id;

            $this->lancamento_dados['compensado'] = 0;

            $this->lancamento_dados['dt_compensacao'] = '';
            //end: insere o lançamento pai

            $contador = 2;
            $fator_mes =  1;
            $fator_dia = 1;
            
            while($contador <= $total_parcelas){

                //============= Atualiza a data de vencimento e de competência da próxima parcela ===================================
                if($frequencia_mes!=0){

                    $mes_prox_venc = mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),'1',date($ano));
                    
                    $qtd_dias_mes = date('t',$mes_prox_venc);
                    
                    if( $qtd_dias_mes < $dia ){

                        $this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),date($qtd_dias_mes),date($ano)));

                        //$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes)-$mes_dif,'01',date($ano)));

                    }else{

                        $this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),date($dia),date($ano)));

                        //$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes)-$mes_dif,'01',date($ano)));
                    }

                }else{
                    
                    $this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes),date($dia)+($frequencia_dia*$fator_dia),date($ano)));
                    
                    //$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)-$mes_dif,'01',date($ano)));
                }

                $fator_mes += 1;
                $fator_dia += 1;
                //============= Fim atualização da data de vencimento e de competência da próxima parcela ============================
                
                $this->lancamento_dados['parcela_numero'] = $contador;
                $parcela_dscr = ' - '.$contador.' de '.$total_parcelas;
                $this->lancamento_dados['descricao'] = $lnct_dscr.$parcela_dscr;
                $lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
                //if($array_dados['ct_resp_lancamentos']!=""){
                    parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"R",$this->lancamento_dados['valor']);
                //}

                $contador += 1;

                $historico = $this->lancamento_dados;
                $historico['id'] = $lancamento_id;
                array_push($lancamentosHistorico,$historico);
            }

            $lancamento_id = $lancamento_pai_id;
        }

        Lancamento::HistoricoLancamentos($db,$lancamentosHistorico);

        return $lancamento_id;
    }

/*
===========================================================================================
EDITAR
===========================================================================================
*/

	function lancamentoEditar($db,$array_dados){      
         
        //REMOVE ESSES CAMPOS DO ARRAY PARA NÃO ATRAPALHAR O REGISTRO DOS LANÇAMENTOS PARCELADOS
        unset($this->lancamento_dados['dt_venc_ref']);
        unset($this->lancamento_dados['lancamento_pai_id']);
        unset($this->lancamento_dados['parcela_numero']);
        unset($this->lancamento_dados['qtd_parcelas']);
		unset($this->lancamento_dados['frequencia']);
		

		//Verificação se o input beneficiário esta preenchido. Se não estiver, atribui 0 ao valor.
		$this->lancamento_dados['favorecido_id_dep'] = ($this->lancamento_dados['favorecido_id_dep'] == 0 || $this->lancamento_dados['favorecido_id_dep'] == "")? 0 : $this->lancamento_dados['favorecido_id_dep'];


        
        if( $array_dados['compensado']!=1 ){

			if($array_dados['rcr']){ //usado para editar lançamentos recorrentes na conciliação; substituir depois por recebimentosRcrEditar
				 $lnct_prog_id = parent::rcr_to_prog($db,$array_dados);
				 $retorno = $lnct_prog_id;
			}else{
				
				$lnct = $db->fetch_assoc('select valor, compensado, conta_id from lancamentos where id = '.$array_dados['lancamento_id'].' for update');
				
                //start: exclui boleto da conta anterior caso ele exista
                if($lnct['conta_id'] != $array_dados['conta_id'])
                    $db->query('delete from boletos where lancamento_id = '.$array_dados['lancamento_id']);
                //end: exclui boleto da conta anterior caso ele exista

				if($lnct['compensado']){
	
					$atualizar_saldo = self::atualizarSaldoConta($db,$lnct['valor'],$lnct['conta_id'],'exc',$array_dados['lancamento_id']);
					if($atualizar_saldo){
                       
						$this->lancamento_dados['compensado'] = 0;
						$this->lancamento_dados['dt_compensacao'] = '0000-00-00';
                        $this->lancamento_dados['fit_id'] = '';
                        $db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
						//if( $array_dados['ct_resp_lancamentos']!='' ){
							parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R",$this->lancamento_dados['valor']);
							$db->query('update ctr_plc_lancamentos set situacao = 0 where lancamento_id = '.$array_dados['lancamento_id']);
						//}
					}
					$retorno = $atualizar_saldo;
	
				}else{

					$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
					parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R",$this->lancamento_dados['valor']);
					$retorno = true;
					
				}
			}
	
		}else{

			$lnct = $db->fetch_assoc('select compensado, conta_id from lancamentos where id = '.$array_dados['lancamento_id'].' for update');
			
            //start: exclui boleto da conta anterior caso ele exista
            if($lnct['conta_id'] != $array_dados['conta_id'])
                $db->query('delete from boletos where lancamento_id = '.$array_dados['lancamento_id']);
            //end: exclui boleto da conta anterior caso ele exista

			if($lnct['compensado'])
				$atualizar_saldo = self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$array_dados['conta_id'],'edit',$array_dados['lancamento_id']);
			else
				$atualizar_saldo = self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$array_dados['conta_id'],'add',$array_dados['lancamento_id']);
			
			if($atualizar_saldo){

				$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
				parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R",$this->lancamento_dados['valor']);
                $db->query('update ctr_plc_lancamentos set situacao = 1 where lancamento_id = '.$array_dados['lancamento_id']);
			}
			
			$retorno = $atualizar_saldo;
		}

        if($retorno){
            $historico = $this->lancamento_dados;
            $historico['id'] = $array_dados['lancamento_id'];
            Lancamento::HistoricoLancamentos($db,array($historico));
        }

        return $retorno;
	}

/*
===========================================================================================
EDITAR RECORRENTE
===========================================================================================
*/

	function lancamentoRcrEditar($db,$array_dados){
        $array_dados['qtd_parcelas'] = 1;
		$incluir = self::lancamentoIncluir($db,$array_dados); 
		if($incluir){
			parent::atualizarVencimentoRcr($db,$array_dados['lancamento_id']);
		}
		return $incluir;
	}

/*
===========================================================================================
EXCLUÍR RECEBIMENTO
===========================================================================================
*/

	function lancamentoExcluir($db,$array_dados){

		$lancamento_id = $array_dados['lancamento_id'];

        $compensado = $db->fetch_assoc('select tipo, compensado, favorecido_id, conta_id, dt_vencimento from lancamentos where id = '.$lancamento_id.' for update');

		if( $compensado['compensado']!=1 ){
			
			$lancamento = $db->fetch_assoc("select lancamento_pai_id from lancamentos where id = ".$lancamento_id);
			$db->query("delete from lancamentos where id = ".$lancamento_id);
			$db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id);
			$db->query("delete from boletos where lancamento_id = ".$lancamento_id);
			parent::anexoExcluir($db,$lancamento_id);
			
            //====== refaz a contagem das parcelas===============================================================================================
			if($lancamento['lancamento_pai_id'] != 0){
				$qtd_parcelas = $db->fetch_assoc("select count(id), qtd_parcelas from lancamentos where lancamento_pai_id = ".$lancamento['lancamento_pai_id']);
				if($qtd_parcelas['qtd_parcelas'] > 0){
					$array_parcelas = $db->fetch_all_array("select id from lancamentos where lancamento_pai_id = ".$lancamento['lancamento_pai_id']." order by id");
					$contador = 1;
					foreach($array_parcelas as $parcela){
						$parcela_id = $parcela['id'];
						$where = "id = ".$parcela_id;
						$dados['parcela_numero'] = $contador;
						$dados['qtd_parcelas'] = $qtd_parcelas['qtd_parcelas'];
						$db->query_update('lancamentos',$dados,$where);
						$contador += 1;
					}
				}
			}
			//======= fim refaz a contagem das parcelas =========================================================================================

			$retorno = true;

		}else{

			$lnct = $db->fetch_assoc("select valor, conta_id from lancamentos where id = ".$lancamento_id." for update");
			$atualizar_saldo = self::atualizarSaldoConta($db,$lnct['valor'],$lnct['conta_id'],'exc',$lancamento_id);
			if($atualizar_saldo){
				$db->query("delete from lancamentos where id = ".$lancamento_id);
				$db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id);
				$db->query("delete from boletos where lancamento_id = ".$lancamento_id);
				parent::anexoExcluir($db,$lancamento_id);
			}
			$retorno = $atualizar_saldo;
		}
		
        if($retorno){
            $historico = $this->lancamento_dados;
            $historico['id'] = $lancamento_id;
            $historico['tipo'] = $compensado['tipo'];
            $historico['favorecido_id'] = $compensado['favorecido_id'];
            $historico['conta_id'] = $compensado['conta_id'];
            $historico['dt_vencimento'] = $compensado['dt_vencimento'];
            $historico['excluido'] = 1;
            Lancamento::HistoricoLancamentos($db,array($historico));
        }

        return $retorno;
	}

/*
===========================================================================================
EXCLUÍR RECEBIMENTO PARCELADO
===========================================================================================
*/

	function lancamentoExcluirParcelado($db,$array_dados){

        if($array_dados['este_lancamento']=='true')
            self::lancamentoExcluir($db,$array_dados);

		$lancamentosHistorico = array();
        
        $lancamento_pai_id = $array_dados['lnct_pai_id'];

        if($array_dados['a_vencer']=='true' && $array_dados['vencido']=='true')
            $tds_lnct_parcelados = $db->fetch_all_array('select id, tipo, compensado, favorecido_id, conta_id, dt_vencimento, parcela_numero from lancamentos where lancamento_pai_id = '.$lancamento_pai_id.' AND compensado = 0');
        elseif($array_dados['a_vencer']=='true')
            $tds_lnct_parcelados = $db->fetch_all_array('select id, tipo, compensado, favorecido_id, conta_id, dt_vencimento, parcela_numero from lancamentos where lancamento_pai_id = '.$lancamento_pai_id.' AND compensado = 0 and dt_vencimento >= "'.date('Y-m-d').'"');
        else
            $tds_lnct_parcelados = $db->fetch_all_array('select id, tipo, compensado, favorecido_id, conta_id, dt_vencimento, parcela_numero from lancamentos where lancamento_pai_id = '.$lancamento_pai_id.' AND compensado = 0 and dt_vencimento < "'.date('Y-m-d').'"');
        
        if(count($tds_lnct_parcelados)>0){

            $obs = '*****************************************************
            Parcelas excluídas: ';

            foreach($tds_lnct_parcelados as $lancamento){
                
                $db->query("delete from lancamentos where id = ".$lancamento['id']);
                $db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento['id']);
                $db->query("delete from boletos where lancamento_id = ".$lancamento['id']);
                parent::anexoExcluir($db,$lancamento['id']);
                
                $obs .= '
                - Parcela nº '.$lancamento['parcela_numero'].' com vencimento '.$db->sql_to_data($lancamento['dt_vencimento']);

                $lancamento['excluido'] = 1;
                array_push($lancamentosHistorico,$lancamento);
            }

            $obs .= '
            *****************************************************';

            Lancamento::HistoricoLancamentos($db,$lancamentosHistorico);

            //====== refaz a contagem das parcelas===============================================================================================
            $qtd_parcelas = $db->fetch_assoc("select count(id), qtd_parcelas from lancamentos where lancamento_pai_id = ".$lancamento_pai_id);
			if($qtd_parcelas['qtd_parcelas'] > 0){
				$array_parcelas = $db->fetch_all_array("select id, observacao from lancamentos where lancamento_pai_id = ".$lancamento_pai_id." order by id");
				$contador = 1;
				foreach($array_parcelas as $parcela){
					$parcela_id = $parcela['id'];
					$where = "id = ".$parcela_id;
					$dados['parcela_numero'] = $contador;
					$dados['qtd_parcelas'] = $qtd_parcelas['qtd_parcelas'];
                    $dados['observacao'] = $parcela['observacao'] .= $obs;
					$db->query_update('lancamentos',$dados,$where);
					$contador += 1;
				}
			}
            //======= fim refaz a contagem das parcelas =========================================================================================
        }
        
		return true;   
	}
    
/*
===========================================================================================
COMPENSAR
===========================================================================================
*/

	function lancamentoCompensar($db,$array_dados){
		$db->query('update lancamentos set compensado = 1, dt_compensacao = "'.$this->lancamento_dados['dt_compensacao'].'" where id = '.$array_dados['lancamento_id']);
		self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id'],'add');
		parent::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R",$this->lancamento_dados['valor']);
		$db->query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$array_dados['lancamento_id']);

        $historico = $this->lancamento_dados;
        $historico['id'] = $array_dados['lancamento_id'];
        Lancamento::HistoricoLancamentos($db,array($historico));

		return true;
	}

/*
===========================================================================================
COMPENSAR RECORRENTE
===========================================================================================
*/
 
	function lancamentoRcrCompensar($db,$array_dados){
        $array_dados['qtd_parcelas'] = 1;
        $array_dados['compensado'] = 1;
		$compensar = self::lancamentoIncluir($db,$array_dados);
		if($compensar){
			parent::atualizarVencimentoRcr($db,$array_dados['lancamento_id']);
		}
		return $compensar;
	}

/*
===========================================================================================
CONCILIAR
===========================================================================================
*/ 

	function conciliarLancamento($db,$array_dados){

		$lncts_exist_id = str_replace('\"','"',$array_dados['lncts_exist_id']);
		$array_lnct_exist_id = json_decode($lncts_exist_id, true);
		
		//verifica se há transferências e se o valor total delas pode ser quitado pela conta de origem

			//agrupa o valor das transferências por conta financeira
			$saldo_suficiente = true;
			$vl_total_transf = 0;
			$array_vl_cf = array();
			foreach($array_lnct_exist_id as $lnct_exist){
				$conta_id_origem = $lnct_exist['conta_id_origem'];
				if($lnct_exist['tipo']=='T'){
					$valor = $db->valorToDouble($lnct_exist['valor']);
					if(array_key_exists($conta_id_origem,$array_vl_cf)){
						$array_vl_cf[$conta_id_origem] += $valor;
					}else{
						$array_vl_cf[$conta_id_origem] = $valor;
					}
				}
			}

			//verifica se o saldo total de cada conta financeira é maior ou igual ao valor dos lançamentos
			foreach($array_vl_cf as $conta_id_origem => $vl_total_transf){
				$conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$conta_id_origem." for update");
				if($conta['saldo_total']<$vl_total_transf)
					$saldo_suficiente = false;
			}

		//fim verifica transferências

		if($saldo_suficiente){

			foreach($array_lnct_exist_id as $lnct_exist){
	
				if(!$lnct_exist['is_rcr']){
					
					$lancamento = $db->fetch_assoc("select * from lancamentos where id = ".$lnct_exist['id']);

                    if($lancamento['compensado'] == 0){
                    
                        if($lancamento['tipo']=='T'){
                            
                            $transferencia = new Transferencia();
                            $transferencia->conciliarLancamento($db,$array_dados);
                            
                        }else{
                            
                            //if($lancamento['dt_compensacao']=='0000-00-00'){
							$dt_compensacao = $db->data_to_sql($array_dados['dt_vencimento_cnlc']);
                            //}else{
							//$dt_compensacao = $lancamento['dt_compensacao'];
                            //}
                            
                            $valor = $db->valorToDouble($lnct_exist['valor']); //$lancamento['valor'];

                            $observacao = $lancamento['observacao'];
                            
                            $diferenca = 0;

                            if($valor != $lancamento['valor']){
                                
                                $diferenca = $valor - $lancamento['valor'];
                                
                                if($diferenca > 0)
                                    $observacao .= ' Juros/Multa: R$ '.$db->valorFormat($valor - $lancamento['valor']);
                                else
                                    $observacao .= ' Desconto: R$ '.$db->valorFormat(($valor - $lancamento['valor'])*(-1));
                            }
                                
                            $db->query("update lancamentos set valor = ".$valor.", dt_compensacao = '".$dt_compensacao."', observacao = '".$observacao."', valor_multa = ".$diferenca.", compensado = 1, fit_id = ".$array_dados['fit_id']." where id = ".$lnct_exist['id']);
                            
                            $conta_id = $lancamento['conta_id'];
                            
                            $this->atualizarSaldoConta($db,$valor,$conta_id,'add');
                            $db->query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$lnct_exist['id']);
                            
                            $lancamento['valor'] = $valor;
                            $lancamento['dt_compensacao'] = $dt_compensacao;
                            $lancamento['compensado'] = 1;
                            Lancamento::HistoricoLancamentos($db,array($lancamento));
                        }
                    }
					
				}else{
	
					$lnct_rcr_dados = $this->lancamentosRcrExibir($db,array("lancamento_id"=>$lnct_exist['id']),1);
	
					$lnct_rcr = $lnct_rcr_dados['lancamento'];
					$lnct_rcr_ctr_plc = $lnct_rcr_dados['ctr_plc_lancamentos'];
					//refazer esse bloco adaptando a função lancamentosRcrExibir de programação para retornar os atributos da classe sem formatação
					
                    foreach($this->lancamento_dados as $chave => $val){
						if(array_key_exists($chave,$lnct_rcr))
							$this->lancamento_dados[$chave] = $lnct_rcr[$chave];
					}
					
                    $this->lancamento_dados['tipo'] = 'R';
					$this->lancamento_dados['qtd_parcelas'] = 1;
					$this->lancamento_dados['parcela_numero'] = 1;
					$this->lancamento_dados['lancamento_recorrente_id'] = $lnct_rcr['id'];
					$this->lancamento_dados['compensado'] = 1;
					$this->lancamento_dados['dt_competencia'] = $db->data_to_sql('01/'.$lnct_rcr['dt_competencia']);
					$this->lancamento_dados['dt_emissao'] = $db->data_to_sql($lnct_rcr['dt_emissao']);
					$this->lancamento_dados['dt_vencimento'] = $db->data_to_sql($lnct_rcr['dt_vencimento']);
					$this->lancamento_dados['dt_compensacao'] = $db->data_to_sql($array_dados['dt_vencimento_cnlc']);
                    $this->lancamento_dados['fit_id'] = $array_dados['fit_id'];

                    $valor = $db->valorToDouble($lnct_exist['valor']);

                    $this->lancamento_dados['valor'] = $valor; //$db->valorToDouble($lnct_rcr['valor']);

                    $observacao = $lnct_rcr['observacao'];
                    if($valor != $db->valorToDouble($lnct_rcr['valor']))
                        $observacao .= ' Juros/Multa: R$ '.$db->valorFormat($valor - $db->valorToDouble($lnct_rcr['valor']));
	
                    $this->lancamento_dados['observacao'] = $observacao;

					$array_dados['ct_resp_lancamentos'] = $lnct_rcr_ctr_plc;
					$array_dados['frequencia'] = $lnct_rcr['frequencia'];
					$array_dados['dt_venc_ref'] = $lnct_rcr['dt_venc_ref'];
					$array_dados['qtd_dias'] = $lnct_rcr['qtd_dias'];
					$array_dados['dia_mes'] = $lnct_rcr['dia_mes'];
					$array_dados['dt_vencimento'] = $lnct_rcr['dt_vencimento'];
					$array_dados['lancamento_id'] = $lnct_rcr['id'];
					//refazer esse bloco
	
					$this->lancamentoRcrCompensar($db,$array_dados);
	
				}
			
			}

			return true;

		}else{

			return false;

		}
		
	}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA
===========================================================================================
*/

	function atualizarSaldoConta($db,$valor,$conta_id,$opr,$lnct_id=''){
	
		//ALTERAR SALDO PARA INCLUSÃO DE LANÇAMENTO COMPENSADO

		if($opr=='add'){
	
			$array_conta = $db->fetch_assoc("select vl_credito, limite_credito from contas where id = ".$conta_id." for update");

			if(bccomp($array_conta['vl_credito'], $array_conta['limite_credito'], 2) == 0){
				$db->query("update contas set vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id);
			}else{
				$credito_usado = bcsub($array_conta['limite_credito'], $array_conta['vl_credito'], 2);

				if(bccomp($valor, $credito_usado, 2) <= 0){
					$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id); //repoem somente o cheque especial usado
				}else{
					$valor = bcsub($valor, $credito_usado, 2);
					$db->query("update contas set vl_saldo = vl_saldo + ".$valor.", vl_credito = vl_credito + ".$credito_usado." where id = ".$conta_id); //repoem o cheque especial usado e o saldo da conta
				}
			}

			$saldo_total = self::getSaldoTotal($db);
			
            self::setSessionSaldoTotal($db,$saldo_total);
	
			return true;
	
		}elseif($opr=='edit'){ //ALTERAR SALDO PARA EDIÇÃO DE LANÇAMENTO COMPENSADO
	
			$array_lnct = $db->fetch_assoc("select valor as valor_ini, conta_id as conta_id_ini from lancamentos where id = ".$lnct_id." for update");
	
			//verifica se o saldo da conta continuará positivo após a alteração do valor do lançamento
			//==================================================================================================
			$valor_ini = $array_lnct['valor_ini'];
			$array_conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$array_lnct['conta_id_ini']." for update");
			$saldo_total = $array_conta['saldo_total'];
			if( $array_lnct['conta_id_ini'] != $conta_id ){
				$novo_saldo = bcsub($saldo_total, $valor_ini, 2) ;
			}else{
				$novo_saldo = bcadd( bcsub($saldo_total, $valor_ini, 2), $valor, 2);
			}
			//==================================================================================================
			//fim da verificação
		
			if( bccomp($novo_saldo,0,2) >= 0){ //atualiza o valor do lançamento se o saldo não ficar negativo
		
				if($array_lnct['conta_id_ini'] == $conta_id){ //se a conta não mudou
		
					$limite_credito = $array_conta['limite_credito'];
	
					if( bccomp($valor_ini,$array_conta['vl_saldo'],2) == -1 || bccomp($limite_credito,0,2) == 0 ){ //desconta e repoem o valor apenas no saldo da conta

                        $vl_saldo = $array_conta['vl_saldo'];
                        $vl_saldo = bcsub($vl_saldo,$valor_ini,2);
                        $vl_saldo = bcadd($vl_saldo,$valor,2);

                        $db->query("update contas set vl_saldo = $vl_saldo where id = ".$conta_id);
					
                    }else{ //desconta o valor inicial da conta e o restante desconta do cheque especial
					
                        $valor_ini =  bcsub($valor_ini,$array_conta['vl_saldo'],2);
						
                        $db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor_ini where id = ".$conta_id);
						
                        $credito_usado = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = ".$conta_id);
						
                        if( bccomp($valor,$credito_usado['credito_usado'],2) <= 0 ){ //repõe apenas o cheque especial até o litime do novo valor
						
                            $db->query("update contas set vl_credito = vl_credito + $valor where id = ".$conta_id);
						
                        }else{ //repoem o cheque especial e o restante é creditado no saldo da conta
						
                            $valor =  bcsub($valor,$credito_usado['credito_usado'],2);
							$db->query("update contas set vl_credito = $limite_credito, vl_saldo = vl_saldo + $valor where id = ".$conta_id);
						}
					}
	
				}else{ //se a conta mudou
		
                    $limite_credito = $array_conta['limite_credito'];

					//desconta o valor todo da conta inicial
					if(  bccomp($valor_ini,$array_conta['vl_saldo'],2) <= 0 ||  bccomp($limite_credito,0,2) == 0 ){ //desconta apenas do saldo da conta
						$db->query("update contas set vl_saldo = vl_saldo - $valor_ini where id = ".$array_lnct['conta_id_ini']);
					}else{
						$valor_ini =  bcsub($valor_ini,$array_conta['vl_saldo'],2); //desconta do saldo da conta e o restante desconta do cheque especial
						$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor_ini where id = ".$array_lnct['conta_id_ini']);
					}
		
					//credita o valor todo na conta final
					
                    $limite_credito = $db->fetch_assoc("select limite_credito from contas where id = ".$conta_id);
					
                    $credito_usado = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = ".$conta_id);
					
                    if( bccomp($valor,$credito_usado['credito_usado'],2) <= 0 ){ //repoem o cheque especial
						$db->query("update contas set vl_credito = vl_credito + $valor where id = ".$conta_id);
					}else{
						$valor = bcsub($valor,$credito_usado['credito_usado'],2); //repoem o cheque especial e credita no saldo da conta o que sobrou
						$db->query("update contas set vl_saldo = vl_saldo + $valor, vl_credito = ".$limite_credito['limite_credito']." where id = ".$conta_id);
					}
								
				}
		
				return true;
		
			}else{

				return false;

			}
	
		}elseif($opr=='exc'){ //ALTERAR SALDO PARA EXCLUSÃO DE LANÇAMENTO COMPENSADO
	
			//verifica se o saldo ficará negativo após a exclusão
			$conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id." for update");
			$saldo_total = $conta['saldo_total'];
			$novo_saldo = bcsub($saldo_total,$valor,2);
			//fim da verificação do saldo
	
			if( bccomp($novo_saldo,0,2) >= 0 ){
			
				if( bccomp($valor,$conta['vl_saldo'],2) <= 0 || bccomp($conta['limite_credito'],0,2) == 0 ){
					$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$conta_id);
				}else{
					$valor -= $conta['vl_saldo'];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$conta_id);
				}			

				return true;
	
			}else{
				
				return false;
				
			}
	
		}
	
	}

//GERAR NOSSO NÚMERO DO BOLETO
//===========================================================================================

    static function GerarNossoNumeroBoleto($codBanco,$carteira,$sequencial,$anoEmissao,$convenio,$agencia){

        $host = $_SERVER['DOCUMENT_ROOT'].'/sistema/modulos/boleto/';

        require_once($host.'Boleto.class.php');
        
        switch ($codBanco){
            case '001'; require_once($host."Boleto.Bb.class.php"); $nossoNumero = BoletoBb::GerarNossoNumero($sequencial,$anoEmissao,$convenio); break;
            case '104'; require_once($host."Boleto.Cef.class.php"); $nossoNumero = BoletoCef::GerarNossoNumero($carteira,$sequencial,$anoEmissao); break;
            case '756'; require_once($host."Boleto.Sicoob.class.php"); $nossoNumero = BoletoSicoob::GerarNossoNumero($sequencial,$anoEmissao,$convenio,$agencia); break;
            case '033'; require_once($host."Boleto.Santander.class.php"); $nossoNumero = BoletoSantander::GerarNossoNumero($sequencial,$anoEmissao); break;
            case '021'; require_once($host."Boleto.Banestes.class.php"); $nossoNumero = BoletoBanestes::GerarNossoNumero($sequencial,$anoEmissao); break;
            case '237'; require_once($host."Boleto.Bradesco.class.php"); $nossoNumero = BoletoBradesco::GerarNossoNumero($carteira,$sequencial,$anoEmissao); break;
        }

        return $nossoNumero;

    }

//GERA CHAVE PARA EMISSÃO DO BOLETO
//===========================================================================================

	function boletosChaveGerar($db,$array_dados,$cliente_id){

        $cod_banco_habilitado = array('001','021','033','104','756','237');

        if( in_array($array_dados['cod_banco'], $cod_banco_habilitado) ){
        
            //convenio da conta para emissão do boleto
		    $contaFinanceira = $db->fetch_assoc("select convenio, carteira, agencia from contas where id = ".$array_dados['conta_id']);
	
		    //número sequencial do boleto
		    $query_sequencial = "
			    select id as boleto_id, sequencial
			    from boletos
			    where lancamento_id = ".$array_dados['lancamento_id'];
		    $sequencial = $db->fetch_assoc($query_sequencial);
		    if(empty($sequencial)){
			    $db->query("start transaction");
			    $sequencial = $db->fetch_assoc("select boleto_ano, sequencial from contas where id = ".$array_dados['conta_id']." for update");
			    //verifica se o sequencial e ano do boleto devem ser reiniciados
			    //suporta apenas até o ano de 2115 e então começará a repetir
			    if($sequencial['boleto_ano']!=date('y')){
				    $novo_boleto_ano = $sequencial['boleto_ano'] * 1 + 1;
				    if($novo_boleto_ano==100)
					    $novo_boleto_ano = '00';
				    $db->query("update contas set sequencial = 2, boleto_ano = '".$novo_boleto_ano."' where id = ".$array_dados['conta_id']);
				    $conta_sequencial = 1;
			    }else{
				    $db->query("update contas set sequencial = sequencial + 1 where id = ".$array_dados['conta_id']);
				    $conta_sequencial = $sequencial['sequencial'];
			    }
			    
                $boleto_sequencial = $conta_sequencial;
			    
                $anoEmissao = date('y');
                $nossoNumero = self::GerarNossoNumeroBoleto($array_dados['cod_banco'],$contaFinanceira['carteira'],$boleto_sequencial,$anoEmissao,$contaFinanceira['convenio'],$contaFinanceira['agencia']);

                $boleto = array("sequencial"=>$boleto_sequencial,"lancamento_id"=>$array_dados['lancamento_id'], 'nosso_numero'=>$nossoNumero);
			    $boleto_id = $db->query_insert("boletos",$boleto);
                
                $db->query("commit");

		    }else{
			    $boleto_id = $sequencial['boleto_id'];
			    $boleto_sequencial = $sequencial['sequencial'];
		    }
		
		    //chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
		    $chave = $cliente_id.'-'.$contaFinanceira['convenio'].'-'.$array_dados['lancamento_id'].'-'.$boleto_id.'-'.$boleto_sequencial;
		
		    return $chave;
		
        }else{

            return false;

        }
	}

/*
===========================================================================================
GERA CHAVE PARA EMISSÃO DO BOLETO PARA LANÇAMENTO RECORRENTE
===========================================================================================
*/

	function boletosChaveGerarRcr($db,$array_dados,$cliente_id){
	
		$cod_banco_habilitado = array('001','021','033','104','756');

        if( in_array($array_dados['cod_banco'], $cod_banco_habilitado) ){
        
            //converte o lançamento recorrente em programado
			$lancamento_id = parent::rcr_to_prog($db,$array_dados); 
		
		    //inclui registro do boleto e gera chave
			//convenio da conta para emissão do boleto
			$contaFinanceira = $db->fetch_assoc("select convenio, carteira, agencia from contas where id = ".$array_dados['conta_id']);
		
			//número sequencial do boleto
			$query_sequencial = "
				select id as boleto_id, sequencial
				from boletos
				where lancamento_id = ".$lancamento_id;

			$sequencial = $db->fetch_assoc($query_sequencial);
			
            if(empty($sequencial)){
				$db->query("start transaction");
				$sequencial = $db->fetch_assoc("select boleto_ano, sequencial from contas where id = ".$array_dados['conta_id']." for update");
				//verifica se o sequencial e ano do boleto devem ser reiniciados
				//suporta apenas até o ano de 2115 e então começará a repetir
				if($sequencial['boleto_ano']!=date('y')){
					$novo_boleto_ano = $sequencial['boleto_ano'] * 1 + 1;
					if($novo_boleto_ano==100)
						$novo_boleto_ano = '00';
					$db->query("update contas set sequencial = 2, boleto_ano = '".$novo_boleto_ano."' where id = ".$array_dados['conta_id']);
					$conta_sequencial = 1;
				}else{
					$db->query("update contas set sequencial = sequencial + 1 where id = ".$array_dados['conta_id']);
					$conta_sequencial = $sequencial['sequencial'];
				}
				
                $boleto_sequencial = $conta_sequencial;
				
                $anoEmissao = date('y');
                $nossoNumero = self::GerarNossoNumeroBoleto($array_dados['cod_banco'],$contaFinanceira['carteira'],$boleto_sequencial,$anoEmissao,$contaFinanceira['convenio'],$contaFinanceira['agencia']);

                $boleto = array("sequencial"=>$boleto_sequencial,"lancamento_id"=>$lancamento_id,'nosso_numero'=>$nossoNumero);
				
                $boleto_id = $db->query_insert("boletos",$boleto);

				$db->query("commit");
			    
            }else{
				$boleto_id = $sequencial['boleto_id'];
				$boleto_sequencial = $sequencial['sequencial'].date('y');
			}
	
			//chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
			$chave = $cliente_id.'-'.$contaFinanceira['convenio'].'-'.$lancamento_id.'-'.$boleto_id.'-'.$boleto_sequencial;
		    //fim inclui registro do boleto e gera chave
		
		    return $chave;
		
        }else{
            
            return false;

        }

	}

}

?>