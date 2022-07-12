<?php
/*
LEMBRETES
- alterar nome da função listar lançamentos em funcoes.php
- acrescentar o parametro tp_lnct nas funções que foram alterardas
*/

class Programacao extends Lancamento{

	var $lancamento_dados = array(
		"tipo" => "",
		"descricao" => "",
		"lancamento_pai_id" => "",
		"lancamento_recorrente_id" => "",
		"parcela_numero" => "",
		"qtd_parcelas" => "",
		"favorecido_id" => "",
		"forma_pgto_id" => "",
		"conta_id" => "",
		"conta_id_origem" => "",
		"conta_id_destino" => "",
		"documento_id" => "",
		"valor" => "",
		"frequencia" => "",
		"auto_lancamento" => "",
		"observacao" => "",
		"dt_competencia" => "",
		"dt_emissao" => "",
		"dt_vencimento" => "",
		"sab_dom" => "",
		"dt_venc_ref" => "",
		"dt_compensacao" => "",
		"compensado" => "",
	);

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($db="",$array_dados=""){
		if($array_dados!=""){
			foreach($this->lancamento_dados as $chave => $valor){
				if(isset($array_dados[$chave])){
					$this->lancamento_dados[$chave] = $array_dados[$chave];
				}
			}
			$this->lancamento_dados['valor'] = $db->valorToDouble($array_dados['valor']);
			$this->lancamento_dados['dt_competencia'] = $db->data_to_sql('01/'.$array_dados['dt_competencia']); //$db->data_to_sql('01/01/2015');
			$this->lancamento_dados['dt_emissao'] = $db->data_to_sql($array_dados['dt_emissao']);
			$this->lancamento_dados['dt_vencimento'] = $db->data_to_sql($array_dados['dt_vencimento']);
			if(isset($array_dados['dt_compensacao']))
				$this->lancamento_dados['dt_compensacao'] = $db->data_to_sql($array_dados['dt_compensacao']);
		}
	}

/*
===========================================================================================
INCLUÍR RECEBIMENTO COMPENSADO
===========================================================================================
*/

	function recebimentoCompensadoIncluir($db,$array_dados){
		$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
		$array_dados['lancamento_id'] = $lancamento_id;
		self::recebimentosCompensar($db,$array_dados);
	}

/*
===========================================================================================
INCLUÍR RECEBIMENTO PROGRAMADO
===========================================================================================
*/

	function recebimentoProgIncluir($db,$array_dados){
		if($array_dados['qtd_parcelas']==1){
			$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
			if($array_dados['ct_resp_lancamentos']!=""){
				self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"R",1);
			}
			return $lancamento_id;
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

			//insere o lançamento pai
			$lnct_dscr = $this->lancamento_dados['descricao'];
			$this->lancamento_dados['descricao'] = $lnct_dscr.' - 1 de '.$total_parcelas;
			$this->lancamento_dados['parcela_numero'] = 1;
			$lancamento_pai_id = $db->query_insert('lancamentos',$this->lancamento_dados);
			$db->query("update lancamentos set lancamento_pai_id = ".$lancamento_pai_id." where id = ".$lancamento_pai_id);
			if($array_dados['ct_resp_lancamentos']!=""){
				self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_pai_id,"R",$total_parcelas);
			}
	
			$this->lancamento_dados['lancamento_pai_id'] = $lancamento_pai_id;
	
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
						$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes)-$mes_dif,'01',date($ano)));
					}else{
						$this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),date($dia),date($ano)));
						$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes)-$mes_dif,'01',date($ano)));
					}
				}else{
					$this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes),date($dia)+($frequencia_dia*$fator_dia),date($ano)));
					$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)-$mes_dif,'01',date($ano)));
				}
				$fator_mes += 1;
				$fator_dia += 1;
				//============= Fim atualização da data de vencimento e de competência da próxima parcela ============================

				$this->lancamento_dados['parcela_numero'] = $contador;
				$parcela_dscr = ' - '.$contador.' de '.$total_parcelas;
				$this->lancamento_dados['descricao'] = $lnct_dscr.$parcela_dscr;
				$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
				if($array_dados['ct_resp_lancamentos']!=""){
					self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"R",$total_parcelas);
				}
				$contador += 1;
			}
			return 1;
		}
	}

/*
===========================================================================================
EDITAR RECEBIMENTO
===========================================================================================
*/

	function recebimentoEditar($db,$array_dados){
		if($array_dados['rcr']){ //usado para editar lançamentos recorrentes na conciliação; substituir depois por recebimentosRcrEditar
			 $lnct_prog_id = $this->rcr_to_prog($db,$array_dados);
			 return $lnct_prog_id;
		}else{
			
			$compensado = $db->fetch_assoc('select compensado from lancamentos where id = '.$array_dados['lancamento_id']);
			
			if($compensado){

				$atualizar_saldo = parent::atualizarSaldoContaRcbt($db,$this->lancamento_dados['valor'],$array_dados['conta_id'],'exc',$array_dados['lancamento_id']);
				if($atualizar_saldo){
					$this->lancamento_dados['compensado'] = 0;
					$this->lancamento_dados['dt_compensacao'] = '0000-00-00';
					$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
					if( $array_dados['ct_resp_lancamentos']!='' ){
						self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R");
						$db->query('update ctr_plc_lancamentos set situacao = 0 where lancamento_id = '.$array_dados['lancamento_id']);
					}
					return true;
				}else{
					return false;
				}

			}else{
				unset($this->lancamento_dados['dt_venc_ref']);
				unset($this->lancamento_dados['lancamento_pai_id']);
				unset($this->lancamento_dados['parcela_numero']);
				unset($this->lancamento_dados['qtd_parcelas']);
				unset($this->lancamento_dados['frequencia']);
				$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
				self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R",1);
			}
		}
	}

/*
===========================================================================================
EXCLUIR RECEBIMENTO
===========================================================================================
*/	

	function recebimentosExcluir($db,$lancamento_id){
		$lancamento = $db->fetch_assoc("select lancamento_pai_id from lancamentos where id = ".$lancamento_id);
		$db->query("delete from lancamentos where id = ".$lancamento_id);
		$db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id);
		$db->query("delete from boletos where lancamento_id = ".$lancamento_id);
		//====== refaz a contagem das parcelas===============================================================================================
		if($lancamento['lancamento_pai_id'] != 0){
			$qtd_parcelas = $db->fetch_assoc("select count(id) qtd_parcelas from lancamentos where lancamento_pai_id = ".$lancamento['lancamento_pai_id']);
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
		$retorno = array("situacao" => 1,"notificacao"=>"Lançamento excluído com sucesso.");
		return $retorno;
	}

/*
===========================================================================================
EDITAR RECEBIMENTO RECORRENTE
===========================================================================================
*/

function recebimentosRcrEditar($db,$array_dados){
	$this->rcr_to_prog($db,$array_dados);
}

/*
===========================================================================================
INCLUÍR PAGAMENTO COMPENSADO
===========================================================================================
*/

	function pagamentoCompensadoIncluir($db,$array_dados){
		$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
		$array_dados['lancamento_id'] = $lancamento_id;
		self::pagamentosCompensar($db,$array_dados);
	}

/*
===========================================================================================
INCLUÍR PAGAMENTO
===========================================================================================
*/

	function pagamentosProgIncluir($db,$array_dados){
		if($array_dados['qtd_parcelas']==1){
			$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
			if($array_dados['ct_resp_lancamentos']!=""){
				self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"P",1);
			}
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
			
			//insere o lançamento pai
			$lnct_dscr = $this->lancamento_dados['descricao'];
			$this->lancamento_dados['descricao'] = $lnct_dscr.' - 1 de '.$total_parcelas;
			$this->lancamento_dados['parcela_numero'] = 1;
			$lancamento_pai_id = $db->query_insert('lancamentos',$this->lancamento_dados);
			$db->query("update lancamentos set lancamento_pai_id = ".$lancamento_pai_id." where id = ".$lancamento_pai_id);
			if($array_dados['ct_resp_lancamentos']!=""){
				self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_pai_id,"P",$total_parcelas);
			}

			$this->lancamento_dados['lancamento_pai_id'] = $lancamento_pai_id;
	
			$contador = 2;
			$fator_mes =  1;
			$fator_dia = 1;
			
			while($contador <= $total_parcelas){
				//============= Atualiza a data da próxima parcela ===================================
				if($frequencia_mes!=0){
					$mes_prox_venc = mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),'1',date($ano));
					$qtd_dias_mes = date('t',$mes_prox_venc);
					if( $qtd_dias_mes < $dia ){
						$this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),date($qtd_dias_mes),date($ano)));
						$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes)-$mes_dif,'01',date($ano)));
					}else{
						$this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes),date($dia),date($ano)));
						$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)+($frequencia_mes*$fator_mes)-$mes_dif,'01',date($ano)));
					}
				}else{
					$this->lancamento_dados['dt_vencimento'] = date('Y-m-d',mktime(0,0,0,date($mes),date($dia)+($frequencia_dia*$fator_dia),date($ano)));
					$this->lancamento_dados['dt_competencia'] = date('Y-m-01',mktime(0,0,0,date($mes)-$mes_dif,'01',date($ano)));
				}
				$fator_mes += 1;
				$fator_dia += 1;
				//============= Fim atualização da data da próxima parcela============================

				$this->lancamento_dados['parcela_numero'] = $contador;
				$parcela_dscr = ' - '.$contador.' de '.$total_parcelas;
				$this->lancamento_dados['descricao'] = $lnct_dscr.$parcela_dscr;
				$lancamento_id = $db->query_insert('lancamentos',$this->lancamento_dados);
				if($array_dados['ct_resp_lancamentos']!=""){
					self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,"P",$total_parcelas);
				}
				$contador += 1;
			}
		}
		return 1;
	}

