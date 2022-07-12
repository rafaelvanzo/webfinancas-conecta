<?php
/*
echo 'Correção iniciada '.date('Y-m-d H:i:s').' <br>';

require_once 'Database.class.php';

//conecta ao banco principal do web finanças
$db = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

//Separa clientes em grupos
$novosGrupos = array();

$clientesSemGrupo = array();

$gruposEconomicos = $db->fetch_all_array('select * from grupos_economicos');

foreach($gruposEconomicos as $grupoEconomico){
    
    $grupointegrantes = $db->fetch_all_array('select cliente_id from grupos_economicos_integrantes where grupo_id = '.$grupoEconomico['id']);

    if(count($grupointegrantes)>1)
        array_push($novosGrupos, $grupointegrantes);
    else
        array_push($clientesSemGrupo, $grupointegrantes[0]['cliente_id']);
}

//Limpa tabela de grupos e integrantes
$db->query('truncate table grupos_economicos');
$db->query('truncate table grupos_economicos_integrantes');

//Criar novos grupos
$grupoEconomicoQtd = 1;

foreach($novosGrupos as $grupo){

    //inserir novo grupo
    $array_grupo_economico = array(
            'nome' => utf8_encode('Grupo Econômico '.$grupoEconomicoQtd),
            'dt_cadastro' => date('Y-m-d H:i:s')
        );
    $db->query_insert('grupos_economicos',$array_grupo_economico);

    $grupoEconomicoId = mysql_insert_id($db->link_id);

    foreach($grupo as $cliente){

        //inserir cliente no grupo
        $array_grupo_economico_integrante = array(
                'grupo_id' => $grupoEconomicoId,
                'cliente_id' => $cliente['cliente_id'],
                'dt_cadastro' => date('Y-m-d H:i:s')
            );
        $db->query_insert('grupos_economicos_integrantes',$array_grupo_economico_integrante);

        //atualiza grupo_id da tabela usuário
        $db->query('update usuarios set grupo_economico_id = '.$grupoEconomicoId.' where cliente_id = '.$cliente['cliente_id']);
        
    }

    $grupoEconomicoQtd++;
}

//Zera grupo_economico_id de clientes que não possuem grupo
foreach($clientesSemGrupo as $clienteId){
    $db->query('update usuarios set grupo_economico_id = 0 where cliente_id = '.$clienteId);
}

$db->close();

echo 'Correção finalizada '.date('Y-m-d H:i:s').' <br>';
*/
?>