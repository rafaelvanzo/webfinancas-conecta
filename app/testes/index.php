<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/sistema/php/Database.class.php';

//RENOMEAR ARQUIVO
//----------------------------------------------------------------------------------------------
/*
$nomeArquivo = '134_teste2º ç - ã _ú ÀÉ óÕ.pdf';

echo $nomeArquivo.'<br>';

$nomeArquivo = utf8_decode(RemoverAcento($nomeArquivo));

echo $nomeArquivo.'<br>';

//Extensão do arquivo
$ext = substr($nomeArquivo, strrpos($nomeArquivo,'.'));

echo $ext.'<br>';

//Gera novo nome aleatório
$novoNome = bin2hex(openssl_random_pseudo_bytes(16)).$ext;

echo $novoNome.'<br>';

//Caminho dos arquivos
$filePath = $_SERVER['DOCUMENT_ROOT'].'sistema/uploads/cliente_134/';

echo $filePath.'<br>';

//Renomeia arquivo
echo rename($filePath.$nomeArquivo, $filePath.$novoNome).'<br>';
*/
//SCRIPT BASE PARA TESTE
//-----------------------------------------------------------------------------------------------------

/*
require_once $_SERVER['DOCUMENT_ROOT'].'/sistema/php/Database.class.php';

//conecta ao banco de dados principal do Web Finanças
$dbWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

$bancos = $dbWf->fetch_all_array('select * from clientes_db where situacao = 1');

$dbWf->close();

$contador = 0;

$contasDivergentes = array();

foreach($bancos as $banco){
    
    //conecta ao banco de dados do cliente
    $dbCliente = new Database("mysql.webfinancas.com", $banco['db'], $banco['db_senha'], $banco['db']);

    $contas_financeiras = $dbCliente->fetch_all_array('select * from contas');

    foreach($contas_financeiras as $conta){
        
        $lancamentos = $dbCliente->fetch_assoc('select sum(if(tipo="R",valor,0)) receitas, sum(if(tipo="P",valor,0)) despesas, (sum(if(tipo="R",valor,0)) - sum(if(tipo="P",valor,0))) diferenca from lancamentos where compensado = 1 and conta_id = '.$conta['id']);

        $transfEntrada = $dbCliente->fetch_assoc('select sum(valor) valor
            from lancamentos
            where tipo = "T" 
            and conta_id_destino = '.$conta['id'].'
            and compensado = 1;
            ');

        $transfSaida = $dbCliente->fetch_assoc('select sum(valor) valor
            from lancamentos
            where tipo = "T" 
            and conta_id_origem = '.$conta['id'].'
            and compensado = 1;
            ');

        if( ($conta['vl_saldo'] - $conta['limite_credito'] - $conta['vl_credito']) != ($lancamentos['receitas'] - $lancamentos['despesas']) ){
            array_push($contasDivergentes,array('db'=>$banco['db'],'cliente_id'=>$banco['cliente_id'],'conta'=>$conta,'valores'=>array('receitas'=>$lancamentos['receitas']+$transfEntrada['valor'],'despesas'=>$lancamentos['despesas']+$transfSaida['valor'])));
            $contador++;
        }
    }

    $dbCliente->close();
}

echo 'total de clientes com saldo incorreto: '.$contador;

foreach($contasDivergentes as $conta){
    if( $conta['conta']['vl_saldo'] != $conta['valores']['despesas']){
        echo 'db: '.$conta['db'].
        ' <br> cliente_id: '.$conta['cliente_id'].
        ' <br> Saldo: '.$conta['conta']['vl_saldo'].
        ' <br> Diferença: '.($conta['valores']['receitas'] - $conta['valores']['despesas']).' <br><br>';
    }
    
}
*/

//VERIFICA CLIENTES SEM HONORÁRIOS CADASTRADOS
//--------------------------------------------------------------------------------------

//conecta ao banco de dados do cliente
/*
$dbCliente = new Database("mysql.webfinancas.com", "webfinanca485", "W2BSISTEMAS", "webfinanca485");

$conexao = $dbCliente->fetch_all_array('select * from honorarios');
print_r($conexao);
*/
//VERIFICA SE HÁ CONTAS FINANCEIRAS COM SALDO INCORRETO
//--------------------------------------------------------------------------------------

