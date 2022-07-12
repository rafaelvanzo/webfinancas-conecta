<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Conta.class.php");
require("../../boleto/Remessa.class.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){

	case "contasIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(35);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$conta = new Conta($_REQUEST);
		$incluir = $conta->contasIncluir($db);
		$contas_listar = $conta->contasListar($db);
		$retorno = array('status'=>1,"contas" => $contas_listar,"notificacao"=>$incluir['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "contasIncluirAc":
        $verificaPermissao = VerificaPermissaoUsuario(35);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$conta = new Conta();
		$conta_id = $conta->contasIncluirAc($db,$_REQUEST);
		$retorno = array("conta_id"=>$conta_id,"notificacao"=>"Conta cadastrada com sucesso.");
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "contasEditar":
        $verificaPermissao = VerificaPermissaoUsuario(36);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		try{
			$db->query('start transaction');
			$conta = new Conta($_REQUEST);
			$editar = $conta->contasEditar($db,$_REQUEST);
			if($editar['situacao']==1){
				$contas_listar = $conta->contasListar($db);
				$db->query('commit');
				$retorno = array("status"=>$editar['situacao'],"notificacao"=>$editar['notificacao'],"contas"=>$contas_listar);
			}else{
				$db->query('rollback');
				$retorno = array("status"=>$editar['situacao'],"notificacao"=>$editar['notificacao']);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('status'=>0,'notificacao'=>$e->getMessage());
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "contasExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(37);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$conta = new Conta();
		$excluir = $conta->contasExcluir($db,$_REQUEST['conta_id']);
		$retorno = array("notificacao"=>$excluir['notificacao'],"status"=>$excluir['situacao'],"conta_id"=>$_REQUEST['conta_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "contasVisualizar":
        $verificaPermissao = VerificaPermissaoUsuario(34);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$conta = new Conta();
		$retorno = $conta->contasVisualizar($db,$_REQUEST['conta_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "carteira":
		$conta = new Conta();
		$retorno = $conta->carteira($_REQUEST['cod_banco']);
		//$retorno = json_encode($retorno);
		echo $retorno;
	break;
    
    case "visualizarBoletos":
		$visualizarBoletos = new Conta();
		$retorno = $visualizarBoletos->visualizarBoletos($db,$_REQUEST);
		$retorno = json_encode($retorno);
		echo $retorno;
    break;
    
    case "gerarRemessa":
		$gerarRemessa = new Conta();        
		$retorno = $gerarRemessa->gerarRemessa($db,$_REQUEST);
		$retorno = json_encode($retorno);
		echo $retorno;
    break;
    
    case "gerarRemessaBotao":
		$gerarRemessa = new Conta();        
		$retorno = $gerarRemessa->gerarRemessaBotao($db,$_REQUEST);
		$retorno = json_encode($retorno);
		echo $retorno;
    break;
    
    case "listarRemessa":
        $verificaPermissao = VerificaPermissaoUsuario(34);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$listarRemessa = new Conta();        
		$retorno = $listarRemessa->listarRemessa($db);
		$retorno = json_encode($retorno);
		echo $retorno;
   break;
	
}
?>
