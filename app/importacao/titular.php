<?php

require_once 'Database.class.php';

//conecta ao banco principal da w2b
$dbW2b = new Database("mysql.web2business.com.br", 'web2business', "W2BSISTEMAS", 'web2business');

//clientes da lexdata
$clientes = $dbW2b->fetch_all_array('select * from clientes where parceiro_id = 244 and inscricao = "cpf"');

//fecha conexão com w2b
$dbW2b->close();

//conecta ao banco principal do wf
$dbWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

$clientesAfetados = 0;

foreach ($clientes as $cliente){
    
    try{
        $dadosDbCliente = $dbWf->fetch_assoc('select * from clientes_db where cliente_id = '.$cliente['id']);

        //conecta ao banco do cliente
        if(!$dbWfCliente = new Database("mysql.webfinancas.com", $dadosDbCliente['db'], "W2BSISTEMAS", $dadosDbCliente['db']))
            echo $dadosDbCliente['db'];

        //verifica se há apenas uma conta carnê leão
        $qtdContaCarneLeao = $dbWfCliente->numRows('select id from contas where carne_leao = 1');

        if($qtdContaCarneLeao == 1){
            $dbWfCliente->query('update contas set nomeTitular = "'.$cliente['nome'].'", inscricao = "CPF", cpf_cnpj = "'.$cliente['cpf_cnpj'].'" where carne_leao = 1');
            $clientesAfetados ++;
        }
        
        //fecha conexão com o cliente
        $dbWfCliente->close();

    }catch(Exception $e){

        echo 'cliente: '.$cliente['nome'].' <br>';
        echo 'cliente: '.$cliente['cpf_cnpj'].' <br>';
        echo 'cliente: '.$dadosDbCliente['db'].' <br><br>';
    }
    
}

//fecha conexão com wf
$dbWf->close();

echo 'total de clientes: '.count($clientes).' <br>';
echo 'clientes afetados: '.$clientesAfetados;