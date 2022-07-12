<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Relatorio.class.php");

switch($_REQUEST['funcao']){
	
	case "contasFinanceirasSaldo":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->contasFinanceirasSaldo($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "contasFinanceirasExtrato":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->contasFinanceirasExtrato($db,$_REQUEST);
		$retorno = array("relatorio" => $relatorio_gerar);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "planoContas":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->planoContas($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;
	
	case "centroResp":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->centroResp($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "movimentoFinanceiro":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->movimentoFinanceiro($db,$_REQUEST['params']);
		$retorno = array("relatorio" => $relatorio_gerar);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "planoContasCentroResp":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->planoContasCentroResp($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "fluxoCaixaDiario":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->fluxoCaixaDiario($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "fluxoCaixaMensal":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->fluxoCaixaMensal($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "fluxoCaixaMensalN":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->fluxoCaixaMensalN($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "fluxoCaixaDiarioN":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->fluxoCaixaDiarioN($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "dre":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->dre($db,$_REQUEST['params']);
	break;

	case "rcbts_pgtos":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->rcbts_pgtos($db,$_REQUEST['params']);
		//$retorno = array("relatorio" => $relatorio_gerar);
		//$retorno = json_encode($retorno);
		//echo $retorno;
	break;

	case "simei":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->simei($db,$_REQUEST['params']);
	break;

    case "CarneLeao":
		$relatorio = new Relatorio();
		$relatorio_gerar = $relatorio->CarneLeao($db,$_REQUEST['params']);
        break;

    case "HistoricoLancamentos":
		$relatorio = new Relatorio();
		$relatorio->HistoricoLancamentos($db,$_REQUEST['params']);
        break;

	case "pdfGerar":
		$relatorio = new Relatorio();
		$pdf_gerar = $relatorio->pdfGerar("",$_REQUEST["tp_print"]);
	break;
}
?>