/*
===========================================================================================
EDITAR PAGAMENTO
===========================================================================================
*/

	function pagamentosEditar($db,$array_dados){
		if($array_dados['rcr']){ //usado para editar lançamentos recorrentes na conciliação; substituir depois por pagamentosRcrEditar
			 $lnct_prog_id = $this->rcr_to_prog($db,$array_dados);
			 return $lnct_prog_id;
		}else{		
			unset($this->lancamento_dados['dt_venc_ref']);
			unset($this->lancamento_dados['lancamento_pai_id']);
			unset($this->lancamento_dados['parcela_numero']);
			unset($this->lancamento_dados['qtd_parcelas']);
			unset($this->lancamento_dados['frequencia']);
			$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
			self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"P",1);
		}
	}

/*
===========================================================================================
EXCLUIR PAGAMENTO
===========================================================================================
*/	

	function pagamentosExcluir($db,$lancamento_id){
		$lancamento = $db->fetch_assoc("select lancamento_pai_id from lancamentos where id = ".$lancamento_id);
		$db->query("delete from lancamentos where id = ".$lancamento_id);
		$db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id);
		//====== refaz a contagem das parcelas===============================================================================================
		if($lancamento['lancamento_pai_id'] != 0){
			$qtd_parcelas = $db->fetch_assoc("select count(id) qtd_parcelas from lancamentos where lancamento_pai_id = ".$lancamento['lancamento_pai_id']);
			if($qtd_parcelas['qtd_parcelas'] > 0){
				$array_parcelas = $db->fetch_all_array("select id from lancamentos where lancamento_pai_id = ".$lancamento['lancamento_pai_id']." order by id");
				$contador = 1;
				foreach($array_parcelas as $parcela){
					$parcela_id = $parcela[id];
					$where = "id = ".$parcela_id;
					$dados['parcela_numero'] = $contador;
					$dados['qtd_parcelas'] = $qtd_parcelas['qtd_parcelas'];
					$db->query_update('lancamentos',$dados,$where);
					$contador += 1;
				}
			}
		}
		//======= fim refaz a contagem das parcelas =========================================================================================
		$retorno = array("situacao" => 1,"notificacao"=>"Pagamento excluído com sucesso.");
		return $retorno;
	}

/*
===========================================================================================
EDITAR PAGAMENTO RECORRENTE
===========================================================================================
*/

function pagamentosRcrEditar($db,$array_dados){
	$this->rcr_to_prog($db,$array_dados);
}

/*
===========================================================================================
INCLUÍR TRANSFERÊNCIA COMPENSADA
===========================================================================================
*/

	function transferenciaCompensadaIncluir($db,$array_dados){
		$db->query_insert('lancamentos',$this->lancamento_dados);
		self::transferenciasCompensar($db);
	}
	
/*
===========================================================================================
INCLUÍR TRANSFERÊNCIA
===========================================================================================
*/

	function transferenciasProgIncluir($db,$array_dados){
		$db->query_insert('lancamentos',$this->lancamento_dados);
	}

/*
===========================================================================================
EDITAR TRANSFERÊNCIA
===========================================================================================
*/
 
	function transferenciasEditar($db,$array_dados){
		$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
	}

/*
===========================================================================================
EXCLUIR TRANSFERÊNCIA
===========================================================================================
*/	

	function transferenciasExcluir($db,$lancamento_id){
		$db->query("delete from lancamentos where id = ".$lancamento_id);
		$retorno = array("situacao" => 1,"notificacao"=>"Transferência excluída com sucesso.");
		return $retorno;
	}

/*
===========================================================================================
EXIBIR
===========================================================================================
*/

	function lancamentosExibir($db,$array_dados){
		
		if($array_dados['tp_lnct']=='R' || $array_dados['tp_lnct']=='P'){
		
			$query = "
				select l.id, l.tipo, l.descricao, f.nome favorecido, f.id favorecido_id, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, c.id conta_id,
				date_format(l.dt_competencia, '%m/%Y') dt_competencia, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, l.sab_dom, 
				date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, auto_lancamento,
				l.documento_id, l.forma_pgto_id, l.observacao
				from lancamentos l
				left join contas c on l.conta_id = c.id
				left join bancos b on c.banco_id = b.id
				left join favorecidos f on l.favorecido_id = f.id
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
				$porcentagem = number_format($lancamento['porcentagem'],2,',','.');
				$ctr_plc_lancamentos .= '{"ctr_plc_lancamento_id":"'.$lancamento['ctr_plc_lancamento_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'"},';
			}
			
			$ctr_plc_lancamentos = substr($ctr_plc_lancamentos,0,-1); //retira a ultima virgula
	
			$jsonText = '['.$ctr_plc_lancamentos.']';
			//fim da montagem da lista de lannçamentos do centro de responsabilidade
		
			$lancamentos_visualizar = $db->fetch_array($db->query($query));
			$lancamentos_visualizar['valor'] = $db->valorFormat($lancamentos_visualizar['valor']);
			$retorno = array("lancamento"=>$lancamentos_visualizar,"ctr_plc_lancamentos"=>$jsonText);
			return $retorno;
		
		}else{
		
			$query = "
				select l.id, l.tipo, l.descricao, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta_origem, IFNULL(concat(c2.descricao,' - ',b2.nome),c2.descricao) conta_destino,
				l.conta_id_origem, l.conta_id_destino, date_format(l.dt_competencia, '%m/%Y') dt_competencia, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, 
				date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, l.observacao, l.auto_lancamento
				from lancamentos l
				left join contas c on l.conta_id_origem = c.id
				left join bancos b on c.banco_id = b.id
				left join contas c2 on l.conta_id_destino = c2.id
				left join bancos b2 on c2.banco_id = b2.id
				where l.id = ".$array_dados['lancamento_id']."
			";
			$lancamentos_visualizar = $db->fetch_array($db->query($query));
			$lancamentos_visualizar['valor'] = number_format($lancamentos_visualizar['valor'],2,',','.');
			$retorno = array("lancamento"=>$lancamentos_visualizar,"ctr_plc_lancamentos"=>"");
			return $retorno;
		
		}
		
	}

