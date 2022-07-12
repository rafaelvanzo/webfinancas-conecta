<?php
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/servicos/mensagem/MensagemHelper.php";

// Your email address
//$to = 'contato@webfinancas.com';
$to = 'fabio@web2business.com.br';

$subject = $_POST['subject'];

$nome = $_POST['name'];
$email = $_POST['email'];
$mensagem = $_POST['message'];
	
$data = date('d/m/Y H:i:s');
	
//Endereço dos arquivos no servidor
$enderecoArquivos = "https://www.webfinancas.com/site/modulos/email/";
	
//Mensagem Email
$message = '	

<style>
/* ========================== CSS ============================ */
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


.img { width: 400px; }

</style>
	


<div id="palco">
	
<table width="700" height="100%" border="0" align="center">
<tr>
		<td width="50" align="left" ><img src="'.$enderecoArquivos.'logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>
		<td width="50" align="right" > <h5>'.$data.'</h5> </td>      
</tr>
<tr>    
<td align="left" colspan="2" valign="top">
			  
    <h3>Nome: '.$nome.' </h3>
		<p><b>E-mail:</b> '.$email.'</p>
		<p><b>Mensagem:</b> <br> '.$mensagem.'</p>
			
	</td>
</tr>
</table>	
';

$message = "Nome: $nome <br><br> Email: $email <br><br> Mensagem: $mensagem";

$mensagemHelper = new MensagemHelper();
$result = $mensagemHelper::EnviarEmail($to,"Web Finanças - Contato",$message);

if($result["status"])
	$arrResult = array ('response'=>'success');
else
    $arrResult = array ('response'=>'error');

echo json_encode($arrResult);
?>