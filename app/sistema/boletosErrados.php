<?php 
require("php/Database.class.php");

$host = "mysql.webfinancas.com";
$usuario = "webfinancas12";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinancas12";

$db = new Database($host,$usuario,$senha,$db_usuario);



$boletos = $db->fetch_all_array('SELECT id, lancamento_id FROM ctr_plc_lancamentos WHERE centro_resp_id = 51');

    foreach($boletos as $boletos){

        //$lanc = $db->fetch_assoc('select id, dt_vencimento FROM lancamentos WHERE id ='.$boletos['lancamento_id'].' AND dt_vencimento >= "2017-02-28"');
        /*
        if(!($lanc['dt_vencimento'] == ''))
        
            $data = explode('-', $lanc['dt_vencimento']);
        
        $mes = $data['1']; // Mês desejado, pode ser por ser obtido por POST, GET, etc.
        $ano = $data['0']; // Ano atual
        $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano)); // Mágica, plim!
        
        $dt_atualizada = $ano.'-'.$mes.'-'.$ultimo_dia;
        */

        
        
        $lancamento = $db->fetch_assoc('select id, descricao, conta_id, favorecido_id, dt_vencimento, valor FROM lancamentos WHERE id ='.$boletos['lancamento_id'].' ');
        
        
        echo $c.' <b>Descrição:</b> '.$lancamento['descricao'].'<br>';    
        
        $contaFinanceira = $db->fetch_assoc('select descricao FROM contas WHERE id ='.$lancamento['conta_id']);
        
        $favorecido = $db->fetch_assoc('select nome FROM favorecidos WHERE id ='.$lancamento['favorecido_id']);
        echo ' <b>ID:</b>'.$lancamento['id'].'<br>';
        echo ' <b>Favorecido:</b> '.utf8_decode($favorecido['nome']).'<br>';
        echo ' <b>Vencimento:</b> '.$lancamento['dt_vencimento'].'<br>';
        echo ' <b>Valor:</b> '.$lancamento['valor'].'<br>';
        echo ' <b>Conta Financeira:</b> '.$contaFinanceira['descricao'].'<br>';
        echo '<br>---------------------------------<br><br>';
     
}
    
?>