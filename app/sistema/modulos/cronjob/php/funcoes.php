<?php
require_once("../../../php/Database.class.php");
require_once("../../lancamento/class/Lancamento.class.php");
require_once("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){

	//PRIME HALL
	//===========================================================================================
	
	case "FaturasGerarPrimeHall":
        require_once("../../lancamento/class/Recebimento.class.php");
		Lancamento::FaturasGerarPrimeHall($_GET['DataInicial']);
    break;
}
?>