//Documentação float: http://php.net/manual/pt_BR/language.types.float.php
//Documentação para operações com float: http://php.net/manual/pt_BR/ref.bc.php

require_once $_SERVER['DOCUMENT_ROOT'].'/sistema/php/Database.class.php';

//conecta ao banco de dados principal do Web Finanças
$dbWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

//$whereDbId = ' and id = 396';

$bancos = $dbWf->fetch_all_array('select * from clientes_db where situacao = 1 '.$whereDbId);

$dbWf->close();

$contador = 0;

foreach($bancos as $banco){
    
    //conecta ao banco de dados do cliente
    $dbCliente = new Database("mysql.webfinancas.com", $banco['db'], $banco['db_senha'], $banco['db']);

    //contas financeiras
    $contas = $dbCliente->fetch_all_array('select * from contas');
    
    //echo 'qtd_contas: '.count($contas).'<br>';
    
    $arrayContasDivergentes = array();

    foreach($contas as $conta){
    
        $saldo = $dbCliente->fetch_assoc('select sum(vl_saldo) vl_saldo, sum(vl_saldo_inicial) vl_saldo_inicial, sum(limite_credito) limite_credito, sum(vl_credito) vl_credito from contas where id = '.$conta['id']);

        $lancamentos = $dbCliente->fetch_assoc('select sum(if(tipo="R",valor,0)) receitas, sum(if(tipo="P",valor,0)) despesas from lancamentos where compensado = 1 and conta_id = '.$conta['id']);

        $query_trans_entrada = '
			select sum(l.valor) valor
			from lancamentos l
			where l.tipo = "T"
				and l.conta_id_destino = '.$conta['id'].'
				and l.compensado = 1
		';
		$trans_entrada = $dbCliente->fetch_assoc($query_trans_entrada);
        
		$query_trans_saida = '
			select sum(l.valor) valor
			from lancamentos l
			where l.tipo = "T"
				and l.conta_id_origem = '.$conta['id'].'
				and l.compensado = 1
		';
		$trans_saida = $dbCliente->fetch_assoc($query_trans_saida);

        $saldoTotal = $saldo['vl_saldo'];

        $receitas = bcadd($lancamentos['receitas'],$saldo['vl_saldo_inicial'],2);
        $receitas = bcadd($receitas,$trans_entrada['valor'],2);
        $receitas = bcadd($receitas,bcsub($saldo['limite_credito'],$saldo['vl_credito'],2),2);
        $creditoUtilizado = bcsub($saldo['limite_credito'],$saldo['vl_credito'],2);
        
        $despesas = bcadd($lancamentos['despesas'],$trans_saida['valor'],2);
        
        $diferenca = bcsub($receitas,$despesas,2);
        
        $arrayContaDivergente = array();

        if(bccomp($saldoTotal,$diferenca) != 0){
            
            $arrayContaDivergente = array(
                'db'=>$banco['db'],
                'cliente_id'=>$banco['cliente_id'],
                'saldo'=>$saldoTotal,
                'diferenca'=>$diferenca
                );

            array_push($arrayContasDivergentes,$arrayContaDivergente);

            echo  'cliente_id: '.$banco['cliente_id'].' banco_id: '.$banco['db'].' conta_id: '.$conta['id'].' <br>';
            echo 'saldo: '.$saldoTotal.' saldo calculado: '.$diferenca.' diferença: '.bcsub($arrayContaDivergente['saldo'], $arrayContaDivergente['diferenca'], 2).' <br>';
            echo 'cálculo: r '.bcsub($receitas,bcsub($saldo['limite_credito'],$saldo['vl_credito'],2),2).' d '.$despesas.' = '.bcsub(bcsub($receitas,bcsub($saldo['limite_credito'],$saldo['vl_credito'],2),2),$despesas,2);
            echo '<br>';

            if($creditoUtilizado > 0){
                
                $entradas = $lancamentos['receitas'] + $saldo['vl_saldo_inicial'] + $trans_entrada['valor'];
                $saidas = $lancamentos['despesas'] + $trans_saida['valor'];
                $resultado = $entradas - $saidas;
                if($resultado != $creditoUtilizado * -1)
                    echo 'Crédito incorreto <br>';
                echo 'Saldo sem contabilizar o crédito: '.$resultado;
                
            }
            //print_r($conta);
            echo '<br>';
        }

    }

    $dbCliente->close();

    if(count($arrayContasDivergentes)>0){
        $contador++;
        echo '--------------------------------------------------------------------------------------------------<br><br>';
    }
        
    $arrayContaDivergente = array();
}