/*
===========================================================================================
EXIBIR LANÇAMENTO RECORRENTE
===========================================================================================
*/

	function lancamentosRcrExibir($db,$array_dados,$ctr_plc_opr_inc=0){
		
		//monta lançamento
		$query = "
			select lr.id, lr.tipo, lr.favorecido_id, f.nome favorecido, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, lr.conta_id, date_format(dt_competencia, '%m/%Y') dt_competencia, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, dt_vencimento dt_venc_ref,  lr.descricao, valor, frequencia, auto_lancamento, qtd_dias, dia_mes, lr.documento_id, lr.forma_pgto_id, lr.observacao, lr.dt_comp_mes_dif
			from lancamentos_recorrentes lr
			left join contas c on lr.conta_id = c.id
			left join bancos b on c.banco_id = b.id
			left join favorecidos f on lr.favorecido_id = f.id
			where lr.id = ".$array_dados['lancamento_id']."
		";

		$lancamentos_exibir = $db->fetch_assoc($query);

		//formata valor
		$lancamentos_exibir['valor'] = $db->valorFormat($lancamentos_exibir['valor']);

		//calcula data de competência
		$dt_vencimento = $lancamentos_exibir['dt_venc_ref'];
		$dt_vencimento = explode('-',$dt_vencimento);
		$dia = $dt_vencimento['2'];
		$mes = $dt_vencimento['1'];
		$ano = $dt_vencimento['0'];
		$mes_dif = $lancamentos_exibir['dt_comp_mes_dif'];
		$dt_competencia_ts = mktime(0,0,0,date($mes)-$mes_dif,'01',date($ano));
		$dt_competencia = date('m/Y',$dt_competencia_ts);
		$lancamentos_exibir['dt_competencia'] = $dt_competencia;
		
		//calcula data de emissão com base na competência
		$qtd_dias_mes = date('t',$dt_competencia_ts);
		if( $qtd_dias_mes < $dia ){
			$lancamentos_exibir['dt_emissao'] = $qtd_dias_mes.'/'.$dt_competencia;
		}else{
			$lancamentos_exibir['dt_emissao'] = $dia.'/'.$dt_competencia;
		}
		
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
			$porcentagem = number_format($lancamento['porcentagem'],2,',','.');
			if($ctr_plc_opr_inc){
				$ctr_plc_lancamentos .= '{"ctr_plc_lancamento_id":"'.$lancamento['ctr_plc_lancamento_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'","operacao":"1"},';
			}else{
				$ctr_plc_lancamentos .= '{"ctr_plc_lancamento_id":"'.$lancamento['ctr_plc_lancamento_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'"},';
			}
		}
		
		$ctr_plc_lancamentos = substr($ctr_plc_lancamentos,0,-1); //retira a ultima virgula

		$jsonText = '['.$ctr_plc_lancamentos.']';
		//fim da montagem da lista de lannçamentos do centro de responsabilidade
		
		$retorno = array("lancamento"=>$lancamentos_exibir,"ctr_plc_lancamentos"=>$jsonText);
		return $retorno;
	}

/*
===========================================================================================
LISTAR LANÇAMENTOS
===========================================================================================
*/

	function lancamentosRcbtPgtoListar($db,$params,$tp_lnct){
	
		//filtro
		$array_filtro = array();
		$array_filtro_rcr = array();
		$exc_rcr = false;
	
		$filtro = $params['filtro'];
		$filtro = str_replace('\"','"',$filtro);
		$filtro = str_replace("\'","'",$filtro);
		$filtro = json_decode($filtro, true);
		
		//data
		$flt_dt_ativo = $filtro["dt_ativo"];
		$flt_tp_venc = explode(',',$filtro["tp_venc"]);
		$a_vencer = in_array("'av'",$flt_tp_venc);
		$vencido = in_array("'v'",$flt_tp_venc);
	
		if($flt_dt_ativo=="mes"){
			$dt_mes = $filtro["dt_mes"];
			if($dt_mes==""){
				$dt_ini = date('Y').'-'.date('m').'-01';
				$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
				$dt_fim = date('Y-m-d',$dt_fim);
			}else{
				$dt_mes = explode('/',$dt_mes);
				$dt_ini = date('Y').'-'.$dt_mes[0].'-01';
				$dt_fim = mktime(0,0,0,$dt_mes[0]+1,'00',date('Y'));
				$dt_fim = date('Y-m-d',$dt_fim);
			}
		}else{
			$dt_ini = $filtro['dt_ini'];
			$dt_fim = $filtro['dt_fim'];
			if($dt_ini=="" || $dt_fim==""){
				$dt_ini = date('Y').'-'.date('m').'-01';
				$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
				$dt_fim = date('Y-m-d',$dt_fim);
			}else{
				$dt_ini = $db->data_to_sql($dt_ini);
				$dt_fim = $db->data_to_sql($dt_fim);
			}
		}
	
		if( $a_vencer && !$vencido ){
			if( strtotime($dt_ini) < strtotime('now') );
				$dt_ini = date('Y-m-d');
		}elseif( $vencido && !$a_vencer ){
			if( strtotime($dt_fim) > strtotime('-1 day') )
				$dt_fim = date('Y-m-d',strtotime('-1 day'));
		}
	
		$array_filtro[] =	'dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"';
		$array_filtro_rcr[] = 'dt_vencimento <= "'.$dt_fim.'"';
	
		//descrição
		//$descricao = $filtro['descricao'];
		//if(!$descricao==''){
			//$descricao = trim($descricao,' ');
			//$descricao = str_replace(' ','%',$descricao);
			//$array_filtro[] = 'descricao like "%'.$descricao.'%"';
		//}
	
		//contas financeiras
		if($filtro["conta_id"]!=''){
			$array_filtro[] = 'conta_id in ('.$filtro["conta_id"].')';
			$array_filtro_rcr[] = 'conta_id in ('.$filtro["conta_id"].')';
		}
		
		//favorecido
		if($filtro["favorecido_id"]){
			$array_filtro[] = 'favorecido_id = '.$filtro["favorecido_id"];
			$array_filtro_rcr[] = 'favorecido_id = '.$filtro["favorecido_id"];
		}
	
		//valor
		$valor = $filtro["valor"];
		if($valor!='' && $valor!='0,00'){
			$valor = $db->valorToDouble($valor);
			$array_filtro[] = 'valor = '.$valor;
			$array_filtro_rcr[] = 'valor = '.$valor;
		}
	
		//documento
		if($filtro["documento_id"]!=""){
			$array_filtro[] = 'documento_id = '.$filtro["documento_id"];
			$array_filtro_rcr[] = 'documento_id = '.$filtro["documento_id"];
		}
	
		//forma de pagamento
		if($filtro["forma_pgto_id"]!=""){
			$array_filtro[] = 'forma_pgto_id = '.$filtro["forma_pgto_id"];
			$array_filtro_rcr[] = 'forma_pgto_id = '.$filtro["forma_pgto_id"];
		}
	
		//quantidade de parcelas
		$bool_parcelado = filter_var($filtro["parcelado"], FILTER_VALIDATE_BOOLEAN);
		if($bool_parcelado){
			$array_filtro[] = 'qtd_parcelas > 1';
			$exc_rcr = true;
		}
	
		//plano de contas
		$plano_contas_id = $filtro["plano_contas_id"];
		$bool_plc = false;
		if($plano_contas_id>0){
			$bool_plc = true;
		}
	
		//centro de responsabilidade
		$centro_resp_id = $filtro["centro_resp_id"];
		$bool_ctr = false;
		if($centro_resp_id>0){
			$bool_ctr = true;
		}
	
		//monta where dos lançamentos programados
		$filtro_query = "";
		foreach($array_filtro as $filtro){
			$filtro_query .= ' and '.$filtro;
		}
	
		//monta where dos lançamentos recorrentes
		$filtro_query_rcr = "";
		foreach($array_filtro_rcr as $filtro){
			$filtro_query_rcr .= ' and '.$filtro;
		}
		//fim do filtro
	
		//monta a lista de lançamentos recorrentes
		if(!$exc_rcr){
			$db->query("
				CREATE TEMPORARY TABLE lancamentos_recorrentes_temp (
					id int(11),
					dt_vencimento date NOT NULL,
					dt_prox_venc date NOT NULL,
					descricao varchar(255),
					favorecido_id int(1),
					conta_id int(1),
					valor decimal(10,2) NOT NULL,
					frequencia int(3),
					dia_mes int(1),
					qtd_dias smallint(6),
					recorrente int(1)
				) ENGINE=MEMORY
			");
		
			//para lançamento recorrentes é usada apenas dt_fim como referencia, pois quando a busca é específica por mês, o lançamento não é retornado
			$query_lnct_rcr = '
				select id 
				from lancamentos_recorrentes 
				where tipo = "'.$tp_lnct.'"'.$filtro_query_rcr;
			
			$array_lancamentos_rcr = $db->fetch_all_array($query_lnct_rcr);
			
			foreach($array_lancamentos_rcr as $lancamento){
				
				$lancamento_rcr = $db->fetch_assoc("select id, dt_vencimento, favorecido_id, descricao, conta_id, valor, frequencia, qtd_dias, dia_mes, 1 recorrente, dt_prox_venc from lancamentos_recorrentes where id = ".$lancamento['id']);
				
				$dt_vencimento = date($lancamento_rcr['dt_vencimento']);
		
				while($dt_vencimento <= $dt_fim){
				
					//só inclui para vencimento maior que a data inicial de referencia para que a busca específica por mês não retorne os lançamentos acumulados dos meses anteriores
					if($dt_vencimento >= $dt_ini){
						$db->query_insert('lancamentos_recorrentes_temp',$lancamento_rcr);
					}
					
					if($lancamento_rcr['frequencia']==0){
			
						$dt_vencimento_atual = explode('-',$dt_vencimento);
						$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+$lancamento_rcr['qtd_dias'],$dt_vencimento_atual[0]);
						$dt_vencimento = date('Y-m-d',$dt_vencimento);
						
					}elseif($lancamento_rcr['frequencia']>=30){
					
						$frequencia = $lancamento_rcr['frequencia']/30;
						$dia_vencimento = $lancamento_rcr['dia_mes'];
						$dt_vencimento_atual = explode('-',$dt_vencimento);
						$mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[0]);
						$qtd_dias_mes = date('t',$mes_prox_venc);
		
						if( $qtd_dias_mes < $dia_vencimento ){
							$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[0]);
							$dt_vencimento = date('Y-m-d',$dt_vencimento);
						}else{
							$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[0]);
							$dt_vencimento = date('Y-m-d',$dt_vencimento);
						}
					
					}else{
		
						$dt_vencimento_atual = explode('-',$dt_vencimento);
						$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
						$dt_vencimento = date('Y-m-d',$dt_vencimento);
		
					}
				
					$lancamento_rcr['dt_vencimento'] = $dt_vencimento;
				
				}
		
			}
		}
		//fim da busca por lançamentos recorrentes
	
		if($exc_rcr){
			$query_lancamentos = '
				select dt_vencimento ordem, id, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento_format, dt_vencimento, favorecido_id, descricao, conta_id, valor, 0 recorrente, "" dt_prox_venc
				from lancamentos 
				where tipo = "'.$tp_lnct.'"
					and compensado = 0 '.$filtro_query;
		}else{
			$query_lancamentos = '
				(select dt_vencimento ordem, id, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento_format, dt_vencimento, favorecido_id, descricao, conta_id, valor, 0 recorrente, "" dt_prox_venc
				from lancamentos 
				where tipo = "'.$tp_lnct.'"
					and compensado = 0 '.$filtro_query.')
					
				union
		
				(select dt_vencimento ordem, id, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento_format, dt_vencimento, favorecido_id, descricao, conta_id, valor, recorrente, dt_prox_venc
				from lancamentos_recorrentes_temp)
				
				order by dt_vencimento
			';
		}
	
		$lancamentos_listar = "";
		
		$array_lancamentos = $db->fetch_all_array($query_lancamentos);
		
		foreach($array_lancamentos as $lancamento){
			if($bool_ctr && $bool_plc){
				$query_plc_ctr = 'select id from ctr_plc_lancamentos where lancamento_id = '.$lancamento['id'].' and (centro_resp_id = '.$centro_resp_id.' or plano_contas_id = '.$plano_contas_id.')';
			}elseif($bool_ctr){
				$query_plc_ctr = 'select id from ctr_plc_lancamentos where lancamento_id = '.$lancamento['id'].' and centro_resp_id = '.$centro_resp_id;
			}elseif($bool_plc){
				$query_plc_ctr = 'select id from ctr_plc_lancamentos where lancamento_id = '.$lancamento['id'].' and plano_contas_id = '.$plano_contas_id;
			}
			$plc_ctr = 0;
			if($bool_ctr || $bool_plc){
				$plc_ctr = $db->numRows($query_plc_ctr);
			}
			if( $plc_ctr>0 || !($bool_ctr || $bool_plc) ){
	
				$nome_favorecido = $db->fetch_assoc("select nome from favorecidos where id = ".$lancamento['favorecido_id']);	
	
				$dt_vencimento = $lancamento['dt_vencimento_format'];
			
				$data = explode('-',$lancamento['dt_vencimento']);
				$dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
				/* Verifica se o data esta atrasada*/
				if(date('Y-m-d') > date('Y-m-d',$dt_limite)){
					$hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
					$atraso = $hoje - $dt_limite;
					//$atraso = date('d',$atraso);
					$atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
					//$dt_vencimento .= "</b> <font class='subTexto red'>  <br> Atrasado ".$atraso." dia(s) </font>";
					$atrasado = 'red';
				}else{ $atrasado = ''; }
				
				// ============ data ============
						$dt_compensar = explode("/", $dt_vencimento);
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
			
				$opcoes = "";
				if($lancamento[recorrente]==0){
					if($tp_lnct=="R"){
						$class_excluir = 'recebimentosExcluir';
						$form_id = 'form_rcbt';
						$dialog_id = 'dialog-rcbt';
						$link_boleto = '<a href="javascript://void(0);" original-title="Boleto" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',2,\''.$form_id.'\',\''.$dialog_id.'\',\'boleto\','.$lancamento['recorrente'].')"><img src="images/icons/dark/barCod.png" width="10"></a>';
						$cor_valor = 'blue';
					}else{
						$class_excluir = 'pagamentosExcluir';
						$link_boleto = '';
						$cor_valor = 'red';
						$form_id = 'form_pgto';
						$dialog_id = 'dialog-pgto';
					}
					$opcoes = '
						<a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS '.$class_excluir.'" id="link_excluir_'.$lancamento['id'].'" onClick="alertaExcluir('.$lancamento['id'].',\''.$tp_lnct.'\','.$lancamento['recorrente'].');"><img src="images/icons/light/close.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].',\'\',\''.$form_id.'\',\''.$dialog_id.'\',\'edit\','.$lancamento['recorrente'].',\''.$tp_lnct.'\')"><img src="images/icons/light/pencil.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Quitar" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',1,\''.$form_id.'\',\''.$dialog_id.'\',\'qtr\','.$lancamento['recorrente'].',\''.$tp_lnct.'\')"><img src="images/icons/dark/check.png" width="10"></a>	
						'.$link_boleto;
				}else{
					if($tp_lnct=="R"){
						$class_excluir = 'recebimentosRcrExcluir';
						$link_boleto = '<a href="javascript://void(0);" original-title="Boleto" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',2,\''.$form_id.'\',\''.$dialog_id.'\',\'boleto\','.$lancamento['recorrente'].')"><img src="images/icons/dark/barCod.png" width="10"></a>';
						$cor_valor = 'blue';
					}else{
						$class_excluir = 'pagamentosRcrExcluir';
						$link_boleto = '';
						$cor_valor = 'red';
					}
					$opcoes = '
						<a href="javascript://void(0);" original-title="Excluír" class="smallButton redB  btTBwf tipS '.$class_excluir.'" id="link_excluir_'.$lancamento['id'].'" onClick="alertaExcluir('.$lancamento['id'].',\''.$tp_lnct.'\','.$lancamento['recorrente'].');"><img src="images/icons/light/close.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].',\'\',\''.$form_id.'\',\''.$dialog_id.'\',\'edit\','.$lancamento['recorrente'].',\''.$tp_lnct.'\')"><img src="images/icons/light/pencil.png" width="10"></a>
						<a href="javascript://void(0);" original-title="Quitar" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',1,\''.$form_id.'\',\''.$dialog_id.'\',\'qtr\','.$lancamento['recorrente'].',\''.$tp_lnct.'\')"><img src="images/icons/dark/check.png" width="10"></a>
						'.$link_boleto;
				}
		
				$lancamentos_listar .= '
					<tr class="gradeA">
						<td>'.$lancamento['dt_ordem'].'</td>
						<td class="updates newUpdate">
										
								<div class="uDate tbWF tipS" original-title="Vencimento" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
								<span class="lDespesa tbWF" >
									<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
										<span original-title="Favorecido" class="tipN">'.$nome_favorecido['nome'].'</span>
								</span>											
													
								<div class="tbWFoption" id="link_excluir_'.$lancamento['id'].'">
										'.$opcoes.'		
								</div>
																																											
								<div class="tbWFvalue '.$cor_valor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
		
						</td> 
					</tr>
				';
			}
		}
	
		$lancamentos_listar ='
			<table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos" >
			<thead>
			 <tr style="border-bottom: 1px solid #e7e7e7;">
							<th>Ordem</th>
							<th> 
									<table width="100%"><tr>
										<td>Descrição</td>
										<td width="60">Opções</td>
									</td></tr></table>
							</th>
						</tr>
			</thead>
			<tbody>
				'.$lancamentos_listar.'
			</tbody>
			</thead>
		';
		
		return $lancamentos_listar;
	}

