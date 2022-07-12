<?php

if($_GET["p"] == "contadorMensagens"){
    $atualizarListaMsg =' $(document).ready(function () {
        timerListaMensagem(); 
    });';
}


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

'.$atualizarListaMsg.'

</script>
<script type="text/javascript" src="modulos/clientes/js/funcoes.js"></script>
';
?>