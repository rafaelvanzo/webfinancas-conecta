<?php

//CRIAR BANCOS DE DADOS (A API DE CRIAÇÃO NÃO ESTÁ FUNCIONANDO. É NECESSÁRIO FAZER O ESQUEMA DO POST COM A SESSÃO DO NAVEGADOR ABERTA.)
//----------------------------------------------------------------------------------------------------------------------------

//require_once 'Mysql.php';

////credenciais para conectar à API
//if(!$mysql = new Mysql('rafaelvanzo@gmail.com' , '6<@+l3>V1GP~{lY'))
//    echo 'não foi possível realizar a conexão';

////array de erros
//$erros = array();

////Loop para criação de tabelas no banco de dados
//$qtdBancos = 1;

//for($i=1;$i<=$qtdBancos;$i++)
//{
//    //cria banco de dados
//    $novoDb = $mysql->addBanco(array('idDominio'=>'328373', 'senha'=>'W2BSISTEMAS'));
//}

//DISPONIBILIZA BANCOS DE DADOS PARA NOVOS CLIENTES
//-----------------------------------------------------------------------------------------
/*
require_once 'Database.class.php';

//array de erros
$erros = array();

//conecta ao banco de dados principal do Web Finanças
$mysqliWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

//Busca maior id + 1 do banco de dados no Web Finanças
$dbId = 601;

//Loop para criação de tabelas no banco de dados
try{

    $qtdBancos = 16;

    for($i=1;$i<=$qtdBancos;$i++){

        //Insere bancos de dados disponíveis no Web Finanças
        $db_insert = array(
            'cliente_id'=>0,
            'db'=>'webfinanca'.$dbId,
            'db_senha'=>'W2BSISTEMAS',
            'situacao' => 0,
            'contador' => 0
        );
        
        if(!$mysqliWf->query_insert('clientes_db', $db_insert))
            throw new Exception('2 - Erro ao inserir novo banco de dados no Web Finanças', 2);
        
        $dbId++;
    }

}
catch(Exception $e ){

    if($e->getCode()==1)
        $mysqliWf->query('rollback');
    
    array_push($erros,array('db'=>'webfinanca'.$dbId, 'erro'=>$e->getMessage()));

}

$mysqliWf->close();

//Exibe erros na criação das tabelas para cada banco
if(count($erros)>0)
    foreach($erros as $erro)
        echo 'Db: '.$erro['db'].' <br> Erro: '.$erro['erro'].' <br><br>';
*/

//CRIAR TABELAS NOS BANCOS DE DADOS
//-----------------------------------------------------------------------------------------

/*
require_once 'Database.class.php';

//array de erros
$erros = array();

//Script para criação das tabelas
$script = file_get_contents('script.php');

//Busca maior id de banco de dados no Web Finanças
$dbId = 616;

//Loop para criação de tabelas no banco de dados
try{

    $qtdBancos = 1;

    for($i=1;$i<=$qtdBancos;$i++){

        //cria tabelas no novo banco de dados
        $mysqli = new mysqli("mysql.webfinancas.com", 'webfinanca'.$dbId, "W2BSISTEMAS", 'webfinanca'.$dbId);
        $mysqli->query('start transaction');
        if(!$mysqli->multi_query($script))
            throw new Exception('1 - Erro ao inserir tabelas no novo banco de dados', 1);
        $mysqli->query('commit');
        $mysqli->close();

        $dbId++;
    }

}catch(Exception $e ){

    if($e->getCode()==1)
        $mysqli->query('rollback');

    array_push($erros,array('db'=>'webfinanca'.$dbId, 'erro'=>$e->getMessage()));
}

//Exibe erros na criação das tabelas para cada banco
if(count($erros)>0)
    foreach($erros as $erro)
        echo 'Db: '.$erro['db'].' <br> Erro: '.$erro['erro'].' <br><br>';
*/        

//CONSULTAR BANCOS DE DADOS EXISTENTES NA KINGHOST
//-----------------------------------------------------------------------------------------

/*
require_once 'Mysql.php';

//credenciais para conectar à API
$mysql = new Mysql('rafaelvanzo@gmail.com' , '6<@+l3>V1GP~{lY');

$bancos = $mysql->getBancos('328373');

$bancos = $bancos['body'];

foreach($bancos as $banco)
    echo 'banco: '.$banco['Banco'].' <br>';

    */

//ADICIONAR ACESSO EXTERNO AO BANCO DE DADOS
//-----------------------------------------------------------------------------------------
/*
require_once 'Mysql.php';

//credenciais para conectar à API
$mysql = new Mysql('rafaelvanzo@gmail.com' , '6<@+l3>V1GP~{lY');
$requisicao = $mysql->setIpExterno(array('idDominio'=>'328373','nomeBanco'=>'webfinanca100','host'=>'54.207.18.189'));
print_r($requisicao);
//echo $requisicao;
*/
?>