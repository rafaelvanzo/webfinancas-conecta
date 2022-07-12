<?php
/*
//require_once('../Controllers/HonorarioLexdataController.php');

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
define('ROOT_MODULOS',$_SERVER['DOCUMENT_ROOT'].'/sistema/modulos/');

//require_once(ROOT.'lexdata/Util/Mascara.php');
//require_once(ROOT_MODULOS.'favorecido/class/Favorecido.class.php');
//require_once(ROOT_MODULOS.'lancamento/class/Lancamento.class.php');
//require_once(ROOT_MODULOS.'lancamento/class/Recebimento.class.php');
//require_once(ROOT_MODULOS.'lancamento/class/Pagamento.class.php');
//require_once(ROOT_MODULOS.'lancamento/class/Transferencia.class.php');
//require_once(ROOT.'lexdata/Controllers/RemessaLexdataController.php');
//require_once(ROOT."sistema/php/db_conexao.php");
//require_once(ROOT.'lexdata/Database/DbMSSql.php');

//Conexão com o db do SUFOL
$conexao = "host=177.53.174.212 port=15780 dbname=dbsufol user=wf password=webfinancas";
$dbSufol = pg_connect($conexao);

//Select no db SUFOL
$dtIni = '2017-11-01';
$dtFim = '2017-11-30';

$query = pg_query($dbSufol, "select nomesacado, cnpjcpf, nossonumero, mes, ano, valorapagar, datavencimento, cnpjcpf, nomesacado, enderecosacado, bairrosacado, cidadesacado, ufsacado, cepsacado 
from view_boletos 
where datavencimento >= '".$dtIni."' 
and datavencimento <= '".$dtFim."'
and valorpago is null 
order by nomesacado");

$honorariosLexdata = pg_fetch_all($query);

//["nossonumero"]=> string(7) "3026534" ["datavencimento"]=> string(19) "2017-10-30 00:00:00" ["valorapagar"]=> string(6) "331.17" ["datapagamento"]=> NULL ["valorpago"]=> NULL ["mes"]=> string(2) "10" ["ano"]=> string(4) "2017" ["diavencimento"]=> string(2) "30" ["agencia"]=> string(6) "0485-5" ["cnpj"]=> string(14) "11262007000106" ["nomecedente"]=> string(29) "LEXDATA CONTABILIDADE LTDA ME" ["mensagemcobranca"]=> string(27) "EVITE O PAGAMENTO COM JUROS" ["nomesacado"]=> string(20) "Augusto Altoe Puppin" ["cnpjcpf"]=> string(11) "12753725705" ["enderecosacado"]=> string(18) "Rua Santa Rosa, 26" ["bairrosacado"]=> string(6) "Glória" ["cidadesacado"]=> string(10) "Vila Velha" ["ufsacado"]=> string(2) "ES" ["cepsacado"]=> string(8) "29122290" ["login"]=> string(11) "agusto10796" ["senha"]=> string(9) "041810796" ["tpservico"]=> string(13) "CONTABILIDADE" ["codcontrato"]=> string(5) "10796" }

foreach($honorariosLexdata as $honorario){
    echo $honorario['nomesacado'].' '.$honorario['datavencimento'].' '.$honorario['cnpjcpf'].' '.$honorario['nossonumero'].'<br><br>';
}
*/
?>