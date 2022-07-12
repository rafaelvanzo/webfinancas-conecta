<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/PlanoContas.class.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){
	
	case "planoContasIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(31);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
        
		$planoContas = new planoContas($_REQUEST);
		$incluir = $planoContas->planoContasIncluir($db);
		if($incluir[situacao]==1){
			$planoContas_listar = $planoContas->planoContasListar($db);
			$retorno = array("planoContas" => $planoContas_listar,"situacao"=>$incluir[situacao],"notificacao"=>$incluir[notificacao]);
		}else{
			$retorno = array("situacao"=>$incluir[situacao],"notificacao"=>$incluir[notificacao]);
		}
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "planoContasEditar":
        $verificaPermissao = VerificaPermissaoUsuario(32);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$db->query('start transaction');
		$planoContas = new planoContas($_REQUEST);
		$editar = $planoContas->planoContasEditar($db,$_REQUEST);
		$db->query('commit');
		if($editar[situacao]==1){
			$planoContas_listar = $planoContas->planoContasListar($db);
			$retorno = array("planoContas" => $planoContas_listar,"situacao"=>$editar[situacao],"notificacao"=>$editar[notificacao]);
		}else{
			$retorno = array("situacao"=>$editar[situacao],"notificacao"=>$editar[notificacao]);
		}
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "planoContasExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(33);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$db->query('start transaction');
		$planoContas = new planoContas();
		$excluir = $planoContas->planoContasExcluir($db,$_REQUEST[planoContas_id]);
		$db->query('commit');
		$planoContas_listar = $planoContas->planoContasListar($db);
		$retorno = array("planoContas" => $planoContas_listar,"notificacao"=>$excluir[notificacao],"situacao"=>$excluir[situacao]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "planoContasExibir":
        $verificaPermissao = VerificaPermissaoUsuario(30);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$planoContas = new planoContas();
		$retorno = $planoContas->planoContasExibir($db,$_REQUEST[planoContas_id]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

    case "CarregarPlanoContas":
        $verificaPermissao = VerificaPermissaoUsuario(31);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$planoContas = new planoContas();
		$planoContas->CarregarPlanoContas($db,$_REQUEST);
        $plc = $planoContas->planoContasListar($db);
        echo json_encode(array('situacao'=>1,'notificacao'=>'Plano de contas carregado','planoContas'=>$plc['planoContas'],'totalCategorias'=>$plc['totalCategorias']));
    break;
	
}
?>
