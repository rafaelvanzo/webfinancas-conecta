<?php
class Informativo{
	
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

function DataTableAjax($db_wf,$params){
    
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

        $query_dataTable = "SELECT * FROM clientes_informativo WHERE contador_id = ".$cliente_id;

    }else{
        //Query princial
        $query_dataTable = "SELECT * FROM clientes_informativo WHERE contador_id = ".$cliente_id." AND titulo like '%".$sSearch."%'";
    }   
    
    //total de registros
    $iTotalDisplayRecords = $db_wf->numRows('select id from ('.$query_dataTable.') as lancamentos WHERE contador_id = '.$cliente_id);
    
    //Limita a consulta no db de acordo com a configuração da tabela
    $query_order .= ' order by dt_inicio DESC, id limit '.$iDisplayStart.','.$iDisplayLength;
    
    
    $array_dados = $db_wf->fetch_all_array($query_dataTable.$search.$query_order);

    foreach($array_dados as $dados){	
  
    // ============ data ============
    $dt_inicio = explode("-", $dados['dt_inicio']);
    $dia = substr($dt_inicio[2], 0, 2);
    $m = $dt_inicio[1];
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
    $ano = substr($dt_inicio[0], -2);
   
    if($dados['dt_final'] != '0000-00-00'){
    $dt_final = explode("-", $dados['dt_final']);
    $dt_final = $dt_final[2].'/'.$dt_final[1].'/'.$dt_final[0];
    }else{ $dt_final = 'Tempo indeterminado'; }
    
    if($dados['situacao'] == 1){
        $situacao = '<div class="tbWFvalue blue">ATIVO</div>';
    }else{
        $situacao = '<div class="tbWFvalue red">INATIVO</div>';
    }
    
    $registros_tabela = '<span>
                                    <div class="uDate tbWF tipS" align="center" original-title="Data inicial" > <span class="uDay ">'.$dia.'</span>'.$mes.'/'.$ano.'<br></div>
                                    
                                    <span class="lDespesa tbWF">
					                   <a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS"> <strong class="blue">'.$dados['titulo'].' </strong></a>                                     
						                <span class="tipN">Data Final: <b>'.$dt_final.'</b></span>
				                    </span>                                    
                                    <div class="tbWFoption" style="z-index:999;">					
			    	                    <a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS" onClick="excluirDialog('.$dados['id'].');"><img src="images/icons/light/close.png" width="10"></a>
				                        <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="visualizarInfo('.$dados['id'].');"><img src="images/icons/light/pencil.png" width="10"></a>
				                    </div>   
                                    
                                    '.$situacao.'
                             
                             </span>'  ;
    
         //insere resultado dentro do arra $aaData
         array_push($aaData,array('msg'=>$registros_tabela));
         
    }
    
    $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
    
    return json_encode($retorno);
}


/*
===========================================================================================
ADD INFORMATIVO
===========================================================================================
*/

function addInfo($db_wf, $params){
    
    session_start();
    $cliente_id = $_SESSION['cliente_id'];
    
    $registro['contador_id'] = $cliente_id;
    $registro['titulo'] = $params['titulo'];
    $registro['descricao'] = $params['descricao'];
    
    $dt_inicio = explode("/", $params['dt_inicio']);
    $dia = $dt_inicio[0];
    $mes = $dt_inicio[1];
    $ano = $dt_inicio[2];
    $dt_inicio = $ano.'-'.$mes.'-'.$dia;
    
    $registro['dt_inicio'] = $dt_inicio;
    
    $dt_final = explode("/", $params['dt_final']);
    $dia2 = $dt_final[0];
    $mes2 = $dt_final[1];
    $ano2 = $dt_final[2];
    $dt_final = $ano2.'-'.$mes2.'-'.$dia2;
        
    $registro['dt_final'] = $dt_final;
    $registro['situacao'] = $params['situacao'];
    
    $db_wf->query_insert('clientes_informativo', $registro);
    
    }



/*
===========================================================================================
VISUALIZAR INFORMATIVO
===========================================================================================
*/

function visualizarInfo($db_wf, $params){

    $registro = $db_wf->fetch_assoc('SELECT *, DATE_FORMAT(dt_inicio, "%d/%m/%Y") as dt_inicio, DATE_FORMAT(dt_final, "%d/%m/%Y") as dt_final FROM clientes_informativo WHERE id ='.$params['id']);
   
   $retorno = array("titulo"=>$registro['titulo'], "dt_inicio"=>$registro['dt_inicio'], "dt_final"=>$registro['dt_final'], "situacao"=>$registro['situacao'], "descricao"=>nl2br($registro['descricao']));
   
   return $retorno;
    
    }


/*
===========================================================================================
EDITAR INFORMATIVO
===========================================================================================
 */

function editarInfo($db_wf, $params){
    
    $registro['titulo'] = $params['titulo'];
    $registro['situacao'] = $params['situacao'];
    $registro['descricao'] = $params['descricao'];
    
    $dt_inicio = explode("/",$params['dt_inicio']); 
    $registro['dt_inicio'] = $dt_inicio[2].'-'.$dt_inicio[1].'-'.$dt_inicio[0];
    
    $dt_final = explode("/",$params['dt_final']); 
    $registro['dt_final'] = $dt_final[2].'-'.$dt_final[1].'-'.$dt_final[0];
    

    $registro = $db_wf->query_update('clientes_informativo', $registro ,' id ='.$params['id']);
    
}

/*
===========================================================================================
EXCLUIR INFORMATIVO
===========================================================================================
 */

function excluirInfo($db_wf, $params){   

    $db_wf->query("delete from clientes_informativo where id = ".$params['id']);
    
}


/*
===========================================================================================
FIM DAS CLASSES
===========================================================================================
*/
}





