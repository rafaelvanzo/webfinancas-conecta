<?php
require("../../php/db_conexao.php");

$username = trim(strtolower($_POST['cod_ref']));
$username = mysql_escape_string($username);

$query = "SELECT cod_ref FROM plano_CONTAS WHERE cod_ref = '$username' LIMIT 1";
$result = $connector->query($query);
$num = mysql_num_rows($result);

echo $num;
mysql_close();
?>