/*
===========================================================================================
LISTAR LANÇAMENTOS DE TRANSFERÊNCIA
===========================================================================================
*/

	function lancamentosTransfListar($db,$params){
		
		//filtro
		$array_filtro = array();
	
		$filtro = $params['filtro'];
		$filtro = str_replace('\"','"',$filtro);
		$filtro = str_replace("\'","'",$filtro);
		$filtro = json_decode($filtro, true);
	
		//quantidade de contas no total e quantidade selecionada no filtro
		$qtd_contas = $db->numRows("select id from contas");
		$qtd_contas_fltr = count(explode(',',$filtro['conta_id']));
		
		//data
		$flt_dt_ativo = $filtro["dt_ativo"];
		$flt_tp_venc = explode(',',$filtro["tp_venc"]);
		$a_vencer = in_array("'av'",$flt_tp_venc);
		$vencido = in_array("'v'",$flt_tp_venc);
	
		if($flt_dt_ativo=="mes"){
			$dt_mes = $filtro["dt_mes"];
			if($dt_mes==""){
				$dt_ini = date('Y').'-'.date('m').'-01';
				$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
				$dt_fim = date('Y-m-d',$dt_fim);
			}else{
				$dt_mes = explode('/',$dt_mes);
				$dt_ini = date('Y').'-'.$dt_mes[0].'-01';
				$dt_fim = mktime(0,0,0,$dt_mes[0]+1,'00',date('Y'));
				$dt_fim = date('Y-m-d',$dt_fim);
			}
		}else{
			$dt_ini = $filtro['dt_ini'];
			$dt_fim = $filtro['dt_fim'];
			if($dt_ini=="" || $dt_fim==""){
				$dt_ini = date('Y').'-'.date('m').'-01';
				$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
				$dt_fim = date('Y-m-d',$dt_fim);
			}else{
				$dt_ini = $db->data_to_sql($dt_ini);
				$dt_fim = $db->data_to_sql($dt_fim);
			}
		}
	
		if( $a_vencer && !$vencido ){
			if( strtotime($dt_ini) < strtotime('now') );
				$dt_ini = date('Y-m-d');
		}elseif( $vencido && !$a_vencer ){
			if( strtotime($dt_fim) > strtotime('-1 day') )
				$dt_fim = date('Y-m-d',strtotime('-1 day'));
		}
	
		$array_filtro[] =	'dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"';
	
		//contas financeiras
		if($filtro["conta_id"]!=''){
			//$array_filtro[] = 'conta_id in ('.$filtro["conta_id"].')';
			//$conta_id_origem = 'and conta_id_origem in ('.$filtro["conta_id"].')';
			//$conta_id_destino = 'and conta_id_destino in ('.$filtro["conta_id"].')';
			$fltr_cf = explode(',',$filtro["conta_id"]);
			$conta_id_origem = 'conta_id_origem in ('.$filtro["conta_id"].')';
			$conta_id_destino = 'conta_id_destino in ('.$filtro["conta_id"].')';
		}
		
	
		//valor
		$valor = $filtro["valor"];
		if($valor!='' && $valor!='0,00'){
			$valor = $db->valorToDouble($valor);
			$array_filtro[] = 'valor = '.$valor;
		}
	
		//monta where
		//$filtro_query = $array_filtro[0];
		//array_shift($array_filtro);
		$filtro_query = "";
		foreach($array_filtro as $filtro){
			$filtro_query .= ' and '.$filtro;
		}
		//fim do filtro
		
		/*
		$query_lancamentos = "
			(select dt_vencimento ordem, id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, descricao, valor, conta_id_origem, conta_id_destino
			from lancamentos
			where tipo = 'T'
				and compensado = 0 
				".$filtro_query."
				".$conta_id_origem.")
			
			union all	
	
			(select dt_vencimento ordem, id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, descricao, valor, conta_id_origem, conta_id_destino
			from lancamentos
			where tipo = 'T'
				and compensado = 0 
				".$filtro_query."
				".$conta_id_destino.")
		";
		*/
	
		if(!$fltr_cf){
			$query_lancamentos = "
				select dt_vencimento ordem, id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, descricao, valor, conta_id_origem, conta_id_destino, conta_id_destino
				from lancamentos
				where tipo = 'T'
					and compensado = 0".$filtro_query;
		}else{
			$query_lancamentos = "
				select dt_vencimento ordem, id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, descricao, valor, conta_id_origem, conta_id_destino, conta_id_destino
				from lancamentos
				where tipo = 'T'
					and compensado = 0 
					and (".$conta_id_origem." or ".$conta_id_destino.")".$filtro_query;
		}
		
		$lancamentos_listar = '';
	
		$array_lancamentos = $db->fetch_all_array($query_lancamentos);
	
		foreach($array_lancamentos as $lancamento){
			
				$nome_conta_org = $db->fetch_assoc("select descricao from contas where id = ".$lancamento['conta_id_origem']);
				$nome_conta_dest = $db->fetch_assoc("select descricao from contas where id = ".$lancamento['conta_id_destino']);
	
			//verifica se o lançamento está atrasado
			$dt_vencimento = $lancamento['dt_vencimento_format'];
	
			$data = explode('-',$lancamento['dt_vencimento']);
			$dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
	 
			if(date('Y-m-d') > date('Y-m-d',$dt_limite)){
				$hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
				$atraso = $hoje - $dt_limite;
				//$atraso = date('d',$atraso);
				$atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
				$atrasado = 'red';
			}else{ $atrasado = ''; }
	
						// ============ data ============
						$dt_compensar = explode("/", $dt_vencimento);
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
		
			//$conta_id = $lancamento['conta_id_origem'];
			//$conta = $db->fetch_assoc('select banco_id, descricao from contas where id ='.$conta_id);
	
			//if($conta['banco_id']!=0){
				//$banco_id = $conta['banco_id'];
				//$banco = $db->fetch_assoc('select nome from bancos where id ='.$banco_id);
				//$banco_nome = " - ".$banco["nome"];
			//}else{
				//$banco_nome = "";
			//}
			//<td>'.$lancamento['descricao'].'<font class="subTexto blue">  <br> <b>'.$conta['descricao'].'</b>'.$banco_nome.' </font></td>
	
			if($fltr_cf){
				$cf_lnct_org = in_array($lancamento['conta_id_origem'],$fltr_cf); //verifica se a conta de origem do lançamento está no filtro
				$cf_lnct_desti = in_array($lancamento['conta_id_destino'],$fltr_cf); //verifica se a conta de destino do lançamento está no filtro
			}
						
			if( !$fltr_cf || ($qtd_contas_fltr==$qtd_contas) || ($cf_lnct_org && $cf_lnct_desti) ){
				$lancamentos_listar .= '
					<tr class="gradeA">
									<td>'.$lancamento['dt_ordem'].'</td>
									<td class="updates newUpdate">
													
											<div class="uDate tbWF tipS" original-title="Vencimento" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
												<span class="lDespesa tbWF" >
													<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
														<span original-title="Conta Financeira" class="tipN"><b>Débito:</b> '.$nome_conta_org['descricao'].'</span>
												</span>											
																
											<div class="tbWFoption">										
											<a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS transferenciasExcluir" onClick="alertaExcluir('.$lancamento['id'].',\'T\',0);"><img src="images/icons/light/close.png" width="10"></a>
											<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].')"><img src="images/icons/light/pencil.png" width="10"></a>
											<a href="javascript://void(0);" original-title="Quitar" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',1)"><img src="images/icons/dark/check.png" width="10"></a>
											</div>
																																														
											<div class="tbWFvalue red">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
					
								</td> 
								</tr>
								<tr class="gradeA">
									<td>'.$lancamento['dt_ordem'].'</td>
									<td class="updates newUpdate">
													
											<div class="uDate tbWF tipS" original-title="Vencimento" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
												<span class="lDespesa tbWF" >
													<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
														<span original-title="Conta Financeira" class="tipN"><b>Crédito:</b> '.$nome_conta_dest['descricao'].'</span>
												</span>											
																
											<div class="tbWFoption">										
											<a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS transferenciasExcluir" onClick="alertaExcluir('.$lancamento['id'].',\'T\',0);"><img src="images/icons/light/close.png" width="10"></a>
											<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].')"><img src="images/icons/light/pencil.png" width="10"></a>
											<a href="javascript://void(0);" original-title="Quitar" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',1)"><img src="images/icons/dark/check.png" width="10"></a>
											</div>
																																														
											<div class="tbWFvalue blue">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
					
								</td> 
								</tr>
				';
			}else{
				($cf_lnct_org)? $cor = 'color="#FF0000"' : $cor = 'color="#009900"';
				$lancamentos_listar .= '
					<tr class="gradeA">
						<td>'.$lancamento['ordem'].'</td>
						<td align="center"><b>'.$dt_vencimento.'</b></td>
						<td>'.$lancamento['descricao'].'</td>
						<td align="right"><font '.$cor.'>R$ '.number_format($lancamento['valor'],2,',','.').'</font></td>
						<td class="center">
							<a href="javascript://void(0);" title="Editar" class="smallButton" style="margin: 5px;" onClick="lancamentosExibir('.$lancamento['id'].',\''.$lancamento['tipo'].'\')"><img src="images/icons/dark/pencil.png" alt=""></a>
							<a href="'.$lancamento['id'].'" title="Excluír" class="smallButton transferenciasExcluir" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
						</td>
					</tr>
				';
			}
		}
	
		$lancamentos_listar ='
			<table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos">
			<thead>
			 <tr style="border-bottom: 1px solid #e7e7e7;">
				<th>Ordem</th>
				<th> 
						<table width="100%"><tr>
							<td>Descrição</td>
							<td width="60">Opções</td>
						</td></tr></table>
				</th> 
			</tr>
			</thead>
			<tbody>
				'.$lancamentos_listar.'
			</tbody>
			</table>
		';
	
		return $lancamentos_listar;
	}

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
			$marcacao = "";
			if($conta['id']==$conta_id){
				$marcacao = '<img src="images/icons/icon-checked-conta.png" border="0"/>';
			}
					
			 if(!empty($conta['logo'])){ $banco_logo = $conta['logo']; }else{ $banco_logo = "bank.png"; }
			
			$contas_saldo .= '
				<div class="userRow">
						<img src="images/bancos/'.$banco_logo.'" alt="" class="floatL">
						<ul class="leftList">
								<li><a href="javascript://void(0);" style="cursor: default;"><strong>'.$array_contas[0]['descricao'].'</strong></a></li>
								<li style="font-size: 9px;">'.$conta['nome'].'</li>
						</ul>
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
COMPENSAR RECEBIMENTO
===========================================================================================
*/
 
	function recebimentosCompensar($db,$array_dados){
		parent::atualizarSaldoContaRcbt($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id'],'add');
		self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],"R",1);
		$db->query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$array_dados['lancamento_id']);
	}

