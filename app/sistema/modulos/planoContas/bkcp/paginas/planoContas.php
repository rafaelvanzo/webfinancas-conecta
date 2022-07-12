<?php
require('modulos/planoContas/class/PlanoContas.class.php');
?>
<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Categorias</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php //include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs 
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="javascript://" style="cursor: default;">Geral</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Plano de Contas</a>
                 </li>
            </ul>
	</div>  
</div> Fim Breadcrumbs -->

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />
   
    <!-- Botões -->
        <div class="wrapper">        	    
	        <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-planoContas-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Nova Categoria</span></a>
        </div>

    
 <!--   <div class="line"></div>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
        <!-- Notifications 
        <div class="nNote nWarning hideit" style="display:none;">
            <p></p>
        </div>
        <div class="nNote nInformation hideit" style="display:none;">
            <p></p>
        </div>   
        <div class="nNote nSuccess hideit" style="display:none;">
            <p></p>
        </div>  
        <div class="nNote nFailure hideit" style="display:none;">
            <p></p>
        </div>-->
    		
        <!-- =================== Palco =================== -->

 <!-- Dynamic table -->
        <div class="widget">
            <div id="planoContas">
              
              <?php
              $planoContas = new PlanoContas();
              echo $planoContas->planoContasListar($db);
              ?>
             
            </div>
        </div>
 
 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php require("plano_dialogs.php");?>

  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 