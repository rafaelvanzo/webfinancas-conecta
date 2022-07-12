<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Favorecidos</h2>
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
                      <a href="#">Geral</a>
	               </li>
                 <li class="current">
                      <a href="#">Favorecidos</a>
                 </li>
            </ul>
	</div>  
</div> Fim Breadcrumbs -->

	  <div class="wrapper">
      <!--
      <span class="line">
      </span>
      -->
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />

    <!-- Botões -->
    <div class="wrapper">        	    
        <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-favorecido-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo favorecido</span></a>
	    <!--<a href="#" title="" class="button basic" style="margin: 5px;" id="opener-fav-import"><img src="images/icons/dark/arrowDown.png" alt="" class="icon"/><span>Importar favorecidos</span></a>-->
	    <a href="javascript://void(0);" title="" class="button greyishB" style="margin: 5px;" id="fav-export" onClick="favExport();"><img src="images/icons/light/arrowRight.png" alt="" class="icon"/><span>Exportar favorecidos</span></a>
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
        <div id="favorecidos">
            <table class="display" id="dTableFavorecidos">
		        <tbody>
		        </tbody>
		    </table>

        </div>
    </div>
 
    <!-- ====== Fim do Palco ====== -->
 
    <!-- ====== *** UI Dialogs *** ====== -->

    <?php include("fav_dialogs.php"); ?>

    <!-- ====== *** Fim UI Dialogs *** ====== -->

    <?php
    echo '<input type="hidden" id="cliente_id" value="'.$_SESSION['cliente_id'].'"/>';
    echo '<input type="hidden" id="usuario_id" value="'.$_SESSION['usuario_id'].'"/>';
    ?>

</div> 

<form target="_self" method="post" action="favExport" id="formFavExport"> <!-- action="php/MPDF/examples/relatorios.php" -->
	<input type="hidden" name="funcao" value="favExport"/>
</form>