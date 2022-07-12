<?php
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
</script>
<script type="text/javascript" src="modulos/planejamento/js/funcoes_orct.js"></script>
<script type="text/javascript" src="js/plugins/mask/mascara.js"></script>
';
?>