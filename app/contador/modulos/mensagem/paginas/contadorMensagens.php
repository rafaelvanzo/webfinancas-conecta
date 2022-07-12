<?php
require("modulos/mensagem/class/ContadorMensagens.class.php");
$contadorMensagens = new ContadorMensagens();
?>
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Solicitações</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

	     </div>
    </div>    
    <!-- Fim título -->  
 
	<div class="wrapper">
      <!--
      <span class="line">
      </span>
      -->
      <div class="divider">
      	<span></span>
      </div>
    </div>
     
<!-- main content wrapper -->
<div class="wrapper">
         
    <br />

	<!--<a href="#" title="" class="button blueB" style="margin: 5px;" id="opener-add-mensagem"><img src="../../sistema/images/icons/light/dialog.png" alt="" class="icon"><span>Nova solicitação</span></a>-->
     
    <!-- =================== Palco =================== -->
           
     <!-- Organiza o layout -->


      <div class="fluid">

        <div class="span12">
	
			<!-- Dynamic table -->
  <div class="widget">
     <!-- <div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon" /><h6><span class="blue"> Lançamentos </span> </h6></div> -->
      <div id="mensagens">

		<table class="display dTableMensagens" id="dTableMsg">
		    <tbody>
		    </tbody>
		</table>

      </div>
  </div>


              </div>

            </div>

      </div>
       

       
    </div> <!-- Fim Fluid -->
     
 
    <!-- ====== Fim do Palco ====== -->
 
    <!-- ====== *** UI Dialogs *** ====== -->

    <?php include("dialog_add_mensagens.php"); ?>

    <?php include("dialog_visualizar_mensagens.php"); ?>
  
    <!-- ====== *** Fim UI Dialogs *** ====== -->
  
</div>

<form target="_blank" method="post" action="RemessaHistorico" id="formHistorico"> <!-- action="php/MPDF/examples/relatorios.php" -->
	<input type="hidden" name="funcao" value="RemessaHistorico"/>
	<input type="hidden" name="mes" value="" id="historicoMes"/>
	<input type="hidden" name="ano" value="" id="historicoAno"/>
</form>