echo 'total de clientes com saldo incorreto: '.$contador.' <br><br>';


//VERIFICA SE HÁ ALGUM BANCO NÃO UTILIZADO COM SALDO OU CRÉDITO DIFERENTE DE ZERO
//--------------------------------------------------------------------------------------
/*
//conecta ao banco de dados principal do Web Finanças
$bancosLivres = $dbWf->fetch_all_array('select * from clientes_db where situacao = 0');

foreach($bancosLivres as $banco){
    //conecta ao banco de dados livre
    $db = new Database("mysql.webfinancas.com", $banco['db'], $banco['db_senha'], $banco['db']);
    $saldo = $db->fetch_assoc('select sum(vl_saldo) saldo_total, sum(vl_credito) credito_total from contas');
    if($saldo['saldo_total']>0 || $saldo['credito_total'] > 0)
        echo 'banco: '.$banco['db'].' saldo: '.$saldo['saldo_total'];
    $db->close();
}
*/
//conecta ao banco de dados principal do Web Finanças
/*
$dbWf = new Database("mysql.webfinancas.com", 'webfinancas', "W2BSISTEMAS", 'webfinancas');

//conecta ao banco de dados principal da w2b
$dbW2b = new Database("mysql.web2business.com.br", 'web2business', "W2BSISTEMAS", 'web2business');

$parceiros = $dbW2b->fetch_all_array('select * from clientes where parceiro <> 0');

$relatorio = '';

foreach($parceiros as $parceiro){

    $clientes = $dbW2b->fetch_all_array('select id from clientes where parceiro_id = '.$parceiro['id'].' and dt_cadastro <= "2017-09-01"');
    $clientesId = array();
    foreach($clientes as $cliente)
        array_push($clientesId,$cliente['id']);

    $grupos = $dbWf->fetch_assoc('select count(grupo_economico_id) qtd_grupos from (select grupo_economico_id, count(cliente_id) qtd_clientes from usuarios where grupo_economico_id > 0 and cliente_id in ('.join(',', $clientesId).') group by grupo_economico_id having count(cliente_id) > 1) a');

    $relatorio .= 'Parceiro: '.$parceiro['nome'].' <br>';
    $relatorio .= 'Total de clientes: '.count($clientes).' <br>';
    $relatorio .= 'Total de grupos econômicos: '.$grupos['qtd_grupos'].' <br>';
    $relatorio .= 'Query: ('.join(',', $clientesId).') <br><br>';
}

echo $relatorio;
*/

//Remover acentuação
function RemoverAcento($str){

    $arrConv = array(
    "ç" => "c",
    "Ç" => "C",
    "ã" => "a",
    "Ã" => "A",
    "á" => "a",
    "Á" => "A",
    "à" => "a",
    "À" => "A",
    "â" => "a",
    "Â" => "A",
    "é" => "e",
    "É" => "E",
    "ê" => "e",
    "Ê" => "E",
    "è" => "e",
    "È" => "E",
    "í" => "i",
    "Í" => "I",
    "î" => "i",
    "Î" => "I",
    "ì" => "i",
    "Ì" => "I",
    "ó" => "o",
    "Ó" => "O",
    "ô" => "o",
    "Ô" => "O",
    "õ" => "o",
    "Õ" => "O",
    "ò" => "o",
    "Ò" => "O",
    "ú" => "u",
    "Ú" => "U",
    "ù" => "u",
    "Ù" => "U",
    "ü" => "u",
    "Ü" => "U",
    "ñ" => "n",
    "Ñ" => "N",
    "ý" => "y",
    "Ý" => "Y",
    "\"" => "",
    "'" => "",
    "," => "",
    //" " => "",
    "<" => "",
    ">" => "",
    );

    return strtr($str, $arrConv);
}

?>