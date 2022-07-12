<?php
define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');

require_once ROOT.'/sistema/servicos/mensagem/MensagemHelper.php';

class Lancamento{

	var $lancamento_dados = array(
		"tipo" => "",
		"descricao" => "",
		"lancamento_pai_id" => "",
		"lancamento_recorrente_id" => "",
		"parcela_numero" => "1",
		"qtd_parcelas" => "1",
		"favorecido_id" => "",
		"favorecido_id_dep" => "",
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
		"dt_venc_ref" => "",
		"dt_compensacao" => "",
		"sab_dom" => "0",
		"compensado" => "",
        "fit_id" => ""
	);

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
			$this->lancamento_dados['dt_vencimento'] = $db->data_to_sql($array_dados['dt_vencimento']);
			if( isset($array_dados['dt_emissao']) )
				$this->lancamento_dados['dt_emissao'] = $db->data_to_sql($array_dados['dt_emissao']);
			if( isset($array_dados['dt_compensacao']) )
				$this->lancamento_dados['dt_compensacao'] = $db->data_to_sql($array_dados['dt_compensacao']);
		}
	}

/*
===========================================================================================
EXIBIR
===========================================================================================
*/

	function lancamentosExibir($db,$array_dados){
	    $tipo = $db->fetch_assoc("select tipo from lancamentos where id = ".$array_dados['lancamento_id']);
	    $array_dados['tp_lancamento'] = $tipo['tipo'];
		if($array_dados['tp_lancamento']=="T"){
			$query = "
				select l.id, l.tipo, l.descricao, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta_origem, IFNULL(concat(c2.descricao,' - ',b2.nome),c2.descricao) conta_destino,
				l.conta_id_origem, l.conta_id_destino, date_format(l.dt_competencia, '%m/%Y') dt_competencia, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento,
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
			$query = "
				select l.id, l.tipo, l.descricao, f.nome favorecido, f.id favorecido_id, d.nome favorecido_dep, d.id favorecido_id_dep, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, c.id conta_id,
				date_format(l.dt_competencia, '%m/%Y') dt_competencia, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, 
				date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, l.documento_id, l.forma_pgto_id, l.auto_lancamento, l.observacao, b.codigo cod_banco
				from lancamentos l
				left join contas c on l.conta_id = c.id
				left join favorecidos f on l.favorecido_id = f.id
				left join favorecidos d on l.favorecido_id_dep = d.id
				left join bancos b on c.banco_id = b.id
				where l.id = ".$array_dados['lancamento_id']."
			";

			//monta a lista de lannçamentos do centro de responsabilidade
			$query_ctr_plc_lancamentos = "
				select pc.cod_conta, cr.cod_centro, crl.id ctr_plc_lancamento_id, cr.id centro_resp_id, pc.id plano_contas_id, pc.nome conta, cr.nome centro, crl.valor, crl.porcentagem
				from ctr_plc_lancamentos crl
				left join centro_resp cr on crl.centro_resp_id = cr.id
				left join plano_contas pc on crl.plano_contas_id = pc.id
				where crl.lancamento_id = ".$array_dados['lancamento_id']." and (crl.centro_resp_id <> 0 || crl.plano_contas_id <> 0)";

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
		
		$anexos = self::anexosExibir($db,$array_dados["lancamento_id"]);

		$lancamentos_visualizar = $db->fetch_array($db->query($query));
		$lancamentos_visualizar['valor'] = number_format($lancamentos_visualizar['valor'],2,',','.');
		$retorno = array("lancamento"=>$lancamentos_visualizar,"ctr_plc_lancamentos"=>$jsonText,"anexos"=>$anexos); 
		return $retorno;
		
	}

/*
===========================================================================================
EXIBIR RECORRENTE
===========================================================================================
*/

	function lancamentosRcrExibir($db,$array_dados,$ctr_plc_opr_inc=0){ 

		//monta lançamento
		$query = "
			select lr.id, lr.tipo, lr.favorecido_id, f.nome favorecido, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, lr.conta_id, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, dt_vencimento dt_venc_ref, dt_emissao, dt_inicio, lr.descricao, valor, frequencia, auto_lancamento, qtd_dias, dia_mes, lr.documento_id, lr.forma_pgto_id, lr.observacao, lr.dt_comp_mes_dif, b.codigo cod_banco
			from lancamentos_recorrentes lr
			left join contas c on lr.conta_id = c.id
			left join bancos b on c.banco_id = b.id
			left join favorecidos f on lr.favorecido_id = f.id
			where lr.id = ".$array_dados['lancamento_id']."
		";

		$lancamentos_exibir = $db->fetch_assoc($query);

        //Utiliza a data de vencimento referente ao mês de vencimento exibido na lista de lançamentos para lançamentos recorrentes exibidos em meses seguintes ao próximo vencimento
        if(isset($array_dados['dt_referencia'])){
            $lancamentos_exibir['dt_vencimento'] = $db->sql_to_data($array_dados['dt_referencia']);
            $lancamentos_exibir['dt_venc_ref'] = $array_dados['dt_referencia'];
        }

		//formata valor
		$lancamentos_exibir['valor'] = $db->valorFormat($lancamentos_exibir['valor']);

		//calcula data de competência com base no vencimento
        $lancamentos_exibir['dt_competencia'] = self::DtCompetenciaCalc($lancamentos_exibir['dt_venc_ref'], $lancamentos_exibir['dt_comp_mes_dif']);

		//calcula data de emissão com base no vencimento
		$lancamentos_exibir['dt_emissao'] = self::dtEmissaoRcrCalc($lancamentos_exibir['dt_emissao'],$lancamentos_exibir['dt_inicio'],$lancamentos_exibir['dt_venc_ref']);
		
		//monta a lista de lançamentos do centro de responsabilidade
		$query_ctr_plc_lancamentos = "
			select pc.cod_conta, cr.cod_centro, crl.id ctr_plc_lancamento_id, cr.id centro_resp_id, pc.id plano_contas_id, pc.nome conta, cr.nome centro, crl.valor, crl.porcentagem
			from ctr_plc_lancamentos_rcr crl
			left join centro_resp cr on crl.centro_resp_id = cr.id
			left join plano_contas pc on crl.plano_contas_id = pc.id
			where crl.lancamento_rcr_id = ".$array_dados['lancamento_id']." and (crl.centro_resp_id <> 0 || crl.plano_contas_id <> 0)";

		$array_ctr_plc_lancamentos = $db->fetch_all_array($query_ctr_plc_lancamentos);

		$ctr_plc_lancamentos = "";

		foreach($array_ctr_plc_lancamentos as $lancamento){
			$valor = number_format($lancamento['valor'],2,',','.');
			$porcentagem = $lancamento['porcentagem'] * 100;
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

function lancamentosListar($db,$params){
/*
	$filtro = array(
		"dt_ativo" =>'mes',
		"dt_ini" => "01/05/2015",
		"dt_fim" => "31/05/2015",
		"tp_venc" =>"'av','v'",
		"conta_id" =>"1,2,20,21",
		"tp_lnct" =>"'R','P','T'",
		"valor" =>"",
		"centro_resp_id" =>"",
		"plano_contas_id" =>"",		
		"favorecido_id" =>"",
		"documento_id" =>"",
		"forma_pgto_id" =>"",
		"parcelado" => true
	);
*/

	//comentar este bloco caso queira utilizar os parâmetros de teste
	$filtro = $params['filtro'];
	$filtro = str_replace('\"','"',$filtro);
	$filtro = str_replace("\'","'",$filtro);
	$filtro = json_decode($filtro, true);

	//filtro
	$array_filtro_rp = array();
	$array_filtro_t = array();
	$array_filtro_rcr = array();
	$exc_trans = false;
	$exc_rcr = false;

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

	//descrição
	//$descricao = $filtro['descricao'];
	//if(!$descricao==''){
		//$descricao = trim($descricao,' ');
		//$descricao = str_replace(' ','%',$descricao);
		//$array_filtro_rp[] = 'descricao like "%'.$descricao.'%"';
		//$array_filtro_t[] = 'descricao like "%'.$descricao.'%"';
	//}

	//contas financeiras
	$conta_id_origem = "";
	$conta_id_estino = "";
	if($filtro["conta_id"]!=''){
		$fltr_cf = explode(',',$filtro["conta_id"]);
		$array_filtro_rp[] = 'conta_id in ('.$filtro["conta_id"].')';
		$array_filtro_rcr[] = 'conta_id in ('.$filtro["conta_id"].')';
		$conta_id_origem = "conta_id_origem in (".$filtro["conta_id"].")";
		$conta_id_destino = "conta_id_destino in (".$filtro["conta_id"].")";
	}

	//favorecido
	if($filtro["favorecido_id"]){
		$array_filtro_rp[] = 'favorecido_id = '.$filtro["favorecido_id"];
		$array_filtro_rcr[] = 'favorecido_id = '.$filtro["favorecido_id"];
		$exc_trans = true;
	}

	//valor
	$valor = $filtro["valor"];
	if($valor!='' && $valor!='0,00'){
		$valor = $db->valorToDouble($valor);
		$array_filtro_rp[] = 'valor = '.$valor;
		$array_filtro_t[] = 'valor = '.$valor;
		$array_filtro_rcr[] = 'valor = '.$valor;
	}

	//documento
	if($filtro["documento_id"]!=""){
		$array_filtro_rp[] = 'documento_id = '.$filtro["documento_id"];
		$array_filtro_rcr[] = 'documento_id = '.$filtro["documento_id"];
		$exc_trans = true;
	}

	//forma de pagamento
	if($filtro["forma_pgto_id"]!=""){
		$array_filtro_rp[] = 'forma_pgto_id = '.$filtro["forma_pgto_id"];
		$array_filtro_rcr[] = 'forma_pgto_id = '.$filtro["forma_pgto_id"];
		$exc_trans = true;
	}

	//tipo de lançamento
	//$filtro["tp_lnct"] = "'R','P'";
	//$filtro["tp_lnct"] = "'T'";
	if($filtro["tp_lnct"]!=''){
		$flt_tp_lnct = str_replace("\'","'",$filtro["tp_lnct"]);
		$flt_tp_lnct = explode(',',$flt_tp_lnct);
		$flt_rcbt = in_array("'R'",$flt_tp_lnct);
		$flt_pgto = in_array("'P'",$flt_tp_lnct);
		$flt_trans = in_array("'T'",$flt_tp_lnct);
		if($flt_trans){
			$flt_trans_pos = array_search("'T'",$flt_tp_lnct);
			unset($flt_tp_lnct[$flt_trans_pos]);
		}
		$flt_tp_lnct = join(',',$flt_tp_lnct);
		$array_filtro_rp[] = 'tipo in ('.$flt_tp_lnct.')';
		$array_filtro_rcr[] = 'tipo in ('.$flt_tp_lnct.')';
		if($flt_tp_lnct=='' && $flt_trans)
			$exc_rcr = true;
	}

	//quantidade de parcelas
	$bool_parcelado = filter_var($filtro["parcelado"], FILTER_VALIDATE_BOOLEAN);
	if($bool_parcelado){
		$array_filtro_rp[] = 'qtd_parcelas > 1';
		$exc_trans = true;
		$exc_rcr = true;
	}
	
	//plano de contas
	$plano_contas_id = $filtro["plano_contas_id"];
	$bool_plc = false;
	if($plano_contas_id>0){
		$bool_plc = true;
		$exc_trans = true;
	}

	//centro de responsabilidade
	$centro_resp_id = $filtro["centro_resp_id"];
	$bool_ctr = false;
	if($centro_resp_id>0){
		$bool_ctr = true;
		$exc_trans = true;
	}

	//compensado
	$bool_compensado = filter_var($filtro["compensado"], FILTER_VALIDATE_BOOLEAN);
	$bool_aberto = filter_var($filtro["aberto"], FILTER_VALIDATE_BOOLEAN);
	if( ($bool_compensado && !$bool_aberto) || (!$bool_compensado && $bool_aberto) ){
		if($bool_compensado){
			$compensado = 'compensado = 1';
			$exc_rcr = true;
		}elseif($bool_aberto)
			$compensado = 'compensado = 0';
		$array_filtro_rp[] = $compensado;
		$array_filtro_t[] = $compensado;
	}

	//monta where para recebimentos e pagamentos
	//$filtro_query = $array_filtro[0];
	//array_shift($array_filtro);
	if(count($array_filtro_rp)>0){
		$filtro_query_rp = "";
		foreach($array_filtro_rp as $filtro_rp){
			$filtro_query_rp .= ' and '.$filtro_rp;
		}
		$filtro_query_rp .=	' and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"';
	}
	
	//monta where para transferências
	if(count($array_filtro_t)>0){
		$filtro_query_t = "";
		foreach($array_filtro_t as $filtro_t){
			$filtro_query_t .= ' and '.$filtro_t;
		}
		//$filtro_query_t .= ' and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"';
	}
	//fim do filtro

	//monta where dos lançamentos recorrentes
	if(count($array_filtro_rcr)>0){
		$filtro_query_rcr = "";
		foreach($array_filtro_rcr as $filtro){
			$filtro_query_rcr .= ' and '.$filtro;
		}
		$filtro_query_rcr .= ' and dt_vencimento <= "'.$dt_fim.'"';
	}
	//fim do filtro

	//monta a lista de lançamentos recorrentes
	if(!$exc_rcr){
		$db->query("
			CREATE TEMPORARY TABLE lancamentos_recorrentes_temp (
				id int(11),
				tipo char(1),
				dt_vencimento date NOT NULL,
				descricao varchar(255),
				favorecido_id int(1),
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
			where 1=1 '.$filtro_query_rcr;

		$array_lancamentos_rcr = $db->fetch_all_array($query_lnct_rcr);
		
		foreach($array_lancamentos_rcr as $lancamento){

			$lancamento_rcr = $db->fetch_assoc("select id, tipo, dt_vencimento, favorecido_id, descricao, valor, frequencia, qtd_dias, dia_mes, 1 recorrente from lancamentos_recorrentes where id = ".$lancamento['id']);
			
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

	$query_lancamentos = "";

	if( $filtro_query_rp=="" && $filtro_query_t=="" ){ //filtro vazio só precisa de restrição para data

		$query_lancamentos = '
		  (select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, valor, conta_id_origem, conta_id_destino, compensado, 0 recorrente
			from lancamentos
			where dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'")
			 
			union
		
			(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, valor, 0 as conta_id_origem, 0 as conta_id_destino, 0 compensado, 1 recorrente
			from lancamentos_recorrentes_temp)

			order by dt_vencimento
		';

	}else{

		if( $flt_rcbt || $flt_pgto || $filtro["tp_lnct"]==''  ){ //recebimentos ou pagamentos
			$query_lancamentos = '
				(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, valor, conta_id_origem, conta_id_destino, compensado, 0 recorrente
				 from lancamentos
				 where 1=1 '.$filtro_query_rp.')
			';
		}


		if(!$exc_rcr){

			if($query_lancamentos!=""){
				$query_lancamentos .= " union ";
			}

			$query_lancamentos .= '
			(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, valor, 0 as conta_id_origem, 0 as conta_id_destino, 0 compensado, 1 recorrente
			from lancamentos_recorrentes_temp)
			';

		}

		if( !$exc_trans && ($flt_trans || $filtro["tp_lnct"]=='') ){ //transferências
		
			if($query_lancamentos!=""){
				$query_lancamentos .= " union ";
			}
			
			if(!$fltr_cf){ //transferências de todas as contas

				$query_lancamentos .= '
					(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, valor, conta_id_origem, conta_id_destino, compensado, 0 recorrente
					from lancamentos
					where tipo = "T" 
						and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"
					'.$filtro_query_t.')
				';
				
			}else{ //transferências das contas que estão no filtro

				$query_lancamentos .= '
					(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, valor, conta_id_origem, conta_id_destino, compensado, 0 recorrente
					from lancamentos
					where tipo = "T"
					  and ('.$conta_id_origem.' or '.$conta_id_destino.')
						and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"
						'.$filtro_query_t.')';

			}
			
		}
		
		$query_lancamentos .= ' order by dt_vencimento';
		
	}

	$lancamentos_listar = "";
	
	if($query_lancamentos!=""){
		$array_lancamentos = $db->fetch_all_array($query_lancamentos);
		foreach($array_lancamentos as $lancamento){	

			$tp_lnct = $lancamento['tipo'];
			
			//Busca o nome do Favorecido
			$nome_favorecido = $db->fetch_assoc("select nome from favorecidos where id = ".$lancamento['favorecido_id']);
			
			// ============ data ============
			$dt_compensar = explode("/", $lancamento['dt_vencimento']);
			$dia = $dt_compensar[0];
			$m = $dt_compensar[1];
			if($m == '01'){ $mes = 'Jan';}
			elseif($m == '02'){ $mes = 'Fev';}
			elseif($m == '03'){ $mes = 'Mar';}
			elseif($m == '04'){ $mes = 'Abr';}
			elseif($m == '05'){ $mes = 'Mai';}
			elseif($m == '06'){ $mes = 'Jun';}
			elseif($m == '07'){ $mes = 'Jul';}
			elseif($m == '08'){ $mes = 'Ago';}
			elseif($m == '09'){ $mes = 'Set';}
			elseif($m == '10'){ $mes = 'Out';}
			elseif($m == '11'){ $mes = 'Nov';}
			else{ $mes = 'Dez';}
			$ano = substr($dt_compensar[2], -2);
			
			// ==============================

			// Verifica se a data esta atrasada
		 	$atrasado = '';
			if($lancamento['compensado']==0){
				$data = explode('/',$lancamento['dt_vencimento']);
				$dt_limite = mktime(0,0,0,$data[1],$data[0],$data[2]);
				$hoje = strtotime(date('Y-m-d'));
				//$atraso = $hoje - $dt_limite;
				//$atraso = date('d',$atraso);
				//$atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
				if($hoje > $dt_limite){
					//$dt_vencimento .= "</b> <font class='subTexto red'>  <br> Atrasado ".$atraso." dia(s) </font>";
					$atrasado = 'red';
				}
			}
			//transferência de entrada em contrapartida da transferência de saída
			$trans_contrapartida = "";

			//verifica se o plano de contas e o centro de resposabilidade foram filtrados
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

				$opcoes = "";
				if($lancamento['recorrente']==0){
					if($tp_lnct=="R"){
						$class_excluir = 'recebimentosExcluir';
						$form_id = 'form_rcbt';
						$dialog_id = 'dialog-rcbt';
						if($lancamento['compensado']==0)
							$link_boleto = '<a href="javascript://void(0);" original-title="Boleto" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',2,\''.$form_id.'\',\''.$dialog_id.'\',\'boleto\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].')"><img src="images/icons/dark/barCod.png" width="10"></a>';
						else
							$link_boleto = '';
						$cor_valor = 'blue';
					}elseif($tp_lnct=="P"){
						$class_excluir = 'pagamentosExcluir';
						$link_boleto = '';
						$cor_valor = 'red';
						$form_id = 'form_pgto';
						$dialog_id = 'dialog-pgto';
					}else{
						$classe_excluir = "transferenciasExcluir";
						$link_boleto = '';
						$cor_valor = 'red';
						$form_id = 'form_trsf';
						$dialog_id = 'dialog-trsf';
					}
				}else{
					if($tp_lnct=="R"){
						$class_excluir = 'recebimentosRcrExcluir';
						$form_id = 'form_rcbt';
						$dialog_id = 'dialog-rcbt';
						$link_boleto = '<a href="javascript://void(0);" original-title="Boleto" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',2,\''.$form_id.'\',\''.$dialog_id.'\',\'boleto\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].')"><img src="images/icons/dark/barCod.png" width="10"></a>';
						$cor_valor = 'blue';
					}else{
						$class_excluir = 'pagamentosRcrExcluir';
						$link_boleto = '';
						$cor_valor = 'red';
						$form_id = 'form_pgto';
						$dialog_id = 'dialog-pgto';
					}
				}

				$opcoes = '
					<a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS '.$class_excluir.'" onClick="alertaExcluir('.$lancamento['id'].',\''.$tp_lnct.'\','.$lancamento['recorrente'].','.$lancamento['compensado'].');"><img src="images/icons/light/close.png" width="10"></a>
					<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].',\'\',\''.$form_id.'\',\''.$dialog_id.'\',\'edit\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].')"><img src="images/icons/light/pencil.png" width="10"></a>
				';
				if($lancamento['compensado']==0)
					$opcoes .= '<a href="javascript://void(0);" original-title="Quitar" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',1,\''.$form_id.'\',\''.$dialog_id.'\',\'qtr\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].')"><img src="images/icons/dark/check.png" width="10"></a>';
				$opcoes .= $link_boleto;

				if($tp_lnct=="T"){
					
					if($fltr_cf){
						$cf_lnct_org = in_array($lancamento['conta_id_origem'],$fltr_cf);
						$cf_lnct_desti = in_array($lancamento['conta_id_destino'],$fltr_cf);
					}
					
					//verifica se as contas financeiras foram filtradas
						//se não houver filtro de conta financeira a conta de origem e destino estarão na lista e precisará haver contrapartida
						//se as contas financeiras de origem e destino estiverem no filtro haverá contrapartida
					if( !$fltr_cf || ($cf_lnct_org && $cf_lnct_desti) ){

						$trans_contrapartida = '
							<tr class="gradeA">
								<td style="display:none;">'.$lancamento['dt_ordem'].'</td>
								<td>
							
								<div class="updates">
      					<div class="newUpdate">

										<div class="uDate tbWF tipS" original-title="Vencimento" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
											<span class="lDespesa tbWF" >
												<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
													<span original-title="Favorecido" class="tipN">'.$nome_favorecido['nome'].'</span>
											</span>											
															
											<div class="tbWFoption" id="link_excluir_'.$lancamento['id'].'">
												'.$opcoes.'
											</div>
																																													
										<div class="tbWFvalue red">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
						
									</div>
								</div>
				
							</td> 
							</tr>
						';
						
						$cor_valor = 'blue';

					}else{ //não há contrapartida e a cor do lançamento deve ser definida
					
						($cf_lnct_org)? $cor_valor = 'red' : $cor_valor = 'blue';
						
					}
					
				}

				$lancamentos_listar .= '
					<tr class="gradeA" id="tbl-lnct-row-'.$lancamento['id'].'">
							<td style="display:none;">'.$lancamento['dt_ordem'].'</td>
							<td class="updates newUpdate">

										<div class="uDate tbWF tipS" original-title="Compensação" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
											<span class="lDespesa tbWF" >
												<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
													<span original-title="Favorecido" class="tipN">'.$nome_favorecido['nome'].'</span>
											</span>											
															
										<div class="tbWFoption">
											'.$opcoes.'
										</div>
																																													
										<div class="tbWFvalue '.$cor_valor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
				
							</td> 
						</tr>
				'.$trans_contrapartida;
			}
		}
	}

	$lancamentos_listar = '
		<table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos">
		<thead>
		<tr style="border-bottom: 1px solid #e7e7e7;">
						<th style="display:none;">Ordem</th>
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
DATA TABLE AJAX
===========================================================================================
*/

function DataTableAjax($db,$params){
    
    //filtro do data table
    $sSearch = $params["sSearch"];
    $sEcho = $params["sEcho"];
    $iDisplayStart = $params["iDisplayStart"];
    $iDisplayLength = $params["iDisplayLength"];
    //$iTotalRecords = $db->numRows('select id from lancamentos');
    $iTotalDisplayRecords = 0;

    //----------------------------------------------------------------
    //comentar este bloco caso queira utilizar os parâmetros de teste
	
    $filtro = $params['filtro'];
	$filtro = str_replace('\"','"',$filtro);
	$filtro = str_replace("\'","'",$filtro);
	$filtro = json_decode($filtro, true);
    
    //----------------------------------------------------------------

	//filtro
	$array_filtro_rp = array();
	$array_filtro_t = array();
	$array_filtro_rcr = array();
	$exc_trans = false;
	$exc_rcr = false;

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
            $dt_ini = $dt_mes[1].'-'.$dt_mes[0].'-01';
            $dt_fim = mktime(0,0,0,$dt_mes[0]+1,'00',$dt_mes[1]);
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

    //descrição
    //$descricao = $filtro['descricao'];
    //if(!$descricao==''){
    //$descricao = trim($descricao,' ');
    //$descricao = str_replace(' ','%',$descricao);
    //$array_filtro_rp[] = 'descricao like "%'.$descricao.'%"';
    //$array_filtro_t[] = 'descricao like "%'.$descricao.'%"';
    //}

    //contas financeiras
    $conta_id_origem = "";
    if($filtro["conta_id"]!=''){
        $fltr_cf = explode(',',$filtro["conta_id"]);
        $array_filtro_rp[] = 'conta_id in ('.$filtro["conta_id"].')';
        $array_filtro_rcr[] = 'conta_id in ('.$filtro["conta_id"].')';
        $conta_id_origem = "conta_id_origem in (".$filtro["conta_id"].")";
        $conta_id_destino = "conta_id_destino in (".$filtro["conta_id"].")";
    }

    //favorecido
    if($filtro["favorecido_id"]){
        $array_filtro_rp[] = 'favorecido_id = '.$filtro["favorecido_id"];
        $array_filtro_rcr[] = 'favorecido_id = '.$filtro["favorecido_id"];
        $exc_trans = true;
    }

    //valor
    $valor = $filtro["valor"];
    if($valor!='' && $valor!='0,00'){
        $valor = $db->valorToDouble($valor);
        $array_filtro_rp[] = 'valor = '.$valor;
        $array_filtro_t[] = 'valor = '.$valor;
        $array_filtro_rcr[] = 'valor = '.$valor;
    }

    //documento
    if($filtro["documento_id"]!=""){
        $array_filtro_rp[] = 'documento_id = '.$filtro["documento_id"];
        $array_filtro_rcr[] = 'documento_id = '.$filtro["documento_id"];
        $exc_trans = true;
    }

    //forma de pagamento
    if($filtro["forma_pgto_id"]!=""){
        $array_filtro_rp[] = 'forma_pgto_id = '.$filtro["forma_pgto_id"];
        $array_filtro_rcr[] = 'forma_pgto_id = '.$filtro["forma_pgto_id"];
        $exc_trans = true;
    }

    //tipo de lançamento
    //$filtro["tp_lnct"] = "'R','P'";
    //$filtro["tp_lnct"] = "'T'";
    if($filtro["tp_lnct"]!=''){
        $flt_tp_lnct = str_replace("\'","'",$filtro["tp_lnct"]);
        $flt_tp_lnct = explode(',',$flt_tp_lnct);
        $flt_rcbt = in_array("'R'",$flt_tp_lnct);
        $flt_pgto = in_array("'P'",$flt_tp_lnct);
        $flt_trans = in_array("'T'",$flt_tp_lnct);
        if($flt_trans){
            $flt_trans_pos = array_search("'T'",$flt_tp_lnct);
            unset($flt_tp_lnct[$flt_trans_pos]);
        }
        $flt_tp_lnct = join(',',$flt_tp_lnct);
        $array_filtro_rp[] = 'tipo in ('.$flt_tp_lnct.')';
        $array_filtro_rcr[] = 'tipo in ('.$flt_tp_lnct.')';
        if($flt_tp_lnct=='' && $flt_trans)
            $exc_rcr = true;
    }

    //quantidade de parcelas
    $bool_parcelado = filter_var($filtro["parcelado"], FILTER_VALIDATE_BOOLEAN);
    if($bool_parcelado){
        $array_filtro_rp[] = 'qtd_parcelas > 1';
        $exc_trans = true;
        $exc_rcr = true;
    }
    
    //plano de contas
    $plano_contas_id = $filtro["plano_contas_id"];
    $bool_plc = false;
    if($plano_contas_id>0){
        $bool_plc = true;
        $exc_trans = true;
    }

    //centro de responsabilidade
    $centro_resp_id = $filtro["centro_resp_id"];
    $bool_ctr = false;
    if($centro_resp_id>0){
        $bool_ctr = true;
        $exc_trans = true;
    }

    //compensado
    $bool_compensado = filter_var($filtro["compensado"], FILTER_VALIDATE_BOOLEAN);
    $bool_aberto = filter_var($filtro["aberto"], FILTER_VALIDATE_BOOLEAN);
    if( ($bool_compensado && !$bool_aberto) || (!$bool_compensado && $bool_aberto) ){
        if($bool_compensado){
            $compensado = 'compensado = 1';
            $exc_rcr = true;
        }elseif($bool_aberto)
            $compensado = 'compensado = 0';
        $array_filtro_rp[] = $compensado;
        $array_filtro_t[] = $compensado;
    }

    //nosso número
    $nossoNumero = $filtro["nosso_numero"];
    if($nossoNumero!=""){
        $exc_trans = true;
        $exc_rcr = true;
    }

    //localizar
    if($sSearch!=''){
        $search = 'descricao like "%'.$sSearch.'%"';
        $array_filtro_rp[] = $search;
        $array_filtro_t[] = $search;
        $array_filtro_rcr[] = $search;
    }

    //monta where para recebimentos e pagamentos
    //$filtro_query = $array_filtro[0];
    //array_shift($array_filtro);
    if(count($array_filtro_rp)>0){
        $filtro_query_rp = join(' and ', $array_filtro_rp);
        //$filtro_query_rp = "";
        //foreach($array_filtro_rp as $filtro_rp){
        //$filtro_query_rp .= ' and '.$filtro_rp;
        //}
        $filtro_query_rp .=	' and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"';
    }
    
    //monta where para transferências
    if(count($array_filtro_t)>0){
        $filtro_query_t = ' and '.join(' and ', $array_filtro_t);
        //$filtro_query_t = "";
        //foreach($array_filtro_t as $filtro_t){
        //$filtro_query_t .= ' and '.$filtro_t;
        //}
        //$filtro_query_t .= ' and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"';
    }

    //monta where dos lançamentos recorrentes
    if(count($array_filtro_rcr)>0){
        $filtro_query_rcr = ' and '.join(' and ', $array_filtro_rcr);
        //$filtro_query_rcr = "";
        //foreach($array_filtro_rcr as $filtro){
        //$filtro_query_rcr .= ' and '.$filtro;
        //}
        //$filtro_query_rcr .= ' and dt_vencimento <= "'.$dt_fim.'"';
    }
    //fim do filtro

    //monta a lista de lançamentos recorrentes
    if(!$exc_rcr){
        $db->query("
			CREATE TEMPORARY TABLE lancamentos_recorrentes_temp (
				id int(11),
				tipo char(1),
				dt_vencimento date NOT NULL,
				descricao varchar(255),
                lancamento_pai_id int(10),
				favorecido_id int(1),
				valor decimal(10,2) NOT NULL,
				frequencia int(3),
				dia_mes int(1),
				qtd_dias smallint(6),
				recorrente int(1),
                conta_id int(11)
			) ENGINE=MEMORY
		");

        //para lançamento recorrentes é usada apenas dt_fim como referencia, pois quando a busca é específica por mês, o lançamento não é retornado
        $query_lnct_rcr = '
			select id 
			from lancamentos_recorrentes 
			where dt_vencimento <= "'.$dt_fim.'"'.$filtro_query_rcr;

        $array_lancamentos_rcr = $db->fetch_all_array($query_lnct_rcr);
        
        foreach($array_lancamentos_rcr as $lancamento){

            $lancamento_rcr = $db->fetch_assoc("select id, tipo, dt_vencimento, favorecido_id, descricao, valor, frequencia, qtd_dias, dia_mes, 1 recorrente from lancamentos_recorrentes where id = ".$lancamento['id']);
            
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

    $joinCtr = '';
    $joinPlc = '';
    $joinCtrRcr = '';
    $joinPlcRcr = '';
    if($bool_ctr){
        $joinCtr = ' join ctr_plc_lancamentos cpl1 on l.id = cpl1.lancamento_id and cpl1.centro_resp_id = '.$centro_resp_id;
        $joinCtrRcr = ' join ctr_plc_lancamentos_rcr cpl1 on l.id = cpl1.lancamento_rcr_id and cpl1.centro_resp_id = '.$centro_resp_id;
    }
    if($bool_plc){
        $joinPlc .= ' join ctr_plc_lancamentos cpl2 on l.id = cpl2.lancamento_id and cpl2.plano_contas_id = '.$plano_contas_id;
        $joinPlcRcr .= ' join ctr_plc_lancamentos_rcr cpl2 on l.id = cpl2.lancamento_rcr_id and cpl2.centro_resp_id = '.$plano_contas_id;
    }

    $query_lancamentos = "";

    if($nossoNumero!=''){ //filtro com nosso número deve retornar exatamente o recebimento correspondente
    
        $query_lancamentos = '
		    (select distinct l.dt_vencimento dt_ordem, l.id, l.tipo, date_format(l.dt_vencimento, "%d/%m/%Y") dt_vencimento, l.favorecido_id, l.descricao, l.lancamento_pai_id, l.valor, l.conta_id_origem, l.conta_id_destino, conta_id, l.compensado, 0 recorrente, dt_compensacao
			from lancamentos l
            join boletos b on l.id = b.lancamento_id 
            where b.nosso_numero = "'.$nossoNumero.'")';

    }elseif( $filtro_query_rp=="" && $filtro_query_t=="" ){ //filtro vazio só precisa de restrição para data

        $query_lancamentos = '
		    (select distinct l.dt_vencimento dt_ordem, l.id, l.tipo, date_format(l.dt_vencimento, "%d/%m/%Y") dt_vencimento, l.favorecido_id, l.descricao, l.lancamento_pai_id, l.valor, l.conta_id_origem, l.conta_id_destino, conta_id, l.compensado, 0 recorrente, dt_compensacao
			from lancamentos l
            '.$joinCtr.'
            '.$joinPlc.'
			where dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'")
			 
			union
		
			(select distinct l.dt_vencimento dt_ordem, l.id, l.tipo, date_format(l.dt_vencimento, "%d/%m/%Y") dt_vencimento, l.favorecido_id, l.descricao, l.lancamento_pai_id, l.valor, 0 as conta_id_origem, 0 as conta_id_destino, conta_id, 0 compensado, 1 recorrente, "" dt_compensacao
			from lancamentos_recorrentes_temp l
            '.$joinCtrRcr.'
            '.$joinPlcRcr.')';

    }else{ //filtro preenchido

        if( $flt_rcbt || $flt_pgto || $filtro["tp_lnct"]==''  ){ //recebimentos ou pagamentos
            $query_lancamentos = '
				(select distinct l.dt_vencimento dt_ordem, l.id, l.tipo, date_format(l.dt_vencimento, "%d/%m/%Y") dt_vencimento, l.favorecido_id, l.descricao, l.lancamento_pai_id, l.valor, l.conta_id_origem, l.conta_id_destino, conta_id, l.compensado, 0 recorrente, dt_compensacao
				 from lancamentos l
                 '.$joinCtr.'
                 '.$joinPlc.'
				 where '.$filtro_query_rp.')
			';
        }

        if(!$exc_rcr){ //recebimentos ou pagamentos recorrentes

            if($query_lancamentos!=""){
                $query_lancamentos .= " union ";
            }

            $query_lancamentos .= '
			    (select distinct l.dt_vencimento dt_ordem, l.id, l.tipo, date_format(l.dt_vencimento, "%d/%m/%Y") dt_vencimento, l.favorecido_id, l.descricao, l.lancamento_pai_id, l.valor, 0 as conta_id_origem, 0 as conta_id_destino, conta_id, 0 compensado, 1 recorrente, "" dt_compensacao
			    from lancamentos_recorrentes_temp l
                '.$joinCtrRcr.'
                '.$joinPlcRcr.')';

        }

        if( !$exc_trans && ($flt_trans || $filtro["tp_lnct"]=='') ){ //transferências
            
            if($query_lancamentos!=""){
                $query_lancamentos .= " union ";
            }
            
            if(!$fltr_cf){ //transferências de todas as contas

                $query_lancamentos .= '
					(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, lancamento_pai_id, valor, conta_id_origem, conta_id_destino, 0 conta_id, compensado, 0 recorrente, dt_compensacao
					from lancamentos
					where tipo = "T" 
						and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"
					    '.$filtro_query_t.')
				';
                
            }else{ //transferências das contas que estão no filtro

                $query_lancamentos .= '
					(select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, "%d/%m/%Y") dt_vencimento, favorecido_id, descricao, lancamento_pai_id, valor, conta_id_origem, conta_id_destino, 0 conta_id, compensado, 0 recorrente, dt_compensacao
					from lancamentos
					where tipo = "T"
					  and ('.$conta_id_origem.' or '.$conta_id_destino.')
					  and dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"
					  '.$filtro_query_t.')';

            }
            
        }
        
    }

    $iTotalDisplayRecords = $db->numRows('select id from ('.$query_lancamentos.') as lancamentos');

    /*
     * a variavel $query_lancamentos é utilizada 
     * para pesquisar os lançmentos para o cálculo do carnê leão, 
     * por isso, não deve se limitar à quantidade de registros exibida no data table, 
     * deve pegar os lançamentos de todo o mês
    */
    //$query_lancamentos .= ' order by dt_ordem, id limit '.$iDisplayStart.','.$iDisplayLength;
    
    //echo $query_lancamentos.' <br><br>';
    //$query_lancamentos = '';

    //Busca lançamentos que serão exibidos
    $aaData = array();
    
    $array_lancamentos = array();

    if($query_lancamentos!=""){

        $array_lancamentos = $db->fetch_all_array($query_lancamentos.' order by dt_ordem, id limit '.$iDisplayStart.','.$iDisplayLength);

        foreach($array_lancamentos as $lancamento){	

            $tp_lnct = $lancamento['tipo'];
            
            //Busca o nome do Favorecido
            $nome_favorecido = $db->fetch_assoc("select nome from favorecidos where id = ".$lancamento['favorecido_id']);
            
            // ============ data ============
            $dt_compensar = explode("/", $lancamento['dt_vencimento']);
            $dia = $dt_compensar[0];
            $m = $dt_compensar[1];
            if($m == '01'){ $mes = 'Jan';}
            elseif($m == '02'){ $mes = 'Fev';}
            elseif($m == '03'){ $mes = 'Mar';}
            elseif($m == '04'){ $mes = 'Abr';}
            elseif($m == '05'){ $mes = 'Mai';}
            elseif($m == '06'){ $mes = 'Jun';}
            elseif($m == '07'){ $mes = 'Jul';}
            elseif($m == '08'){ $mes = 'Ago';}
            elseif($m == '09'){ $mes = 'Set';}
            elseif($m == '10'){ $mes = 'Out';}
            elseif($m == '11'){ $mes = 'Nov';}
            else{ $mes = 'Dez';}
            $ano = substr($dt_compensar[2], -2);
            
            // ==============================

            // Verifica se a data esta atrasada
            $atrasado = '';
            if($lancamento['compensado']==0){
                $data = explode('/',$lancamento['dt_vencimento']);
                $dt_limite = mktime(0,0,0,$data[1],$data[0],$data[2]);
                $hoje = strtotime(date('Y-m-d'));
                //$atraso = $hoje - $dt_limite;
                //$atraso = date('d',$atraso);
                //$atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
                if($hoje > $dt_limite){
                    //$dt_vencimento .= "</b> <font class='subTexto red'>  <br> Atrasado ".$atraso." dia(s) </font>";
                    $atrasado = 'red';
                }
            }

            $opcoes = "";
            if($lancamento['recorrente']==0){
                if($tp_lnct=="R"){
                    $class_excluir = 'recebimentosExcluir';
                    $form_id = 'form_rcbt';
                    $dialog_id = 'dialog-rcbt';
                    if($lancamento['compensado']==0)
                        $link_boleto = '<a href="javascript://void(0);" original-title="Boleto" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',2,\''.$form_id.'\',\''.$dialog_id.'\',\'boleto\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].')"><img src="images/icons/dark/barCod.png" width="10"></a>';
                    else
                        $link_boleto = '';
                    $cor_valor = 'blue';
                }elseif($tp_lnct=="P"){
                    $class_excluir = 'pagamentosExcluir';
                    $link_boleto = '';
                    $cor_valor = 'red';
                    $form_id = 'form_pgto';
                    $dialog_id = 'dialog-pgto';
                }else{
                    $classe_excluir = "transferenciasExcluir";
                    $link_boleto = '';
                    $cor_valor = 'red';
                    $form_id = 'form_trsf';
                    $dialog_id = 'dialog-trsf';
                }
            }else{
                if($tp_lnct=="R"){
                    $class_excluir = 'recebimentosRcrExcluir';
                    $form_id = 'form_rcbt';
                    $dialog_id = 'dialog-rcbt';
                    $link_boleto = '<a href="javascript://void(0);" original-title="Boleto" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',2,\''.$form_id.'\',\''.$dialog_id.'\',\'boleto\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].',\''.$lancamento['dt_ordem'].'\')"><img src="images/icons/dark/barCod.png" width="10"></a>';
                    $cor_valor = 'blue';
                }else{
                    $class_excluir = 'pagamentosRcrExcluir';
                    $link_boleto = '';
                    $cor_valor = 'red';
                    $form_id = 'form_pgto';
                    $dialog_id = 'dialog-pgto';
                }
            }

            //Verifica se existe o lançamento PAI, se for 0 é porque não tem lançamento PAI
            if($lancamento['lancamento_pai_id'] != ""){ $lancPaiID = $lancamento['lancamento_pai_id']; }else{ $lancPaiID = "0"; }
            
            $opcoes = '
				<a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS '.$class_excluir.'" id="link-exc-'.$lancamento['id'].'" onClick="alertaExcluir('.$lancamento['id'].',\''.$tp_lnct.'\','.$lancamento['recorrente'].','.$lancamento['compensado'].','.$lancPaiID.');"><img src="images/icons/light/close.png" width="10"></a>
				<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].',\'\',\''.$form_id.'\',\''.$dialog_id.'\',\'edit\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].',\''.$lancamento['dt_ordem'].'\')"><img src="images/icons/light/pencil.png" width="10"></a>
			';
            if($lancamento['compensado']==0)
                $opcoes .= '<a href="javascript://void(0);" original-title="Quitar" class="smallButton btTBwf tipN" onClick="lancamentosExibir('.$lancamento['id'].',1,\''.$form_id.'\',\''.$dialog_id.'\',\'qtr\','.$lancamento['recorrente'].',\''.$tp_lnct.'\','.$lancamento['compensado'].',\''.$lancamento['dt_ordem'].'\')"><img src="images/icons/dark/check.png" width="10"></a>';
            $opcoes .= $link_boleto;


            //transferência de entrada em contrapartida da transferência de saída
            $trans_contrapartida = "";

            if($tp_lnct=="T"){
                
                if($fltr_cf){
                    $cf_lnct_org = in_array($lancamento['conta_id_origem'],$fltr_cf);
                    $cf_lnct_desti = in_array($lancamento['conta_id_destino'],$fltr_cf);
                }
                
                //verifica se as contas financeiras foram filtradas
                //se não houver filtro de conta financeira a conta de origem e destino estarão na lista e precisará haver contrapartida
                //se as contas financeiras de origem e destino estiverem no filtro haverá contrapartida
                if( !$fltr_cf || ($cf_lnct_org && $cf_lnct_desti) ){

                    $trans_contrapartida = '
						<div class="uDate tbWF tipS" original-title="Vencimento" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
						<span class="lDespesa tbWF" >
							<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
								<span original-title="Favorecido" class="tipN">'.$nome_favorecido['nome'].'</span>
						</span>											
															
						<div class="tbWFoption" id="link_excluir_'.$lancamento['id'].'">
							'.$opcoes.'
						</div>
																																													
						<div class="tbWFvalue red">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
					';
                    
                    $cor_valor = 'blue';

                }else{ //não há contrapartida e a cor do lançamento deve ser definida
                    
                    ($cf_lnct_org)? $cor_valor = 'red' : $cor_valor = 'blue';
                    
                }
                
            }

            $dadosLnct = '
				<div class="uDate tbWF tipS" original-title="Compensação" align="center"> <span class="uDay '.$atrasado.'">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
				<span class="lDespesa tbWF" >
					<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
						<span original-title="Favorecido" class="tipN">'.$nome_favorecido['nome'].'</span>
				</span>											
															
				<div class="tbWFoption">
					'.$opcoes.'
				</div>
																																													
				<div class="tbWFvalue '.$cor_valor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
			';

            array_push($aaData,array('lancamento'=>$dadosLnct));

            if($trans_contrapartida!='')
                array_push($aaData,array('lancamento'=>$trans_contrapartida));

        }
    }

    if($_SESSION['carne_leao']==1)
        $imposto = self::CalcularImposto($db,$query_lancamentos,$dt_ini,$dt_fim);
    else
        $imposto = array(
                    'recebimentosPf' => '0,00',
                    'recebimentosPj' => '0,00',
                    'recebimentos' => '0,00',
                    'pagamentos' => '0,00',
                    'deducoes' => '0,00',
                    'naoDedutivel' => '0,00',
                    'base' => '0,00',
                    'imposto' => '0,00',
                );
    
    /*
    $imposto = array(
                'recebimentos' => '1.000,00',
                'pagamentos' => '100,00',
                'deducoes' => '75,00',
                'naoDedutivel' => '25,00',
                'base' => '925,00',
                'imposto' => '0,00'
            );
      */      

    $retorno = array('carne_leao'=>$_SESSION['carne_leao'],'imposto'=>$imposto,'sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
    
    return json_encode($retorno);

}

/*
===========================================================================================
LISTAR SALDO DAS CONTAS
===========================================================================================
*/

function contasSaldoListar($db,&$sessao=false){ 
	$saldo_total = 0;
    $saldo = 0;
    $credito = 0;
	$array_contas = $db->fetch_all_array("
		select c.id, b.nome, b.logo, c.descricao, c.vl_saldo, c.vl_credito
		from contas c
		left join bancos b on c.banco_id = b.id
		order by c.descricao
	");
	$contas_saldo = "";
	foreach($array_contas as $conta){
		
		 if(!empty($conta['logo'])){ $banco_logo = $conta['logo']; }else{ $banco_logo = "bank.png"; }
		
		$contas_saldo .= '
			<div class="userRow">
						<img src="images/bancos/'.$banco_logo.'" alt="" class="floatL">
						<ul class="leftList">
								<li><a href="javascript://void(0);" class="tipW"><strong>'.$conta['descricao'].'</strong> </a></li>
								<li style="font-size: 9px;">'.$conta['nome'].'</li>
						</ul>
						<div class="rightList"></div>
				</div>
			<div class="orderRow">
					<ul class="leftList">
							<li>Saldo:</li>
							<li>Crédito disponível:</li>
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
		$saldo_total += $conta['vl_saldo'] + $conta['vl_credito'];
        $saldo += $conta['vl_saldo'];
        $credito += $conta['vl_credito'];
	}
	if($sessao)
		$sessao = "R$ ".number_format($saldo_total,2,',','.');
	$contas = array("contas_saldo"=>$contas_saldo,"saldo_total"=>"R$ ".number_format($saldo_total,2,',','.'),"saldo"=>"R$ ".number_format($saldo,2,',','.'),"credito"=>"R$ ".number_format($credito,2,',','.'));
	return $contas;
}

/*
===========================================================================================
ATUALIZAR LANÇAMENTOS NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function ctrPlcLancamentosAtualizar($db,$lancamentos,$lancamento_id,$tp_lancamento,$valor_parcela){
	
    $qtd_lnct = 0;
    $qtd_lnct_excluido = 0;
    
    if($lancamentos!=''){

        $jsonTxt = str_replace('\"','"',$lancamentos);
        $jsonObj = json_decode($jsonTxt, true);
        $array_lancamentos = $jsonObj;
        //$query = $db->query("delete from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id);
        $qtd_lnct = count($array_lancamentos);
        $qtd_lnct_excluido = 0;
        
        if($qtd_lnct>0){

            foreach($array_lancamentos as $lancamento){

                //start:correção de bug que está incluindo valor e porcentagem maiores do que o limite
                if(bccomp($lancamento['porcentagem'], 100) == 1)
                    $lancamento['porcentagem'] = 100;
                //end:correção de bug que está incluindo valor e porcentagem maiores do que o limite

                if($lancamento["operacao"]=="1"){ //inclui um novo lançamento

                    $array_insert["lancamento_id"] = $lancamento_id;
                    $array_insert["centro_resp_id"] = $lancamento["centro_resp_id"];
                    $array_insert["plano_contas_id"] = $lancamento["plano_contas_id"];
                    $array_insert["tp_lancamento"] = $tp_lancamento;
                    
                    $porcentagem = $lancamento["porcentagem"];
                    $array_insert["porcentagem"] = $porcentagem / 100; //A porcentagem é na escala de 0 a 1

                    $array_insert["valor"] = ($porcentagem / 100) * $valor_parcela;
                    
                    $array_insert["situacao"] = 0;
                    $array_insert["dt_cadastro"] = date('Y-m-d');
                    $db->query_insert("ctr_plc_lancamentos",$array_insert);

                }elseif($lancamento["operacao"]=="2"){ //mantem o registro do lançamento ou edita futuramente
 /*                   
                    $porcentagem = $lancamento["porcentagem"];
                    $array_update = array();
                    $array_update["valor"] = ($porcentagem / 100) * $valor_parcela;
                    $array_update["porcentagem"] = $porcentagem / 100;
                    $db->query_update("ctr_plc_lancamentos",$array_update,'id = '.$lancamento["ctr_plc_lancamento_id"]);
*/

				$verif = $db->fetch_assoc('SELECT * FROM ctr_plc_lancamentos WHERE lancamento_id ='.$lancamento_id);

				if($verif <= 0)
				{
					$array_insert["lancamento_id"] = $lancamento_id;
					$array_insert["centro_resp_id"] = $lancamento["centro_resp_id"];
					$array_insert["plano_contas_id"] = $lancamento["plano_contas_id"];
					$array_insert["tp_lancamento"] = $tp_lancamento;
					
					$porcentagem = $lancamento["porcentagem"];
					$array_insert["porcentagem"] = $porcentagem / 100; //A porcentagem é na escala de 0 a 1

					$array_insert["valor"] = ($porcentagem / 100) * $valor_parcela;
					
					$array_insert["situacao"] = 0;
					$array_insert["dt_cadastro"] = date('Y-m-d');
					$db->query_insert("ctr_plc_lancamentos",$array_insert);
				}else{
					$porcentagem = $lancamento["porcentagem"];
					$array_update = array();
					$array_update["valor"] = ($porcentagem / 100) * $valor_parcela;
					$array_update["porcentagem"] = $porcentagem / 100;
					$db->query_update("ctr_plc_lancamentos",$array_update,'id = '.$lancamento["ctr_plc_lancamento_id"]);
				}



                }elseif($lancamento["operacao"]=="3"){ //exclui o lançamento
                    if($lancamento["ctr_plc_lancamento_id"]!=0){
                        $db->query("delete from ctr_plc_lancamentos where id = ".$lancamento["ctr_plc_lancamento_id"]);
                        $qtd_lnct_excluido += 1;
                    }else{
                        $qtd_lnct --;
                    }
                }
            }
        }
    }
    
    //Se não houver lançamentos para incluir na categoria e centro de custo, inclui no geral
    if( ($lancamentos == '') || (($qtd_lnct - $qtd_lnct_excluido)==0) ){
        
        //Verifica se já foi incluso valor na categoria e centro gerais
        $lnct_geral = $db->fetch_assoc("select id from ctr_plc_lancamentos where lancamento_id = ".$lancamento_id." and centro_resp_id = 0 and plano_contas_id = 0");
        
        //Inclui lançamento geral
        if(!$lnct_geral){
            $db->query_insert("ctr_plc_lancamentos",array(
                'lancamento_id' => $lancamento_id,
                'centro_resp_id' => 0,
                'plano_contas_id' => 0,
                'tp_lancamento' => $tp_lancamento,
                'porcentagem' => 1,
                'valor' => $valor_parcela,
                'dt_cadastro' => date("Y-m-d")
                ));
        }else{
            $db->query('update ctr_plc_lancamentos set valor = '.$valor_parcela.' where lancamento_id = '.$lancamento_id);
        }
        
    }else{
        $db->query('delete from ctr_plc_lancamentos where lancamento_id = '.$lancamento_id.' and centro_resp_id = 0 and plano_contas_id = 0');
    }
}

/*
===========================================================================================
CALCULAR DIFERENÇA ENTRE DATA DE VENCIMENTO E DATA DE COMPENSAÇÃO EM ESCALA DE MESES
===========================================================================================
*/

function dtCompetenciaDif($dt_vencimento=""){

	//separa o dia, mes e ano da data de vencimento
	if($dt_vencimento==""){
		$dt_vencimento = $this->lancamento_dados['dt_vencimento'];
	}
	$dt_vencimento = explode('-',$dt_vencimento);
	$dia = $dt_vencimento[2];
	$mes = $dt_vencimento[1];
	$ano = $dt_vencimento[0];
	
	//separa o mes e ano da data de competência
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

	return $mes_dif;

}

/*
===========================================================================================
CONSULTAR SOMA DO SALDO DAS CONTAS FINANCEIRAS
===========================================================================================
*/

	function getSaldoTotal($db){
		$saldo_total = $db->fetch_assoc("select sum(vl_saldo + vl_credito) saldo_total from contas");
		return $saldo_total['saldo_total'];
	}

/*
===========================================================================================
ATRIBUÍR SOMA DO SALDO DAS CONTAS FINANCEIRAS Á SESSÃO
===========================================================================================
*/

	function setSessionSaldoTotal($db,$saldo_total){
		$_SESSION['total_disponivel'] = 'R$ '.$db->valorFormat($saldo_total);
	}

/*
===========================================================================================
ATUALIZAR VENCIMENTO DE LANÇAMENTO RECORRENTE
===========================================================================================
*/

	function atualizarVencimentoRcr($db,$lnct_rcr_id){
		$lancamento_rcr = $db->fetch_assoc('select dt_vencimento, frequencia, dia_mes, qtd_dias from lancamentos_recorrentes where id = '.$lnct_rcr_id.' for update');
		$dt_vencimento = self::calculaProxVencRcr($lancamento_rcr);
		$db->query('update lancamentos_recorrentes set dt_vencimento = "'.$dt_vencimento.'", dt_prox_venc = "'.$dt_vencimento.'"  where id = '.$lnct_rcr_id);
	}

/*
===========================================================================================
CONVERTE LANÇAMENTO RECORRENTE PARA PROGRAMADO
===========================================================================================
*/
  
	function rcr_to_prog($db,$array_dados,$autoCompensar=false){

		$lnct_rcr_id = $array_dados['lancamento_id'];
		
		//inclui lançamento programado
		$this->lancamento_dados['lancamento_recorrente_id'] = $lnct_rcr_id;
		$lnct_prog_id = $db->query_insert('lancamentos',$this->lancamento_dados);

		//inclui valores no plano de contas e centro de responsabilidade
		//if($array_dados['ct_resp_lancamentos']!="")
			$this->ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lnct_prog_id,$array_dados['tipo'],$db->valorToDouble($this->lancamento_dados['valor']));

		self::atualizarVencimentoRcr($db,$lnct_rcr_id);
		
        $historico = $this->lancamento_dados;
        $historico['id'] = $lnct_prog_id;
        self::HistoricoLancamentos($db,array($historico),$autoCompensar);

		return $lnct_prog_id;
	}
	
/*
================================================================================================
ENVIAR EMAIL
================================================================================================
*/

	function emailEnviar($email_destinatario,$assunto,$conteudo)
    {
		$mensagemHelper = new MensagemHelper();
        $mensagemHelper->EnviarEmail($email_destinatario,$assunto,$conteudo);
	}

/*
===========================================================================================
RECIBO
===========================================================================================
*/

	function recibo($db,$array_dados){


		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		$cliente_id = $_SESSION['cliente_id'];
		$cidade = $db_w2b->fetch_assoc('select logradouro, numero, complemento, bairro, cidade, uf, cep from clientes where id = '.$cliente_id);

		//$fav = $db->fetch_assoc('select id, nome, inscricao, cpf_cnpj from favorecidos where id = '.$array_dados['favorecido_id']);
		
        if($array_dados['tipo'] == 'P'){
            
			$fav = $db->fetch_assoc('select id, nome, inscricao, cpf_cnpj from favorecidos where id = '.$array_dados['favorecido_id']);            
            $nome_beneficiario = $db_w2b->fetch_assoc('select nome, inscricao, cpf_cnpj from clientes where id = '.$cliente_id);
            $nome_do_arquivo = $fav['nome'];
            //CPF
            if(!empty($nome_beneficiario['cpf_cnpj'])){ $insc_cpf_cnpj = ", inscrito no ".mb_strtoupper($nome_beneficiario['inscricao'])." sob o número <b>".$nome_beneficiario['cpf_cnpj']."</b>, "; }else{ $insc_cpf_cnpj = ""; }
            
		}elseif($array_dados['tipo'] == 'R'){
            
			$fav = $db_w2b->fetch_assoc('select id, nome, inscricao, cpf_cnpj from clientes where id = '.$cliente_id);            
            $nome_beneficiario = $db->fetch_assoc('select nome, inscricao, cpf_cnpj from favorecidos where id = '.$array_dados['favorecido_id']);
            $nome_do_arquivo = $nome_beneficiario['nome'];
            //CPF
            if(!empty($nome_beneficiario['cpf_cnpj'])){ $insc_cpf_cnpj = ", inscrito no ".mb_strtoupper($nome_beneficiario['inscricao'])." sob o número <b>".$nome_beneficiario['cpf_cnpj']."</b>, "; }else{ $insc_cpf_cnpj = ""; }
		}
        
		$favorecido = $fav['nome'];
		$descricao = $array_dados['descricao'];
		if(!empty($fav['cpf_cnpj'])){ $cpf_cnpj = '<br>'.$fav['inscricao'].': '.$fav['cpf_cnpj']; }

		
		$valor = "R$ ".$array_dados['valor'];
		//Escrever o valor por extenso
		$valorPorExtenso = clsTexto::valorPorExtenso($valor, true, false);
		
		
		if(!empty($array_dados['dt_compensacao'])){ $data = explode("/", $array_dados['dt_compensacao']); }else{ $data = explode("/", $array_dados['dt_vencimento']); }
			$dia = $data['0'];
			$m = $data['1'];
			$ano = $data['2'];
			
			$dt_recibo = $ano.'-'.$m.'-'.$dia;
		
			//$m = date('m');
			if($m == '01'){ $mes = 'Janeiro';}
			elseif($m == '02'){ $mes = 'Fevereiro';}
			elseif($m == '03'){ $mes = 'Março';}
			elseif($m == '04'){ $mes = 'Abril';}
			elseif($m == '05'){ $mes = 'Maio';}
			elseif($m == '06'){ $mes = 'Junho';}
			elseif($m == '07'){ $mes = 'Julho';}
			elseif($m == '08'){ $mes = 'Agosto';}
			elseif($m == '09'){ $mes = 'Setembro';}
			elseif($m == '10'){ $mes = 'Outubro';}
			elseif($m == '11'){ $mes = 'Novembro';}
			else{ $mes = 'Dezembro';}
			
	 function moeda($get_valor) {  
		$source = array('.', ',');  
		$replace = array('', '.');  
		$valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto  
		return $valor; //retorna o valor formatado para gravar no banco
	} 
			
			$valor_db = moeda($array_dados['valor']);
			
			// nome, cpf_cnpj, id_favorecido, valor, descricao, dt_recibo
			$dados_recibo = array('tp'=>$array_dados['tipo'], 'id_favorecido'=>$fav['id'], 'nome'=>$favorecido, 'inscricao'=> $fav['inscricao'], 'cpf_cnpj'=>$fav['cpf_cnpj'], 'valor'=>$valor_db, 'descricao'=>$descricao, 'dt_recibo'=>$dt_recibo);
			
			$db->query_insert("recibos",$dados_recibo); //Insere os dados na tabela de recibo
			$numeroRecibo = $db->fetch_assoc('SELECT id FROM recibos ORDER BY id DESC LIMIT 1'); //Pega o ultimo registro inserido na tabela e utiliza como número do recibo
		
            
            // LOGO DO CLIENTE E WEB FINANÇAS
            //if($cliente_id == '207'){ $logo = "../logos/logo_odontolar.png"; }else{ $logo = "../../../images/logo_webfinancas_fundo_branco.png"; }
            session_start();
            $logo = '../../../'.$_SESSION['logo_recibo']; //Logo recibo
            
            $numRecibo = str_pad($numeroRecibo['id'], 10, "0", STR_PAD_LEFT);
            
		$html = '<table width="100%" id="recibo" border="0" cellpadding="0" cellspacing="0">
					<tr class="linhaTitulo">
						<td align="left" width="33%"><img src="'.$logo.'" height="90"></td>
						<td align="center" class="titulo" width="33%">RECIBO</td>
						<td align="right" width="33%">Nº: <span class="numeroRecibo">'.$numRecibo.'</span></td>
					</tr>
					<tr>
						<td  colspan="3" align="right"><b class="numeroRecibo"> '.$valor.'</b></td>
					</tr>
					<tr>
						<td colspan="3" height="100" class="texto">Recebemos de <b>'.mb_strtoupper($nome_beneficiario['nome']).'</b>'.$insc_cpf_cnpj.' a importância de <b>'.$valor.'</b> (<b>'.mb_strtoupper($valorPorExtenso).'</b>), referente a <b>'.mb_strtoupper($descricao).'</b>. Para maior clareza firmo(amos) o presente.</td>
					</tr>
					
					<tr>
						<td colspan="3" align="center" ><br> '.$cidade['cidade'].', '.$dia.' de '.$mes.' de '.$ano.'</td>
					</tr>
					<tr>
						<td colspan="3" align="center" height="100"> _____________________________________________________ <br>'.$favorecido.' '.$cpf_cnpj.'</td>
					</tr>
					<tr>
						<td colspan="3" align="center" class="rodape">'.$cidade['logradouro'].', '.$cidade['numero'].', '.$cidade['complemento'].', '.$cidade['bairro'].', '.$cidade['cidade'].' - '.$cidade['uf'].' - '.$cidade['cep'].'</td>
					</tr>
					<tr>
						<td colspan="3" align="center" class="rodape">Recibo gerado pelo sistema <a href="https://www.webfinancas.com" target="_blank">webfinancas.com</a></td>
					</tr>
				</table>
                
                <br> <br>
                
                 <span class="textLinhaPontilhada">Corte na linha pontilhada.<br>
                - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                </span>
                
                <br> <br>
                
                <table width="100%" id="recibo" border="0" cellpadding="0" cellspacing="0">
					<tr class="linhaTitulo">
						<td align="left" width="33%"><img src="'.$logo.'" height="90"></td>
						<td align="center" class="titulo" width="33%">RECIBO</td>
						<td align="right" width="33%">Nº: <span class="numeroRecibo">'.$numRecibo.'</span></td>
					</tr>
					<tr>
						<td  colspan="3" align="right"><b class="numeroRecibo"> '.$valor.'</b></td>
					</tr>
					<tr>
						<td colspan="3" height="100" class="texto">Recebemos de <b>'.mb_strtoupper($nome_beneficiario['nome']).'</b>'.$insc_cpf_cnpj.' a importância de <b>'.$valor.'</b> (<b>'.mb_strtoupper($valorPorExtenso).'</b>), referente a <b>'.mb_strtoupper($descricao).'</b>. Para maior clareza firmo(amos) o presente.</td>
					</tr>
					
					<tr>
						<td colspan="3" align="center" ><br> '.$cidade['cidade'].', '.$dia.' de '.$mes.' de '.$ano.'</td>
					</tr>
					<tr>
						<td colspan="3" align="center" height="100"> _____________________________________________________ <br>'.$favorecido.' '.$cpf_cnpj.'</td>
					</tr>
					<tr>
						<td colspan="3" align="center" class="rodape">'.$cidade['logradouro'].', '.$cidade['numero'].', '.$cidade['complemento'].', '.$cidade['bairro'].', '.$cidade['cidade'].' - '.$cidade['uf'].' - '.$cidade['cep'].'</td>
					</tr>
					<tr>
						<td colspan="3" align="center" class="rodape">Recibo gerado pelo sistema <a href="https://www.webfinancas.com" target="_blank">webfinancas.com</a></td>
					</tr>
				</table>
                
                '; 

			//Cabeçalho		
/*			$pdfHeader = '
				<table width="100%" class="rodape"><tr>
					<td><img src="../../../images/logo_Fatura_Expressa_fundo_branco.png"></td>
					<td align="right"><img src="../../../images/logo_claro.png"></td>
				</tr></table>'; */
			//Rodapé
		
		$nome_arq = str_replace(' ','_',$nome_do_arquivo);
		//==============================================================
		
		$mpdf=new mPDF('pt_BR','A4','','',20,20,10,18,5,8); //cria um novo container PDF no formato A4 com orientação customizada ex.:class mPDF ([ string $mode [, mixed $format [, float $default_font_size [, string $default_font [, float $margin_left , float $margin_right , float $margin_top , float $margin_bottom , float $margin_header , float $margin_footer [, string $orientation ]]]]]]) 
		//$mpdf->SetDisplayMode('fullpage');
		$mpdf->useSubstitutions=false;
		$mpdf->simpleTables = false;
		// LOAD a stylesheet
		//$mpdf->SetHTMLHeader($pdfHeader);
		//$mpdf->SetHTMLFooter($pdfFooter);
		$stylesheet = file_get_contents('../class/style_recibo_pdf.css');
		$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
		$mpdf->WriteHTML($html,2);
		//$mpdf->Output('pdf/teste.pdf','F'); ou D
		$mpdf->Output('Recibo_'.$nome_arq.'.pdf', 'D'); //faz o download
		//$mpdf->Output(); //para imprimir direto na tela
		//exit;
		//==============================================================

		//$recibo = $descricao.' - '.$favorecido.' - '.$valor;
		//return $retorno = array('situacao'=>1,'recibo'=>$recibo);
		return true;
	
	}

/*
===========================================================================================
EXIBIR ANEXO
===========================================================================================
*/

	function anexosExibir($db,$lancamento_id){
		
        $queryAnexos = $db->fetch_all_array("select id, nome_arquivo, nome_arquivo_org from lnct_anexos where lancamento_id = ".$lancamento_id);
		
        if(count($queryAnexos)>0)
        {
			$anexos = array();

            //$anexoJson = '';

			foreach($queryAnexos as $anexo)
            {
				$nome_arquivo = $anexo['nome_arquivo'];
				$ponto_pos = strrpos($nome_arquivo, '.')+1;
				$ext = substr($nome_arquivo, $ponto_pos);
				$anexo_tam = round(filesize("../../../uploads/cliente_".$_SESSION['cliente_id']."/".$nome_arquivo)/1024);
				array_push($anexos,array(
                    'id'=>$anexo['id'],
                    'nome_arquivo'=>$nome_arquivo,
                    'nome_arquivo_org'=>$anexo['nome_arquivo_org'],
                    'ext'=>$ext,
                    'tamanho'=>$anexo_tam
                    ));
                
                //$anexoJson .= '{"id":"'.$anexo['id'].'","nome_arquivo":"'.$nome_arquivo.'","nome_arquivo_org":"'.$anexo['nome_arquivo_org'].'","ext":"'.$ext.'","tamanho":"'.$anexo_tam.'"},';
			}

			//$anexoJson = substr($anexoJson,0,-1); //retira a ultima virgula
			
            //$anexoJson = '['.$anexoJson.']';
			
            //return $anexoJson;

            return $anexos;
		}
        else
        {
            return array();
        }
        
	}


/*
===========================================================================================
EXCLUIR ANEXO
===========================================================================================
*/

	function anexoExcluir($db,$lancamento_id='',$anexo_id=''){
		if($lancamento_id!=''){
			$anexos = $db->fetch_all_array("select id, nome_arquivo from lnct_anexos where lancamento_id = ".$lancamento_id);
			foreach($anexos as $anexo){
				$arquivo = "../../../uploads/cliente_".$_SESSION['cliente_id']."/".$anexo["nome_arquivo"];
				if(file_exists($arquivo)){
					unlink($arquivo);
				}
				$db->query("delete from lnct_anexos where id = ".$anexo["id"]);
			}
		}elseif($anexo_id!=''){
			$anexo = $db->fetch_assoc('select nome_arquivo from lnct_anexos where id = '.$anexo_id);
			$arquivo = "../../../uploads/cliente_".$_SESSION['cliente_id']."/".$anexo["nome_arquivo"];
			if(file_exists($arquivo)){
				unlink($arquivo);
			}
			$db->query("delete from lnct_anexos where id = ".$anexo_id);
		}
	}

/*
===========================================================================================
CALCULA PRÓXIMO VENCIMENTO PARA LANÇAMENTO RECORRENTE
===========================================================================================
*/

	function calculaProxVencRcr($array_dados){
		if($array_dados['frequencia']==0){
			$dt_vencimento_atual = explode('-',$array_dados['dt_vencimento']);
			$qtd_dias = $array_dados['qtd_dias'];
			$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+$qtd_dias,$dt_vencimento_atual[0]);
			$dt_vencimento = date('Y-m-d',$dt_vencimento);
		}elseif($array_dados['frequencia']>=30){
			$frequencia = $array_dados['frequencia']/30;
			$dia_vencimento = $array_dados['dia_mes'];
			$dt_vencimento_atual = explode('-',$array_dados['dt_vencimento']);
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
			$dt_vencimento_atual = explode('-',$array_dados['dt_vencimento']);
			$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
			$dt_vencimento = date('Y-m-d',$dt_vencimento);
		}
		return $dt_vencimento;
	}

/*
===========================================================================================
CALCULAR DATA DE EMISSÃO PARA EXIBIR LANÇAMENTO RECORRENTE
===========================================================================================
*/

	/*
	Utiliza a data de emissao e data de vencimento inicial 
	do lançamento recorrente para encontrar a diferença de dias
	e substrai da próxima data de vencimento do lançamento recorrente
	para encontrar a data de emissão
	*/

	function dtEmissaoRcrCalc($dt_emissao,$dt_inicio,$dt_vencimento){
		$dt_emissao_ts = strtotime($dt_emissao);
		$dt_inicio_ts = strtotime($dt_inicio);
		$dias_dif = (int)floor( ($dt_inicio_ts - $dt_emissao_ts) / (60 * 60 * 24));
		$dt_vencimento = explode('-',$dt_vencimento);
		$emissao = mktime(0,0,0,$dt_vencimento[1],$dt_vencimento[2]-$dias_dif,$dt_vencimento[0]);
		$emissao = date("d/m/Y",$emissao);
		return $emissao;
	}


/*
===========================================================================================
LOG
===========================================================================================
*/

	function log_rotina($email_destinatario,$arquivo_log,$msg,$enviar_email=false,$assunto=''){
		$arquivo = '../log/'.$arquivo_log;
		$fp = fopen($arquivo,"a+");
		fwrite($fp,$msg."\r\n");
		fclose($fp);
		if($enviar_email){
			$conteudo = $msg;
			self::emailEnviar($email_destinatario,$assunto,$conteudo);
		}
		//echo $arquivo_log.' '.$erro_msg;
	}

/*
===========================================================================================
COMPENSAR LANÇAMENTOS AUTOMATICAMENTE
===========================================================================================
*/

	function lnctAutoCompensar(){

        $emailDestinatarioLog = 'rafael@web2business.com.br';

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
				self::log_rotina($emailDestinatarioLog,$arquivo_log,$msg);
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
			self::log_rotina($emailDestinatarioLog,$arquivo_log,$msg,true,$assunto);
			exit();
		}

		try{
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
					$lnct_rcr_dados = self::lancamentosRcrExibir($cliente_db_conexao,array("lancamento_id"=>$lancamento_rcr['id']),1);
					$lnct_rcr = $lnct_rcr_dados['lancamento'];
					$lnct_rcr_ctr_plc = $lnct_rcr_dados['ctr_plc_lancamentos'];

					//converte lançamento recorrente para programado
					$lancamento = new Lancamento($cliente_db_conexao,$lnct_rcr);

					$array_dados = array(
						"lancamento_id" => $lancamento_rcr['id'],
						"tipo" => $lancamento_rcr['id'],
						"ct_resp_lancamentos" => $lnct_rcr_ctr_plc
					);

					$lancamento->rcr_to_prog($cliente_db_conexao,$array_dados,true);

				}
				//fim da busca por lançamentos recorrentes
	
				//busca lançamentos à vencer na tabela lancamentos ordenados por recebimentos, transferências e depois pagamentos
				//VERIFICAR A VIABILIDADE DE CRIAR UM INDICE NA DATA DE VENCIMENTO
				$dia_semana = date('N');
				if($dia_semana>=6){ //sabado ou domingo
					$query_lancamentos = mysql_query('
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and sab_dom = 1
							and tipo = "R"
						
						union
						
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, 0 as favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and sab_dom = 1
							and tipo = "T"

						union
						
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, favorecido_id, dt_vencimento
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
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento >= "'.$dt_ini.'"
							and dt_vencimento <= "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "R"
							
						union
						
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, 0 as favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento >= "'.$dt_ini.'"
							and dt_vencimento <= "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "T"
							
						union
						
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento >= "'.$dt_ini.'"
							and dt_vencimento <= "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "P"												
					',$cliente_db_conexao->link_id);
				}else{ //exceto sabado, domingo e segunda feira
					$query_lancamentos = mysql_query('
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "R"
							
						union
							
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, 0 as favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "T"
							
						union
						
						select id, descricao, tipo, conta_id, qtd_parcelas, valor, conta_id_origem, conta_id_destino, lancamento_recorrente_id, favorecido_id, dt_vencimento
						from lancamentos 
						where dt_vencimento = "'.$hoje.'"
							and compensado = 0
							and auto_lancamento = "A"
							and tipo = "P"
					',$cliente_db_conexao->link_id);
				}

				$dt_compensacao = date('Y-m-d');
		
                $lancamentosHistorico = array();
				
                //quita os lançamentos encontrados
				while($lancamento = mysql_fetch_assoc($query_lancamentos)){

                    $lancamento['compensado'] = 0;
                    $lancamento['dt_compensacao'] = $dt_compensacao;

					if($lancamento['tipo']=='R'){
						Recebimento::atualizarSaldoConta($cliente_db_conexao,$lancamento['valor'],$lancamento['conta_id'],'add');
						mysql_query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$lancamento['id'],$cliente_db_conexao->link_id);
						mysql_query("update lancamentos set compensado = 1, dt_compensacao = '".$dt_compensacao."' where id = ".$lancamento['id'],$cliente_db_conexao->link_id);
                        
                        $lancamento['compensado'] = 1;
                        array_push($lancamentosHistorico,$lancamento);

                        //start: excluir após verificação do erro
                        self::VerificarSaldo($cliente_db_conexao,$lancamento['conta_id'],$lancamento,$lancamento['id']);
                        //end: excluir após verificação do erro

					}elseif($lancamento['tipo']=='P'){
						$conta_saldo = mysql_fetch_assoc(mysql_query("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$lancamento['conta_id'],$cliente_db_conexao->link_id));
						if($conta_saldo['saldo_total']>=$lancamento['valor']){
							Pagamento::atualizarSaldoConta($cliente_db_conexao,$lancamento['valor'],$lancamento['conta_id'],'add');
							mysql_query("update ctr_plc_lancamentos set situacao = 1 where lancamento_id = ".$lancamento['id'],$cliente_db_conexao->link_id);
							mysql_query("update lancamentos set compensado = 1, dt_compensacao = '".$dt_compensacao."' where id = ".$lancamento['id'],$cliente_db_conexao->link_id);
                            
                            $lancamento['compensado'] = 1;
                            array_push($lancamentosHistorico,$lancamento);

                            //start: excluir após verificação do erro
                            self::VerificarSaldo($cliente_db_conexao,$lancamento['conta_id'],$lancamento,$lancamento['id']);
                            //end: excluir após verificação do erro
						}
					}elseif($lancamento['tipo']=='T'){
						$conta_origem_saldo = mysql_fetch_assoc(mysql_query("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$lancamento['conta_id_origem'],$cliente_db_conexao->link_id));
						if($conta_origem_saldo['saldo_total']>=$lancamento['valor']){
							Transferencia::atualizarSaldoConta($cliente_db_conexao,$lancamento['valor'],$lancamento['conta_id_origem'],$lancamento['conta_id_destino'],'add');
							mysql_query("update lancamentos set compensado = 1, dt_compensacao = '".$dt_compensacao."' where id = ".$lancamento['id'],$cliente_db_conexao->link_id);
                            
                            $lancamento['compensado'] = 1;
                            array_push($lancamentosHistorico,$lancamento);

                            //start: excluir após verificação do erro
                            self::VerificarSaldo($cliente_db_conexao,$lancamento['conta_id_origem'],$lancamento,$lancamento['id']);
                            self::VerificarSaldo($cliente_db_conexao,$lancamento['conta_id_destino'],$lancamento,$lancamento['id']);
                            //end: excluir após verificação do erro
						}
					}
				}
				//fim da busca e quitação na tabela lançamentos

                //start: registra histórico dos lançamentos
                if(count($lancamentosHistorico>0))
                    self::HistoricoLancamentos($cliente_db_conexao,$lancamentosHistorico,true);
                //end: registra histórico dos lançamentos

				//encerra a transação
				$cliente_db_conexao->query('commit');
				$cliente_db_conexao->close();
			}
			if($db_limite>=$db_limite_total){
				$hora = $hoje.' - '.date('H:i:s');
				$msg = 'Rotina auto compensar executada com sucesso - '.$hora;
				self::log_rotina($emailDestinatarioLog,$arquivo_log,$msg,true,'Log Web Finanças');
			}
		}

		catch(Exception $e){
			//echo 'Erro: ',$e->getMessage();
			$cliente_db_conexao->query('rollback');
			$assunto = 'ERRO - Rotina auto compensar';
			$msg = $e->getMessage();
			self::log_rotina($emailDestinatarioLog,$arquivo_log,$msg,true,$assunto);
			exit();
		}
	}

    //GERAR FATURA - PRIME HALL
    //================================================================================================

    function FaturasGerarPrimeHall($DataInicial){
	
        //DESCRIÇÃO
        //...continuar descrição depois ;)

        try{
                 
         if(empty($DataInicial)){ echo $DataInicial = date('Y-m-01'); }

         $DataFinal = date('Y-m-d', strtotime('+1 months', strtotime(date($DataInicial)))); 
       
        
            //conecta aos bancos da w2b e wf da w2b
            $db_wf = new Database('mysql.webfinancas.com','webfinancas12','W2BSISTEMAS','webfinancas12'); //fazer conexão com banco de dados da prime hall
            
            //busca dados do lançamento no wf (***para faturas com vencimento em 10 dias***) Intervalo manual de dias -> '2016-10-01' AND '2016-10-30' ou '".date('Y-m-d', strtotime('+1 months', strtotime(date('Y-m-01'))))."' AND '".date('Y-m-d', strtotime('+1 months', strtotime(date('Y-m-t'))))."'

            //$lancamentos = $db_wf->fetch_all_array("select * FROM lancamentos WHERE dt_vencimento BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t', strtotime('+1 months', strtotime(date('Y-m-01'))))."' AND tipo = 'R' AND compensado = 0");
            //$lancamentos = $db_wf->fetch_all_array("select * FROM lancamentos WHERE dt_vencimento BETWEEN '2017-03-01' AND '".date('Y-m-d', strtotime('+1 months', strtotime(date('Y-m-t'))))."' AND tipo = 'R' AND compensado = 0");

             $lancamentos = $db_wf->fetch_all_array("select * FROM lancamentos WHERE dt_vencimento BETWEEN '".$DataInicial."' AND '".$DataFinal."' AND tipo = 'R' AND compensado = 0");
            

            $contador = 0;

            foreach($lancamentos as $lancamento){
                
                $verificaBoleto = $db_wf->fetch_assoc("select lancamento_id FROM boletos WHERE lancamento_id = ".$lancamento['id']);
                
                if($verificaBoleto == false){                
                    
                    //insere registro do boleto
                    $sequencial = $db_wf->fetch_assoc("select boleto_ano, sequencial from contas where id = ".$lancamento["conta_id"]." for update");
                    
                    //verifica se o sequencial e ano do boleto devem ser reiniciados
                    //suporta apenas até o ano de 2115 e então começará a repetir
                    if($sequencial['boleto_ano']!=date('y')){
                        $novo_boleto_ano = $sequencial['boleto_ano'] * 1 + 1;
                        if($novo_boleto_ano==100)
                            $novo_boleto_ano = '00';
                        $db_wf->query("update contas set sequencial = 2, boleto_ano = '".$novo_boleto_ano."' where id = ".$lancamento['conta_id']);
                        $conta_sequencial = 1;
                    }else{
                        $db_wf->query("update contas set sequencial = sequencial + 1 where id = ".$lancamento['conta_id']);
                        $conta_sequencial = $sequencial['sequencial'];
                    }
                    
                    $sequencial = $conta_sequencial;
                    
                    //Busca o código do banco
                    $contaFinanceira = $db_wf->fetch_assoc('SELECT banco_id, carteira, convenio, agencia FROM contas WHERE id = '.$lancamento['conta_id']);
                    $bank_cod = $db_wf->fetch_assoc('SELECT codigo FROM bancos WHERE id = '.$contaFinanceira['banco_id']);
                    $codBanco = $bank_cod['codigo'];

                    //gera nosso número
                    $anoEmissao = date('y');
                    $nossoNumero = Recebimento::GerarNossoNumeroBoleto($codBanco,$contaFinanceira['carteira'],$sequencial,$anoEmissao,$contaFinanceira['convenio'],$contaFinanceira['agencia']);
                    
                    //insere boleto
                    $array_boleto = array(
                        "sequencial" => $sequencial,
                        "nosso_numero" => $nossoNumero,
                        "lancamento_id" => $lancamento['id'],
                    );

                    $db_wf->query_insert("boletos",$array_boleto);

                    $boleto_id = mysql_insert_id($db_wf->link_id);


                    //Verifica a conta financeira (Não remover)
                    if($lancamento['conta_id'] != $conta_id){ 
                        $conta_id = $lancamento['conta_id']; 
                        $resultado .= ' <tr><td colspan="2">'.$contaFinanceira['descricao'].'</td></tr>
                                        <tr><td colspan="2">---------------------------------------------------------------------------------------------------------------</td></tr>'; 
                    }

                    $favorecido = $db_wf->fetch_assoc('SELECT nome FROM favorecidos WHERE id ='.$lancamento['favorecido_id']);

                    $resultado .= ' <tr><td colspan="2"><b>Nosso número: </b> '.$nossoNumero.'</td></tr>
                                    <tr><td align="left"><b>Descrição: </b>'.$lancamento['descricao'].' </td><td align="right"> '.date('d/m/Y', strtotime($lancamento['dt_vencimento'])).'</td> </tr>
                                    <tr><td align="left"><b>Favorecido: </b>'.$favorecido['nome'].' </td><td align="right"> '.number_format($lancamento['valor'], 2, ',', '.').'</td> </tr>
                                    <tr><td colspan="2">---------------------------------------------------------------------------------------------------------------</td></tr>';
                    
                    
                    $db_wf->query("commit");

                    $contador++;
                }
            }

            echo 'Total de boletos gerados: <b>'.$contador.'</b><br><br>';

            echo '<table width="600">'.utf8_decode($resultado).'</table>'; 

            $db_wf->close();

            //self::emailEnviar('Rotina Prime Hall','Geração de boletos executada com sucesso em '.date('d/m/Y H:i:s'));
        
        }catch(Exception $e){
            //self::emailEnviar('Rotina Prime Hall','Falha na geração de boletos em '.date('d/m/Y H:i:s'));
        }
        
        
    }

    /*
    ================================================================================================
    CALCULAR DATA DE COMPETÊNCIA
    ================================================================================================
    */
    
    static function DtCompetenciaCalc($dt_vencimento, $mes_dif){
		$dt_vencimento = explode('-',$dt_vencimento);
		$mes = $dt_vencimento['1'];
		$ano = $dt_vencimento['0'];
		$dt_competencia_ts = mktime(0,0,0,date($mes)-$mes_dif,1,$ano*1);
		$dt_competencia = date('m/Y',$dt_competencia_ts);
        return $dt_competencia;
    }

    //CALCULAR IMPOSTO
    //================================================================================================

    function CalcularImposto($db,$query_lancamentos,$dt_ini,$dt_fim){
        
        $arr_dt_ini = explode('-',$dt_ini);
        $arr_dt_fim = explode('-',$dt_fim);

        $totalRecebimento = 0;
        $totalPagamento = 0;
        $recebimentos = array();
        $pagamentos = array();

        //Contas financeiras tributáveis
        $contasTributaveis = $db->fetch_all_array("select id from contas where carne_leao = 1");

        if( count($contasTributaveis)>0 && ($arr_dt_ini[1].$arr_dt_ini[0] == $arr_dt_fim[1].$arr_dt_fim[0]) ){

            $query_lancamentos = str_replace('dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'"', '((dt_vencimento >= "'.$dt_ini.'" and dt_vencimento <= "'.$dt_fim.'") || (dt_compensacao >= "'.$dt_ini.'" and dt_compensacao <= "'.$dt_fim.'" and compensado = 1))', $query_lancamentos);

            $lancamentos = $db->fetch_all_array($query_lancamentos);

            foreach($contasTributaveis as $conta){
                
                foreach($lancamentos as $key => $lancamento){
                    
                    if($lancamento['conta_id'] == $conta['id']){ //Verifica se o lançamento pertence a uma conta tributável
                        
                        //Garante que lançamentos com vencimento no período especificado também foram compensados no mesmo período
                        $incluirCompensado = false;
                        if($lancamento['compensado']==1){
                            $dtCompensacao = $lancamento['dt_compensacao'];
                            $dtCompensacao = explode('-', $dtCompensacao);
                            if($dtCompensacao[1].$dtCompensacao[0] == $arr_dt_ini[1].$arr_dt_ini[0])
                                $incluirCompensado = true;
                        }

                        //Considera lançamentos em aberto e com vencimento no período especificado e considera lançamentos compensados no período especificado
                       //Removido para permitir o sistema exibir apenas lançamentos compensados no carne leão.
					   // if($lancamento['compensado'] == 0 || $incluirCompensado){

					    if($incluirCompensado){

                            if($lancamento['tipo']=='R'){
                                $totalRecebimento += $lancamento['valor'];
                                array_push($recebimentos,$lancamento['id']);
                            }elseif($lancamento['tipo']=='P'){
                                $totalPagamento += $lancamento['valor'];
                                array_push($pagamentos,$lancamento['id']);
                            }

                        }

                        unset($lancamentos[$key]);
                    }

                }

            }
            
            //Desconsidera os recebimentos de PJ
            $recebimentosId = join(',',$recebimentos);
            if($recebimentosId != ''){
                $totalRecebimentoPj = mysql_fetch_assoc(mysql_query('
                        select IFNULL(sum(l.valor),0) valor
                        from lancamentos l
                        join favorecidos f on l.favorecido_id = f.id
                        where l.id in ('.$recebimentosId.')
                          and f.inscricao = "cnpj"'));
            }else{
                $totalRecebimentoPj['valor'] = 0;
            }

            $totalRecebimento = $totalRecebimento - $totalRecebimentoPj['valor'];
            

            //Pagamentos dedutíveis
            if(count($pagamentos)>0){
                
                $pagamentosId = join(',',$pagamentos);

                $pagamentosDedutiveis = mysql_fetch_assoc(mysql_query('
                    select IFNULL(sum(a.valor),0) valor
                    from ctr_plc_lancamentos a
                    join plano_contas b on a.plano_contas_id = b.id and b.dedutivel = 1
                    join lancamentos c on a.lancamento_id = c.id
                    join favorecidos d on c.favorecido_id = d.id
                    where lancamento_id in ('.$pagamentosId.')'));

                $deducoes = $pagamentosDedutiveis['valor'];

            }else{
                $deducoes = 0;
            }
            
            
            //Valor base para cálculo do imposto devido
            $valorBaseImposto = $totalRecebimento - $deducoes;

            //Alíquotas (Verificar se a idade do contribuinte será considerada para cálculo do imposto)
            
            /** Abril de 2015
             * Até 1.903,98:  isento 
             * De 1.903,99 até 2.826,65: 7,5%  - R$ 142,80 
             * De 2.826,66 até 3.751,05: 15%   - R$ 354,80 
             * De 3.751,06 até 4.664,68: 22,5% - R$ 636,13 
             * Acima de 4.664,68: 27,5%        - R$ 869,36 
             * */

            $arrayTabelaVigencia = array(
                    0 => array('mes_ini'=>4, 'ano_ini'=>2015, 'mes_fim'=>'', 'ano_fim'=>''),
                );

            $arrayTabelas = array(
                    0 => array(
                        0=>array('base'=>1903.98,'aliquota'=>0,'deducao'=>0),
                        1=>array('base'=>2826.65,'aliquota'=>0.075,'deducao'=>142.8),
                        2=>array('base'=>3751.05,'aliquota'=>0.15,'deducao'=>354.8),
                        3=>array('base'=>4664.68,'aliquota'=>0.225,'deducao'=>636.13),
                        4=>array('base'=>4664.68,'aliquota'=>0.275,'deducao'=>869.36),
                    )
                );

            $timeMes = strtotime($arr_dt_ini[0].'-'.$arr_dt_ini[1].'-01');

            foreach($arrayTabelaVigencia as $key => $vigencia){
                
                $timeVigenciaIni = strtotime($vigencia['ano_ini'].'-'.$vigencia['mes_ini'].'-01');
                $timeVigenciaFim = strtotime($vigencia['ano_fim'].'-'.$vigencia['mes_fim'].'-01');
                                
                //if( ($dt_ini[1] >= $vigencia['mes_ini'] && $dt_ini[0] >= $vigencia['ano_ini'] ) && ( ($dt_ini[1] <= $vigencia['mes_fim'] && $dt_ini[0] <= $vigencia['ano_fim']) || ($vigencia['mes_fim'] == '' && $vigencia['ano_fim'] == '') ) ){
                if( ($timeMes >= $timeVigenciaIni ) && ( ($vigencia['mes_fim'] == '' && $vigencia['ano_fim'] == '') || ($timeMes <= $timeVigenciaFim) ) ){
                    $aliquotas = $arrayTabelas[$key];
                    break;
                }
            }
            
            $aliquotaMaxima = $aliquotas[count($aliquotas)-1];

            if($valorBaseImposto > $aliquotaMaxima['base']){
                $imposto =  $valorBaseImposto * $aliquotaMaxima['aliquota'] - $aliquotaMaxima['deducao'];
            }else{
                foreach($aliquotas as $aliquota){
                    
                    if($valorBaseImposto <= $aliquota['base']){
                        $imposto =  $valorBaseImposto * $aliquota['aliquota'] - $aliquota['deducao'];
                        break;
                    }
                    
                }
            }
            
            return array(
                    'recebimentosPf' => $db->valorFormat($totalRecebimento),
                    'recebimentosPj' => $db->valorFormat($totalRecebimentoPj['valor']),
                    'pagamentos' => $db->valorFormat($totalPagamento),
                    'deducoes' => $db->valorFormat($deducoes),
                    'naoDedutivel' => $db->valorFormat($totalPagamento - $deducoes),
                    'base' => $db->valorFormat($valorBaseImposto),
                    'imposto' => $db->valorFormat($imposto)
                );
        }else{
            return array(
                    'recebimentosPf' => '0,00',
                    'recebimentosPj' => '0,00',
                    'recebimentos' => '0,00',
                    'pagamentos' => '0,00',
                    'deducoes' => '0,00',
                    'naoDedutivel' => '0,00',
                    'base' => '0,00',
                    'imposto' => '0,00',
                );
        }
        
    }

    //HISTÓRICO DE LANÇAMENTOS
    //================================================================================================
    
    static function HistoricoLancamentos($db,$lancamentos,$autoCompensar=false){
    
        foreach($lancamentos as $lancamento){

            $historico = array(
            'tipo' => $lancamento['tipo'],
            'lancamento_id' => $lancamento['id'],
            'favorecido_id' => $lancamento['favorecido_id'],
            'conta_financeira_id' => $lancamento['conta_id'],
            'conta_financeira_id_origem' => $lancamento['conta_id_origem'],
            'conta_financeira_id_destino' => $lancamento['conta_id_destino'],
            'usuario_id' => $autoCompensar ? 0 : $_SESSION['usuario_id'],
            'descricao' => $lancamento['descricao'],
            'favorecido' => $lancamento['favorecido'],
            'conta_financeira' => $lancamento['conta_financeira'],
            'valor' => $lancamento['valor'],
            'dt_vencimento' => $lancamento['dt_vencimento'],
            'dt_compensacao' => $lancamento['dt_compensacao'],
            'compensado' => $lancamento['excluido'] ? 0 : $lancamento['compensado'],
            'excluido' => $lancamento['excluido'],
            'dt_alteracao' => date('Y-m-d H:i:s'),
            'usuario' => $autoCompensar ? 'Sistema' : $_SESSION['email']
            );

            $db->query_insert('lancamentos_historico',$historico);
        }
    }

    function VerificarSaldo($db, $contaId, $dados, $lancamentoId){
        
        $contaFinanceira = $db->fetch_assoc('select 
            descricao,
            vl_saldo, 
            vl_saldo_inicial, 
            limite_credito, 
            vl_credito 
            from contas 
            where id = '.$contaId);

        $lancamentos = $db->fetch_assoc('select 
            sum(if(tipo="R",valor,0)) recebimentos, 
            sum(if(tipo="P",valor,0)) pagamentos 
            from lancamentos 
            where compensado = 1 
            and conta_id = '.$contaId);

        $query_trans_entrada = '
			select sum(l.valor) valor
			from lancamentos l
			where l.tipo = "T"
				and l.conta_id_destino = '.$contaId.'
				and l.compensado = 1
		';
		$transEntrada = $db->fetch_assoc($query_trans_entrada);

        $query_trans_saida = '
			select sum(l.valor) valor
			from lancamentos l
			where l.tipo = "T"
				and l.conta_id_origem = '.$contaId.'
				and l.compensado = 1
		';
		$transSaida = $db->fetch_assoc($query_trans_saida);

        $saldo = $contaFinanceira['vl_saldo'];
        $saldoInicial = $contaFinanceira['vl_saldo_inicial'];
        $recebimentos = $lancamentos['recebimentos'];
        $pagamentos = $lancamentos['pagamentos'];
        $creditoUtilizado = bcsub($contaFinanceira['limite_credito'],$contaFinanceira['vl_credito'],2);

        $totalEntradas = bcadd($saldoInicial,$creditoUtilizado,2);
        $totalEntradas = bcadd($totalEntradas,$recebimentos,2);
        $totalEntradas = bcadd($totalEntradas,$transEntrada['valor'],2);
        
        $totalSaidas = bcadd($pagamentos,$transSaida['valor'],2);

        if( bccomp(bcsub($totalEntradas,$totalSaidas,2),$saldo,2) != 0 ){
            
			if($dados['tipo']=='R' || $dados['tipo']=='P')
			{
				$contaId = 'conta_id: '.$dados['conta_id'].' ('.$contaFinanceira['descricao'].')';
				$db->query_update('contas', array('vl_saldo' => (bcsub($totalEntradas,$totalSaidas,2))), 'id='.$dados['conta_id']);
            }else
                $contaId = 'conta_id_origem: '.$dados['conta_id_origem'].' <br> conta_id_destino: '.$dados['conta_id_destino'];
            
            $mensagem = 'A conta financeira abaixo está com o saldo incorreto: <br><br>'
                
                .'* Cliente: '.$_SESSION['cliente_id'].' - '.$_SESSION['nome'].' <br><br>'
                
                .'* Banco de dados: '.$_SESSION['db_usuario'].' <br><br>'
                
                .'* Função: '.$_REQUEST['funcao'].' <br><br>'
                
                .'* Dados da conta financeira <br>'
                .$contaId.' <br>'
                .'Valor total de entradas: '.$totalEntradas.' <br>'
                .'Valor total de saídas: '.$totalSaidas.' <br>'
                .'Saldo incorreto: '.$saldo.' <br>'
                .'Saldo correto: '.(bcsub($totalEntradas,$totalSaidas,2)).' <br><br>'
                
                .'* Dados do lançamento <br>'
                .'Lançamento: '.$lancamentoId.' - '.$dados['descricao'].' <br>'
                .'Tipo: '.$dados['tipo'].' <br>'
                .'Vencimento: '.$dados['dt_vencimento'].' <br>'
                .'Compensado: '.$dados['compensado'].' <br>'
                .'Compensação: '.$dados['dt_compensacao'].' <br>'
				.'Valor: '.$dados['valor'].' <br><br>';

            //self::emailEnviar('rafael@web2business.com.br','Saldo Incorreto', $mensagem);
        }
	}
	

/* ========== Verificar se o lançamento pode ser incluido, editado ou excluido pelo bloqueio da contabilidade ============= */
	function BlqLiberarLanc($db, $dados)
	{

		if($dados['lancamentoId'] == "0")
		{
			$data = explode('/', $dados['dtVencimento']);

			$ano = $data[2];
			$mes = (INT)$data[1];		
		
		}else{

			$lancamento = $db->fetch_assoc('SELECT MONTH(dt_vencimento) as mes, YEAR(dt_vencimento) as ano FROM lancamentos  WHERE id ='.$dados['lancamentoId']);
			
			$ano = $lancamento['ano'];
			$mes = $lancamento['mes'];
	
		}


		$verificacao = $db->numRows('SELECT id FROM remessa_contabil WHERE ano = '.$ano.' AND mes = '.$mes);

		if($verificacao == 0){

			$situacao = 1; //Liberado

		}else{

			$situacao = 0; //Bloqueado

		}

		return array('situacao' => $situacao, 'dataBloqueio' => str_pad($mes, 2, "0", STR_PAD_LEFT).'/'.$ano);

	}



}
