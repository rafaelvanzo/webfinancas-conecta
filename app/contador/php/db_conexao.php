<?php
require("Database.class.php");

//Conex�o com o Web Finan�as do usu�rio
$host = "mysql.webfinancas.com";
$usuario = $_SESSION['db_usuario'];
$senha = $_SESSION['db_senha'];
$db_usuario = $_SESSION['db_usuario'];
$db = new Database($host,$usuario,$senha,$db_usuario);

//Conex�o com Web 2 Business
$w2b_host_ = "mysql.web2business.com.br";
$w2b_usuario = "web2business";
$w2b_senha = "W2BSISTEMAS";
$w2b_db_usuario = "web2business";
$db_w2b = new Database($w2b_host_,$w2b_usuario,$w2b_senha,$w2b_db_usuario);

//Dados para conex�o com Web Finan�as
$dadosDbWf = array(
        'host' => 'mysql.webfinancas.com',
        'usuario' => 'webfinancas',
        'senha' => 'W2BSISTEMAS',
        'db' => 'webfinancas'
    );
?>