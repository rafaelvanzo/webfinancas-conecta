<?php
$centros_lista;
$query = "select id, cod_centro, nome from centro_resp where situacao = 1 and cod_centro > 0 order by cod_centro";
$centros = $db->fetch_all_array($query);
foreach($centros as $centro){
	//verifica a quantidade de níveis subsequentes para cada centro
	$arr_aux = array();
	$niveis = 1;
	$arr_filhos = $db->fetch_all_array("select id from centro_resp where centro_pai_id = ".$centro['id']);
	$hasFilho = count($arr_filhos);
	while($hasFilho){
		++ $niveis;
		foreach($arr_filhos as $filho){
			array_push($arr_aux,$filho['id']);
		}
		$centro_pai_id = join(',',$arr_aux);
		$arr_filhos = $db->fetch_all_array("select id from centro_resp where centro_pai_id in (".$centro_pai_id.")");
		$hasFilho = count($arr_filhos);
		if($hasFilho)
			$arr_aux = array();
	}
	
	$centros_lista .= '{"label":"'.$centro['cod_centro'].' - '.$centro['nome'].'","id":"'.$centro['id'].'","niveis":"'.$niveis.'"},';
}

$centros_lista = '
	centros = [
		'.$centros_lista.'
	];
';

echo '
<!--<script type="text/javascript" src="js/plugins/tables/FixedHeader.min_2.0.6.js"></script>-->
<script type="text/javascript">
'.$centros_lista.'
</script>
<script type="text/javascript" src="modulos/relatorios/js/funcoes.js"></script>
';
?>