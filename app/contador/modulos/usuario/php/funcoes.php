<?php
session_start();
require("../../../../sistema/php/db_conexao_login.php");
require("../class/Usuario.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){

	case "login":
		$usuario = new Usuario();
		$login = $usuario->login($db,$_REQUEST);
		if($login['situacao']==1){
			$_SESSION['cli_acesso_situacao'] = $login['cli_acesso_situacao'];
			$_SESSION['permissao_contador'] = $login['permissao_contador'];
			$_SESSION['usuario_id'] = $login['usuario_id'];
			$_SESSION['cliente_id'] = $login['cliente_id'];
			$_SESSION['email'] = $login['email'];
			$_SESSION['db_usuario'] = $login['cliente_db'];
			$_SESSION['db_senha'] = $login['cliente_db_senha'];
			$_SESSION['primeiro_acesso'] = $login['primeiro_acesso'];
		}
		$retorno = array("situacao"=>$login['situacao'],"notificacao"=>$login['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "logoff":
        session_destroy();
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
	
	case "usuariosEditar":
		$usuario = new Usuario();
		$usuariosEditar = $usuario->usuariosEditar($db,$_REQUEST);
		$retorno = json_encode($usuariosEditar);
		echo $retorno;
	break;
	
	case "planoEditar":
		$plano = new Usuario();
		$planoEditar = $plano->planoEditar($db,$_REQUEST);
		$retorno = json_encode($planoEditar);
		echo $retorno;
	break;
	
}

?>