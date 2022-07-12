<?php
/*
require_once 'Database.class.php';
require_once 'Mysql.php';

//credenciais para conectar à API
$mysql = new Mysql('rafaelvanzo@gmail.com' , '6<@+l3>V1GP~{lY');

//array de erros
$erros = array();

//Script para criação das tabelas
$script = file_get_contents('script.php');

//conecta ao banco de dados principal do Web Finanças
$mysqliWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

//Busca maior id de banco de dados no Web Finanças
$maxId = $mysqliWf->fetch_assoc('select max(id) id from clientes_db');
$dbId = $maxId['id'] + 1;

//Loop para criação de tabelas no banco de dados
try{

    $qtdBancos = 1;

    for($i=1;$i<=$qtdBancos;$i++){

        //cria banco de dados
        $novoDb = $mysql->addBanco(array('idDominio'=>'328373', 'senha'=>'W2BSISTEMAS'));
        
        //criar tabelas a partir do banco 288
        //lembrar de inserir o banco 500 na tabela clientes_db
        //conecta ao novo banco de dados
        $mysqli = new mysqli("mysql.webfinancas.com", $novoDb['BancoNome'], "W2BSISTEMAS", $novoDb['BancoNome']);

        //cria tabelas no novo banco de dados
        $mysqli->query('start transaction');
        if(!$mysqli->multi_query($script))
            throw new Exception('1 - Erro ao inserir tabelas no novo banco de dados', 1);
        $mysqli->query('commit');
        $mysqli->close();
                
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

}catch(Exception $e ){

    if($e->getCode()==1)
        $mysqli->query('rollback');
    
    array_push($erros,array('db'=>'webfinancas'.$dbId, 'erro'=>$e->getMessage()));

}

$mysqliWf->close();

//Exibe erros na criação das tabelas para cada banco
if(count($erros)>0)
    foreach($erros as $erro)
        echo 'Db: '.$erro['db'].' <br> Erro: '.$erro['erro'].' <br><br>';


//Exibe retorno da criação do banco de dados
print_r($novoDb);
*/

//CRIA TABELAS NOS BANCOS DE DADOS
//----------------------------------------------------------------------------------------------------------------------------

//require_once 'Database.class.php';
//require_once 'Mysql.php';

////credenciais para conectar à API
//$mysql = new Mysql('rafaelvanzo@gmail.com' , '6<@+l3>V1GP~{lY');

////array de erros
//$erros = array();

////Script para criação das tabelas
//$script = file_get_contents('script.php');

////conecta ao banco de dados principal do Web Finanças
//$mysqliWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

////Busca maior id de banco de dados no Web Finanças
//$dbId = 288;

////Loop para criação de tabelas no banco de dados
//try{

//    $qtdBancos = 213;

//    for($i=1;$i<=$qtdBancos;$i++){

//        //cria banco de dados
//        //$novoDb = $mysql->addBanco(array('idDominio'=>'328373', 'senha'=>'W2BSISTEMAS'));
        
//        //criar tabelas a partir do banco 288
//        //lembrar de inserir o banco 500 na tabela clientes_db
//        //conecta ao novo banco de dados
//        $mysqli = new mysqli("mysql.webfinancas.com", 'webfinanca'.$dbId, "W2BSISTEMAS", 'webfinanca'.$dbId);

//        //cria tabelas no novo banco de dados
//        $mysqli->query('start transaction');
//        if(!$mysqli->multi_query($script))
//            throw new Exception('1 - Erro ao inserir tabelas no novo banco de dados', 1);
//        $mysqli->query('commit');
//        $mysqli->close();
        
//        //Insere bancos de dados disponíveis no Web Finanças
//        /*
//        $db_insert = array(
//            'cliente_id'=>0,
//            'db'=>'webfinanca'.$dbId,
//            'db_senha'=>'W2BSISTEMAS',
//            'situacao' => 0,
//            'contador' => 0
//        );
        
//        if(!$mysqliWf->query_insert('clientes_db', $db_insert))
//            throw new Exception('2 - Erro ao inserir novo banco de dados no Web Finanças', 2);
//            */
//        $dbId++;
//    }

//}catch(Exception $e ){

//    if($e->getCode()==1)
//        $mysqli->query('rollback');
    
//    array_push($erros,array('db'=>'webfinanca'.$dbId, 'erro'=>$e->getMessage()));

//}

//$mysqliWf->close();

////Exibe erros na criação das tabelas para cada banco
//if(count($erros)>0)
//    foreach($erros as $erro)
//        echo 'Db: '.$erro['db'].' <br> Erro: '.$erro['erro'].' <br><br>';


////Exibe retorno da criação do banco de dados
////print_r($novoDb);

?>