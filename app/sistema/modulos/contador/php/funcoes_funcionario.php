<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Contador.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){

	case "conviteContador":
		$contador = new Contador();
		$conviteContador = $contador-> conviteContador($db,$_REQUEST);
		$retorno = array("situacao"=>$conviteContador['situacao'],"notificacao"=>$conviteContador['notificacao'],"lista_conexoes"=>$conviteContador['lista_conexoes']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "reenviarConvite":
		$contador = new Contador();
		$reenviarConvite = $contador-> reenviarConvite($db,$_REQUEST);
		$retorno = array("situacao"=>$reenviarConvite['situacao'],"notificacao"=>$reenviarConvite['notificacao'],"lista_conexoes"=>$reenviarConvite['lista_conexoes']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "cancelarConexoes":
		$contador = new Contador();
		$cancelarConexoes = $contador-> cancelarConexoes($db,$_REQUEST);
		$retorno = array("situacao"=>$cancelarConexoes['situacao'],"notificacao"=>$cancelarConexoes['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "cancelarConexoesAtivas":
		$contador = new Contador();
		$cancelarConexoesAtivas = $contador-> cancelarConexoesAtivas($db,$_REQUEST);
		$retorno = array("situacao"=>$cancelarConexoesAtivas['situacao'],"notificacao"=>$cancelarConexoesAtivas['notificacao'],"conexao_contador"=>$cancelarConexoesAtivas['conexao_contador']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "aceitarConexoes":
		$contador = new Contador();
		$aceitarConexoes = $contador-> aceitarConexoes($db,$_REQUEST);
		$retorno = array("situacao"=>$aceitarConexoes['situacao'],"notificacao"=>$aceitarConexoes['notificacao'],"lista_conexoes"=>$aceitarConexoes['lista_conexoes'],"contador_info"=>$aceitarConexoes['contador_info']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "addConversa":
		$contador = new Contador();
		$addConversa = $contador-> addConversa($db,$_REQUEST);
		$retorno = array("atualizarConversa"=>$addConversa['atualizarConversa'],"chat_id"=>$addConversa['chat_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "addMensagem":
		$contador = new Contador();
		$addMensagem = $contador-> addMensagem($db,$_REQUEST);
		$retorno = array("atualizarConversa"=>$addMensagem['atualizarConversa'],"chat_id"=>$addMensagem['chat_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "visualizarMensagens":
		$contador = new Contador();
		$visualizarMensagens = $contador-> visualizarMensagens($db,$_REQUEST);
		$retorno = array("atualizarConversa"=>$visualizarMensagens['atualizarConversa'],"chat_id"=>$visualizarMensagens['chat_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "cfListar":
		$contador = new Contador();
		$cf = $contador->cfListar($db,$mes,$ano);
		echo $cf;
	break;

	case "RemessaContabil":
		$contador = new Contador();
		$contador->RemessaContabil($db,$_REQUEST);
		$cf_listar = $contador->cfListar($db,$_REQUEST['mes'],$_REQUEST['ano']);
		$retorno = array('situacao'=>1,'notificacao'=>'Remessa contábil enviada com sucesso','contas'=>$cf_listar);
		echo json_encode($retorno);
	break;
	
	case "RemessaPesquisar":
		$contador = new Contador();
		$cf_listar = $contador->cfListar($db,$_REQUEST['mes'],$_REQUEST['ano']);
		echo json_encode(array('contas'=>$cf_listar));
	break;
	
	case "RemessaHistorico":
		$contador = new Contador();
		$contador->RemessaHistorico($db,$_REQUEST);
	break;
	
}

?>