<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Importar Favorecidos</h2>
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
      <a href="favorecidos" title="" class="button greenB" style="margin: 5px;"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Listar favorecidos</span></a>
      <a href="#" title="" class="button greenB" style="margin: 5px;"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Finalizar importação</span></a>
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
            <div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon" /><h6><span class="green"> Favorecidos </span> </h6></div>
            <div id="favorecidos">
              <table cellpadding="0" cellspacing="0" border="0" class="display tblFavorecidos">
              <thead>
              <tr>
              <th>Nome</th>
              <th width="110" align="center">Telefone</th>
              <th>E-mail</th>
              <th width="100">Opções</th>
              </tr>
              </thead>
              <tbody>
              
              <?php
              $array_favorecidos = $db->fetch_all_array("select id, nome, telefone, email from favorecidos order by nome");
              foreach($array_favorecidos as $favorecido){
                echo '
                  <tr class="gradeA">
                  <td>'.$favorecido[nome].'</td>
                  <td>'.$favorecido[telefone].'</td>
                  <td>'.$favorecido[email].'</td>
                  <td class="center">
                    <a href="javascript://void(0);" title="Editar" class="smallButton" style="margin: 5px;" onClick="favorecidosVisualizar('.$favorecido[id].')"><img src="images/icons/dark/pencil.png" alt=""></a>
                    <a href="'.$favorecido[id].'" title="Excluír" class="smallButton favorecidosExcluir" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
                  </tr>
                ';
              }
              ?>
             
              </tbody>
              </table>
            </div>
        </div>
 
 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php include("dialog_favorecidos_incluir.php"); ?>
  
  <?php include("dialog_favorecidos_editar.php"); ?>
 
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 