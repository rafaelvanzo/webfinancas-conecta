	<!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Importar Lançamentos</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php //include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs -->
   <!--
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="javascript://" style="cursor: default;">Geral</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Conciliação</a>
                 </li>
            </ul>
	</div>  
</div> --><!-- Fim Breadcrumbs -->

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
      <a href="#" title="" class="button basic" style="margin: 5px;" id="opener-lnct-importar-xls"><img src="images/icons/dark/arrowDown.png" alt="" class="icon"/><span>Importar XLS</span></a>
      <a href="#" title="" class="button dblueB" style="margin: 5px;" id="opener-lnct-lote"><img src="images/icons/light/inbox2.png" alt="" class="icon"/><span>Incluir em lote</span></a>
      <a href="javascript://void(0);" title="" class="button redB" style="margin: 5px;" onClick="alertaExcluirLote();"><img src="images/icons/light/close.png" alt="" class="icon"/><span>Excluír selecionados</span></a>
			<!--
      <span class="middleNav2">
          <ul>
              <li class="btnOpt"><a href="javascript://void(0);" title="" class="button greenB" style="margin: 5px;" id="opcoes"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Opções</span></a>
                  <ul class="subOpt">
                      <li><a href="javascript://void(0);" title="" class="opt" onClick="lnctIncluirLote();">Novos lançamentos</a></li>
                      <li><a href="javascript://void(0);" title="" class="opt" onClick="alertaExcluirLote();">Excluír</a></li>
                  </ul>
              </li>
          </ul>
      </span>
			-->
      
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
        $query_lnct_import = "
					select vencimento, id, descricao, valor, date_format(competencia, '%d/%m/%Y') dt_competencia_format, date_format(vencimento, '%d/%m/%Y') dt_vencimento_format
					from lancamentos_import
					order by vencimento
				";
				$array_lnct = $db->fetch_all_array($query_lnct_import);
        $lancamentos = "";
        foreach($array_lnct as $lnct){
          if($lnct['valor']>0){
						$cor = 'blue'; /* color="#009900" */
						$tp_lnct = "R";
					}else{
						$cor = 'red'; /* color="#FF0000 */
						$tp_lnct = "P";
						$lnct['valor'] = $lnct['valor']*(-1);
					}
	
					// ============ data ============
					$dt_compensar = explode("/", $lnct['dt_vencimento_format']);
					$dia = $dt_compensar[0];
					$m = $dt_compensar[1];
					if($m == 1){ $mes = 'Jan';}
										elseif($m == 2){ $mes = 'Fev';}
										elseif($m == 3){ $mes = 'Mar';}
										elseif($m == 4){ $mes = 'Abr';}
										elseif($m == 5){ $mes = 'Mai';}
										elseif($m == 6){ $mes = 'Jun';}
										elseif($m == 7){ $mes = 'Jul';}
										elseif($m == 8){ $mes = 'Ago';}
										elseif($m == 9){ $mes = 'Set';}
										elseif($m == 10){ $mes = 'Out';}
										elseif($m == 11){ $mes = 'Nov';}
										else{ $mes = 'Dez';}
					$ano = substr($dt_compensar[2], -2);
					// ==============================				

					$lancamentos .= '					
						<tr class="gradeA" >
							<td>'.$lnct['data'].'</td>
							<td class="updates newUpdate">
										
										<div class="lnctCheckbox" style="float:left; padding-top:12px; padding-bottom:-12px;"><input type="checkbox" value="'.$lnct['id'].'" id="check_'.$lnct['id'].'" class="'.$tp_lnct.'"/></div>
											
										<div class="uDate tbWF tipS"  style="margin-left:15px;" original-title="Vencimento" align="center"> 
											
											<span id="data_'.$lnct['id'].'" style="display:none">'.$lnct['dt_vencimento_format'].'</span>
											<span id="data_c_'.$lnct['id'].'" style="display:none">'.$lnct['dt_competencia_format'].'</span>
																				
											<span  class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br>
										
										</div>
										
										<span class="lDespesa tbWF" style="width:65%;">
											<a href="javascript://void(0);"  style="cursor: default;" original-title="Descrição" class="tipS" ><strong id="dscr_'.$lnct['id'].'">'.$lnct['descricao'].'</strong></a>
												<span original-title="'.$title_fav.'" class="tipN">'.$nome_desc.'</span>
										</span>											
															
	  								<div class="tbWFoption" id="link_excluir_'.$lnct['id'].'">		
											<a href="javascript://void(0);" original-title="Excluir" class="smallButton btTBwf redB tipS excluir"  onClick="alertaExcluir('.$lnct['id'].');"><img src="images/icons/light/close.png" width="10"></a>								
											<a href="javascript://void(0);" original-title="Novo lançamento" class="smallButton btTBwf tipS" id="link_quitar_'.$lnct['id'].'" onClick="novoLancamento(\''.$tp_lnct.'\','.$lnct['id'].')"><img src="images/icons/dark/add.png" width="10"></a>
											<a href="javascript://void(0);" original-title="Transferência" class="smallButton btTBwf tipN" id="link_trans_'.$lnct['id'].'" onClick="novoLancamento(\'T'.$tp_lnct.'\','.$lnct['id'].')"><img src="images/icons/dark/transfer.png" width="10"></a>
										</div> 
																																													
										<div class="tbWFvalue '.$cor.'" >R$ <b id="vl_'.$lnct['id'].'">'.$db->valorFormat($lnct['valor']).' </b></div>
				
							</td> 
						</tr>'; 
        }
        echo '
          <table cellpadding="0" cellspacing="0" border="0" class="display dTableLancamentos">
          <thead>
          <tr style="border-bottom: 1px solid #e7e7e7;">
            <th>Ordem</th>
						<th class="ckbHeaderCell" style="padding:4px 0px 5px 15px" >
							<div class="sItem" style="float:left; width:20px; margin-left:-7px; margin-top:2px; padding-left:7px; padding-right:9px; padding-top:1px; padding-bottom:2px; border:1px solid #CCC; background:#F9F9F9;">
								<input type="checkbox" id="ckbTblHeader" onclick="lnctChecarTodos(\'\');" style="padding-left:20px; padding-bottom:10px;">
								<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s btnDropDownCk" id="listItens" style="margin-left:7px;"></span>
								<div class="statsDetailed" id="dropDownMenuCk" style="margin-top:11px;">											
										<div class="statsContent" align="left" >
												<div class="statsUpdate statsUpdateCk">
														<input type="checkbox" id="ckbDropDownHeader"> <label for="ckbDropDownHeader">Todos</label> <div class="clear"></div>
												</div>
																														 
												<div id="ckbDropDownList">
													<div class="statsUpdate statsUpdateCk">
															<input type="checkbox" value="R" class="ckbListItem" id="tpLnctCk01"> <label for="tpLnctCk01">Recebimentos</label> <div class="clear"></div>
													</div>
												 
													<div class="statsUpdate statsUpdateCk">
															<input type="checkbox" value="P" class="ckbListItem" id="tpLnctCk02"> <label for="tpLnctCk02">Pagamentos</label> <div class="clear"></div>
													</div>
												</div>
										</div>
								</div>
							</div>
						</th>
          </tr>
          </thead>
          <tbody>
            ',$lancamentos,'
          </tbody>
          </table>
        ';
        ?>
      </div>
  </div>
 
 <!-- ====== Fim do Palco ====== -->

  <!-- ====== *** UI Dialogs *** ====== -->

  <?php require("lnct_import_dialogs.php"); ?>
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->

	<?php
	echo '<input type="hidden" id="cliente_id" value="'.$_SESSION['cliente_id'].'"/>';
	echo '<input type="hidden" id="usuario_id" value="'.$_SESSION['usuario_id'].'"/>';
	?>
 
	</div>
</div>
