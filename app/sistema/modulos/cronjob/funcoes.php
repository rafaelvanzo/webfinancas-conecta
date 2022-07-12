<?php
require("../../php/Database.class.php");
require("../lancamento/class/Lancamento.class.php");

/*
===========================================================================================
ARRAY PARA TESTE
===========================================================================================
*/
/*
$_REQUEST = array(
	'funcao'=>'recibo',
	'lancamento_id'=>'356',
	'valor_ini'=>'1',
	'conta_id_origem_ini'=>'20',
	'conta_id_destino_ini'=>'21',
	'tipo'=>'T',
	'sab_dom'=>'1',
	'descricao'=>'teste descricao',
	'favorecido_id'=>'125',
	'conta_id_origem'=>'20',
	'conta_id_destino'=>'21',
	'dt_competencia'=>'05/2015',
	'dt_emissao'=>'22/05/2015',
	'dt_vencimento'=>'22/05/2015',
	'valor'=>'1.000,00',
	'observacao'=>''
);
*/

switch($_REQUEST['funcao']){

	/*
	===========================================================================================
	TESTE
	===========================================================================================
	*/

	//case "teste":
		//$lancamento->anexoExcluir($db,419);
	//break;

	/*
	===========================================================================================
	AUTO COMPENSAR
	===========================================================================================
     */
	
	case "lnctAutoCompensar":
		require("../lancamento/class/Recebimento.class.php");
		require("../lancamento/class/Pagamento.class.php");
		require("../lancamento/class/Transferencia.class.php");
		Lancamento::lnctAutoCompensar();
        break;

    /*
	===========================================================================================
	PRIME HALL
	===========================================================================================
    */
	
	case "FaturasGerarPrimeHall":
		Lancamento::FaturasGerarPrimeHall();
    break;
}
?>
