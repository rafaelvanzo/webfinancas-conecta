<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Favorecido.class.php");

define(FAV_IMPORT,"Importação realizada cmo sucesso.");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){
	
	case "favorecidosIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(23);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
        
		$favorecido = new Favorecido($_REQUEST);
		$incluir = $favorecido->favorecidosIncluir($db);
		//$favorecidos_listar = $favorecido->favorecidosListar($db);
		$retorno = array('status'=>$incluir['situacao'], "notificacao"=>$incluir['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "favorecidosIncluirAc": //incluír favorecido autocompletar pelo autocompletar
        $verificaPermissao = VerificaPermissaoUsuario(23);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
         
		$favorecido = new Favorecido();
		$favorecido_id = $favorecido->favorecidosIncluirAc($db,$_REQUEST);
		//$retorno = array('status'=>1, "favorecido_id"=>$favorecido_id,"notificacao"=>"Favorecido cadastrado com sucesso.");
		$retorno = $favorecido_id;
		$retorno = json_encode($retorno);
		echo $retorno;		
	break;

	case "favorecidosEditar":
        $verificaPermissao = VerificaPermissaoUsuario(24);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
         
		$_REQUEST['agencia'] = $_REQUEST['ag']; //bug do html que não está acenitando o input com nome agencia
		$favorecido = new Favorecido($_REQUEST);
		$editar = $favorecido->favorecidosEditar($db,$_REQUEST);
		//$favorecidos_listar = $favorecido->favorecidosListar($db);
		$retorno = array('status'=>$editar['situacao'], "notificacao"=>$editar['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "favorecidosExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(25);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
         
		$favorecido = new Favorecido();
		$excluir = $favorecido->favorecidosExcluir($db,$_REQUEST[favorecido_id]);
		$retorno = array("notificacao"=>$excluir[notificacao],"status"=>$excluir[situacao],"favorecido_id"=>$_REQUEST['favorecido_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "favorecidosVisualizar":
        $verificaPermissao = VerificaPermissaoUsuario(22);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
         
		$favorecido = new Favorecido();
		$retorno = $favorecido->favorecidosVisualizar($db,$_REQUEST[favorecido_id]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;	

	case "favorecidosImportar":
        $verificaPermissao = VerificaPermissaoUsuario(23);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
         
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
