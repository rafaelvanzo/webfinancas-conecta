<?php
session_start();
//require($_SERVER['DOCUMENT_ROOT']."webfinancas/php/db_conexao.php");
require("../../../php/db_conexao.php");

//$q = strtolower($_GET["term"]);
$tp_lnct = $_GET["tp_lnct"];
$cf_id = $_GET["cf_id"];
if($tp_lnct=='R'){
	$tp_conta_id_transf = 'and conta_id_destino = ';
}else{
	$tp_conta_id_transf = 'and conta_id_origem = ';
}

$q = $_GET["term"];

if (!$q || $q==""){
	$query = mysql_query("
		(select a.id, a.tipo, a.descricao, date_format(a.dt_vencimento, '%d/%m/%Y') dt_vencimento_format, a.dt_vencimento, a.valor, 0 as rcr, 0 as conta_id_origem, b.nome
		from lancamentos a
        join favorecidos b on a.favorecido_id = b.id
		where tipo = '".$tp_lnct."'
			and conta_id = ".$cf_id."
			and compensado = 0)
	
		union 

		(select id, 'T' as tipo, descricao, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, valor, 0 as rcr, conta_id_origem, '' as nome
		from lancamentos
		where tipo = 'T'
			".$tp_conta_id_transf." ".$cf_id."
			and compensado = 0)

		union	

		(select a.id, a.tipo, a.descricao, date_format(a.dt_vencimento, '%d/%m/%Y') dt_vencimento_format, a.dt_vencimento, a.valor, 1 as rcr, 0 as conta_id_origem, b.nome
		from lancamentos_recorrentes a
        join favorecidos b on a.favorecido_id = b.id
		where tipo = '".$tp_lnct."'
			and conta_id = ".$cf_id.")
		
		order by dt_vencimento
	");

}else{

	$query = mysql_query("
		(select a.id, a.tipo, a.descricao, date_format(a.dt_vencimento, '%d/%m/%Y') dt_vencimento_format, a.dt_vencimento, a.valor, 0 as rcr, 0 as conta_id_origem, b.nome
		from lancamentos a
        join favorecidos b on a.favorecido_id = b.id
		where tipo = '".$tp_lnct."'
			and conta_id = ".$cf_id."
			and compensado = 0
			and descricao LIKE '%".$q."%')

		union

		(select id, 'T' as tipo, descricao, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, valor, 0 as rcr, conta_id_origem, '' as nome
		from lancamentos
		where tipo = 'T'
			".$tp_conta_id_transf." ".$cf_id."
			and compensado = 0
			and descricao LIKE '%".$q."%')
			
		union	
					
		(select a.id, a.tipo, a.descricao, date_format(a.dt_vencimento, '%d/%m/%Y') dt_vencimento_format, a.dt_vencimento, a.valor, 1 as rcr, 0 as conta_id_origem, b.nome
		from lancamentos_recorrentes a
        join favorecidos b on a.favorecido_id = b.id
		where tipo = '".$tp_lnct."'
			and conta_id = ".$cf_id."
			and descricao LIKE '%".$q."%')
			
		order by dt_vencimento
	");
}

$items = array();

while($consulta = mysql_fetch_assoc($query)){
	$valor = number_format($consulta['valor'],2,',','.');
	$id = $consulta['id'];
	$rcr = $consulta['rcr'];
	$dt_vencimento = $consulta['dt_vencimento_format'];
	$dscr = $consulta['descricao'];
	$tipo = $consulta['tipo'];
	$conta_id_origem = $consulta['conta_id_origem'];
	$label = $dt_vencimento.' - '.$dscr.' - R$ '.$valor;
    $nome = $consulta['nome'];
	$arr_item = array("label"=>$label,"id"=>$id,"rcr"=>$rcr,"dt_vencimento"=>$dt_vencimento,"valor"=>$valor,"dscr"=>$dscr,"tipo"=>$tipo,"conta_id_origem"=>$conta_id_origem,'nome'=>$nome);
	array_push($items,$arr_item);
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
	foreach ($items as $item) {
		//if (strpos(strtolower($key), $q) !== false) {
            array_push($result, array("id"=>$item['id'], "label"=>$item['label'], "is_rcr"=>$item['rcr'], "dt_vencimento"=>$item['dt_vencimento'], "valor"=>$item['valor'], "dscr"=>$item['dscr'], "tipo"=>$item['tipo'], "conta_id_origem"=>$item['conta_id_origem'], 'nome'=>$item['nome']));
		//}
		if (count($result) > 11)
			break;
	}
	echo array_to_json($result);
}else{
	echo '[]';
}

?>