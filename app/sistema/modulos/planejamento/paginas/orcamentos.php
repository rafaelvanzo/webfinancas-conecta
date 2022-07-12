 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Orçamentos Financeiros</h2>
            </div>
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
                      <a href="javascript://" style="cursor: default;">Orçamentos Financeiros</a>
                 </li>
            </ul>
	</div>  
</div> Fim Breadcrumbs -->

    <div class="wrapper">
        <div class="divider">
            <span></span>
        </div>
    </div>

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
				<div class="fluid">

          <div class="span6">
            <div class="widget">
              <div class="title"><img src="images/icons/dark/magnify.png" alt="" class="titleIcon" /><h6>Orçamento</h6></div>
              <form class="form" id="form_orcamento">
                  <div class="formRowB orct_container">
                    <input type="radio" name="radio_orcamento" value="incluir" onClick="oprSalvar();" checked style="float:left;margin-top:8px;margin-right:2px;" id="radio-orct-1"/><label for="radio-orct-1"><b>Novo orçamento:</b></label>
                    <br><br>
                    <input type="text" name="orcamento_novo" id="orcamento_novo" required/>
                    <!--
                    <br><br>
                    <a href="javascript://void(0);" title="" class="button greenB" id="btnSalvar" onClick="orcamentosIncluir();"><span>Salvar</span></a>
										-->
                    <br><br>
                    <input type="radio" name="radio_orcamento" value="editar" onClick="oprSalvar();" style="float:left;margin-top:8px;margin-right:2px;" id="radio-orct-2"/><label for="radio-orct-2"><b>Orçamento existente:</b></label>
                    <br><br>
                    <div class="fluid">
                      <span class="span12 input-autocomplete-container">
                        <input type="text" name="orct_id" id="orcamento_buscar" class="orcamentos_buscar input-buscar" required disabled/>
                        <input type="hidden" name="orcamento_id" id="orct_id" value="0"/>
                        <input type="hidden" name="dscr_ini" id="dscr_ini" value=""/>
                      </span>
                    </div>
                    <br>
                    <a href="javascript://void(0);" title="" class="button greenB" onClick="orcamentosSalvar();"><span>Salvar</span></a>
                    <a href="javascript://void(0);" title="" class="button redB" onClick="orcamentosExcluir();"><span>Excluír</span></a>
                      <a href="javascript://void(0);" title="" class="button blueB" onClick="orcamentosLimpar();"><span>Limpar</span></a>
                    <div style="height:8px">
                    </div>
                  </div>
              </form>
            </div>
          </div>

        	<div class="span6">
            <div class="widget">
		          <div class="title"><img src="images/icons/dark/money.png" alt="" class="titleIcon"><h6>Valores</h6></div>
              <form class="form">
                  <input type="hidden" id="conta_id" value=""/>
              		<input type="hidden" id="valores" name="valores" value=""/>
                  <input type="hidden" id="ano_selecionado" name="ano_selecionado" value="<?php echo date('Y');?>"/>
                  <div class="formRowB">
                  	<div class="fluid" id="contaNome">
                    	<!--<b>Nome da conta</b>-->
                    </div>
                    <br>
                    <div class="fluid" id="divVlUnico" style="margin-top: -20px;">
                      <span class="span3">
                        <b>Ano:</b>
                        
                        <?php
												$ano = date("Y");
												$ano_ini = $ano-5;
												$ano_fim = $ano+5;
												echo '
													<select id="ano" name="ano" onChange="vlPlcExibir();">
												';
												while($ano_ini<=$ano_fim){
													echo '<option value="',$ano_ini,'">',$ano_ini,'</option>';
													$ano_ini++;
												}
												echo '
													</select>
												';
												?>
                      </span>
                      <span class="span5">
                        &nbsp;
                      </span>                      
                      <span class="span4">
                      	<input type="checkbox" name="vl_unico_check" id="vl_unico_check" onClick="valUnico();" style="float: right;"> <b>Valor único:</b>

                        <input type="text" class="moeda" value="0" name="" id="vl_unico" onBlur="valUnicoAttr();vlAnualAtualizar();" readonly/>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Jan:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="jan" id="jan" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Fev:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="fev" id="fev" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Mar:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="mar" id="mar" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Abr:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="abr" id="abr" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Mai:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="mai" id="mai" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Jun:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="jun" id="jun" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Jul:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="jul" id="jul" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Ago:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="ago" id="ago" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Set:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="sete" id="sete" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Out:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="outu" id="outu" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Nov:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="nov" id="nov" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                      <span class="span1">
                        Dez:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes" value="0" name="dez" id="dez" onBlur="vlAnualAtualizar();"/></label>
                      </span>
                    </div>
                    <!--
                    <br>
                    <a href="javascript://void(0);" title="" class="button greenB" onClick="plcValIncluir();"><span>Incluír</span></a>
                    -->
                  </div>
             </form>
  	        </div>
          </div>
        
        </div>
        
        <div class="widget">


            <div id="planoContas">

              
              
              <?php
              //start: busca plano de contas ordenado
              $maiorNivel = $db->fetch_assoc('select max(nivel) nivel from plano_contas');
              $maiorNivel = $maiorNivel['nivel'];

              $ordem = '';
              $orderBy = '';

              if($maiorNivel>1){
                  
                  $arrayOrderBy = array();
                  $arrayOrdem = array();

                  for($i=2;$i<$maiorNivel;$i++){
                      array_push($arrayOrderBy,'ordem'.$i);
                      array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_conta,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
                  }

                  array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_conta,".",-1) as decimal),0) as ordem'.$i);
                  array_push($arrayOrderBy,'ordem'.$i);

                  $ordem = ','.join(',',$arrayOrdem);
                  $orderBy = ','.join(',',$arrayOrderBy);
              }

              $array_plano_contas = $db->fetch_all_array('
                select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as decimal) as ordem1'.$ordem.'
                from plano_contas 
                where cod_conta > 0 
                order by ordem1'.$orderBy);
              //end: busca plano de contas ordenado			  


              $categorias = '';

              $countCheckBox = 1;
              $countCategoria = 1;

              foreach($array_plano_contas as $planoContas){

                  $espc = $planoContas['nivel'] * 10;  $espc = $espc.'px';
                  if($planoContas['nivel'] == 1){ $strong = 'bold;'; $fontN = '16px;';  }else{ $strong = 'normal;'; $fontN = '12px;'; }

                    if($planoContas['tp_conta']==1){

                        $checkBox = '
                        <div class="tbWFoption" style="width:20px;margin-top:14px;">										
						    <input type="radio" name="conta_id" class="cntRadio" id="cntRadio'.$countCheckBox.'" value="'.$planoContas['id'].'" data-contador-categoria="'.$countCategoria.'" onclick="contaSelecionar(\''.$planoContas['id'].'\',\'<b>'.$planoContas['cod_conta'].'</b> - '.$planoContas['nome'].'\')"/> 
					    </div>
                        <div class="tbWFvalue">R$ <b id="vl_'.$planoContas['id'].'" class="vl_conta_anual">0,00</b></div>
                        ';

                        $countCheckBox ++;

                    }else{
                        $checkBox = '<div class="tbWFvalue">&nbsp;</div>';
                    }
                    
                    $categorias .= '
								 
				    <tr class="gradeA">

                        <td style="display:none;">'.$countCategoria.'</td>

					    <td class="updates newUpdate">
												
					        <div class="uDate tbWF tipS" original-title="Plano de Contas" align="center" style="width:auto; padding-left:'.$espc.'"> 
                                <span class="uDay " style="font-size:'.$fontN.$cor.'" id="codConta'.$countCategoria.'">'.$planoContas['cod_conta'].' - </span>
                            </div>

                            <span class="lDespesa tbWF" style="margin-top:2px; font-size:14px;">
								<a href="javascript://void(0);" style="cursor: default; font-size:'.$fontN.' font-weight:'.$strong.$cor.'" original-title="Descrição" class="tipS" id="cntNome'.$countCategoria.'">'.$planoContas['nome'].'</a>													
							</span>	

						    '.$checkBox.'

					    </td>
				    </tr>
                    ';

                    $countCategoria++;
              }
              ?>

                <table class="display tblplanoContas">
				    <thead>
				        <tr style="border-bottom: 1px solid #e7e7e7;">
				            <th style="display:none;"></th>
                            <th> 
				                <table width="100%">
                                    <tr>
						                <td>Categorias</td>
						                <td width="60">Opções</td>
					                </tr>
				                </table>
				            </th> 
				        </tr>
				    </thead>
				    <tbody>
					    <?php echo $categorias; ?>
				    </tbody>
			    </table>

            </div>
        </div>
 
	</div>