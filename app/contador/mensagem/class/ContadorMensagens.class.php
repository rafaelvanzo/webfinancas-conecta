<?php
class ContadorMensagens{
	
	var $dados = array(
		"email"=>"",
		"senha"=>"",
	);
	
	/*
	================================================================================================
	CONSTRUTOR
	================================================================================================
     */

	function __construct(){
	}

	/*
	================================================================================================
	ENVIAR EMAIL
	================================================================================================
     */

	function emailEnviar($email_destinatario,$assunto,$conteudo){

		$email_remetente = "contato@webfinancas.com";
		$nome_remetente = "Web Finanças";
		
		/*=========== INICIALIZA O OBJETO QUE ENVIA O EMAIL =======================================*/
		$transport = Swift_SmtpTransport::newInstance('smtp.webfinancas.com', 587); //$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 25);
		$transport->setUsername('contato@webfinancas.com');
		$transport->setPassword('W2BSISTEMAS');
		
		$message = Swift_Message::newInstance();
		$message->setSubject($assunto);
		$message->setFrom(array($email_remetente => $nome_remetente));
		//$message->setReturnPath('fabio@web2business.com.br');
        
		$mailer = Swift_Mailer::newInstance($transport);
		/*==============================================================================================*/
		
		$message->setBody($conteudo, 'text/html');
		$message->setTo(array($email_destinatario)); //não precisa limpar o destinatario a cada envio, esta função sobre-escreve o destinatario anterior
		
		$mailer->send($message); 

	}
     
    
    
/*
===========================================================================================
DATA TABLE AJAX
===========================================================================================
*/

function DataTableAjax($db, $db_wf, $db_w2b, $params){
    
    
    session_start(); 
    $cliente_id = $_SESSION['cliente_id'];
    
    //filtro do data table
    $sSearch = $params["sSearch"];
    $sEcho = $params["sEcho"];
    $iDisplayStart = $params["iDisplayStart"];
    $iDisplayLength = $params["iDisplayLength"];
    //$iTotalRecords = $db->numRows('select id from lancamentos');
    $iTotalDisplayRecords = 0;
    
    $aaData = array();
    
    if($sSearch==""){

        $query_dataTable = "SELECT * FROM chat_categorias WHERE situacao = 1 AND contador_id = ".$cliente_id;

    }else{
        //Query princial
        $query_dataTable = "SELECT * FROM chat_categorias WHERE situacao = 1 AND contador_id = ".$cliente_id." AND assunto like '%".$sSearch."%'";
    }   
    
    
    //total de registros
    $iTotalDisplayRecords = $db_wf->numRows('select id from ('.$query_dataTable.') as lancamentos WHERE situacao = 1 AND contador_id = '.$cliente_id);
    
    //Limita a consulta no db de acordo com a configuração da tabela
    $query_order .= ' order by dt_novas_mensagens DESC, id limit '.$iDisplayStart.','.$iDisplayLength;
      
    
    $array_dados = $db_wf->fetch_all_array($query_dataTable.$search.$query_order);
    

    foreach($array_dados as $dados){	
        
        
        
         
       $detalhes = $db_wf->fetch_assoc('SELECT tp_solicitacao,
                                                funcionario_id,
                                                recalculo_tp,
                                                recalculo_tp_gps,
                                                DATE_FORMAT(recalculo_dt_competencia, "%d/%m/%Y") as recalculo_dt_competencia,
                                                DATE_FORMAT(recalculo_dt_pgto, "%d/%m/%Y") as recalculo_dt_pgto,
                                                rescisao_funcionario_id
                                        FROM chat_solicitacao WHERE chat_categoria_id ='.$dados['id']);
        
        //No cliente entra o setor da contabilidade e no contador mostra quem é o cliente
       
       
       $cliente_db_id = $db_wf->fetch_assoc("SELECT db, db_senha FROM clientes_db WHERE cliente_id = ".$dados['cliente_id']);
       
       if
        ($detalhes['tp_solicitacao'] == 1){
                
           $db_cliente2 = new Database("mysql.webfinancas.com", $cliente_db_id['db'], $cliente_db_id['db_senha'], $cliente_db_id['db']);
           
           $nome_admissao = mysql_fetch_assoc(mysql_query('SELECT nome FROM funcionarios WHERE id ='.$detalhes['funcionario_id']), $db_cliente2->link_id) or mysql_error();
           $det = "Admissão de: <b>".$nome_admissao."</b>";
           //$db_cliente2->close();
       }elseif($detalhes['tp_solicitacao'] == 2){
                          
                   if($detalhes['recalculo_tp'] == 1){
                                    $det = "CONFINS";
                   }elseif($detalhes['recalculo_tp'] == 2){
                                    $det = "CSLL";
                   }elseif($detalhes['recalculo_tp'] == 3){
                                    $det = "DAS";
                   }elseif($detalhes['recalculo_tp'] == 4){
                                    $det = "FGTS";
                   }elseif($detalhes['recalculo_tp'] == 5){
                                    $det = "GPS";
                   }elseif($detalhes['recalculo_tp'] == 6){
                                    $det = "IRPJ";
                   }elseif($detalhes['recalculo_tp'] == 7){
                                    $det = "ISS";
                   }elseif($detalhes['recalculo_tp'] == 8){
                                    $det = "PIS";
                                }

                   if(!empty($detalhes['recalculo_tp_gps'])){    
                            
                       if($detalhes['recalculo_tp_gps'] == 1){
                                        $det2 = " -> GPS Trabalhador";
                       }elseif($detalhes['recalculo_tp_gps'] == 2){
                                        $det2 = " -> GPS Trabalhador autonômo";
                       }elseif($detalhes['recalculo_tp_gps'] == 3){
                                        $det2 = " -> GPS Trabalhador doméstica";
                                    }
                                }  
                            
                   $det = 'Imposto: <b>'.$det.' '.$det2.'</b> - Data de Competência: <b>'.$detalhes['recalculo_dt_competencia'].'</b> - Data do Pagamento: <b>'.$detalhes['recalculo_dt_pgto'].'</b>'; 
            
       }elseif($detalhes['tp_solicitacao'] == 3){
                   
           $db_cliente2 = new Database("mysql.webfinancas.com", $cliente_db_id['db'], $cliente_db_id['db_senha'], $cliente_db_id['db']);
           
           $nome_rescisao = mysql_fetch_assoc(mysql_query('SELECT nome FROM funcionarios WHERE id ='.$detalhes['rescisao_funcionario_id']), $db_cliente2->link_id);
            $det = "Rescisão de: <b>".$nome_rescisao.'</b>';
           //$db_cliente2->close();
        }else{ 
            $det = "Geral"; // inserir o assunton depois **
        }
       
       
        
        // ============ data ============
        $dt_cadastro = explode("-", $dados['dt_novas_mensagens']);
        $dia = substr($dt_cadastro[2], 0, 2);
        $m = $dt_cadastro[1];
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
        $ano = substr($dt_cadastro[0], -2);
        $horaio = substr($dt_cadastro[2], 3, 11);
        $ultima_interacao = $dia.'/'.$m.'/'.$ano.' - '.$horaio;
        
        $qtd_msg_novas = $db_wf->numRows('SELECT id FROM chat_mensagens WHERE chat_categoria_id = '.$dados['id'].' AND visualizada = 0 AND remetente_id != '.$cliente_id);
        
        if($qtd_msg_novas > 0){
        $qtd_msg = '<div class="tbWFoption">
		         <a href="javascript://void(0);" title="Nova mensagem" class="smallButton btTBwf redB ">'.str_pad($qtd_msg_novas, 2, "0", STR_PAD_LEFT).'</a
		 </div>';
        }
        
                        
        $nome = $db_w2b->fetch_assoc("SELECT nome FROM clientes WHERE id =".$dados['cliente_id']);
        
        $registros_tabela = '<span class="tabelaLink" onClick="abrirMensagem('.$dados['id'].');">
                                    <div class="uDate tbWF tipS" title="Última interação: '.$ultima_interacao.'" align="center" > <span class="uDay ">'.$dia.'</span>'.$mes.'/'.$ano.'<br></div>
                                    <span class="lDespesa tbWF">
					                    <b>'.$nome['nome'].':</b> <strong class="blue">'.$dados['assunto'].' </strong>
						                <span class="tipN">'.$det.'</span>
				                    </span>
                                   '.$qtd_msg.'
                             </span>';
        
        
        
         //insere resultado dentro do arra $aaData
         array_push($aaData,array('msg'=>$registros_tabela));

         $qtd_msg_novas = '';
         
    }
    
    $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
    
    return json_encode($retorno);
   // $db_w2b->close();
}

/*
===========================================================================================
MENSAGEM ADD
===========================================================================================
*/

function addMensagem($db, $db_wf, $params){
 
    session_start();
    $cliente_id = $_SESSION['cliente_id'];
     
    //Solicitação
    $solicitcacao['tp_solicitacao'] = $params['solicitacao'];
    
    if($params['solicitacao'] == "1"){
        $registros['assunto'] = "Admissão de empregados";
        
        //Solicitação
        $solicitcacao['funcionario_id'] = $params['funcionario_id'];
        
    }elseif($params['solicitacao'] == "2"){
        
        $registros['assunto'] = "Recálculo de impostos e contribuições";
        
        //Solicitação
        $solicitcacao['recalculo_tp'] = $params['recalculo_tp'];
        $solicitcacao['recalculo_tp_gps'] = $params['recalculo_tp_gps'];
                                                    $r_dt_c = explode("/",$params['recalculo_dt_competencia']);                                                    
        $solicitcacao['recalculo_dt_competencia'] = $r_dt_c[1].'-'.$r_dt_c[0].'-01';
                                             $r_dt_p = explode("/",$params['recalculo_dt_pgto']); 
        $solicitcacao['recalculo_dt_pgto'] = $r_dt_p[2].'-'.$r_dt_p[1].'-'.$r_dt_p[0];
    
    }elseif($params['solicitacao'] == "3"){
        
        $registros['assunto'] = "Rescisão contratual";
    
        //Solicitação
        $solicitcacao['rescisao_funcionario_id'] = $params['rescisao_funcionario_id'];
                                        $r_dt = explode("/",$params['rescisao_data']); 
        $solicitcacao['rescisao_data'] = $r_dt[2].'-'.$r_dt[1].'-'.$r_dt[0];
        $solicitcacao['rescisao_solicitante'] = $params['rescisao_solicitante'];
        $solicitcacao['rescisao_modalidade'] = $params['rescisao_modalidade'];
        
    }else{  $registros['assunto'] = $params['assunto']; }    
    
        $contador_id = $db->fetch_assoc('SELECT contador_id FROM conexao WHERE conectado = 1');
        
    $registros['cliente_id'] = $cliente_id;
    $registros['contador_id'] = $contador_id['contador_id'];
    $registros['setor_id'] = $params['solicitacao'];
    $registros['dt_cadastro'] = date('Y-m-d H:i:s');
    $registros['dt_conclusao'] = "";
    $registros['situacao'] = 1;
    $registros['dt_novas_mensagens'] = date('Y-m-d H:i:s');
    $registros['qtd_novas_mensagens'] = 1;
        
        $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
        $id = $db_wf->query_insert('chat_categorias',$registros);

    /* === Solicitação ===*/      
        
        $solicitcacao['chat_categoria_id'] = $id;
    
        $db_wf->query_insert('chat_solicitacao',$solicitcacao);
        
    /* === Mensagem ===*/    
    
    $registro_msg['chat_categoria_id'] = $id;
    $registro_msg['mensagem'] = $params['mensagem'];
    $registro_msg['remetente_id'] = $cliente_id; /* trocar no módulo contador para 'contador_id' */
    $registro_msg['visualizada'] = 0;
    $registro_msg['dt_cadastro'] = date('Y-m-d H:i:s');
    
    
         $id_mensagem = $db_wf->query_insert('chat_mensagens',$registro_msg);
    
   

}

/*
===========================================================================================
MENSAGEM ABRIR
===========================================================================================
*/

function abrirMensagem( $db_wf, $params){
    
    $cliente_id2 = $db_wf->fetch_assoc("SELECT cliente_id FROM chat_categorias WHERE id = ".$params['chat_categoria_id']);
    
    $cliente_db_id = $db_wf->fetch_assoc("SELECT db, db_senha FROM clientes_db WHERE cliente_id = ".$cliente_id2['cliente_id']);
    
    $db = new Database("mysql.webfinancas.com", $cliente_db_id['db'], $cliente_db_id['db_senha'], $cliente_db_id['db']);
    
    
    /* ========================= */
    $solicitcacao = $db_wf->fetch_assoc("SELECT *, 
    DATE_FORMAT(rescisao_data, '%d/%m/%Y') as rescisao_data, 
    DATE_FORMAT(recalculo_dt_competencia, '%d/%m/%Y') as recalculo_dt_competencia, 
    DATE_FORMAT(recalculo_dt_pgto, '%d/%m/%Y') as recalculo_dt_pgto 
    FROM chat_solicitacao WHERE chat_categoria_id = ".$params['chat_categoria_id']);
    
    if($solicitcacao['tp_solicitacao'] == "1"){
        
        $titulo = "Admissão de empregados";
        
        //Abas visiveis
        $abas = 1;
        
        $funcionarios = $db->fetch_assoc("SELECT *, 
        DATE_FORMAT(dt_exame_admissional, '%d/%m/%Y') as dt_exame_admissional, 
        DATE_FORMAT(dt_nasc, '%d/%m/%Y') as dt_nasc, 
        DATE_FORMAT(dt_admissao, '%d/%m/%Y') as dt_admissao, 
        DATE_FORMAT(dt_demissao, '%d/%m/%Y') as dt_demissao,
        DATE_FORMAT(carteira_dt_emissao, '%d/%m/%Y') as carteira_dt_emissao,
        DATE_FORMAT(rg_dt_emissao, '%d/%m/%Y') as carteira_dt_emissao,
        DATE_FORMAT(pis_dt_inscricao, '%d/%m/%Y') as pis_dt_inscricao
        FROM funcionarios WHERE id = ".$solicitcacao['funcionario_id']);
        
        //1º emprego?
        if($funcionarios['primeiro_emprego_ano'] == 1){ $primeiro_emprego = 'SIM'; }else{ $primeiro_emprego = 'NÃO'; }
        
        //Desconto de transporte?
        if($funcionarios['desconto_transporte'] == 1){ $desconto_transporte = 'SIM'; }else{ $desconto_transporte = 'NÃO'; }
        
        //Adicional noturno?
        if($funcionarios['adicional_noturno'] == 1){ $adicional_noturno = 'SIM'; }else{ $adicional_noturno = 'NÃO'; }
        
        //insalubridade?
        if($funcionarios['insalubridade'] == 1){ $insalubridade = 'SIM'; }else{ $insalubridade = 'NÃO'; }
        
        //Sindicalizado?
        if($funcionarios['sindicalizado'] == 1){ $sindicalizado = 'SIM'; }else{ $sindicalizado = 'NÃO'; }
        
        //$optante_fgts?
        if($funcionarios['optante_fgts'] == 1){ $optante_fgts = 'SIM'; }else{ $optante_fgts = 'NÃO'; }
      
        //Tipo de salario
        if($funcionarios['tp_salario'] == 1){ $tp_salario = 'Mensal'; }                           
        elseif($funcionarios['tp_salario'] == 2){ $tp_salario = 'Quinzenal';  }                                
        elseif($funcionarios['tp_salario'] == 3){ $tp_salario = 'Semanal';  }                                
        elseif($funcionarios['tp_salario'] == 4){ $tp_salario = 'Diário';  }                                
        elseif($funcionarios['tp_salario'] == 5){ $tp_salario = 'Horário';  }                                
        elseif($funcionarios['tp_salario'] == 6){ $tp_salario = 'Tarefa';  }                                
        else{ $tp_salario = 'Outros';  }                                
        
        //Sexo
        if($funcionarios['sexo'] == 'M'){ $sexo = 'Masculino'; }else{ $sexo = 'Feminino'; }

        //Cor / Raça?
        if($funcionarios['raca'] == 1){ $raca = 'Branco'; }                           
        elseif($funcionarios['raca'] == 2){ $raca = 'Negro';  }                                
        elseif($funcionarios['raca'] == 3){ $raca = 'Amarelo';  }                                
        elseif($funcionarios['raca'] == 4){ $raca = 'Pardo';  }                                                            
        else{ $raca = 'Índio';  } 
        
        //Deficiente?
        if($funcionarios['deficiente'] == 1){ $deficiente = 'SIM'; }else{ $deficiente = 'NÃO'; }
        
        //Estado civil:
        if($funcionarios['estado_civil'] == 1){ $estado_civil = 'Solteiro(a)'; }                           
        elseif($funcionarios['estado_civil'] == 2){ $estado_civil = 'Casado(a)';  }                                
        elseif($funcionarios['estado_civil'] == 3){ $estado_civil = 'Divorciado(a)';  }                                
        else{ $estado_civil = 'Viúvo(a)';  }                                                            
        
        //Grau de instrução
        if($funcionarios['instrucao'] == 1){ $grau_instrucao = '1º Grau Incompleto(a)'; }                           
        elseif($funcionarios['instrucao'] == 2){ $grau_instrucao = '1º Grau Completo';  }                                
        elseif($funcionarios['instrucao'] == 3){ $grau_instrucao = '2º Grau Incompleto';  }  
        elseif($funcionarios['instrucao'] == 4){ $grau_instrucao = '2º Grau Completo';  }                                
        elseif($funcionarios['instrucao'] == 5){ $grau_instrucao = 'Superior Incompleto';  }  
        elseif($funcionarios['instrucao'] == 6){ $grau_instrucao = 'Superior Completo';  }                                
        elseif($funcionarios['instrucao'] == 7){ $grau_instrucao = 'Pós Graduado';  }  
        elseif($funcionarios['instrucao'] == 8){ $grau_instrucao = 'Mestrado';  }                                
        elseif($funcionarios['instrucao'] == 9){ $grau_instrucao = 'Doutorado';  }  
        else{ $grau_instrucao = 'Pós Doutorado';  }    
        
        //Função
        $funcao = $db->fetch_assoc("SELECT nome FROM func_funcoes WHERE id = ".$funcionarios['funcao_id']);       
        
        //=======================//
        
        $detalhes_solicitacao = ' <br>
 
      <h6 align="left" style="padding-left:15px;">Informações Gerais</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 
 
   <div class="formRow">
        <span class="span5">
          <label>Função: </label> 
          <input type="text" value="'.$funcao['nome'].'" readonly="readonly"/>
        </span>
        <span class="span4">
          <label>Data exam. admissional: </label> 
          <input type="text" value="'.$funcionarios['dt_exame_admissional'].'" readonly="readonly"/>
        </span>
        <span class="span3">
          <label>Data admissão: </label> 
          <input type="text" value="'.$funcionarios['dt_admissao'].'" readonly="readonly"/>
        </span>       
    </div>     
    
     <div class="formRow">
        <span class="span4">
          <label>1º emprego Do Ano? </label> 
          <input type="text" value="'.$primeiro_emprego.'" readonly="readonly"/>
        </span>
        <span class="span3">
          <label>Desconto vale transp.? </label> 
          <input type="text" value="'.$desconto_transporte.'" readonly="readonly"/>
        </span>
        <span class="span3">
          <label>Adicional noturno? </label> 
          <input type="text" value="'.$adicional_noturno.'" readonly="readonly"/>
        </span>   
        <span class="span2">
          <label>Insalubridade? </label> 
          <input type="text" value="'.$insalubridade.'" readonly="readonly"/>
        </span>
    </div>   
    
    <div class="formRow">        
        <span class="span3">
          <label>Sindicalizado? </label> 
          <input type="text" value="'.$sindicalizado.'" readonly="readonly"/>
        </span> 
        <span class="span4">
          <label>Sindicato: </label> 
          <input type="text" value="'.$funcionarios['sindicato'].'" readonly="readonly"/>
        </span>
         <span class="span2">
          <label>Optante FGTS: </label> 
          <input type="text" value="'.$optante_fgts.'" readonly="readonly"/>
        </span>
         <span class="span3">
          <label>Código banco: </label> 
          <input type="text" value="'.$funcionarios['cod_banco_fgts'].'" readonly="readonly"/>
        </span>
    </div> 
    
    <div class="formRow"> 
        <span class="span3">
          <label>Tipo de salário: </label> 
          <input type="text" value="'.$tp_salario.'" readonly="readonly"/>
        </span>
        <span class="span5">
          <label>Salário atual: </label> 
          <input type="text" value="'.number_format($funcionarios['salario'], 2, ',', '.').'" readonly="readonly"/>
        </span>
    </div>
     
    <br>
   <h6 align="left" style="padding-left:15px;">Informações do Funcionário</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 

      <div class="formRow">
          <span class="span5">
          <label>Nome do funcionário: </label> 
          <input type="text" value="'.$funcionarios['nome'].'" readonly="readonly"/>
         </span>
         <span class="span2">
          <label>Data nasc.: </label> 
          <input type="text" value="'.$funcionarios['dt_nasc'].'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Cidade nasc.: </label> 
          <input type="text" value="'.$funcionarios['cidade_nasc'].'" readonly="readonly"/>
         </span>
         <span class="span2">
          <label>UF nasc.: </label> 
          <input type="text" value="'.$funcionarios['uf_nasc'].'" readonly="readonly"/>
         </span>
         
    </div>
    
    <div class="formRow">
        <span class="span3">
          <label>CPF: </label> 
          <input type="text" value="'.$funcionarios['cpf'].'" readonly="readonly"/>
         </span> 
        <span class="span3">
          <label>RG: </label> 
          <input type="text" value="'.$funcionarios['rg'].'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Orgão emissor RG: </label> 
          <input type="text" value="'.$funcionarios['rg_emissor'].'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Data de emissão RG: </label> 
          <input type="text" value="'.$funcionarios['rg_dt_emissao'].'" readonly="readonly"/>
         </span>                
    </div>
    
     <div class="formRow">
          <span class="span3">
          <label>PIS: </label> 
          <input type="text" value="'.$sexo.'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Data insc. PIS: </label> 
          <input type="text" value="'.$funcionarios['pis_dt_inscricao'].'" readonly="readonly"/>
         </span> 
         <span class="span3">
          <label>Cart. profissional: </label> 
          <input type="text" value="'.$funcionarios['carteira'].'" readonly="readonly"/>
         </span> 
          <span class="span3">
          <label>Data emissão carteira: </label> 
          <input type="text" value="'.$funcionarios['carteira_dt_emissao'].'" readonly="readonly"/>
         </span>
    </div>  
     
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 
    
   <div class="formRow">
          <span class="span6">
          <label>Nome do Pai: </label> 
          <input type="text" value="'.$funcionarios['nome_pai'].'" readonly="readonly"/>
         </span>
         <span class="span6">
          <label>Nome da Mãe: </label> 
          <input type="text" value="'.$funcionarios['nome_mae'].'" readonly="readonly"/>
         </span>         
    </div>
    
     <div class="formRow">
          <span class="span2">
          <label>Sexo: </label> 
          <input type="text" value="'.$sexo.'" readonly="readonly"/>
         </span>
         <span class="span2">
          <label>Cor/Raça: </label> 
          <input type="text" value="'.$raca.'" readonly="readonly"/>
         </span> 
         <span class="span2">
          <label>Deficiente? </label> 
          <input type="text" value="'.$deficiente.'" readonly="readonly"/>
         </span> 
          <span class="span3">
          <label>Estado civil: </label> 
          <input type="text" value="'.$estado_civil.'" readonly="readonly"/>
         </span>
          <span class="span3">
          <label>Grau de instrução: </label> 
          <input type="text" value="'.$grau_instrucao.'" readonly="readonly"/>
         </span>
    </div>  
    
    <br>
     <h6 align="left" style="padding-left:15px;">Endereço, E-mail e Telefones</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 
    
    <div class="formRow">
        <span class="span6">
          <label>Logradouro: </label> 
          <input type="text" value="'.$funcionarios['logradouro'].'" readonly="readonly"/>
         </span> 
        <span class="span2">
          <label>Nº: </label> 
          <input type="text" value="'.$funcionarios['numero'].'" readonly="readonly"/>
       </span>
       <span class="span4">
          <label>Complemento: </label> 
          <input type="text" value="'.$funcionarios['complemento'].'" readonly="readonly"/>
       </span>      
    </div>
    
    <div class="formRow">
        <span class="span4">
          <label>Bairro: </label> 
          <input type="text" value="'.$funcionarios['bairro'].'" readonly="readonly"/>
       </span>
        <span class="span4">
          <label>Cidade: </label> 
          <input type="text" value="'.$funcionarios['cidade'].'" readonly="readonly"/>
         </span> 
        <span class="span2">
          <label>UF: </label> 
          <input type="text" value="'.$funcionarios['uf'].'" readonly="readonly"/>
       </span>
       <span class="span2">
          <label>CEP: </label> 
          <input type="text" value="'.$funcionarios['cep'].'" readonly="readonly"/>
       </span>
    </div> 
     
   <div class="formRow">
          <span class="span6">
          <label>E-mail 01: </label> 
          <input type="text" value="'.$funcionarios['email01'].'" readonly="readonly"/>
         </span>
         <span class="span6">
          <label>E-mail 02: </label> 
          <input type="text" value="'.$funcionarios['email02'].'" readonly="readonly"/>
         </span> 
    </div>  
    
    
    <div class="formRow">
        <span class="span4">
          <label>Tel 01: </label> 
          <input type="text" value="'.$funcionarios['tel01'].'" readonly="readonly"/>
         </span> 
        <span class="span4">
          <label>Tel 02: </label> 
          <input type="text" value="'.$funcionarios['tel02'].'" readonly="readonly"/>
       </span>
       <span class="span4">
          <label>Referência: </label> 
          <input type="text" value="'.$funcionarios['estado_civil'].'" readonly="readonly"/>
       </span>
    </div>
    
     <div class="formRow">
        <span class="span12">
          <label>Observação: </label> 
          <textarea rows="5" readonly="readonly">'.$funcionarios['observacao'].'</textarea>
         </span> 
    </div>
   
    <br />
    
    ';
        
        
        
    }elseif($solicitcacao['tp_solicitacao'] == "2"){
        
        $titulo = "Recálculo de impostos e contribuições";
        
        if($solicitcacao['recalculo_tp'] == 1){
            $recalculo_tp = "CONFINS";
        }elseif($solicitcacao['recalculo_tp'] == 2){
            $recalculo_tp = "CSLL";
        }elseif($solicitcacao['recalculo_tp'] == 3){
            $recalculo_tp = "DAS";
        }elseif($solicitcacao['recalculo_tp'] == 4){
            $recalculo_tp = "FGTS";
        }elseif($solicitcacao['recalculo_tp'] == 5){
            $recalculo_tp = "GPS";
        }elseif($solicitcacao['recalculo_tp'] == 6){
            $recalculo_tp = "IRPJ";
        }elseif($solicitcacao['recalculo_tp'] == 7){
            $recalculo_tp = "ISS";
        }elseif($solicitcacao['recalculo_tp'] == 8){
            $recalculo_tp = "PIS";
        }
        
        //Abas visiveis
        $abas = 1;
        
        $detalhes_solicitacao = ' <br>
    
         <div class="formRow">
            <span class="span12">
                <label>Imposto / Contribuição: </label> 
              <input type="text" value="'.$recalculo_tp.'" readonly="readonly"/>
            </span>      
        </div>';

        
        if(!empty($solicitcacao['recalculo_tp_gps'])){    
            
            if($solicitcacao['recalculo_tp_gps'] == 1){
                $recalculo_tp_gps = "GPS Trabalhador";
            }elseif($solicitcacao['recalculo_tp_gps'] == 2){
                $recalculo_tp_gps = "GPS Trabalhador autonômo";
            }elseif($solicitcacao['recalculo_tp_gps'] == 3){
                $recalculo_tp_gps = "GPS Trabalhador doméstica";
            }
            
            
            $detalhes_solicitacao .= '             
            <div class="formRow" style="display:none;">
              <span class="span12">
                <label>Tipo GPS: </label> 
                <input type="text" value="'.$solicitcacao['recalculo_tp_gps'].'" readonly="readonly"/>
              </span>      
            </div>';
            
        } 
        $detalhes_solicitacao .='
        <div class="formRow" >
              <span class="span6">
                <label>Data de competência: </label> 
              <input type="text" value="'.$solicitcacao['recalculo_dt_competencia'].'" readonly="readonly"/>
             </span>      

              <span class="span6">
                <label>Data de competência: </label>
              <input type="text" value="'.$solicitcacao['recalculo_dt_pgto'].'" readonly="readonly"/>
          </span>
        </div>
    
    <br><br>
    ';
        

        
    }elseif($solicitcacao['tp_solicitacao'] == "3"){    
        
        $titulo = "Rescisão contratual";
        
        if($solicitcacao['rescisao_modalidade'] == 1){
            $rescisao_modalidade = "Indenizado";
        }elseif($solicitcacao['rescisao_modalidade'] == 2){
            $rescisao_modalidade = "Trabalhado";
        }elseif($solicitcacao['rescisao_modalidade'] == 3){
            $rescisao_modalidade = "Quebra / Término de contrato";
        }
        
        if($solicitcacao['rescisao_solicitante'] == 1){
            $rescisao_solicitante = "Funcionário";
        }elseif($solicitcacao['rescisao_solicitante'] == 2){
            $rescisao_solicitante = "Empregador";
        }
        //Abas visiveis
        $abas = 1;
        
        

        $funcionarios = $db->fetch_assoc("SELECT *, 
        DATE_FORMAT(dt_exame_admissional, '%d/%m/%Y') as dt_exame_admissional, 
        DATE_FORMAT(dt_nasc, '%d/%m/%Y') as dt_nasc, 
        DATE_FORMAT(dt_admissao, '%d/%m/%Y') as dt_admissao, 
        DATE_FORMAT(dt_demissao, '%d/%m/%Y') as dt_demissao,
        DATE_FORMAT(carteira_dt_emissao, '%d/%m/%Y') as carteira_dt_emissao,
        DATE_FORMAT(rg_dt_emissao, '%d/%m/%Y') as carteira_dt_emissao,
        DATE_FORMAT(pis_dt_inscricao, '%d/%m/%Y') as pis_dt_inscricao
        FROM funcionarios WHERE id = ".$solicitcacao['rescisao_funcionario_id']);
        
        //1º emprego?
        if($funcionarios['primeiro_emprego_ano'] == 1){ $primeiro_emprego = 'SIM'; }else{ $primeiro_emprego = 'NÃO'; }
        
        //Desconto de transporte?
        if($funcionarios['desconto_transporte'] == 1){ $desconto_transporte = 'SIM'; }else{ $desconto_transporte = 'NÃO'; }
        
        //Adicional noturno?
        if($funcionarios['adicional_noturno'] == 1){ $adicional_noturno = 'SIM'; }else{ $adicional_noturno = 'NÃO'; }
        
        //insalubridade?
        if($funcionarios['insalubridade'] == 1){ $insalubridade = 'SIM'; }else{ $insalubridade = 'NÃO'; }
        
        //Sindicalizado?
        if($funcionarios['sindicalizado'] == 1){ $sindicalizado = 'SIM'; }else{ $sindicalizado = 'NÃO'; }
        
        //$optante_fgts?
        if($funcionarios['optante_fgts'] == 1){ $optante_fgts = 'SIM'; }else{ $optante_fgts = 'NÃO'; }
      
        //Tipo de salario
        if($funcionarios['tp_salario'] == 1){ $tp_salario = 'Mensal'; }                           
        elseif($funcionarios['tp_salario'] == 2){ $tp_salario = 'Quinzenal';  }                                
        elseif($funcionarios['tp_salario'] == 3){ $tp_salario = 'Semanal';  }                                
        elseif($funcionarios['tp_salario'] == 4){ $tp_salario = 'Diário';  }                                
        elseif($funcionarios['tp_salario'] == 5){ $tp_salario = 'Horário';  }                                
        elseif($funcionarios['tp_salario'] == 6){ $tp_salario = 'Tarefa';  }                                
        else{ $tp_salario = 'Outros';  }                                
        
        //Sexo
        if($funcionarios['sexo'] == 'M'){ $sexo = 'Masculino'; }else{ $sexo = 'Feminino'; }

        //Cor / Raça?
        if($funcionarios['raca'] == 1){ $raca = 'Branco'; }                           
        elseif($funcionarios['raca'] == 2){ $raca = 'Negro';  }                                
        elseif($funcionarios['raca'] == 3){ $raca = 'Amarelo';  }                                
        elseif($funcionarios['raca'] == 4){ $raca = 'Pardo';  }                                                            
        else{ $raca = 'Índio';  } 
        
        //Deficiente?
        if($funcionarios['deficiente'] == 1){ $deficiente = 'SIM'; }else{ $deficiente = 'NÃO'; }
        
        //Estado civil:
        if($funcionarios['estado_civil'] == 1){ $estado_civil = 'Solteiro(a)'; }                           
        elseif($funcionarios['estado_civil'] == 2){ $estado_civil = 'Casado(a)';  }                                
        elseif($funcionarios['estado_civil'] == 3){ $estado_civil = 'Divorciado(a)';  }                                
        else{ $estado_civil = 'Viúvo(a)';  }                                                            
        
        //Grau de instrução
        if($funcionarios['instrucao'] == 1){ $grau_instrucao = '1º Grau Incompleto(a)'; }                           
        elseif($funcionarios['instrucao'] == 2){ $grau_instrucao = '1º Grau Completo';  }                                
        elseif($funcionarios['instrucao'] == 3){ $grau_instrucao = '2º Grau Incompleto';  }  
        elseif($funcionarios['instrucao'] == 4){ $grau_instrucao = '2º Grau Completo';  }                                
        elseif($funcionarios['instrucao'] == 5){ $grau_instrucao = 'Superior Incompleto';  }  
        elseif($funcionarios['instrucao'] == 6){ $grau_instrucao = 'Superior Completo';  }                                
        elseif($funcionarios['instrucao'] == 7){ $grau_instrucao = 'Pós Graduado';  }  
        elseif($funcionarios['instrucao'] == 8){ $grau_instrucao = 'Mestrado';  }                                
        elseif($funcionarios['instrucao'] == 9){ $grau_instrucao = 'Doutorado';  }  
        else{ $grau_instrucao = 'Pós Doutorado';  }    
        
        //Função
        $funcao = $db->fetch_assoc("SELECT nome FROM func_funcoes WHERE id = ".$funcionarios['funcao_id']);       
        
        //=======================//
        
        $detalhes_solicitacao = '  <br>  
        
        <h6 align="left" style="padding-left:15px;">Informações da Rescisão</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 
    
    <div class="formRow">
        <span class="span4">
          <label>Modalidade de aviso: </label> 
          <input type="text" value="'.$rescisao_solicitante.'" readonly="readonly"/>
        </span>
         <span class="span4">
          <label>Modalidade de aviso: </label> 
          <input type="text" value="'.$rescisao_modalidade.'" readonly="readonly"/>
        </span>
        <span class="span4">
          <label>Data de demissão: </label> 
          <input type="text" value="'.$solicitcacao['rescisao_data'].'" readonly="readonly"/>
        </span>         
    </div>
    
    <br>
      <h6 align="left" style="padding-left:15px;">Informações do Gerais</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 
        
    <div class="formRow">
        <span class="span5">
          <label>Função: </label> 
          <input type="text" value="'.$funcao['nome'].'" readonly="readonly"/>
        </span>
        <span class="span4">
          <label>Data exam. admissional: </label> 
          <input type="text" value="'.$funcionarios['dt_exame_admissional'].'" readonly="readonly"/>
        </span>
        <span class="span3">
          <label>Data admissão: </label> 
          <input type="text" value="'.$funcionarios['dt_admissao'].'" readonly="readonly"/>
        </span>       
    </div>     
    
     <div class="formRow">
        <span class="span4">
          <label>1º emprego do ano? </label> 
          <input type="text" value="'.$primeiro_emprego.'" readonly="readonly"/>
        </span>
        <span class="span3">
          <label>Desconto vale transp.? </label> 
          <input type="text" value="'.$desconto_transporte.'" readonly="readonly"/>
        </span>
        <span class="span3">
          <label>Adicional noturno? </label> 
          <input type="text" value="'.$adicional_noturno.'" readonly="readonly"/>
        </span>   
        <span class="span2">
          <label>Insalubridade? </label> 
          <input type="text" value="'.$insalubridade.'" readonly="readonly"/>
        </span>
    </div>   
    
    <div class="formRow">        
        <span class="span3">
          <label>Sindicalizado? </label> 
          <input type="text" value="'.$sindicalizado.'" readonly="readonly"/>
        </span> 
        <span class="span4">
          <label>Sindicato: </label> 
          <input type="text" value="'.$funcionarios['sindicato'].'" readonly="readonly"/>
        </span>
         <span class="span2">
          <label>Optante FGTS: </label> 
          <input type="text" value="'.$optante_fgts.'" readonly="readonly"/>
        </span>
         <span class="span3">
          <label>Código banco: </label> 
          <input type="text" value="'.$funcionarios['cod_banco_fgts'].'" readonly="readonly"/>
        </span>
    </div> 
    
    <div class="formRow"> 
        <span class="span3">
          <label>Tipo de salário: </label> 
          <input type="text" value="'.$tp_salario.'" readonly="readonly"/>
        </span>
        <span class="span5">
          <label>Salário atual: </label> 
          <input type="text" value="'.number_format($funcionarios['salario'], 2, ',', '.').'" readonly="readonly"/>
        </span>
    </div>
      
    <br>
     <h6 align="left" style="padding-left:15px;">Informações do Funcionário</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 

      <div class="formRow">
          <span class="span5">
          <label>Nome do funcionário: </label> 
          <input type="text" value="'.$funcionarios['nome'].'" readonly="readonly"/>
         </span>
         <span class="span2">
          <label>Data nasc.: </label> 
          <input type="text" value="'.$funcionarios['dt_nasc'].'" readonly="readonly"/>
         </span>
         <span class="span2">
          <label>UF nasc.: </label> 
          <input type="text" value="'.$funcionarios['uf_nasc'].'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>UF nasc.: </label> 
          <input type="text" value="'.$funcionarios['cidade_nasc'].'" readonly="readonly"/>
         </span>
         
    </div>
    
    <div class="formRow">
        <span class="span3">
          <label>CPF: </label> 
          <input type="text" value="'.$funcionarios['cpf'].'" readonly="readonly"/>
         </span> 
        <span class="span3">
          <label>RG: </label> 
          <input type="text" value="'.$funcionarios['rg'].'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Orgão emissor RG: </label> 
          <input type="text" value="'.$funcionarios['rg_emissor'].'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Data de emissão RG: </label> 
          <input type="text" value="'.$funcionarios['rg_dt_emissao'].'" readonly="readonly"/>
         </span>                
    </div>
    
     <div class="formRow">
          <span class="span3">
          <label>PIS: </label> 
          <input type="text" value="'.$sexo.'" readonly="readonly"/>
         </span>
         <span class="span3">
          <label>Data insc. PIS: </label> 
          <input type="text" value="'.$funcionarios['pis_dt_inscricao'].'" readonly="readonly"/>
         </span> 
         <span class="span3">
          <label>Cart. prof.: </label> 
          <input type="text" value="'.$funcionarios['carteira'].'" readonly="readonly"/>
         </span> 
          <span class="span3">
          <label>Data emissão carteira: </label> 
          <input type="text" value="'.$funcionarios['carteira_dt_emissao'].'" readonly="readonly"/>
         </span>
    </div>  
        
        <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div>
    
   <div class="formRow">
          <span class="span6">
          <label>Nome do Pai: </label> 
          <input type="text" value="'.$funcionarios['nome_pai'].'" readonly="readonly"/>
         </span>
         <span class="span6">
          <label>Nome da Mãe: </label> 
          <input type="text" value="'.$funcionarios['nome_mae'].'" readonly="readonly"/>
         </span>         
    </div>
    
     <div class="formRow">
          <span class="span2">
          <label>Sexo: </label> 
          <input type="text" value="'.$sexo.'" readonly="readonly"/>
         </span>
         <span class="span2">
          <label>Cor/Raça: </label> 
          <input type="text" value="'.$raca.'" readonly="readonly"/>
         </span> 
         <span class="span2">
          <label>Deficiente? </label> 
          <input type="text" value="'.$deficiente.'" readonly="readonly"/>
         </span> 
          <span class="span3">
          <label>Estado civil: </label> 
          <input type="text" value="'.$estado_civil.'" readonly="readonly"/>
         </span>
          <span class="span3">
          <label>Grau de instrução: </label> 
          <input type="text" value="'.$grau_instrucao.'" readonly="readonly"/>
         </span>
    </div>  
    
    <br>
     <h6 align="left" style="padding-left:15px;">Endereço, E-mail e Telefones</h6>
       <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div> 
    
    <div class="formRow">
        <span class="span6">
          <label>Logradouro: </label> 
          <input type="text" value="'.$funcionarios['logradouro'].'" readonly="readonly"/>
         </span> 
        <span class="span2">
          <label>Nº: </label> 
          <input type="text" value="'.$funcionarios['numero'].'" readonly="readonly"/>
       </span>
       <span class="span4">
          <label>Complemento: </label> 
          <input type="text" value="'.$funcionarios['complemento'].'" readonly="readonly"/>
       </span>      
    </div>
    
    <div class="formRow">
        <span class="span4">
          <label>Bairro: </label> 
          <input type="text" value="'.$funcionarios['bairro'].'" readonly="readonly"/>
       </span>
        <span class="span4">
          <label>Cidade: </label> 
          <input type="text" value="'.$funcionarios['cidade'].'" readonly="readonly"/>
         </span> 
        <span class="span2">
          <label>UF: </label> 
          <input type="text" value="'.$funcionarios['uf'].'" readonly="readonly"/>
       </span>
       <span class="span2">
          <label>CEP: </label> 
          <input type="text" value="'.$funcionarios['cep'].'" readonly="readonly"/>
       </span>
    </div> 
     
   <div class="formRow">
          <span class="span6">
          <label>E-mail 01: </label> 
          <input type="text" value="'.$funcionarios['email01'].'" readonly="readonly"/>
         </span>
         <span class="span6">
          <label>E-mail 02: </label> 
          <input type="text" value="'.$funcionarios['email02'].'" readonly="readonly"/>
         </span> 
    </div>  
    
    
    <div class="formRow">
        <span class="span4">
          <label>Tel 01: </label> 
          <input type="text" value="'.$funcionarios['tel01'].'" readonly="readonly"/>
         </span> 
        <span class="span4">
          <label>Tel 02: </label> 
          <input type="text" value="'.$funcionarios['tel02'].'" readonly="readonly"/>
       </span>
       <span class="span4">
          <label>Referência: </label> 
          <input type="text" value="'.$funcionarios['estado_civil'].'" readonly="readonly"/>
       </span>
    </div>
    
     <div class="formRow">
        <span class="span12">
          <label>Observação: </label> 
          <textarea rows="5" readonly="readonly">'.$funcionarios['observacao'].'</textarea>
         </span> 
    </div>
 
    
    <br><br>
    
    ';
    }else{         
        $chat_categoria = $db_wf-> fetch_assoc("SELECT assunto FROM chat_categorias WHERE id = ".$params['chat_categoria_id']);
        
        $titulo = $chat_categoria['assunto'];
        
        //Abas escondidas
        $abas = 0;
    }
    
    
   $msg_conversa = self::visualizarMensagem($db_wf, $params['chat_categoria_id'], '0');
            
        $retorno = array('msg'=>$msg_conversa['msg'],'detalhes_solicitacao'=>$detalhes_solicitacao, 'titulo'=>$titulo, 'abas'=>$abas);
        
        return $retorno;
        
        $db_cliente->close();
    }
  

/*
===========================================================================================
MENSAGEM VISUALIZAR
===========================================================================================
*/

function visualizarMensagem($db_wf, $params, $atualizar){
    
    //if($tipo = '1'){ $chat_categoria_id = $abrir_mensagem; }elseif($tipo = '2'){ $chat_categoria_id = $params['chat_categoria_id'];  }
    
    session_start();
    $cliente_id = $_SESSION['cliente_id'];
    
    if($atualizar != '1'){
        
        $mensagem = $db_wf->fetch_all_array("SELECT id, mensagem, date_format(dt_cadastro, '%d/%m/%y') data, date_format(dt_cadastro, '%H:%i') hora, remetente_id, dt_visualizacao FROM chat_mensagens WHERE chat_categoria_id = ".$params['chat_categoria_id']);
   
    
          foreach($mensagem as $msg){            
            
              if($msg['remetente_id'] == $cliente_id){  
                
                $msg_conversa .= '<li class="chat_li">
                  <div class="chat_right">
                    <span class="chat_hora_left"> '.$msg['hora'].' - '.$msg['data'].' </span> <br /> 
                    <p> '.nl2br($msg['mensagem']).' </p>
                </div>
              </li>';
                
            }else{              
               
                
                $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");                
                $nome = $db_w2b->fetch_assoc("SELECT nome FROM clientes WHERE id =".$msg['remetente_id']);
               
                
                $msg_conversa .= '
            <li class="chat_li">
                      <div class="chat_left">
                        <b> '.$nome['nome'].' </b> <span class="chat_hora_right"> &nbsp; '.$msg['data'].' - '.$msg['hora'].'</span> <br />
                        <p> '.nl2br($msg['mensagem']).' </p>
                      </div>
                  </li>';
                
                
                $atualizar = array('dt_visualizacao'=> date('Y-m-d H:i:s'), 'visualizada'=> 1);
                
                $db_wf->query_update("chat_mensagens", $atualizar,"id =".$msg['id']);
                
                
                $db_w2b->close();
            }
        }
          //Verifica se existe mensagem
          if($mensagem == true){ $novaMensagem = 1; }else{ $novaMensagem = 0; }
          
    }else{
    
    
        $mensagem = $db_wf->fetch_all_array("SELECT id, mensagem, date_format(dt_cadastro, '%d/%m/%y') data, date_format(dt_cadastro, '%H:%i') hora, remetente_id, dt_visualizacao FROM chat_mensagens WHERE chat_categoria_id = ".$params['chat_categoria_id'].' AND visualizada = 0 AND remetente_id !='.$cliente_id);
        
        
        foreach($mensagem as $msg){
                
                $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");                
                $nome = $db_w2b->fetch_assoc("SELECT nome FROM clientes WHERE id =".$msg['remetente_id']);
                // 
                
                $msg_conversa .= '
            <li class="chat_li">
                      <div class="chat_left">
                        <b> '.$nome['nome'].' </b> <span class="chat_hora_right"> &nbsp; '.$msg['data'].' - '.$msg['hora'].'</span> <br />
                        <p> '.nl2br($msg['mensagem']).' </p>
                      </div>
                  </li>';
                
                
                $atualizar = array('dt_visualizacao'=> date('Y-m-d H:i:s'), 'visualizada'=> 1);
                
                $db_wf->query_update("chat_mensagens", $atualizar,"id =".$msg['id']); 
                
                
                $db_w2b->close();
        }
        
        //Verifica se existe mensagem
        if($mensagem == true){ $novaMensagem = 1; }else{ $novaMensagem = 0; }
    }
        
        $retorno = array('msg'=>$msg_conversa, 'novaMensagem'=>$novaMensagem);
        return $retorno;
        

}
/*
===========================================================================================
MENSAGEM ENVIAR 
===========================================================================================
*/

function enviarMensagem($db_wf, $params){
    
    session_start();
    $cliente_id = $_SESSION['cliente_id'];
    
        $registro_msg['chat_categoria_id'] = $params['chat_categoria_id'];
        $registro_msg['mensagem'] = $params['mensagem'];
        $registro_msg['remetente_id'] = $cliente_id; /* trocar no módulo contador para 'contador_id' */
        $registro_msg['visualizada'] = 0;
        $registro_msg['dt_cadastro'] = date('Y-m-d H:i:s');

        $db_wf->query_insert("chat_mensagens", $registro_msg);  
        
        $msg_dt = strtotime($registro_msg['dt_cadastro']);
           
                $msg_conversa .= '<li class="chat_li">
                  <div class="chat_right">
                    <span class="chat_hora_left"> '.date('H:i - d/m/y', $msg_dt).' </span> <br /> 
                    <p> '. nl2br($registro_msg['mensagem']).' </p>
                </div>
              </li>';
        
       $registro_msg_cat['dt_novas_mensagens'] = $registro_msg['dt_cadastro'];        
       $db_wf->query_update("chat_categorias", $registro_msg_cat,"id =".$registro_msg['chat_categoria_id'] );
                $retorno = array('msg'=>$msg_conversa);
        
        return $retorno;
    
    }



}






