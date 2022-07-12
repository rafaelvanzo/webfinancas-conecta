<?php
session_start();
require("../../../php/db_conexao_login.php");
require("../class/Usuario.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){

	case "login":
		$usuario = new Usuario();
		$login = $usuario->login($db,$_REQUEST);
		if($login['situacao']==1){
			$_SESSION['permissao'] = $login['permissao'];
			$_SESSION['usuario_id'] = $login['usuario_id'];
			$_SESSION['email'] = $login['email'];
			$_SESSION['db_usuario'] = $login['cliente_db'];
			$_SESSION['db_senha'] = $login['cliente_db_senha'];
		}
		$retorno = array("situacao"=>$login['situacao'],"notificacao"=>$login['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "logoff":
		unset($_SESSION['permissao']);
		unset($_SESSION['usuario_id']);
		unset($_SESSION['email']);
		unset($_SESSION['db_usuario']);
		unset($_SESSION['db_senha']);
		unset($_SESSION['total_disponivel']);
	break;
	
	case "senhaAlterar":
		$usuario = new Usuario();
		$alterarSenha = $usuario-> senhaAlterar($db,$_REQUEST);
		$retorno = array("situacao"=>$alterarSenha['situacao'],"notificacao"=>$alterarSenha['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "senhaRecuperar":
		$usuario = new Usuario();
		$senhaRecuperar = $usuario->senhaRecuperar($db,$_REQUEST);
		$retorno = json_encode($senhaRecuperar);
		echo $retorno;
	break;

}

?>