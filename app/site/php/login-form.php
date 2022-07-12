<?php
// ====================== db ======================= 
require('Database.class.php');
require ('php-mailer/class.phpmailer.php');

$host = "mysql.webfinancas.com";
$usuario = "webfinancas";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinancas";
$db = new Database($host,$usuario,$senha,$db_usuario);

// =================== Cabeçalho do arquivo ============================== 

//session_cache_limiter('nocache');
//header('Expires: ' . gmdate('r', 0));
//header('Content-type: application/json');


// ================ Requisita função ================================= 


$funcao = $_POST['funcao'];

switch($funcao){

	case 'login':
		login($db);
	break;

}

// ================== Funções =============================== 

function login($db){
/*
	//Email
	$email = $_POST['email'];

	//$subject = $_POST['subject'];
	
	$usuario_id = $db->fetch_assoc('select id from usuarios where email = "'.$email.'"');
	
	if(!$usuario_id['id']){
	
		$cliente_id = $db_w2b->fetch_assoc('select id from clientes where email = "'.$email.'"');

		if(!$cliente_id['id']){
			//insere cliente no banco de dados da w2b
			$array_cadastro['nome'] = $_POST['nome'];
			$array_cadastro['email'] = $email;
			$array_cadastro['telefone'] = $_POST['tel'];
			$array_cadastro['dt_cadastro'] = date('Y-m-d H:i:s');
			$cliente_id = $db_w2b->query_insert('clientes',$array_cadastro);
			$cliente_id = $db_w2b->fetch_assoc('select id from clientes where email = "'.$email.'"');
		}
		
		$cliente_id = $cliente_id['id'];
		
		//busca um banco de dados no web finanças para alocar os registros do cliente
		$cliente_db = $db->fetch_assoc('select min(id) id from clientes_db where situacao = 0');
		$db->query('update clientes_db set cliente_id = '.$cliente_id.', situacao = 1 where id = '.$cliente_db['id']);

		//insere usuário no banco de dados do web finanças
		$array_usuario['cliente_id'] = $cliente_id;
		$array_usuario['cliente_db_id'] = $cliente_db['id'];
		$array_usuario['email'] = $_POST['email'];
		$array_usuario['senha'] = md5($_POST['senha']);
		$array_usuario['situacao'] = 0;
		$array_usuario['dt_cadastro'] = date('Y-m-d H:i:s');
		$usuario_id = $db->query_insert('usuarios',$array_usuario);
		
		//registra acesso trial do cliente
		//$db->query_insert('clientes_trial',$array_usuario);
		
	// ========================== CSS ============================
	
	$enderecoArquivos = 'https://www.webfinancas.com/site/modulos/email/';
	
	$message = '
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
	
	</style>
		
	
	
		<div id="palco">
		
	<table width="700" height="100%" border="0" align="center">
			<tr>
			
				<td width="50%" align="left"><img src="'.$enderecoArquivos.'logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>      
				<td width="50%" align="right">
						<div class="social-icons">
	
									<a href="http://www.facebook.com/" target="_blank" title="Facebook"><img src="'.$enderecoArquivos.'facebook.png"></a></li>
									<a href="http://www.twitter.com/" target="_blank" title="Twitter"><img src="'.$enderecoArquivos.'googlePlus.png"></a></li>
									<a href="http://www.linkedin.com/" target="_blank" title="Linkedin"><img src="'.$enderecoArquivos.'linkedin.png"></a></li>
	
							</div>
				</td>
				
			</tr>
			<tr>
			
			<td width="50%" rowspan="2" align="center" valign="top">
			
				<br>
				
			 <h2> Confirmação de cadastro! </h2>
				<p>Para começar a utilizar os benefícios do Web Finanças clique no link abaixo para confirmar o seu cadastro. </p> <br>
				<a href="https://www.webfinancas.com/site/php/cadastro-ativar.php?id='.$usuario_id.'" target="_blank" ><img src="'.$enderecoArquivos.'bt_confirmar.png" height="39"></a>
	
			
			</td>    
			<td width="50%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'macbook.png" align="right" class="img" width="400" />	
			 </td>
			 
		</tr>
	</table>	
		';
		
		// ================================================================================
		
		$mail = new PHPMailer;
	
		$mail->IsSMTP();                                    // Set mailer to use SMTP
		
		// Optional Settings
		$mail->Host = 'smtp.webfinancas.com'; //'carteiroexpress.com';					  // Specify main and backup server
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'contato@webfinancas.com'; //no-reply';		  							// SMTP username
		$mail->Password = 'W2BSISTEMAS'; //'7061c986e';                  // SMTP password
		//$mail->SMTPSecure = 'tls';                    // Enable encryption, 'ssl' also accepted
	
		$mail->From = 'contato@webfinancas.com'; //$email;
		$mail->FromName = 'Web Finanças - Confirmação de cadastro'; 	//$_POST['name'];
		$mail->AddAddress($email);								  // Add a recipient
		//$mail->AddReplyTo($email, $name);
	
		$mail->IsHTML(true);                                  // Set email format to HTML
		
		$mail->CharSet = 'UTF-8';

		$subject = 'Cadastro - Web Finanças';

		$mail->Subject = $subject;
		$mail->Body    = $message;
	
		if(!$mail->Send()) {
			 $arrResult = array ('situacao'=>0);
		}
	
		$arrResult = array ('situacao'=>1);
		
		echo json_encode($arrResult);
		
	}else{
		$arrResult = array ('situacao'=>2);
		echo json_encode($arrResult);
	}
*/
	$arrResult = array ('situacao'=>1);
	echo json_encode($arrResult);
}

?>









