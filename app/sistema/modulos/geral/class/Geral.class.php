<?php

class geral{

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($db="",$array_dados=""){
		if($array_dados!=""){
			foreach($this->lancamento_dados as $chave => $valor){
				$this->lancamento_dados[$chave] = $array_dados[$chave];
			}
			$this->lancamento_dados[valor] = $db->valorToDouble($array_dados[valor]);
			$this->lancamento_dados[dt_emissao] = $db->data_to_sql($array_dados[dt_emissao]);
			$this->lancamento_dados[dt_vencimento] = $db->data_to_sql($array_dados[dt_vencimento]);
			$this->lancamento_dados[dt_compensacao] = $db->data_to_sql($array_dados[dt_compensacao]);
		}
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
VISUALIZAR INFORMATIVO
===========================================================================================
*/
function visualizarInfo($db_wf, $params){

    $registro = $db_wf->fetch_assoc('SELECT titulo, descricao, DATE_FORMAT(dt_inicio, "%d/%m/%Y") as dt_inicio FROM clientes_informativo WHERE id ='.$params['id']);
    
    $retorno = array("titulo"=>$registro['titulo'], "dt_inicio"=>$registro['dt_inicio'], "descricao"=>nl2br($registro['descricao']));
    
    return $retorno;
    
    }

}


?>