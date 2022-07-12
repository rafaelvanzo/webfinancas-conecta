<?php
session_start();
//require($_SERVER['DOCUMENT_ROOT']."webfinancas/php/db_conexao.php");
require("db_conexao.php");

$q = $_GET["term"];
$clienteId = $_SESSION['contador_cliente']['id'];

//Conexão no banco do Web Finanças
$dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
//Conexão com o db do cliente
$dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$clienteId);
$dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
$dbWf->close();

//start: monta ordenação do plano de contas
$maiorNivel = $dbCliente->fetch_assoc('select max(nivel) nivel from plano_contas');
$maiorNivel = $maiorNivel['nivel'];

$ordem = '';
$orderBy = '';

if($maiorNivel>1){
    
    $arrayOrderBy = array();
    $arrayOrdem = array();

    for($i=2;$i<$maiorNivel;$i++){
        array_push($arrayOrderBy,'ordem'.$i);
        array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_conta,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
    }

    array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_conta,".",-1) as decimal),0) as ordem'.$i);
    array_push($arrayOrderBy,'ordem'.$i);

    $ordem = ','.join(',',$arrayOrdem);
    $orderBy = ','.join(',',$arrayOrderBy);
}
//end: monta ordenação do plano de contas

if (!$q || $q==""){
	$query = mysql_query("select id, cod_conta, nome, tp_conta, dedutivel, cast(substring_index(cod_conta,'.',1) as decimal) as ordem1".$ordem."
											from plano_contas 
											where situacao = 0 
                                                and cod_conta > 0
												order by ordem1".$orderBy);
}else{
	$query = mysql_query("select id, cod_conta, nome, tp_conta, dedutivel, cast(substring_index(cod_conta,'.',1) as decimal) as ordem1".$ordem."
											from plano_contas 
											where situacao = 0 
												and cod_conta LIKE '%".$q."%' or nome LIKE '%".$q."%'
                                                having cod_conta > 0
												order by ordem1".$orderBy)or die(mysql_error());
}

while($consulta = mysql_fetch_assoc($query)){
	$key = $consulta['cod_conta'].' - '.$consulta['nome'];
	$value = $consulta['id'];
	$items[$key] = array("value"=>$value,"tp_conta"=>$consulta['tp_conta'],"dedutivel"=>$consulta['dedutivel']);
}

$dbCliente->close();

/*"Great Bittern"=>"Botaurus stellaris",*/


function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = "\"".addslashes($key)."\"";

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "\"".addslashes($value)."\"";
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}

if(count($items)>0){
	$result = array();
	foreach ($items as $key=>$value) {
		//if (strpos(strtolower($key), $q) !== false) {
            array_push($result, array("id"=>$value['value'], "label"=>$key, "value" => strip_tags($key), "tp_ctr_plc" => $value['tp_conta'], "dedutivel" => $value['dedutivel']));
		//}
		//if (count($result) > 11)
			//break;
	}
	echo array_to_json($result);
}else{
	echo '[]';
}

?>