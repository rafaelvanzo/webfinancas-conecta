<?php
require("conexao_db.php");


$Registros = $db->fetch_all_array('SELECT id, nome, email, bairro, cidade FROM favorecidos');

foreach($Registros as $Registros){

	$Resultado .= '<tr><td>'.$Registros['id'].'</td><td>'.$Registros['nome'].'</td><td>'.$Registros['email'].'</td><td>'.$Registros['bairro'].'</td><td>'.$Registros['cidade'].'</td></tr>';

}

$Cabecalho = '<tr><td><b>Id</b></td><td><b>Nome</b></td><td><b>Email</b></td><td><b>Bairro</b></td><td><b>cidade</b></td></tr>';
echo '<table>'.$Cabecalho.utf8_decode($Resultado).'</table>';

?>