/*
===========================================================================================
COMPENSAR RECEBIMENTO RECORRENTE
===========================================================================================
*/
 
	function recebimentosRcrCompensar($db,$array_dados){
		$compensar = $this->recebimentosIncluir($db,$array_dados);
		if($compensar==1){
			//atualiza a data de próximo vencimento do lançamento recorrente
			if($array_dados['frequencia']==0){
				$dt_vencimento_atual = explode('-',$array_dados['dt_venc_ref']);
				$qtd_dias = $array_dados['qtd_dias'];
				$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+$qtd_dias,$dt_vencimento_atual[0]);
				$dt_vencimento = date('Y-m-d',$dt_vencimento);
			}elseif($array_dados['frequencia']>=30){
				$frequencia = $array_dados['frequencia']/30;
				$dia_vencimento = $array_dados['dia_mes'];
				$dt_vencimento_atual = explode('/',$array_dados['dt_vencimento']);
				$mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[2]);
				$qtd_dias_mes = date('t',$mes_prox_venc);
				if( $qtd_dias_mes < $dia_vencimento ){
					$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[2]);
					$dt_vencimento = date('Y-m-d',$dt_vencimento);
				}else{
					$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[2]);
					$dt_vencimento = date('Y-m-d',$dt_vencimento);
				}
			}else{
				$dt_vencimento_atual = explode('/',$array_dados['dt_vencimento']);
				$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[0]+7,$dt_vencimento_atual[2]);
				$dt_vencimento = date('Y-m-d',$dt_vencimento);
			}
			$db->query("update lancamentos_recorrentes set dt_vencimento = '".$dt_vencimento."', dt_prox_venc = '".$dt_vencimento."'  where id = ".$array_dados['lancamento_id']);
			//fim da atualização da data de próximo vencimento
			return 1;
		}else{
			return 2;
		}
	}

