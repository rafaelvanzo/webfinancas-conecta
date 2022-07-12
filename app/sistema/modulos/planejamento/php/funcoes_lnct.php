<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Lancamento.class.php");

define('ERRO','Falha ao executar a operção. Por favor, tente novamente.');
define('SALDO','Saldo insuficiente.');
define('LNCT_INC','Lançamento incluído com sucesso.');
define('LNCT_EDT','Lançamento atualizado com sucesso..');
define('LNCT_EXC','Lançamento excluído com sucesso.');

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){

	case "recebimentosIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(15);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
        try{
			$db->query('start transaction');
			$lancamento = new Lancamento($db,$_REQUEST);
			$lancamento_id = $lancamento->recebimentosIncluir($db,$_REQUEST);
			$lancamentos_listar = $lancamento->lancamentosListar($db,$lancamento_id);
			$retorno = array('situacao'=>1,"notificacao"=>LNCT_INC,"lancamento"=>$lancamentos_listar);
			$db->query('commit');
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>ERRO);
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "recebimentosEditar":
        $verificaPermissao = VerificaPermissaoUsuario(16);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
        try{
			$db->query('start transaction');
			$lancamento = new Lancamento($db,$_REQUEST);
			$lancamento_id = $lancamento->recebimentosEditar($db,$_REQUEST);
			$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST['lancamento_id']);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_EDT,"lancamento"=>$lancamentos_listar);
			$db->query('commit');
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>ERRO);
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "recebimentosExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(17);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		try{
			$db->query('start transaction');
			$lancamento = new Lancamento();
			$excluir = $lancamento->recebimentosExcluir($db,$_REQUEST['lancamento_id']);
			$retorno = array('situacao'=>1,"notificacao"=>LNCT_EXC);
			$db->query('commit');
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>ERRO);
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "pagamentosIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(15);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		try{
			$db->query('start transaction');
			$lancamento = new Lancamento($db,$_REQUEST);
			$lancamento_id = $lancamento->pagamentosIncluir($db,$_REQUEST);
			$lancamentos_listar = $lancamento->lancamentosListar($db,$lancamento_id);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_INC,"lancamento" => $lancamentos_listar);
			$db->query('commit');
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>ERRO);
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "pagamentosEditar":
        $verificaPermissao = VerificaPermissaoUsuario(16);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		try{
			$db->query('start transaction');
			$lancamento = new Lancamento($db,$_REQUEST);
			$lancamento_id = $lancamento->pagamentosEditar($db,$_REQUEST);
			$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST['lancamento_id']);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_EDT,"lancamento" => $lancamentos_listar);
			$db->query('commit');
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->ERRO);
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "pagamentosExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(17);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
		try{
			$db->query('start transaction');
			$lancamento = new Lancamento();
			$excluir = $lancamento->pagamentosExcluir($db,$_REQUEST['lancamento_id']);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_EXC);
			$db->query('commit');
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>ERRO);
		}
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lancamentosExibir":
        $verificaPermissao = VerificaPermissaoUsuario(14);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }
        $lancamento = new Lancamento();
		$lancamento_dados = $lancamento->lancamentosExibir($db,$_REQUEST);
		$retorno = array("lancamento"=>$lancamento_dados['lancamento'],"ctr_plc_lancamentos"=>$lancamento_dados['ctr_plc_lancamentos']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lancamentosListar":
		$lancamento = new Lancamento();
		$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST);
		$retorno = array("lancamentos" => $lancamentos_listar['lancamentos'],"nome_conta" => $lancamentos_listar['nome_conta']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;
	
	case "lancamentosBuscarPeriodo":
		$lancamento = new Lancamento();
		$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST,$_REQUEST['tp_busca']);
		$retorno = array("lancamentos" => $lancamentos_listar['lancamentos']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lancamentosBuscarMes":
		$lancamento = new Lancamento();
		$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST,$_REQUEST['tp_busca']);
		$retorno = array("lancamentos" => $lancamentos_listar['lancamentos']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;
	
}
?>
