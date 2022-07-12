<?php
require("modulos/lancamento/class/Lancamento.class.php");
?>

<!-- <script> alert(window.innerWidth); </script> -->
 
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Lançamentos</h2>
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
                      <a href="javascript://" style="cursor: default;">Recebimentos Programados</a>
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
      		<a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-rcbt-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Recebimento</span></a>
          <a href="#" title="" class="button redB" style="margin: 5px;" id="opener-pgto-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Pagamento</span></a>
          <a href="#" title="" class="button blueB" style="margin: 5px;" id="opener-trsf-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Nova Transferência</span></a>
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
  
 <!-- Organiza o layout -->
 <div class="fluid">   
    
      <!-- Contas -->

			<?php
      $array_contas = $db->fetch_all_array("
				select c.id, b.nome, b.logo, c.descricao, c.vl_saldo, c.vl_credito
				from contas c
				left join bancos b on c.banco_id = b.id
				order by c.descricao
      ");
            ?>
  
    <div class="span5">

        <div class="widget">

            <!--
            Alterações para ajustar box das contas finaceiras e carnê leão:

            - retirar texto <h6>Saldos</h6>
            - retirar border-bottom da div .title
            - ajustar padding do icone para preencher a div title até encontrar com a borda superior das abas
            - margin-top -38px na div das abas .tabs-bs
            * falta retirar o border-top do elemento <a> no menu de navegação das abas ui_custom.css linha 757
            * melhorar a responsividade quando a tela é reduzida
            * retirar max-width: 680px; da classe toggle, pois está travando a largura do saldo disponível para resuloções maiores
            -->

          <div class="title" style="border-bottom:0px;">
              <img src="images/icons/dark/money.png" alt="" class="titleIcon" style="padding:12px 11px;"><?php if($_SESSION['carne_leao'] == 0){ echo '<h6>Saldos</h6>'; }?> <!--<h6>Saldos</h6>-->
              <div class="num">
                  <a href="javascript://" class="blueNum tipE" style="cursor: default;" original-title="Total de <?php echo count($array_contas)?> conta(s)"><?php echo count($array_contas)?></a>
              </div>
          </div>

          <div class="tab-bs" style="margin-top:-38px;">
               
               <?php if($_SESSION['carne_leao'] == 1){ ?> 
               <ul class="nav nav-tabs" id="abas-cf">
			        <li class="active"><a data-target="#aba-cf1" data-toggle="tab">Contas Financeiras</a></li>
			        <li><a data-target="#aba-cf2" data-toggle="tab">Carnê leão</a></li>
			    </ul> 
                <?php } ?>

                <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			    <div class="tab-content">                 

			        <div class="tab-pane active" id="aba-cf1" style="width:100%;">  

                <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

              <div class="scroll" style="height:136px;">
                 <input type="hidden" id="conta_id" value="<?php echo $array_contas[0]['id'];?>" /> <!-- armazena a id da conta que está sendo exibida na página de lançamentos(usado pela função de editar e de excluir) -->
                 <div class="newOrder" id="contasSaldo">
  
                   <?php
                   $saldo_total = $array_contas[0]['vl_saldo']+$array_contas[0]['vl_credito'];
									 $caixa_total = $array_contas[0]['vl_saldo'];
									 $credito_total = $array_contas[0]['vl_credito'];
                   //$nome_conta_ini = $array_contas[0]['nome']." - ".$array_contas[0]['descricao'];
                   $conta_id_ini = $array_contas[0]['id'];
									 
												if(!empty($array_contas[0]['logo'])){ $banco_logo = $array_contas[0]['logo']; }else{ $banco_logo = "bank.png"; }
  
                    echo '
                      <div class="userRow">
                          <img src="images/bancos/'.$banco_logo.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;">
                          <ul class="leftList">
                              <li><a href="javascript://void(0);" style="cursor: default;"><strong>'.$array_contas[0]['descricao'].'</strong></a></li>
                              <li style="font-size: 9px;">'.$array_contas[0]['nome'].'</li>
                          </ul>
                      </div>
                      <div class="orderRow">
                          <ul class="leftList">
                              <li>Saldo:</li>
                              <li>Crédito disponível:</li>
                              <li><b>Total:</b></li>
                          </ul>
                          <ul class="rightList">
                              <li>R$ '.number_format($array_contas[0]['vl_saldo'],2,',','.').'</li>
                              <li>R$ '.number_format($array_contas[0]['vl_credito'],2,',','.').'</li>
                              <li><strong class="grey">R$ '.number_format($array_contas[0]['vl_saldo']+$array_contas[0]['vl_credito'],2,',','.').'</strong></li>
                          </ul>
                      </div>
                      <div class="linha" style="margin-bottom:0;"></div>  <!-- Linha deve estar no ultimo formRow -->
                    ';
  
                   unset($array_contas[0]);
  
                   foreach($array_contas as $conta){
										  if(!empty($conta['logo'])){ $banco_logo = $conta['logo']; }else{ $banco_logo = "bank.png"; }
											
                    echo '
                      <div class="userRow">
                          <img src="images/bancos/'.$banco_logo.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;">
                          <ul class="leftList">
                              <li><a href="javascript://void(0);" style="cursor: default;"><strong>'.$conta['descricao'].'</strong></a></li>
                              <li style="font-size: 9px;">'.$conta['nome'].'</li>
                          </ul>
                          <div class="rightList"></div>
                      </div>
                      <div class="orderRow">
                          <ul class="leftList">
                              <li>Saldo:</li>
                              <li>C. Especial:</li>
                              <li><b>Total:</b></li>
                          </ul>
                          <ul class="rightList">
                              <li>R$ '.number_format($conta['vl_saldo'],2,',','.').'</li>
                              <li>R$ '.number_format($conta['vl_credito'],2,',','.').'</li>
                              <li><strong class="grey">R$ '.number_format($conta['vl_saldo']+$conta['vl_credito'],2,',','.').'</strong></li>
                          </ul>
                      </div>
                     <div class="linha" style="margin-bottom:0;"></div>  <!-- Linha deve estar no ultimo formRow -->
                    ';


                    $saldo_total += $conta['vl_saldo']+$conta['vl_credito'];
										$caixa_total += $conta['vl_saldo'];
										$credito_total += $conta['vl_credito'];
										
										$total_disponivel = "R$ ".number_format($saldo_total,2,',','.');
										$_SESSION['total_disponivel'] = $total_disponivel;
                   } ?>							
                </div>
            </div> 
  
            <!--<div class="cLine"></div>-->

                <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                </div>

                <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			    <div class="tab-pane" id="aba-cf2" style="width:100%;">
                    
                    <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget table-hover">
                        
                        <tbody>
                            <tr>
                                <td class="taskPb">Total De Rendimentos PF</td>
                                <td style="text-align-last:right;"><span class="lGrey f11 blue" id="vl-rendimento-pf" style="font-weight:bold;"> 0,00</span></td>
                            </tr>
                            <tr>
                                <td class="taskPb">Total De Rendimentos PJ</td>
                                <td style="text-align-last:right;"><span class="lGrey f11 blue" id="vl-rendimento-pj" style="font-weight:bold;"> 0,00</span></td>
                            </tr>
                            <tr>
                                <td class="taskPr">Total De Despesas Dedutíveis</td>
                                <td style="text-align-last:right;"><span class="lGrey f11 green" id="vl-dedutivel" style="font-weight:bold; color:#599414;">0,00</span></td>
                            </tr>
                            <tr>
                                <td class="taskD">Total De Despesas Não Dedutíveis</td>
                                <td style="text-align-last:right;"><span class="lGrey f11 red" id="vl-nao-dedutivel" style="font-weight:bold; color:#A73939;">0,00</span></td>
                            </tr>
                            <tr>
                                <td class="taskP">Total De Despesas</td>
                                <td style="text-align-last:right;"><span class="lGrey f11" id="vl-despesa">0,00</span></td>
                            </tr>
                            <tr>
                                <td class="taskP">Base De Cálculo Do Imposto</td>
                                <td style="text-align-last:right;"><span class="lGrey f11" id="vl-base-imposto">0,00</span></td>
                            </tr>
                            <tr>
                                <td class="taskPB">Imposto Calculado</td>
                                <td style="text-align-last:right;"><span class="lGrey f11" id="vl-imposto" style="font-weight:bold; color:#000000;">0,00</span></td>
                            </tr>
                        </tbody>

                    </table>

                <!-- fim aba 2 --------------------------------------------------------------------------------------------------------------->

                </div>

            </div>
        </div>
           
                 <div class="toggle tipS" style="border-top: 1px solid #CCC; border-bottom: 0; margin-top: 0px;"  original-title="Detalhamento">
                    <div class="title closed" id="opened" align="right" style="vertical-align: central; border: 0;">
                    	<img src="images/icons/dark/full2.png" alt="" class="titleIcon tipS" />
                      		<div style="padding-top: 8px; margin-right: 10px;"> Disponível:<b> <span class="saldoTotal"> <?php echo $total_disponivel; ?> </span> </b> </div>
                     </div>
                    <div class="body" style="padding:0;border:0;display:none;">
                      <table cellpadding="0" cellspacing="0" width="100%" class="sTable" style=" border-top: 1px solid #CCC;">
                          <tbody>
                      		 		<tr>
                              		<td width="40"><b class="blue">Saldo</b></td>
                                  <td align="right"><b class="blue" id="saldo-acumulado"> R$ <?php echo $db->valorFormat($caixa_total);?> </b></td>
                              </tr>
                              <tr>    
                                  <td width="40"><b class="blue">Crédito</b></td>
                                  <td align="right"><b class="blue" id="credito-acumulado"> R$ <?php echo $db->valorFormat($credito_total);?> </b></td>
                              </tr> 
                          </tbody>
                      </table>   
                    </div>
                </div> 
               
         </div>
  
       </div>  <!-- Fim Contas -->  

      <div class="span7">
      
     	 <div class="widget" style="min-height:212px;">
  
      	<div class="title"><img src="images/icons/dark/magnify.png" alt="" class="titleIcon" /><h6>Pesquisar</h6></div>
                
        <form class="form" id="formPesq">

        <div class="formRowB pesq_container" style="padding-top:6px; padding-bottom:2px;">
         
            <!--<div class="selecionar" style="height: 146px;">-->

             <div class="fluid" align="center" >
              
                 <span class="span6" style="padding-bottom:6px; padding-top:2px;">
									 <?php 
                    $dt_ini = date('01/m/Y');
										$dt_fim = mktime(0,0,0,date('m')+1,0,date('Y'));
										$dt_fim = date('d/m/Y',$dt_fim);
                   ?>
                   <!--<input type="radio" name="dt_pesq"> Data:-->
                   <input type="hidden" name="funcao" value="lancamentosBuscarPeriodo">
                   <input type="hidden" name="tp_busca" value="periodo" class="dtFltAtivo" id="dtFltP">
                   <!--<label style="width: 100%;"><strong>Período:</strong></label>-->
                   <input name="dt_ini" id="dt_ini" type="text" class="datepicker maskDate dtFltP" placeholder="Data inicial" onclick="mudaDtFlt('dtFltM','dtFltP');" value="<?php echo $dt_ini;?>"/>
                   <input name="dt_fim" id="dt_fim" type="text" class="datepicker maskDate dtFltP" placeholder="Data final" onclick="mudaDtFlt('dtFltM','dtFltP');" value="<?php echo $dt_fim;?>"/>
                    
                 </span>
                
                 <span class="span6" style="padding-bottom:6px;">
                 
                  <!--<input type="radio" name="dt_pesq"> Mês:-->
                  <input name="dt_ini_m" id="dt_ini_m" type="text" class="monthpicker datepickerM dtFltM" placeholder="Mês" onclick="mudaDtFlt('dtFltP','dtFltM');" readonly/>
                  <input type="hidden" name="funcao" value="lancamentosBuscarMes">
                  <input type="hidden" name="tp_busca" value="mes" class="dtFltAtivo" id="dtFltM">
                  &nbsp;
                  <a href="javascript://void(0);" title="" class="button basic bt_monthpicker" style="width:38px; margin-bottom:-10px;" onclick="mudaDtFlt('dtFltP','dtFltM');"><img src="images/icons/dark/dayCalendar.png" alt="" class="icon"/></a>
                </span>
                
              </div>	
              
        </div>
         <div class="formRowB pesq_container" style="padding-top:6px; padding-bottom:2px;"> 
              
              <div class="fluid" align="center" style="padding-bottom:6px;">
                    
                <span class="span6" style="text-align:">

                 <div class="sItem dropDownList1">
                      <a href="javascript://void(0);" title="" class="button basic btnDropDown" style="" id="listItens1"><span>Contas Financeiras</span>  <img src="images/icons/dark/list.png" alt="" class="icon iconList" id="iconList1"/><div class="NumF countList" style="display:none;" id="countList1"></div></a> 
                      <div class="statsDetailed listItens1">
                          <div class="statsContent" align="left">
                              <div class="statsUpdate statsUpdateCk">
                                  <input type="checkbox" id="checkAllCf" checked> <label for="checkAllCf">Todos</label> <div class="clear"></div>
                              </div>
                              <div id="contasFinanceiras">
                              	<?php
																$count = 1;
																$cnt_fin = $db->fetch_all_array("select id, descricao from contas order by descricao");
																foreach($cnt_fin as $cf){
																	echo '
																		<div class="statsUpdate statsUpdateCk" title="'.$cf['descricao'].'">
																				<input type="checkbox" value="'.$cf['id'].'" class="checkCf" checked id="cfCk'.$count.'"> <label for="cfCk'.$count.'" class="ellipsis">'.$cf['descricao'].'</label> <div class="clear"></div>
																		</div>
																	';
																	$count ++;
																}
																if(count($cnt_fin)>0){
																	echo '
																		<script>
																			document.getElementById("countList1").innerHTML = "'.count($cnt_fin).'";
																			document.getElementById("countList1").style.display = "block";
																			document.getElementById("iconList1").style.display = "none";
																		</script>
																	';
																}
                                  ?>
                              </div>
                              <div class="statsDropBtn"><a href="javascript://void(0);" class="button greyishB" onClick="filtroSelecionar('listItens1');"><img src="images/icons/light/check.png" class="icon" alt="" /><span>Selecionar</span></a></div>
                          </div>
                      </div>
                      <!--
                      <span class="changes">
                          <span class="negBar" values="5,10,15,20,25,30,35,40,45,50"></span>
                          <span class="negative">+0.6%</span>
                      </span>
                      -->
                  </div>

                </span>

                <span class="span6" style="text-align:">

                 <div class="sItem dropDownList3">
                    <a href="javascript://void(0);" title="" class="button basic btnDropDown" id="listItens3"><span>Centro / Categoria</span><img src="images/icons/dark/list.png" alt="" class="icon iconList" id="iconList3"/><div class="NumF countList" id="countList3" style="display:none;">0</div></a>
                    <div class="statsDetailed listItens3" style="width:250px;">
                        <div class="statsContent">
                            
                            <div class="statsUpdate">
                                 <div class="fluid" style="">
                                  <span class="span12 input-autocomplete-container">
                                    <!--<label><strong>Centro de Responsabilidade:</strong></label>-->
                                    <input type="text" class="input-buscar centro_resp_buscar" placeholder="Centro de Custo" name="ct_resp_pesq" id="form-pesq-ctr-buscar">
                                    <input type="hidden" name="" id="ct_resp_pesq" class="buscar" value="0"/>
                                  </span>
                                </div>
                            </div>
                            
                            <div class="statsUpdate">
                                 <div class="fluid" style="">
                                  <span class="span12 input-autocomplete-container">
                                    <!--<label><strong>Centro de Responsabilidade:</strong></label>-->
                                    <input type="text" class="input-buscar plano_contas_buscar" placeholder="Categoria" name="pl_cnt_pesq" id="form-pesq-plc-buscar">
                                    <input type="hidden" name="" id="pl_cnt_pesq" class="buscar" value="0"/>
                                  </span>
                                </div>
                            </div>
                            <div class="statsDropBtn"><a href="javascript://void(0);" class="button greyishB" onClick="filtroSelecionar('listItens3');"><img src="images/icons/light/check.png" class="icon" alt="" /><span>Selecionar</span></a></div>
                        </div>
                    </div>
 	              </div>

              </span>
              
            </div>                    
            
            <div class="fluid" align="center" style="padding-bottom:6px;">
            
              <span class="span6" style="text-align:">
                
                  <div class="sItem dropDownList2">
                      <a href="javascript://void(0);" title="" class="button basic btnDropDown" style="" id="listItens2"><span>Tipo de Lançamento</span><img src="images/icons/dark/list.png" alt="" class="icon iconList" id="iconList2" style="display:none;"/><div class="NumF countList" id="countList2">3</div></a>
                      <div class="statsDetailed listItens2">
                          <div class="statsContent" align="left">
                              <div class="statsUpdate statsUpdateCk">
                                  <input type="checkbox" id="checkAllTpLnct" checked> <label for="checkAllTpLnct">Todos</label> <div class="clear"></div>
                              </div>
                                                                   
                              <div id="tpLnct">
                                <div class="statsUpdate statsUpdateCk">
                                    <input type="checkbox" value="R" class="checkLnct" checked id="tpLnctCk01"> <label for="tpLnctCk01">Recebimentos</label> <div class="clear"></div>
                                </div>
                               
                                <div class="statsUpdate statsUpdateCk">
                                    <input type="checkbox" value="P" class="checkLnct" checked id="tpLnctCk02"> <label for="tpLnctCk02">Pagamentos</label> <div class="clear"></div>
                                </div>
                                
                                <div class="statsUpdate statsUpdateCk">
                                    <input type="checkbox" value="T" class="checkLnct" checked id="tpLnctCk03"> <label for="tpLnctCk03">Transferências</label> <div class="clear"></div>
                                </div>
                              </div>
															<div class="statsDropBtn"><a href="javascript://void(0);" class="button greyishB" onClick="filtroSelecionar('listItens2');"><img src="images/icons/light/check.png" class="icon" alt="" /><span>Selecionar</span></a></div>
                          </div>
                      </div>
                      <!--
                      <span class="changes">
                          <span class="negBar" values="5,10,15,20,25,30,35,40,45,50"></span>
                          <span class="negative">+0.6%</span>
                      </span>
                      -->
                  </div>

              </span>

              <span class="span6">

                <div class="sItem dropDownList5">
                    <a href="javascript://void(0);" title="" class="button basic btnDropDown" id="listItens5"><span>Mais Opções</span><img src="images/icons/dark/list.png" alt="" class="icon iconList" id="iconList5"/><div class="NumF countList" id="countList5" style="display:none;"></div></a>
                    <div class="statsDetailed listItens5" style="width:250px;">
       								
                        <div class="statsContent">
                                  
                                      <div class="statsUpdate">
                                             <div class="fluid" style="">
                                              <span class="span12 input-autocomplete-container">
                                                <!--<label><strong>Favorecido:</strong></label>-->
                                                <input type="text" name="fav_pesq" id="fav_pesq_01" class="input-buscar favorecido_buscar" placeholder="Favorecido">
                                                <input type="hidden" name="favorecido_id" id="fav_pesq" class="buscar" value="0"/>
                                              </span>
                                            </div>
                                        </div>
                                                                           
                                    <div class="statsUpdate">
                                          <div class="fluid" style="">
                                          <span class="span12">
                                            <!--<label><strong>Centro de Responsabilidade:</strong></label>-->
                                            <input type="text" class="moeda" placeholder="Valor" id="vl_pesq">
                                          </span>
                                          
                                        </div>
                                    </div>

                                    <div class="statsUpdate">
                                          <div class="fluid" style="">
                                          <span class="span12">
                                            <!--<label><strong>Centro de Responsabilidade:</strong></label>-->
                                            <input type="text" class="" placeholder="Nosso Número" id="nosso-numero-pesquisar">
                                          </span>
                                          
                                        </div>
                                    </div>
                                    
                                   <!--<div class="statsUpdate">
                                         <div class="fluid" style="">
                                            <span class="span12">
                                              <input type="text" placeholder="Descrição">
                                            </span>
                                        </div>
                                    </div>-->
 
                                   <div class="statsUpdate">
                                             <div class="fluid" style="">
                                              <span class="span12 input-autocomplete-container">
                                                 <select name="documento_id" id="doc_pesq">
                                                    <option value="">Tipo de documento..</option>
                                                    <?php
                                                    $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                                    foreach($array_documentos as $documento){
                                                      echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                                    }
                                                    ?>
                                                  </select>
                                              </span>
                                            </div>
                                        </div>
                                  
                                  <div class="statsUpdate">
                                      <div class="fluid" style="">
                                        <span class="span12 input-autocomplete-container">
                                          <select name="forma_pgto_id" id="forma_pgto_pesq">
                                            <option value="">Forma de pagamento..</option>
                                            <?php
                                            $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                            foreach($array_forma_pgto as $forma_pgto){
                                              echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                            }
                                            ?>
                                          </select>
                                        </span>
                                      </div>
                                  </div>
                                  <div class="statsUpdate statsUpdateCk" align="left">
                                    <input type="checkbox" id="prcl_pesq"> <label for="prcl_pesq">Somente parcelado</label> <div class="clear"></div>
                                  </div>
                                	<div class="statsDropBtn"><a href="javascript://void(0);" class="button greyishB" onClick="filtroSelecionar('listItens5');"><img src="images/icons/light/check.png" class="icon" alt="" /><span>Selecionar</span></a></div>
                            </div>
                        </div>
 	              		</div>
                
              </span>
                    
            </div>
            
            <div class="fluid" align="center" style="padding-bottom:6px;">
            
              <span class="span6" style="text-align:">

                <div class="sItem dropDownList4" >
                    <a href="javascript://void(0);" title="" class="button basic btnDropDown" id="listItens4"><span>Vencimento</span><img src="images/icons/dark/list.png" alt="" class="icon iconList" id="iconList4" style="display:none;"/><div class="NumF countList" id="countList4">2</div></a>
                    <div class="statsDetailed listItens4" >
                         <div class="statsContent" align="left" >

                           		<!--
                              <div class="statsUpdate statsUpdateCk">
                                  <input type="checkbox" id="checkAllTpVenc"> <label for="checkAllTpVenc">Todos</label> <div class="clear"></div>
                              </div>
                              -->
                               
                              <div id="tpVenc">
                                <div class="statsUpdate statsUpdateCk">
                                    <input type="checkbox" value="av" class="checkVenc" id="checkVenc01"> <label for="checkVenc01">À Vencer</label> <div class="clear"></div>
                                </div>    
                                                                     
                                <div class="statsUpdate statsUpdateCk" style=" border-bottom:-20px;">
                                    <input type="checkbox" value="v" class="checkVenc" id="checkVenc02"> <label for="checkVenc02">Vencidos</label> <div class="clear"></div>
                                </div>
                              <div class="statsUpdate statsUpdateCk" align="left">
                                <input type="checkbox" id="compensado_pesq" checked> <label for="compensado_pesq">Lançamentos compensados</label> <div class="clear"></div>
                              </div>
                              <div class="statsUpdate statsUpdateCk" align="left">
                                <input type="checkbox" id="aberto_pesq" checked> <label for="aberto_pesq">Lançamentos em aberto</label> <div class="clear"></div>
                              </div>
                              </div>



                              <div class="statsDropBtn"><a href="javascript://void(0);" class="button greyishB" onClick="filtroSelecionar('listItens4');"><img src="images/icons/light/check.png" class="icon" alt="" /><span>Selecionar</span></a></div>
                        </div>
                    </div>
                  </div>

              </span>
              
              <span class="span6" >
                
                 <a href="javascript://void(0);" title="" class="button greenB" onClick="lancamentosFiltrar();" style="height:26px; width:38px;"><img src="images/icons/light/magnify.png" alt="" class="icon"></a>
                 &nbsp;<a href="javascript://void(0);" title="" class="button redB" onClick="fltLimpar();" style="height:26px; width:38px;"><img src="images/icons/light/close.png" alt="" class="icon"></a>
              
              </span>
                    
            </div>
              
          </div> <!-- Fim FormRowB Fluid -->
              
        </form>
          
      </div> <!-- Fim Buscar período -->
   
    </div> <!-- Fim Fluid -->
 
	</div>

 <!-- Dynamic table -->
  <div class="widget">
     <!-- <div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon" /><h6><span class="blue"> Lançamentos </span> </h6></div> -->
      <div id="lancamentos">

				<?php
                /*
				$dt_ini = date('01/m/Y');
				$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
				$dt_fim = date('d/m/Y',$dt_fim);
				$filtro = array(
					"dt_ativo" =>'periodo',
					"dt_ini" => $dt_ini,
					"dt_fim" => $dt_fim,
					"tp_venc" =>"'av','v'",
					"conta_id" =>"",
					"tp_lnct" =>"'R','P','T'",
					"valor" =>"",
					"centro_resp_id" =>"",
					"plano_contas_id" =>"",		
					"favorecido_id" =>"",
					"documento_id" =>"",
					"forma_pgto_id" =>"",
					"parcelado" => false
				);
				$filtro = json_encode($filtro);
				$lancamento = new Lancamento();
				echo $lancamento->lancamentosListar($db,array('filtro'=>$filtro));
                */
                ?>

		<table class="display dTableLancamentos" id="dTableLnct">
		    <tbody>
		    </tbody>
		</table>

      </div>
  </div>

<!--    <script type="text/javascript" src="http://www.plupload.com/plupload/js/plupload.full.min.js" charset="UTF-8"></script>

<div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
<br />
 
<div id="container">
    <a id="pickfiles" href="javascript:;">[Select files]</a>
    <a id="uploadfiles" href="javascript:;">[Upload files]</a>
</div>
 
<br />
<pre id="console"></pre>
-->

	</div> 
</div>

  <!-- ====== Fim do Palco ====== -->

  <!-- ====== *** UI Dialogs *** ====== -->
  <?php 
	include("lnct_dialogs.php");
  include("dialog_dt_compensacao.php");
	?>
  <!-- ====== *** Fim UI Dialogs *** ====== -->

<?php
echo '<input type="hidden" id="cliente_id" value="'.$_SESSION['cliente_id'].'"/>';
?>
