<?php

class Relatorio{

	var $dados = array();

    /*
    ===========================================================================================
    CONSTRUTOR
    ===========================================================================================
    */
	
	function __construct($array_dados=""){
		//if($array_dados!=""){
			//foreach($this->dados as $chave => $valor){
				//$this->dados[$chave] = $array_dados[$chave];
			//}
		//}
	}


    //GERAR PDF
    //===========================================================================================

    function pdfGerar($relatorio,$pdfHeader,$pdfFooter,$nomeRelatorio,$tp_print,$orientation="A4"){

	    require("../../../php/MPDF/mpdf.php");
        
	    $mpdf=new mPDF('c'); 
        
	    $mpdf->SetDisplayMode('fullpage');
        
	    //$mpdf->ignore_invalid_utf8 = true;
        
	    //$mpdf->allow_charset_conversion = true;
        
	    //$mpdf->charset_in='UTF-8';
        
	    // LOAD a stylesheet
	    //$stylesheet = file_get_contents('mpdfstyleA4.css');
	    $stylesheet = file_get_contents('../../../css/css_relatorios.css');
        
	    //$mpdf=new mPDF('pt_BR','A4-L','','',10,10,29,18,5,8); //Pagina estilo Paisagem (Horizontal)
	    $mpdf=new mPDF('pt_BR',$orientation,'','',10,10,29,18,5,8); //cria um novo container PDF no formato A4 com orientação customizada ex.:class mPDF ([ string $mode [, mixed $format [, float $default_font_size [, string $default_font [, float $margin_left , float $margin_right , float $margin_top , float $margin_bottom , float $margin_header , float $margin_footer [, string $orientation ]]]]]])
	    $mpdf->useSubstitutions=false;
	    $mpdf->simpleTables = true;
	    $mpdf->SetHTMLHeader($pdfHeader);
	    $mpdf->SetHTMLFooter($pdfFooter);
	    $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
	    $mpdf->WriteHTML($relatorio);
        
	    if($tp_print=="t"){
		    //Visualização na tela
		    $mpdf->Output($nomeRelatorio.'.pdf','I');

	    }else{
		    //Download
		    //$nomeRelatorio = 'Relatório_Movimentação_Financeira';
		    $mpdf->Output($nomeRelatorio.'.pdf','D');
	    }
	    //exit;

    }

    //DECODIFICA JSON
    //===========================================================================================

    static function JsonDecode($params){
        //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    //$jsonObj = json_decode($jsonTxt, true);
        $jsonObj = json_decode(stripslashes($params), true);
	    return($jsonObj);
    }

    static function PeriodoRelatorio($db, $periodo)
    {
        $filtroPeriodo = self::JsonDecode($periodo);

        if($filtroPeriodo["periodo"] == "data")
        {
            $dtIni = $db->data_to_sql($filtroPeriodo["dt_ini"]);
            $dtFim = $db->data_to_sql($filtroPeriodo["dt_fim"]);
            $dtReferenciaIni = $filtroPeriodo["dt_ini"];
            $dtReferenciaFim = $filtroPeriodo["dt_fim"];
        }
        elseif($filtroPeriodo["periodo"] == "ano")
        {
            $dtIni = "$filtroPeriodo[ano]-01-01";
            $dtFim = "$filtroPeriodo[ano]-12-31";
            $dtReferenciaIni = "01/01/$filtroPeriodo[ano]";
            $dtReferenciaFim = "31/12/$filtroPeriodo[ano]";
        }
        else
        {
            //Mês inicial
            $mes = $filtroPeriodo["mes"];
            $ano = $filtroPeriodo["ano"];
            $dtIni = $ano.'-'.$mes.'-01';
            
            //Mês final
            $mesFim = $filtroPeriodo["mesFim"];
            $anoFim = $filtroPeriodo["anoFim"];
            $dtFim = $anoFim.'-'.$mesFim.'-'.date('t',strtotime("$anoFim-$mesFim-01"));
            
            //Data de referência do relatório
            $dtReferenciaIni = '01/'.$mes.'/'.$ano;
            $dtReferenciaFim = $db->sql_to_data($dtFim);
        }

        return array(
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
            'dtReferenciaIni' => $dtReferenciaIni,
            'dtReferenciaFim' => $dtReferenciaFim,
            );
    }

    //CALCULO DA DATA DE COMPETÊNCIA
    //===========================================================================================

    //Calcula a data de competência baseando-se na data de vencimento  Essa função é diferente da classe Lancamento.class.php
    function DtCompetenciaCalc($dt_vencimento, $mes_dif){
        $dt_vencimento = explode('-',$dt_vencimento);
        $dt_competencia_ts = mktime(0,0,0,$dt_vencimento['1']-$mes_dif,'01',$dt_vencimento['0']);
        $dt_competencia = date('Y-m-d',$dt_competencia_ts);
        return $dt_competencia;
    }

    //GetAllMeses
    //===========================================================================================
    /**
     * Retorna todos os meses do ano.
     * @return string[]
     */
    static function GetAllMeses()
    {
        $meses = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');
        return $meses;
    }

    //CALCULO DO SALDO ANTERIOR
    //===========================================================================================

    function saldo_anterior($db,$conta_id,$dt_ini) {

		    $saldo_anterior = 0;

		    $query_receita = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.conta_id = '.$conta_id.'
				    and l.tipo = "R"
				    and l.conta_id = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $receita = $db->fetch_assoc($query_receita);
		
		    $query_despesa = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.conta_id = '.$conta_id.'
				    and l.tipo = "P"
				    and l.conta_id = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $despesa = $db->fetch_assoc($query_despesa);
		
		    $query_trans_entrada = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.tipo = "T"
				    and l.conta_id_destino = '.$conta_id.'
				    and l.conta_id_destino = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $trans_entrada = $db->fetch_assoc($query_trans_entrada);
	
		    $query_trans_saida = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.tipo = "T"
				    and l.conta_id_origem = '.$conta_id.'
				    and l.conta_id_origem = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $trans_saida = $db->fetch_assoc($query_trans_saida);
	
		    $saldo_anterior = $receita['valor'] - $despesa['valor'] + $trans_entrada['valor'] - $trans_saida['valor'];
		
		    return $saldo_anterior;

    }

    //SALDO DA CONTA FINANCEIRA
    //===========================================================================================

    function contasFinanceirasSaldo($db,$params){

	$jsonObj = json_decode(stripslashes($params), true);
	$array_dados = $jsonObj;

	//periodo do relatório
	$jsonTxtPeriodo = $array_dados["periodo"];
	$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	$array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	if($array_filtro_periodo["periodo"] == "data"){
		$dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		$dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		$dt_referencia = $array_filtro_periodo["dt_fim"];
	}else{
		$mes = $array_filtro_periodo["mesFim"];
		$ano = $array_filtro_periodo["anoFim"];
		$dt_fim = mktime(0,0,0,$mes+1,'00',$ano);
		$dt_fim = date('Y-m-d',$dt_fim);
		$dt_referencia = $db->sql_to_data($dt_fim);
	}

	//contas financeiras do relatorio
	$array_cf_id = explode(',',$array_dados["contas_financeiras"]);

	$hora_relatorio = date('H:i:s');
	$data_relatorio = date('d/m/Y');
	$saldo_total = 0;
	$contas_saldo = "";
	
	foreach($array_cf_id as $cf_id){ $n += 1;

		$array_contas = $db->fetch_assoc('
			select c.id, b.nome, c.descricao, c.vl_saldo_inicial
			from contas c
			left join bancos b on c.banco_id = b.id
			where c.id = '.$cf_id.'
		');

		$saldo = 0;
		
		$conta_id = $array_contas[id];
	
		$query_receita = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.conta_id = '.$conta_id.'
				and l.tipo = "R"
				and l.conta_id = c.id
				and l.dt_compensacao <= "'.$dt_fim.'"
				and l.compensado = 1
			group by c.id
		';
		$receita = $db->fetch_assoc($query_receita);
		
		$query_despesa = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.conta_id = '.$conta_id.'
				and l.tipo = "P"
				and l.conta_id = c.id
				and l.dt_compensacao <= "'.$dt_fim.'"
				and l.compensado = 1
			group by c.id
		';
		$despesa = $db->fetch_assoc($query_despesa);
		
		$query_trans_entrada = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.tipo = "T"
				and l.conta_id_destino = '.$conta_id.'
				and l.conta_id_destino = c.id
				and l.dt_compensacao <= "'.$dt_fim.'"
				and l.compensado = 1
			group by c.id
		';
		$trans_entrada = $db->fetch_assoc($query_trans_entrada);
	
		$query_trans_saida = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.tipo = "T"
				and l.conta_id_origem = '.$conta_id.'
				and l.conta_id_origem = c.id
				and l.dt_compensacao <= "'.$dt_fim.'"
				and l.compensado = 1
			group by c.id
		';
		$trans_saida = $db->fetch_assoc($query_trans_saida);
	
		$saldo = $array_contas['vl_saldo_inicial'] + $receita[valor] - $despesa[valor] + $trans_entrada[valor] - $trans_saida[valor];
		
		$saldo_total += $saldo;
		
		 if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
		
		$contas_saldo .= '
			<tr bgcolor="'.$bg_color.'">
				<td align="left">'.$array_contas[descricao].' - '.$array_contas[nome].'</td>
				<td align="right">R$ '.$db->valorFormat($saldo).'</td>
			</tr>
		';
		
	}
	
	$relatorio .= '
		<div class="bordaArredondadaTitulo6" align="center"> 
		<table border="0" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td width="550" align="left"><span class="spanCinza"><b>DESCRIÇÃO</b></span></td>
					<td width="140" align="right"><span class="spanCinza"><b>SALDO</b></span></td>
				</tr>
			</thead>
			<tbody>
				'.$contas_saldo.'
			</tbody>
		</table>									
		<div class="subTotal" align="right">  <span class="spanCinza"> Total: </span> R$ '.$db->valorFormat($saldo_total).'</div>
		</div><br>
	';
	
	if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	$pdfHeader = '
								<div align="left" class="cabecalho" width="565">
									<span class="nomeRelatorio">RELATÓRIO DE</span>
										<br><b>SALDO DAS CONTAS FINANCEIRAS</b><br>
									<div class="linha">
										<div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Saldo até:</font> '.$dt_referencia.' </div>	<br>
									</div>
								</div>
								<div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	';
	
	$pdfFooter = '
								<div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div>
								<div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								<div class="rodape" width="255" align="right">webfinancas.com</div>
	';
	
	self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"Saldo_Contas_Financeiras",$array_dados['tp_print']);

}

    //EXTRATO DA CONTA FINANCEIRA
    //===========================================================================================

    function contasFinanceirasExtrato($db,$array_dados){

	//periodo do relatório
	$jsonTxtPeriodo = $array_dados["periodo"];
	//$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	$array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	if($array_filtro_periodo["periodo"] == "data"){
		$dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		$dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		$dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		$dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	}else{
		$mes = $array_filtro_periodo["mes"];
		$ano = $array_filtro_periodo["ano"];
		$dt_ini = $ano.'-'.$mes.'-01';
		$dt_fim = mktime(0,0,0,$mes+1,'00',$ano);
		$dt_fim = date('Y-m-d',$dt_fim);
		$dt_referencia_ini = '01/'.$mes.'/'.$ano;
		$dt_referencia_fim = $db->sql_to_data($dt_fim);
	}

	//contas financeiras do relatorio
	$array_cf_id = explode(',',$array_dados["contas_financeiras"]);

	$hora_relatorio = date('H:i');
	$data_relatorio = date('d/m/Y');
	$contas_saldo = "";
	$relatorio = "";
	
	foreach($array_cf_id as $cf_id){ $n += 1;

		$array_contas = $db->fetch_assoc('
			select c.id, b.nome, c.descricao
			from contas c, bancos b
			where c.id = '.$cf_id.'
				and c.banco_id = b.id
		');
	
		$saldo_anterior = 0;
		$saldo_atual = 0;
		$total_lancamentos = 0;
		$total_entradas = 0;
		$total_saidas = 0;
		$lancamentos = "";
		
		
		$conta_id = $array_contas[id];
	
		//calculo do saldo anterior
		$query_receita = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.conta_id = '.$conta_id.'
				and l.tipo = "R"
				and l.conta_id = c.id
				and l.dt_compensacao < "'.$dt_ini.'"
				and l.compensado = 1
			group by c.id
		';
		$receita = $db->fetch_assoc($query_receita);
		
		$query_despesa = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.conta_id = '.$conta_id.'
				and l.tipo = "P"
				and l.conta_id = c.id
				and l.dt_compensacao < "'.$dt_ini.'"
				and l.compensado = 1
			group by c.id
		';
		$despesa = $db->fetch_assoc($query_despesa);
		
		$query_trans_entrada = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.tipo = "T"
				and l.conta_id_destino = '.$conta_id.'
				and l.conta_id_destino = c.id
				and l.dt_compensacao < "'.$dt_ini.'"
				and l.compensado = 1
			group by c.id
		';
		$trans_entrada = $db->fetch_assoc($query_trans_entrada);
	
		$query_trans_saida = '
			select sum(l.valor) valor
			from contas c, lancamentos l
			where l.tipo = "T"
				and l.conta_id_origem = '.$conta_id.'
				and l.conta_id_origem = c.id
				and l.dt_compensacao < "'.$dt_ini.'"
				and l.compensado = 1
			group by c.id
		';
		$trans_saida = $db->fetch_assoc($query_trans_saida);
	
		$saldo_anterior = $receita[valor] - $despesa[valor] + $trans_entrada[valor] - $trans_saida[valor];
		$saldo_atual += $saldo_anterior;
		//fim do cáculo do saldo anterior
		
		//busca dos lançamentos
		$query_lancamentos = "
			(select date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao_formatada, dt_compensacao, l.descricao, l.valor, l.tipo
			from lancamentos l
			where l.conta_id = ".$conta_id."
			and l.tipo in ('R','P')
			and l.compensado = 1
			and l.dt_compensacao >= '".$dt_ini."'
			and l.dt_compensacao <= '".$dt_fim."')
			
			union all
		
			(select date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao_formatada, dt_compensacao, l.descricao, l.valor, 'R' tipo
			from lancamentos l
			where l.tipo = 'T'
			and l.conta_id_destino = ".$conta_id."
			and l.compensado = 1
			and l.dt_compensacao >= '".$dt_ini."'
			and l.dt_compensacao <= '".$dt_fim."')
			
			union all
			
			(select date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao_formatada, dt_compensacao, l.descricao, l.valor, 'P' tipo
			from lancamentos l
			where l.tipo = 'T'
			and l.conta_id_origem = ".$conta_id."
			and l.compensado = 1
			and l.dt_compensacao >= '".$dt_ini."'
			and l.dt_compensacao <= '".$dt_fim."')
			
			order by dt_compensacao
		";
	
		$array_lancamentos = $db->fetch_all_array($query_lancamentos);
	
		foreach($array_lancamentos as $lancamento){
			++ $total_lancamentos;
			if($lancamento[tipo]=='R'){
				$saldo_atual += $lancamento[valor];
				$total_entradas += $lancamento[valor];
				$cor = '#009900';
			}else{
				$saldo_atual -= $lancamento[valor];
				$total_saidas += $lancamento[valor];
				$cor = '#FF0000';
			}
			
					 if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
			
			$lancamentos .='
				<tr bgcolor="'.$bg_color.'">
						<td>'.$lancamento[dt_compensacao_formatada].'</td>
						<td>'.$lancamento[descricao].'</td>
						<td><font color="'.$cor.'">R$ '.$db->valorFormat($lancamento[valor]).'</font></td>
						<td>R$ '.$db->valorFormat($saldo_atual).'</td>
				</tr>
			';
		}
		//fim da busca pelos lançamentos
	
		$relatorio .= '
			<div class="widget">
			<div class="title"><img src="images/icons/dark/stats.png" alt="" class="titleIcon" /><h6>'.$array_contas[nome].' / '.$array_contas[descricao].' - Extrato de '.$dt_referencia_ini.' até '.$dt_referencia_fim.' - Emitido em '.$data_relatorio.' as '.$hora_relatorio.'</h6></div>
				<table cellpadding="0" cellspacing="0" width="100%" class="sTable">
					<thead>
						 <tr>
								<td width="1">Data</td>
								<td width="">Descrição</td>
								<td width="">Valor</td>
								<td width="">Saldo</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="3" align="right">
								<strong>SALDO ANTERIOR</strong>
							</td>
							<td>
								R$ '.$db->valorFormat($saldo_anterior).'
							</td>						
						</tr>
						'.$lancamentos.'
						<tr>
							<td colspan="3" align="right">
								<strong>TOTAL DE ENTRADAS</strong>
							</td>
							<td>
								<font color="#009900">R$ '.$db->valorFormat($total_entradas).'</font>
							</td>						
						</tr>
						<tr>
							<td colspan="3" align="right">
								<strong>TOTAL DE SAÍDAS</strong>
							</td>
							<td>
								<font color="#FF0000">R$ '.$db->valorFormat($total_saidas).'</font>
							</td>
						</tr>
						<tr>
							<td colspan="3" align="right">
								<strong>TOTAL DE LANÇAMENTOS</strong>
							</td>
							<td>
								'.$total_lancamentos.'
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		';
	}
	return $relatorio;
}

    //CATEGORIAS
    //===========================================================================================

    function planoContas($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	    }else{
		    $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		    $dt_referencia_ini = '01/'.$mes.'/'.$ano;
				
		    $mes_fim = $array_filtro_periodo["mesFim"];
		    $ano_fim = $array_filtro_periodo["anoFim"];
		    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
		    $dt_fim = date('Y-m-d',$dt_fim_ts);
		    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	    }
	    //fim período

	    $array_meses_nome = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');
	    $dt_hoje = date('Y-m-d');
	    $data_relatorio = date("d/m/Y");
	    $hora_relatorio = date("H:i:s");
	    $relatorio = '';
	
	    //contas financeiras do relatorio
	    $array_contas_financeiras = explode(',',$array_dados["contas_financeiras"]);

	    //nivel do plano de contas
	    $nivel_plc = $array_dados['nivel'];

