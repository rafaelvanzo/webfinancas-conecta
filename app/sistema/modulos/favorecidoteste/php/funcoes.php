<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Favorecido.class.php");

define(FAV_IMPORT,"Importação realizada cmo sucesso.");

switch($_REQUEST['funcao']){
	
	case "favorecidosIncluir":
		$favorecido = new Favorecido($_REQUEST);
		$incluir = $favorecido->favorecidosIncluir($db);
		//$favorecidos_listar = $favorecido->favorecidosListar($db);
		$retorno = array(/*"favorecidos" => $favorecidos_listar,*/"notificacao"=>$incluir[notificacao]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "favorecidosIncluirAc": //incluír favorecido autocompletar pelo autocompletar
		$favorecido = new Favorecido();
		$favorecido_id = $favorecido->favorecidosIncluirAc($db,$_REQUEST);
		$retorno = array("favorecido_id"=>$favorecido_id,"notificacao"=>"Favorecido cadastrado com sucesso.");
		$retorno = json_encode($retorno);
		echo $retorno;		
	break;

	case "favorecidosEditar":
		$_REQUEST['agencia'] = $_REQUEST['ag']; //bug do html que não está acenitando o input com nome agencia
		$favorecido = new Favorecido($_REQUEST);
		$editar = $favorecido->favorecidosEditar($db,$_REQUEST);
		//$favorecidos_listar = $favorecido->favorecidosListar($db);
		$retorno = array(/*"favorecidos" => $favorecidos_listar,*/"notificacao"=>$editar['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "favorecidosExcluir":
		$favorecido = new Favorecido();
		$excluir = $favorecido->favorecidosExcluir($db,$_REQUEST[favorecido_id]);
		$retorno = array("notificacao"=>$excluir[notificacao],"situacao"=>$excluir[situacao],"favorecido_id"=>$_REQUEST['favorecido_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "favorecidosVisualizar":
		$favorecido = new Favorecido();
		$retorno = $favorecido->favorecidosVisualizar($db,$_REQUEST[favorecido_id]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;	

	case "favorecidosImportar":
		require("../../../php/excel/reader.php");
		$favorecido = new Favorecido();
		$retorno = $favorecido->favorecidosImportar($db,$_REQUEST);
		echo $retorno;
	break;

	case "favorecidosImportarFim":
		require("../../../php/excel/reader.php");
		$favorecido = new Favorecido();
		$favorecido->favorecidosImportarFim($db,$_REQUEST);
		$favorecidos_listar = $favorecido->favorecidosListar($db);
		$retorno = array("favorecidos"=>$favorecidos_listar,"notificacao"=>FAV_IMPORT);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "arquivosExcluir":
		$favorecido = new Favorecido();
		$favorecido->arquivosExcluir($_REQUEST);
	break;

	case "favExport":
		$root = $_SERVER['DOCUMENT_ROOT'];
		chdir($root.'/sistema/php/excel');
		require("Writer.php");
		chdir($root.'/sistema/modulos/favorecido/php');
		$favorecido = new Favorecido();
		$favorecido->favExport($db);
	break;

    case "DataTableAjax":
		$retorno = Favorecido::DataTableAjax($db,$_REQUEST);
		$db->close();
		echo $retorno;
        break;

}
?>
