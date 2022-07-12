<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Lancamento.class.php");
require("../class/Conciliacao.class.php");
require("../class/Recebimento.class.php");
require("../class/Pagamento.class.php");
require("../class/Transferencia.class.php");

define('ERRO','Falha ao executar a operção. Por favor, tente novamente.');
define('SALDO','Saldo insuficiente.');
define('LNCT_INC','Lançamento incluído com sucesso.');
define('LNCT_EDT','Lançamento atualizado com sucesso.');
define('LNCT_EXC','Lançamento excluído com sucesso.');
define('LNCT_EXC_LOTE','Lançamentos excluídos com sucesso.');
define('LNCT_CNLC','Lançamento conciliado com sucesso.');
define('LNCT_IMPORT','Importação realizada com sucesso.');

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'msg'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

$tp_lnct = $_REQUEST['tipo'];

if($tp_lnct=='R'){
	//require("../class/Recebimento.class.php");
	$lancamento = new Recebimento($db,$_REQUEST);
}elseif($tp_lnct=='P'){
	//require("../class/Pagamento.class.php");
	$lancamento = new Pagamento($db,$_REQUEST);
}elseif($tp_lnct=='T'){
	//require("../class/Transferencia.class.php");
	$lancamento = new Transferencia($db,$_REQUEST);
}


switch($_REQUEST['funcao']){

	case "extratoIncluir":
		$conciliacao = new Conciliacao();
		$incluirLancamentos = $conciliacao->extratoIncluir($db,$_REQUEST);
        
        if($incluirLancamentos){
            $status = true;
            $lancamentos_listar = $conciliacao->lancamentosListar($db,$_REQUEST['conta_id_import']);
            $saldo_banco = $conciliacao->getCfSaldo($db,$_REQUEST['conta_id_import']);
            $retorno = array('status'=>true, "lancamentos"=>$lancamentos_listar['lancamentos'],'qtd_lnct'=>$lancamentos_listar['qtd_lnct'],'dt_saldo_banco'=>$saldo_banco['dt_saldo_banco'],'vl_saldo_banco'=>$saldo_banco['vl_saldo_banco'],"notificacao"=>LNCT_IMPORT);
        }else{
            $status = false;
            $retorno = array('status'=>false, "notificacao"=>'O arquivo importado não corresponde à conta selecionada. Verifique o banco e o número da conta.');
        }
		
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;
	
	case "lancamentosExcluir":
		$conciliacao = new Conciliacao();
		$conciliacao->lancamentosCnlcExcluir($db,$_REQUEST);
		$retorno = array("notificacao"=>LNCT_EXC);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;	

	case "lnctExcluirLote":
		$conciliacao = new Conciliacao();
		$conciliacao->lancamentosCnlcExcluirLote($db,$_REQUEST);
		$retorno = array("notificacao"=>LNCT_EXC_LOTE);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;	

	case "boletosIncluir":

		$conciliacao = new Conciliacao();

		$incluirBoletos = $conciliacao->boletosIncluir($db,$_REQUEST);
		
        if($incluirBoletos){
            $satus = true;
            $boletos_listar = $conciliacao->boletosListar($db,$_REQUEST['conta_id_import']);
            $retorno = array("boletos"=>$boletos_listar['boletos'],"qtd_blt"=>$boletos_listar['qtd_blt'],"notificacao"=>LNCT_IMPORT,'status'=>$satus);
        }else{
            $status = false;
            $retorno = array("notificacao"=>'O arquivo importado não corresponde à conta selecionada.','status'=>$satus);
        }
		
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lancamentoIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(3);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		try{
			$db->query('start transaction'); 
			$lancamento_id = $lancamento->lancamentoIncluir($db,$_REQUEST);
			$db->query('commit');
			if($lancamento_id){
				$contas_saldo_listar = $lancamento->contasSaldoListar($db,$_REQUEST['conta_id'],$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_INC,"saldo_total"=>$contas_saldo_listar['saldo_total'],'lancamento_id'=>$lancamento_id);
				$conciliacao = new Conciliacao($db,$_REQUEST);				
				$conciliacao->lancamentosCnlcExcluir($db,$_REQUEST);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;


	case "lancamentoEditar":
        $verificaPermissao = VerificaPermissaoUsuario(4);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$lancamento->lancamentoEditar($db,$_REQUEST);//$conciliacao->lancamentosExistEditar($db,$_REQUEST);
		$retorno = array("situacao"=>1,"notificacao"=>LNCT_EDT);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lancamentoRcrEditar":
        $verificaPermissao = VerificaPermissaoUsuario(8);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$lnct_prog_id = $lancamento->lancamentoRcrEditar($db,$_REQUEST);//$conciliacao->lancamentosExistEditar($db,$_REQUEST);
		$retorno = array("situacao"=>1,"notificacao"=>LNCT_EDT,"lnct_prog_id"=>$lnct_prog_id);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lancamentosExibir":
        $verificaPermissao = VerificaPermissaoUsuario(2);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		$conciliacao = new conciliacao();
		$lancamento_dados = $conciliacao->lancamentosExibir($db,$_REQUEST);
		$retorno = array("lancamento"=>$lancamento_dados['lancamento'],"ctr_plc_lancamentos"=>$lancamento_dados['ctr_plc_lancamentos']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lnctSugest":
		$conciliacao = new conciliacao();
		$lancamentos = $conciliacao->lnctSugest($db,$_REQUEST);
		$retorno = json_encode($lancamentos);
		$db->close();
		echo $retorno;
	break;
	
	case "lnctExistBuscar":
		$conciliacao = new conciliacao();
		$retorno = $conciliacao->lnctExistBuscar($db,$_REQUEST);
		$db->close();
		echo $retorno;
	break;

	case "conciliarLancamento":
        $verificaPermissao = VerificaPermissaoUsuario(4);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
        
        try{
			$db->query('start transaction');
			$conciliado = $lancamento->conciliarLancamento($db,$_REQUEST);
			$db->query('commit');
			if($conciliado){
				$conciliacao = new conciliacao();
				$saldo_total = $conciliacao->getSaldoTotal($db);
				$saldo_total = $db->valorFormat($saldo_total);
				$conciliacao->lancamentosCnlcExcluir($db,$_REQUEST);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_CNLC,"saldo_total"=>$saldo_total);
			}else{
				$retorno = array("situacao"=>0,"notificacao"=>SALDO);
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
	
	case "cnlcIniciar":
		$conciliacao = new conciliacao($db);
		$retorno = $conciliacao->cnlcIniciar($db,$_REQUEST['cf_id']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

}
?>
