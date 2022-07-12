<?php
require("php/Database.class.php");
$host = "mysql.web2business.com.br";
$usuario = "web2business";
$senha = "W2BSISTEMAS";
$db_usuario = "web2business";
$db = new Database($host,$usuario,$senha,$db_usuario);

//$emails = $db->fetch_all_array('select email from clientes');


$email_content = '
	<!DOCTYPE HTML>
	<html>
	<head>
	<meta charset="utf-8">
	<title>Untitled Document</title>
	</head>
	<body style="margin: 40px 0; padding:0; color: #333; font-family: \'Open Sans\', sans-serif; font-size: 0.9em; line-height: 1.8em; text-align:center; background-color:#eeeeee;">

		<div style="width: 100%; height: 100%;">
		
			<div style="width:700px; background-color: #fff; border: 1px solid #ccc; text-align:left; margin:0 auto;">

				<div style="width: 100%; max-width: 640px; padding: 10px 30px;">

					<div style="display:inline;">
						<img src="http://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png" align="middle" class="logo" />
					</div>

					<div style="width: 50%; display:inline-block; text-align:right">
						<a href="http://www.webfinancas.com">www.webfinancas.com</a>
					</div>
					
					<p style="border-bottom: 1px dotted #999;"></p>

					Caro(a) Cliente,
					
					<br><br>
					
					Informamos que foram realizadas as seguintes atualizações no Web Finanças:
					
					<ul>
						<li>Unificação de lançamentos compensados e programados na página Lançamentos;</li>
						<li>Emissão de Recibo;</li>
						<li>Possibilidade de desfazer compensação de lançamento;</li>
						<li>Anexação de arquivos;</li>
						<li>Unificação de recebimentos e pagamentos recorrentes na página Lançamentos Recorrentes dentro do menu Recorrência;</li>
						<li>"Plano de Contas" agora é "Categorias" no menu lateral esquerdo;</li>
						<li>"Centro de responsabilidade" agora é "Centro de Custo" no menu lateral esquerdo;</li>
						<li>Novo relatório de Fluxo de Caixa;</li>
						<li>Novo relatório de DRE;</li>
						<li>Conciliação de lançamentos em Contas Financeiras.</li>
					</ul>

					<br>
					
					Atenciosamente,
					<br>
					Equipe Web 2 Business

				</div>
	
				<div style="width: 100%; max-width: 640px; padding: 10px 30px; background-color:#f4f4f4;">
					<p style="font-size:0.9em;">
						* Em caso de dúvidas, entre em contato pelo email <a href="mailto:contato@webfinancas.com">contato@webfinancas.com</a>.
						<br>
						** Esta é uma mensagem gerada eletronicamente e não deve ser respondida.
					</p>
				</div>

			</div>
		
		</div>
		
	</body>
	</html>
';

/*
	$enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
	
	$email_content = '
	<style>
	body { 
	padding:0 !important; 
	margin:0 !important; 
	display:block !important; 
	background:#f8f8f8;  
	font-family: Arial, Helvetica, sans-serif;
	font-size:12px;
	color:#666666;
	}
	a { text-decoration: none; }
	
	#palco {
	border: 0px;
	min-width: 250px;
	max-width: 700px;
	min-height: 200px;
	height: 100%;
	margin-left:auto;
	margin-right:auto;
	border-radius: 20px;
	}
	
	#rodape {
	text-align: left;
	min-width: 250px;
	max-width: 700px;
	height: 50px;
	margin-left:auto;
	margin-right:auto;
	font-size:10px;
	}
	
	.img { width: 400px; }

.btn{
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
	padding: 10px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;	
}
.btn-primary,
.active > a {
	text-decoration:none; 
  border-color: #0088cc;
  color: #ffffff;
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
  background-color: #0088cc;
  border-color: #0044cc #0044cc #002a80;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
}
.btn-primary:hover,
.active > a:hover {
  border-color: #0099e6;
  background-color: #0099e6;
}
.btn-primary:active,
.active > a:active,
.btn-primary:focus,
.active > a:focus {
  border-color: #0077b3;
  background-color: #0077b3;
}
	
	</style>
		
	
	
		<div id="palco">
		
	<table width="700" height="100%" border="0" align="center">
			<tr>
			
				<td width="50%" align="center"><img src="http://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>      
				<td width="50%" align="center">
	
									<a href="http://www.facebook.com/" target="_blank" title="Facebook"><img src="'.$enderecoArquivos.'iconFacebook.png"></a></li>
									<a href="http://www.twitter.com/" target="_blank" title="Twitter"><img src="'.$enderecoArquivos.'iconGooglePlus.png"></a></li>
									<a href="http://www.linkedin.com/" target="_blank" title="Linkedin"><img src="'.$enderecoArquivos.'iconLinkedin.png"></a></li>

				</td>
				
			</tr>
			<tr>
			
			<td width="50%" align="center" valign="top">
			
				<br>
				
				<h3> Novas atualizações! </h3>
				<p>
					Caro(a) Cliente,
					<br>
					Informamos que foram realizadas as seguintes atualizações no Web Finanças:

					<ul>
						<li>Unificação de lançamentos compensados e programados na página Lançamentos;</li>
						<li>Emissão de Recibo;</li>
						<li>Possibilidade de desfazer compensação de lançamento;</li>
						<li>Anexação de arquivos;</li>
						<li>Unificação de recebimentos e pagamentos recorrentes na página Lançamentos Recorrentes dentro do menu Recorrência;</li>
						<li>"Plano de Contas" agora é "Categorias" no menu lateral esquerdo;</li>
						<li>"Centro de responsabilidade" agora é "Centro de Custo" no menu lateral esquerdo;</li>
						<li>Relatório de Fluxo de Caixa;</li>
						<li>Relatório de DRE.</li>
					</ul>

				</p>

			</td>    
			<td width="50%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'macbook.png" align="right" class="img" width="400" />
			 </td>
		</tr>
		<tr>			
				<td colspan="2" align="center" valign="top"> 
					<span style="font-size:11px; color:999;">	
					** Leia atentamente os <a href="https://www.webfinancas.com/termosUso" target="blank">termos de uso</a>. ** <br/>
					</span>
				</td>			 
		</tr>
	</table>	
		';
*/
require("php/swiftMailer/lib/swift_required.php");
/*=========== INICIALIZA O OBJETO QUE ENVIA O EMAIL =======================================*/
$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 587); //$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 25);
$transport->setUsername('fabio@web2business.com.br');
$transport->setPassword('fa537985');

$message = Swift_Message::newInstance();
$message->setSubject("Novas Atualizações");
$message->setFrom(array("fabio@web2business.com.br" => "Web Finanças"));
$message->setReturnPath("fabio@web2business.com.br");

$mailer = Swift_Mailer::newInstance($transport);
/*==============================================================================================*/

$message->setBody($email_content, 'text/html');

//foreach($emails as $email){
	//$message->setTo(array($email['email']));//$message->setTo(array($_POST["email"])); //não precisa limpar o destinatario a cada envio, esta função sobre-escreve o destinatario anterior
	//$mailer->send($message);
//}
echo $email_content;
?>
