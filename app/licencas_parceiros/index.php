<?php

//CONECTA
//------------------------------------------------------------------------------------------------------------------------------------------------------

echo 'Conecta Contabilidade <br>------------------------------------------------------<br>';

//TOTAL DE CLIENTES CONECTA
//------------------------------------------------------------------------------------------------------------------------------------------------------

$parceiroId = 342;

$dtLimite = '2017-10-01';

$mysqlConnectionW2b = mysql_connect('mysql.web2business.com.br','web2business','W2BSISTEMAS');

$dbW2b = mysql_select_db('web2business',$mysqlConnectionW2b);

$query = mysql_query('select * from clientes where parceiro_id = '.$parceiroId.' and dt_cadastro < "'.$dtLimite.'" ',$mysqlConnectionW2b);

$arrayClientes = array();

while($cliente = mysql_fetch_assoc($query)){
    array_push($arrayClientes,$cliente['id']);
    //echo $cliente['id'].', <br>';
}

mysql_close($mysqlConnectionW2b);

//echo join(',', $arrayClientes).' <br>';

echo "Total de clientes: ".count($arrayClientes)." <br>";

//GRUPOS ECONÔMICOS CONECTA
//------------------------------------------------------------------------------------------------------------------------------------------------------

$mysqlConnectionWf = mysql_connect('mysql.webfinancas.com','webfinancas','W2BSISTEMAS');

$dbWf = mysql_select_db('webfinancas',$mysqlConnectionWf);

$queryGrupo = mysql_query('select count(id) qtd, grupo_economico_id
                    from usuarios
                    where cliente_id in ('.join(',', $arrayClientes).')
                    and grupo_economico_id <> 0
                    group by grupo_economico_id
                    having count(id) > 1',$mysqlConnectionWf);

$arrayGrupos = array();

while($grupo = mysql_fetch_assoc($queryGrupo)){
    array_push($arrayGrupos,$grupo);
    //echo $grupo['grupo_economico_id'].' <br>';
}

echo 'Total de grupos com mais de uma licença: '.count($arrayGrupos).' <br>';

mysql_close($mysqlConnectionWf);

echo 'Total de licenças a faturar: '.(count($arrayClientes) - count($arrayGrupos)).' <br><br>';

//LEXDATA
//------------------------------------------------------------------------------------------------------------------------------------------------------

echo 'Lexdata <br>------------------------------------------------------<br>';

//TOTAL DE CLIENTES 
//------------------------------------------------------------------------------------------------------------------------------------------------------

$parceiroId = 244;

$mysqlConnectionW2b = mysql_connect('mysql.web2business.com.br','web2business','W2BSISTEMAS');

$dbW2b = mysql_select_db('web2business',$mysqlConnectionW2b);

$query = mysql_query('select * from clientes where parceiro_id = '.$parceiroId,$mysqlConnectionW2b);

$arrayClientes = array();

while($cliente = mysql_fetch_assoc($query)){
    array_push($arrayClientes,$cliente['id']);
    //echo $cliente['id'].', <br>';
}

mysql_close($mysqlConnectionW2b);

//echo join(',', $arrayClientes).' <br>';

echo "Total de clientes: ".count($arrayClientes)." <br>";

?>