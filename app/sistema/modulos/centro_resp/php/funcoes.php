<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/CentroResp.class.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){
	
	case "centroRespIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(27);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $db->query('start transaction');
		$centro_resp = new CentroResp($_REQUEST);
		$incluir = $centro_resp->centroRespIncluir($db);
		$db->query('commit');
		if($incluir[situacao]==1){
			$centro_resp_listar = $centro_resp->centroRespListar($db);
			$retorno = array("centro_resp"=>$centro_resp_listar,"situacao"=>$incluir[situacao],"notificacao"=>$incluir[notificacao]);
		}else{
			$retorno = array("situacao"=>$incluir[situacao],"notificacao"=>$incluir[notificacao]);
		}
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "centroRespEditar":
        $verificaPermissao = VerificaPermissaoUsuario(28);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

		$db->query('start transaction');
		$centro_resp = new CentroResp($_REQUEST);
		$editar = $centro_resp->centroRespEditar($db,$_REQUEST);
		$db->query('commit');
		if($editar[situacao]==1){
			$centro_resp_listar = $centro_resp->centroRespListar($db);
			$retorno = array("centro_resp"=>$centro_resp_listar,"situacao"=>$editar[situacao],"notificacao"=>$editar[notificacao]);
		}else{
			$retorno = array("situacao"=>$editar[situacao],"notificacao"=>$editar[notificacao]);
		}
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "centroRespExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(29);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

		$db->query('start transaction');
		$centro_resp = new CentroResp();
		$excluir = $centro_resp->centroRespExcluir($db,$_REQUEST['centro_resp_id']);
		$db->query('commit');
		$centro_resp_listar = $centro_resp->centroRespListar($db);
		$retorno = array("centro_resp"=>$centro_resp_listar,"notificacao"=>$excluir['notificacao'],"situacao"=>$excluir['situacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "centroRespExibir":
        $verificaPermissao = VerificaPermissaoUsuario(26);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
         
		$centro_resp = new CentroResp();
		$retorno = $centro_resp->centroRespExibir($db,$_REQUEST[centro_resp_id]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

/*
	case "codRefValidar":
		$centro_resp = new CentroResp();
		$retorno = $centro_resp->codRefValidar($db,$_REQUEST[cod_ref]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;	
*/

}
?>
