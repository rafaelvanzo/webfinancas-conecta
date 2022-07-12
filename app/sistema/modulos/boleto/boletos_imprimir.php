<?php
session_start();

require("../../php/Database.class.php");
require("../../php/MPDF/mpdf.php");
require("Boleto.class.php");

$boleto_valido = true;

if(isset($_GET["k"])){
	
	$chave = $_GET["k"];
	$chave = explode('-',$chave);

	if(count($chave)==5){
	
		//chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
		$cliente_id_c = $chave[0];
		$convenio = $chave[1];
		$lancamento_id = $chave[2];
		$boleto_id = $chave[3];
		$sequencial = $chave[4];
		
		$db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
		
		$cliente = $db_wf->fetch_assoc("select db, db_senha from clientes_db where cliente_id = ".$cliente_id_c);
		if(empty($cliente)){
			$boleto_valido = false;
		}else{

			$db_cliente = new Database("mysql.webfinancas.com",$cliente["db"],$cliente["db_senha"],$cliente["db"]);

			$query = "
				select l.id, bnc.codigo
				from lancamentos l
				left join boletos b on l.id = b.lancamento_id
				left join contas c on l.conta_id = c.id
				left join bancos bnc on c.banco_id = bnc.id
				where l.id = ".$lancamento_id."
					and b.id = ".$boleto_id."
					and b.sequencial = ".$sequencial;
			$fatura = $db_cliente->fetch_assoc($query);
			$cod_banco = $fatura['codigo'];
			
			if(empty($fatura))
				$boleto_valido = false;
			
		}
		
	}else{
		$boleto_valido = false;
	}

}else{
	$boleto_valido = false;
}