	    //situação dos lançamentos
	    $lancamento_situacao = $array_dados['lancamento_situacao'];
	    $compensado = "";
        $dt_venc_comp = "";
	    if($lancamento_situacao==0){
		    $compensado = " and l.compensado = 0";
            $dt_venc_comp = ' and l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'"';
	    }elseif($lancamento_situacao==1){
		    $compensado = " and l.compensado = 1";
            $dt_venc_comp = ' and l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'"';
        }elseif($lancamento_situacao==3){
            $dt_venc_comp = ' and ((l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'" and compensado = 0) || (l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'"))';
	    }

	    //tabela temporária para lançamentos recorrentes
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_rcr_temp (
			    id int(11),
			    conta_id int(11),
			    dt_vencimento date NOT NULL,
			    valor decimal(10,2) NOT NULL,
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //busca lançamentos recorrentes
	    if($lancamento_situacao!=1){
		    foreach($array_contas_financeiras as $cf_id){ 
			    $query_lancamentos_rcr = mysql_query("
				    select id 
				    from lancamentos_recorrentes
				    where conta_id = ".$cf_id."
					    and dt_vencimento <= '".$dt_fim."'
			    ");
		
			    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
		 
				    $lancamento_rcr = $db->fetch_assoc("
					    select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
					    from lancamentos_recorrentes 
					    where id = ".$lancamento[id]
				    );
				
				    $dt_vencimento = date($lancamento_rcr[dt_vencimento]);
		
				    while($dt_vencimento <= $dt_fim){
				
					    if($dt_vencimento >= $dt_ini){
						    $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
					    }
					
					    if($lancamento_rcr[frequencia]>=30){
					
						    $frequencia = $lancamento_rcr[frequencia]/30;
						    $dia_vencimento = $lancamento_rcr[dia_mes];
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
				
					    $lancamento_rcr[dt_vencimento] = $dt_vencimento;
				
				    }
			    }
		    }
	    }
	    //fim da busca por lançamentos recorrentes

	    //busca valores de cada conta do plano
	    $array_valores = array();

	    $array_contas_analitias = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    foreach($array_contas_analitias as $conta_analitica){

		    $conta_analitica_id = $conta_analitica['id'];
		    $valor_acumulado = 0;

		    foreach($array_contas_financeiras as $conta_financeira_id){

                $valor = $db->fetch_assoc('
				    select sum(if(l.tipo="R", IFNULL(cpl.valor,0), IFNULL(cpl.valor,0) * (-1) )) valor
				    from ctr_plc_lancamentos cpl
				    join lancamentos l on cpl.lancamento_id = l.id
				    where cpl.plano_contas_id = '.$conta_analitica_id.'
					    and l.conta_id = '.$conta_financeira_id.'
					    '.$compensado.$dt_venc_comp);

			    $valor_acumulado += $valor['valor'];
		    }

		    $valor_rcr = $db->fetch_assoc('
			    select sum(if(cpl.tp_lancamento="R", IFNULL(cpl.valor,0), IFNULL(cpl.valor,0) * (-1) )) valor
			    from ctr_plc_lancamentos_rcr cpl
			    join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
			    where cpl.plano_contas_id = '.$conta_analitica_id
		    );
		    $valor_acumulado += $valor_rcr['valor'];

		    $array_hierarquia = explode(',',$conta_analitica['hierarquia']);
		    foreach($array_hierarquia as $plc_conta_id){
			    if(array_key_exists($plc_conta_id,$array_valores)){
				    $array_valores[$plc_conta_id] += $valor_acumulado;
			    }else{
				    $array_valores[$plc_conta_id] = $valor_acumulado;
			    }
		    }
	    }
	    //fim busca valores de cada conta do plano

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

        $array_plano_contas = $db->fetch_all_array('
            select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as decimal) as ordem1'.$ordem.'
            from plano_contas 
            where nivel <= '.$nivel_plc.'
            order by ordem1'.$orderBy);
        //end: busca plano de contas ordenado

	    //valores com plano de contas
	    foreach($array_plano_contas as $plano_contas){ 

	     $n +=1;

	     if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 
         $espc = $plano_contas[nivel] * 10;  $espc = $espc.'px'; 
	 
	 
		    $valor = $array_valores[$plano_contas[id]];
		
            $pl_contas .= '
			    <tr bgcolor="'.$bg_color.'">
				    <td align="left" style="padding-left:'.$espc.'">'.$plano_contas[cod_conta].' - '.$plano_contas[nome].'</td>
				    <td align="right">R$ '.$db->valorFormat($valor).'</td>
			    </tr>
		    ';
	    }
	
	    $relatorio .= '
		    <div class="bordaArredondadaTitulo6" align="center"> 
		    <table border="0" cellpadding="0" cellspacing="0">
			    <thead>
				    <tr>
					    <td width="550" align="left"><span class="spanCinza"><b>DESCRIÇÃO</b></span></td>
					    <td width="140" align="right"><span class="spanCinza"><b>VALOR</b></span></td>
				    </tr>
			    </thead>
			    <tbody>
				    '.$pl_contas.'
			    </tbody>
		    </table>									
		    </div><br>
		';
		
		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="565">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>CATEGORIAS</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.' </div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div>
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
	
	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"Plano_Contas",$array_dados['tp_print']);
    }


    //CENTRO DE CUSTO
    //===========================================================================================

    function centroResp($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	    }else{
		    $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		    $dt_referencia_ini = '01/'.$mes.'/'.$ano;
        
		    $mes_fim = $array_filtro_periodo["mesFim"];
		    $ano_fim = $array_filtro_periodo["anoFim"];
		    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
		    $dt_fim = date('Y-m-d',$dt_fim_ts);
		    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	    }
	    //fim período

	    $data_relatorio = date("d/m/Y");
	    $hora_relatorio = date("H:i:s");
	    $relatorio = '';

	    //contas financeiras do relatorio
	    $array_contas_financeiras = explode(',',$array_dados["contas_financeiras"]);

	    //situação dos lançamentos
	    $lancamento_situacao = $array_dados['lancamento_situacao'];
	    $compensado = "";
        $dtVencimentoQuery = "";
	    if($lancamento_situacao==0){
		    $compensado = " and l.compensado = 0";
            $dtVencimentoQuery = ' and l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'"';
	    }elseif($lancamento_situacao==1){
		    $compensado = " and l.compensado = 1";
            $dtVencimentoQuery = ' and l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'"';
	    }elseif($lancamento_situacao==3){
            $dtVencimentoQuery = ' and ((l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'" and compensado = 0) || (l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'"))';
	    }

	    //nível do centro de responsabilidade
	    $nivel = $array_dados['nivel'];

	    //centro selecionado
	    $centro_pai_id = $array_dados['centro_resp_id'];
	    if(!$centro_pai_id)
		    $centro_pai_id = "";
	    $centro_pai_id_ini = $centro_pai_id;
	    if($centro_pai_id!=""){
		    $nivel_ini = $db->fetch_assoc('select nivel from centro_resp where id = '.$centro_pai_id);
		    $nivel_ini = $nivel_ini['nivel'];
		    $nivel_fim = $nivel_ini + $nivel - 1;
	    }
	
	    //em cada nível encontra os filhos analíticos do centro selecionado
	    if($centro_pai_id!=""){
		    $arr_ctr_id[] = $centro_pai_id;
		    $arr_aux = array();
		    $arr_aux2 = array();
		    $arr_filhos = $db->fetch_all_array("select id, tp_centro from centro_resp where centro_pai_id = ".$centro_pai_id);
		    $hasFilho = count($arr_filhos);
		    if($hasFilho){
			    while($hasFilho){
				    foreach($arr_filhos as $filho){
					    if($filho['tp_centro']==1)
						    array_push($arr_aux2,$filho['id']);
					    array_push($arr_aux,$filho['id']);
                        array_push($arr_ctr_id,$filho['id']);
				    }
				    $centro_pai_id = join(',',$arr_aux);
				    //$arr_ctr_nivel[] = $centro_pai_id;
				    $arr_filhos = $db->fetch_all_array("select id, tp_centro from centro_resp where centro_pai_id in (".$centro_pai_id.")");
				    $hasFilho = count($arr_filhos);
				    if($hasFilho)
					    $arr_aux = array();
			    }
		    }else{
			    $arr_aux2[] = $centro_pai_id;
		    }
	    }
	    $arr_ctr_analitico = "";
	    if(count($arr_aux2))
		    $arr_ctr_analitico = join(',',$arr_aux2);
	
	    //tabela temporária para lançamentos recorrentes
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_rcr_temp (
			    id int(11),
			    conta_id int(11),
			    dt_vencimento date NOT NULL,
			    valor decimal(10,2) NOT NULL,
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //busca lançamentos recorrentes
	    if($arr_ctr_analitico!=""){
		    $arr_lnct_rcr_id = array();
		    $arr_lnct_rcr = $db->fetch_all_array("select distinct lancamento_rcr_id from ctr_plc_lancamentos_rcr where centro_resp_id in (".$arr_ctr_analitico.")");
		    foreach($arr_lnct_rcr as $lnct_rcr_id){
			    $arr_lnct_rcr_id[] = $lnct_rcr_id['lancamento_rcr_id'];
		    }
		    $arr_lnct_rcr_id = join(',',$arr_lnct_rcr_id);
		    $lnct_rcr_id = 'and id in ('.$arr_lnct_rcr_id.')';
	    }

	    if($arr_lnct_rcr_id!="" || $centro_pai_id_ini==""){
		    if($lancamento_situacao!=1){
			    foreach($array_contas_financeiras as $cf_id){
				    $query_lancamentos_rcr = mysql_query("
					    select id 
					    from lancamentos_recorrentes
					    where conta_id = ".$cf_id."
						    and dt_vencimento <= '".$dt_fim."'
						    ".$lnct_rcr_id."
				    ");
                
				    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
                    
					    $lancamento_rcr = $db->fetch_assoc("
						    select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
						    from lancamentos_recorrentes 
						    where id = ".$lancamento['id']
					    );
                    
					    $dt_vencimento = date($lancamento_rcr['dt_vencimento']);
                    
					    while($dt_vencimento <= $dt_fim){
                        
						    if($dt_vencimento >= $dt_ini){
							    $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
						    }
						
						    if($lancamento_rcr['frequencia']>=30){
                            
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
		    }
	    }
	    //fim da busca por lançamentos recorrentes

	    //busca valores de cada centro
	    $array_valores = array();

	    if($arr_ctr_analitico!=""){
		    $array_centros_analiticos = $db->fetch_all_array('
			    select id, hierarquia
			    from centro_resp
			    where id in ('.$arr_ctr_analitico.')
		    ');
	    }else{
		    $array_centros_analiticos = $db->fetch_all_array('
			    select id, hierarquia
			    from centro_resp
			    where tp_centro = 1
		    ');
	    }

	    foreach($array_centros_analiticos as $centro_analitico){

		    $centro_analitico_id = $centro_analitico['id'];
		    $valor_acumulado_r = 0;
		    $valor_acumulado_d = 0;

		    foreach($array_contas_financeiras as $conta_financeira_id){

			    $valor_r = $db->fetch_assoc('
				    select sum(IFNULL(cpl.valor,0)) valor
				    from ctr_plc_lancamentos cpl
				    join lancamentos l on cpl.lancamento_id = l.id
				    where cpl.centro_resp_id = '.$centro_analitico_id.'
					    and l.conta_id = '.$conta_financeira_id.'
                        and tp_lancamento = "R"					
                        '.$compensado.$dtVencimentoQuery);

			    $valor_d = $db->fetch_assoc('
				    select sum(IFNULL(cpl.valor,0)) valor
				    from ctr_plc_lancamentos cpl
				    join lancamentos l on cpl.lancamento_id = l.id
				    where cpl.centro_resp_id = '.$centro_analitico_id.'
					    and l.conta_id = '.$conta_financeira_id.'
					    and tp_lancamento = "P"
					    '.$compensado.$dtVencimentoQuery);

			    $valor_acumulado_r += $valor_r['valor'];
			    $valor_acumulado_d += $valor_d['valor'];
		    }

		    if($arr_lnct_rcr_id!="" || $centro_pai_id_ini==""){
			    $valor_rcr_r = $db->fetch_assoc('
				    select sum(IFNULL(cpl.valor,0)) valor
				    from ctr_plc_lancamentos_rcr cpl
				    join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
				    where cpl.centro_resp_id = '.$centro_analitico_id.'
					    and tp_lancamento = "R"
			    ');
			    $valor_rcr_d = $db->fetch_assoc('
				    select sum(IFNULL(cpl.valor,0)) valor
				    from ctr_plc_lancamentos_rcr cpl
				    join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
				    where cpl.centro_resp_id = '.$centro_analitico_id.'
					    and tp_lancamento = "P"
			    ');
			    $valor_acumulado_r += $valor_rcr_r['valor'];
			    $valor_acumulado_d += $valor_rcr_d['valor'];
		    }
		
		    $array_hierarquia = explode(',',$centro_analitico['hierarquia']);
		    foreach($array_hierarquia as $centro_id){
			    if(array_key_exists($centro_id,$array_valores)){
				    $array_valores[$centro_id]['receitas'] += $valor_acumulado_r;
				    $array_valores[$centro_id]['despesas'] += $valor_acumulado_d;
			    }else{
				    $array_valores[$centro_id]['receitas'] = $valor_acumulado_r;
				    $array_valores[$centro_id]['despesas'] = $valor_acumulado_d;
			    }
		    }
	    }
	    //fim busca valores de cada centro

        //start: monta ordenação do centro de custo
        $maiorNivel = $db->fetch_assoc('select max(nivel) nivel from centro_resp');
        $maiorNivel = $maiorNivel['nivel'];

        $ordem = '';
        $orderBy = '';

        if($maiorNivel>1){
        
            $arrayOrderBy = array();
            $arrayOrdem = array();

            for($i=2;$i<$maiorNivel;$i++){
                array_push($arrayOrderBy,'ordem'.$i);
                array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_centro,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
            }

            array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_centro,".",-1) as decimal),0) as ordem'.$i);
            array_push($arrayOrderBy,'ordem'.$i);

            $ordem = ','.join(',',$arrayOrdem);
            $orderBy = ','.join(',',$arrayOrderBy);
        }
        //end: monta ordenação do centro de custo

	    //valores com centro de custo
	    if($centro_pai_id_ini==""){

		    $array_centro_resp = $db->fetch_all_array('select id, cod_centro, nome, tp_centro, nivel, cast(substring_index(cod_centro,".",1) as decimal) as ordem1'.$ordem.' from centro_resp where nivel <= '.$nivel.' order by ordem1'.$orderBy);
		
		    foreach($array_centro_resp as $centro_resp){ 
			
                $n +=1;
			
                if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
			
                $espc = $centro_resp['nivel'] * 10;  $espc = $espc.'px'; 
			
			
			    $valor_r = $array_valores[$centro_resp['id']]['receitas'];
			    $valor_d = $array_valores[$centro_resp['id']]['despesas'];
			
                $ctr .= '
			    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:'.$espc.'">'.$centro_resp['cod_centro'].' - '.$centro_resp['nome'].'</td>
					    <td align="right"><font class="verde"> R$ '.$db->valorFormat($valor_r).'</font></td>
					    <td align="right"><font class="vermelho">R$ '.$db->valorFormat($valor_d).'</font></td>
			     </tr>
			    ';
		    }

	    }else{

            $CtrIds = join(',', $arr_ctr_id);

            $array_centro_resp = $db->fetch_all_array('select id, cod_centro, nome, tp_centro, nivel, cast(substring_index(cod_centro,".",1) as decimal) as ordem1'.$ordem.' from centro_resp where id in ('.$CtrIds.') and nivel >= '.$nivel_ini.' and nivel <= '.$nivel_fim.' order by ordem1'.$orderBy);

            foreach($array_centro_resp as $centro_resp){ 
            
                $n +=1;
            
                if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
            
                $espc = $centro_resp['nivel'] * 10;  $espc = $espc.'px'; 
            
                $valor_r = $array_valores[$centro_resp['id']]['receitas'];
                $valor_d = $array_valores[$centro_resp['id']]['despesas'];
            
                $ctr .= '
				    <tr bgcolor="'.$bg_color.'">
						    <td align="left" style="padding-left:'.$espc.'">'.$centro_resp['cod_centro'].' - '.$centro_resp['nome'].'</td>
						    <td align="right"><font class="verde"> R$ '.$db->valorFormat($valor_r).'</font></td>
						    <td align="right"><font class="vermelho">R$ '.$db->valorFormat($valor_d).'</font></td>
				     </tr>
				    ';
            }
	    }

	    $relatorio .= '
		    <div class="bordaArredondadaTitulo6" align="center"> 
		    <table border="0" cellpadding="0" cellspacing="0">
			    <thead>
				    <tr>
					    <td width="550" align="left"><span class="spanCinza"><b>DESCRIÇÃO</b></span></td>
					    <td width="140" align="right"><span class="spanCinza"><b>RECEITAS</b></span></td>
					    <td width="140" align="right"><span class="spanCinza"><b>DESPESAS</b></span></td>
				    </tr>
			    </thead>
			    <tbody>
				    '.$ctr.'
			    </tbody>
		    </table>									
		    </div><br>
	    ';


		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="565">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>CENTRO DE CUSTO</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.' </div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div>
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
	
	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"Centro_Responsabilidade",$array_dados['tp_print']);
    }

    /*
    ===========================================================================================
    MOVIMENTAÇÃO FINANCEIRA
    ===========================================================================================
    */

    function movimentoFinanceiro($db,$params){
/*
		$json_string = $params;
		$json_string = stripslashes(html_entity_decode($json_string));
		$bookingdata =  json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json_string), true ); 
	
		var_dump($bookingdata);
*/
	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);$jsonData)
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	    }else{ 
		    /*
            $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		    $dt_fim = mktime(0,0,0,$mes+1,'00',$ano);
		    $dt_fim = date('Y-m-d',$dt_fim);
		    $dt_referencia_ini = '01/'.$mes.'/'.$ano;
		    $dt_referencia_fim = $db->sql_to_data($dt_fim);
            */

            $mes_ini = $array_filtro_periodo["mes"];
            $ano_ini = $array_filtro_periodo["ano"];	
            $dt_ini = $ano_ini.'-'.$mes_ini.'-01';
            $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;

            $mes_fim = $array_filtro_periodo["mesFim"];
            $ano_fim = $array_filtro_periodo["anoFim"];
            $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
            $dt_fim = date('Y-m-d',$dt_fim_ts);
            $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	    }

		

	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i:s');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	
	    //tabela temporaria para armazenar lançamentos
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_temp (
			    id int PRIMARY KEY AUTO_INCREMENT,
			    ordem tinyint(1),
			    compensado tinyint(1),
			    dt_vencimento date NOT NULL,
			    dt_compensacao date NOT NULL,
			    descricao varchar(255),
			    valor decimal(10,2) NOT NULL,
			    tipo char(1),
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //situação dos lançamentos
	    $lancamento_situacao = 1;//$array_dados['lancamento_situacao'];
	    $compensado = "";
	    if($lancamento_situacao==0){
		    $compensado = "and l.compensado = 0";
	    }elseif($lancamento_situacao==1){
		    $compensado = "and l.compensado = 1";
	    }

	    //contas financeiras do relatório	
	    $array_cf_id = explode(',',$array_dados["contas_financeiras"]);

		//DT Emissao
        if($array_dados['dt_emissao']==1){
            $FiltroData = 'and l.dt_emissao >= "'.$dt_ini.'" and l.dt_emissao <= "'.$dt_fim.'"';
        }else{
            $FiltroData = 'and l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'"';
        }

	    //inclui lançamento na tabela temporária
	    foreach($array_cf_id as $cf_id){

		    $saldo_anterior = 0;
		    $saldo_atual = 0;
		    $total_lancamentos = 0;
		    $total_entradas = 0;
		    $total_saidas = 0;
		    $lancamentos = "";
		    $n = 0;

		    $array_conta = $db->fetch_assoc('
			    select c.id, b.nome, c.descricao, c.vl_saldo_inicial
			    from contas c
			    left join bancos b on c.banco_id = b.id
			    where c.id = '.$cf_id.'
		    ');
		
		    $conta_id = $array_conta['id'];

		    //calculo do saldo anterior
		    $query_receita = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.conta_id = '.$conta_id.'
				    and l.tipo = "R"
				    and l.conta_id = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $receita = $db->fetch_assoc($query_receita);
		
		    $query_despesa = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.conta_id = '.$conta_id.'
				    and l.tipo = "P"
				    and l.conta_id = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $despesa = $db->fetch_assoc($query_despesa);
		
		    $query_trans_entrada = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.tipo = "T"
				    and l.conta_id_destino = '.$conta_id.'
				    and l.conta_id_destino = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $trans_entrada = $db->fetch_assoc($query_trans_entrada);
	
		    $query_trans_saida = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.tipo = "T"
				    and l.conta_id_origem = '.$conta_id.'

				    and l.conta_id_origem = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $trans_saida = $db->fetch_assoc($query_trans_saida);
	
		    $saldo_anterior = $array_conta['vl_saldo_inicial'] + $receita[valor] - $despesa[valor] + $trans_entrada[valor] - $trans_saida[valor];
		    $saldo_atual += $saldo_anterior;
		    //fim do cáculo do saldo anterior

		    //busca lançamentos existentes
		    $query_lancamentos = " 
			    select l.compensado, l.dt_vencimento, l.dt_compensacao, l.descricao, l.valor, l.tipo
			    from lancamentos l
			    where l.conta_id = ".$conta_id."
			    and l.tipo <> 'T'
			    ".$compensado."
			    ".$FiltroData."
	
			    union all
	
			    select l.compensado, l.dt_vencimento, l.dt_compensacao, l.descricao, l.valor, 'R' tipo
			    from lancamentos l
			    where l.tipo = 'T'
			    and l.conta_id_destino = ".$conta_id."
			    ".$compensado."
			    ".$FiltroData."
			
			    union all
			
			    select l.compensado, l.dt_vencimento, l.dt_compensacao, l.descricao, l.valor, 'P' tipo
			    from lancamentos l
			    where l.tipo = 'T'
			    and l.conta_id_origem = ".$conta_id."
			    ".$compensado."
			    ".$FiltroData."
		    ";

		    $query_lancamentos = mysql_query($query_lancamentos);

		    while($lancamento = mysql_fetch_assoc($query_lancamentos)){
			    ($lancamento['tipo']=='R')? $lancamento['ordem'] = 1: $lancamento['ordem'] = 2;
			    $db->query_insert('lancamentos_temp',$lancamento);

		    }
		    //fim da busca por lançamentos existentes

		    //busca lançamentos recorrentes
		    if($lancamento_situacao!=1){
			    $query_lancamentos_rcr = mysql_query("
				    select id 
				    from lancamentos_recorrentes
				    where conta_id = ".$conta_id."
					    and dt_vencimento <= '".$dt_fim."'
			    ");
		
			    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
		 
				    $lancamento_rcr = $db->fetch_assoc("
					    select 0 compensado, dt_vencimento, descricao, valor, frequencia, dia_mes, tipo
					    from lancamentos_recorrentes 
					    where id = ".$lancamento[id]
				    );
				
				    $dt_vencimento = date($lancamento_rcr[dt_vencimento]);
		
				    while($dt_vencimento <= $dt_fim){
				
					    if($dt_vencimento >= $dt_ini){
						    ($lancamento_rcr['tipo']=='R')? $lancamento_rcr['ordem'] = 1: $lancamento_rcr['ordem'] = 2;
						    $db->query_insert('lancamentos_temp',$lancamento_rcr);
					    }
					
					    if($lancamento_rcr[frequencia]>=30){
					
						    $frequencia = $lancamento_rcr[frequencia]/30;
						    $dia_vencimento = $lancamento_rcr[dia_mes];
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
				
					    $lancamento_rcr[dt_vencimento] = $dt_vencimento;
				
				    }
			    }
		    }
		    //fim da busca por lançamentos recorrentes

		    //busca todos os lançamentos na tabela temporária
		    $query_lancamentos_temp = mysql_query("
			    select id, tipo, descricao, valor, if(compensado=1,dt_compensacao,dt_vencimento) as dt_lnct, date_format(if(compensado=1,dt_compensacao,dt_vencimento), '%d/%m/%Y') as dt_lnct_format, compensado
			    from lancamentos_temp 
			    order by dt_compensacao , ordem, id
		    ");

		    while($lancamento_temp = mysql_fetch_assoc($query_lancamentos_temp)){ 
			    $dt_lnct_temp = explode('-',$lancamento_temp['dt_lnct']);
			    $time_dt_compensacao = mktime(0,0,0,$dt_lnct_temp[1],$dt_lnct_temp[2],$dt_lnct_temp[0]);
			    $time_hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
			    if( $lancamento_temp['compensado']==1 || ($lancamento_temp['compensado']==0 && ($time_dt_compensacao >= $time_hoje)) ){
				    ++ $total_lancamentos;
				    if($lancamento_temp[tipo]=='R'){
					    $saldo_atual += $lancamento_temp[valor];
					    $total_entradas += $lancamento_temp[valor];
					    $cor = 'verde';
					    $valor = 'R$ '.$db->valorFormat($lancamento_temp[valor]);
				    }else{
					    $saldo_atual -= $lancamento_temp[valor];
					    $total_saidas += $lancamento_temp[valor];
					    $cor = 'vermelho';
					    $valor = '- R$ '.$db->valorFormat($lancamento_temp[valor]);
				    }
	
                    if($saldo_atual>=0)
                        $corSaldo = "verde";
                    else
                        $corSaldo = "vermelho";

				    //calcular atraso do lançamento
				    $dt_compensacao = $lancamento_temp['dt_lnct_format'];
				    if($lancamento_temp['compensado']==0){
					    $data = explode('-',$lancamento_temp['dt_lnct']);
					    $dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
					    $hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
					    $atraso = $hoje - $dt_limite;
					    //$atraso = date('d',$atraso);
					    $atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
				 
					    $dt_limite = date('Y-m-d',$dt_limite);
				 
					    if($dt_hoje > $dt_limite){
						    $situacao = "<font class='vermelho'>Atrasado ".$atraso." dia(s) </font>";
					    }else{
						    $situacao = "<font class='azul'> À realizar </font>";
					    }					
				    }else{
						    $situacao = "<font class='azul'> Realizado </font>";
				    }

				    $n +=1;	
				    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
					    $lancamentos .='
						    <tr bgcolor="'.$bg_color.'">
							    <td align="center">'.$dt_compensacao.'</td>
							    <td align="left">'.$lancamento_temp[descricao].'</td>
							    <!--<td align="center">'.$situacao.'</td>-->
							    <td align="right"><span class="'.$cor.'">'.$valor.'<span></td>
							    <td align="right"><span class="'.$corSaldo.'">R$ '.$db->valorFormat($saldo_atual).'<span></td>
						    </tr>
					    ';
				
			    }
		    }
		    //fim da busca dos lançamentos na tabela temporária

		    $relatorio .= '
		
			
			    <div class="bordaArredondadaTitulo6" align="center"> 
			
				    <div class="cabecalhoInterno">  
					    <div class="bordaArredondadaTitulo4">	<span class="spanCinza"> &bull; '.$array_conta['nome'].' - '.$array_conta['descricao'].' </span> </div>
					    <div class="bordaArredondadaTitulo2" align="right">	<span class="spanCinza"> Saldo Anterior: </span> R$ '.$db->valorFormat($saldo_anterior).' </div>
				    </div>
			
				    <table border="0" cellpadding="0" cellspacing="0">
				
					    <thead>
						    <tr>
							    <td width="100" align="center"><span class="spanCinza">VENC. / COMP.</span></td>
							    <td width="350" align="center"><span class="spanCinza">DESCRIÇÃO</span></td>
							    <!--<td width="100" align="center"><span class="spanCinza">SITUAÇÃO</span></td>-->
							    <td width="140" align="right"><span class="spanCinza">VALOR</span></td>
							    <td width="140" align="right"><span class="spanCinza">SALDO</span></td>
						    </tr>
					    </thead>
			
					    <tbody>
						    '.$lancamentos.'
					    </tbody>
	
				    </table>									
			
				    <div class="subTotal" align="right">  <span class="spanCinza"> Total de Entradas: </span> R$ '.$db->valorFormat($total_entradas).' <span class="spanCinza">&nbsp;&nbsp; Total de Saídas: </span> R$ '.$db->valorFormat($total_saidas).' </div>
			
			    </div><br>					
		    ';
	
		    $db->query("truncate table lancamentos_temp");

		}
		

		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }

	
	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="565">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>MOVIMENTAÇÃO FINANCEIRA</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Movimentação_Financeira',$array_dados['tp_print']);
	
    }

    //CATEGORIAS X CENTRO DE CUSTO
    //===========================================================================================

    function planoContasCentroResp($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	    }else{
		    $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		    $dt_referencia_ini = '01/'.$mes.'/'.$ano;
				
		    $mes_fim = $array_filtro_periodo["mesFim"];
		    $ano_fim = $array_filtro_periodo["anoFim"];
		    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
		    $dt_fim = date('Y-m-d',$dt_fim_ts);
		    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	    }

	    $hora_relatorio = date('H:i');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	
	    //situação dos lançamentos
	    $lancamento_situacao = $array_dados['lancamento_situacao'];
	    $compensado = "";
        $dataLancamentos = "";
	    if($lancamento_situacao==0){
		    $compensado = " and l.compensado = 0";
            $dataLancamentos = ' and l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'"';
	    }elseif($lancamento_situacao==1){
		    $compensado = " and l.compensado = 1";
            $dataLancamentos = ' and l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'"';
	    }elseif($lancamento_situacao==3){
            $dataLancamentos = ' and ((l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'") or (l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'" and compensado = 0))';
	    }

	    //contas financeiras do relatório	
	    $array_cf_id = explode(',',$array_dados["contas_financeiras"]);

	    //nível do plano de contas
	    $nivel_plc = $array_dados['nivel_plc'];
	
	    //nível do centro de responsabilidade
	    $nivel_ctr = $array_dados['nivel_ctr'];

	    //centro selecionado
	    $centro_pai_id = $array_dados['centro_resp_id'];
	    if(!$centro_pai_id)
		    $centro_pai_id = "";
	    $centro_pai_id_ini = $centro_pai_id;
	    if($centro_pai_id!=""){
		    $nivel_ini = $db->fetch_assoc('select nivel from centro_resp where id = '.$centro_pai_id);
		    $nivel_ini = $nivel_ini['nivel'];
		    $nivel_fim = $nivel_ini + $nivel_ctr - 1;
	    }

	    //em cada nível encontra os filhos analíticos do centro selecionado
	    if($centro_pai_id!=""){
		    $arr_ctr_nivel[] = $centro_pai_id;
		    $arr_aux = array();
		    $arr_aux2 = array();
		    $arr_filhos = $db->fetch_all_array("select id, tp_centro from centro_resp where centro_pai_id = ".$centro_pai_id);
		    $hasFilho = count($arr_filhos);
		    if($hasFilho){
			    while($hasFilho){
				    foreach($arr_filhos as $filho){
					    if($filho['tp_centro']==1)
						    array_push($arr_aux2,$filho['id']);
					    array_push($arr_aux,$filho['id']);
				    }
				    $centro_pai_id = join(',',$arr_aux);
				    $arr_ctr_nivel[] = $centro_pai_id;
				    $arr_filhos = $db->fetch_all_array("select id, tp_centro from centro_resp where centro_pai_id in (".$centro_pai_id.")");
				    $hasFilho = count($arr_filhos);
				    if($hasFilho)
					    $arr_aux = array();
			    }
		    }else{
			    $arr_aux2[] = $centro_pai_id;
		    }
	    }
	    $arr_ctr_analitico = "";
	    if(count($arr_aux2))
		    $arr_ctr_analitico = join(',',$arr_aux2);

	    //tabela temporária para lançamentos recorrentes
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_rcr_temp (
			    id int(11),
			    conta_id int(11),
			    dt_vencimento date NOT NULL,
			    valor decimal(10,2) NOT NULL,
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //busca lançamentos recorrentes
	    if($arr_ctr_analitico!=""){
		    $arr_lnct_rcr_id = array();
		    $arr_lnct_rcr = $db->fetch_all_array("select distinct lancamento_rcr_id from ctr_plc_lancamentos_rcr where centro_resp_id in (".$arr_ctr_analitico.")");
		    foreach($arr_lnct_rcr as $lnct_rcr_id){
			    $arr_lnct_rcr_id[] = $lnct_rcr_id['lancamento_rcr_id'];
		    }
		    $arr_lnct_rcr_id = join(',',$arr_lnct_rcr_id);
		    $lnct_rcr_id = 'and id in ('.$arr_lnct_rcr_id.')';
	    }

	    if($arr_lnct_rcr_id!="" || $centro_pai_id_ini==""){

		    if($lancamento_situacao!=1){

			    foreach($array_cf_id as $cf_id){

				    $query_lancamentos_rcr = mysql_query("
					    select id 
					    from lancamentos_recorrentes
					    where conta_id = ".$cf_id."
						    and dt_vencimento <= '".$dt_fim."'
						    ".$lnct_rcr_id."
				    ");
			
				    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
			 
					    $lancamento_rcr = $db->fetch_assoc("
						    select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
						    from lancamentos_recorrentes 
						    where id = ".$lancamento['id']
					    );
					
					    $dt_vencimento = date($lancamento_rcr['dt_vencimento']);
			
					    while($dt_vencimento <= $dt_fim){
					
						    if($dt_vencimento >= $dt_ini){
							    $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
						    }
						
						    if($lancamento_rcr['frequencia']>=30){
						
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
					
						    $lancamento_rcr[dt_vencimento] = $dt_vencimento;
					
					    }
				    }
			    }
		    }
	    }
	    //fim da busca por lançamentos recorrentes

	    //busca valores
	    $array_valores = array();	

	    $array_contas_analiticas = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    if($arr_ctr_analitico!=""){
		    $array_centros_analiticos = $db->fetch_all_array('
			    select id, hierarquia
			    from centro_resp
			    where id in (0,'.$arr_ctr_analitico.')
		    ');
	    }else{
		    $array_centros_analiticos = $db->fetch_all_array('
			    select id, hierarquia
			    from centro_resp
			    where tp_centro = 1
		    ');
	    }

	    foreach($array_contas_analiticas as $conta_analitica){
	
		    $conta_analitica_id = $conta_analitica['id'];
		    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);

		    foreach($array_centros_analiticos as $centro_analitico){
			
			    $valor_ctr_plc = 0;
			    $centro_analitico_id = $centro_analitico['id'];
			    $centro_hierarquia = explode(',',$centro_analitico['hierarquia']);
			
			    foreach($array_cf_id as $cf_id){

				    $valores = $db->fetch_all_array('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos cpl
					    join lancamentos l on cpl.lancamento_id = l.id
					    where cpl.centro_resp_id = '.$centro_analitico_id.'
						    and cpl.plano_contas_id = '.$conta_analitica_id.'
						    and l.conta_id = '.$cf_id.'
						    '.$compensado.'
						    '.$dataLancamentos.'
                        group by cpl.tp_lancamento');
				
                    //O foreach é uma correção para quando o cliente coloca valores positivos e negativos numa mesma conta do plano de contas, pois o certo seria a query retornar apenas um registro
                    foreach($valores as $valor){
                    
                        if($valor['tp_lancamento']=='R')
                            $valor_ctr_plc += $valor['valor'];
                        else
                            $valor_ctr_plc += $valor['valor']*(-1);
                    }
                
			    }

			    if($arr_lnct_rcr_id!="" || $centro_pai_id_ini==""){
                 
				    $valores_rcr = $db->fetch_all_array('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos_rcr cpl
					    join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
					    where cpl.centro_resp_id = '.$centro_analitico_id.'
						    and cpl.plano_contas_id = '.$conta_analitica_id.'
                        group by cpl.tp_lancamento');
                
                    //O foreach é uma correção para quando o cliente coloca valores positivos e negativos numa mesma conta do plano de contas, pois o certo seria a query retornar apenas um registro
                    foreach($valores_rcr as $valor_rcr){
                    
                        if($valor_rcr['tp_lancamento']=='R')
                            $valor_ctr_plc += $valor_rcr['valor'];
                        else
                            $valor_ctr_plc += $valor_rcr['valor']*(-1);
                    }
                }
				
			    foreach($conta_hierarquia as $conta_id){
				    foreach($centro_hierarquia as $centro_id){
					    if(isset($array_valores[$conta_id][$centro_id])){
						    $array_valores[$conta_id][$centro_id] += $valor_ctr_plc;
					    }else{

						    $array_valores[$conta_id][$centro_id] = $valor_ctr_plc;
					    }
				    }
			    }
			
		    }
	    }
	    //fim busca valores

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

        $array_plano_contas = $db->fetch_all_array('
            select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as decimal) as ordem1'.$ordem.'
            from plano_contas 
            where nivel <= '.$nivel_plc.'
            order by ordem1'.$orderBy);
        //end: busca plano de contas ordenado

        //start: busca centro de custo ordenado
        $maiorNivel = $db->fetch_assoc('select max(nivel) nivel from centro_resp');
        $maiorNivel = $maiorNivel['nivel'];

        $ordem = '';
        $orderBy = '';

        if($maiorNivel>1){
        
            $arrayOrderBy = array();
            $arrayOrdem = array();

            for($i=2;$i<$maiorNivel;$i++){
                array_push($arrayOrderBy,'ordem'.$i);
                array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_centro,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
            }

            array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_centro,".",-1) as decimal),0) as ordem'.$i);
            array_push($arrayOrderBy,'ordem'.$i);

            $ordem = ','.join(',',$arrayOrdem);
            $orderBy = ','.join(',',$arrayOrderBy);
        }

	    if($centro_pai_id!=""){
		
            $arr_ctr_id = array();
            foreach($arr_ctr_nivel as $ctr_id){
                $arr_ctr_id[] = $ctr_id;
            }
            $ctr_ids = join(',',$arr_ctr_id);

            $array_centro_resp = $db->fetch_all_array('select id, cod_centro, nome, tp_centro, nivel, cast(substring_index(cod_centro,".",1) as decimal) as ordem1'.$ordem.' from centro_resp where id in (0,'.$ctr_ids.') order by ordem1'.$orderBy);

	    }else{

            $array_centro_resp = $db->fetch_all_array('select id, cod_centro, nome, tp_centro, nivel, cast(substring_index(cod_centro,".",1) as decimal) as ordem1'.$ordem.' from centro_resp where nivel <= '.$nivel_ctr.' order by ordem1'.$orderBy);

	    }
        //end: busca centro de custo ordenado

	    //monta relatório

	    //valores com plano de contas
	    $i=0;
	    $col_limite = 4;
	    $qtd_centros	= count($array_centro_resp);

	    while($i<$qtd_centros){

		    $linhas = '';
		
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:'.$espc.'px;"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';
			
			    $colunas=1;
			    $j=$i;
			    while($colunas<=$col_limite && $j<$qtd_centros){
				    $centro_resp = $array_centro_resp[$j];
				    $valor = $array_valores[$plano_contas['id']][$centro_resp['id']];
				    $linhas .= '
					    <td align="right">R$ '.$db->valorFormat($valor).'</td>
				    ';
				    $colunas++;
				    $j++;
			    }
			    $linhas .= '</tr>';
		    }
	
		    $tbl_cabecalho = '
			    <tr>
				    <td width="300px"><span class="spanCinza">P.C / C.R</span></td>
		    ';
		    $colunas=1;
		    $j=$i;
		    while($colunas<=$col_limite && $j<$qtd_centros){
			    $centro_resp = $array_centro_resp[$j];
			    $tbl_cabecalho .= '<td align="right" width="200px"><span class="spanCinza">'.$centro_resp['cod_centro'].' - '.$centro_resp['nome'].'</span></td>';
			    $colunas++;
			    $j++;
		    }
		    $i = $i + $col_limite;
		    $tbl_cabecalho .= '</tr>';
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>

					    <tbody>
						    '.$linhas.'
					    </tbody>
				    </table>
			    </div><br>
		    ';

	    }//fim monta relatório
	
		$db->query("drop table lancamentos_rcr_temp");
		
		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>CATEGORIAS X CENTRO DE CUSTO</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="442" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="442" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Plano_Contas_x_Centro_Resp',$array_dados['tp_print'],"A4-L");

	    //return $relatorio;

    }

    /*
    ===========================================================================================
    FLUXO DE CAIXA DIÁRIO
    ===========================================================================================
    */

    function fluxoCaixaDiario($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	    }else{
		    $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		    $dt_fim = mktime(0,0,0,$mes+1,'00',$ano);
		    $dt_fim = date('Y-m-d',$dt_fim);
		    $dt_referencia_ini = '01/'.$mes.'/'.$ano;
		    $dt_referencia_fim = $db->sql_to_data($dt_fim);
	    }

	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";

	    //=================  CALCULA VALORES DE HOJE ATÉ A DATA INICIAL DO RELATÓRIO (SALDO ANTERIOR) ==========================================================

	    //valores compensados
	
	    $query_receita = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "R"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $receita_realizada = $db->fetch_assoc($query_receita);
	
	    $query_despesa = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "P"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $despesa_realizada = $db->fetch_assoc($query_despesa);
	    //fim valores compensados
	
	    //valores em aberto

	    //busca lançamentos recorrentes
	    $receita_recorrente = 0;
	    $despesa_recorrente = 0;
	
	    $query_lancamentos_rcr = mysql_query("
		    select id 
		    from lancamentos_recorrentes
		    where dt_vencimento < '".$dt_ini."'
	    ");

	    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
 
		    $lancamento_rcr = $db->fetch_assoc("
			    select id, tipo, dt_vencimento, valor, frequencia, dia_mes
			    from lancamentos_recorrentes 
			    where id = ".$lancamento['id']
		    );
		
		    $dt_vencimento = date($lancamento_rcr['dt_vencimento']);

		    while($dt_vencimento < $dt_ini){
		
			    if($dt_vencimento >= $dt_hoje){
				    if($lancamento_rcr['tipo']=='R'){
					    $receita_recorrente += $lancamento_rcr['valor'];
				    }elseif($lancamento_rcr['tipo']=='P'){
					    $despesa_recorrente += $lancamento_rcr['valor'];
				    }
			    }
			
			    if($lancamento_rcr['frequencia']>=30){
			
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
		    }
	    }
	    //fim da busca por lançamentos recorrentes
	
	    //busca valores programados
	
	    $receita_programada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos
		    where compensado = 0
			    and tipo = "R"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');

	    $despesa_programada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos
		    where compensado = 0
			    and tipo = "P"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');

	    //fim busca valores programados
	
	    //busca valores empenhados
	    $receita_empenhada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos_plnj
		    where tipo = "R"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');
			
	    $despesa_empenhada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos_plnj
		    where tipo = "P"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');
	    //fim busca valores empenhados
	
	    //fim valores em aberto

	    $saldo_anterior = $receita_realizada['valor'] + $receita_recorrente + $receita_programada['valor'] + $receita_empenhada['valor'] - $despesa_empenhada['valor'] - $despesa_programada['valor'] - $despesa_realizada['valor'] - $despesa_recorrente;
	    $array_saldo_anterior[] = $saldo_anterior;

	    //===============================  CALCULA VALORES À PARTIR DA DATA INICIAL DO RELATÓRIO ==============================================================


	    //tabela temporária para lançamentos recorrentes
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_rcr_temp (
			    id int(11),
			    conta_id int(11),
			    dt_vencimento date NOT NULL,
			    valor decimal(10,2) NOT NULL,
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //nível do plano de contas
	    $nivel_plc = $array_dados['nivel_plc'];

	    //busca lançamentos recorrentes
	    $query_lancamentos_rcr = mysql_query("
		    select id 
		    from lancamentos_recorrentes
		    where dt_vencimento <= '".$dt_fim."'
	    ");

	    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
 
		    $lancamento_rcr = $db->fetch_assoc("
			    select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
			    from lancamentos_recorrentes 
			    where id = ".$lancamento['id']
		    );
		
		    $dt_vencimento = date($lancamento_rcr['dt_vencimento']);
		
		    while($dt_vencimento <= $dt_fim){
		
			    if($dt_vencimento >= $dt_hoje){
				    $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
			    }
			
			    if($lancamento_rcr['frequencia']>=30){
			
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

	    //fim da busca por lançamentos recorrentes

	    //busca valores para saldo do plano de contas
	    $array_valores = array();	
	    $array_dias = array();

	    $array_contas_analiticas = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    $dia = strtotime($dt_ini);
	    $dia_fim = strtotime($dt_fim);
	    $i = 0; //índice para referenciar os dias no array de saldo final

	    while($dia<=$dia_fim){

		    $dia = date('Y-m-d',$dia);
		    $array_dias[] = $dia;
		
	
		    foreach($array_contas_analiticas as $conta_analitica){
		
			    $conta_analitica_id = $conta_analitica['id'];
			    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
	
			    $valor_plc = 0; //valor do plano de contas acumulado por período
	
			    //lançamentos compensados
				    $valor_compensado = $db->fetch_assoc('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos cpl
					    join lancamentos l on cpl.lancamento_id = l.id
					    where cpl.plano_contas_id = '.$conta_analitica_id.'
						    and l.compensado = 1
						    and l.dt_compensacao = "'.$dia.'"
					    group by cpl.tp_lancamento');

				    $valor_plc += $valor_compensado['valor'];
				    ($valor_compensado['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_compensado['valor'] : $array_saldo_final[$i] -= $valor_compensado['valor'];
	
			    //lançamentos abertos e não vencidos
				
				    if($dia >= $dt_hoje){
				
				    //recebimentos ou pagamentos programados
					    $valor_aberto = $db->fetch_assoc('
						    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
						    from ctr_plc_lancamentos cpl
						    join lancamentos l on cpl.lancamento_id = l.id
						    where cpl.plano_contas_id = '.$conta_analitica_id.'
							    and l.compensado = 0
							    and l.dt_vencimento = "'.$dia.'"
						    group by cpl.tp_lancamento');

					    $valor_plc += $valor_aberto['valor'];
					    ($valor_aberto['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_aberto['valor'] : $array_saldo_final[$i] -= $valor_aberto['valor'];
				
				    //recebimentos ou pagamentos empenhados
					    $valor_empenho = $db->fetch_assoc('
						    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
						    from ctr_plc_lancamentos_plnj cpl
						    join lancamentos_plnj l on cpl.lancamento_plnj_id = l.id
						    where cpl.plano_contas_id = '.$conta_analitica_id.'
							    and l.dt_vencimento = "'.$dia.'"
						    group by cpl.tp_lancamento');
						
					    $valor_plc += $valor_empenho['valor'];
					    ($valor_empenho['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_empenho['valor'] : $array_saldo_final[$i] -= $valor_empenho['valor'];
				
				    }
				
			    //lançamentos recorrentes
				    $valor_rcr = $db->fetch_assoc('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos_rcr cpl
					    join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
					    where cpl.plano_contas_id = '.$conta_analitica_id.'
						    and l.dt_vencimento = "'.$dia.'"
					    group by cpl.tp_lancamento');
						
				    $valor_plc += $valor_rcr['valor'];
				    ($valor_rcr['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_rcr['valor'] : $array_saldo_final[$i] -= $valor_rcr['valor'];
	
			    //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
				    foreach($conta_hierarquia as $conta_id){
					    if(isset($array_valores[$conta_id][$i])){
						    $array_valores[$conta_id][$i] += $valor_plc;
					    }else{
						    $array_valores[$conta_id][$i] = $valor_plc;
					    }
				    }
					
		    }//fim busca valores para o plano de contas
		
		    //atualiza o saldo inicial do período seguinte(dia ou mês)
		    $array_saldo_final[$i] += $array_saldo_anterior[$i];
		    $array_saldo_anterior[$i+1] = $array_saldo_final[$i];

		    $i++;
		    $dia = strtotime("+1 days",strtotime($dia));

	    }

	    $qtd_dias = count($array_dias);
		
	    //retira a última posição do array de saldo anterior que fica além do período especificado
	    array_pop($array_saldo_anterior);

	    //ordena plano de contas
	    $col_ordem = "";
	    $i=1;
	    while($i<=$nivel_plc){
		    $col_ordem.= 'ordem'.$i.' smallint(2),';
		    $i++;
	    }
	    $db->query("
		    CREATE TEMPORARY TABLE plc_temp (
			    id int(11),
			    cod_conta varchar(20),
			    nome varchar(30),
			    ".$col_ordem."
			    nivel smallint(2),
			    posicao smallint(4)
		    ) ENGINE=MEMORY
	    ");

	    $array_plano_contas = array();
	    $arr_plc_n1 = $db->fetch_all_array('select id, cod_conta, nome, nivel, posicao from plano_contas where nivel = 1');
	    foreach($arr_plc_n1 as $plc_n1){
		    $arr_cod_conta = explode('.',$plc_n1['cod_conta']);
		    $i=1;
		    while($i<=count($arr_cod_conta)){
			    $plc_n1['ordem'.$i] = $arr_cod_conta[$i-1];
			    $i++;
		    }
		    $db->query_insert('plc_temp',$plc_n1);
		    if($nivel_plc>1){
			    $cont_nivel = 2;
			    $arr_filhos = $db->fetch_all_array("select id, cod_conta, nome, nivel, posicao from plano_contas where nivel = 2 and conta_pai_id = ".$plc_n1['id']);
			    $hasFilho = count($arr_filhos);
			    while($hasFilho && $cont_nivel<=$nivel_plc){
				    $arr_conta_filho_id = array();
				    foreach($arr_filhos as $filho){
					    $arr_cod_conta = explode('.',$filho['cod_conta']);
					    $i=1;
					    while($i<=count($arr_cod_conta)){
						    $filho['ordem'.$i] = $arr_cod_conta[$i-1];
						    $i++;
					    }
					    $db->query_insert('plc_temp',$filho);
					    $arr_conta_filho_id[] = $filho['id'];
				    }
				    $cont_nivel++;
				    if($cont_nivel<=$nivel_plc){
					    $str_conta_filho_id = join(',',$arr_conta_filho_id);
					    $arr_filhos = $db->fetch_all_array("select id, cod_conta, nome, nivel, posicao from plano_contas where conta_pai_id in (".$str_conta_filho_id.")");
					    $hasFilho = count($arr_filhos);
				    }
			    }
		    }
	    }

	    $col_ordem = "";
	    $i=1;
	    while($i<=$nivel_plc){
		    $col_ordem.= 'ordem'.$i.',';
		    $i++;
	    }
	    $col_ordem = substr($col_ordem,0,strlen($col_ordem)-1);
	    $arr_plc_ordem = $db->fetch_all_array('select * from plc_temp order by '.$col_ordem);
	    foreach($arr_plc_ordem as $conta){
		    $array_plano_contas[] = $conta;
	    }
	    $db->query('drop table plc_temp');
	    $arr_plc_ordem = null;
	    unset($arr_plc_ordem);
	    //fim ordena plano de contas

	    //monta relatório
	    $relatorio = '';
	    $i=0;
	    $col_limite = 5;

	    while($i<$qtd_dias){

		    $linhas = '';
		
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:'.$espc.'px;"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';
			
			    $colunas=1;
			    $j=$i;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $linhas .= '
					    <td align="right">R$ '.$db->valorFormat($valor).'</td>
				    ';
				    $colunas++;
				    $j++;
			    }
			
			    $linhas .= '</tr>';
		    }
		    //fim monta valores do plano de contas na tabela
	
		    //monta cabeçalho da tabela
			    //$cabecalho_saldo_total = '<tr bgcolor=""><td align="left"><span class="spanCinza"><b>Saldo anterior</b><span style="font-size:11px;"> (Caixa + Bancos)</span></span></td>';
			    //foreach($array_saldo_anterior as $saldo_total){
				    //($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
				    //$cabecalho_saldo_total .= '<td align="right" style="color:'.$cor.'"><b>R$ '.$db->valorFormat($saldo_total).'</b></td>';
			    //}
			    //$cabecalho_saldo_total .= "</tr>";
			    $tbl_cabecalho = '';//$cabecalho_saldo_total;
		    //fim monta cabeçalho da tabela
		
		    //monta cabeçalho secundário da tabela
			    $tbl_cabecalho .= '<tr bgcolor="">';
			    //$tbl_cabecalho .= '<td rowspan="2" align="left" style="width:200px"><span class="spanCinza"><b>Plano de contas / Período</b></span></td>';
			    $tbl_cabecalho .= '<td align="left">Contas</td>';
			    $colunas=1;
			    $j=$i;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $dia = $array_dias[$j];
				    $dia = $db->sql_to_data($dia);
				    $tbl_cabecalho .= '<td align="right"><span class="spanCinza">'.$dia.'</span></td>';
				    $colunas++;
				    $j++;
			    }
			    $tbl_cabecalho .= "</tr>";
			    //$tbl_cabecalho .= '<tr bgcolor="">';
			    //$hoje = strtotime(date('Y-m-d'));
			    //for($i=0;$i<$qtd_dias;$i++){
				    //$dia = $array_dias[$i];
				    //$dia = strtotime($dia);
				    //if($dia<$hoje){
					    //$tbl_cabecalho.= '<td align="center" style="min-width:150px"><span class="spanCinza">Realizado</span></td>';
				    //}elseif($dia==$hoje){
					    //$tbl_cabecalho.= '<td align="center" style="min-width:150px"><span class="spanCinza">Previsto / Realizado</span></td>';
				    //}else{
					    //$tbl_cabecalho.= '<td align="center" style="min-width:150px"><span class="spanCinza">Previsto</span></td>';
				    //}
			    //}
			    //$tbl_cabecalho .= "</tr>";
		    //fim monta cabeçalho secundário da tabela
		
			    $rodape_saldo_total = '
				    <tr bgcolor="">
					    <td><span class="spanCinza">Saldo Final <span style="font-size:11px;">(Caixa + Bancos)</span></span></td>
			    ';
			    $colunas=1;
			    $j=$i;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $saldo_total = $array_saldo_final[$j];
				    ($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
				    $rodape_saldo_total .= '<td align="right" style="color:'.$cor.'">R$ '.$db->valorFormat($saldo_total).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $rodape_saldo_total .= "</tr>";
			    $tbl_rodape = $rodape_saldo_total;//.$tbl_rodape;
			    //fim monta saldo final no rodapé da tabela
			    $i = $i + $col_limite;
			
			    $relatorio .= '
				    <div class="bordaArredondadaTitulo6" align="center">
					    <table border="0" cellpadding="0" cellspacing="0">
						    <thead>
							    '.$tbl_cabecalho.'
						    </thead>
						    <tbody>
							    '.$linhas.$tbl_rodape.'
						    </tbody>
					    </table>
				    </div><br>
			    ';
	    }
	
	    ($array_saldo_anterior[0]>=0)? $cor = "#009900" : $cor = "#FF0000";
	    $relatorio = '
		    <div class="bordaArredondadaTituloH3">	<span class="spanCinza"> Categorias / Período </span> </div>
		    <div class="bordaArredondadaTituloH3" align="right">	<span class="spanCinza"> Saldo Anterior <span style="font-size:11px;"> (Caixa + Bancos)</span> </span><span style="color:'.$cor.'">R$ '.$db->valorFormat($array_saldo_anterior[0]).'</span></div>
	    '.$relatorio;//fim monta relatório

		$db->query("drop table lancamentos_rcr_temp");
		

		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>FLUXO DE CAIXA DIÁRIO</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="442" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="442" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Fluxo_de_Caixa_Diário',$array_dados['tp_print'],"A4-L");

	    //return $relatorio;

    }

    /*
    ===========================================================================================
    FLUXO DE CAIXA MENSAL
    ===========================================================================================
    */

    function fluxoCaixaMensal($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){

		    $dt_ini = $array_filtro_periodo["dt_ini"];
		    $dt_ini = explode('/',$dt_ini);
		    $mes_ini = $dt_ini[1];
		    $ano_ini = $dt_ini[2];
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		
		    $dt_fim = $array_filtro_periodo["dt_fim"];
		    $dt_fim = explode('/',$dt_fim);
		    $mes_fim = $dt_fim[1];
		    $ano_fim = $dt_fim[2];
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		
		    $array_anos_meses = array();
		    $array_meses = array();
		    $array_anos = array();
		    $i = (int)$ano_ini;
		    $j = (int)$mes_ini;
		    $k = 12;
		    $qtd_meses = 0;
		    while($i<=$ano_fim){
			    if($i==$ano_fim){
				    $k = $mes_fim;
			    }
			    while($j<=$k){
				    $array_meses[] = $j;
				    $array_anos_meses[$i][] = $j;
				    $j++;
				    $qtd_meses++;
			    }
			    $array_anos[] = $i;
			    $i++;
			    $j=1;
		    }

		    $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;
		    $dt_fim = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
		    $dt_fim = date('Y-m-d',$dt_fim);
		    $dt_referencia_fim = $db->sql_to_data($dt_fim);

	    }else{

		    $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		    $dt_fim = mktime(0,0,0,$mes+1,'00',$ano);
		    $dt_fim = date('Y-m-d',$dt_fim);
		    $array_meses[] = (int)$mes;
		    $array_anos[] = $ano;
		    $array_anos_meses[$ano][0] = (int)$mes;
		    $qtd_meses = 1;
		    $dt_referencia_ini = '01/'.$mes.'/'.$ano;
		    $dt_referencia_fim = $db->sql_to_data($dt_fim);

	    }

	    $array_meses_report = $array_meses;

	    $array_meses_nome = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');
	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	    $mes_atual = date('m');
	    $ano_atual = date('Y');
	    $orcamento_id = $array_dados['orcamento_id'];

	    //=================  CALCULA VALORES DE HOJE ATÉ A DATA INICIAL DO RELATÓRIO (SALDO ANTERIOR) ==========================================================

	    //saldo inicial das contas
	
	    $saldo_inicial = $db->fetch_assoc("select sum(vl_saldo_inicial) saldo_inicial_total from contas");

	    //valores compensados

	    $query_receita = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "R"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $receita_realizada = $db->fetch_assoc($query_receita);
	
	    $query_despesa = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "P"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $despesa_realizada = $db->fetch_assoc($query_despesa);
	    //fim valores compensados
	
	    //valores em aberto

	    //busca lançamentos recorrentes
	    $receita_recorrente = 0;
	    $despesa_recorrente = 0;
	
	    $query_lancamentos_rcr = mysql_query("
		    select id 
		    from lancamentos_recorrentes
		    where dt_vencimento < '".$dt_ini."'
	    ");

	    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
 
		    $lancamento_rcr = $db->fetch_assoc("
			    select id, tipo, dt_vencimento, valor, frequencia, dia_mes
			    from lancamentos_recorrentes 
			    where id = ".$lancamento['id']
		    );
		
		    $dt_vencimento = date($lancamento_rcr['dt_vencimento']);

		    while($dt_vencimento < $dt_ini){
		
			    if($dt_vencimento >= $dt_hoje){
				    if($lancamento_rcr['tipo']=='R'){
					    $receita_recorrente += $lancamento_rcr['valor'];
				    }elseif($lancamento_rcr['tipo']=='P'){
					    $despesa_recorrente += $lancamento_rcr['valor'];
				    }
			    }
			
			    if($lancamento_rcr['frequencia']>=30){
			
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
		    }
	    }
	    //fim da busca por lançamentos recorrentes
	
	    //busca valores programados
	
	    $receita_programada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos
		    where compensado = 0
			    and tipo = "R"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');

	    $despesa_programada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos
		    where compensado = 0
			    and tipo = "P"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');

	    //fim busca valores programados
	
	    //busca valores empenhados
	    $receita_empenhada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos_plnj
		    where tipo = "R"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');
			
	    $despesa_empenhada = $db->fetch_assoc('
		    select sum(IFNULL(valor,0)) valor
		    from lancamentos_plnj
		    where tipo = "P"
			    and dt_vencimento >= "'.$dt_hoje.'"
			    and dt_vencimento < "'.$dt_ini.'"');
	    //fim busca valores empenhados
	
	    //fim valores em aberto

	    $saldo_anterior = $saldo_inicial['saldo_inicial_total'] + $receita_realizada['valor'] + $receita_recorrente + $receita_programada['valor'] + $receita_empenhada['valor'] - $despesa_empenhada['valor'] - $despesa_programada['valor'] - $despesa_realizada['valor'] - $despesa_recorrente;
	    $array_saldo_anterior[] = $saldo_anterior;

	    //===============================  CALCULA VALORES À PARTIR DA DATA INICIAL DO RELATÓRIO ==============================================================

	    //tabela temporária para lançamentos recorrentes
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_rcr_temp (
			    id int(11),
			    conta_id int(11),
			    dt_vencimento date NOT NULL,
			    valor decimal(10,2) NOT NULL,
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //nível do plano de contas
	    $nivel_plc = $array_dados['nivel_plc'];
	
	    //busca lançamentos recorrentes
	    $query_lancamentos_rcr = mysql_query("
		    select id 
		    from lancamentos_recorrentes
		    where dt_vencimento <= '".$dt_fim."'
	    ");

	    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
 
		    $lancamento_rcr = $db->fetch_assoc("
			    select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
			    from lancamentos_recorrentes 
			    where id = ".$lancamento['id']
		    );
		
		    $dt_vencimento = date($lancamento_rcr['dt_vencimento']);

		    while($dt_vencimento <= $dt_fim){
		
			    if($dt_vencimento >= $dt_hoje){
				    $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
			    }
			
			    if($lancamento_rcr['frequencia']>=30){
			
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
	    //fim da busca por lançamentos recorrentes

	    //busca valores para saldo do plano de contas
	    $array_valores = array();	

	    $array_contas_analiticas = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    $i = 0;

	    foreach($array_anos_meses as $ano => $array_meses){
		
		    foreach($array_meses as $mes){
			
			    foreach($array_contas_analiticas as $conta_analitica){
			
				    $conta_analitica_id = $conta_analitica['id'];
				    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
		
				    $valor_plc = 0; //valor do plano de contas acumulado por período
	
				    //lançamentos compensados
				    $valor_compensado = $db->fetch_assoc('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos cpl
					    join lancamentos l on cpl.lancamento_id = l.id
					    where cpl.plano_contas_id = '.$conta_analitica_id.'
						    and l.compensado = 1
						    and month(l.dt_compensacao) = '.$mes.'
						    and year(l.dt_compensacao) = '.$ano.'
					    group by cpl.tp_lancamento');
				    $valor_plc += $valor_compensado['valor'];
				    ($valor_compensado['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_compensado['valor'] : $array_saldo_final[$i] -= $valor_compensado['valor'];
	
				    //lançamentos abertos e não vencidos
				    if($mes >= $mes_atual && $ano >= $ano_atual){
					
					    //recebimentos ou pagamentos programados
					    $valor_aberto = $db->fetch_assoc('
						    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
						    from ctr_plc_lancamentos cpl
						    join lancamentos l on cpl.lancamento_id = l.id
						    where cpl.plano_contas_id = '.$conta_analitica_id.'
							    and l.compensado = 0
							    and month(l.dt_vencimento) = '.$mes.'
							    and year(l.dt_vencimento) = '.$ano.'
							    and l.dt_vencimento >= "'.$dt_hoje.'"
						    group by cpl.tp_lancamento');
					    $valor_plc += $valor_aberto['valor'];
					    ($valor_aberto['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_aberto['valor'] : $array_saldo_final[$i] -= $valor_aberto['valor'];
					
					    //recebimentos ou pagamentos empenhados
					    $valor_empenho = $db->fetch_assoc('
						    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
						    from ctr_plc_lancamentos_plnj cpl
						    join lancamentos_plnj l on cpl.lancamento_plnj_id = l.id
						    where cpl.plano_contas_id = '.$conta_analitica_id.'
							    and month(l.dt_vencimento) = '.$mes.'
							    and year(l.dt_vencimento) = '.$ano.'
							    and l.dt_vencimento >= "'.$dt_hoje.'"
						    group by cpl.tp_lancamento');
					    $valor_plc += $valor_empenho['valor'];
					    ($valor_empenho['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_empenho['valor'] : $array_saldo_final[$i] -= $valor_empenho['valor'];
					
				    }
	
				    //lançamentos recorrentes
				    $valor_rcr = $db->fetch_assoc('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos_rcr cpl
					    join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
					    where cpl.plano_contas_id = '.$conta_analitica_id.'
						    and month(l.dt_vencimento) = '.$mes.'
						    and year(l.dt_vencimento) = '.$ano.'
					    group by cpl.tp_lancamento');
				    $valor_plc += $valor_rcr['valor'];
				    ($valor_rcr['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_rcr['valor'] : $array_saldo_final[$i] -= $valor_rcr['valor'];
	
				    //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
				    foreach($conta_hierarquia as $conta_id){
					    if(isset($array_valores[$conta_id][$i])){
						    $array_valores[$conta_id][$i] += $valor_plc;
					    }else{
						    $array_valores[$conta_id][$i] = $valor_plc;
					    }
				    }
				
			    }//fim busca valores para o plano de contas
	
			    //atualiza o saldo inicial do período seguinte(dia ou mês)
			    $array_saldo_final[$i] += $array_saldo_anterior[$i];
			    $array_saldo_anterior[$i+1] = $array_saldo_final[$i];
			
			    //incrementa o mês
			    $i++;

		    }//fim do for dos meses

	    }//fim do for dos anos

	    //retira a última posição do array de saldo anterior que fica além do período especificado
	    array_pop($array_saldo_anterior);

	    if($orcamento_id!=""){
		    $mes_texto = array('jan','fev','mar','abr','mai','jun','jul','ago','sete','outu','nov','dez');
		    $k = 0;
		    foreach($array_anos_meses as $ano => $array_meses){
			
			    $array_meses_txt = array();
			    foreach($array_meses as $mes){
				    $array_meses_txt[] = $mes_texto[$mes-1];
			    }
			    $j = count($array_meses_txt);
			    $array_meses_txt = join(',',$array_meses_txt);
						
			    foreach($array_contas_analiticas as $conta_analitica){
				
				    $i = $k;
				
				    $conta_analitica_id = $conta_analitica['id'];
				    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
	
				    $orcamento = $db->fetch_assoc("select ".$array_meses_txt." from orcamentos_plnj_vl where orcamento_id = ".$orcamento_id." and plano_contas_id = ".$conta_analitica_id." and ano = ".$ano);
				    if(!empty($orcamento)){
					    foreach($orcamento as $valor){
						    foreach($conta_hierarquia as $conta_id){
							    if(isset($array_orcamento_valores[$conta_id][$i])){
								    $array_orcamento_valores[$conta_id][$i] += $valor;
							    }else{
								    $array_orcamento_valores[$conta_id][$i] = $valor;
							    }
						    }
						    $i++;
					    }
				    }else{
					    while($i<=$j){
						    $array_orcamento_valores[$conta_analitica_id][$i] = 0;
						    $i++;
					    }
				    }
			    }
			    $k = $j;
		    }
	    }
	    //fim busca valores

	    //ordena plano de contas
	    $col_ordem = "";
	    $i=1;
	    while($i<=$nivel_plc){
		    $col_ordem.= 'ordem'.$i.' smallint(2),';
		    $i++;
	    }
	    $db->query("
		    CREATE TEMPORARY TABLE plc_temp (
			    id int(11),
			    cod_conta varchar(20),
			    nome varchar(30),
			    ".$col_ordem."
			    nivel smallint(2),
			    posicao smallint(4)
		    ) ENGINE=MEMORY
	    ");

	    $array_plano_contas = array();
	    $arr_plc_n1 = $db->fetch_all_array('select id, cod_conta, nome, nivel, posicao from plano_contas where nivel = 1');
	    foreach($arr_plc_n1 as $plc_n1){
		    $arr_cod_conta = explode('.',$plc_n1['cod_conta']);
		    $i=1;
		    while($i<=count($arr_cod_conta)){
			    $plc_n1['ordem'.$i] = $arr_cod_conta[$i-1];
			    $i++;
		    }
		    $db->query_insert('plc_temp',$plc_n1);
		    if($nivel_plc>1){
			    $cont_nivel = 2;
			    $arr_filhos = $db->fetch_all_array("select id, cod_conta, nome, nivel, posicao from plano_contas where nivel = 2 and conta_pai_id = ".$plc_n1['id']);
			    $hasFilho = count($arr_filhos);
			    while($hasFilho && $cont_nivel<=$nivel_plc){
				    $arr_conta_filho_id = array();
				    foreach($arr_filhos as $filho){
					    $arr_cod_conta = explode('.',$filho['cod_conta']);
					    $i=1;
					    while($i<=count($arr_cod_conta)){
						    $filho['ordem'.$i] = $arr_cod_conta[$i-1];
						    $i++;
					    }
					    $db->query_insert('plc_temp',$filho);
					    $arr_conta_filho_id[] = $filho['id'];
				    }
				    $cont_nivel++;
				    if($cont_nivel<=$nivel_plc){
					    $str_conta_filho_id = join(',',$arr_conta_filho_id);
					    $arr_filhos = $db->fetch_all_array("select id, cod_conta, nome, nivel, posicao from plano_contas where conta_pai_id in (".$str_conta_filho_id.")");
					    $hasFilho = count($arr_filhos);
				    }
			    }
		    }
	    }

	    $col_ordem = "";
	    $i=1;
	    while($i<=$nivel_plc){
		    $col_ordem.= 'ordem'.$i.',';
		    $i++;
	    }
	    $col_ordem = substr($col_ordem,0,strlen($col_ordem)-1);
	    $arr_plc_ordem = $db->fetch_all_array('select * from plc_temp order by '.$col_ordem);
	    foreach($arr_plc_ordem as $conta){
		    $array_plano_contas[] = $conta;
	    }
	    $db->query('drop table plc_temp');
	    $arr_plc_ordem = null;
	    unset($arr_plc_ordem);
	    //fim ordena plano de contas

	    //monta relatório
	    $relatorio = '';
	    $i=0;
	    $col_limite = 3; 
	    $ano_ini = $array_anos[0];
	    $qtd_meses_ano_ini = count($array_anos_meses[$ano]);
	
	    while($i<$qtd_meses){
		    $col_restante = $qtd_meses - $i;
		    if($col_restante==1){$w_conta='60%'; $w_valor='20%'; $p_mes='12%';}elseif($col_restante==2){$w_conta='32%'; $w_valor='17%'; $p_mes='10%';}else{$w_conta='22%'; $w_valor='13%'; $p_mes='8%';}
		
		    $linhas = '';
		    $n = 0;
		
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:'.$espc.'px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $linhas .= '
					    <td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
					    <td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    ';
				    $colunas++;
				    $j++;
			    }
			    $linhas .= '</tr>';
		    }
		    //fim monta valores do plano de contas na tabela
		
		    //$cabecalho_saldo_total = '<tr bgcolor="#F5F5F5"><td><b>Saldo anterior</b><div style="font-size:11px;display:inline;width:200px"> (Caixa + Bancos)</div></td>';
		    //foreach($array_saldo_anterior as $saldo_total){
			    //($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
			    //$cabecalho_saldo_total .= '<td colspan="2" align="right" style="color:'.$cor.'"><b>R$ '.$db->valorFormat($saldo_total).'</b></td>';
		    //}
		    //$cabecalho_saldo_total .= "</tr>";
		    //$tbl_cabecalho = $cabecalho_saldo_total;//.$tbl_cabecalho;
		    //fim monta cabeçalho da tabela
		
		    //monta cabeçalho secundário da tabela
		    $tbl_cabecalho = '<tr>';
		    //$tbl_cabecalho .= '<td rowspan="2" align="center" style="width:200px"><b>Plano de contas / Período</b></td>';
		    $tbl_cabecalho .= '<td rowspan="2" align="left">Contas</td>';

		    $colunas=1;
		    $j=$i;
		    $cont_meses = 1;
		    $ano = $ano_ini;
		    $qtd_meses_ano = $qtd_meses_ano_ini;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if($cont_meses>$qtd_meses_ano){
				    $ano++;
				    $qtd_meses_ano = count($array_anos_meses[$ano]);
				    $cont_meses = 1;
			    }
			    $num_mes = $array_meses_report[$j];
			    $nome_mes = $array_meses_nome[$num_mes];
			    $tbl_cabecalho .= '<td colspan="2" align="center" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';
			    $colunas++;
			    $j++;
			    $cont_meses++;
		    }

		    //foreach($array_anos_meses as $ano => $array_meses){
			    //foreach($array_meses as $mes){
				    //$nome_mes = $array_meses_nome[$mes];
				    //$tbl_cabecalho .= '<td colspan="2" align="center" style="width:300px">'.$nome_mes.' / '.$ano.'</td>';
			    //}
		    //}
		    $tbl_cabecalho .= "</tr>";
		    $tbl_cabecalho .= '<tr>';
		
		    //for($i=0;$i<count($array_meses);$i++){

		    $colunas=1;
		    $j=$i;
		    $cont_meses = 1;
		    $ano = $ano_ini;
		    $qtd_meses_ano = $qtd_meses_ano_ini;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if($cont_meses>$qtd_meses_ano){
				    $ano++;
				    $qtd_meses_ano = count($array_anos_meses[$ano]);
				    $cont_meses = 1;
				    $ano_ini++;
				    $qtd_meses_ano_ini = count($array_anos_meses[$ano_ini]);
			    }
			    $mes = $array_meses_report[$j];

			    $tbl_cabecalho.= '<td align="right">Orçamento</td>';
			    if( $ano<$ano_atual || ($ano==$ano_atual && $mes<$mes_atual) ){
				    $tbl_cabecalho.= '<td align="right">Realizado</td>';
			    }elseif($mes==$mes_atual){
				    $tbl_cabecalho.= '<td align="right">Previsto / Realizado</td>';
			    }else{
				    $tbl_cabecalho.= '<td align="right">Previsto</td>';
			    }

			    $colunas++;
			    $j++;
			    $cont_meses++;
		    }
		
		    //foreach($array_anos_meses as $ano => $array_meses){
			    //foreach($array_meses as $mes){
	
				    //$tbl_cabecalho.= '<td align="right" style="width:">Orçamento</td>';
				    //if( $ano<$ano_atual || ($ano==$ano_atual && $mes<$mes_atual) ){
					    //$tbl_cabecalho.= '<td align="right" style="width:">Realizado</td>';
				    //}elseif($mes==$mes_atual){
					    //$tbl_cabecalho.= '<td align="right" style="width:">Previsto / Realizado</td>';
				    //}else{
					    //$tbl_cabecalho.= '<td align="right" style="width:">Previsto</td>';
				    //}
	
			    //}
		    //}
	
		    //}
		    $tbl_cabecalho .= "</tr>";
		    //fim monta cabeçalho secundário da tabela
		
		    $rodape_saldo_total = '
			    <tr>
				    <td><span class="spanCinza">Saldo Final <span style="font-size:11px;">(Caixa + Bancos)</span></span></td>
		    ';
		    $colunas=1;
		    $j=$i;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $saldo_total = $array_saldo_final[$j];
			    ($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
			    $rodape_saldo_total .= '<td></td><td align="right" style="color:'.$cor.'">R$ '.$db->valorFormat($saldo_total).'</td>';
			    $colunas++;
			    $j++;
		    }
		    $rodape_saldo_total .= "</tr>";
		    $tbl_rodape = $rodape_saldo_total;//.$tbl_rodape;
		    //fim monta saldo final no rodapé da tabela
		    $i = $i + $col_limite;
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>
					    <tbody>
						    '.$linhas.$tbl_rodape.'
					    </tbody>
				    </table>
			    </div><br>
		    ';
	    }

	    ($array_saldo_anterior[0]>=0)? $cor = "#009900" : $cor = "#FF0000";
	    $relatorio = '
		    <div class="bordaArredondadaTituloH3">	<span class="spanCinza"> Categorias / Período </span> </div>
		    <div class="bordaArredondadaTituloH3" align="right">	<span class="spanCinza"> Saldo Anterior <span style="font-size:11px;"> (Caixa + Bancos)</span> </span><span style="color:'.$cor.'">R$ '.$db->valorFormat($array_saldo_anterior[0]).'</span></div>
	    '.$relatorio;//fim monta relatório

		$db->query("drop table lancamentos_rcr_temp");
		
		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>FLUXO DE CAIXA MENSAL</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="442" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="442" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Fluxo_de_Caixa_Mensal',$array_dados['tp_print'],"A4-L");

	    //return $relatorio;

    }

    /*
    ===========================================================================================
    FLUXO DE CAIXA DIÁRIO NOVO
    ===========================================================================================
    */

    function fluxoCaixaDiarioN($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	
	    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
	    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
	    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
	    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	
	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";

	    //=================  CALCULA VALORES DE HOJE ATÉ A DATA INICIAL DO RELATÓRIO (SALDO ANTERIOR) ==========================================================

	    //saldo inicial das contas
	    $saldo_inicial = $db->fetch_assoc("select sum(vl_saldo_inicial) saldo_inicial_total from contas");

	    //valores compensados
	    $query_receita = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "R"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $receita_realizada = $db->fetch_assoc($query_receita);
	
	    $query_despesa = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "P"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $despesa_realizada = $db->fetch_assoc($query_despesa);
	    //fim valores compensados
	
	    $saldo_anterior = $saldo_inicial['saldo_inicial_total'] + $receita_realizada['valor'] - $despesa_realizada['valor'];
	    $array_saldo_anterior[] = $saldo_anterior;

	    //===============================  CALCULA VALORES À PARTIR DA DATA INICIAL DO RELATÓRIO ==============================================================

	    //busca valores para saldo do plano de contas
	    $array_valores = array();
	    $array_dias = array();
	

	    $array_contas_analiticas = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    $dia = strtotime($dt_ini);
	    $dia_fim = strtotime($dt_fim);
	    $i = 0; //índice para referenciar os dias no array de saldo final

	    while($dia<=$dia_fim){

		    $dia = date('Y-m-d',$dia);
		    $array_dias[] = $dia;
	
		    foreach($array_contas_analiticas as $conta_analitica){
		
			    $conta_analitica_id = $conta_analitica['id'];
			    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
	
			    $valor_plc = 0; //valor do plano de contas acumulado por período
	
			    //lançamentos compensados
			    $valor_compensado = $db->fetch_assoc('
				    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
				    from ctr_plc_lancamentos cpl
				    join lancamentos l on cpl.lancamento_id = l.id
				    where cpl.plano_contas_id = '.$conta_analitica_id.'
					    and l.compensado = 1
					    and l.dt_compensacao = "'.$dia.'"
				    group by cpl.tp_lancamento');

			    $valor_plc += $valor_compensado['valor'];
			    ($valor_compensado['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_compensado['valor'] : $array_saldo_final[$i] -= $valor_compensado['valor'];
	
			    //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
			    foreach($conta_hierarquia as $conta_id){
				    if(isset($array_valores[$conta_id][$i])){
					    $array_valores[$conta_id][$i] += $valor_plc;
				    }else{
					    $array_valores[$conta_id][$i] = $valor_plc;
				    }
			    }
					
		    }//fim busca valores para o plano de contas
		
		    //atualiza o saldo inicial do período seguinte(dia ou mês)
		    $array_saldo_final[$i] += $array_saldo_anterior[$i];
		    $array_saldo_anterior[$i+1] = $array_saldo_final[$i];

		    $i++;
		    $dia = strtotime("+1 days",strtotime($dia));

	    }

	    $qtd_dias = count($array_dias);

	    //retira a última posição do array de saldo anterior que fica além do período especificado
	    array_pop($array_saldo_anterior);

	    //monta relatório

	    //Valores sem plano de contas
	    $valor_sem_plc = $db->fetch_all_array('
		    select sum( if(l.tipo="R",l.valor,0) ) valor_r, sum( if(l.tipo="P",l.valor,0) ) valor_p, date_format(l.dt_compensacao, "%d/%m/%Y") dt_compensacao
		    from lancamentos l 
		    left join ctr_plc_lancamentos cpl on l.id = cpl.lancamento_id
		    where l.compensado = 1
			    and l.dt_compensacao >= "'.$dt_ini.'"
			    and l.dt_compensacao <= "'.$dt_fim.'"
			    and cpl.tp_lancamento is NULL
		    group by l.dt_compensacao
		    order by l.dt_compensacao');
	
	    if(count($valor_sem_plc)>0){

		    $linha_dia = '<tr>';
		    $linha_valor = '<tr>';

		    foreach($valor_sem_plc as $valor){
			
			    /*
			    $relatorio .= '
				    <span style="width:200px;text-align:right;display:inline-table">
					    '.$valor['dt_compensacao'].'
					    <br>
					    <span class="verde">R$ '.$db->valorFormat($valor['valor_r']).'</span> / <span class="vermelho">R$ -'.$db->valorFormat($valor['valor_p']).'</span>
				    </span>';
			    */		
			
			    $linha_dia .= '<td align="right"><span>'.$valor['dt_compensacao'].'</span></td>';
			    $linha_valor .= '<td align="right"><span class="verde">R$ '.$db->valorFormat($valor['valor_r']).'</span> / <span class="vermelho">R$ -'.$db->valorFormat($valor['valor_p']).'</span></td>';
		    }

		    $linha_dia .= '</tr>';
		    $linha_valor .= '</tr>';
		
		
		    $relatorio .= '
			    <div class="titulo">	<span class="vermelho"> * Atenção. Os valores abaixo encontram-se fora do plano de contas. Realize as devidas atualizações para gerar o relatório corretamente. </span> </div>
			    <div class="bordaArredondadaTitulo6">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$linha_dia.'
					    </thead>
					    <tbody>
						    '.$linha_valor.'
					    </tbody>
				    </table>
			    </div><br>
		    ';
		
		    /*
		    $relatorio = '
			    <div class="titulo">	<span class="vermelho"> * Atenção. Os valores abaixo encontram-se fora do plano de contas. Realize as devidas atualizações para gerar o relatório corretamente. </span> </div>
			    <div class="bordaArredondadaTexto6">
				    '.$relatorio.'
			    </div><br>
		    ';
		    */
	    }

	    //Valores com plano de contas	
	    $i=0;
	    $col_limite = 5;

	    while($i<$qtd_dias){

		    $col_restante = $qtd_dias - $i;
		    //if($col_restante==1){$w_conta='60%'; $w_valor='20%'; $p_mes='12%';}elseif($col_restante==2){$w_conta='32%'; $w_valor='17%'; $p_mes='10%';}else{$w_conta='22%'; $w_valor='13%'; $p_mes='8%';}
		    $w_conta='40%'; $w_valor='10%';
		
		    $linhas = '';
		    $n = 0;
		
		    //monta valores do plano de contas na tabela
		
		    //classificação - entrada operacional========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc = 1 and tp_conta = 1');
		    $array_vl_clfc_a = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_a[$j] += $valor;
				    //$linhas_plc .= '
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> A: (+) Entrada Operacional </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_a as $vl_clfc){
			    $vl_total += $vl_clfc;
			    ($vl_clfc>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    ($vl_total>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc.$linhas_plc;

		    //classificação - saída operacional========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc = 2 and tp_conta = 1');
		    $array_vl_clfc_b = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_b[$j] += $valor;
				    //$linhas_plc .= '
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    if($valor>0){$valor = $valor * (-1);}
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total*(-1)).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> B: (-) Saída Operacional </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_b as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc>0){$vl_clfc = $vl_clfc * (-1);$bgcolor='rgba(128, 0, 0, 0.16)';}else{$bgcolor = "#F0F0F0";}
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    ($vl_total>0)? $bgcolor = "rgba(128, 0, 0, 0.16)" : $bgcolor = "#F0F0F0";
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total*(-1)).'</td></tr>';

		    $linhas .= $linha_clfc.$linhas_plc;

		    //classificação - fluxo de caixa operacional========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Operacional (A-B) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $vl_clfc = $array_vl_clfc_a[$j]-$array_vl_clfc_b[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;
		
		    //classificação - fluxo do investimento========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (3,4,5) and tp_conta = 1');
		    $array_vl_clfc_c = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_c[$j] += $valor;
				    //$linhas_plc .= '
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> C: (+) Fluxo do Investimento </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_c as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc.$linhas_plc;

		    //classificação - fluxo de caixa operacional livre========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Operacional Livre (A-B+C) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $vl_clfc = $array_vl_clfc_a[$j]-$array_vl_clfc_b[$j]+$array_vl_clfc_c[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - fluxo do financiamento========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (6,7,8) and tp_conta = 1');
		    $array_vl_clfc_d = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_d[$j] += $valor;
				    //$linhas_plc .= '
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> D: (+) Fluxo do Financiamento </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_d as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc.$linhas_plc;

		    //classificação - fluxo de caixa livre dos sócios========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Livre dos Sócios (A-B+C+D) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $vl_clfc = $array_vl_clfc_a[$j]-$array_vl_clfc_b[$j]+$array_vl_clfc_c[$j]+$array_vl_clfc_d[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - fluxo dos sócios========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (9,10) and tp_conta = 1');
		    $array_vl_clfc_e = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
	 		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_e[$j] += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    //$linhas_plc .= '
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
					    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> E: (+) Fluxo dos Sócios </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_e as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		
		    $linhas .= $linha_clfc.$linhas_plc;

		    //classificação - variação de caixa da empresa========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Variação de Caixa da Empresa (A-B+C+D+E) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $vl_clfc = $array_vl_clfc_a[$j]-$array_vl_clfc_b[$j]+$array_vl_clfc_c[$j]+$array_vl_clfc_d[$j]+$array_vl_clfc_e[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - saldo inicial========================================================================================================================================================
		    $colunas=1; 
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> F: (+) Saldo Inicial </td>';
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $vl_clfc = $array_saldo_anterior[$j];
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    $saldo_inicial_total = $array_saldo_anterior[$i];
		    if($saldo_inicial_total==0){ $bgcolor = "#F0F0F0"; }elseif($saldo_inicial_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($saldo_inicial_total).'</td></tr>';

		    $linhas .= $linha_clfc;
 
		    //classificação - saldo final========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Saldo Final (A-B+C+D+E+F) </td>';
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $vl_clfc = $array_vl_clfc_a[$j]-$array_vl_clfc_b[$j]+$array_vl_clfc_c[$j]+$array_vl_clfc_d[$j]+$array_vl_clfc_e[$j]+$array_saldo_anterior[$j];
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    $saldo_final_total = $array_saldo_final[$j-1];//$array_vl_clfc_a[$dia_final]-$array_vl_clfc_b[$dia_final]+$array_vl_clfc_c[$dia_final]+$array_vl_clfc_d[$dia_final]+$array_vl_clfc_e[$dia_final]+$array_saldo_anterior[$dia_final];
		    if($saldo_final_total==0){ $bgcolor = "#F0F0F0"; }elseif($saldo_final_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($saldo_final_total).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //fim monta valores do plano de contas na tabela
		
		    //monta cabeçalho da tabela
		    $tbl_cabecalho = '<tr> <td align="left">Categorias</td>';//'<td rowspan="2" align="left">Contas</td>';

		    $colunas=1;
		    $j=$i;
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $dia = $array_dias[$j];
			    $dia = $db->sql_to_data($dia);
			    $tbl_cabecalho .= '<td align="right" style="padding-left:'.$p_mes.'">'.$dia.'</td>';
			    $colunas++;
			    $j++;
		    }

		    $tbl_cabecalho .= '<td align="right">Total</td></tr>';
		
		    $i = $i + $col_limite;
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>
					    <tbody>
						    '.$linhas.'
					    </tbody>
				    </table>
			    </div><br>
		    ';
	    }

	    /*
	    //Necessidade de caixa

	    $i=0;
	    $col_limite = 3;
	    $ano_ini = $array_anos[0];
	    $qtd_meses_ano_ini = count($array_anos_meses[$ano]);
	    //$linhas = '';
	    //$linhas_plc = '';
	    //$linhas_clfc = '';

	    $arr_plc_rcbt = $db->fetch_all_array('select id, cod_conta, nome from plano_contas where clfc_fc in (1,4,5,6,9)');
	    $arr_plc_pgto = $db->fetch_all_array('select id, cod_conta, nome from plano_contas where clfc_fc in (2,3,7,8,10)');
	    $arr_vl_rcbt = array();
	    $arr_vl_pgto = array();
	
	    while($i<$qtd_meses){

		    $arr_vl_rcbt = array();
		    $arr_vl_pgto = array();
		    $linhas = '';
		    $col_restante = $qtd_meses - $i;
		    if($col_restante==1){$w_conta='60%'; $w_valor='20%'; $p_mes='12%';}elseif($col_restante==2){$w_conta='32%'; $w_valor='17%'; $p_mes='10%';}else{$w_conta='22%'; $w_valor='13%'; $p_mes='8%';}
		
		    //Contas à receber ==========================================================================================================================================================================

		    foreach($arr_plc_rcbt as $plano_contas){
			
			    $linhas_plc = '
				    <tr bgcolor="#FFF">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';
	
			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;

			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $arr_vl_rcbt[$j] += $valor;
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
		
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> CONTAS À RECEBER </td>';
		    $vl_total = 0;
		    foreach($arr_vl_rcbt as $vl_rcbt){
			    $vl_total += $vl_rcbt;
			    ($vl_rcbt>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_rcbt).'</td>';
		    }
		    ($vl_total>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc.$linhas_plc;
		
		    //Contas à pagar ============================================================================================================================================================================

		    foreach($arr_plc_pgto as $plano_contas){
			
			    $linhas_plc = '
				    <tr bgcolor="#FFF">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';
	
			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;

			    while($colunas<=$col_limite && $j<$qtd_dias){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $arr_vl_pgto[$j] += $valor;
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
		
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> CONTAS À PAGAR </td>';
		    $vl_total = 0;
		    foreach($arr_vl_pgto as $vl_pgto){
			    $vl_total += $vl_pgto;
			    if($vl_pgto>0){ $vl_pgto = $vl_pgto * (-1); $bgcolor = "rgba(128, 0, 0, 0.16)"; }else{ $bgcolor = "#F0F0F0"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_pgto).'</td>';
		    }
		    if($vl_total>0){ $vl_total = $vl_total * (-1); $bgcolor = "rgba(128, 0, 0, 0.16)"; }else{ $bgcolor = "#F0F0F0"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc.$linhas_plc;

		    //Necessidade de caixa ============================================================================================================================================================================
		
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> NECESSIDADE DE CAIXA </td>';
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    $valor = $arr_vl_rcbt[$j] - $arr_vl_pgto[$j];
			    $vl_total += $valor;
			    if($valor<0){ $valor = $valor * (-1); }else{ $valor = 0; }
			    $linha_clfc .='<td style="background-color:rgba(128, 0, 0, 0.16);width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($valor).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total<0){ $vl_total = $vl_total * (-1); }else{ $vl_total = 0; }
		    $linha_clfc .= '<td style="background-color:rgba(128, 0, 0, 0.16);width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //monta cabeçalho da tabela necessidade de caixa
		    $tbl_cabecalho = '<tr> <td align="left">Plano de Contas</td>';//'<td rowspan="2" align="left">Contas</td>';

		    $colunas=1;
		    $j=$i;
		    $cont_meses = 1;
		    $ano = $ano_ini;
		    $qtd_meses_ano = $qtd_meses_ano_ini;
		    while($colunas<=$col_limite && $j<$qtd_dias){
			    if($cont_meses>$qtd_meses_ano){
				    $ano++;
				    $qtd_meses_ano = count($array_anos_meses[$ano]);
				    $cont_meses = 1;
			    }
			    $num_mes = $array_meses_report[$j];
			    $nome_mes = $array_meses_nome[$num_mes];
			    $tbl_cabecalho .= '<td align="right" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';//'<td colspan="2" align="center" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';
			    $colunas++;
			    $j++;
			    $cont_meses++;
		    }

		    $tbl_cabecalho .= '<td align="right">Total</td></tr>';
		    //fim monta cabeçalho da tabela necessidade de caixa
		
		    if($i==0){
			    $relatorio .= '
				    <div class="titulo">	<span class="spanCinza"> * Faça uma análise das contas à pagar / receber e da sua necessidade de caixa para cada mês: </span> </div>
			    ';
		    }
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>
					    <tbody>
						    '.$linhas.'
					    </tbody>
				    </table>
			    </div><br>
		    ';

		    $i = $i + $col_limite;

	    }
	    */
	
	    //Fim necessidade de caixa
	
	    //fim monta relatório

		//$db->query("drop table lancamentos_rcr_temp");
		
		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>FLUXO DE CAIXA DIÁRIO</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="442" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="442" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Fluxo_de_Caixa_Diario',$array_dados['tp_print'],"A4-L");

	    //return $relatorio;

    }

    /*
    ===========================================================================================
    FLUXO DE CAIXA MENSAL NOVO
    ===========================================================================================
    */

    function fluxoCaixaMensalN($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	
	    $mes_ini = $array_filtro_periodo["mes"];
	    $ano_ini = $array_filtro_periodo["ano"];
	    $dt_ini = $ano_ini.'-'.$mes_ini.'-01';
	    $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;

	    $mes_fim = $array_filtro_periodo["mesFim"];
	    $ano_fim = $array_filtro_periodo["anoFim"];
	    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
	    $dt_fim = date('Y-m-d',$dt_fim_ts);
	    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	
	    $array_anos_meses = array();
	    $array_meses = array();
	    $array_anos = array();
	    $i = (int)$ano_ini;
	    $j = (int)$mes_ini;
	    $k = 12;
	    $qtd_meses = 0;
	    while($i<=$ano_fim){
		    if($i==$ano_fim){
			    $k = $mes_fim;
		    }
		    while($j<=$k){
			    $array_meses[] = $j;
			    $array_anos_meses[$i][] = $j;
			    $j++;
			    $qtd_meses++;
		    }
		    $array_anos[] = $i;
		    $i++;
		    $j=1;
	    }

	    $array_meses_report = $array_meses;

	    $array_meses_nome = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');
	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	    $mes_atual = date('m');
	    $ano_atual = date('Y');
	    $orcamento_id = $array_dados['orcamento_id'];
	    if($orcamento_id==0)
		    $orcamento_id = "";

	    //=================  CALCULA VALORES DE HOJE ATÉ A DATA INICIAL DO RELATÓRIO (SALDO ANTERIOR) ==========================================================

	    //saldo inicial das contas
	
	    $saldo_inicial = $db->fetch_assoc("select sum(vl_saldo_inicial) saldo_inicial_total from contas");

	    //valores compensados

	    $query_receita = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "R"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $receita_realizada = $db->fetch_assoc($query_receita);
	
	    $query_despesa = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "P"
			    and dt_compensacao < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $despesa_realizada = $db->fetch_assoc($query_despesa);
	    //fim valores compensados
	
	    $saldo_anterior = $saldo_inicial['saldo_inicial_total'] + $receita_realizada['valor'] - $despesa_realizada['valor'];
	    $array_saldo_anterior[] = $saldo_anterior;

	    //===============================  CALCULA VALORES À PARTIR DA DATA INICIAL DO RELATÓRIO ==============================================================

	    //busca valores para saldo do plano de contas
	    $array_valores = array();	

	    $array_contas_analiticas = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    $i = 0;

	    foreach($array_anos_meses as $ano => $array_meses){
		
		    foreach($array_meses as $mes){
			
			    foreach($array_contas_analiticas as $conta_analitica){
                
				    $conta_analitica_id = $conta_analitica['id'];
				    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
                
				    //$valor_plc = 0; //valor do plano de contas acumulado por período
                
				    //lançamentos compensados
				    $valor_compensado = $db->fetch_all_array('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos cpl
					    join lancamentos l on cpl.lancamento_id = l.id
					    where cpl.plano_contas_id = '.$conta_analitica_id.'
						    and l.compensado = 1
						    and month(l.dt_compensacao) = '.$mes.'
						    and year(l.dt_compensacao) = '.$ano.'
					    group by cpl.tp_lancamento');
				    //$valor_plc += $valor_compensado['valor'];
				    //($valor_compensado['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_compensado['valor'] : $array_saldo_final[$i] -= $valor_compensado['valor'];
                    //O foreach é uma correção para quando o cliente coloca valores positivos e negativos numa mesma conta do plano de contas, pois o certo seria a query retornar apenas um registro
                    foreach($valor_compensado as $valor){

                        if($valor['tp_lancamento']=='R')
                            $valor_plc = $valor['valor'];
                        else
                            $valor_plc = $valor['valor']*(-1);

                        $array_saldo_final[$i] += $valor_plc;

                        //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
                        foreach($conta_hierarquia as $conta_id){
                            if(isset($array_valores[$conta_id][$i])){
                                $array_valores[$conta_id][$i] += $valor_plc;
                            }else{
                                $array_valores[$conta_id][$i] = $valor_plc;
                            }
                        }
                    }

			    }//fim busca valores para o plano de contas
            
			    //atualiza o saldo inicial do período seguinte(dia ou mês)
			    $array_saldo_final[$i] += $array_saldo_anterior[$i];
			    $array_saldo_anterior[$i+1] = $array_saldo_final[$i];
			
			    //incrementa o mês
			    $i++;

		    }//fim do for dos meses

	    }//fim do for dos anos

	    //retira a última posição do array de saldo anterior que fica além do período especificado
	    array_pop($array_saldo_anterior);

	    if($orcamento_id!=""){
		    $mes_texto = array('jan','fev','mar','abr','mai','jun','jul','ago','sete','outu','nov','dez');
		    $k = 0;
		    foreach($array_anos_meses as $ano => $array_meses){
			
			    $array_meses_txt = array();
			    foreach($array_meses as $mes){
				    $array_meses_txt[] = $mes_texto[$mes-1];
			    }
			    $j = count($array_meses_txt);
			    $array_meses_txt = join(',',$array_meses_txt);
            
			    foreach($array_contas_analiticas as $conta_analitica){
				
				    $i = $k;
				
				    $conta_analitica_id = $conta_analitica['id'];
				    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
                
				    $orcamento = $db->fetch_assoc("select ".$array_meses_txt." from orcamentos_plnj_vl where orcamento_id = ".$orcamento_id." and plano_contas_id = ".$conta_analitica_id." and ano = ".$ano);
				    if(!empty($orcamento)){
					    foreach($orcamento as $valor){
						    $array_orcamento_valores[$conta_analitica_id][$i] = $valor;
						    $i++;
					    }
				    }else{
					    while($i<=$j){
						    $array_orcamento_valores[$conta_analitica_id][$i] = 0;
						    $i++;
					    }
				    }
			    }
			    $k = $j;
		    }
	    }
	    //fim busca valores

	    //monta relatório
	
	    //Valores sem plano de contas
	    $valor_sem_plc = $db->fetch_all_array('
		    select sum( if(l.tipo="R",l.valor,0) ) valor_r, sum( if(l.tipo="P",l.valor,0) ) valor_p, month(l.dt_compensacao) mes, year(l.dt_compensacao) ano
		    from lancamentos l 
		    left join ctr_plc_lancamentos cpl on l.id = cpl.lancamento_id
		    where l.compensado = 1
			    and l.dt_compensacao >= "'.$dt_ini.'"
			    and l.dt_compensacao <= "'.$dt_fim.'"
			    and cpl.tp_lancamento is NULL
		    group by month(l.dt_compensacao), year(l.dt_compensacao)
		    order by year(l.dt_compensacao), month(l.dt_compensacao)');
	
	    if(count($valor_sem_plc)>0){

		    $linha_mes = '<tr>';
		    $linha_valor = '<tr>';

		    foreach($valor_sem_plc as $valor){
			    $linha_mes .= '<td align="right"><span>'.$array_meses_nome[$valor['mes']].' / '.$valor['ano'].'</span></td>';
			    $linha_valor .= '<td align="right"><span class="verde">R$ '.$db->valorFormat($valor['valor_r']).'</span> / <span class="vermelho">R$ -'.$db->valorFormat($valor['valor_p']).'</span></td>';
		    }

		    $linha_mes .= '</tr>';
		    $linha_valor .= '</tr>';
        
		    $relatorio .= '
			    <div class="titulo">	<span class="vermelho"> * Atenção. Os valores abaixo encontram-se fora do plano de contas. Realize as devidas atualizações para gerar o relatório corretamente. </span> </div>
			    <div class="bordaArredondadaTitulo6" align="left">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$linha_mes.'
					    </thead>
					    <tbody>
						    '.$linha_valor.'
					    </tbody>
				    </table>
			    </div><br>
		    ';

	    }

	    //Valores com plano de contas	
	    $i=0;
	    $col_limite = 3;
	    $ano_ini = $array_anos[0];
	    $qtd_meses_ano_ini = count($array_anos_meses[$ano]);

	    while($i<$qtd_meses){
		
		    $col_restante = $qtd_meses - $i;
		    //if($col_restante==1){$w_conta='60%'; $w_valor='20%'; }elseif($col_restante==2){$w_conta='32%'; $w_valor='17%'; }else{$w_conta='22%'; $w_valor='13%'; }
		    $w_conta='40%'; $w_valor='15%';
		
		    $linhas = '';
		    $n = 0;
		
		    //monta valores do plano de contas na tabela
		
		    //classificação - entrada operacional========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc = 1 and tp_conta = 1');
		    $array_vl_clfc_a = array();
		    $array_vl_clfc_a_orct = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_a[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $array_vl_clfc_a_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> A: (+) Entrada Operacional </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_vl_clfc_a[$j];
			    $vl_total += $vl_clfc;
			    ($vl_clfc>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($orcamento_id==""){
			    ($vl_total>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - saída operacional========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc = 2 and tp_conta = 1');
		    $array_vl_clfc_b = array();
		    $array_vl_clfc_b_orct = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_b[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $array_vl_clfc_b_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    //if($valor>0){$valor = $valor * (-1);}
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> B: (-) Saída Operacional </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_vl_clfc_b[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_b_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($orcamento_id==""){
			    ($vl_total>0)? $bgcolor = "#F0F0F0" : $bgcolor = "rgba(128, 0, 0, 0.16)";
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - fluxo de caixa operacional========================================================================================================================================================
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Operacional (A-B) </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b;
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j]-$array_vl_clfc_b_orct[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
		
		    $linhas .= $linha_clfc;
		
		    //classificação - fluxo do investimento========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (3,4,5) and tp_conta = 1');
		    $array_vl_clfc_c = array();
		    $array_vl_clfc_c_orct = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_c[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $array_vl_clfc_c_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> C: (+) Fluxo do Investimento </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_vl_clfc_c[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_c_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
		
		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - fluxo de caixa operacional livre========================================================================================================================================================
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Operacional Livre (A-B+C) </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b + $array_vl_clfc_c[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j]-$array_vl_clfc_b_orct[$j]+$array_vl_clfc_c_orct[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

		    //classificação - fluxo do financiamento========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (6,7,8) and tp_conta = 1');
		    $array_vl_clfc_d = array();
		    $array_vl_clfc_d_orct = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_d[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $array_vl_clfc_d_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> D: (+) Fluxo do Financiamento </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_vl_clfc_d[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $valor_orct = $array_vl_clfc_d_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($valor_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }
        
		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - fluxo de caixa bruto dos sócios========================================================================================================================================================
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Bruto dos Sócios (A-B+C+D) </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
			    $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b + $array_vl_clfc_c[$j] + $array_vl_clfc_d[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j]-$array_vl_clfc_b_orct[$j]+$array_vl_clfc_c_orct[$j]+$array_vl_clfc_d_orct[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

		    //classificação - fluxo dos sócios========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (9,10) and tp_conta = 1');
		    $array_vl_clfc_e = array();
		    $array_vl_clfc_e_orct = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_e[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $array_vl_clfc_e_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> E: (+) Fluxo dos Sócios </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_vl_clfc_e[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_e_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

            //classificação - fluxo de caixa líquido dos sócios========================================================================================================================================================
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Fluxo de Caixa Líquido dos Sócios (A-B+C+D+E) </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
			    $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b + $array_vl_clfc_c[$j] + $array_vl_clfc_d[$j] + $array_vl_clfc_e[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j]-$array_vl_clfc_b_orct[$j]+$array_vl_clfc_c_orct[$j]+$array_vl_clfc_d_orct[$j]+$array_vl_clfc_e_orct[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

            //classificação - fluxo da tesouraria========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_fc in (11,12) and tp_conta = 1');
		    $array_vl_clfc_f = array();
		    $array_vl_clfc_f_orct = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_f[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $array_vl_clfc_f_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> F: (+) Fluxo da Tesouraria </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_vl_clfc_f[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_f_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - variação de caixa da empresa========================================================================================================================================================
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Variação de Caixa da Empresa (A-B+C+D+E+F) </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
			    $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b + $array_vl_clfc_c[$j] + $array_vl_clfc_d[$j] + $array_vl_clfc_e[$j] + $array_vl_clfc_f[$j];
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j]-$array_vl_clfc_b_orct[$j]+$array_vl_clfc_c_orct[$j]+$array_vl_clfc_d_orct[$j]+$array_vl_clfc_e_orct[$j]+$array_vl_clfc_f_orct[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

		    //classificação - saldo inicial========================================================================================================================================================
		    $colunas=1; 
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> G: (+) Saldo Inicial </td>';
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $array_saldo_anterior[$j];
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $vl_clfc;//$array_saldo_anterior_orct[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    $saldo_inicial_total = $array_saldo_anterior[$i]; //é o saldo inicial do período levando em consideração as respectivas divisões das colunas para caber na página
			    if($saldo_inicial_total==0){ $bgcolor = "#F0F0F0"; }elseif($saldo_inicial_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($saldo_inicial_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

		    //classificação - saldo final========================================================================================================================================================
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Saldo Final (A-B+C+D+E+F+G) </td>';
		    $colunas=1;
		    $j=$i;
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
			    $vl_clfc = $array_vl_clfc_a[$j]-$vl_clfc_b+$array_vl_clfc_c[$j]+$array_vl_clfc_d[$j]+$array_vl_clfc_e[$j]+$array_vl_clfc_f[$j]+$array_saldo_anterior[$j];
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    if($orcamento_id!=""){
				    $vl_clfc_orct = $array_vl_clfc_a_orct[$j]-$array_vl_clfc_b_orct[$j]+$array_vl_clfc_c_orct[$j]+$array_vl_clfc_d_orct[$j]+$array_vl_clfc_e_orct[$j]+$array_saldo_anterior[$j];
				    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc_orct).'</td>';
			    }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    $saldo_final_total = $array_saldo_final[$j-1];//$array_vl_clfc_a[$j]-$array_vl_clfc_b[$j]+$array_vl_clfc_c[$j]+$array_vl_clfc_d[$j]+$array_vl_clfc_e[$j]+$array_saldo_anterior[$j];
			    if($saldo_final_total==0){ $bgcolor = "#F0F0F0"; }elseif($saldo_final_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($saldo_final_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

		    //fim monta valores do plano de contas na tabela
		
		    //monta cabeçalho secundário da tabela
		    $tbl_cabecalho = '<tr> <td align="left">Categorias</td>';//'<td rowspan="2" align="left">Contas</td>';

		    $colunas=1;
		    $j=$i;
		    $cont_meses = 1;
		    $ano = $ano_ini;
		    $qtd_meses_ano = $qtd_meses_ano_ini;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if($cont_meses>$qtd_meses_ano){
				    $ano++;
				    $qtd_meses_ano = count($array_anos_meses[$ano]);
				    $cont_meses = 1;
			    }
			    $num_mes = $array_meses_report[$j];
			    $nome_mes = $array_meses_nome[$num_mes];
			    if($orcamento_id!=""){
				    $tbl_cabecalho .= '<td align="right">Orçamento</td>';//'<td colspan="2" align="center" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';
			    }
			    $tbl_cabecalho .= '<td align="right">'.$nome_mes.' / '.$ano.'</td>';//'<td colspan="2" align="center" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';
			    $colunas++;
			    $j++;
			    $cont_meses++;
		    }

		    if($orcamento_id==""){
			    $tbl_cabecalho .= '<td align="right">Total</td></tr>';
		    }
		
		    $i = $i + $col_limite;
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>
					    <tbody>
						    '.$linhas.'
					    </tbody>
				    </table>
			    </div><br>
		    ';
	    }

	    //Necessidade de caixa
	
	    $i=0;
	    $col_limite = 3;
	    $ano_ini = $array_anos[0];
	    $qtd_meses_ano_ini = count($array_anos_meses[$ano]);
	    //$linhas = '';
	    //$linhas_plc = '';
	    //$linhas_clfc = '';

	    $arr_plc_rcbt = $db->fetch_all_array('select id, cod_conta, nome from plano_contas where clfc_fc in (1,4,5,6,9) and tp_conta = 1');
	    $arr_plc_pgto = $db->fetch_all_array('select id, cod_conta, nome from plano_contas where clfc_fc in (2,3,7,8,10) and tp_conta = 1');
	    $arr_vl_rcbt = array();
	    $arr_vl_pgto = array();
	
	    while($i<$qtd_meses){
        
		    $arr_vl_rcbt = array();
		    $arr_vl_pgto = array();
		    $arr_vl_rcbt_orct = array();
		    $arr_vl_pgto_orct = array();
		    $linhas = '';
		    $col_restante = $qtd_meses - $i;
		    //if($col_restante==1){$w_conta='60%'; $w_valor='20%'; }elseif($col_restante==2){$w_conta='32%'; $w_valor='17%'; }else{$w_conta='22%'; $w_valor='13%'; }
		    $w_conta='40%'; $w_valor='15%';
		
		    //Contas à receber ==========================================================================================================================================================================
		    $linhas_plc = '';
		    foreach($arr_plc_rcbt as $plano_contas){
			
			    $linhas_plc .= '
				    <tr bgcolor="#FFF">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';
            
			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;

			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $arr_vl_rcbt[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $arr_vl_rcbt_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }
		
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> CONTAS À RECEBER </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_rcbt = $arr_vl_rcbt[$j];
			    $vl_total += $vl_rcbt;
			    ($vl_rcbt>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    if($orcamento_id!=""){
				    $vl_rcbt_orct =  $arr_vl_rcbt_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_rcbt_orct).'</td>';
			    }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_rcbt).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    ($vl_total>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;
		
		    //Contas à pagar ============================================================================================================================================================================
		    $linhas_plc = '';
		    foreach($arr_plc_pgto as $plano_contas){
			
			    $linhas_plc .= '
				    <tr bgcolor="#FFF">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';
            
			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;

			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $arr_vl_pgto[$j] += $valor;
				    if($orcamento_id!=""){
					    $valor_orct = $array_orcamento_valores[$plano_contas['id']][$j];
					    $arr_vl_pgto_orct[$j] += $valor_orct;
					    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orct).'</td>';
				    }
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    if($orcamento_id==""){
				    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
			    }
		    }
		
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> CONTAS À PAGAR </td>';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_pgto = $arr_vl_pgto[$j];
			    $vl_total += $vl_pgto;
			    if($orcamento_id!=""){
				    $vl_pgto_orct =  $arr_vl_pgto_orct[$j];
				    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_pgto_orct).'</td>';
			    }
			    if($vl_pgto>0){ $vl_pgto = $vl_pgto * (-1); $bgcolor = "#F0F0F0"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_pgto).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total>0){ $vl_total = $vl_total * (-1); $bgcolor = "#F0F0F0"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //Necessidade de caixa ============================================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> NECESSIDADE / SOBRA DE CAIXA </td>';
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_pgto = ($arr_vl_pgto[$j]>=0)? $arr_vl_pgto[$j] : $arr_vl_pgto[$j]*(-1);
                $valor = $arr_vl_rcbt[$j] - $vl_pgto;
			    $vl_total += $valor;
			    if($orcamento_id!=""){
				    $valor_orct = $arr_vl_rcbt_orct[$j] - $arr_vl_pgto_orct[$j];
				    if($valor_orct<0){ $valor_orct = $valor_orct * (-1); }else{ $valor_orct = 0; }
				    $linha_clfc .= '<td style="background-color:rgba(128, 0, 0, 0.16);width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($valor_orct).'</td>';
			    }
			    if($valor<0){ $valor = $valor * (-1); $bgcolor = "rgba(128, 0, 0, 0.16)";}elseif($valor==0){ $bgcolor = "#F0F0F0";}else{ $bgcolor = "rgba(0, 128, 0, 0.16)";}
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($valor).'</td>';
			    $colunas++;
			    $j++;
		    }

		    if($orcamento_id==""){
			    if($vl_total<0){ $vl_total = $vl_total * (-1); $bgcolor = "rgba(128, 0, 0, 0.16)";}elseif($vl_total==0){ $bgcolor = "#F0F0F0";}else{ $bgcolor = "rgba(0, 128, 0, 0.16)";}
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linhas .= $linha_clfc;

		    //monta cabeçalho da tabela necessidade de caixa
		    $tbl_cabecalho = '<tr> <td align="left">Categorias</td>';//'<td rowspan="2" align="left">Contas</td>';

		    $colunas=1;
		    $j=$i;
		    $cont_meses = 1;
		    $ano = $ano_ini;
		    $qtd_meses_ano = $qtd_meses_ano_ini;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if($cont_meses>$qtd_meses_ano){
				    $ano++;
				    $qtd_meses_ano = count($array_anos_meses[$ano]);
				    $cont_meses = 1;
			    }
			    $num_mes = $array_meses_report[$j];
			    $nome_mes = $array_meses_nome[$num_mes];
			    if($orcamento_id!=""){
				    $tbl_cabecalho .= '<td align="right">Orçamento</td>';
			    }
			    $tbl_cabecalho .= '<td align="right">'.$nome_mes.' / '.$ano.'</td>';//'<td colspan="2" align="center" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';
			    $colunas++;
			    $j++;
			    $cont_meses++;
		    }

		    if($orcamento_id==""){
			    $tbl_cabecalho .= '<td align="right">Total</td></tr>';
		    }else{
			    $tbl_cabecalho .= '</tr>';
		    }
		    //fim monta cabeçalho da tabela necessidade de caixa
		
		    if($i==0){
			    $relatorio .= '
				    <div class="titulo">	<span class="spanCinza"> * Faça uma análise das contas à pagar / receber e da sua necessidade de caixa para cada mês: </span> </div>
			    ';
		    }
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>
					    <tbody>
						    '.$linhas.'
					    </tbody>
				    </table>
			    </div><br>
		    ';

		    $i = $i + $col_limite;

	    }
	
		//Fim necessidade de caixa
		

		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }

	
	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>FLUXO DE CAIXA MENSAL</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="442" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="442" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Fluxo_de_Caixa_Mensal',$array_dados['tp_print'],"A4-L");

	    //return $relatorio;

    }

    /*
    ===========================================================================================
    DRE
    ===========================================================================================
    */

    function dre($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	
	    $mes_ini = $array_filtro_periodo["mes"];
	    $ano_ini = $array_filtro_periodo["ano"];
	    $dt_ini = $ano_ini.'-'.$mes_ini.'-01';
	    $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;

	    $mes_fim = $array_filtro_periodo["mesFim"];
	    $ano_fim = $array_filtro_periodo["anoFim"];
	    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
	    $dt_fim = date('Y-m-d',$dt_fim_ts);
	    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	
	    $array_anos_meses = array();
	    $array_meses = array();
	    $array_anos = array();
	    $i = (int)$ano_ini;
	    $j = (int)$mes_ini;
	    $k = 12;
	    $qtd_meses = 0;
	    while($i<=$ano_fim){
		    if($i==$ano_fim){
			    $k = $mes_fim;
		    }
		    while($j<=$k){
			    $array_meses[] = $j;
			    $array_anos_meses[$i][] = $j;
			    $j++;
			    $qtd_meses++;
		    }
		    $array_anos[] = $i;
		    $i++;
		    $j=1;
	    }

	    $array_meses_report = $array_meses;

	    $array_meses_nome = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');
	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	    $mes_atual = date('m');
	    $ano_atual = date('Y');
	    $orcamento_id = $array_dados['orcamento_id'];

	    //=================  CALCULA VALORES DE HOJE ATÉ A DATA INICIAL DO RELATÓRIO (SALDO ANTERIOR) ==========================================================

	    //saldo inicial das contas
	    $saldo_inicial = $db->fetch_assoc("select sum(vl_saldo_inicial) saldo_inicial_total from contas");

	    //valores compensados
	    $query_receita = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "R"
			    and dt_competencia < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $receita_realizada = $db->fetch_assoc($query_receita);
	
	    $query_despesa = '
		    select sum(valor) valor
		    from lancamentos
		    where tipo = "P"
			    and dt_competencia < "'.$dt_ini.'"
			    and compensado = 1
	    ';
	    $despesa_realizada = $db->fetch_assoc($query_despesa);
	    //fim valores compensados

	    $saldo_anterior = $saldo_inicial['saldo_inicial_total'] + $receita_realizada['valor'] - $despesa_realizada['valor'];
	    $array_saldo_anterior[] = $saldo_anterior;

        //Busca centros analíticos
        $centro_resp_id = '';
        if($array_dados['centro_resp_id']!=0){
        
            $centro_pai_id = $array_dados['centro_resp_id'];
        
            //encontra os filhos analíticos do centro selecionado
            $arr_aux = array(); //id dos centros sintéticos
            $arr_ctr_analitico = array(); //centros analíticos
            $arr_ctr_analitico_id = array(); //id dos centros analíticos
            $arr_filhos = $db->fetch_all_array("select id, tp_centro from centro_resp where centro_pai_id = ".$centro_pai_id);
            $hasFilho = count($arr_filhos);
            if($hasFilho){
                while($hasFilho){
                    foreach($arr_filhos as $filho){
                        if($filho['tp_centro']==1){
                            array_push($arr_ctr_analitico,$filho);
                            array_push($arr_ctr_analitico_id,$filho['id']);
                        }
                        array_push($arr_aux,$filho['id']);
                    }
                    $centro_pai_id = join(',',$arr_aux);
                    $arr_filhos = $db->fetch_all_array("select id, tp_centro from centro_resp where centro_pai_id in (".$centro_pai_id.")");
                    $hasFilho = count($arr_filhos);
                    if($hasFilho)
                        $arr_aux = array();
                }

                $centro_resp_id = 'and cpl.centro_resp_id in ('.join(',', $arr_ctr_analitico_id).')';

            }else{
                $arr_ctr_analitico = $db->fetch_assoc("select id from centro_resp where id = ".$centro_pai_id);
                $centro_resp_id = 'and cpl.centro_resp_id = '.$arr_ctr_analitico['id'];
            }
        
        }

        //busca valores para saldo do plano de contas
	    $array_valores = array();	

	    $array_contas_analiticas = $db->fetch_all_array('
		    select id, hierarquia
		    from plano_contas
		    where tp_conta = 1
	    ');

	    $i = 0;

	    foreach($array_anos_meses as $ano => $array_meses){
		
		    foreach($array_meses as $mes){
			
			    foreach($array_contas_analiticas as $conta_analitica){
                
				    $conta_analitica_id = $conta_analitica['id'];
				    $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
                
				    $valor_plc = 0; //valor do plano de contas acumulado por período
                
				    //lançamentos compensados
				    $valor_compensado = $db->fetch_all_array('
					    select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
					    from ctr_plc_lancamentos cpl
					    join lancamentos l on cpl.lancamento_id = l.id
					    where cpl.plano_contas_id = '.$conta_analitica_id.'
                            '.$centro_resp_id.'
						    and month(l.dt_competencia) = '.$mes.'
						    and year(l.dt_competencia) = '.$ano.'
					    group by cpl.tp_lancamento');
                
                    //O foreach é uma correção para quando o cliente coloca valores positivos e negativos numa mesma conta do plano de contas, pois o certo seria a query retornar apenas um registro
                    foreach($valor_compensado as $valor){

                        if($valor['tp_lancamento']=='R')
                            $valor_plc = $valor['valor'];
                        else
                            $valor_plc = $valor['valor']*(-1);

                        $array_saldo_final[$i] += $valor_plc;

                        //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
                        foreach($conta_hierarquia as $conta_id){
                            if(isset($array_valores[$conta_id][$i])){
                                $array_valores[$conta_id][$i] += $valor_plc;
                            }else{
                                $array_valores[$conta_id][$i] = $valor_plc;
                            }
                        }
                    }

			    }//fim busca valores para o plano de contas
            
			    //atualiza o saldo inicial do período seguinte(dia ou mês)
			    $array_saldo_final[$i] += $array_saldo_anterior[$i];
			    $array_saldo_anterior[$i+1] = $array_saldo_final[$i];
			
			    //incrementa o mês
			    $i++;

		    }//fim do for dos meses

	    }//fim do for dos anos

	    //retira a última posição do array de saldo anterior que fica além do período especificado
	    array_pop($array_saldo_anterior);

	    //monta relatório

	    //Valores sem plano de contas - CRIAR PRIMEIRA LINHA DO RELATÓRIO COM "VALORES NÃO ALOCADOS" SEPARADOS POR MÊS E CRIAR RELATÓRIO PARA INFORMAR QUAIS SÃO OS LANÇAMENTOS
        /*
	    $valor_sem_plc = $db->fetch_all_array('
        select sum( if(l.tipo="R",l.valor,0) ) valor_r, sum( if(l.tipo="P",l.valor,0) ) valor_p, month(l.dt_competencia) mes, year(l.dt_competencia) ano
        from lancamentos l 
        left join ctr_plc_lancamentos cpl on l.id = cpl.lancamento_id
        where l.compensado = 1
        and l.dt_competencia >= "'.$dt_ini.'"
        and l.dt_competencia <= "'.$dt_fim.'"
        and cpl.tp_lancamento is NULL
        group by month(l.dt_competencia), year(l.dt_competencia)
        order by year(l.dt_competencia), month(l.dt_competencia)');
	
	    if(count($valor_sem_plc)>0){

        $linha_mes = '<tr>';
        $linha_valor = '<tr>';

        foreach($valor_sem_plc as $valor){
        $linha_mes .= '<td align="right"><span>'.$array_meses_nome[$valor['mes']].' / '.$valor['ano'].'</span></td>';
        $linha_valor .= '<td align="right"><span class="verde">R$ '.$db->valorFormat($valor['valor_r']).'</span> / <span class="vermelho">R$ -'.$db->valorFormat($valor['valor_p']).'</span></td>';
        }

        $linha_mes .= '</tr>';
        $linha_valor .= '</tr>';
    
        $relatorio .= '
        <div class="titulo">	<span class="vermelho"> * Atenção. Os valores abaixo encontram-se fora do plano de contas. Realize as devidas atualizações para gerar o relatório corretamente. </span> </div>
        <div class="bordaArredondadaTitulo6" align="left">
        <table border="0" cellpadding="0" cellspacing="0">
        <thead>
        '.$linha_mes.'
        </thead>
        <tbody>
        '.$linha_valor.'
        </tbody>
        </table>
        </div><br>
        ';

	    }
         */

	    //Valores de depreciação, amortização e provisão
	    $dpre = $db->fetch_assoc('select jan "1", fev "2", mar "3", abr "4", mai "5", jun "6", jul "7", ago "8", sete "9", outu "10", nov "11", dez "12" from provisao where tipo = 1 and ano in ('.$ano_ini.','.$ano_fim.') order by ano');
	    $amrt = $db->fetch_assoc('select jan "1", fev "2", mar "3", abr "4", mai "5", jun "6", jul "7", ago "8", sete "9", outu "10", nov "11", dez "12" from provisao where tipo = 2 and ano in ('.$ano_ini.','.$ano_fim.') order by ano');
	    $prov_trab = $db->fetch_all_array('select jan "1", fev "2", mar "3", abr "4", mai "5", jun "6", jul "7", ago "8", sete "9", outu "10", nov "11", dez "12" from provisao where tipo = 3 and ano in ('.$ano_ini.','.$ano_fim.') order by ano');

	    $arr_vl_dpre = array();
	    $arr_vl_amrt = array();
	    $arr_vl_prov_trab = array();
	    $i = 0;
	    foreach($array_anos_meses as $ano => $_array_meses){
		    foreach($_array_meses as $mes){
			
			    if(isset($dpre[$i][$mes])){
				    $arr_vl_dpre[] = $dpre[$i][$mes];
			    }

			    if(isset($amrt[$i][$mes])){
				    $arr_vl_amrt[] = $amrt[$i][$mes];
			    }

			    if(isset($prov_trab[$i][$mes])){
				    $arr_vl_prov_trab[] = $prov_trab[$i][$mes];
			    }

		    }
		    $i++;
	    }

	    //Valores com plano de contas
	    $i=0;
	    $col_limite = 3;
	    $ano_ini = $array_anos[0];
	    $qtd_meses_ano_ini = count($array_anos_meses[$ano]);

	    while($i<$qtd_meses){
		
		    $col_restante = $qtd_meses - $i;
		    //if($col_restante==1){$w_conta='60%'; $w_valor='20%'; }elseif($col_restante==2){$w_conta='32%'; $w_valor='17%'; }else{$w_conta='22%'; $w_valor='13%'; }
		    $w_conta='40%'; $w_valor='15%';
		
		    $linhas = '';
		    $n = 0;
		
		    //monta valores do plano de contas na tabela
		
		    //classificação - A: (+) Receitas Operacionais 
            //========================================================================================================================================================

		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_dre = 1 and tp_conta = 1');
		    $array_vl_clfc_a = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '

				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_a[$j] += $valor;
				    //$linhas_plc .= '
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> A: (+) Receitas Operacionais </td>';
		    $vl_total_a = 0;
		    foreach($array_vl_clfc_a as $vl_clfc){
			    $vl_total_a += $vl_clfc;
			    ($vl_clfc>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    ($vl_total_a>0)? $bgcolor = "rgba(0, 128, 0, 0.16)" : $bgcolor = "#F0F0F0";
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total_a).'</td></tr>';

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - B: (-) Impostos S/ Vendas 
            //========================================================================================================================================================

		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_dre = 10 and tp_conta = 1');
		    $array_vl_clfc_b = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_b[$j] += $valor;
				    //$linhas_plc .= '
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    if($valor>0){$valor = $valor * (-1);}
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total*(-1)).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> B: (-) Impostos S/ Vendas </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_b as $vl_clfc){
			    $vl_total += $vl_clfc;
                if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

            $vl_total_b = $vl_total;

		    //classificação - (=) Receitas Operacionais Líquidas (A-B)
            //========================================================================================================================================================
		    
            $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Receitas Operacionais Líquidas (A-B) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc_b = ($array_vl_clfc_b[$j]>0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b;
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;
		
		    //classificação - C: (-) Custos e Despesas Variáveis
            //========================================================================================================================================================

		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_dre in (5,7,8,9) and tp_conta = 1');
		    $array_vl_clfc_c = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_c[$j] += $valor;
				    //$linhas_plc .= '
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> C: (-) Custos e Despesas Variáveis </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_c as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;
            
            $vl_total_c = $vl_total;

		    //classificação - (=) Margem de Contribuição (A-B-C) 
            //========================================================================================================================================================

		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Margem de Contribuição (A-B-C) </td>';
		    $vl_total_mrg_cntr = 0;
		    $array_vl_mrg_cntr = array();
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc_b = ($array_vl_clfc_b[$j]>0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc_c = ($array_vl_clfc_c[$j]>0)? $array_vl_clfc_c[$j] : $array_vl_clfc_c[$j]*(-1);
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b - $vl_clfc_c;
			    $array_vl_mrg_cntr[$j] = $vl_clfc;
			    $vl_total_mrg_cntr += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total_mrg_cntr==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total_mrg_cntr>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total_mrg_cntr).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - D: (-) Custos e Despesas Fixas
            //========================================================================================================================================================

		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_dre = 6 and tp_conta = 1');
		    $array_vl_clfc_d = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    $array_vl_clfc_d[$j] += $valor;
				    //$linhas_plc .= '
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;

			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    //provisões trabalhistas
		    $n +=1;	
		    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
		    $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

		    $linhas_plc .= '
			    <tr bgcolor="'.$bg_color.'">
				    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">Provisões Trabalhistas</span></td>
		    ';
		    $colunas=1;
		    $j=$i;
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $valor = $arr_vl_prov_trab[$j];
			    $vl_total += $valor;
			    $array_vl_clfc_d[$j] += $valor;
			    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
			    $colunas++;
			    $j++;
		    }
		    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>'; 

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> D: (-) Custos e Despesas Fixas </td>';
		    $vl_total_d = 0;
		    foreach($array_vl_clfc_d as $vl_clfc){
			    $vl_total_d += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total_d==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total_d>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total_d).'</td></tr>';

		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

		    //classificação - EBITDA ========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) EBITDA (A-B-C-D) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc_c = ($array_vl_clfc_c[$j]>=0)? $array_vl_clfc_c[$j] : $array_vl_clfc_c[$j]*(-1);
                $vl_clfc_d = ($array_vl_clfc_d[$j]>=0)? $array_vl_clfc_d[$j] : $array_vl_clfc_d[$j]*(-1);			
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b - $vl_clfc_c - $vl_clfc_d;
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - Depreciação e Amortização ========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> E: (-) Depreciação e Amortização </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    $vl_clfc = $arr_vl_amrt[$j] + $arr_vl_dpre[$j];
			    $array_vl_clfc_e[$j] = $vl_clfc;
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';

		    $vl_total_e = $vl_total;

            $linhas .= $linha_clfc;

		    //classificação - EBIT ========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) EBIT (A-B-C-D-E) </td>';
		    $vl_total = 0;
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc_c = ($array_vl_clfc_c[$j]>=0)? $array_vl_clfc_c[$j] : $array_vl_clfc_c[$j]*(-1);
                $vl_clfc_d = ($array_vl_clfc_d[$j]>=0)? $array_vl_clfc_d[$j] : $array_vl_clfc_d[$j]*(-1);
                $vl_clfc_e = ($array_vl_clfc_e[$j]>=0)? $array_vl_clfc_e[$j] : $array_vl_clfc_e[$j]*(-1);
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b - $vl_clfc_c - $vl_clfc_d - $vl_clfc_e;
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
            
		    $linhas .= $linha_clfc;

		    //classificação - F: (-/+) Resultado Financeiro ========================================================================================================================================================
		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_dre in (2,4) and tp_conta = 1');
		    $array_vl_clfc_f = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_f[$j] += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    //$linhas_plc .= '
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> F: (-/+) Resultado Financeiro </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_f as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		
		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

            $vl_total_f = $vl_total;

            //classificação - Lucro Antes dos IR e CSLL - LAIR 
            //========================================================================================================================================================

		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Lucro Antes dos IR e CSLL - LAIR (A-B-C-D-E+F) </td>';
		    $vl_total_lucro_preju = 0;
		    $array_lucro_preju = array();
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc_c = ($array_vl_clfc_c[$j]>=0)? $array_vl_clfc_c[$j] : $array_vl_clfc_c[$j]*(-1);
                $vl_clfc_d = ($array_vl_clfc_d[$j]>=0)? $array_vl_clfc_d[$j] : $array_vl_clfc_d[$j]*(-1);
                $vl_clfc_e = ($array_vl_clfc_e[$j]>=0)? $array_vl_clfc_e[$j] : $array_vl_clfc_e[$j]*(-1);
                $vl_clfc = $array_vl_clfc_a[$j] - $vl_clfc_b - $vl_clfc_c - $vl_clfc_d - $vl_clfc_e + $array_vl_clfc_f[$j];
			    $array_lucro_preju[$j] = $vl_clfc;
			    $vl_total_lucro_preju += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total_lucro_preju==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total_lucro_preju>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total_lucro_preju).'</td></tr>';

		    $linhas .= $linha_clfc;

            //classificação - G: (-) Impostos s/ Lucro
            //========================================================================================================================================================

		    $array_plano_contas = $db->fetch_all_array('select id, nivel, cod_conta, nome from plano_contas where clfc_dre = 11 and tp_conta = 1');
		    $array_vl_clfc_g = array();
		    $linhas_plc = '';
		    foreach($array_plano_contas as $plano_contas){
			    $n +=1;	
			    $bg_color='#FFF';//if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $espc = $plano_contas['nivel'] * 10;  $espc = $espc.'px'; 

			    $linhas_plc .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left" style="padding-left:20px;" width="'.$w_conta.'"><span class="spanCinza">'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</span></td>
			    ';

			    $colunas=1;
			    $j=$i;
			    $vl_total = 0;
			    while($colunas<=$col_limite && $j<$qtd_meses){
				    $valor = $array_valores[$plano_contas['id']][$j];
				    $vl_total += $valor;
				    $array_vl_clfc_g[$j] += $valor;
				    //$valor_orcamento = ($orcamento_id=="")? 0: $array_orcamento_valores[$plano_contas['id']][$j];
				    //$linhas_plc .= '
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor_orcamento).'</td>
                    //<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>
				    //';
				    $linhas_plc .= '<td align="right" width="'.$w_valor.'">R$ '.$db->valorFormat($valor).'</td>';
				    $colunas++;
				    $j++;
			    }
			    $linhas_plc .= '<td align="right">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		    }

		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> G: (-) Impostos s/ Lucro </td>';
		    $vl_total = 0;
		    foreach($array_vl_clfc_g as $vl_clfc){
			    $vl_total += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
		    }
		    if($vl_total==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total).'</td></tr>';
		
		    $linhas .= $linha_clfc;
            if($array_dados["detalhamento"]=='analitico')
                $linhas .= $linhas_plc;

            $vl_total_g = $vl_total;

            //classificação - Lucro ou Prejuízo do Exercício ========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> (=) Lucro ou Prejuízo do Exercício (A-B-C-D-E+F-G) </td>';
		    $vl_total_lucro_preju = 0;
		    $array_lucro_preju = array();
		    while($colunas<=$col_limite && $j<$qtd_meses){
                $vl_clfc_a = ($array_vl_clfc_a[$j]>=0)? $array_vl_clfc_a[$j] : $array_vl_clfc_a[$j]*(-1);
                $vl_clfc_b = ($array_vl_clfc_b[$j]>=0)? $array_vl_clfc_b[$j] : $array_vl_clfc_b[$j]*(-1);
                $vl_clfc_c = ($array_vl_clfc_c[$j]>=0)? $array_vl_clfc_c[$j] : $array_vl_clfc_c[$j]*(-1);
                $vl_clfc_d = ($array_vl_clfc_d[$j]>=0)? $array_vl_clfc_d[$j] : $array_vl_clfc_d[$j]*(-1);
                $vl_clfc_e = ($array_vl_clfc_e[$j]>=0)? $array_vl_clfc_e[$j] : $array_vl_clfc_e[$j]*(-1);
                $vl_clfc_g = ($array_vl_clfc_g[$j]>=0)? $array_vl_clfc_g[$j] : $array_vl_clfc_g[$j]*(-1);
			    $vl_clfc = $vl_clfc_a - $vl_clfc_b - $vl_clfc_c - $vl_clfc_d - $vl_clfc_e + $array_vl_clfc_f[$j] - $vl_clfc_g;
			    $array_lucro_preju[$j] = $vl_clfc;
			    $vl_total_lucro_preju += $vl_clfc;
			    if($vl_clfc==0){ $bgcolor = "#F0F0F0"; }elseif($vl_clfc>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_clfc).'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total_lucro_preju==0){ $bgcolor = "#F0F0F0"; }elseif($vl_total_lucro_preju>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">R$ '.$db->valorFormat($vl_total_lucro_preju).'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - Lucratividade ========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> Lucratividade </td>';
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if( $array_vl_clfc_a[$j]>0 ){
				    $porcentagem = round($array_lucro_preju[$j]*100/$array_vl_clfc_a[$j]).'%';
			    }else{
				    $porcentagem = "";
			    }
			    if($array_lucro_preju[$j]==0){ $bgcolor = "#F0F0F0"; $sinal = ""; }elseif($array_lucro_preju[$j]>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; $sinal = "";}else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; /*$sinal = "-";*/}
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">'.$sinal.$porcentagem.'</td>';
			    $colunas++;
			    $j++;
		    }
		    if($vl_total_a>0){
			    $porcentagem_total = round($vl_total_lucro_preju*100/$vl_total_a).'%';
		    }else{
			    $porcentagem_total = "";
		    }
		    if($vl_total_lucro_preju==0){ $bgcolor = "#F0F0F0"; $sinal = "";}elseif($vl_total_lucro_preju>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; $sinal = "";}else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; /*$sinal = "-";*/}
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">'.$sinal.$porcentagem_total.'</td></tr>';

		    $linhas .= $linha_clfc;

		    //classificação - Ponto de Equilíbrio ========================================================================================================================================================
		    $colunas=1;
		    $j=$i;
		    $linha_clfc = '<tr bgcolor="#F0F0F0"> <td> Ponto de Equilíbrio </td>';
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if( $array_vl_clfc_a[$j]>0 && $array_vl_mrg_cntr[$j]>0 ){
				    $vl_pto_eql = ($array_vl_clfc_d[$j]+$array_vl_clfc_e[$j]+$array_vl_clfc_f[$j]+$array_vl_clfc_g[$j])/($array_vl_mrg_cntr[$j]/$array_vl_clfc_a[$j]);
                    $vl_pto_eql = ($vl_pto_eql>=0)? $vl_pto_eql : $vl_pto_eql*(-1);
				    $vl_pto_eql = 'R$ '.$db->valorFormat($vl_pto_eql);
			    }else{
				    $vl_pto_eql = "";
			    }
			    if($vl_pto_eql==0){ $bgcolor = "#F0F0F0"; }elseif($vl_pto_eql>0){ $bgcolor = "rgba(0, 128, 0, 0.16)"; }else{ $bgcolor = "rgba(128, 0, 0, 0.16)"; }
			    $linha_clfc .='<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">'.$vl_pto_eql.'</td>';
			    $colunas++;
			    $j++;
		    }
		    if( $vl_total_a>0 && $vl_total_mrg_cntr>0 ){
			    $vl_total_pto_eql = ($vl_total_d + $vl_total_e + $vl_total_f + $vl_total_g)/($vl_total_mrg_cntr/$vl_total_a);
                $vl_total_pto_eql = ($vl_total_pto_eql>=0)? $vl_total_pto_eql : $vl_total_pto_eql*(-1);
			    $vl_total_pto_eql = 'R$ '.$db->valorFormat($vl_total_pto_eql);
		    }else{
			    $vl_total_pto_eql = "";
		    }
		    $bgcolor = "#F0F0F0";
		    $linha_clfc .= '<td style="background-color:'.$bgcolor.';width:'.$w_valor.';text-align:right;">'.$vl_total_pto_eql.'</td></tr>';

		    $linhas .= $linha_clfc;
		
		    //monta cabeçalho da tabela
		    $tbl_cabecalho = '<tr> <td align="left">Categorias</td>';//'<td rowspan="2" align="left">Contas</td>';

		    $colunas=1;
		    $j=$i;
		    $cont_meses = 1;
		    $ano = $ano_ini;
		    $qtd_meses_ano = $qtd_meses_ano_ini;
		    while($colunas<=$col_limite && $j<$qtd_meses){
			    if($cont_meses>$qtd_meses_ano){
				    $ano++;
				    $qtd_meses_ano = count($array_anos_meses[$ano]);
				    $cont_meses = 1;
			    }
			    $num_mes = $array_meses_report[$j];
			    $nome_mes = $array_meses_nome[$num_mes];
			    $tbl_cabecalho .= '<td align="right">'.$nome_mes.' / '.$ano.'</td>';//'<td colspan="2" align="center" style="padding-left:'.$p_mes.'">'.$nome_mes.' / '.$ano.'</td>';
			    $colunas++;
			    $j++;
			    $cont_meses++;
		    }

		    $tbl_cabecalho .= '<td align="right">Total</td></tr>';
		    //fim monta cabeçalho da tabela
		
		    $i = $i + $col_limite;
		
		    $relatorio .= '
			    <div class="bordaArredondadaTitulo6" align="center">
				    <table border="0" cellpadding="0" cellspacing="0">
					    <thead>
						    '.$tbl_cabecalho.'
					    </thead>
					    <tbody>
						    '.$linhas.'
					    </tbody>
				    </table>
			    </div><br>
		    ';
	    }


		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>DRE</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="442" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="442" align="right">webfinancas.com</div>
	    ';
	

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'DRE',$array_dados['tp_print'],"A4-L");

    }

    /*
    ===========================================================================================
    CONTAS À RECEBER E À PAGAR
    ===========================================================================================
     */

    function rcbts_pgtos($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_ini = $db->data_to_sql($array_filtro_periodo["dt_ini"]);
		    $dt_fim = $db->data_to_sql($array_filtro_periodo["dt_fim"]);
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
	    }else{
		    //Mês inicial
            $mes = $array_filtro_periodo["mes"];
		    $ano = $array_filtro_periodo["ano"];
		    $dt_ini = $ano.'-'.$mes.'-01';
		
            //Mês final
            $mesFim = $array_filtro_periodo["mesFim"];
		    $anoFim = $array_filtro_periodo["anoFim"];
            $dt_fim = mktime(0,0,0,$mesFim+1,'00',$anoFim);
		    $dt_fim = date('Y-m-d',$dt_fim);
		
            //Data de referência do relatório
            $dt_referencia_ini = '01/'.$mes.'/'.$ano;
		    $dt_referencia_fim = $db->sql_to_data($dt_fim);
	    }

	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i:s');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	
        //Data de competência
        $dtCompIni = ''; 
        $dtCompFim = '';

        if($array_filtro_periodo['dtCompIni']!=''){
            $arrayDtCompIni = explode('/',$array_filtro_periodo['dtCompIni']);
            $dtCompIni = $arrayDtCompIni[1]."-".$arrayDtCompIni[0]."-01";
            $dtCompIniQ = "and dt_competencia >= '".$dtCompIni."'";
            $dtCompIniRef = '01/'.$arrayDtCompIni[0]."/".$arrayDtCompIni[1];
        }

        if($array_filtro_periodo['dtCompFim']!=''){
            $arrayDtCompFim = explode('/',$array_filtro_periodo['dtCompFim']);    
            //$dtCompFim = mktime(0,0,0,$arrayDtCompFim[0]+1,'00',$arrayDtCompFim[1]);
            //$dtCompFim = date('Y-m-d',$dtCompFim);
            $dtCompFim = $arrayDtCompFim[1]."-".$arrayDtCompFim[0]."-01";
            $dtCompFimQ = "and dt_competencia <= '".$dtCompFim."'";
            $dtCompFimRef = $dtCompFim = date('d/m/Y', mktime(0,0,0,$arrayDtCompFim[0]+1,'00',$arrayDtCompFim[1]));
        }

        //Situação do lançamento
	    $lancamento_situacao = $array_dados['lancamento_situacao'];
	    if($array_dados['tp_lancamento']=="R"){
		    if($lancamento_situacao==0){
			    $nome_relatorio = "CONTAS À RECEBER";
			    $nome_pdf = "Contas_a_Receber";
			    $coluna_data = "VENCIMENTO";
		    }else{
			    $nome_relatorio = "CONTAS RECEBIDAS";
			    $nome_pdf = "Contas_Recebidos";
			    $coluna_data = "COMPENSAÇÃO";
		    }
		    $texto_total = 'Total de Entradas';
	    }else{
		    if($lancamento_situacao==0){
			    $nome_relatorio = "CONTAS À PAGAR";
			    $nome_pdf = "Contas_a_Pagar";
			    $coluna_data = "VENCIMENTO";
		    }else{
			    $nome_relatorio = "CONTAS PAGAS";
			    $nome_pdf = "Contas_Pagas";
			    $coluna_data = "COMPENSAÇÃO";
		    }
		    $texto_total = 'Total de Saídas';
	    }

	    $texto_saldo_anterior = '';
	    $total_t_entrada = '';
	    $total_t_saida = '';
	
	    //define a data de ordenação para a busca na tabela temporária
	    if( $lancamento_situacao==0 )
		    $order_by = 'order by dt_vencimento, id' ;
	    else
		    $order_by = 'order by dt_compensacao, id' ;
    
	    //tabela temporaria para armazenar lançamentos existentes
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_temp (
			    id int PRIMARY KEY AUTO_INCREMENT,
			    conta_id int,
			    compensado int(1),
                dt_comp_mes_dif int,
                dt_competencia date NOT NULL,
                dt_vencimento date NOT NULL,
			    dt_compensacao date NOT NULL,
			    descricao varchar(150),
			    valor decimal(10,2) NOT NULL,
			    tipo char(1),
			    frequencia int(3),
			    dia_mes int(1),
			    nome char(30)
		    ) ENGINE=MEMORY
	    ");

	    //tipo do lançamento
	    $tp_lancamento = $array_dados['tp_lancamento'];

	    //situação dos lançamentos
	    //$lancamento_situacao = $array_dados['lancamento_situacao'];
	    $compensado = "";
	    if($lancamento_situacao==0){
		    $compensado = "and l.compensado = 0";
            $queryData = 'and (l.dt_vencimento >= "'.$dt_ini.'" and l.dt_vencimento <= "'.$dt_fim.'")';
	    }elseif($lancamento_situacao==1){
		    $compensado = "and l.compensado = 1";
            $queryData = 'and (l.dt_compensacao >= "'.$dt_ini.'" and l.dt_compensacao <= "'.$dt_fim.'")';
	    }

	    //id do favorecido
	    $favorecido_id = "";
	    if($array_dados['favorecido_id']!="" && $array_dados['favorecido_id']!=0){
		    $favorecido_id = "and favorecido_id = ".$array_dados['favorecido_id'];
	    }	

	    //contas financeiras do relatório	
	    $cf_id = $array_dados["contas_financeiras"];
	    $array_cf_id = explode(',',$array_dados["contas_financeiras"]); 

	    //agrupar centro de responsabilidade
	    $agp_ctr = $array_dados["agp_ctr"];

	    //centro selecionado
	    $centro_pai_id = $array_dados['centro_resp_id'];

	    //calcula total de transferências de entrada e saída dentro do período de referência
	    if($lancamento_situacao==1){
		
            $query_transferencias = '
                select conta_id_origem, conta_id_destino, sum(valor) as valor
				    from lancamentos
				    where (conta_id_origem in ('.$cf_id.') or conta_id_destino in ('.$cf_id.'))
					    and tipo = "T"
					    and compensado = 1
                        and dt_compensacao >= "'.$dt_ini.'" and dt_compensacao <= "'.$dt_fim.'"
                        '.$dtCompIniQ.'
                        '.$dtCompFimQ.'
				    group by conta_id_origem, conta_id_destino
                ';

            $array_transferencias = $db->fetch_all_array($query_transferencias);

            $array_total_t_entradas = array();
            $array_total_t_saidas = array();

            foreach($array_transferencias as $transf){

                if(array_key_exists($transf['conta_id_destino'], $array_total_t_entradas))
                    $array_total_t_entradas[$transf['conta_id_destino']] += $transf['valor'];
                else
                    $array_total_t_entradas[$transf['conta_id_destino']] = $transf['valor'];

                if(array_key_exists($transf['conta_id_origem'], $array_total_t_saidas))
                    $array_total_t_saidas[$transf['conta_id_origem']] += $transf['valor'];
                else
                    $array_total_t_saidas[$transf['conta_id_origem']] = $transf['valor'];
            }
	    }
        //fim calcula total de transferências de entrada e saída dentro do período de referência

	    if($centro_pai_id!=0){
		
		    //encontra os filhos analíticos do centro selecionado
		    $arr_aux = array(); //id dos centros sintéticos
		    $arr_ctr_analitico = array(); //centros analíticos
		    $arr_filhos = $db->fetch_all_array("select id, nome, hierarquia, tp_centro from centro_resp where centro_pai_id = ".$centro_pai_id);
		    $hasFilho = count($arr_filhos);
		    if($hasFilho){
			    while($hasFilho){
				    foreach($arr_filhos as $filho){
					    if($filho['tp_centro']==1)
						    array_push($arr_ctr_analitico,$filho);
					    array_push($arr_aux,$filho['id']);
				    }
				    $centro_pai_id = join(',',$arr_aux);
				    $arr_filhos = $db->fetch_all_array("select id, nome, hierarquia, tp_centro from centro_resp where centro_pai_id in (".$centro_pai_id.")");
				    $hasFilho = count($arr_filhos);
				    if($hasFilho)
					    $arr_aux = array();
			    }
		    }else{
			    $arr_ctr_analitico = $db->fetch_all_array("select id, nome, hierarquia from centro_resp where id = ".$centro_pai_id);
		    }
		
	    }else{
		    $arr_ctr_analitico = $db->fetch_all_array('select id, nome, hierarquia from centro_resp where tp_centro = 1 and hierarquia > 0');
	    }

	    if($agp_ctr || $centro_pai_id!=0){ //monta query para relatório com centro de custo agrupado ou selecionado

            //lançamentos programados ou compensados
		    $q_lnct = '
			    select distinct l.id, l.conta_id, l.compensado, dt_vencimento, dt_compensacao, l.descricao, l.valor, f.nome, l.tipo
			    from ctr_plc_lancamentos cpl
			    join lancamentos l on cpl.lancamento_id = l.id
			    join favorecidos f on l.favorecido_id = f.id
			    where l.conta_id in ('.$cf_id.')
				    and l.tipo in ("R","P")
				    '.$compensado.'
				    '.$dtCompIniQ.'
                    '.$dtCompFimQ.'
				    '.$queryData.'
				    '.$favorecido_id;
	    }

	    if($agp_ctr){ //agrupa por centro de custo
        
		    foreach($arr_ctr_analitico as $ctr){
            
			    //inclui lançamentos programados ou compensados na tabela temporária
			    $arr_lnct = $db->fetch_all_array($q_lnct.' and cpl.centro_resp_id = '.$ctr['id']);
			    foreach($arr_lnct as $lnct){
				    $db->query_insert('lancamentos_temp',$lnct);
			    }
			
			    //inclui lançamentos recorrentes na tabela temporária
                if($lancamento_situacao==0){
                    $q_lnct_rcr = '
				    select distinct l.id, l.conta_id, 0 compensado, dt_vencimento, descricao, l.valor, frequencia, dia_mes, f.nome, tipo, dt_comp_mes_dif
				    from ctr_plc_lancamentos_rcr cpl
				    join lancamentos_recorrentes l on cpl.lancamento_rcr_id = l.id
				    join favorecidos f on l.favorecido_id = f.id
				    where l.conta_id in ('.$cf_id.')
					    and l.tipo in ("R","P")
                        and dt_vencimento <= "'.$dt_fim.'"
					    and f.id = l.favorecido_id
					    '.$favorecido_id;//.' and cpl.centro_resp_id = '.$ctr['id'];

                    $q_lnct_rcr = mysql_query($q_lnct_rcr.' and cpl.centro_resp_id = '.$ctr['id']);

                    while($lnct_rcr = mysql_fetch_assoc($q_lnct_rcr)){
                    
                        //Data de vencimento
                        $dt_vencimento = date($lnct_rcr['dt_vencimento']);

                        //Data de competência
                        $dt_competencia = self::DtCompetenciaCalc($dt_vencimento, $lnct_rcr['dt_comp_mes_dif']);
                        $lnct_rcr['dt_competencia'] = $dt_competencia;
                    
                        //Inclui lançamento na tabela temporária se as datas de vencimento e de competência forem válidas
                        while($dt_vencimento <= $dt_fim){
                        
                            if($dtCompIni=='' && $dtCompFim==''){

                                if($dt_vencimento >= $dt_ini)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);                                

                            }elseif($dtCompIni!='' && $dtCompFim!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia >= $dtCompIni && $dt_competencia <= $dtCompFim)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);

                            }elseif($dtCompIni!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia >= $dtCompIni)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);

                            }elseif($dtCompFim!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia <= $dtCompFim)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);
                            }
                        
                            //Calcula data de vencimento
                            if($lnct_rcr['frequencia']>=30){
                            
                                $frequencia = $lnct_rcr['frequencia']/30;
                                $dia_vencimento = $lnct_rcr['dia_mes'];
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

                            $lnct_rcr['dt_vencimento'] = $dt_vencimento;

                            //Calcula data de competência
                            $dt_competencia = self::DtCompetenciaCalc($dt_vencimento, $lnct_rcr['dt_comp_mes_dif']);
                            $lnct_rcr['dt_competencia'] = $dt_competencia;
                        
                        }//fim while
                    
                    }//fim while
                }
            
			    //buscar nome de toda hierarquia do centro analítico
			    $nome_hrq = '';
			    $arr_hrq = $db->fetch_all_array('select nome from centro_resp where id in ('.$ctr['hierarquia'].') order by nivel');
			    $qtd_hrq = count($arr_hrq);
			    $i = 0;
			    while($i<$qtd_hrq){
				    if($i<($qtd_hrq-1)){
					    $nome_hrq .= $arr_hrq[$i]['nome'];
					    $nome_hrq .= " > ";
				    }else{
					    $nome_hrq .= $arr_hrq[$i]['nome'];
				    }
				    $i++;
			    }
            
			    foreach($array_cf_id as $cf_id){

				    $query_lancamentos_temp = "
					    select id, nome, tipo, descricao, valor, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao_format, dt_compensacao, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, compensado
					    from lancamentos_temp 
					    where conta_id = ".$cf_id."
					    ".$order_by;
                
				    $query_lancamentos_temp = mysql_query($query_lancamentos_temp);
                
				    $lancamentos = '';
				    $total_entradas = 0;
				    $total_saidas = 0;
				    $saldo_total = 0;
				    $n = 0;

				    //busca todos os lançamentos na tabela temporária e monta bloco de lançamentos para conta financeira
				    while($lancamento_temp = mysql_fetch_assoc($query_lancamentos_temp)){
					    //calcular atraso do lançamento
					    if($lancamento_temp['compensado']==0){
						    $dt_venc_comp = $lancamento_temp['dt_vencimento_format'];
						    $data = explode('-',$lancamento_temp[dt_compensacao]);
						    $dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
						    $hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
						    $atraso = $hoje - $dt_limite;
						    //$atraso = date('d',$atraso);
						    $atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
                        
						    $dt_limite = date('Y-m-d',$dt_limite);
                        
						    //if($dt_hoje > $dt_limite){
                            //$dt_compensacao .= "</b> <font class='subTexto red'>  <br> Atrasado ".$atraso." dia(s) </font>";
						    //}else{
                            //$dt_compensacao .= "</b> <font class='subTexto blue'>  <br> À realizar </font>";
						    //}
						
                            $celula_vencimento = '';

					    }else{
						    $dt_venc_comp = $lancamento_temp['dt_compensacao_format'];
                            $celula_vencimento = '<td align="center">'.$lancamento_temp['dt_vencimento_format'].'</td>';
					    }
					    if($lancamento_temp['tipo']==$tp_lancamento){
						    $n +=1;
						    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
						    $lancamentos .='
							    <tr bgcolor="'.$bg_color.'">
                                        '.$celula_vencimento.'
									    <td align="center">'.$dt_venc_comp.'</td>
									    <td align="left">'.$lancamento_temp['nome'].'</td>
									    <td align="left">'.$lancamento_temp['descricao'].'</td>
									    <td align="right">R$ '.$db->valorFormat($lancamento_temp['valor']).'</td>
							    </tr>
						    ';
					    }

					    if($lancamento_temp['tipo']=='R')
						    $total_entradas += $lancamento_temp['valor'];
					    else
						    $total_saidas += $lancamento_temp['valor'];

				    }
				    //fim while
                
				    //monta relatório
				
                    //busca dados da conta financeira
                    $conta = $db->fetch_assoc('
						    select IFNULL(concat(b.nome," - ",c.descricao), c.descricao) as conta_nome, vl_saldo_inicial
						    from contas c
						    left join bancos b on c.banco_id = b.id
						    where c.id = '.$cf_id);

                    //monta os saldos
                    if($lancamento_situacao==1){
                    
                        //$saldo_anterior = self::saldo_anterior($db,$cf_id,$dt_ini) + $conta['vl_saldo_inicial'];
                        //$texto_saldo_anterior = '<div class="bordaArredondadaTitulo2" align="right"> <span class="spanCinza"> Saldo Anterior: </span> R$ '.$db->valorFormat($saldo_anterior).' </div>';
                    
                        //acrescenta soma do valor de transferências ao final do bloco de lançamentos
                        //$total_entradas += $array_total_t_entradas[$i]['valor'];
                        //$total_saidas += $array_total_t_saidas[$i]['valor'];
                        //$i++;
                    
                        //calcula saldo final
                        //$saldo_final = $saldo_anterior + $total_entradas - $total_saidas;

                        //monta texto sub total
                        if($tp_lancamento=='R'){
                            $texto_sub_total = '
								    <span class="spanCinza"> Total de Entradas: </span> R$ '.$db->valorFormat($total_entradas);
                            //<span class="spanCinza"> Total de Saídas: </span> R$ '.$db->valorFormat($total_saidas).' <br>
                            //<span class="spanCinza"> Saldo Disponível: </span> R$ '.$db->valorFormat($saldo_final);
                        }else{
                            $texto_sub_total = '
								    <span class="spanCinza"> Total de Saídas: </span> R$ '.$db->valorFormat($total_saidas);
                            //<span class="spanCinza"> Total de Entradas: </span> R$ '.$db->valorFormat($total_entradas).' <br>
                            //<span class="spanCinza"> Saldo Disponível: </span> R$ '.$db->valorFormat($saldo_final);
                        }

                    }else{
                        if($tp_lancamento=='R')
                            $texto_sub_total = '<span class="spanCinza"> '.$texto_total.' </span> R$ '.$db->valorFormat($total_entradas);
                        else
                            $texto_sub_total = '<span class="spanCinza"> '.$texto_total.' </span> R$ '.$db->valorFormat($total_saidas);
                    }
                    //fim monta os saldos
                
                    if($lancamento_situacao==1)
                        $coluna_vencimento = '<td width="1" align="center"><span class="spanCinza">VENCIMENTO</span></td>';
                    else
                        $coluna_vencimento = '';

                    //monta bloco da conta financeira incluindo bloco de lançamentos
                    $relatorio .= '
						    <div class="bordaArredondadaTitulo6" align="center"> 

							    <div class="cabecalhoInterno">
								    <div class="bordaArredondadaTitulo4">
									    <div class="spanCinza">&bull; '.$nome_hrq.'</div>
									    <span class="spanCinza">&bull; '.$conta['conta_nome'].'	</span>
								    </div>
								    '.$texto_saldo_anterior.'
							    </div>
	
							    <table border="0" cellpadding="0" cellspacing="0">
								    <thead>
									    <tr>
                                            '.$coluna_vencimento.'
										    <td width="1" align="center"><span class="spanCinza">'.$coluna_data.'</span></td>
										    <td width="250" align="left"><span class="spanCinza">FAVORECIDO</span></td>
										    <td width="250" align="left"><span class="spanCinza">DESCRIÇÃO</span></td>
										    <td width="140" align="right"><span class="spanCinza">VALOR</span></td>
									    </tr>
								    </thead>
								    <tbody>
									    '.$lancamentos.'
								    </tbody>
							    </table>									
	
							    <div class="subTotal" align="right">
								    '.$texto_sub_total.'
							    </div>
						    </div><br>
					    ';

				    //fim monta relatório
				
                
			    }//fim foreach $array_cf_id
            
		    }//fim foreach $arr_ctr_analitico

		    //limpa tabelas da memória
		    $db->query("drop table lancamentos_temp");

	    }else{ //não agrupa por centro de custo

		    if($centro_pai_id!=0){ //insere na tabela temporária somente lançamentos que estão no centro de custo especificado
            
                //id dos centros analiticos
                $arr_ctr_id = array();
                foreach($arr_ctr_analitico as $ctr){
                    array_push($arr_ctr_id, $ctr['id']);
			    }
            
                //lançamentos programados ou compensados
                $q_lnct .= ' and cpl.centro_resp_id in ('.join(',', $arr_ctr_id).')';
                $arr_lnct = $db->fetch_all_array($q_lnct);
                foreach($arr_lnct as $lnct){
                    $db->query_insert('lancamentos_temp',$lnct);
                }
            
                //inclui lançamentos recorrentes na tabela temporária
                if($lancamento_situacao==0){
                    $q_lnct_rcr = '
					        select distinct l.id, l.conta_id, 0 compensado, dt_vencimento, descricao, l.valor, frequencia, dia_mes, f.nome, tipo, dt_comp_mes_dif
					        from ctr_plc_lancamentos_rcr cpl
					        join lancamentos_recorrentes l on cpl.lancamento_rcr_id = l.id
					        join favorecidos f on l.favorecido_id = f.id
					        where l.conta_id in ('.$cf_id.')
						        and l.tipo in ("R","P")
						        and dt_vencimento <= "'.$dt_fim.'"
						        and f.id = l.favorecido_id
						        '.$favorecido_id;//.' and cpl.centro_resp_id = '.$ctr['id'];

                    $q_lnct_rcr .= ' and cpl.centro_resp_id in ('.join(',', $arr_ctr_id).')';
                    $q_lnct_rcr = mysql_query($q_lnct_rcr);
			    
                    while($lnct_rcr = mysql_fetch_assoc($q_lnct_rcr)){
                    
                        $dt_competencia = self::DtCompetenciaCalc($lnct_rcr['dt_vencimento'], $lnct_rcr['dt_comp_mes_dif']);
                        $lnct_rcr['dt_competencia'] = $dt_competencia;

                        $dt_vencimento = date($lnct_rcr['dt_vencimento']);
                    
                        //Inclui lançamento na tabela temporária se as datas de vencimento e de competência forem válidas
                        while($dt_vencimento <= $dt_fim){
                        
                            if($dtCompIni=='' && $dtCompFim==''){

                                if($dt_vencimento >= $dt_ini)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);                                

                            }elseif($dtCompIni!='' && $dtCompFim!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia >= $dtCompIni && $dt_competencia <= $dtCompFim)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);

                            }elseif($dtCompIni!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia >= $dtCompIni)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);

                            }elseif($dtCompFim!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia <= $dtCompFim)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);
                            }

                            //Calcula data de vencimento
                            if($lnct_rcr['frequencia']>=30){
                            
                                $frequencia = $lnct_rcr['frequencia']/30;
                                $dia_vencimento = $lnct_rcr['dia_mes'];
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
                        
                            $lnct_rcr['dt_vencimento'] = $dt_vencimento;
                        
                            //Calcula data de competência
                            $dt_competencia = self::DtCompetenciaCalc($dt_vencimento, $lnct_rcr['dt_comp_mes_dif']);
                            $lnct_rcr['dt_competencia'] = $dt_competencia;

                        }
                    }
                }

		    }else{ //insere lançamentos na tabela temporária independete de qualquer centro de custo

			    //lançamentos programados ou compensados
			    $q_lnct = '
				    select l.conta_id, l.compensado, dt_vencimento, dt_compensacao, l.descricao, l.valor, f.nome, tipo
				    from lancamentos l
				    join favorecidos f on l.favorecido_id = f.id
				    where l.conta_id in ('.$cf_id.')
					    and l.tipo in ("R","P")
					    '.$compensado.'
                        '.$dtCompIniQ.'
                        '.$dtCompFimQ.'
					    '.$queryData.'
					    and f.id = l.favorecido_id
					    '.$favorecido_id;

			    $arr_lnct = $db->fetch_all_array($q_lnct);
            
			    foreach($arr_lnct as $lnct){
				    $db->query_insert('lancamentos_temp',$lnct);
			    }
			
			    //lançamentos recorrentes
                if($lancamento_situacao==0){
                    $q_lnct_rcr = mysql_query('
				    select l.conta_id, 0 compensado, dt_vencimento, descricao, l.valor, frequencia, dia_mes, f.nome, tipo, dt_comp_mes_dif
				    from lancamentos_recorrentes l
				    join favorecidos f on l.favorecido_id = f.id
				    where l.conta_id in ('.$cf_id.')
					    and l.tipo in ("R","P")
					    and dt_vencimento <= "'.$dt_fim.'"
					    and f.id = l.favorecido_id
					    '.$favorecido_id);
                
                    while($lnct_rcr = mysql_fetch_assoc($q_lnct_rcr)){
                    
                        $dt_competencia = self::DtCompetenciaCalc($lnct_rcr['dt_vencimento'], $lnct_rcr['dt_comp_mes_dif']);
                        $lnct_rcr['dt_competencia'] = $dt_competencia;

                        $dt_vencimento = date($lnct_rcr['dt_vencimento']);
                    
                        //Inclui lançamento na tabela temporária se as datas de vencimento e de competência forem válidas
                        while($dt_vencimento <= $dt_fim){
                        
                            if($dtCompIni=='' && $dtCompFim==''){

                                if($dt_vencimento >= $dt_ini)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);                                

                            }elseif($dtCompIni!='' && $dtCompFim!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia >= $dtCompIni && $dt_competencia <= $dtCompFim)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);

                            }elseif($dtCompIni!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia >= $dtCompIni)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);

                            }elseif($dtCompFim!=''){

                                if($dt_vencimento >= $dt_ini && $dt_competencia <= $dtCompFim)
                                    $db->query_insert('lancamentos_temp',$lnct_rcr);
                            }
                        
                            if($lnct_rcr['frequencia']>=30){
                            
                                $frequencia = $lnct_rcr['frequencia']/30;
                                $dia_vencimento = $lnct_rcr['dia_mes'];
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
                        
                            $lnct_rcr['dt_vencimento'] = $dt_vencimento;
                        
                            $dt_competencia = self::DtCompetenciaCalc($dt_vencimento, $lnct_rcr['dt_comp_mes_dif']);
                            $lnct_rcr['dt_competencia'] = $dt_competencia;

                        }
                    }
                }
		    }

		    //buscar nome de toda hierarquia do centro analítico
		    $nome_hrq = '';
		    $centro_pai_id = $array_dados['centro_resp_id'];
		    $ctr_pai_hrq = $db->fetch_assoc('select hierarquia from centro_resp where id = '.$centro_pai_id);
		    if($centro_pai_id!=0){
			    $arr_hrq = $db->fetch_all_array('select nome from centro_resp where id in ('.$ctr_pai_hrq['hierarquia'].') order by nivel');
			    $qtd_hrq = count($arr_hrq);
			    $i = 0;
			    while($i<$qtd_hrq){
				    if($i<($qtd_hrq-1)){
					    $nome_hrq .= $arr_hrq[$i]['nome'];
					    $nome_hrq .= " > ";
				    }else{
					    $nome_hrq .= $arr_hrq[$i]['nome'];
				    }
				    $i++;
			    }
		    }

            if($nome_hrq!="")
                $nome_hrq = '<div class="spanCinza"> &bull; '.$nome_hrq.'</div>';

		    $i=0;

		    $total_geral_entradas = 0;
            $total_geral_saidas = 0;
        
            foreach($array_cf_id as $cf_id){
			
			    $query_lancamentos_temp = "
				    select id, nome, tipo, descricao, valor, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao_format, dt_compensacao, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, compensado
				    from lancamentos_temp 
				    where conta_id = ".$cf_id."
				    ".$order_by;

			    $query_lancamentos_temp = mysql_query($query_lancamentos_temp);
            
			    $lancamentos = '';
			    $total_entradas = 0;
			    $total_saidas = 0;
                $total_t_entrada_valor = 0;
                $total_t_saida_valor = 0;
			    $saldo_total = 0;
			    $n = 0;
            
			    //busca todos os lançamentos na tabela temporária
			    while($lancamento_temp = mysql_fetch_assoc($query_lancamentos_temp)){
				    //calcular atraso do lançamento
				    if($lancamento_temp['compensado']==0){
					    $dt_venc_comp = $lancamento_temp['dt_vencimento_format'];
					    $data = explode('-',$lancamento_temp[dt_compensacao]);
					    $dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
					    $hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
					    $atraso = $hoje - $dt_limite;
					    //$atraso = date('d',$atraso);
					    $atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
                    
					    $dt_limite = date('Y-m-d',$dt_limite);
                    
					    //if($dt_hoje > $dt_limite){
                        //$dt_compensacao .= "</b> <font class='subTexto red'>  <br> Atrasado ".$atraso." dia(s) </font>";
					    //}else{
                        //$dt_compensacao .= "</b> <font class='subTexto blue'>  <br> À realizar </font>";
					    //}

                        $celula_vencimento = '';

				    }else{
					    $dt_venc_comp = $lancamento_temp['dt_compensacao_format'];
                        $celula_vencimento = '<td align="center">'.$lancamento_temp['dt_vencimento_format'].'</td>';
				    }
				
				    if($lancamento_temp['tipo']==$tp_lancamento){
					    $n +=1;
					    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
					    $lancamentos .='
						    <tr bgcolor="'.$bg_color.'">
                                    '.$celula_vencimento.'
								    <td align="center">'.$dt_venc_comp.'</td>
								    <td align="left">'.$lancamento_temp['nome'].'</td>
								    <td align="left">'.$lancamento_temp['descricao'].'</td>
								    <td align="right">R$ '.$db->valorFormat($lancamento_temp['valor']).'</td>
						    </tr>
					    ';
				    }

				    if($lancamento_temp['tipo']=='R')
					    $total_entradas += $lancamento_temp['valor'];
				    else
					    $total_saidas += $lancamento_temp['valor'];

			    }
			    //fim da busca dos lançamentos na tabela temporária

			    //monta relatório
			
                //pega dados da conta financeira
                $conta = $db->fetch_assoc('
					    select IFNULL(concat(b.nome," - ",c.descricao), c.descricao) as conta_nome, vl_saldo_inicial
					    from contas c
					    left join bancos b on c.banco_id = b.id
					    where c.id = '.$cf_id);
            
                //monta os saldos
                if($lancamento_situacao==1){

                    if($centro_pai_id==0){
                        $saldo_anterior = self::saldo_anterior($db,$cf_id,$dt_ini) + $conta['vl_saldo_inicial'];
                        $texto_saldo_anterior = '<div class="bordaArredondadaTitulo2" align="right"> <span class="spanCinza"> Saldo Anterior: </span> R$ '.$db->valorFormat($saldo_anterior).' </div>';
                    
                        //acrescenta soma do valor de transferências ao final do bloco de lançamentos
                        //$total_entradas += $array_total_t_entradas[$i]['valor'];
                        //$total_saidas += $array_total_t_saidas[$i]['valor'];
                    
                        //$total_t_entrada = ' [T: '.$db->valorFormat($array_total_t_entradas[$i]['valor']).']';
                        //$total_t_saida = ' [T: '.$db->valorFormat($array_total_t_saidas[$i]['valor']).']';
                        $total_t_entrada = ' [T: 0,00]';
                        $total_t_saida = ' [T: 0,00]';

                        if(array_key_exists($cf_id, $array_total_t_entradas)){
                            $total_t_entrada_valor = $array_total_t_entradas[$cf_id];
                            $total_t_entrada = ' [T: '.$db->valorFormat($array_total_t_entradas[$cf_id]).']';
                        }

                        if(array_key_exists($cf_id, $array_total_t_saidas)){
                            $total_t_saida_valor = $array_total_t_saidas[$cf_id];
                            $total_t_saida = ' [T: '.$db->valorFormat($array_total_t_saidas[$cf_id]).']';
                        }
                    
                        //calcula saldo final
                        $saldo_final = $saldo_anterior + $total_entradas + $total_t_entrada_valor - $total_saidas - $total_t_saida_valor;
                        $i++;
                    }

                    //monta texto sub total
                    if($tp_lancamento=='R'){
                        $texto_sub_total = '<span class="spanCinza"> Total de Entradas: </span> R$ '.$db->valorFormat($total_entradas).$total_t_entrada;
                    
                        if($centro_pai_id==0){
                            $texto_sub_total .= '
							    <br>
							    <span class="spanCinza"> Total de Saídas: </span> R$ '.$db->valorFormat($total_saidas).$total_t_saida.'<br>
							    <span class="spanCinza"> Saldo Final: </span> R$ '.$db->valorFormat($saldo_final);
                        }

                    }else{
                        $texto_sub_total = '<span class="spanCinza"> Total de Saídas: </span> R$ '.$db->valorFormat($total_saidas).$total_t_saida;

                        if($centro_pai_id==0){
                            $texto_sub_total = '<span class="spanCinza"> Total de Entradas: </span> R$ '.$db->valorFormat($total_entradas).$total_t_entrada.' <br>
							    '.$texto_sub_total.' <br>
							    <span class="spanCinza"> Saldo Final: </span> R$ '.$db->valorFormat($saldo_final);
                        }
                    }
                
                }else{
                    if($tp_lancamento=='R')
                        $texto_sub_total = '<span class="spanCinza"> '.$texto_total.' </span> R$ '.$db->valorFormat($total_entradas);
                    else
                        $texto_sub_total = '<span class="spanCinza"> '.$texto_total.' </span> R$ '.$db->valorFormat($total_saidas);
                }

                //Total geral de entradas e saídas
                $total_geral_entradas += $total_entradas;
                $total_geral_saidas += $total_saidas;

                //fim monta os saldos

                //monta bloco de lançamentos da conta financeira
            
                if($lancamento_situacao==1)
                    $coluna_vencimento = '<td width="1" align="center"><span class="spanCinza">VENCIMENTO</span></td>';
                else
                    $coluna_vencimento = '';

                $relatorio .= '
					    <div class="bordaArredondadaTitulo6" align="center">
	
						    <div class="cabecalhoInterno">
							    <div class="bordaArredondadaTitulo4">
								    '.$nome_hrq.'
								    <span class="spanCinza"> &bull; '.$conta['conta_nome'].' </span> 
							    </div>
							    '.$texto_saldo_anterior.'
						    </div>

						    <table border="0" cellpadding="0" cellspacing="0">
							    <thead>
								    <tr>
                                        '.$coluna_vencimento.'
									    <td width="1" align="center"><span class="spanCinza">'.$coluna_data.'</span></td>
									    <td width="250" align="left"><span class="spanCinza">FAVORECIDO</span></td>
									    <td width="250" align="left"><span class="spanCinza">DESCRIÇÃO</span></td>
									    <td width="140" align="right"><span class="spanCinza">VALOR</span></td>
								    </tr>
							    </thead>
							    <tbody>
								    '.$lancamentos.'
							    </tbody>
						    </table>									
						    <div class="subTotal" align="right">
							    '.$texto_sub_total.'
						    </div>
					    </div>
					    <br>
				    ';
            
			    //fim monta relatório
            
		    }//fim foreach $array_cf_id
		
            //Total geral de transferências de entrada e saída
            if($lancamento_situacao==1){
                $total_geral_t_entradas = 0;
                foreach($array_total_t_entradas as $entrada)
                    $total_geral_t_entradas += $entrada;

                $total_geral_t_saidas = 0;
                foreach($array_total_t_saidas as $saida)
                    $total_geral_t_saidas += $saida;
            }

        
            if($centro_pai_id==0){
            
                if($lancamento_situacao==1){

                    $relatorio .= '
                    <div class="subTotal" align="right">
				        Total Geral de Entradas: '.$db->valorFormat($total_geral_entradas).' [T: '.$db->valorFormat($total_geral_t_entradas).']
                        <br/>
                        Total Geral de Saídas: '.$db->valorFormat($total_geral_saidas).' [T: '.$db->valorFormat($total_geral_t_saidas).']
			        </div>
                    ';

                }else{

                    $relatorio .= '
                    <div class="subTotal" align="right">
				        Total Geral de Entradas: '.$db->valorFormat($total_geral_entradas).'
                        <br/>
                        Total Geral de Saídas: '.$db->valorFormat($total_geral_saidas).'
			        </div>
                    ';
                }
            
            }else{
            
                if($nome_relatorio == "CONTAS PAGAS"){
                
                    $relatorio .= '
                    <div class="subTotal" align="right">
				        Total Geral: '.$db->valorFormat($total_geral_saidas).'
			        </div>
                ';
                
                }else{
                
                    $relatorio .= '
                    <div class="subTotal" align="right">
				        Total Geral: '.$db->valorFormat($total_geral_entradas).'
			        </div>
                ';
                }
            }
        

		    //limpa tabelas da memória
		    $db->query("drop table lancamentos_temp");
	    }

        $competencia = '';
        if($dtCompIni!='' && $dtCompFim!='')
            $competencia = '<div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Competência:</font> '.$dtCompIniRef.' - '.$dtCompFimRef.' </div>	<br>';
        elseif($dtCompIni!='')
            $competencia = '<div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Competência:</font> A partir de '.$dtCompIniRef.' </div>	<br>';
        elseif($dtCompFim!='')
            $competencia = '<div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Competência:</font> Até '.$dtCompFimRef.' </div>	<br>';



			if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="565">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>'.$nome_relatorio.'</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.' </div>
                                            '.$competencia.'
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div>
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';

	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,$nome_pdf,$array_dados['tp_print']);

    }

    /*
    ===========================================================================================
    SIMEI
    ===========================================================================================
    */

    function simei($db,$params){

	    //$jsonTxt = str_replace('\"','"',$params);
	    //$jsonTxt = str_replace('\\"','"',$jsonTxt);
	    $jsonObj = json_decode(stripslashes($params), true);
	    $array_dados = $jsonObj;

	    //periodo do relatório
	    $jsonTxtPeriodo = $array_dados["periodo"];
	    //$jsonTxtPeriodo = str_replace('\"','"',$jsonTxtPeriodo);
	    $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);

	    if($array_filtro_periodo["periodo"] == "data"){
		    $dt_referencia_ini = $array_filtro_periodo["dt_ini"];
		    $dt_referencia_fim = $array_filtro_periodo["dt_fim"];
		    $dt_ini = $db->data_to_sql($dt_referencia_ini);
		    $dt_fim = $db->data_to_sql($dt_referencia_fim);
		    $mes_ini = substr($dt_referencia_ini,3,2);
		    $ano_ini = substr($dt_referencia_ini,6,4);
		    $mes_fim = substr($dt_referencia_fim,3,2);
		    $ano_fim = substr($dt_referencia_fim,6,4);
	    }else{
		    $mes_ini = $array_filtro_periodo["mes"];
		    $ano_ini = $array_filtro_periodo["ano"];
		    $dt_ini = $ano_ini.'-'.$mes_ini.'-01';
		    $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;
	
		    $mes_fim = $array_filtro_periodo["mesFim"];
		    $ano_fim = $array_filtro_periodo["anoFim"];
		    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
		    $dt_fim = date('Y-m-d',$dt_fim_ts);
		    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);
	    }

	    $hora_relatorio = date('H:i:s');
	    $data_relatorio = date('d/m/Y');
	    $saldo_total = 0;
	    $linhas = '';

	    //busca pagamentos referentes ao DAS SIMEI
	    $query_valores = '
		    select sum(valor) valor, month(dt_compensacao) mes, year(dt_compensacao) ano
		    from lancamentos
		    where compensado = 1
			    and tipo = "P"
			    and documento_id = 23
			    and dt_compensacao >= "'.$dt_ini.'" and dt_compensacao <= "'.$dt_fim.'"
		    group by month(dt_compensacao), year(dt_compensacao)
	    ';
	    $arr_valores = $db->fetch_all_array($query_valores);

	    //$n = 0;
	    //$ponteiro_1 = 1;
	    foreach($arr_valores as $valor){
		    $mes =  $valor['mes'];
		    $ano =  $valor['ano'];
		    if($mes<10)
			    $mes = '0'.$mes;
		    /*
		    $ponteiro_2 = 0;
		    $dif = $mes - $ponteiro_1;
		    while($ponteiro_2<$dif){
			    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
			    $linhas .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left">'.$ponteiro_1.' / '.$ano_ini.'</td>
					    <td align="right">R$ '.$db->valorFormat(0).'</td>
				    </tr>
			    ';
			    $ponteiro_1++;
			    $ponteiro_2++;
			    $n++;
		    }
		    */
		    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
		    $linhas .= '
			    <tr bgcolor="'.$bg_color.'">
				    <td align="left">'.$mes.' / '.$ano.'</td>
				    <td align="right">R$ '.$db->valorFormat($valor['valor']).'</td>
			    </tr>
		    ';
		    //$ponteiro_1 = $mes + 1;
		    $n++;
		    $receita_bruta += $valor['valor'];
	    }

	    /*
	    $qtd_meses = count($arr_valores);
	    $ultimo_mes = $arr_valores[$qtd_meses-1]['mes'];

	    if($ultimo_mes<12){
		    $i = $ultimo_mes + 1;
		    while($i<=12){
			    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
			    $linhas .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="left">'.$i.' / '.$ano_ini.'</td>
					    <td align="right">R$ '.$db->valorFormat(0).'</td>
				    </tr>
			    ';
			    $i++;
			    $n++;
		    }
	    }
	    */
	    //fim busca valores
	
	    //cálculo da receita
	    $query_receita_bruta = '
		    select sum(valor) valor
		    from lancamentos
		    where conta_id in ('.$array_dados["contas_financeiras"].')
			    and compensado = 1
			    and tipo = "R"
			    and dt_compensacao >= "'.$dt_ini.'" and dt_compensacao <= "'.$dt_fim.'"
	    ';
	    $receita_bruta = $db->fetch_assoc($query_receita_bruta);
	    $receita_bruta = $receita_bruta['valor'];

	    $receita_bruta_icms = 0;
	    if($receita_bruta>60000){
		    $receita_bruta_icms = $receita_bruta - 60000;
	    }
	    //fim cálculo da receita

	    //cálculo da receita referente apenas à comércio, indústria e transporte
	    $query_receita_outras = '
		    select sum(valor) valor
		    from lancamentos
		    where conta_id in ('.$array_dados["contas_financeiras"].')
			    and compensado = 1
			    and tipo = "R"
			    and mei_outros = 1
			    and dt_compensacao >= "'.$dt_ini.'" and dt_compensacao <= "'.$dt_fim.'"
	    ';
	    $receita_outras = $db->fetch_assoc($query_receita_outras);
	    $receita_outras = $receita_outras['valor'];
	    //fim cálculo de outras receitas
	
	    //dados da empresa
	    $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
	    $empresa = $db_w2b->fetch_assoc("select nome, cpf_cnpj from clientes where id = 1"); //$_SESSION['cliente_id']
	    $db_w2b->close();
	    //fim dados da empresa

	    $relatorio .= ' 
		    <div class="titulo">	<span class="spanCinza"> 1 - Informações do Contribuinte </span> </div>
		    <div class="bordaArredondadaTitulo3">	<span class="spanCinza">  Nome Empresarial: '.$empresa['nome'].' </span> </div>
		    <div class="bordaArredondadaTitulo3" align="right">	<span class="spanCinza"> CNPJ: '.$empresa['cpf_cnpj'].' </span>  </div>
		    <br>
		    <div class="titulo">	<span class="spanCinza"> 2 - Resumo da Declaração </span> </div>
		    <div class="bordaArredondadaTitulo6" align="center"> 
		    <table border="0" cellpadding="0" cellspacing="0">
			    <thead>
				    <tr>
					    <td width="550" align="left"><span class="spanCinza"><b>PA</b></span></td>
					    <td width="140" align="right"><span class="spanCinza"><b>VALOR PAGO</b></span></td>
				    </tr>
			    </thead>
			    <tbody>
				    '.$linhas.'
			    </tbody>
		    </table>
		    </div>
		    <br>
		    <div class="titulo">	<span class="spanCinza"> 3 - Informações Socioeconômicas e Fiscais </span> </div>
		    <div class="bordaArredondadaTitulo3">	
			    <span class="spanCinza">Receita Bruta Total: R$ '.$db->valorFormat($receita_bruta).'</span><br>
			    <span class="spanCinza">Receita com comércio, indústria e transporte: R$ '.$db->valorFormat($receita_outras).'</span>
		    </div>
		    <div class="bordaArredondadaTitulo3" align="right">	<span class="spanCinza"> Receita Bruta sujeita ao ICMS: R$ '.$db->valorFormat($receita_bruta_icms).'</span><br> &nbsp;</div>
	    ';
	

		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="565">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>Declaração Anual do SIMEI</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período abrangido:</font> '.$dt_referencia_ini.' a '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div>
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
	
	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"SIMEI",$array_dados['tp_print']);

    }

    //CANRÊ LEÃO
    //===========================================================================================

    function CarneLeao($db,$params){

        $jsonObj = json_decode(stripslashes($params), true);
        $array_dados = $jsonObj;

        //periodo do relatório
        $periodo = self::PeriodoRelatorio($db,$array_dados["periodo"]);
        $periodoDtIni = $periodo["dtIni"];
        $periodoDtFim = $periodo["dtFim"];
        $dt_referencia_ini = $periodo["dtReferenciaIni"]; 
        $dt_referencia_fim = $periodo["dtReferenciaFim"];
        $ano  = date('Y',strtotime($periodoDtIni));
        $mesIni = intval(date('m',strtotime($periodoDtIni)));
        $mesFim = intval(date('m',strtotime($periodoDtFim)));

        $jsonTxtPeriodo = $array_dados["periodo"];
        $array_filtro_periodo = json_decode($jsonTxtPeriodo, true);
        $ano = $array_filtro_periodo['ano'];
        
        //$dt_referencia_ini = '01/01/'.$ano;
        //$dt_referencia_fim = '31/12/'.$ano;
        $hora_relatorio = date('H:i:s');
        $data_relatorio = date('d/m/Y');
        $relatorio = "";

        //contas financeiras do relatório
        $array_cf_id = explode(',',$array_dados["contas_financeiras"]);

        //start: Contas financeiras tributáveis selecionadas
        $titular = $_SESSION['nome'];
        $cpfCnpj = $_SESSION['cpf_cnpj'];

        $contasTributaveisSelecionadas = array();

        $contasTributaveis = $db->fetch_all_array("select id, nomeTitular, cpf_cnpj from contas where carne_leao = 1");

        foreach($contasTributaveis as $cfTributavel){
            
            if(in_array($cfTributavel['id'],$array_cf_id)){
                
                array_push($contasTributaveisSelecionadas, $cfTributavel['id']);
            }
        }

        $contasTributaveisId = join(',',$contasTributaveisSelecionadas);
        //end: Contas financeiras tributáveis selecionadas

        //start: Titular do carnê leão

        //end: Titular do carnê leão
        
        $n = 0;

        $listaLancamentos = '';

        $arrayValores = array(
                'recebimentosPf' => array('Total De Rendimentos PF'),
                'recebimentosPj' => array('Total De Rendimentos PJ'),
                'recebimentos' => array('Total De Rendimentos'),
                'deducoes' => array('Total De Despesas Dedutíveis'),
                'naoDedutivel' => array('Total De Despesas Não Dedutíveis'),
                'pagamentos' => array('Total De Despesas'),
                'recebimentosMenosDespesas' => array('Rendimentos - Despesas'),
                'base' => array('Base De Cálculo Do Imposto'),
                'imposto' => array('Imposto Calculado')
            );

        $nomeMeses = self::GetAllMeses();
        $cabecalhoMesesTabelaRendimentos = "";
        for($mes=$mesIni;$mes<=$mesFim;$mes++){
            
            $dtIni = date('Y-m-d',strtotime($ano.'-'.$mes.'-01'));
            $totalDiasMes = date('t',strtotime($dtIni));
            $dtFim = date('Y-m-d',strtotime($ano.'-'.$mes.'-'.$totalDiasMes));

            $totalRecebimentoPf = 0;
            $totalRecebimentoPj = 0;
            $totalPagamento = 0;
            $pagamentos = array();

            $queryLancamentos = mysql_query('
            select l.*, f.inscricao, f.nome, f.cpf_cnpj
            from lancamentos l
            join favorecidos f on l.favorecido_id = f.id
            where conta_id in ('.$contasTributaveisId.')
                and tipo in ("R","P")
                and ((dt_compensacao >= "'.$dtIni.'" and dt_compensacao <= "'.$dtFim.'" and compensado = 1))
			   order by dt_vencimento, l.id
            ', $db->link_id);
			//Parte da query removida para permitir o relatório só exibir os lançamentos compensados.
			// and ((dt_vencimento >= "'.$dtIni.'" and dt_vencimento <= "'.$dtFim.'" and compensado = 0) || (dt_compensacao >= "'.$dtIni.'" and dt_compensacao <= "'.$dtFim.'" and compensado = 1))
               
            while($lancamento = mysql_fetch_assoc($queryLancamentos)){

                if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
                $n++;
                
                //start: monta lista de lançamentos
                $dtCompensacao = ($lancamento['compensado']==0)? '' : $db->sql_to_data($lancamento['dt_compensacao']);

                $listaLancamentos .= '<tr bgcolor="'.$bg_color.'"><td align="center">'.$db->sql_to_data($lancamento['dt_vencimento']).'</td><td align="center">'.$dtCompensacao.'</td><td>'.$lancamento['nome'].'<br>'.strtoupper($lancamento['inscricao']).': '.$lancamento['cpf_cnpj'].'</td><td>'.$lancamento['descricao'].'</td><td align="right">'.$db->valorFormat($lancamento['valor']).'</td></tr>';
                //end: monta lista de lançamentos

                //start: separa valores de recebimento e pagamento de pj e pf
                if($lancamento['tipo']=='R'){
                    
                    if($lancamento['inscricao']=='cpf')
                        $totalRecebimentoPf += $lancamento['valor'];
                    else
                        $totalRecebimentoPj += $lancamento['valor'];
                    
                }elseif($lancamento['tipo']=='P'){
                    $totalPagamento += $lancamento['valor'];
                    array_push($pagamentos,$lancamento['id']);
                }
                //end: separa valores de recebimento e pagamento de pj e pf

            }

            //start: Soma pagamentos dedutíveis
            if(count($pagamentos)>0){
                
                $pagamentosId = join(',',$pagamentos);

                $pagamentosDedutiveis = mysql_fetch_assoc(mysql_query('
                select IFNULL(sum(a.valor),0) valor
                from ctr_plc_lancamentos a
                join plano_contas b on a.plano_contas_id = b.id and b.dedutivel = 1
                where lancamento_id in ('.$pagamentosId.')'));

                $deducoes = $pagamentosDedutiveis['valor'];

            }else{
                $deducoes = 0;
            }
            //end: Soma pagamentos dedutíveis

            //start: Valor base para cálculo do imposto devido
            $valorBaseImposto = $totalRecebimentoPf - $deducoes;
            //end: Valor base para cálculo do imposto devido

            //start: Alíquotas (Verificar se a idade do contribuinte será considerada para cálculo do imposto) - Só mexer aqui para adicionar novas alíquotas
            
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

            $timeMes = strtotime($ano.'-'.$mes.'-01');

            foreach($arrayTabelaVigencia as $key => $vigencia){
                $timeVigenciaIni = strtotime($vigencia['ano_ini'].'-'.$vigencia['mes_ini'].-'01');
                $timeVigenciaFim = strtotime($vigencia['ano_fim'].'-'.$vigencia['mes_fim'].-'01');
                //if( ($mes >= $vigencia['mes_ini'] && $ano >= $vigencia['ano_ini'] ) && ( ($vigencia['mes_fim'] == '' && $vigencia['ano_fim'] == '') || ($mes <= $vigencia['mes_fim'] && $ano <= $vigencia['ano_fim']) ) ){
                if( ($timeMes >= $timeVigenciaIni ) && ( ($vigencia['mes_fim'] == '' && $vigencia['ano_fim'] == '') || ($timeMes <= $timeVigenciaFim) ) ){
                    //echo $mes.'<br>';
                    $aliquotas = $arrayTabelas[$key];
                    break;
                }
            }
            
            $aliquotaMaxima = $aliquotas[count($aliquotas)-1];
            //end: Alíquotas (Verificar se a idade do contribuinte será considerada para cálculo do imposto) - Só mexer aqui para adicionar novas alíquotas

            //start: cálculo do imposto devido
            if($valorBaseImposto > $aliquotaMaxima['base']){
                $imposto =  $valorBaseImposto * $aliquotaMaxima['aliquota'] - $aliquotaMaxima['deducao'];
            }else{
                foreach($aliquotas as $aliquota){
                    
                    if($valorBaseImposto <= $aliquota['base']){
                        $imposto =  $valorBaseImposto * $aliquota['aliquota'] - $aliquota['deducao'];
                        if($imposto<0)
                            $imposto = 0;
                        break;
                    }
                    
                }
            }
            //end: cálculo do imposto devido

            //start: aloca valores do mês no array para depois montar o relatório
            $arrayValores['recebimentosPf'][$mes] = $db->valorFormat($totalRecebimentoPf);
            $arrayValores['recebimentosPj'][$mes] = $db->valorFormat($totalRecebimentoPj);
            $arrayValores['recebimentos'][$mes] = $db->valorFormat($totalRecebimentoPf + $totalRecebimentoPj);
            $arrayValores['deducoes'][$mes] = $db->valorFormat($deducoes);
            $arrayValores['naoDedutivel'][$mes] = $db->valorFormat($totalPagamento - $deducoes);
            $arrayValores['pagamentos'][$mes] = $db->valorFormat($totalPagamento);
            $arrayValores['recebimentosMenosDespesas'][$mes] = $db->valorFormat($totalRecebimentoPf + $totalRecebimentoPj - $totalPagamento);
            $arrayValores['base'][$mes] = $db->valorFormat($valorBaseImposto);
            $arrayValores['imposto'][$mes] = $imposto == 0 ? 'Isento' : $db->valorFormat($imposto);
            //end: aloca valores do mês no array para depois montar o relatório

            $cabecalhoMesesTabelaRendimentos .= "<td width='' align='right'><span class='spanCinza'>".$nomeMeses[intval($mes)]."</span></td>";

        }//end: for mês

        //start: monta lista de valores de todos os meses
        $valores = '';
        
        $n = 0;
        foreach($arrayValores as $key => $meses){
            if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
            $n++;
            $valores .= '<tr bgcolor="'.$bg_color.'">';
            $valores .= '<td align="left">'.$meses[0].'</td>'; //Identificação dos valores da respectiva linha, alinhada à esquerda
            array_shift($meses); //Retira do array a identificação da linha
            foreach($meses as $mes){
                $valores .= '<td align="right">'.$mes.'</td>';
            }
            $valores .= '</tr>';
        }


        //end: monta lista de valores de todos os meses

        //start: monta relatório com valores acumulados por mês e lançamentos
        $relatorio .= "
			
		<div class='bordaArredondadaTitulo6' align='center'> 
			
			<div class='cabecalhoInterno'>  
				<div class='bordaArredondadaTitulo4'>	
                    <span class='spanCinza'> 
                        &bull; Titular: $titular <br>
                        &bull; CPF: $cpfCnpj
                    </span> 
                </div>
			</div>
			
			<table border='0' cellpadding='0' cellspacing='0'>
				
				<thead>
					<tr>
						<td width='' align='center'><span class='spanCinza'></span></td>
						$cabecalhoMesesTabelaRendimentos
					</tr>
				</thead>
			
				<tbody>
					$valores
				</tbody>
	
			</table>									

            <br>

            <div class='cabecalhoInterno'>  
				<div class='bordaArredondadaTitulo4'><span class='spanCinza'> Lançamentos </span></div>
			</div>

            <table border='0' cellpadding='0' cellspacing='0'>
				
				<thead>
					<tr>
						<td width='1' align='center'><span class='spanCinza'>Vencimento</span></td>
                        <td width='1' align='center'><span class='spanCinza'>Compensação</span></td>
						<td width='250' align='left'><span class='spanCinza'>Favorecido</span></td>
						<td width='250' align='left'><span class='spanCinza'>Descrição</span></td>
						<td width='140' align='right'><span class='spanCinza'>Valor</span></td>
					</tr>
				</thead>
			
				<tbody>
					'.$listaLancamentos.'
				</tbody>
	
			</table>

		</div><br>";
		//end: monta relatório com valores acumulados por mês e lançamentos
		

		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }

        
        $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>CARNÊ LEÃO</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$dt_referencia_ini.' - '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <!--<div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>-->
	    ';
        
        $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
        

        self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,'Carnê_Leão',$array_dados['tp_print'],"A4-L");
    }

    //HISTÓRICO DE LANÇAMENTOS
    //===========================================================================================
    
    function HistoricoLancamentos($db,$params){
    
        $params = self::JsonDecode($params);

        $periodoRelatorio = self::PeriodoRelatorio($db,$params['periodo']);

        $contasFinanceiras = explode(',',$params['contas_financeiras']);

        $relatorio = '';

        //start:loop contas financeiras

        foreach($contasFinanceiras as $contaId){
        
            $queryContaFinanceira = 'select a.descricao, b.nome as banco from contas a left join bancos b on a.banco_id = b.id where a.id = '.$contaId;

            $contaFinanceira = $db->fetch_assoc($queryContaFinanceira);

            $lancamentosHtml = '';

            //start: busca histórico de lançamentos que tenham ou já tiveram vencimento ou tiveram alteração no período selecionado

            $queryHistorico = "
            select lancamento_id, dt_vencimento, excluido
            from lancamentos_historico
            where id in (
                select max(id) id
                from lancamentos_historico
                where ((dt_vencimento >= '".$periodoRelatorio['dtIni']."' and dt_vencimento <= '".$periodoRelatorio['dtFim']."') || (dt_alteracao >= '".$periodoRelatorio['dtIni']."' and dt_alteracao <= '".$periodoRelatorio['dtFim']."'))
                and conta_financeira_id = ".$contaId."
                group by lancamento_id
            )
            order by dt_vencimento
            ";
        
            $historico = $db->fetch_all_array($queryHistorico);

            //end: busca histórico de lançamentos que tenham ou já tiveram vencimento ou tiveram alteração no período selecionado

            //start: busca lançamentos

            foreach($historico as $lancamentoHistorico){

                $queryLancamentos = "select a.tipo, a.dt_vencimento, a.dt_compensacao, b.nome as favorecido, a.descricao, c.descricao as conta_financeira, d.nome as banco, a.valor, date_format(a.dt_alteracao, '%d/%m/%Y %H:%i:%s') dt_alteracao, a.usuario, excluido
                    from lancamentos_historico a
                    join favorecidos b on a.favorecido_id = b.id
                    join contas c on a.conta_financeira_id = c.id
                    left join bancos d on c.banco_id = d.id
                    where a.lancamento_id = ".$lancamentoHistorico['lancamento_id']."
                    order by a.dt_alteracao desc, a.id desc";

                $lancamentos = $db->fetch_all_array($queryLancamentos);

                $totalLancamentos = count($lancamentos);

                //lançamento exibido em destaque com as últimas alterações do lançamento
                $lancamentoAtual = $lancamentos[0];

                //complemento do lançamento em destaque caso ele esteja excluído
                if($lancamentoAtual['excluido']){
                    $lancamentoAtual['descricao'] = $lancamentos[1]['descricao'];
                    $lancamentoAtual['valor'] = $lancamentos[1]['valor'];
                }

                //cor do lançamento
                $cor = $lancamentoAtual['tipo'] == 'R' ? 'verde' : 'vermelho';

                $dtCompensacao = $lancamentoAtual['dt_compensacao'] == '0000-00-00' ? '-' : $db->sql_to_data($lancamentoAtual['dt_compensacao']);

                //$contaFinanceira = $lancamentoAtual['banco'] == '' ? $lancamentoAtual['conta_financeira'] : $lancamentoAtual['conta_financeira'].' - '.$lancamentoAtual['banco'];

                $lancamentosHtml .= '<tr bgcolor="#F0F0F0">'.
                    '<td style="color:red;font-size:20px">'.($lancamentoAtual['excluido'] ? '&bull;' : '').'</td>'.
                    '<td>'.$db->sql_to_data($lancamentoAtual['dt_vencimento']).'</td>'.
                    '<td>'.$dtCompensacao.'</td>'.
                    '<td>'.$lancamentoAtual['favorecido'].'</td>'.
                    '<td>'.$lancamentoAtual['descricao'].'</td>'.
                    //'<td>'.$contaFinanceira.'</td>'.
                    '<td><span class="'.$cor.'">'.$db->valorFormat($lancamentoAtual['valor']).'</span></td>'.
                    '<td>'.$lancamentoAtual['dt_alteracao'].'</td>'.
                    '<td>'.$lancamentoAtual['usuario'].'</td>'.
                '</tr>';

                $i = 1;

                if($totalLancamentos==1){

                    $lancamentosHtml .= '<tr><td></td></tr>';

                }else{

                    while($i <= ($totalLancamentos-1)){
                    
                        $lancamento = $lancamentos[$i];
                    
                        $dtCompensacao = $lancamento['dt_compensacao'] == '0000-00-00' ? '-' : $db->sql_to_data($lancamento['dt_compensacao']);

                        //$contaFinanceira = $lancamento['banco'] == '' ? $lancamento['conta_financeira'] : $lancamento['conta_financeira'].' - '.$lancamento['banco'];

                        $lancamentosHtml .= '<tr>'.
                            '<td style="color:red;font-size:20px">'.($lancamento['excluido'] ? '&bull;' : '').'</td>'.
                            '<td>'.$db->sql_to_data($lancamento['dt_vencimento']).'</td>'.
                            '<td>'.$dtCompensacao.'</td>'.
                            '<td>'.$lancamento['favorecido'].'</td>'.
                            '<td>'.$lancamento['descricao'].'</td>'.
                            //'<td>'.$contaFinanceira.'</td>'.
                            '<td><span class="'.$cor.'">'.$db->valorFormat($lancamento['valor']).'</span></td>'.
                            '<td>'.$lancamento['dt_alteracao'].'</td>'.
                            '<td>'.$lancamento['usuario'].'</td>'.
                        '</tr>';

                        $i++;
                    }

                    $lancamentosHtml .= '<tr><td></td></tr>';
                }
            }

            //end: busca lançamentos

            //start: adiciona lançamentos de cada conta ao html do relatório

            $relatorio .= "
            <div class='bordaArredondadaTitulo6' align='center'>
                <div class='cabecalhoInterno'>
					<div class='bordaArredondadaTitulo4'>
						<span class='spanCinza'>&bull; ".($contaFinanceira['banco'] == '' ? $contaFinanceira['descricao'] : $contaFinanceira['descricao'].' - '.$contaFinanceira['banco'])."</span>
					</div>
				</div>
                <table border='0' cellpadding='0' cellspacing='0'>
                    <thead>
                        <tr>
                            <td width='20'></td>
                            <td><span class='spanCinza'>VENCIMENTO</span></td>
                            <td><span class='spanCinza'>COMPENSAÇÃO</span></td>
                            <td><span class='spanCinza'>FAVORECIDO</span></td>
                            <td><span class='spanCinza'>DESCRIÇÃO</span></td>
                            <td><span class='spanCinza'>VALOR</span></td>
                            <td><span class='spanCinza'>ALTERAÇÃO</span></td>
                            <td><span class='spanCinza'>USUÁRIO</span></td>
                        </tr>
                    </thead>
                    <tbody>
                        ".$lancamentosHtml."
                    </tbody>
                </table>
            </div>
            ";
            
            //end: adiciona lançamentos de cada conta ao html do relatório
        }
        
        //end:loop contas financeiras

        //start: imprime pdf do relatório
		
		
		if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_branco.png"; }


        $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">RELATÓRIO DE</span>
										    <br><b>HISTÓRICO DE LANÇAMENTOS</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de Referência:</font> '.$periodoRelatorio['dtReferenciaIni'].' - '.$periodoRelatorio['dtReferenciaFim'].'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../'.$logo.'" width="150"></div>
	    ';
        
	    $pdfFooter = '
								    <div class="rodape" align="left" style="width:33,33%;">Emitido: '.date('d/m/Y').' as '.date('H:i:s').' </div> 
								    <div class="rodape" align="center" style="width:33,33%;"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" align="right" style="width:33,33%;">webfinancas.com</div>
	    ';

        self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"HISTÓRICO DE LANÇAMENTOS",$params['tp_print'],"A4-L");

        //end: imprime pdf do relatório
    }
}

?>