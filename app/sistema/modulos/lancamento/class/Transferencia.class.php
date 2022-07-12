<?php

class Transferencia extends Lancamento{

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($db="",$array_dados=""){
		parent::__construct($db,$array_dados);
	}

/*
===========================================================================================
INCLUÍR
===========================================================================================
*/

	function lancamentoIncluir($db,$array_dados){
                
		if( $array_dados['compensado']!=1 ){
			
			$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
			
		}else{
			
			$atualizar_saldo = self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id_origem'],$this->lancamento_dados['conta_id_destino'],'add');
			if($atualizar_saldo)
				$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
			else
				return false;
		}

        $historico = $this->lancamento_dados;
        $historico['id'] = $lancamento_id;
        Lancamento::HistoricoLancamentos($db,array($historico));

        return $lancamento_id;
	}

/*
===========================================================================================
EDITAR
===========================================================================================
*/

	function lancamentoEditar($db,$array_dados){

		if( $array_dados['compensado']!=1 ){ //não é para compensar

			$lnct = $db->fetch_assoc('select valor, compensado, conta_id_origem, conta_id_destino from lancamentos where id = '.$array_dados['lancamento_id'].' for update');
			
			if($lnct['compensado']){ //estava compensado, abrir lançamento e desfazer inclusão ou retirada do valor da contas
			
				$atualizar_saldo = self::atualizarSaldoConta($db,$lnct['valor'],$lnct['conta_id_origem'],$lnct['conta_id_destino'],'exc',$array_dados['lancamento_id']);
				if($atualizar_saldo){
                    $this->lancamento_dados['fit_id'] = '';
					$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
				}
				$retorno = $atualizar_saldo;
			
			}else{ //não estava compensado, atualizar lançamento
				
				$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
				$retorno = true;
				
			}

		}else{ //é para compensar
			
			$lnct = $db->fetch_assoc('select compensado from lancamentos where id = '.$array_dados['lancamento_id'].' for update');
			
			if($lnct['compensado'])		
				$opr='edit'; //estava compensado, atualizar lançamento e atualizar valor incluido ou retirado das contas
			else
				$opr='add'; //não estava compensado, atualizar lançamento e incluir ou retirar valor das contas

			$atualizar_saldo = self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id_origem'],$this->lancamento_dados['conta_id_destino'],$opr,$array_dados['lancamento_id']);
			if($atualizar_saldo){
				$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
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
EXCLUÍR
===========================================================================================
*/

	function lancamentoExcluir($db,$array_dados){

		$lancamento_id = $array_dados['lancamento_id'];

        $compensado = $db->fetch_assoc('select tipo, compensado, conta_id_origem, conta_id_destino, dt_vencimento from lancamentos where id = '.$lancamento_id.' for update');

		if( $compensado['compensado']!=1 ){
			
			$db->query("delete from lancamentos where id = ".$lancamento_id);
			parent::anexoExcluir($db,$lancamento_id);
			$retorno = true;
		
		}else{

			$lnct = $db->fetch_assoc("select valor, conta_id_origem, conta_id_destino from lancamentos where id = ".$lancamento_id." for update");
			$atualizar_saldo = self::atualizarSaldoConta($db,$lnct['valor'],$lnct['conta_id_origem'],$lnct['conta_id_destino'],'exc');
			if($atualizar_saldo){
				$db->query("delete from lancamentos where id = ".$lancamento_id);
				parent::anexoExcluir($db,$lancamento_id);
			}
			$retorno = $atualizar_saldo;

		}
		
        if($retorno){
            $historico = $this->lancamento_dados;
            $historico['id'] = $lancamento_id;
            $historico['tipo'] = $compensado['tipo'];
            $historico['conta_id_origem'] = $compensado['conta_id_origem'];
            $historico['conta_id_destino'] = $compensado['conta_id_destino'];
            $historico['dt_vencimento'] = $compensado['dt_vencimento'];
            $historico['excluido'] = 1;
            Lancamento::HistoricoLancamentos($db,array($historico));
        }

        return $retorno;
	}

/*
===========================================================================================
COMPENSAR
===========================================================================================
*/

	function lancamentoCompensar($db,$array_dados){
		$atualizar_saldo = self::atualizarSaldoConta($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id_origem'],$this->lancamento_dados['conta_id_destino'],'add');
		if($atualizar_saldo){
			$db->query('update lancamentos set compensado = 1, dt_compensacao = "'.$this->lancamento_dados['dt_compensacao'].'" where id = '.$array_dados['lancamento_id']);

            $historico = $this->lancamento_dados;
            $historico['id'] = $array_dados['lancamento_id'];
            Lancamento::HistoricoLancamentos($db,array($historico));
		}
		return $atualizar_saldo;
	}

/*
===========================================================================================
CONCILIAR
===========================================================================================
*/

	function conciliarLancamento($db,$array_dados){

		$lncts_exist_id = str_replace('\"','"',$array_dados['lncts_exist_id']);
		$array_lnct_exist_id = json_decode($lncts_exist_id, true);

 		foreach($array_lnct_exist_id as $lnct_exist){

			$lancamento = $db->fetch_assoc("select * from lancamentos where id = ".$lnct_exist['id']);

            if($lancamento['compensado'] == 0){
    
                $valor = $lancamento['valor'];
                $conta_id_origem = $lancamento['conta_id_origem'];
                $conta_id_destino = $lancamento['conta_id_destino'];
                //if($lancamento['dt_compensacao']=='0000-00-00'){
				$dt_compensacao = $db->data_to_sql($array_dados['dt_vencimento_cnlc']);
                //}else{
				//$dt_compensacao = $lancamento['dt_compensacao'];
                //}

                $conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$conta_id_origem." for update");
                
                if($conta_origem['saldo_total']>=$valor){
                    $db->query("update lancamentos set dt_compensacao = '".$dt_compensacao."', compensado = 1, fit_id = ".$array_dados['fit_id']."  where id = ".$lnct_exist['id']);
                    self::atualizarSaldoConta($db,$valor,$conta_id_origem,$conta_id_destino,'add');

                    $lancamento['dt_compensacao'] = $dt_compensacao;
                    $lancamento['compensado'] = 1;
                    Lancamento::HistoricoLancamentos($db,array($lancamento));
                }
            }
		}
	}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA
===========================================================================================
*/

	function atualizarSaldoConta($db,$valor,$conta_id_origem,$conta_id_destino,$opr,$lnct_id=''){

		//ALTERAR SALDO PARA INCLUSÃO DE LANÇAMENTO COMPENSADO
		if($opr=='add'){

			$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$conta_id_origem);

			$conta_saldo = $conta_origem['vl_saldo'];
			
			if(bccomp($conta_origem['saldo_total'],$valor,2)>=0){

				//debita a conta de origem
				if(bccomp($conta_saldo,$valor,2)>=0){ //desconta o debito inteiro do saldo da conta
					$db->query("update contas set vl_saldo = vl_saldo - $valor where id = $conta_id_origem");
				}else{ //desconta uma parte no saldo da conta, se houver credito, e o restante do saldo do cheque especial
					$debito =  bcsub($valor,$conta_saldo,2); //abate do debito o restante de saldo que havia na conta
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $debito where id = $conta_id_origem"); //abate o restante do debito do saldo do cheque especial
				}
				//fim debita a conta de origem
				
				//credita conta de destino
				$conta_destino = $db->fetch_assoc("select vl_credito, limite_credito from contas where id = ".$conta_id_destino);

				if(bccomp($conta_destino['vl_credito'],$conta_destino['limite_credito'],2)==0){
					$db->query("update contas set vl_saldo = vl_saldo + $valor where id = $conta_id_destino");
				}else{
					
                    $credito_usado = bcsub($conta_destino['limite_credito'],$conta_destino['vl_credito'],2);
					
                    if(bccomp($valor,$credito_usado,2)<=0){
						$db->query("update contas set vl_credito = vl_credito + $valor where id = ".$conta_id_destino); //repoem somente o cheque especial usado
					}else{
						$db->query("update contas set vl_saldo = vl_saldo + $valor - $credito_usado, vl_credito = ".$conta_destino['limite_credito']." where id = $conta_id_destino"); //repoem o cheque especial usado e o saldo da conta
					}
				}
				//fim credita conta de destino
			
				return true;
			
			}else{
				return false;
			}
			
		}elseif($opr=='edit'){ //ALTERAR SALDO PARA EDIÇÃO DE LANÇAMENTO COMPENSADO

			$array_lnct = $db->fetch_assoc("select valor as valor_ini, conta_id_origem as conta_id_origem_ini, conta_id_destino as conta_id_destino_ini from lancamentos where id = ".$lnct_id." for update");

			$conta_id_origem_ini = $array_lnct['conta_id_origem_ini'];
			$conta_id_destino_ini = $array_lnct['conta_id_destino_ini'];
			$valor_ini = $array_lnct['valor_ini'];
			$valor = $this->lancamento_dados['valor'];
			
			//verifica se a atualização pode ser feita na conta de origem
			//==================================================================================================
			if(bccomp($conta_id_origem_ini,$conta_id_origem,2)==0){
				$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_origem_ini);
				$saldo_total_origem = $conta_origem['saldo_total'];
				$novo_saldo_origem = bcsub(bcadd($saldo_total_origem,$valor_ini,2),$valor,2);
			}else{
				$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_origem);
				$saldo_total_origem = $conta_origem['saldo_total'];
				$novo_saldo_origem = bcsub($saldo_total_origem,$valor,2);
			}
			//==================================================================================================
	
			//verifica se a atualização pode ser feita na conta de destino
			//==================================================================================================
			$conta_destino = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_destino_ini);
			$saldo_total_destino = $conta_destino['saldo_total'];
			if($conta_id_destino_ini == $conta_id_destino){
				$novo_saldo_destino = bcadd(bcsub($saldo_total_destino,$valor_ini,2),$valor,2);
			}else{
				if($conta_id_origem == $conta_id_destino_ini) //se a nova conta de origem é a conta de destino inicial, ou seja, as contas podem ter sido invertidas
                    $novo_saldo_destino = bcsub(bcsub($saldo_total_destino,$valor_ini,2),$valor,2);
                else //se a nova conta de origem não for a conta de destino inicial
                    $novo_saldo_destino = bcsub($saldo_total_destino,$valor_ini,2);
			}
			//==================================================================================================
	
			if( bccomp($novo_saldo_origem,0,2) >= 0 && bccomp($novo_saldo_destino,0,2) >= 0){ //verifica se o saldo das contas de origem e destino ficarão no minimo iguais a zero
	
				//operações realizadas na conta de origem se ela permanecer a mesma
				if($conta_id_origem_ini == $conta_id_origem){
	
					$credito_usado_origem = $conta_origem['credito_usado'];
					$limite_credito_origem = $conta_origem['limite_credito'];
		
					if( bccomp($valor_ini,$credito_usado_origem,2) <= 0 ){
						$db->query("update contas set vl_credito = vl_credito + $valor_ini - $valor where id = $conta_id_origem");
					}else{
						$valor_ini = bcsub($valor_ini,$credito_usado_origem,2);
						$db->query("update contas set vl_saldo = vl_saldo + $valor_ini, vl_credito = $limite_credito_origem where id = $conta_id_origem");
						$saldo_conta_origem = $db->fetch_assoc("select vl_saldo from contas where id = $conta_id_origem");
						if( bccomp($valor,$saldo_conta_origem['vl_saldo'],2) <= 0 ){
							$db->query("update contas set vl_saldo = vl_saldo - $valor where id = $conta_id_origem");
						}else{
							$valor = bcsub($valor,$saldo_conta_origem['vl_saldo'],2);
							$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor where id = $conta_id_origem");
						}
					}
	
				}else{
	
					//devolve os valores para a conta de origem inicial
					$conta_origem_ini = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado, limite_credito from contas where id = $conta_id_origem_ini");
					$credito_usado_origem = $conta_origem_ini['credito_usado'];
					$limite_credito_origem = $conta_origem_ini['limite_credito'];
				
					if( bccomp($valor_ini,$credito_usado_origem,2) <= 0 ){
						$db->query("update contas set vl_credito = vl_credito + $valor_ini where id = $conta_id_origem_ini");
					}else{
						$valor_ini = bcsub($valor_ini,$credito_usado_origem,2);
						$db->query("update contas set vl_credito = $limite_credito_origem, vl_saldo = vl_saldo + $valor_ini where id = $conta_id_origem_ini");
					}

                    $valor_ini = $array_lnct['valor_ini'];

					//debita os valores da nova conta de origem
                    $saldo_conta_origem = $db->fetch_assoc("select vl_saldo from contas where id = $conta_id_origem");

                    if($conta_id_origem == $conta_id_destino_ini){ //se a nova conta de origem for a conta de destino inicial
                        
                        $saldo_conta_origem['vl_saldo'] = bcsub($saldo_conta_origem['vl_saldo'],$valor_ini,2);

                        if( bccomp($valor,$saldo_conta_origem['vl_saldo'],2) <= 0 ){
                            $db->query("update contas set vl_saldo = vl_saldo - $valor - $valor_ini where id = $conta_id_origem");
                        }else{
                            $valor = bcsub($valor,$saldo_conta_origem['vl_saldo'],2);
                            $db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor where id = $conta_id_origem");
                        }

                    }else{ //se a nova conta de origem for diferente da conta de destino inicial

                        if( bccomp($valor,$saldo_conta_origem['vl_saldo'],2) <= 0 ){
                            $db->query("update contas set vl_saldo = vl_saldo - $valor where id = $conta_id_origem");
                        }else{
                            $valor = bcsub($valor,$saldo_conta_origem['vl_saldo'],2);
                            $db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor where id = $conta_id_origem");
                        }
                    }
				}
				
				$valor_ini = $array_lnct['valor_ini'];
				$valor = $this->lancamento_dados['valor'];
	
				//operações realizadas na conta de destino se ela permanecer a mesma
				if($conta_id_destino_ini == $conta_id_destino){
					
					if( (bccomp($valor_ini,$conta_destino['vl_saldo'],2) <= 0) || (bccomp($conta_destino['limite_credito'],0,2) == 0) ){
						$db->query("update contas set vl_saldo = vl_saldo - $valor_ini + $valor where id = $conta_id_destino");
					}else{
						$valor_ini = bcsub($valor_ini,$conta_destino['vl_saldo'],2);
						$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor_ini where id = $conta_id_destino");
						$credito_usado_destino = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = $conta_id_destino");
						if( bccomp($valor,$credito_usado_destino['credito_usado'],2) <= 0 ){
							$db->query("update contas set vl_credito = vl_credito + $valor where id = $conta_id_destino");
						}else{
							$valor = bcsub($valor,$credito_usado_destino['credito_usado'],2);
							$db->query("update contas set vl_credito = ".$conta_destino['limite_credito'].", vl_saldo = vl_saldo + $valor where id = $conta_id_destino");
						}
					}
	
				}else{
	
					//debita os valores da conta de destino inicial
                    if( $conta_id_origem != $conta_id_destino_ini ){

                        $saldo_conta_destino_inicial = $db->fetch_assoc("select vl_saldo from contas where id = ".$conta_id_destino_ini);

                        if( bccomp($valor_ini,$saldo_conta_destino_inicial['vl_saldo'],2) <= 0 ){
                            $db->query("update contas set vl_saldo = vl_saldo - $valor_ini where id = $conta_id_destino_ini");
                        }else{
                            $valor_ini = bcsub($valor_ini,$saldo_conta_destino_inicial['vl_saldo'],2);
                            $db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor_ini where id = $conta_id_destino_ini");
                        }
                    }
	
					//credita os valores na nova conta de destino
					$conta_destino_final = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado, limite_credito from contas where id = ".$conta_id_destino);
	
					if( bccomp($valor,$conta_destino_final['credito_usado'],2) <= 0 ){
						$db->query("update contas set vl_credito = vl_credito + $valor where id = $conta_id_destino");
					}else{
						$valor = bcsub($valor,$conta_destino_final['credito_usado'],2);
						$db->query("update contas set vl_credito = ".$conta_destino_final['limite_credito'].", vl_saldo = vl_saldo + $valor where id = $conta_id_destino");
					}
				}
				
				return true;
	
			}else{
				
				return false;
			}
	
		}elseif($opr=='exc'){

			//verifica se o saldo da conta de destino ficará negativo
			$conta_destino = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_destino." for update");
			$novo_saldo_destino = bcsub($conta_destino['saldo_total'],$valor,2);
			//fim verifica se o saldo da conta de destino ficará negativo
		
			if( bccomp($novo_saldo_destino,0,2) >= 0 ){
			
				//debita valor da conta de destino
				if( (bccomp($valor,$conta_destino['vl_saldo'],2) <= 0) || (bccomp($conta_destino['limite_credito'],0,2) == 0) ){
					$db->query("update contas set vl_saldo = vl_saldo - $valor where id = $conta_id_destino");
				}else{
					$debito = bcsub($valor,$conta_destino['vl_saldo'],2);
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $debito where id = $conta_id_destino");
				}			
				//fim debita valor da conta de destino
				
				//credita valor na conta de origem
				$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_origem);
				if( bccomp($valor,$conta_origem['credito_usado'],2) <= 0 ){
					$db->query("update contas set vl_credito = vl_credito + $valor where id = $conta_id_origem");
				}else{
					$valor = bcsub($valor,$conta_origem['credito_usado'],2);
					$db->query("update contas set vl_saldo = vl_saldo + $valor, vl_credito = ".$conta_origem['limite_credito']." where id = $conta_id_origem");
				}			
				//fim credita valor na conta de origem
		
		
				return true;
		
			}else{
				return false;
			}

		}
	
	}

}

?>