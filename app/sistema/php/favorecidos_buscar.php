<?php
session_start();
//require($_SERVER['DOCUMENT_ROOT']."webfinancas/php/db_conexao.php");
require("db_conexao.php");

//$q = strtolower($_GET["term"]);
//if (!$q) return;

$q = $_GET["term"];

if (!$q || $q==""){
	$query = mysql_query("select id, nome, cpf_cnpj, cliente_ctr_id, cliente_plc_id, fornecedor_ctr_id, fornecedor_plc_id from favorecidos order by nome");
}else{
	$query = mysql_query("select id, nome, cpf_cnpj, cliente_ctr_id, cliente_plc_id, fornecedor_ctr_id, fornecedor_plc_id from favorecidos where nome LIKE '%".$q."%' OR cpf_cnpj LIKE '%".$q."%' order by nome")or die(mysql_error());
}

$items = array();
while($consulta = mysql_fetch_assoc($query)){
	//$key = $consulta['nome'];
	//$value = $consulta['id'];
	//$items[$key] = $value;
    array_push($items, $consulta);
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
	$achou = false;
	foreach ($items as $item) {//foreach ($items as $key=>$value) {
		//if ( (strpos(strtolower($key), $q) !== false) ) {
        array_push($result, array("id"=>$item['id'], "label"=>$item['nome'].' ( '.$item['cpf_cnpj'].' )', "value" => strip_tags($item['nome']), "cliente_ctr_id" => $item['cliente_ctr_id'], "fornecedor_ctr_id" => $item['fornecedor_ctr_id'], "cliente_plc_id" => $item['cliente_plc_id'], "fornecedor_plc_id" => $item['fornecedor_plc_id']));
		//}
		$keyL = strtolower($item['nome']);
		$qL = strtolower($q);
		if( $keyL == $qL )
			$achou=true;
		//if (count($result) > 11)
			//break;
	}
	if($q!="" && !$achou)
		array_push($result, array("id"=>"add", "label"=>strip_tags($q)." (ADICIONAR)", "value" => strip_tags($q)));
	echo json_encode($result);//array_to_json($result);
}else{
	$result = array();
	array_push($result, array("id"=>"add", "label"=>strip_tags($q)." (ADICIONAR)", "value" => strip_tags($q)));
	echo json_encode($result);//array_to_json($result);
	//echo '[]';
}

?>