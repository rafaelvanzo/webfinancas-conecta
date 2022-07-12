<?php
session_start();
//require($_SERVER['DOCUMENT_ROOT']."webfinancas/php/db_conexao.php");
require("../../../php/db_conexao.php");

$q = strtolower($_GET["term"]);
if (!$q) return;
$query = mysql_query("select id, cod_centro, nome, tp_centro
										from centro_resp 
										where situacao = 1
											and cod_centro LIKE '%".$q."%' or nome LIKE '%".$q."%'
											having tp_centro = 1
											order by cod_centro
									 ")or die(mysql_error());


while($consulta = mysql_fetch_assoc($query)){
	$key = $consulta['cod_centro'].' - '.$consulta['nome'];
	$value = $consulta['id'];
	$items[$key] = $value;
}

$db->close();

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
		if (strpos(strtolower($key), $q) !== false) {
			array_push($result, array("id"=>$value, "label"=>$key, "value" => strip_tags($key)));
		}
		if (count($result) > 11)
			break;
	}
	echo array_to_json($result);
}else{
	echo '[]';
}

?>