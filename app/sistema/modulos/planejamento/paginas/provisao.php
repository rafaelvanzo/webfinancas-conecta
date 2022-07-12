<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Provisão</h2>
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
<!--
          <div class="span6">
            <div class="widget">
              <div class="title"><img src="images/icons/dark/magnify.png" alt="" class="titleIcon" /><h6>Orçamento</h6></div>
              <form class="form" id="form_orcamento">
                  <div class="formRowB orct_container">
                    <input type="radio" name="radio_orcamento" value="incluir" onClick="oprSalvar();" checked style="float:left;margin-top:4px;"/> &nbsp;<b>Novo orçamento:</b>
                    <br><br>
                    <input type="text" name="orcamento_novo" id="orcamento_novo" required/>
                    <br><br>
                    <input type="radio" name="radio_orcamento" value="editar" onClick="oprSalvar();" style="float:left;margin-top:4px;"/> &nbsp;<b>Orçamento existente: </b>
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
                    <div style="height:8px">
                    </div>
                  </div>
              </form>
            </div>
          </div>
-->
        	<div class="span12">
            <div class="widget">
		          <div class="title"><img src="images/icons/dark/money.png" alt="" class="titleIcon"><h6>Valores</h6></div>
              <form class="form">
              		<input type="hidden" id="vl_dpre" name="vl_dpre" value=""/>
              		<input type="hidden" id="vl_amrt" name="vl_amrt" value=""/>
              		<input type="hidden" id="vl_trbt" name="vl_trbt" value=""/>
                  <input type="hidden" id="ano_selecionado" name="ano_selecionado" value="<?php echo date('Y');?>"/>

                  <div class="formRowB">

                  	<div class="fluid" align="center">
                    	<strong>Depreciação</strong>
                    </div>

                    <br>
                      
                    <div class="fluid" id="divVlUnico_dpre" style="margin-top: -20px;">
                      
                      <span class="span2">
                        <b>Ano:</b>
                        <?php
												$ano = date("Y");
												$ano_ini = $ano-5;
												$ano_fim = $ano+5;
												echo '
													<select id="ano_dpre" name="ano_dpre" onChange="provisaoExibir(\'1\',this.value,\'dpre\')">
														<option value="">Selecione</option>
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
											<span class="span8">
                      	&nbsp;
                      </span>
                      <span class="span2">
                      	<input type="checkbox" name="vl_unico_check" id="vl_unico_check_dpre" onClick="valUnico('dpre');" style="float: right;"> <b>Valor único:</b>
                        <input type="text" class="moeda" value="0" name="" id="vl_unico_dpre" onBlur="valUnicoAttr('dpre');" readonly/>
                      </span>

                    </div>

                    <br>

                  	<div class="fluid">
                      <span class="span1">
                        Jan:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="jan" id="jan_dpre"/></label>
                      </span>
                      <span class="span1">
                        Fev:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="fev" id="fev_dpre"/></label>
                      </span>
                      <span class="span1">
                        Mar:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="mar" id="mar_dpre"/></label>
                      </span>
                      <span class="span1">
                        Abr:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="abr" id="abr_dpre" /></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Mai:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="mai" id="mai_dpre" /></label>
                      </span>
                      <span class="span1">
                        Jun:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="jun" id="jun_dpre" /></label>
                      </span>
                      <span class="span1">
                        Jul:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="jul" id="jul_dpre" /></label>
                      </span>
                      <span class="span1">
                        Ago:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="ago" id="ago_dpre" /></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Set:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="sete" id="sete_dpre" /></label>
                      </span>
                      <span class="span1">
                        Out:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="outu" id="outu_dpre" /></label>
                      </span>
                      <span class="span1">
                        Nov:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="nov" id="nov_dpre" /></label>
                      </span>
                      <span class="span1">
                        Dez:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-dpre" value="0" name="dez" id="dez_dpre" /></label>
                      </span>
                    </div>
                    <!--
                    <br>
                    <a href="javascript://void(0);" title="" class="button greenB" onClick="plcValIncluir();"><span>Incluír</span></a>
                    -->
                    <br>
                    <div class="fluid">
                    	<span class="span10">
                      	&nbsp;
                      </span>
                      <span class="span2" style="padding-top:;text-align:right">
                        <a href="javascript://void(0);" title="" class="button greenB" onClick="provisaoSalvar('dpre');"><span>Salvar</span></a>
                      </span>
                    </div>
                    
                  </div>

									<div class="formRowB">

                  	<div class="fluid" align="center">
                    	<strong>Amortização</strong>
                    </div>
                    
                    <br>
                    
                    <div class="fluid" id="divVlUnico_amrt" style="margin-top: -20px;">
                      <span class="span2">
                        <b>Ano:</b>
                        <?php
												$ano = date("Y");
												$ano_ini = $ano-5;
												$ano_fim = $ano+5;
												echo '
													<select id="ano_amrt" name="ano_amrt" onChange="provisaoExibir(\'2\',this.value,\'amrt\')">
														<option value="">Selecione</option>
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
											<span class="span8">
	                      &nbsp;
                      </span>
                      <span class="span2">
                      	<input type="checkbox" name="vl_unico_check" id="vl_unico_check_amrt" onClick="valUnico('amrt');" style="float: right;"> <b>Valor único:</b>
                        <input type="text" class="moeda" value="0" name="" id="vl_unico_amrt" onBlur="valUnicoAttr('amrt');" readonly/>
                      </span>
                    </div>
                    
                    <br>
                    
                  	<div class="fluid">
                      <span class="span1">
                        Jan:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="jan" id="jan_amrt" /></label>
                      </span>
                      <span class="span1">
                        Fev:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="fev" id="fev_amrt" /></label>
                      </span>
                      <span class="span1">
                        Mar:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="mar" id="mar_amrt" /></label>
                      </span>
                      <span class="span1">
                        Abr:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="abr" id="abr_amrt" /></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Mai:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="mai" id="mai_amrt" /></label>
                      </span>
                      <span class="span1">
                        Jun:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="jun" id="jun_amrt" /></label>
                      </span>
                      <span class="span1">
                        Jul:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="jul" id="jul_amrt" /></label>
                      </span>
                      <span class="span1">
                        Ago:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="ago" id="ago_amrt" /></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Set:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="sete" id="sete_amrt" /></label>
                      </span>
                      <span class="span1">
                        Out:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="outu" id="outu_amrt" /></label>
                      </span>
                      <span class="span1">
                        Nov:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="nov" id="nov_amrt" /></label>
                      </span>
                      <span class="span1">
                        Dez:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-amrt" value="0" name="dez" id="dez_amrt" /></label>
                      </span>
                    </div>
                    <!--
                    <br>
                    <a href="javascript://void(0);" title="" class="button greenB" onClick="plcValIncluir();"><span>Incluír</span></a>
                    -->
                    <br>
                    <div class="fluid">
                    	<span class="span10">
                      	&nbsp;
                      </span>
                      <span class="span2" style="padding-top:;text-align:right">
                        <a href="javascript://void(0);" title="" class="button greenB" onClick="provisaoSalvar('amrt');"><span>Salvar</span></a>
                      </span>
                    </div>
                  </div>

									<div class="formRowB">

                  	<div class="fluid" align="center">
                    	<strong>Provisões Trabalhistas</strong>
                    </div>

                    <br>
                      
                    <div class="fluid" id="divVlUnico_trbt" style="margin-top: -20px;">
                      
                      <span class="span2">
                        <b>Ano:</b>
                        <?php
												$ano = date("Y");
												$ano_ini = $ano-5;
												$ano_fim = $ano+5;
												echo '
													<select id="ano_trbt" name="ano_trbt" onChange="provisaoExibir(\'3\',this.value,\'trbt\')">
														<option value="">Selecione</option>
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
											<span class="span8">
                      	&nbsp;
                      </span>
                      <span class="span2">
                      	<input type="checkbox" name="vl_unico_check" id="vl_unico_check_trbt" onClick="valUnico('trbt');" style="float: right;"> <b>Valor único:</b>
                        <input type="text" class="moeda" value="0" name="" id="vl_unico_trbt" onBlur="valUnicoAttr('trbt');" readonly/>
                      </span>

                    </div>

                    <br>

                  	<div class="fluid">
                      <span class="span1">
                        Jan:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="jan" id="jan_trbt"/></label>
                      </span>
                      <span class="span1">
                        Fev:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="fev" id="fev_trbt"/></label>
                      </span>
                      <span class="span1">
                        Mar:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="mar" id="mar_trbt"/></label>
                      </span>
                      <span class="span1">
                        Abr:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="abr" id="abr_trbt" /></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Mai:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="mai" id="mai_trbt" /></label>
                      </span>
                      <span class="span1">
                        Jun:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="jun" id="jun_trbt" /></label>
                      </span>
                      <span class="span1">
                        Jul:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="jul" id="jul_trbt" /></label>
                      </span>
                      <span class="span1">
                        Ago:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="ago" id="ago_trbt" /></label>
                      </span>
                    </div>
                    <br>
                  	<div class="fluid">
                      <span class="span1">
                        Set:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="sete" id="sete_trbt" /></label>
                      </span>
                      <span class="span1">
                        Out:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="outu" id="outu_trbt" /></label>
                      </span>
                      <span class="span1">
                        Nov:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="nov" id="nov_trbt" /></label>
                      </span>
                      <span class="span1">
                        Dez:
                      </span>
                      <span class="span2">
                        <input type="text" class="moeda vl-mes-trbt" value="0" name="dez" id="dez_trbt" /></label>
                      </span>
                    </div>
                    <!--
                    <br>
                    <a href="javascript://void(0);" title="" class="button greenB" onClick="plcValIncluir();"><span>Incluír</span></a>
                    -->
                    <br>
                    <div class="fluid">
                    	<span class="span10">
                      	&nbsp;
                      </span>
                      <span class="span2" style="padding-top:;text-align:right">
                        <a href="javascript://void(0);" title="" class="button greenB" onClick="provisaoSalvar('trbt');"><span>Salvar</span></a>
                      </span>
                    </div>

                  </div>

             </form>
  	        </div>
          </div>
				
        </div>

 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php //include("dialog_planoContas_incluir.php"); ?>
  
  <?php //include("dialog_planoContas_editar.php"); ?>
 
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 