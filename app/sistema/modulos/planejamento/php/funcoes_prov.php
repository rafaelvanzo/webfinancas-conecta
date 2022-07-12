<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Provisao.class.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){
	
	case "dpreIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(20);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
		$provisao = new Provisao();
		$incluir = $provisao->dpreIncluir($db,$_REQUEST);
		$retorno = array("situacao"=>1,"notificacao"=>"Depreciação cadastrada com sucesso.");
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "amrtIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(20);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$provisao = new Provisao();
		$incluir = $provisao->amrtIncluir($db,$_REQUEST);
		$retorno = array("situacao"=>1,"notificacao"=>"Amortização cadastrada com sucesso.");
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "trbtIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(20);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$provisao = new Provisao();
		$incluir = $provisao->trbtIncluir($db,$_REQUEST);
		$retorno = array("situacao"=>1,"notificacao"=>"Provisão trabalhista cadastrada com sucesso.");
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "provisaoExibir":
        $verificaPermissao = VerificaPermissaoUsuario(20);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		$provisao = new Provisao();
		$retorno = $provisao->provisaoExibir($db,$_REQUEST);
		echo $retorno;
	break;

}
?>
