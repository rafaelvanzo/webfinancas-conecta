<?php

//PRIMEIRO FAZER O DROP E DEPOIS A CRIAÇÃO DAS TABELAS

echo 'Correção iniciada '.date('Y-m-d H:i:s').' <br>';

//Script para exclusão das tabelas
$scriptDrop = file_get_contents('script_drop_tabelas.php');

//Script para criação das tabelas
$scriptCriacao = file_get_contents('script_criacao_tabelas.php');

for($i=100;$i<=287;$i++){

    //conecta ao banco de dados do cliente
    //$mysqli = new mysqli("mysql.webfinancas.com", 'webfinanca'.$i, "W2BSISTEMAS", 'webfinanca'.$i);

    //Exclui tabelas
    //$mysqli->multi_query($scriptDrop);

    //Cria tabelas
    //$mysqli->multi_query($scriptCriacao);

    //$mysqli->close();
}

echo 'Correção finalizada '.date('Y-m-d H:i:s').' <br>';

?>