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

    <div class="wrapper">
        <div class="fluid">
            <span class="span6 input-autocomplete-container contador-cliente-container" style="margin-bottom:6px;">
                <input style="padding-left: 5px;" type="text" name="cliente_id" id="input-buscar-cliente" value="<?php echo $_SESSION['contador_cliente']['nome']; ?>" class="buscar-cliente input-buscar required" placeholder="Digite para localizar e selecionar o cliente.."/>
                <input type="hidden" name="" id="cliente_id" value="<?php echo (isset($_SESSION['contador_cliente']['id']))? $_SESSION['contador_cliente']['id'] : 0; ?>" />
            </span>
            <span class="span6">
                <a href="#" title="" class="button greenB" style="margin-top: 0;" id="open-modal-arquivo"><img src="../../sistema/images/icons/light/add.png" alt="" class="icon"/><span>Novo Arquivo</span></a>
            </span>
        </div>
    </div>

    <br />

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
        <div id="arquivos">
            <table class="display" id="dTableArquivos">
                <thead>
                    <tr>
                        <th>Dt. Cadastro</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Classificação</th>
                        <th>Vencimento</th>
                        <th>Visualizado</th>
                        <th>Opções</th>
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
