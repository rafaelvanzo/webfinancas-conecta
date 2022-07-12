<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Orcamento.class.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){
	
	case "orcamentosIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(11);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
		$orcamento = new Orcamento($_REQUEST);
		$incluir = $orcamento->orcamentosIncluir($db,$_REQUEST);
		if($incluir){
			$retorno = array("situacao"=>1,"notificacao"=>"Orçamento cadastrado com sucesso.");
		}else{
			$retorno = array("situacao"=>0,"notificacao"=>"Já existe um orçamento cadastrado com a descrição informada.");
		}
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "orcamentosExibir":
        $verificaPermissao = VerificaPermissaoUsuario(10);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$orcamento = new Orcamento($_REQUEST);
		$exibir = $orcamento->orcamentosExibir($db,$_REQUEST);
		$retorno = array("situacao"=>1,"valores"=>$exibir['valores']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "orcamentosEditar":
        $verificaPermissao = VerificaPermissaoUsuario(12);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
        $orcamento = new Orcamento($_REQUEST);
		$editar = $orcamento->orcamentosEditar($db,$_REQUEST);
		if($editar){
			$retorno = array("situacao"=>1,"notificacao"=>"Orçamento atualizado com sucesso.");
		}else{
			$retorno = array("situacao"=>0,"notificacao"=>"Já existe um orçamento cadastrado com a descrição informada.");
		}
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "orcamentosExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(13);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$orcamento = new Orcamento($_REQUEST);
		$exibir = $orcamento->orcamentosExcluir($db,$_REQUEST);
		$retorno = array("situacao"=>1,"notificacao"=>"Orçamento excluído com sucesso.");
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "contasEditar":
        try{
			$db->query('start transaction');
			$conta = new Conta($_REQUEST);
			$editar = $conta->contasEditar($db,$_REQUEST);
			if($editar['situacao']==1){
				$contas_listar = $conta->contasListar($db);
				$db->query('commit');
				$retorno = array("situacao"=>$editar['situacao'],"notificacao"=>$editar['notificacao'],"contas"=>$contas_listar);
			}else{
				$db->query('rollback');
				$retorno = array("situacao"=>$editar['situacao'],"notificacao"=>$editar['notificacao']);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

}
?>
