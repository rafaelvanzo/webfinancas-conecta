<?php
require("conexao_db.php");


$Registros = $db->fetch_all_array('SELECT id, nome FROM centro_resp');

foreach($Registros as $Registros){

	$Resultado .= '<tr><td>'.$Registros['id'].'</td><td>'.$Registros['nome'].'</td></tr>';

}

$Cabecalho = '<tr><td><b>Id</b></td><td><b>Nome</b></td></tr>';
echo '<table>'.$Cabecalho.utf8_decode($Resultado).'</table>';

?>