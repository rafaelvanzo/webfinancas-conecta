<?php
session_start();
require("../../../php/db_conexao_login.php");
require("../class/Usuario.class.php");

switch($_REQUEST['funcao']){

	case "login":
		$usuario = new Usuario();
		$login = $usuario->login($db,$_REQUEST);
		if($login['situacao']==1){
			$_SESSION['permissao'] = $login['permissao'];
			$_SESSION['usuario_id'] = $login['usuario_id'];
			$_SESSION['email'] = $login['email'];
		}
		$retorno = array("situacao"=>$login['situacao'],"notificacao"=>$login['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

//Cotação do Dolar
$dolar = pega_cota('USD'); 

//Cotação do Euro
$euro = pega_cota('EUR'); 

//Cotação do Libra
$libra = pega_cota('GBP'); 

}

?>