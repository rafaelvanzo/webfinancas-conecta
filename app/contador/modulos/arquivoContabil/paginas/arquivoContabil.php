 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Arquivo Contábil</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

	     </div>
    </div>
    <!-- Fim título -->  
      
	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />

     
    <!-- main content wrapper -->
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
        </div>
        -->

         <!-- Organiza o layout -->   
         <div class="fluid">  
 
 	        <div class="span12">
  
                <div class="widget">
  
                    <!------------------------------------------------------------------------------
                    Clientes conectados
                    -->

                    <div id="tbl-clientes">

                    <!-- <div class="title" ><img src="../sistema/images/icons/dark/adminUser.png" alt="" class="titleIcon" /><h6>Clientes</h6></div> -->

      	            <table cellpadding="0" cellspacing="0" border="0" class="display dTable dTableClientes">
                    <thead>
                    <tr style="border-bottom: 1px solid #e7e7e7;">
						            <th> 
								            <table width="100%"><tr>
									            <td>Nome</td>
									            <td width="60">Opções</td>
								            </td></tr></table>
						            </th> 
					            </tr>
                    </thead>
                    <tbody id="clientes">

                    <?php   
                    require('modulos/arquivoContabil/class/ArquivoContabil.class.php');
                    $clientes = new ArquivoContabil();
                    $listar_clientes = $clientes->listarClientes($db);
                    echo $listar_clientes;
                    ?>

                    </tbody>
                    </table>

                    </div>
     
              </div>
        
            </div>
      
          </div>
     
      </div> <!-- Fim Wrapper -->
     
<!-- Convite Contador -->
<div id="dialog-convite-contador" style="height: auto; padding:0; text-align: center; display:none;" title="Convidar cliente">
  <form id="formConviteContador" action="#" class="dialog">
    <input type="hidden" name="funcao" value="conviteContador" />
    <div class="fluid">

       <div class="formRow">
          <span class="span12">
             <label>Digite o e-mail do seu cliente:</label>
             <input type="text" name="destinatario_email" value="" placeholder="E-mail do cliente" required/>
             <input type="hidden" name="remetente_id" value="<?php echo $_SESSION['cliente_id']; ?>"/>
          </span>
       </div>

    </div>  <!-- fluid --> 
  </form>
</div><!-- Fim dialog --> 



 <!-- ====== *** UI Dialogs *** ====== -->

 <?php include("dialog_bloqueio.php"); ?>
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