/*
===========================================================================================
COMPENSAR PAGAMENTO
===========================================================================================
*/

	function pagamentosCompensar($db,$array_dados){
		$conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$this->lancamento_dados['conta_id']);
		if($conta['saldo_total']>=$this->lancamento_dados['valor']){
			parent::atualizarSaldoContaPgto($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id'],$conta['vl_saldo']);
			$db->query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$array_dados['lancamento_id']);
			return 1;
		}else{
			return 2;
		}
	}

/*
===========================================================================================
COMPENSAR PAGAMENTO RECORRENTE
===========================================================================================
*/

	function pagamentosRcrCompensar($db,$array_dados){
		$compensar = parent::pagamentosIncluir($db,$array_dados);
		if($compensar==1){
			//atualiza a data de próximo vencimento do lançamento recorrente
			if($array_dados['frequencia']==0){
				$dt_vencimento_atual = explode('-',$array_dados['dt_venc_ref']);
				$qtd_dias = $array_dados['qtd_dias'];
				$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+$qtd_dias,$dt_vencimento_atual[0]);
				$dt_vencimento = date('Y-m-d',$dt_vencimento);
			}elseif($array_dados['frequencia']>=30){
				$frequencia = $array_dados['frequencia']/30;
				$dia_vencimento = $array_dados['dia_mes'];
				$dt_vencimento_atual = explode('/',$array_dados['dt_vencimento']);
				$mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[2]);
				$qtd_dias_mes = date('t',$mes_prox_venc);
				if( $qtd_dias_mes < $dia_vencimento ){
					$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[2]);
					$dt_vencimento = date('Y-m-d',$dt_vencimento);
				}else{
					$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[2]);
					$dt_vencimento = date('Y-m-d',$dt_vencimento);
				}
			}else{
				$dt_vencimento_atual = explode('/',$array_dados['dt_vencimento']);
				$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[0]+7,$dt_vencimento_atual[2]);
				$dt_vencimento = date('Y-m-d',$dt_vencimento);
			}
			$db->query("update lancamentos_recorrentes set dt_vencimento = '".$dt_vencimento."', dt_prox_venc = '".$dt_vencimento."'  where id = ".$array_dados['lancamento_id']);
			//fim da atualização da data de próximo vencimento
			return 1;
		}else{
			return 2;
		}
	}

/*
===========================================================================================
COMPENSAR TRANSFERÊNCIA
===========================================================================================
*/

	function transferenciasCompensar($db){
		$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$this->lancamento_dados['conta_id_origem']." for update");
		if($conta_origem['saldo_total']>=$this->lancamento_dados['valor']){
			parent::atualizarSaldoContaTrans($db,$this->lancamento_dados['valor'],$this->lancamento_dados['conta_id_origem'],$this->lancamento_dados['conta_id_destino'],$conta_origem['vl_saldo']);
			$retorno = array("situacao"=>1,"notificacao"=>"Transferência lançada com sucesso.");
			return 1;
		}else{
			return 2;
		}
	}	

/*
===========================================================================================
ATUALIZAR LANÇAMENTOS NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

	function ctrPlcLancamentosAtualizar($db,$lancamentos,$lancamento_id,$tp_lancamento,$qtd_parcelas){
		$jsonTxt = str_replace('\"','"',$lancamentos);
		$jsonObj = json_decode($jsonTxt, true);
		$array_lancamentos = $jsonObj;
		$db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id);
		if(count($array_lancamentos)>0){
			foreach($array_lancamentos as $lancamento){
				if($lancamento["operacao"]=="1"){ //inclui um novo lançamento
					$array_insert["lancamento_id"] = $lancamento_id;
					$array_insert["centro_resp_id"] = $lancamento["centro_resp_id"];
					$array_insert["plano_contas_id"] = $lancamento["plano_contas_id"];
					$array_insert["tp_lancamento"] = $tp_lancamento;
					
					$valor = $db->valorToDouble($lancamento["valor"]);
					$valor = $valor/$qtd_parcelas;
					$array_insert["valor"] = $valor;
		
					$porcentagem = $db->valorToDouble($lancamento["porcentagem"]);
					$porcentagem = $porcentagem/$qtd_parcelas;
					$array_insert["porcentagem"] = $porcentagem;
					
					$array_insert["situacao"] = 0;
					$array_insert["dt_cadastro"] = date('Y-m-d');
					$db->query_insert("ctr_plc_lancamentos",$array_insert);
				}
			}
		}
	}

/*
===========================================================================================
GERA CHAVE PARA EMISSÃO DO BOLETO
===========================================================================================
*/

	function boletosChaveGerar($db,$array_dados,$cliente_id){
	
		//convenio da conta para emissão do boleto
		$convenio = $db->fetch_assoc("select convenio from contas where id = ".$array_dados['conta_id']);
	
		//número sequencial do boleto
		$query_sequencial = "
			select b.id boleto_id, sequencial
			from boletos b, lancamentos l
			where b.id = l.boleto_id
				and l.id = ".$array_dados['lancamento_id'];
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
				$boleto_ano = $novo_boleto_ano;
			}else{
				$db->query("update contas set sequencial = sequencial + 1 where id = ".$array_dados['conta_id']);
				$conta_sequencial = $sequencial['sequencial'];
				$boleto_ano = $sequencial['boleto_ano'];
			}
			$boleto_sequencial = $conta_sequencial.$boleto_ano;
			$boleto = array("sequencial"=>$boleto_sequencial,"lancamento_id"=>$array_dados['lancamento_id']);
			$boleto_id = $db->query_insert("boletos",$boleto);
			//$db->query("update lancamentos set boleto_id = ".$boleto_id." where id = ".$array_dados['lancamento_id']);
			$db->query("commit");
		}else{
			$boleto_id = $sequencial['boleto_id'];
			$boleto_sequencial = $sequencial['sequencial'];
		}
		
		//chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
		$chave = $cliente_id.'-'.$convenio['convenio'].'-'.$array_dados['lancamento_id'].'-'.$boleto_id.'-'.$boleto_sequencial;
		
		return $chave;
		
	}

