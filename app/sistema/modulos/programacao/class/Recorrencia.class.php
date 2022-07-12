<?php

class Recorrencia{

    var $lancamento_dados = array(
	    "tipo" => "",
	    "favorecido_id" => "",
	    "forma_pgto_id" => "",
	    "conta_id" => "",
	    "documento_id" => "",
	    "valor" => "",
	    "frequencia" => "",
	    "auto_lancamento" => "",
	    "observacao" => "",
	    "qtd_dias" => "",
	    "dia_mes" => "",
	    "dia_semana" => "",
	    "sab_dom" => 0,
	    "dt_inicio" => "",
	    "dt_vencimento" => "",
	    "dt_competencia" => "",
	    "dt_comp_mes_dif" => "",
	    "dt_emissao" => "",
	    "dt_prox_venc" => "",
	    "descricao" => "",
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
			
		    $dt_inicio = $db->data_to_sql($array_dados['dt_inicio']);
		    $this->lancamento_dados['dt_vencimento'] = $dt_inicio;

		    $this->lancamento_dados['dt_competencia'] = $db->data_to_sql('01/'.$array_dados['dt_competencia']);
		    $mes_dif = self::dtCompetenciaDif(); 
		    $this->lancamento_dados['dt_comp_mes_dif'] = $mes_dif;
								
		    if($array_dados['funcao']=='lancamentoIncluir' || $array_dados['dt_alterada']=='1'){
			
			    $this->lancamento_dados['dt_inicio'] = $dt_inicio;
			    $this->lancamento_dados['dt_prox_venc'] = $dt_inicio;

		    }else{

			    unset($this->lancamento_dados['dt_inicio']);
			    unset($this->lancamento_dados['dt_vencimento']);
			    unset($this->lancamento_dados['dt_prox_venc']);

		    }

		    $this->lancamento_dados['dt_emissao'] = $db->data_to_sql($array_dados['dt_emissao']);

	    }
    }

    /*
    ===========================================================================================
    INCLUÍR LANÇAMENTO
    ===========================================================================================
    */

    function lancamentoIncluir($db,$array_dados){

	    //========== PEGA A DATA DE INICIO ================
	    $dt_inicio = explode('-',$this->lancamento_dados['dt_inicio']);
	    $dia_inicio = $dt_inicio[2];
	    $mes_inicio = $dt_inicio[1];
	    $ano_inicio = $dt_inicio[0];	
	    //========== FIM PEGA A DATA DE INICIO ================

	    /*
	    if( $this->lancamento_dados[frequencia] < 30 ){
			
		    //========== PEGA A DATA DO PRIMEIRO VENCIMENTO ================
		    $dia_semana = $this->lancamento_dados[dia_semana];
		    $dia_semana_inicial = date('N',mktime(0,0,0,date($mes_inicio),date($dia_inicio),date($ano_inicio)));
		    $contador = 1;
		    if($dia_semana_inicial != $dia_semana){
			    while($dia_semana_inicial != $dia_semana){
				    $dia_semana_inicial = date('N',mktime(0,0,0,date($mes_inicio),date($dia_inicio)+$contador,date($ano_inicio)));
				    if($dia_semana_inicial == $dia_semana){
					    $dt_vencimento = 	date('Y-m-d',mktime(0,0,0,date($mes_inicio),date($dia_inicio)+$contador,date($ano_inicio)));
				    }
				    $contador += 1;
			    }
		    }else{
			    $dt_vencimento = 	date('Y-m-d',mktime(0,0,0,date($mes_inicio),date($dia_inicio),date($ano_inicio)));
		    }
		    $this->lancamento_dados[dt_vencimento] = $dt_vencimento;
		    $this->lancamento_dados[dt_prox_venc] = $dt_vencimento;
		    //========== FIM PEGA A DATA DO PRIMEIRO VENCIMENTO ================
			
	    }
	    */
		
	    $lancamento_id = $db->query_insert('lancamentos_recorrentes',$this->lancamento_dados);
	    self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$lancamento_id,$array_dados["tipo"],$this->lancamento_dados['valor']);
	    $retorno = array("situacao"=>1,"notificacao"=>"Lançamento recorrente incluído com sucesso.");
	    return $retorno;

    }

    /*
    ===========================================================================================
    EDITAR LANÇAMENTO
    ===========================================================================================
    */
 	
    function lancamentoEditar($db,$array_dados){
		
	    //========== PEGA A DATA DE INICIO ================
	    $dt_inicio = explode('-',$this->lancamento_dados[dt_inicio]);
	    $dia_inicio = $dt_inicio[2];
	    $mes_inicio = $dt_inicio[1];
	    $ano_inicio = $dt_inicio[0];	
	    //========== FIM PEGA A DATA DE INICIO ================

	    /*
	    if($this->lancamento_dados[frequencia] < 30){
			
		    //========== PEGA A DATA DO PRIMEIRO VENCIMENTO ================
		    $dia_semana = $this->lancamento_dados[dia_semana];
		    $dia_semana_inicial = date('N',mktime(0,0,0,date($mes_inicio),date($dia_inicio),date($ano_inicio)));
		    $contador = 1;
		    if($dia_semana_inicial != $dia_semana){
			    while($dia_semana_inicial != $dia_semana){
				    $dia_semana_inicial = date('N',mktime(0,0,0,date($mes_inicio),date($dia_inicio)+$contador,date($ano_inicio)));
				    if($dia_semana_inicial == $dia_semana){
					    $dt_vencimento = 	date('Y-m-d',mktime(0,0,0,date($mes_inicio),date($dia_inicio)+$contador,date($ano_inicio)));
				    }
				    $contador += 1;
			    }
		    }else{
			    $dt_vencimento = 	date('Y-m-d',mktime(0,0,0,date($mes_inicio),date($dia_inicio),date($ano_inicio)));
		    }
		    $this->lancamento_dados[dt_vencimento] = $dt_vencimento;
		    $this->lancamento_dados[dt_prox_venc] = $dt_vencimento;
		    //========== FIM PEGA A DATA DO PRIMEIRO VENCIMENTO ================
			
	    }
	    */

	    $db->query_update('lancamentos_recorrentes',$this->lancamento_dados,'id = '.$array_dados['lancamento_id']);
	    self::ctrPlcLancamentosAtualizar($db,$array_dados['ct_resp_lancamentos'],$array_dados['lancamento_id'],$array_dados["tipo"],$this->lancamento_dados['valor']);
	    $retorno = array("situacao"=>1,"notificacao"=>"Lançamento recorrente atualizado com sucesso.");
	    return $retorno;
    }

    /*
    ===========================================================================================
    EXCLUIR LANÇAMENTO
    ===========================================================================================
    */	

    function lancamentoExcluir($db,$lancamento_id){
	    $db->query("delete from lancamentos_recorrentes where id = ".$lancamento_id);
	    $db->query("delete from ctr_plc_lancamentos_rcr where lancamento_rcr_id = ".$lancamento_id);
	    $retorno = array("situacao"=>1,"notificacao"=>"Lançamento recorrente excluído com sucesso.");
	    return $retorno;
    }

    /*
    ===========================================================================================
    EXIBIR LANÇAMENTO
    ===========================================================================================
    */

    function lancamentoExibir($db,$array_dados){
	    $query = "
		    select lr.id, lr.favorecido_id, f.nome favorecido, IFNULL(concat(c.descricao,' - ',b.nome),c.descricao) conta, lr.conta_id, date_format(dt_inicio, '%d/%m/%Y') dt_inicio, date_format(dt_competencia, '%m/%Y') dt_competencia, date_format(dt_emissao, '%d/%m/%Y') dt_emissao, lr.descricao, valor, frequencia, qtd_dias, dia_semana, dia_mes, auto_lancamento, lr.documento_id, lr.forma_pgto_id, lr.observacao, lr.sab_dom
		    from lancamentos_recorrentes lr
		    left join contas c on lr.conta_id = c.id
		    left join bancos b on c.banco_id = b.id
		    left join favorecidos f on lr.favorecido_id = f.id
		    where lr.id = ".$array_dados[lancamento_id]."
	    ";

	    //monta a lista de lannçamentos do centro de responsabilidade

	    $query_ctr_plc_lancamentos = "
		    select pc.cod_conta, cr.cod_centro, crl.id ctr_plc_lancamento_id, cr.id centro_resp_id, pc.id plano_contas_id, pc.nome conta, cr.nome centro, crl.valor, crl.porcentagem
		    from ctr_plc_lancamentos_rcr crl
		    left join centro_resp cr on crl.centro_resp_id = cr.id
		    left join plano_contas pc on crl.plano_contas_id = pc.id
		    where crl.lancamento_rcr_id = ".$array_dados[lancamento_id]." and (crl.centro_resp_id <> 0 || crl.plano_contas_id <> 0)";

	    $array_ctr_plc_lancamentos = $db->fetch_all_array($query_ctr_plc_lancamentos);

	    $ctr_plc_lancamentos = "";

	    foreach($array_ctr_plc_lancamentos as $lancamento){
		    $valor = number_format($lancamento[valor],2,',','.');
		    $porcentagem = $lancamento[porcentagem] * 100;
		    $ctr_plc_lancamentos .= '{"ctr_plc_lancamento_id":"'.$lancamento['ctr_plc_lancamento_id'].'","plano_contas_id":"'.$lancamento['plano_contas_id'].'","conta":"'.$lancamento['cod_conta'].' - '.$lancamento['conta'].'","centro_resp_id":"'.$lancamento['centro_resp_id'].'","centro":"'.$lancamento['cod_centro'].' - '.$lancamento['centro'].'","valor":"'.$valor.'","porcentagem":"'.$porcentagem.'"},';
	    }
		
	    $ctr_plc_lancamentos = substr($ctr_plc_lancamentos,0,-1); //retira a ultima virgula

	    $jsonText = '['.$ctr_plc_lancamentos.']';
	    //fim da montagem da lista de lannçamentos do centro de responsabilidade

	    $lancamentos_exibir = $db->fetch_assoc($query);
	    $lancamentos_exibir[valor] = number_format($lancamentos_exibir[valor],2,',','.');
	    $retorno = array("lancamento"=>$lancamentos_exibir,"ctr_plc_lancamentos"=>$jsonText);
	    return $retorno;
    }

    /*
    ===========================================================================================
    LISTAR LANÇAMENTOS
    ===========================================================================================
    */

    //Lista dos lançamentos
    function lancamentosListar($db){
		
	    $lancamentos_listar ='
		    <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
		    <thead>
			    <th> 
					    <table width="100%"><tr>
						    <td>Descrição</td>
						    <td width="60">Opções</td>
					    </td></tr></table>
			    </th>
		    </thead>
		    <tbody>
	    ';
	
	    $query_lancamentos = "
		    select id, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, date_format(dt_prox_venc, '%d/%m/%Y') dt_prox_venc, qtd_dias, favorecido_id, descricao, valor, frequencia, dia_semana, dia_mes, tipo
		    from lancamentos_recorrentes
	    ";
		
	    $array_lancamentos = $db->fetch_all_array($query_lancamentos);
	
	    foreach($array_lancamentos as $lancamento){
		    $nome_favorecido = $db->fetch_assoc("select nome from favorecidos where id = ".$lancamento['favorecido_id']);	
		    $dia_vencimento = $lancamento[dia_mes];
		    switch ($lancamento[frequencia]){
			    case 0:
				    $title = " dia(s)";
				    $frequencia = "dia(s)";
				    $dia_vencimento = $lancamento[qtd_dias];
			    break;
			    case 7:
				    $title = " / Semanal";
				    $frequencia = "Semanal";
				    switch ($lancamento[dia_semana]){
					    case 1:
						    $dia_vencimento = "Seg";
					    break;
					    case 2:
						    $dia_vencimento = "Ter";
					    break;
					    case 3:
						    $dia_vencimento = "Qua";
					    break;
					    case 4:
						    $dia_vencimento = "Qui";
					    break;
					    case 5:
						    $dia_vencimento = "Sex";
					    break;
					    case 6:
						    $dia_vencimento = "Sáb";
					    break;
					    case 7:
						    $dia_vencimento = "Dom";
					    break;																																															
				    }
			    break;
			    case 30:
				    $title = " / Mensal";
				    $frequencia = "Mensal";
			    break;
			    case 60:
				    $title = " / Bimestral";
				    $frequencia = "Bim.";
			    break;
			    case 90:
				    $title = " / Trimestral";
				    $frequencia = "Trim.";
			    break;
			    case 120;
				    $title = " / Quadrimestral";
				    $frequencia = "Quad.";
			    break;
			    case 180;
				    $title = " / Semestral";
				    $frequencia = "Sem.";
			    break;
			    case 360;
				    $title = " / Anual";
				    $frequencia = "Anual";
			    break;
		    }

		    if($lancamento["tipo"]=="R"){
			    $cor_valor = "blue";
			    $form_id = 'form_rcbt_editar';
		    }else{
			    $cor_valor = "red";
			    $form_id = 'form_pgto_editar';
		    }

		    $lancamentos_listar .= '
			    <tr class="gradeA" id="tbl-lnct-row-'.$lancamento['id'].'">
								    <td class="updates newUpdate">

										    <div class="uDate tbWF tipS" original-title="Venc: '.$dia_vencimento.$title.'" align="center" style="width:50px;"> <span class="uDay">'.$dia_vencimento.'</span>'.$frequencia.'  <br></div>
											    <span class="lDespesa tbWF" style="width:65%;">
												    <a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento["descricao"].'</strong></a>
												    <span original-title="Favorecido" class="tipN" >'.$nome_favorecido["nome"].'</span>
                                                    <div class="red">Próximo Vencimento: '.$lancamento['dt_prox_venc'].'</div>
											    </span>
																
										    <div class="tbWFoption">
													    <a href="'.$lancamento["id"].'" original-title="Excluír" class="smallButton btTBwf redB tipS  lancamentoExcluir"><img src="images/icons/light/close.png"  width="10"></a>
													    <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentoExibir('.$lancamento["id"].',\''.$form_id.'\')"><img src="images/icons/light/pencil.png"  width="10"></a>
											    </div>
																																														
										    <div class="tbWFvalue '.$cor_valor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
					
							    </td> 
							    </tr>
		    ';
	    }
		
	    $lancamentos_listar .= '</tbody></table>';
	    return $lancamentos_listar;
    }

    /*
    ===========================================================================================
    ATUALIZAR LANÇAMENTOS NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
    ===========================================================================================
    */

    function ctrPlcLancamentosAtualizar($db,$lancamentos,$lancamento_id,$tp_lancamento,$valor){
		
        $qtd_lnct = 0;
        $qtd_lnct_excluido = 0;

        if($lancamentos != ''){

            $jsonTxt = str_replace('\"','"',$lancamentos);
            $jsonObj = json_decode($jsonTxt, true);
            $array_lancamentos = $jsonObj;
            //$db->query("delete from ctr_plc_lancamentos_rcr where lancamento_rcr_id = ".$lancamento_id);
            $qtd_lnct = count($array_lancamentos);
            $qtd_lnct_excluido = 0;
            
            if($qtd_lnct>0){
                
                foreach($array_lancamentos as $lancamento){
                    
                    //start:correção de bug que está incluindo valor e porcentagem maiores do que o limite
                    if($lancamento['porcentagem'] > 100)
                        $lancamento['porcentagem'] = 100;
                    //end:correção de bug que está incluindo valor e porcentagem maiores do que o limite

                    if($lancamento["operacao"]=="1"){ //inclui um novo lançamento

                        $array_insert["lancamento_rcr_id"] = $lancamento_id;
                        $array_insert["centro_resp_id"] = $lancamento["centro_resp_id"];
                        $array_insert["plano_contas_id"] = $lancamento["plano_contas_id"];
                        $array_insert["tp_lancamento"] = $tp_lancamento;
                        
                        $porcentagem = $lancamento["porcentagem"];
                        $array_insert["porcentagem"] = $porcentagem / 100; //A porcentagem é na escala de 0 a 1
                        
                        $array_insert["valor"] = ($porcentagem / 100) * $valor;

                        $db->query_insert("ctr_plc_lancamentos_rcr",$array_insert);

                    }elseif($lancamento["operacao"]=="2"){ //mantem o registro do lançamento ou edita futuramente

                        $array_update["valor"] = ($lancamento["porcentagem"] / 100) * $valor;
                        $array_update["porcentagem"] = $lancamento["porcentagem"] / 100;
                        $db->query_update("ctr_plc_lancamentos_rcr",$array_update,'id = '.$lancamento["ctr_plc_lancamento_id"]);
                        
                    }elseif($lancamento["operacao"]=="3"){ //exclui o lançamento
                        
                        if($lancamento["ctr_plc_lancamento_id"]!=0){
                            $db->query("delete from ctr_plc_lancamentos_rcr where id = ".$lancamento["ctr_plc_lancamento_id"]);
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
            $lnct_geral = $db->fetch_assoc("select id from ctr_plc_lancamentos_rcr where lancamento_rcr_id = ".$lancamento_id." and centro_resp_id = 0 and plano_contas_id = 0");
            
            //Inclui lançamento geral
            if(!$lnct_geral){
                $db->query_insert("ctr_plc_lancamentos_rcr",array(
                    'lancamento_rcr_id' => $lancamento_id,
                    'centro_resp_id' => 0,
                    'plano_contas_id' => 0,
                    'tp_lancamento' => $tp_lancamento,
                    'porcentagem' => 1,
                    'valor' => $valor
                    ));
            }else{
                $db->query('update ctr_plc_lancamentos_rcr set valor = '.$valor.' where lancamento_rcr_id = '.$lancamento_id);
            }
        }else{
            $db->query('delete from ctr_plc_lancamentos_rcr where lancamento_rcr_id = '.$lancamento_id.' and centro_resp_id = 0 and plano_contas_id = 0');
        }
    }

    /*
    ===========================================================================================
    CALCULAR DIFERENÇA ENTRE DATA DE VENCIMENTO E DATA DE COMPETÊNCIA EM ESCALA DE MESES
    ===========================================================================================
    */

    function dtCompetenciaDif(){
	
	    //separa o dia, mes e ano da data de vencimento
	    $dt_vencimento = $this->lancamento_dados['dt_vencimento'];
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

        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        if($sSearch==""){
            $query_lancamentos = "
		        select l.id, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, date_format(dt_prox_venc, '%d/%m/%Y') dt_prox_venc, qtd_dias, favorecido_id, f.nome, descricao, valor, frequencia, dia_semana, dia_mes, l.tipo
		        from lancamentos_recorrentes l
                join favorecidos f on l.favorecido_id = f.id";
        }else{
            $query_lancamentos = "
		        select l.id, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, date_format(dt_prox_venc, '%d/%m/%Y') dt_prox_venc, qtd_dias, favorecido_id, f.nome, descricao, valor, frequencia, dia_semana, dia_mes, l.tipo
		        from lancamentos_recorrentes l
                join favorecidos f on l.favorecido_id = f.id
                where descricao like '%".$sSearch."%' or nome like '%".$sSearch."%'";
        }

		$iTotalDisplayRecords = mysql_num_rows(mysql_query($query_lancamentos, $db->link_id));

        $query_lancamentos = mysql_query($query_lancamentos.' limit '.$iDisplayStart.",".$iDisplayLength, $db->link_id);

	    while($lancamento = mysql_fetch_assoc($query_lancamentos)){

            $dia_vencimento = $lancamento[dia_mes];
		    
            switch ($lancamento[frequencia]){
			    case 0:
				    $title = " dia(s)";
				    $frequencia = "dia(s)";
				    $dia_vencimento = $lancamento[qtd_dias];
                    break;
			    case 7:
				    $title = " / Semanal";
				    $frequencia = "Semanal";
				    switch ($lancamento[dia_semana]){
					    case 1:
						    $dia_vencimento = "Seg";
                            break;
					    case 2:
						    $dia_vencimento = "Ter";
                            break;
					    case 3:
						    $dia_vencimento = "Qua";
                            break;
					    case 4:
						    $dia_vencimento = "Qui";
                            break;
					    case 5:
						    $dia_vencimento = "Sex";
                            break;
					    case 6:
						    $dia_vencimento = "Sáb";
                            break;
					    case 7:
						    $dia_vencimento = "Dom";
                            break;																																															
				    }
                    break;
			    case 30:
				    $title = " / Mensal";
				    $frequencia = "Mensal";
                    break;
			    case 60:
				    $title = " / Bimestral";
				    $frequencia = "Bim.";
                    break;
			    case 90:
				    $title = " / Trimestral";
				    $frequencia = "Trim.";
                    break;
			    case 120;
				    $title = " / Quadrimestral";
				    $frequencia = "Quad.";
                    break;
			    case 180;
				    $title = " / Semestral";
				    $frequencia = "Sem.";
                    break;
			    case 360;
				    $title = " / Anual";
				    $frequencia = "Anual";
                    break;
		    }

		    if($lancamento["tipo"]=="R"){
			    $cor_valor = "blue";
			    $form_id = 'form_rcbt_editar';
		    }else{
			    $cor_valor = "red";
			    $form_id = 'form_pgto_editar';
		    }

		    $dadosLnct = '
			    <div class="uDate tbWF tipS" original-title="Venc: '.$dia_vencimento.$title.'" align="center" style="width:50px;"> <span class="uDay">'.$dia_vencimento.'</span>'.$frequencia.'  <br></div>
				    <span class="lDespesa tbWF" style="width:65%;">
					    <a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento["descricao"].'</strong></a>
					    <span original-title="Favorecido" class="tipN" >'.$lancamento["nome"].'</span>
                        <div class="red">Próximo Vencimento: '.$lancamento['dt_prox_venc'].'</div>
				    </span>
																
			    <div class="tbWFoption">
				    <a href="'.$lancamento["id"].'" original-title="Excluír" class="smallButton btTBwf redB tipS  lancamentoExcluir" id="link-exc-'.$lancamento['id'].'"><img src="images/icons/light/close.png"  width="10"></a>
				    <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentoExibir('.$lancamento["id"].',\''.$form_id.'\')"><img src="images/icons/light/pencil.png"  width="10"></a>
				</div>
																																														
			    <div class="tbWFvalue '.$cor_valor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
		    ';
	    
            array_push($aaData,array('lancamento'=>$dadosLnct));

        }
        
        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        return json_encode($retorno);
         
    }

}

?>