<?php

require_once "$_SERVER[DOCUMENT_ROOT]/sistema/php/Database.class.php";

$host = "http://mysql31-farm2.uni5.net";//"mysql.webfinancas.com";
$usuario = "webfinanca418";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinanca418";
$db = new Database($host,$usuario,$senha,$db_usuario);


$lancamentos = $db->fetch_assoc('select * from lancamentos limit 0, 1');
var_dump($lancamentos);
?>