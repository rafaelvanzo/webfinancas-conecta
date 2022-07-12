<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Convites.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){

	
	case "conviteContador":
		$convites = new Convites();
		$conviteContador = $convites-> conviteContador($db,$_REQUEST);
		$retorno = array("situacao"=>$conviteContador['situacao'],"notificacao"=>$conviteContador['notificacao'],"listar_convites"=>$conviteContador['listar_convites']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "reenviarConvite":
		$convites = new Convites();
		$reenviarConvite = $convites-> reenviarConvite($db,$_REQUEST);
		$retorno = array("situacao"=>$reenviarConvite['situacao'],"notificacao"=>$reenviarConvite['notificacao'],"listar_clientes"=>$reenviarConvite['listar_clientes']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "cancelarConexoes":
		$convites = new Convites();
		$cancelarConexoes = $convites-> cancelarConexoes($db,$_REQUEST);
		$retorno = array("situacao"=>$cancelarConexoes['situacao'],"notificacao"=>$cancelarConexoes['notificacao'],"listar_clientes"=>$cancelarConexoes['listar_clientes']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	

    case "AceitarConexoes":
		$contador = new Convites();
		$retorno = $contador->AceitarConexoes($db,$_REQUEST);
        $retorno = json_encode($retorno);
		echo $retorno;
        break;

    case "ConviteExcluir":
		$convites = new Convites();
		$excluir = $convites-> ConviteExcluir($db,$_REQUEST);
		$retorno = array("situacao"=>$excluir['situacao'],"notificacao"=>$excluir['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
        break;
}

?>