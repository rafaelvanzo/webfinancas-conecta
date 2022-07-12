<div id="dialog-contratar" align="center" style="height:auto; padding:0; display:none;" title="Obrigado!">
<br />
 <img src="images/logo_webfinancas_fundo_branco.png" /> 
	<br />
   <h4> Obrigado! </h4>
 <br />  
A contratação será efetivada após
<br /> 
a confirmação do pagamento.
<br /> <br /> 

<?php
if($_SESSION['cli_acesso_situacao'] == '3'){ 
?>
<a href="javascript://void(0);" class="button redB sair" style="color: #FFF;" onclick="CancelarContratacao();"><span>Sair</span></a> 
<?php 
}else{
?>
<a href="perfilUsuario" class="button blueB" style="color: #FFF;"><span>Web Finanças</span></a>
<?php
}
?>
&nbsp;<a href="javascript://void(0);" class="button greenB" style="color: #FFF;" onClick="boleto();"><span>Download Boleto</span></a>
<br /><br /> 
<!-- <span style="font-size:10px;">* </span> -->
<br />
</div>