<?php
require("modulos/contador/class/Contador.class.php");
$contador = new Contador();
?>
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Remessa Contábil</h2>
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
    
    <!-- =================== Palco =================== -->
           
     <!-- Organiza o layout -->
     <div class="fluid">
 
         <div class="span8">
            
                <input name="mes" id="mes" type="text" class="monthpicker" value="<?php echo date('m/Y')?>" readonly style="width:100px;text-align:center;">
                <a href="javascript://void(0);" title="" class="button basic" onClick="RemessaPesquisar();" style="position:relative;top:10px;width:38px;"><img src="images/icons/dark/magnify.png" alt="" class="icon"></a>
			        <!--
			        &nbsp;&nbsp;&nbsp;&nbsp;
                <select style="width:200px">
      	        <option>Selecione</option>
      	        <option value="enviar">Enviar para contabilidade</option>
      	        <option value="reenviar">Reenviar para contabilidade</option>
                </select>
                -->
                <a href="javascript://void(0);" title="" class="button basic" onClick="RemessaValidarConta();" style="padding:7px">Enviar</a>
                <a href="javascript://void(0);" title="" class="button basic" onClick="RemessaHistorico();" style="padding:7px">Histórico</a>
            
         </div>
         
         <div class="span4">

             <div class="widget" style="margin-top:17px;">

                <div id="conexao_contador" class="formRowB">
      
		        <?php
                $display_btn_convite = 'block';

                $conexao = $db->fetch_assoc("SELECT id, contador_id, conectado, remetente FROM conexao WHERE cliente_id = 0 and conectado = 1");
                
                if($conexao==true){
                    echo $contador->ContadorInfo($conexao['contador_id'], $conexao['id']);
                    $display_btn_convite = 'none';
                }

                echo '
                    <div id="div-btn-convite" align="center" style="border-bottom:1px solid #CCC; margin:10px; vertical-align:central;display:'.$display_btn_convite.'">
                            <a href="#" id="opener-convite-contador"class="wContentButton greenwB tipN" original-title="Convide seu contador para se conectar a sua empresa." style="margin-top:10px;">Convidar contador</a>
                    </div>
                ';
                ?>
      
              </div>

            </div>

        </div>

      </div>

      <div class="fluid">

        <div class="span12">
	
            <div class="widget" style="border-bottom:0px;" id="cf-widget">

              <div class="title"><h6 style="margin-left:40px">Contas Financeiras</h6></div>

              <div id="div-contas-financeiras">
      
                <?php
                echo $contador->cfListar($db,date('m'),date('Y'));
                ?>

              </div>

            </div>

      </div>
       
      <!--
      <div class="widget" style="height:404px;" align="center">
                <div class="title" ><img src="../sistema/images/icons/dark/speech.png" alt="" class="titleIcon"><h6>Conversas</h6>
                <div class="topIcons">
                 <a href="0"class="button blueB opener-conversa" style="margin-top:-8px; margin-right:-8px; padding:-8px;"><span >Nova</span></a>
          </div>	
      </div>         		
        
      <div class="scroll" style="height:364px;">
              <ul class="partners">
                  <li>
                   <a href="1-<?php //echo $_SESSION['cliente_id'];?>" title="" class="opener-conversa">
                      <img src="../sistema/images/user.png" class="floatL bordaRedonda" style="height: 30px; width: 30px;">
                      <div class="pInfo" align="left">
                          <strong>Dave Armstrong</strong>
                          <i>Creative </i>	
                      </div>
                   </a>
                  </li>
              </ul>
          </div>
     </div>          
	    -->
       
    </div> <!-- Fim Fluid -->
     
 
    <!-- ====== Fim do Palco ====== -->
 
    <!-- ====== *** UI Dialogs *** ====== -->

    <?php include("dialog_enviar_convite.php"); ?>

    <?php //include("dialog_conversa.php"); ?>
  
    <!-- ====== *** Fim UI Dialogs *** ====== -->
  
</div>

<form target="_blank" method="post" action="RemessaHistorico" id="formHistorico"> <!-- action="php/MPDF/examples/relatorios.php" -->
	<input type="hidden" name="funcao" value="RemessaHistorico"/>
	<input type="hidden" name="mes" value="" id="historicoMes"/>
	<input type="hidden" name="ano" value="" id="historicoAno"/>
</form>