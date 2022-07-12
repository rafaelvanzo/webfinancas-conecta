<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Contas Financeiras</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php //include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>

   <!-- Breadcrumbs -->
   <!--
    <br />

<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="javascript://" style="cursor: default;">Geral</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Contas Financeiras</a>
                 </li>
            </ul>
	</div>  
</div> 
-->
<!-- Fim Breadcrumbs -->


    <br />
   
    <!-- Botões -->
        <div class="wrapper">        	    
	        <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-conta-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Nova conta</span></a>
          <a href="conciliacao" title="" class="button basic" style="margin: 5px;"><img src="images/icons/dark/transfer.png" alt="" class="icon"/><span>Conciliação</span></a>
             <a href="arquivosRemessa" title="" class="button greyishB" style="margin: 5px;"><img src="images/icons/light/transfer.png" alt="" class="icon"/><span>Remessa</span></a>
          <!--<a href="importarLancamentos" title="" class="button dblueB" style="margin:5px;"><img src="images/icons/light/arrowDown.png" alt="" class="icon"/><span>Importação</span></a>-->
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
            <div id="contas">
              <table cellpadding="0" cellspacing="0" border="0" class="display tblContas">
              <thead>
              <tr style="border-bottom: 1px solid #e7e7e7;">
                <th> 
                    <table width="100%"><tr>
                      <td>Descrição</td>
                      <td width="60">Opções</td>
                    </td></tr></table>
                </th> 
              </tr>
              </thead>
              <tbody>
              
              <?php
              $array_contas = $db->fetch_all_array("select id, banco_id, numero, vl_saldo, descricao
                                                    from contas
                                                    order by descricao");
              foreach($array_contas as $conta){ 
							$id_banco = $conta['banco_id'];
							$banco = $db->fetch_assoc("select * from bancos where id = ".$id_banco);	
							if(!empty($banco[logo])){ $logo_banco = $banco[logo];  
							}else{ $logo_banco = 'bank.png'; }
							if(empty($banco[nome])){ $instituicaoFinanceira = 'Livro de Caixa'; }else{ $instituicaoFinanceira = '(<b>'.$banco[codigo].'</b>) '.$banco[nome]; }	
                echo '
								<tr class="gradeA" id="row'.$conta['id'].'">
								<td class="updates newUpdate">
												
										<div class="uDate tbWF" align="center" style="padding-right:8px; padding-bottom: 5px; margin-right:-8px; "> <img src="images/bancos/'.$logo_banco.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;"></div>
											<span class="lDespesa tbWF" >
												<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$conta[descricao].'</strong></a>
													<span original-title="Instituição Financeira" class="tipN">'.$instituicaoFinanceira.'</span>
											</span>											
															
										<div class="tbWFoption">										
												<a href="'.$conta[id].'" original-title="Excluir" class="smallButton btTBwf redB tipS contasExcluir"><img src="images/icons/light/close.png" width="10"></a>		
												<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS"  onClick="contasVisualizar('.$conta[id].')"><img src="images/icons/light/pencil.png" width="10"></a>											
											</div>
																																													
										<div class="tbWFvalue tipS" original-title="Saldo atual">R$ '.number_format($conta[vl_saldo],2,',','.').' </div>
				
							</td> 
						  ';
              }
              ?>
             
              </tbody>
              </table>
            </div>
        </div>
 
 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php include("conta_dialogs.php");?>

 
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div>

<div id="dados"></div>
