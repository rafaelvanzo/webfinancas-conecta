<!-- <script> alert(window.innerWidth); </script> -->

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Documentos</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>
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
                      <a href="#">Funcionários</a>
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
<!--
    <div class="wrapper">        	    
        <a href="#" title="" class="button greenB" style="margin: 5px;" id="open-modal-arquivo"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Arquivo</span></a>
    </div>
    -->
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

    <!-- filtro -->
    <div class="fulid">

         <div class="span12">
            
            <input name="mes" id="mes" type="text" class="monthpicker" value="<?php echo date('m/Y')?>" readonly style="width:100px;text-align:center;">
            <a href="javascript://void(0);" title="" class="button basic" id="btn-pesquisar" style="position:relative;top:10px;width:38px;"><img src="images/icons/dark/magnify.png" alt="" class="icon"></a>
            
         </div>

    </div>

    <!-- Dynamic table -->
    <div class="widget">
        <div id="arquivos">
            <table class="display" id="dTableArquivos">
                <thead>
                    <tr>
                        <th class="hide-mobile">Dt. Cadastro</th>
                        <th class="hide-mobile">Nome</th>
                        <th class="hide-mobile">Tipo</th>
                        <th class="hide-mobile">Classificação</th>
                        <th class="hide-mobile">Vencimento</th>
                        <th class="hide-mobile">Visualizado</th>
                        <th class="hide-mobile">Opções</th>
                        <th class="show-mobile">Arquivo</th>
                    </tr>
                </thead>
                <tbody></tbody>
		    </table>
        </div>
    </div>
 
    <!-- ====== Fim do Palco ====== -->
 
    <!-- ====== *** UI Dialogs *** ====== -->

    <?php include("Dialogs.php"); ?>

    <!-- ====== *** Fim UI Dialogs *** ====== -->

</div> 
