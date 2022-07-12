<?php
require("Database.class.php");
$host = "mysql.webfinancas.com";//"mysql04-farm59.uni5.net";
$usuario = $_SESSION['db_usuario'];
$senha = $_SESSION['db_senha'];
$db_usuario = $_SESSION['db_usuario'];
/*
$usuario = "webfinancas01";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinancas01";
*/
$db = new Database($host,$usuario,$senha,$db_usuario);
?>