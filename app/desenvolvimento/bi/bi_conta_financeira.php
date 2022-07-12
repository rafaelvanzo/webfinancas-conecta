<?php
require("conexao_db.php");


$Registros = $db->fetch_all_array('SELECT id, descricao, banco_id FROM contas');

foreach($Registros as $Registros){

	$Resultado .= '<tr><td>'.$Registros['id'].'</td><td>'.utf8_decode($Registros['descricao']).'</td><td align="center">'.$Registros['banco_id'].'</td></tr>';

}

$Cabecalho = '<tr><td><b>Id</b></td><td><b>Nome da conta</b></td><td><b>Código do banco</b></td></tr>';
echo '<table>'.$Cabecalho.$Resultado.'</table>';

?>