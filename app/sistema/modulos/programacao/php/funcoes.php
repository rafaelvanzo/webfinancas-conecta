<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Recorrencia.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){

	case "lancamentoIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(7);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
        $lancamento = new Recorrencia($db,$_REQUEST);
		$incluir = $lancamento->lancamentoIncluir($db,$_REQUEST);
		//$lancamentos_listar = $lancamento->lancamentosListar($db);
		$retorno = array("status"=>$incluir["situacao"],"notificacao"=>$incluir["notificacao"]/*,"lancamentos"=>$lancamentos_listar*/);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "lancamentoEditar":
        $verificaPermissao = VerificaPermissaoUsuario(8);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$lancamento = new Recorrencia($db,$_REQUEST);
		$editar = $lancamento->lancamentoEditar($db,$_REQUEST);
		//$lancamentos_listar = $lancamento->lancamentosListar($db);
		$retorno = array("status"=>$editar["situacao"],"notificacao"=>$editar["notificacao"]/*,"lancamentos"=>$lancamentos_listar*/);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "lancamentoExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(9);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$lancamento = new Recorrencia();
		$excluir = $lancamento->lancamentoExcluir($db,$_REQUEST[lancamento_id]);
		$retorno = array("status"=>$excluir["situacao"],"notificacao"=>$excluir[notificacao]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "lancamentoExibir":
        $verificaPermissao = VerificaPermissaoUsuario(6);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$lancamento = new Recorrencia();
		$lancamento_dados = $lancamento->lancamentoExibir($db,$_REQUEST);
		$retorno = array("lancamento"=>$lancamento_dados[lancamento],"ctr_plc_lancamentos"=>$lancamento_dados[ctr_plc_lancamentos]);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

    /*
	===========================================================================================
	FILTRAR
	===========================================================================================
     */

	case "DataTableAjax":
		$retorno = Recorrencia::DataTableAjax($db,$_REQUEST);
		$db->close();
		echo $retorno;
        break;

}
?>
