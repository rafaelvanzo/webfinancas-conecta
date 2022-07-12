<?php
require("conexao_db.php");




/* ============================================ */

function FluxoCaixa($Id){

	switch ($Id) {

		case '1':
			$Nome = 'Entradas Operacionais';
			break;
	
		case '2':
			$Nome = 'Investimentos';
			break;
	
		case '3':
			$Nome = 'Resgate de Investimentos';
			break;

		case '4':
			$Nome = 'Resgate de Investimentos';
			break;

		case '5':
			$Nome = 'Receitas Financeiras';
			break;

		case '6':
			$Nome = 'Financiamentos';
			break;

		case '7':
			$Nome = 'Pagamentos dos Financiamentos';
			break;

		case '8':
			$Nome = 'Despesas Financeiras';
			break;

		case '9':
			$Nome = 'Aporte dos S�cios';
			break;

		case '10':
			$Nome = 'Pagamento aos s�cios';
			break;

		case '11':
			$Nome = 'Entrada de Tesouraria';
			break;

		case '12':
			$Nome = 'Sa�da de Tesouraria';
			break;

		default:
			$Nome = 'N�o classificado';
			break;
	}


	return $Nome;
}


function DRE($Id){

	switch ($Id) {

		case '1':
			$Nome = 'Receitas Operacionais';
			break;
	
		case '2':
			$Nome = 'Receitas Financeiras';
			break;
	/*
		case '3':
			$Nome = 'Despesas Operacionais';
			break;
	*/
		case '4':
			$Nome = 'Despesas Financeiras';
			break;

		case '5':
			$Nome = 'Despesas Vari�veis';
			break;

		case '6':
			$Nome = 'Despesas Fixas';
			break;

		case '7':
			$Nome = 'Custos da Produ��o - CP';
			break;

		case '8':
			$Nome = 'Custos da Mercadoria Vendida - CMV';
			break;

		case '9':
			$Nome = 'Custos do Servi�o Prestado - CSP';
			break;

		case '10':
			$Nome = 'Impostos S/ Vendas';
			break;

		case '11':
			$Nome = 'Impostos S/ Lucro';
			break;

		default:
			$Nome = 'N�o classificado';
			break;
	}


	return $Nome;
}






$Registros = $db->fetch_all_array('SELECT id, cod_conta, nome, clfc_fc, clfc_dre FROM plano_contas');

foreach($Registros as $Registros){

		$NomeFluxoCaixa = FLuxoCaixa($Registros['clfc_fc']);

		$NomeDRE = DRE($Registros['clfc_dre']);

	$Resultado .= '<tr><td> '.$Registros['id'].' </td><td> '.$Registros['cod_conta'].' </td><td> '.utf8_decode($Registros['nome']).' </td><td> '.$Registros['clfc_fc'].' </td><td> '.$NomeFluxoCaixa.' </td><td> '.$Registros['clfc_dre'].' </td><td> '.$NomeDRE.' </td></tr>';

}

$Cabecalho = '<tr><td><b> Id </b></td><td><b> C�digo da conta </b></td><td><b> Nome </b></td><td><b> COD Fluxo de Caixa - </b></td><td><b> Classifica��o Fluxo de Caixa </b></td><td><b> COD DRE - </b></td><td><b> Classifica��o DRE </b></td></tr>';
echo '<table>'.$Cabecalho.$Resultado.'</table>';





/* ============================================ */

?>