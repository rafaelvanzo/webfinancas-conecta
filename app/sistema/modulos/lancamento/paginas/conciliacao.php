<?php
require("modulos/lancamento/class/Lancamento.class.php");
require("modulos/lancamento/class/Conciliacao.class.php");
$conciliacao = new Conciliacao();
?>
	<!-- Título -->
  <div class="titleArea">
    <div class="wrapper">
        <div class="pageTitle">
            <h2>Conciliar Lançamentos</h2>
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
  </div> -->
  <!-- Fim Breadcrumbs -->

	<!-- Divisão entre título e palco -->
  <div class="wrapper">
  	<div class="divider">
    	<span></span>
     </div>
  </div>

  <!-- Main content wrapper -->
  <div class="wrapper">
		
      <div class="fluid">

  			<div class="span6">
  
          <div class="widget">
      
            <!--<div class="title"><img src="images/icons/dark/magnify.png" alt="" class="titleIcon" /><h6>Conta Financeira</h6></div>-->
        
            <div class="formRowB cf_container">
            
              <!-- Busca do banco -->
              <span class="span12 input-autocomplete-container">
                <label>Conta financeira</label>
                <input type="text" name="conta_id_import" id="input_conta_import" class="conta_buscar input-buscar" placeholder="Preencha para localizar..." style="padding: 6px 5px;"/>
                <input type="hidden" id="conta_id_import" value="" onchange="cnlcIniciar(this.value);"/>
              </span>
    
              <br>
    
              <div class="fluid">
                
                <!-- Seleciona tipo de extrato -->
                <span class="span3" style="margin-left:0px;padding-top:15px;">
                
                  <div class="selecionar_tp_extrato" id="selecionar_tp_extrato">
                    <input type="radio" name="tp_extrato" id="tp_extrato_01" checked onChange="changeExtrato('1');" value="1"> <label for="tp_extrato_01" id="tp_extrato_01_label"> <strong>Extrato Bancário</strong> </label> 
                    <span class="tp_extrato_divider"></span>
                    <input type="radio" name="tp_extrato" id="tp_extrato_02" onChange="changeExtrato('2');" value="2"> <label for="tp_extrato_02" id="tp_extrato_02_label"> <strong>Arquivo de Retorno</strong> </label>
                  </div>
                
                </span>

                <span id="extrato_saldo_divider"></span>

                <!-- Saldos -->
                <span class="span9" id="span_saldo_cnlc">
                  <div id="saldo_banco">
                  </div>
                  <span id="saldo_divider"></span>
                  <div id="saldo_wf">
                  </div>
                </span>
                
              </div>

              <br>

              <!-- Botôes -->
              <div class="fluid">
                <span class="span12">
                
                  <div id="btn-extrato-banco" class="extrato-banco">
                    <a href="#" title="" class="button basic" id="opener-extrato-importar"><img src="images/icons/dark/arrowDown.png" alt="" class="icon"/><span>Importar Extrato</span></a>
                    <a href="javascript://void(0);" title="" style="" class="button redB" onClick="alertaExcluirLote('dTableExtratoBanco','lnctCheckbox');"><img src="images/icons/light/close.png" alt="" class="icon"/><span>Excluír</span></a>
                  </div>
          
                  <div id="btn-arq-retorno" class="arq-retorno" style="display:none">
                    <a href="#" title="" class="button basic" id="opener-arq-ret-import"><img src="images/icons/dark/arrowDown.png" alt="" class="icon"/><span>Importar Arquivo de Retorno</span></a>
                    <a href="javascript://void(0);" title="" class="button redB" style="" onClick="alertaExcluirLote('dTableBoletos','lnctCheckbox02');"><img src="images/icons/light/close.png" alt="" class="icon"/><span>Excluír</span></a>
                  </div>
                                      
                </span>
              </div>
    
              <div style="height:8px">
              </div>
      
            </div>
      
          </div>
  
      </div>
	
  	</div>

    <div class="fluid">

      <span class="span12">

        <div class="widget extrato-banco" style="margin-top:54px" id="extrato-banco-widget">

           <div class="title"><h6>Extrato Bancário</h6></div>
           <div class="" style="border-bottom:0px;border-top:0px solid rgb(213, 213, 213);background-color:rgb(213, 213, 213);">
            <div class="dataTables_wrapper" style="background-color:rgb(249,249,249);">
              <div class="dataTables_filter" style="float:none;">
                <label><span>Localizar:</span> <input type="text" aria-controls="" id="input-search-table"> <i class="srch"></i></label>
              </div>
            </div>
          </div>

					<div id="lancamentos">

          	<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
            
              <table cellpadding="0" cellspacing="0" border="0" class="display dTableExtratoBanco dataTable" id="DataTables_Table_0">
                
                <thead>
                
                <tr style="border-bottom: 1px solid #e7e7e7;" role="row">

                <th class="ckbHeaderCell ui-state-default" style="padding:1px 0px 1px 15px" role="columnheader" rowspan="1" colspan="1">
                
                  <div class="DataTables_sort_wrapper">
                    
                      <div class="sItem" style="float:left; width:20px; margin-left:-7px; margin-top:2px; padding-left:7px; padding-right:9px; padding-top:1px; padding-bottom:2px; border:1px solid #CCC; background:#F9F9F9;">
                    
                        <input type="checkbox" id="ckbTblHeader" onclick="lnctChecarTodos(\'\');" style="padding-left: 20px; padding-bottom: 10px; opacity: 0;">
                        <span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s btnDropDownCk" id="listItens" style="margin-left:7px;"></span>
                    
                        <div class="statsDetailed" id="dropDownMenuCk" style="margin-top:11px;">											
                            <div class="statsContent" align="left">
                                <div class="statsUpdate statsUpdateCk">
                                    <div class="checker" id="uniform-ckbDropDownHeader"><span><input type="checkbox" id="ckbDropDownHeader" style="opacity: 0;"></span></div> <label for="ckbDropDownHeader">Todos</label> <div class="clear"></div>
                                </div>
                                                                     
                                <div id="ckbDropDownList">
                                  <div class="statsUpdate statsUpdateCk">
                                      <div class="checker" id="uniform-tpLnctCk01"><span><input type="checkbox" value="R" class="ckbListItem" id="tpLnctCk01" style="opacity: 0;"></span></div> <label for="tpLnctCk01">Recebimentos</label> <div class="clear"></div>
                                  </div>
                                 
                                  <div class="statsUpdate statsUpdateCk">
                                      <div class="checker" id="uniform-tpLnctCk02"><span><input type="checkbox" value="P" class="ckbListItem" id="tpLnctCk02" style="opacity: 0;"></span></div> <label for="tpLnctCk02">Pagamentos</label> <div class="clear"></div>
                                  </div>
                                </div>
                            </div>
                        </div>
                      
                      </div>
                    
                    <span class="DataTables_sort_icon"></span>
                    
                  </div>
                  
                </th>
                
                </tr>
                
                </thead>
                
                <tbody role="alert" aria-live="polite" aria-relevant="all">
                  <tr class="odd">
                    <td valign="top" colspan="1" class="dataTables_empty">Nenhum registro econtrado</td>
                  </tr>
                </tbody>
                
              </table>
              
           	</div>

          </div>

        </div>
  
				<div class="widget arq-retorno" style="margin-top:54px;display:none" id="arq-retorno-widget">

           <div class="title"><h6>Arquivo de Retorno</h6></div>    
           <div class="" style="border-bottom:0px;border-top:0px solid rgb(213, 213, 213);background-color:rgb(213, 213, 213);">
            <div class="dataTables_wrapper" style="background-color:rgb(249,249,249);">
              <div class="dataTables_filter" style="float:none;">
                <label><span>Localizar:</span> <input type="text" aria-controls="" id="input-search-table02"> <i class="srch"></i></label>
              </div>
            </div>
          </div>

          <div id="extrato-arq-retorno">
          
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
            
              <table cellpadding="0" cellspacing="0" border="0" class="display dTableBoletos dataTable" id="DataTables_Table_0">
                <thead>
                  <tr style="border-bottom: 1px solid #e7e7e7;" role="row">
                    <th class="ckbHeaderCell02 ui-state-default" style="padding:4px 0px 5px 15px" role="columnheader" rowspan="1" colspan="1">
                      <div class="DataTables_sort_wrapper">
                        <!--<div class="checker" id="uniform-ckbTblHeader02"><span>-->
                          <input type="checkbox" id="ckbTblHeader02" onclick="lnctChecarTodos(\'\');" style="padding-left: 20px; padding-bottom: 10px; opacity: 0;">
                        <!--</span></div>-->
                        <span class="DataTables_sort_icon"></span>
                      </div>
                    </th>
                  </tr>
                </thead>
                
                <tbody role="alert" aria-live="polite" aria-relevant="all">
                  <tr class="odd">
                    <td valign="top" colspan="1" class="dataTables_empty">Nenhum registro econtrado</td>
                  </tr>
                </tbody>
              </table>
            
            </div>
          </div>

        </div>

        <?php
				/*
        <div class="widget extrato-wf" style="border-top:0px;border-bottom:0px;display:none;">
  
           <div class="title"><h6>Extrato Web Finanças</h6></div>    
           <div class="title" style="border-bottom:0px;border-top:0px solid rgb(213, 213, 213);background-color:rgb(213, 213, 213);">
            <div class="dataTables_wrapper">
              <div class="dataTables_filter">
                <label><span>Localizar:</span> <input type="text" aria-controls="" id="input-search-table03"> <i class="srch"></i></label>
              </div>
            </div>
          </div>
      
          <div id="extrato-wf">
            <?php 
            $dt_ini = date('Y').'-'.date('m').'-01';
            $dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
            $dt_fim = date('Y-m-d',$dt_fim);
            
            $query_lancamentos = "
              select dt_compensacao dt_ordem, id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, favorecido_id, valor, conta_id_origem, conta_id_destino
              from lancamentos
              where compensado = 1
               and dt_compensacao >= '".$dt_ini."'
               and dt_compensacao <= '".$dt_fim."'
            ";
    
            $lancamentos_listar = "";
            
            $array_lancamentos = $db->fetch_all_array($query_lancamentos);
            
            foreach($array_lancamentos as $lancamento){
            //Busca o nome do Favorecido
            if($lancamento['tipo']=='T'){										
              $nome_conta_org = $db->fetch_assoc("select descricao from contas where id = ".$lancamento['conta_id_origem']);
              $nome_desc = '<b>Débito: </b>'.$nome_conta_org['descricao']; $title_fav = 'Conta de Origem';
              $nome_conta_dest = $db->fetch_assoc("select descricao from contas where id = ".$lancamento['conta_id_destino']);				
            }else{ $nome_favorecido = $db->fetch_assoc("select nome from favorecidos where id = ".$lancamento['favorecido_id']); $nome_desc =	$nome_favorecido['nome']; $title_fav = 'Favorecido'; }
              
              if($lancamento['tipo']=="R"){
                $classe_excluir = "recebimentosExcluir";
                $cor = 'blue'; // color="#009900" 
              }elseif($lancamento[tipo]=="P"){
                $classe_excluir = "pagamentosExcluir";
                $cor = 'red"'; // color="#FF0000 
              }else{
                $classe_excluir = "transferenciasExcluir";
                $cor = 'red"'; // color="#FF0000 
              }

              // ============ data ============
              $dt_compensar = explode("/", $lancamento['dt_compensacao']);
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
              $lancamentos_listar .='
                <tr class="gradeA">
                  <td style="display:none;">'.$lancamento['dt_ordem'].'</td>
                  <td class="updates newUpdate">
                            
                        <div class="uDate tbWF tipS" original-title="Compensação" align="center"> <span class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
												<span class="lDespesa tbWF" >
													<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
													<span original-title="'.$title_fav.'" class="tipN">'.$nome_desc.'</span>
												</span>											
                                  
                        <div class="tbWFoption">										
													<a href="'.$lancamento['id'].'" original-title="Excluir" class="smallButton btTBwf redB tipS '.$classe_excluir.'"><img src="images/icons/light/close.png" width="10"></a>		
													<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="lancamentosExibir('.$lancamento['id'].',\''.$lancamento['tipo'].'\')"><img src="images/icons/light/pencil.png" width="10"></a>											
												</div>
                                                                                              
                        <div class="tbWFvalue '.$cor.'">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
            
                  </td> 
                </tr>
              ';
              if($lancamento['tipo']=='T'){
                $lancamentos_listar .='
                  <tr class="gradeA">
                    <td style="display:none;">'.$lancamento['dt_ordem'].'</td>
                    <td class="updates newUpdate">
                            
                        <div class="uDate tbWF tipS" original-title="Compensação" align="center"> <span class="uDay">'.$dia.'</span>'.$mes.'/'.$ano.' <br></div>
                          <span class="lDespesa tbWF" >
                            <a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$lancamento['descricao'].'</strong></a>
                              <span original-title="Conta de Destino" class="tipN"><b>Crédito: </b>'.$nome_conta_dest['descricao'].'</span>
                          </span>											
                                  
                        <div class="tbWFoption">										
                            <a href="'.$lancamento['id'].'" original-title="Excluir" class="smallButton btTBwf redB tipS '.$classe_excluir.'"><img src="images/icons/light/close.png" width="10"></a>		
                            <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS"  onClick="lancamentosExibir('.$lancamento['id'].',\''.$lancamento['tipo'].'\')"><img src="images/icons/light/pencil.png" width="10"></a>											
                          </div>
                                                                                              
                        <div class="tbWFvalue blue">R$ '.number_format($lancamento['valor'],2,',','.').' </div>
            
                  </td> 
                  </tr>
                ';						
              }
            }
            
            $lancamentos_listar ='
              <table cellpadding="0" cellspacing="0" border="0" class="display">
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
                '.$lancamentos_listar.'
              </tbody>
              </table>
            ';
            echo $lancamentos_listar;
            ?>            
  
          </div>
       
        </div>
				*/
        ?>
              
      </span>
      
    </div>

	</div>

<!-- ====== Fim do Palco ====== -->

<!-- ====== *** UI Dialogs *** ====== -->

<?php require("cnlc_dialogs.php"); ?>
<?php require("lnct_dialogs.php"); ?>

<!-- ====== *** Fim UI Dialogs *** ====== -->

<?php
echo '<input type="hidden" id="cliente_id" value="'.$_SESSION['cliente_id'].'"/>';
echo '<input type="hidden" id="usuario_id" value="'.$_SESSION['usuario_id'].'"/>';
?>