if($boleto_valido){

	//if($_GET["vbe"]==1){
		$dt_visualizado = date("Y-m-d H:i:s");
		$db_cliente->query("update boletos set visualizado = 1, dt_visualizado = '".$dt_visualizado."' where id = ".$boleto_id);
	//}

	switch ($cod_banco){
		case '001'; require_once("Boleto.Bb.class.php"); $boleto = new BoletoBb($db_cliente,$lancamento_id,$boleto_id,$cliente_id_c); break;
		case '104'; require_once("Boleto.Cef.class.php"); $boleto = new BoletoCef($db_cliente,$lancamento_id,$boleto_id,$cliente_id_c); break;
		case '756'; require_once("Boleto.Sicoob.class.php"); $boleto = new BoletoSicoob($db_cliente,$lancamento_id,$boleto_id,$cliente_id_c); break;
		case '033'; require_once("Boleto.Santander.class.php"); $boleto = new BoletoSantander($db_cliente,$lancamento_id,$boleto_id,$cliente_id_c); break;
		case '021'; require_once("Boleto.Banestes.class.php"); $boleto = new BoletoBanestes($db_cliente,$lancamento_id,$boleto_id,$cliente_id_c); break;
        case '237'; require_once("Boleto.Bradesco.class.php"); $boleto = new BoletoBradesco($db_cliente,$lancamento_id,$boleto_id,$cliente_id_c); break;
	}

	$boleto->boletoMontar();
	//$boleto->nossoNumeroAtualizar($db_cliente);
	$boleto->boletoImprimir();
	
	//$boleto = new Boleto();
	//$boleto->iniciar($db_cliente,$lancamento_id,$cliente_id_c);

    $db_cliente->close();

}else{

	echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Web Finanças - Página não encontrada </title>
		</head>
		
		<body>
		<br/>
		<div id="palco">
			
		<table width="100%" height="100%">
				<tr>
				
					<td width="50%" align="center"><img src="https://app.web2business.com/site/img/logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>      
					<td width="50%" align="center">
							<div class="social-icons">
								 <ul class="social-icons">
										<li class="facebook"><a href="http://www.facebook.com/" target="_blank" title="Facebook">Facebook</a></li>
										<li class="googleplus"><a href="http://www.twitter.com/" target="_blank" title="Twitter">Twitter</a></li>
										<li class="linkedin"><a href="http://www.linkedin.com/" target="_blank" title="Linkedin">Linkedin</a></li>
									</ul> 
								</div>
					</td>
					
				</tr>
				<tr>
				
				<td width="50%" rowspan="2" align="center">  
				 <h2> Boleto inválido! </h2> 
				 <p>Dúvidas entre em contato através do Antedimento Online.</p>
				 <p>Clique abaixo pra retornar a página inicial.</p> <br />
					<p align="center"><a href="https://app.web2business.com" class="bt">Página Inicial</a></p>
				
				</td>    
				<td width="50%" align="center" valign="bottom"> 
					 <img src="https://app.web2business.com/site/img/email_paginas/aviso.png" align="center" class="img" width="256"/>	
				 </td>
				 
			</tr>
		</table>	
		</div>
		<div id="rodape">
		
			<div style="font-size:0pt; line-height:0pt; height:1px; background:#e3eaf0;"></div>
			<div style="font-size:0pt; line-height:0pt; height:1px; background:#ffffff;"></div>
		
			<p align="center">
		 <!-- *Caso não seja o seu e-mail em 24h os registros cadastrados em nossos sistemas serão apagados.<br /> -->
		 
				</p>
			</div>
		</body>
		</html>
		
		
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
		border: 0px solid #CCC;
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
		
		.img { width: 256px; }
		.bt { background: #007CC3; /* background:#289CDC;*/ padding:10px 14px;-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; color: #FFF; text-decoration: none; font-weight: bold;}
		
		/* Social Icons */
		ul.social-icons {
			margin: 0;
			padding: 0;
			width: auto;
		}
		
		ul.social-icons li {
			background-image: url(https://app.web2business.com/site/img/email_paginas/social-sprites.png);
			background-repeat: no-repeat;
			background-color: #FFF;
			background-position: 0 100px;
			display: inline-block;
			margin: -1px 1px 5px 0;
			padding: 0;
			border-radius: 100%;
			overflow: visible;
			transition: all 0.3s ease;
			box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.3);
			-moz-border-radius: 100%;
			-moz-transition: all 0.3s ease;
			-ms-transition: all 0.3s ease;
			-o-transition: all 0.3s ease;
			-webkit-border-radius: 100%;
			-webkit-transition: all 0.3s ease;
		}
		
		ul.social-icons li a {
			display: block;
			height: 30px;
			width: 30px;
			text-align: center;
		}
		
		ul.social-icons li[class] a {
			text-indent: -9999px;
		}
		
		ul.social-icons li a:hover {
			text-decoration: none;
		}
		
		ul.social-icons li a i[class^="icon-"] {
			color: #444;
			font-style: 16px;
			position: relative;
			top: 3px;
		}
		
		ul.social-icons li a:active {
			box-shadow: inset 0 0 10px rgba(0,0,0,0.3), inset 0 0 10px rgba(0,0,0,0.3);
			-moz-box-shadow: inset 0 0 10px rgba(0,0,0,0.3), inset 0 0 10px rgba(0,0,0,0.3);
			-webkit-box-shadow: inset 0 0 10px rgba(0,0,0,0.3), inset 0 0 10px rgba(0,0,0,0.3);
		}
		
		
		ul.social-icons li:active,
		ul.social-icons li a:active {
			border-radius: 100%;
			-moz-border-radius: 100%;
			-webkit-border-radius: 100%;
		}
		
		ul.social-icons li.facebook {
			background-position: 0 -120px;
		}
		
		ul.social-icons li.facebook:hover {
			background-position: 0 -150px;
		}
		
		ul.social-icons li.googleplus {
			background-position: 0 -300px;
		}
		
		ul.social-icons li.googleplus:hover {
			background-position: 0 -330px;
		}
		
		ul.social-icons li.linkedin {
			background-position: 0 -540px;
		}
		
		ul.social-icons li.linkedin:hover {
			background-position: 0 -570px;
		}
		
		}
		
		</style>
	';
}
?>