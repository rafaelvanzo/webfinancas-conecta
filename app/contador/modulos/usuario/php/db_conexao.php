<?php
/*
$usuario = $_SESSION[db_usuario];
$senha = $_SESSION[db_senha];
$db_usuario = $_SESSION[db_usuario];
*/
require("../../../php/Database.class.php");
$host = "mysql.web2business.com.br";
$usuario = "web2business04";
$senha = "W2BSISTEMAS";
$db_usuario = "web2business04";
$db = new Database($host,$usuario,$senha,$db_usuario);
?>