/*
===========================================================================================
GERA CHAVE PARA EMISSÃO DO BOLETO PARA LANÇAMENTO RECORRENTE
===========================================================================================
*/

	function boletosChaveGerarRcr($db,$array_dados,$cliente_id){
	
		//converte o lançamento recorrente em programado
			$lancamento_id = $this->rcr_to_prog($db,$array_dados); 
		
		//inclui registro do boleto e gera chave
			//convenio da conta para emissão do boleto
			$convenio = $db->fetch_assoc("select convenio from contas where id = ".$array_dados['conta_id']);
		
			//número sequencial do boleto
			$query_sequencial = "
				select b.id boleto_id, sequencial
				from boletos b, lancamentos l
				where b.id = l.boleto_id
					and l.id = ".$lancamento_id;
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
					$boleto_ano = $novo_boleto_ano;
				}else{
					$db->query("update contas set sequencial = sequencial + 1 where id = ".$array_dados['conta_id']);
					$conta_sequencial = $sequencial['sequencial'];
					$boleto_ano = $sequencial['boleto_ano'];
				}
				$boleto_sequencial = $conta_sequencial.$boleto_ano;
				$boleto = array("sequencial"=>$boleto_sequencial,"lancamento_id"=>$lancamento_id);
				$boleto_id = $db->query_insert("boletos",$boleto);
				$db->query("update lancamentos set boleto_id = ".$boleto_id." where id = ".$lancamento_id);
				$db->query("commit");
			}else{
				$boleto_id = $sequencial['boleto_id'];
				$boleto_sequencial = $sequencial['sequencial'].date('y');
			}
	
			//chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
			$chave = $cliente_id.'-'.$convenio['convenio'].'-'.$lancamento_id.'-'.$boleto_id.'-'.$boleto_sequencial;
		//fim inclui registro do boleto e gera chave
		
		return $chave;
		
	}

/*
===========================================================================================
ATUALIZAR VENCIMENTO DE LANÇAMENTO RECORRENTE
===========================================================================================
*/

	function atualizarVencimentoRcr($db,$lnct_rcr_id){

		$lancamento_rcr = $db->fetch_assoc('select dt_vencimento, frequencia, dia_mes from lancamentos_recorrentes where id = '.$lnct_rcr_id);

		//atualiza a data de próximo vencimento do lançamento recorrente
		if($lancamento_rcr['frequencia']>=30){
			$frequencia = $lancamento_rcr['frequencia']/30;
			$dia_vencimento = $lancamento_rcr['dia_mes'];
			$dt_vencimento_atual = explode('-',$lancamento_rcr['dt_vencimento']);
			$mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[0]);
			$qtd_dias_mes = date('t',$mes_prox_venc);
			if( $qtd_dias_mes < $dia_vencimento ){
				$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[0]);
				$dt_vencimento = date('Y-m-d',$dt_vencimento);
			}else{
				$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[0]);
				$dt_vencimento = date('Y-m-d',$dt_vencimento);
			}
		}else{
			$dt_vencimento_atual = explode('-',$lancamento_rcr['dt_vencimento']);
			$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
			$dt_vencimento = date('Y-m-d',$dt_vencimento);
		}
		$db->query('update lancamentos_recorrentes set dt_vencimento = "'.$dt_vencimento.'", dt_prox_venc = "'.$dt_vencimento.'"  where id = '.$lnct_rcr_id);
		//fim da atualização da data de próximo vencimento

	}

/*
===========================================================================================
CONVERTE LANÇAMENTO RECORRENTE PARA PROGRAMADO
===========================================================================================
*/
  
	function rcr_to_prog($db,$array_dados){

		$lnct_rcr_id = $array_dados['lancamento_id'];
		
		//inclui lançamento programado
		$this->lancamento_dados['lancamento_recorrente_id'] = $lnct_rcr_id;
		$lnct_prog_id = $db->query_insert('lancamentos',$this->lancamento_dados);
		
		//inclui valores no plano de contas e centro de responsabilidade
		if($array_dados['ct_resp_lancamentos']!="")
			$this->ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lnct_prog_id,$array_dados['tipo'],1);

		$this->atualizarVencimentoRcr($db,$lnct_rcr_id);
		
		return $lnct_prog_id;

	}

/*
================================================================================================
ENVIAR EMAIL
================================================================================================
*/

	function emailEnviar($assunto,$conteudo){
	
		$email_destinatario = array('fabio@web2business.com.br');
		
		/*	
			$conteudo = "conteudo de teste";
			$email_destinatario	= "fabio@web2business.com.br";
			$assunto = "Testando Radar Gourmet";
		*/
		$email_remetente = "fabio@web2business.com.br";
		$nome_remetente = "Web Finanças";
		
		/*=========== INICIALIZA O OBJETO QUE ENVIA O EMAIL =======================================*/
		$transport = Swift_SmtpTransport::newInstance('smtplw.com.br', 465, 'tls'); //$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 25);
		$transport->setUsername('hostw2b');
		$transport->setPassword('W2BSISTEMAS');
		
		$message = Swift_Message::newInstance();
		$message->setSubject($assunto);
		$message->setFrom(array($email_remetente => $nome_remetente));
		//$message->setReturnPath('fabio@web2business.com.br');
	
		$mailer = Swift_Mailer::newInstance($transport);
		/*==============================================================================================*/
		
		$message->setBody($conteudo, 'text/html');
		$message->setTo($email_destinatario); //não precisa limpar o destinatario a cada envio, esta função sobre-escreve o destinatario anterior ; $message->setTo(array($email_destinatario));
		
		$mailer->send($message); 

	}

/*
===========================================================================================
LOG
===========================================================================================
*/

	function log_rotina($arquivo_log,$msg,$enviar_email=false,$assunto=''){
		$arquivo = '../log/'.$arquivo_log;
		$fp = fopen($arquivo,"a+");
		fwrite($fp,$msg."\r\n");
		fclose($fp);
		if($enviar_email){
			$conteudo = $msg;
			self::emailEnviar($assunto,$conteudo);
		}
		//echo $arquivo_log.' '.$erro_msg;
	}


