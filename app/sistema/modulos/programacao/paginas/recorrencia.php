<?php
require("modulos/programacao/class/Recorrencia.class.php");
?>
<!-- <script> alert(window.innerWidth); </script> --> 
 
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Recorrência</h2>
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
                      <a href="javascript://" style="cursor: default;">Recebimentos Recorrentes</a>
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
      		<a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-rcbt-rcr-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Recebimento Recorrente</span></a>
					<a href="#" title="" class="button redB" style="margin: 5px;" id="opener-pgto-rcr-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Pagamento Recorrente</span></a>
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
        <div id="lancamentos">
            
            <?php
            //$recorrencia = new Recorrencia();
            //echo $recorrencia->lancamentosListar($db);
            ?>

            <table class="display" id="dTableLnct">
		        <tbody>
		        </tbody>
		    </table>

        </div>
    </div>
 
 <!-- ====== Fim do Palco ====== 
 <tr class="gradeA">
              <td><b>'.$frequencia.' / '.$dia_vencimento.'</b></td>
              <td>'.$lancamento[descricao].'
									<font class="subTexto blue"><br> Próximo Vencimento:<b> '.$lancamento[dt_prox_venc].'</b> </font>
							</td>
              <td><font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font></td>
              <td class="center">
                <a href="javascript://void(0);" title="Editar" class="smallButton" style="margin: 5px;" onClick="lancamentosExibir('.$lancamento[id].')"><img src="images/icons/dark/pencil.png" alt=""></a>
                <a href="'.$lancamento[id].'" title="Excluír" class="smallButton recebimentosExcluir" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
              </td>
            </tr>-->
  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php include("lnct_dialogs.php"); ?>
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 