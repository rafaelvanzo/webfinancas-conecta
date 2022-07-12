<!-- <script> alert(window.innerWidth); </script> --> 
 
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Empenho</h2>
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
                      <a href="javascript://" style="cursor: default;">Empenho</a>
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
      <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-rcbt-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo recebimento</span></a>
      <a href="#" title="" class="button redB" style="margin: 5px;" id="opener-pgto-incluir"><img src="images/icons/light/postcard.png" alt="" class="icon"/><span>Novo pagamento</span></a>
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
        <table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos">
        <thead>
        <tr style="border-bottom: 1px solid #e7e7e7;">
						<th style="display:none;">Ordem</th>
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
				$query_lancamentos = "
					select dt_vencimento dt_ordem, id, tipo, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento, descricao, valor
					from lancamentos_plnj
					order by dt_ordem
				";

				$array_lancamentos = $db->fetch_all_array($query_lancamentos);
				
				foreach($array_lancamentos as $lancamento){
					if($lancamento[tipo]=="R"){
						$classe_excluir = "recebimentosExcluir";
						$valor = 'R$ '.number_format($lancamento['valor'],2,',','.');
						$title_pgto_recb = 'Recebimento';
						$cor = 'blue';
					}else{
						$classe_excluir = "pagamentosExcluir";
						$valor = 'R$ '.number_format($lancamento['valor'],2,',','.');
						$title_pgto_recb = 'Pagamento';
						$cor = 'red';
					}
					
					// ============ data ============
					$dt_compensar = explode("/", $lancamento['dt_vencimento']);
					$dia = $dt_compensar[0];
					$m = $dt_compensar[1];
					if($m == 01){ $mes = 'Jan';}
										elseif($m == 02){ $mes = 'Fev';}
										elseif($m == 03){ $mes = 'Mar';}
										elseif($m == 04){ $mes = 'Abr';}
										elseif($m == 05){ $mes = 'Mai';}
										elseif($m == 06){ $mes = 'Jun';}
										elseif($m == 07){ $mes = 'Jul';}
										elseif($m == 08){ $mes = 'Ago';}
										elseif($m == 09){ $mes = 'Set';}
										elseif($m == 10){ $mes = 'Out';}
										elseif($m == 11){ $mes = 'Nov';}
										else{ $mes = 'Dez';}
					$ano = substr($dt_compensar[2], -2);
					// ==============================
					
					echo '
						<tr class="gradeA">
							<td style="display:none;">'.$lancamento['dt_ordem'].'</td>
							<td>
									<div class="updates newUpdate">
									
										<div class="uDate tbWF tipS" original-title="Vencimento" align="center"> <span class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
											<span class="lDespesa tbWF" >
												<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
													<span original-title="'.$title_pgto_recb.'" class="tipN '.$cor.'">'.$title_pgto_recb.'</span>
											</span>											
															
										<div class="tbWFoption">										
												<a href="'.$lancamento['id'].'" original-title="Excluir" class="smallButton btTBwf redB tipS '.$classe_excluir.'"><img src="images/icons/light/close.png" width="10"></a>		
												<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS exibir" onClick="lancamentosExibir('.$lancamento['id'].',\''.$lancamento['tipo'].'\')"><img src="images/icons/light/pencil.png" width="10"></a>
										</div>
																																													
										<div class="tbWFvalue '.$cor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
		
									</div>				
							</td>
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
  
  <?php include("lnct_dialogs.php");?>
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div>
