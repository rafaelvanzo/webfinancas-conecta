<?php
$bancos_lista;
$query = "select id, nome, codigo
					from bancos 
					where nome LIKE '%".$q."%' or codigo LIKE '%".$q."%'
					order by nome";
$bancos = $db->fetch_all_array($query);
$arr_emite_boleto = array("001","104","756");
foreach($bancos as $banco){
	$emite_boleto = 0;
	if(in_array($banco['codigo'],$arr_emite_boleto)){
		$emite_boleto = 1;
	}
	$bancos_lista .= '{"label":"( '.$banco['codigo'].' ) '.$banco['nome'].'","id":"'.$banco['id'].'","codigo":"'.$banco['codigo'].'","emite_boleto":"'.$emite_boleto.'"},';
}

$bancos_lista = '
	bancos = [
		'.$bancos_lista.'
	];
';

echo '
<script>
/*
===========================================================================================
Função Ativar Checkbox, Radio e Title estilizados
===========================================================================================
*/
function ativarCROT(ref){
	
		if(ref == "t"){
			
	$(".tipN").tipsy({gravity: "n",fade: true});
	$(".tipS").tipsy({gravity: "s",fade: true});
	$(".tipW").tipsy({gravity: "w",fade: true});
	$(".tipE").tipsy({gravity: "e",fade: true});
	
		}else{
			
	$(".tipN").tipsy({gravity: "n",fade: true});
	$(".tipS").tipsy({gravity: "s",fade: true});
	$(".tipW").tipsy({gravity: "w",fade: true});
	$(".tipE").tipsy({gravity: "e",fade: true});
	$("input:checkbox, input:radio, input:file").uniform();
	
		}
}

/*
===========================================================================================
Lista de bancos
===========================================================================================
*/

'.$bancos_lista.'

</script>
<script type="text/javascript" src="modulos/conta/js/funcoes.js"></script>
<script type="text/javascript" src="js/plugins/mask/mascara.js"></script>
';
?>