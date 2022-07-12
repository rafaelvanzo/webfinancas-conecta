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
			$Nome = 'Aporte dos Sócios';
			break;

		case '10':
			$Nome = 'Pagamento aos sócios';
			break;

		case '11':
			$Nome = 'Entrada de Tesouraria';
			break;

		case '12':
			$Nome = 'Saída de Tesouraria';
			break;

		default:
			$Nome = 'Não classificado';
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
			$Nome = 'Despesas Variáveis';
			break;

		case '6':
			$Nome = 'Despesas Fixas';
			break;

		case '7':
			$Nome = 'Custos da Produção - CP';
			break;

		case '8':
			$Nome = 'Custos da Mercadoria Vendida - CMV';
			break;

		case '9':
			$Nome = 'Custos do Serviço Prestado - CSP';
			break;

		case '10':
			$Nome = 'Impostos S/ Vendas';
			break;

		case '11':
			$Nome = 'Impostos S/ Lucro';
			break;

		default:
			$Nome = 'Não classificado';
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

$Cabecalho = '<tr><td><b> Id </b></td><td><b> Código da conta </b></td><td><b> Nome </b></td><td><b> COD Fluxo de Caixa - </b></td><td><b> Classificação Fluxo de Caixa </b></td><td><b> COD DRE - </b></td><td><b> Classificação DRE </b></td></tr>';
echo '<table>'.$Cabecalho.$Resultado.'</table>';





/* ============================================ */

?>