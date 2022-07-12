<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Importacao.class.php");

define('ERRO','Falha ao executar a operção. Por favor, tente novamente.');
define('SALDO','Saldo insuficiente.');
define('LNCT_INC','Lançamento incluído com sucesso.');
define('LNCT_INC_LOTE','Lançamentos incluídos com sucesso.');
define('LNCT_EDT','Lançamento atualizado com sucesso.');
define('LNCT_EXC','Lançamento excluído com sucesso.');
define('LNCT_EXC_LOTE','Lançamentos excluídos com sucesso.');
define('LNCT_CNLC','Lançamento importado com sucesso.');
define('LNCT_IMPORT_LOTE','Lançamentos importados com sucesso.');
define('LNCT_IMPORT','Importação realizada com sucesso.');
/*
function myErrorHandler($errno, $errstr, $errfile, $errline) {
	$erro = $errstr.' '.$errfile.' '.$errline;
	throw new Exception($erro);
}
set_error_handler("myErrorHandler");
*/

switch($_REQUEST['funcao']){

	case "lancamentosIncluir":
		$importacao = new Importacao();
		$importacao->lancamentosIncluir($db,$_REQUEST);
		$lancamentos_listar = $importacao->lancamentosListar($db);
		$retorno = array("lancamentos" => $lancamentos_listar,"notificacao"=>LNCT_IMPORT);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;
	
	case "lancamentosExcluir":
		$importacao = new Importacao();
		$importacao->lancamentosImportExcluir($db,$_REQUEST);
		$retorno = array("notificacao"=>LNCT_EXC);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;	

	case "lnctExcluirLote":
		$importacao = new Importacao();
		$importacao->lancamentosImportExcluirLote($db,$_REQUEST);
		$retorno = array("notificacao"=>LNCT_EXC_LOTE);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;	

	case "recebimentosIncluir":
		$db->query('start transaction');
		$importacao = new Importacao($db,$_REQUEST);
		$importacao->recebimentosIncluir($db,$_REQUEST);
		$importacao->lancamentosImportExcluir($db,$_REQUEST);
		$db->query('commit');
		$retorno = array("situacao"=>1,"notificacao"=>LNCT_INC);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;	

	case "pagamentosIncluir":
		$db->query('start transaction');
		$importacao = new Importacao($db,$_REQUEST);
		$incluir = $importacao->pagamentosIncluir($db,$_REQUEST);
		if($incluir==1){
			$importacao->lancamentosImportExcluir($db,$_REQUEST);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_INC);
		}else{
			$retorno = array('situacao'=>2,'notificacao'=>SALDO);
		}
		$db->query('commit');
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;	

	case "transferenciasIncluir":
		$db->query('start transaction');
		$importacao = new Importacao($db,$_REQUEST);
		$incluir = $importacao->transferenciasIncluir($db,$_REQUEST);
		if($incluir==1){
			$importacao->lancamentosImportExcluir($db,$_REQUEST);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_INC);
		}else{
			$retorno = array('situacao'=>2,'notificacao'=>SALDO);
		}
		$db->query('commit');
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	case "lnctLoteIncluir":
		$db->query('start transaction');
		$importacao = new Importacao();
		$incluir = $importacao->lnctLoteIncluir($db,$_REQUEST);
		if($incluir==1){
			$importacao->lancamentosImportExcluirLote($db,$_REQUEST);
			$retorno = array("situacao"=>1,"notificacao"=>LNCT_INC_LOTE);
		}else{
			$retorno = array('situacao'=>2,'notificacao'=>SALDO);
		}
		$db->query('commit');
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

}
?>