/*
===========================================================================================
COMPENSAR LANÇAMENTOS AUTOMATICAMENTE
===========================================================================================
*/

	function lnctAutoCompensar(){

		try{
			//data de hoje
			$hoje = date('Y-m-d');
	
			//busca os bancos de dados dos clientes
			//$db_conexao = new mysqli('mysql.web2business.com.br','web2business09','W2BSISTEMAS','web2business09');
			$db_conexao = new mysqli('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
	
			//inicia transação
			mysqli_query($db_conexao,'start transaction');
	
			//quantidade de rotinas programadas para executar
			$qtd_rotinas_ativas = 1;
			
			//verifica se a primeira rotina já iniciou o processo de compensação para o dia corrente
			$result = $db_conexao->query('select count(id) qtd from rotinas_auto_compensar where situacao = 0 and dt_cadastro = "'.$hoje.'"');
			$qtd_rotinas = $result->fetch_assoc();
	
			if($qtd_rotinas['qtd']==0){
				//quantidade de bancos de dados de clientes
				$qtd_clientes_db = mysqli_fetch_assoc(mysqli_query($db_conexao,'select count(id) qtd from clientes_db where situacao = 1'));
				$db_limite_total = $qtd_clientes_db['qtd'];
	
				//quantidade de bancos de dados de clientes que cada rotina processa por vez
				$db_limite = $qtd_clientes_db['qtd']/$qtd_rotinas_ativas;
				$db_limite = ceil($db_limite);

				//arquivo pra registro de erros
				$arquivo_log = 'log_'.$hoje.'.txt';
				
				//insere registro da primeira rotina no banco de dados
				mysqli_query($db_conexao,'insert into rotinas_auto_compensar (limite, total, dt_cadastro, situacao, log) values('.$db_limite.','.$db_limite_total.',"'.$hoje.'",0,"'.$arquivo_log.'")');
				$rotina_id = mysqli_insert_id($db_conexao);
				
				//query para consultar bancos de dados dos clientes
				$query_clientes_db = mysqli_query($db_conexao,'select db, db_senha from clientes_db where situacao = 1 limit 0,'.$db_limite);
				
				//registra no log o início da rotina
				$hora = date('d/m/Y - H:i:s');
				$msg = 'Início da rotina auto compensar - '.$hora;
				self::log_rotina($arquivo_log,$msg);
			}else{
				//quantidade de bancos de dados de clientes
				$query_limite = mysqli_fetch_assoc(mysqli_query($db_conexao,'select id, limite, total, log from rotinas_auto_compensar where situacao = 0 and dt_cadastro = "'.$hoje.'"')); //0 - pendente/processando; 1 - concluído; 2 - erro
				$rotina_id = $query_limite['id'];
				$db_limite_total = $query_limite['total'];
				
				//quantidade de bancos de dados de clientes que cada rotina processa por vez
				$db_limite = $db_limite_total/$qtd_rotinas_ativas;
				$db_limite = ceil($db_limite);

				//arquivo pra registro de erros
				$arquivo_log = $query_limite['log'];

				//query para consultar bancos de dados dos clientes
				$query_clientes_db = mysqli_query($db_conexao,'select db, db_senha from clientes_db where situacao = 1 limit '.$query_limite['limite'].','.$db_limite);
	
				//estabelece o limite mínimo para a próxima rotina
				$db_limite += $query_limite['limite'];
			}
			
			//atualiza o limite inicial para a próxima rotina ou encerra a execução na rotina corrente
			if($db_limite>=$db_limite_total){
				mysqli_query($db_conexao,'update rotinas_auto_compensar set limite = total, situacao = 1 where id = '.$rotina_id);
			}else{
				mysqli_query($db_conexao,'update rotinas_auto_compensar set limite = '.$db_limite.' where id = '.$rotina_id);
			}
			
			//encerra a transação
			//mysqli_query('abc'); //erro de teste
			mysqli_query($db_conexao,'commit');
			$db_conexao->close();
		}
		catch(Exception $e){
			//echo 'Erro: ',$e->getMessage();
			mysqli_query($db_conexao,'rollback');
			$assunto = 'ERRO - Rotina auto compensar';
			$msg = $e->getMessage();
			self::log_rotina($arquivo_log,$msg,true,$assunto);
			exit();
		}

		try{
			//mysqli_query('abc'); //erro de teste
			while($cliente_db = mysqli_fetch_assoc($query_clientes_db)){
				//conecta no banco de dados do cliente
				$cliente_db_conexao = new Database('mysql.webfinancas.com',$cliente_db['db'],$cliente_db['db_senha'],$cliente_db['db']);
				//$cliente_db_conexao = new Database('mysql.web2business.com.br',$cliente_db['db'],$cliente_db['db_senha'],$cliente_db['db']);
				
				//inicia a transação
				$cliente_db_conexao->query('start transaction');
				
				//busca lancamentos recorrentes à vencer e converte para programado
				$query_lancamentos_rcr = mysql_query('
					select id
					from lancamentos_recorrentes
					where dt_vencimento = "'.$hoje.'"
				',$cliente_db_conexao->link_id);

				while($lancamento_rcr = mysql_fetch_assoc($query_lancamentos_rcr)){

					//busca dados do lançamento, plano de contas e centro de responsabilidade
					$lnct_rcr_dados = $this->lancamentosRcrExibir($db,array("lancamento_id"=>$lancamento_rcr['id']),1);
					$lnct_rcr = $lnct_rcr_dados['lancamento'];
					$lnct_rcr_ctr_plc = $lnct_rcr_dados['ctr_plc_lancamentos'];

					//converte lançamento recorrente para programado
					self::__construct($cliente_db_conexao,$lnct_rcr);

					$this->lancamento_dados['qtd_parcelas'] = 1;
					$this->lancamento_dados['parcela_numero'] = 1;
					$this->lancamento_dados['lancamento_recorrente_id'] = $lancamento_rcr['id'];

					if($lnct_rcr['tipo']=='R'){
						$this->recebimentoProgIncluir($cliente_db_conexao,$this->lancamento_dados);
					}else{
						$this->pagamentosProgIncluir($cliente_db_conexao,$this->lancamento_dados);
					}
					
					//atualiza a data de próximo vencimento do lançamento recorrente
					$this->atualizarVencimentoRcr($db,$lancamento_rcr['id']);

				}
				//fim da busca por lançamentos recorrentes
	
				//busca lançamentos à vencer na tabela lancamentos ordenados por recebimentos, transferências e depois pagamentos
				//VERIFICAR A VIABILIDADE DE CRIAR UM INDICE NA DATA DE VENCIMENTO
				$dia_semana = date('N');
				if($dia_semana>=6){ //sabado ou domingo
					$query_lancamentos = mysql_query('
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and sab_dom = 1
							and tipo = "R"
						
						union
						
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and sab_dom = 1
							and tipo = "T"

						union
						
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and sab_dom = 1
							and tipo = "P"
					',$cliente_db_conexao->link_id);
				}elseif($dia_semana==1){ //seguda feira
					$dt_ini = mktime(0,0,0,date('m'),date('d')-2,date('Y'));
					$query_lancamentos = mysql_query('
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento >= "'.$dt_ini.'"
							and dt_vencimento <= "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "R"
							
						union
						
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento >= "'.$dt_ini.'"
							and dt_vencimento <= "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "T"
							
						union
						
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento >= "'.$dt_ini.'"
							and dt_vencimento <= "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "P"												
					',$cliente_db_conexao->link_id);
				}else{ //exceto sabado, domingo e segunda feira
					$query_lancamentos = mysql_query('
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "R"
							
						union
							
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "T"
							
						union
						
						select id, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "P"
					',$cliente_db_conexao->link_id);
				}

				$dt_compensacao = date('Y-m-d');
		
				//quita os lançamentos encontrados
				while($lancamento = mysql_fetch_assoc($query_lancamentos)){
					if($lancamento['tipo']=='R'){
						parent::atualizarSaldoContaRcbt($cliente_db_conexao,$lancamento['valor'],$lancamento['conta_id']);
						mysql_query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$lancamento['id'],$cliente_db_conexao->link_id);
						mysql_query("update lancamentos set compensado = 1, dt_compensacao = '".$dt_compensacao."' where id = ".$lancamento['id'],$cliente_db_conexao->link_id);
					}elseif($lancamento['tipo']=='P'){
						$conta_saldo = mysql_fetch_assoc(mysql_query("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$lancamento['conta_id'],$cliente_db_conexao->link_id));
						if($conta_saldo['saldo_total']>=$lancamento['valor']){
							parent::atualizarSaldoContaPgto($cliente_db_conexao,$lancamento['valor'],$lancamento['conta_id'],$conta_saldo['vl_saldo']);
							mysql_query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$lancamento['id'],$cliente_db_conexao->link_id);
							mysql_query("update lancamentos set compensado = 1, dt_compensacao = '".$dt_compensacao."' where id = ".$lancamento['id'],$cliente_db_conexao->link_id);
						}
					}elseif($lancamento['tipo']=='T'){
						$conta_origem_saldo = mysql_fetch_assoc(mysql_query("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$lancamento['conta_id_origem'],$cliente_db_conexao->link_id));
						if($conta_origem_saldo['saldo_total']>=$lancamento['valor']){
							parent::atualizarSaldoContaTrans($cliente_db_conexao,$lancamento['valor'],$lancamento['conta_id_origem'],$lancamento['conta_id_destino'],$conta_origem_saldo['vl_saldo']);
							mysql_query("update lancamentos set compensado = 1, dt_compensacao = '".$dt_compensacao."' where id = ".$lancamento['id'],$cliente_db_conexao->link_id);
						}
					}
				}
				//fim da busca e quitação na tabela lançamentos
			
				//encerra a transação
				$cliente_db_conexao->query('commit');
				$cliente_db_conexao->close();
			}
			if($db_limite>=$db_limite_total){
				$hora = date('d/m/Y - H:i:s');
				$msg = 'Rotina auto compensar executada com sucesso - '.$hora;
				self::log_rotina($arquivo_log,$msg);
			}
		}

		catch(Exception $e){
			//echo 'Erro: ',$e->getMessage();
			$cliente_db_conexao->query('rollback');
			$assunto = 'ERRO - Rotina auto compensar';
			$msg = $e->getMessage();
			self::log_rotina($arquivo_log,$msg,true,$assunto);
			exit();
		}
	}

}
?>