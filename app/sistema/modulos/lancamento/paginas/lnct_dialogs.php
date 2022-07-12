    <!--
    ======================================================================
    INCLUIR RECEBIMENTOS
    ======================================================================
    -->
    <div id="dialog-rcbt" style=" height:auto; padding:0;" title="" class="modal">

    <form id="form_rcbt" class="dialog">

    <input type="hidden" name="funcao" id="form_rcbt_funcao" value="">
    <input type="hidden" name="lancamento_id" id="form_rcbt_lancamento_id" value="">
    <input type="hidden" name="conta_id_ini" id="form_rcbt_conta_id_ini" value="">
    <input type="hidden" name="valor_ini" id="form_rcbt_valor_ini" value="">
    <input type="hidden" name="tipo" id="form_rcbt_tipo_lnct" value="R">
    <!--<input type="hidden" name="compensado" id="form_rcbt_compensado" value="0">-->
    <input type="hidden" name="ct_resp_lancamentos" id="form_rcbt_ctr_plc_lnct" value="">
    
	    <!-- campos adicionais para lançamento recorrente -->
    <input type="hidden" name="lancamento_recorrente_id" id="form_rcbt_lnct_rcr_id" value="">
    <input type="hidden" name="dia_mes" id="form_rcbt_dia_mes" value="">
    <input type="hidden" name="dt_venc_ref" id="form_rcbt_dt_venc_ref" value="">
    <input type="hidden" name="qtd_dias" id="form_rcbt_qtd_dias" value="">
    <input type="hidden" name="frequencia" id="form_rcbt_frequencia" value="">
    <input type="hidden" name="cod_banco" id="form_rcbt_cod_banco" value="">

    <div class="fluid">

        <div class="span12">

            <div class="tab-bs">
               
                <ul class="nav nav-tabs" id="abas-form_rcbt">
			        <li class="active"><a data-target="#aba-lnct" class="lnctValid-R" data-toggle="tab">Lançamento</a></li>
			        <li><a data-target="#aba-ctr-plc" class="catCtValid-R" data-toggle="tab">Categoria / Centro de Custo</a></li>
			    </ul>

                <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			    <div class="tab-content">

                    <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane active" id="aba-lnct"> 

                        <div class="formRow">
                            <span class="span6">
                                <label>Descrição:</label>
                                <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="form_rcbt_dscr" />
                            </span>
                            <span class="span6 input-autocomplete-container">
                                <label>Receber de:</label>
                                <input style="margin-left: 0px;" type="text" name="form_rcbt_favorecido_id" id="form_rcbt_favorecido" value="" class="favorecido_buscar input-buscar required" placeholder="Preencha para localizar..." data-tp-lnct="R" data-form-id="form_rcbt" autocomplete="off" />
                                <input type="hidden" name="favorecido_id" id="form_rcbt_favorecido_id" value="" />
                            </span>
                        </div>

                        <div class="formRow">
                            <span class="span6 input-autocomplete-container">
                                <label>Conta financeira:</label>
                                <input style="margin-left: 0px;" type="text" name="form_rcbt_conta_id" id="form_rcbt_conta" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." autocomplete="off" />
                                <input type="hidden" name="conta_id" value="" id="form_rcbt_conta_id" />
                            </span>
                            <span class="span2">
                                <label>Vencimento:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_vencimento" id="form_rcbt_dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" />
                            </span>
                            <span class="span4">
                                <label>Valor:</label>
                                <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_rcbt_valor" class="required moeda" onBlur="vlParcelaPcCrAtualizar('form_rcbt');plcCtrValorAtualizar('form_rcbt');" value="" />
                            </span>
                        </div>



                        <div class="formRow">
                            <span class="span6 input-autocomplete-container">
                                <label>Beneficiário do serviço:</label>
                                <input style="margin-left: 0px;" type="text" name="form_rcbt_favorecido_dep_id" id="form_rcbt_favorecido_dep" value="" class="favorecido_buscar input-buscar" placeholder="Preencha para localizar..." data-tp-lnct="R" data-form-id="form_rcbt" autocomplete="off"/>
                                <input type="hidden" name="favorecido_id_dep" id="form_rcbt_favorecido_dep_id" value="0" />
                            </span>
                            
                            <span class="span2" style="padding-left:12px;">
                                <label>Recebido:</label>
                                <input type="checkbox" name="compensado" data-dt-id="form_rcbt_dt_compensacao" value="1" class="ckb-compensado" id="form_rcbt_compensado" style="margin-top: -5px; margin-left: 15px;"/>
                            </span>
                            <span class="span2" >
                                <label>Compensação:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_compensacao" id="form_rcbt_dt_compensacao" value="" class="required datepicker maskDate" disabled />
                                <!--<a href="#" onclick="javascript:recibo('form_rcbt');">Recibo</a>-->                               
                            </span> 
                            <span class="span2">
                                <a href="javascript://void(0);" onclick="javascript:recibo('form_rcbt','R');" title="" class="wContentButton basic" style="margin-top:25px; width:35px;">Recibo</a>
                            </span>                           
                        </div>



                        <div class="formRow">
                            <span class="span12">
                                <br />
                                <!--<label>Arquivos:</label>-->
                                <div id="form_rcbt_container">
                                    <!--
                            <a id="pickfiles" href="javascript:;" >Anexar</a>
                            <a id="form_rcbt_uploadfiles" href="javascript:;" style="display:none;"></a>
                            -->
                                </div>
                                <div id="form_rcbt_filelist" class="controlB scroll bgUpload tipN" original-title="Clique em anexar ou arraste os arquivos até aqui." align="left">
                                </div>
                                <a id="form_rcbt_pickfiles" href="javascript:;">
                                    <div class="vertical-text">
                                        Anexar &nbsp;<img src="images/icons/dark/paperclip.png" class="clips" align="middle" />
                                    </div>
                                </a>
                                <!--<pre id="console"></pre>    -->
                            </span>                            
                           
                        </div>

                        <div class="title closed inactive MaisOpcoes" align="left" > 
                        <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  
                        </div>

                        <div class="body" style="display: block;"> <!-- Body Mais Opções -->

                            <div class="formRow">

                                <span class="span2">
                                    <label style="width:100%; text-align:left;">Competência:</label>
                                    <input style="margin-left: 0px; text-align:center;" type="text" name="dt_competencia" id="form_rcbt_dt_competencia" value="<?php echo date('m/Y')?>" class="required monthpicker" readonly />
                                </span>
                                <span class="span2">
                                    <label>Emissão:</label>
                                    <input style="margin-left: 0px;" type="text" name="dt_emissao" id="form_rcbt_dt_emissao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" />
                                </span>
                                <span class="span3">
                                    <label>Documento:</label>
                                    <select name="documento_id" id="form_rcbt_documento_id">
                                        <option value=""></option>
                                        <?php
                                    $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                    foreach($array_documentos as $documento){
                                    echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                    }
                                        ?>
                                    </select>
                                </span>
                                <span class="span3">
                                    <label>Forma de pagamento:</label>
                                    <select name="forma_pgto_id" id="form_rcbt_forma_pgto_id">
                                        <option value=""></option>
                                        <?php
                                $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                foreach($array_forma_pgto as $forma_pgto){
                                    echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                }
                                        ?>
                                    </select>
                                </span>
                                <span class="span2">
                                    <label>Compensação:</label>
                                    <select name="auto_lancamento" id="form_rcbt_auto_lancamento">
                                        <option value="M">Manual</option>
                                        <option value="A">Automática</option>
                                    </select>
                                </span>

                            </div>

                            <div class="linha" style="margin-top:10px;" id="form_rcbt_linha_01"></div>

                            <div class="formRow">
                                        <!--
                                <span class="span3">
                                    <label>Sábados e Domingos:</label>
                                    <select name="sab_dom" id="form_rcbt_sab_dom">
                                        <option value="0">Desconsiderar</option>
                                        <option value="1">Considerar</option>
                                    </select>
                                </span>
                                            -->
                                <span class="span4" id="form_rcbt_span_frequencia">
                                    <label>Frequência:</label>
                                    <select name="frequencia" onChange="frequenciaAtualizar(this,'form_rcbt');">
                                        <option value="P"> Personalizado </option>
                                        <option value="1"> Diariamente </option>
                                        <option value="7"> Semanal </option>
                                        <option value="30" selected="selected"> Mensal </option>
                                        <option value="60"> Bimestral </option>
                                        <option value="90"> Trimestral </option>
                                        <option value="120"> Quadrimestral </option>
                                        <option value="180"> Semestral </option>
                                        <option value="360"> Anual </option>
                                    </select>
                                </span>
                                <span class="span1" id="form_rcbt_span_dias" style="display:none">
                                    <label>Dias:</label>
                                    <input style="margin-left: 0px;" type="text" name="qtd_dias" value="1" class="required maskNum" />
                                </span>
                                <span class="span3" id="form_rcbt_span_parcelas">
                                    <label>Parcelas:</label>
                                    <input style="margin-left: 0px; text-align:right;" type="text" name="qtd_parcelas" id="form_rcbt_qtd_parcelas" value="1" class="required maskNum" onBlur="valorParcelaAtualizar('form_rcbt')" />
                                </span>
                                <span class="span4" id="form_rcbt_span_vl_parcela">
                                    <label>Valor da parcela:</label>
                                    <input style="margin-left: 0px; text-align:right;" type="text" name="valor_parcela" id="form_rcbt_valor_parcela" class="required moeda" value="0,00" readonly />
                                </span>

                            </div>

                                    <!--
                            <div class="linha" style="margin-top:10px;"></div>

                                <div class="formRow">
                                    <span class="span4">
                                    <label>Juros:</label>
                                    <input style="margin-left: 0px;text-align:right;" type="text" name="vl_juros" id="form_rcbt_vl_juros" class="moeda" value=""/>
                                    </span>
                                    <span class="span4">
                                    <label>Multa:</label>
                                    <input style="margin-left: 0px;text-align:right;" type="text" name="vl_multa" id="form_rcbt_vl_multa" class="moeda" value=""/>
                                    </span>
                                    <span class="span4">
                                    <label>Desconto:</label>
                                    <input style="margin-left: 0px;text-align:right;" type="text" name="vl_desconto" id="form_rcbt_vl_desconto" class="moeda" value=""/>
                                    </span>

                            </div>
                            -->

                            <div class="linha" style="margin-top:10px;"></div>  <!-- Linha deve estar no ultimo formRow -->

                            <div class="formRow">
                                <span class="span12">
                                    <label>Observação:</label>
                                    <textarea name="observacao" rows="4" cols="auto" id="form_rcbt_obs"></textarea>
                                </span>
                            </div>

                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                        </div>    <!-- Fim do Body Mais Opções -->  

			        </div> 
                    
                    <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                    <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane" id="aba-ctr-plc">
                      
                            <div class="formRow">

                                <span class="span4 input-autocomplete-container">
                                    <label>Categoria:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_rcbt_pl_conta_id" id="form_rcbt_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar.." size="" />
                                    <input type="hidden" id="form_rcbt_pl_conta_id" value="0" />
                                </span>
                                <span class="span4 input-autocomplete-container">
                                    <label>Centro de custo:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_rcbt_ct_resp_id" id="form_rcbt_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar.." size="" />
                                    <input type="hidden" id="form_rcbt_ct_resp_id" value="0" />
                                </span>


                                <span class="span2 pc-cr-valor-span">
                                    <label>Valor:</label>
                                    <input style="" type="text" id="form_rcbt_ct_resp_valor" value="0,00" class="required moeda" onblur="valorPorcentagem('form_rcbt');" />
                                </span>
                                <span class="span1 pc-cr-prct-span">
                                    <label>(%):</label>
                                   <input style="" type="text" name="porcentagem" id="form_rcbt_ctr_plc_prct" value="100" class="required porcentagem" onblur="porcentagemValor('form_rcbt');" onkeyup="FormatarPorcentagem('form_rcbt')"/>
                                </span>
                  
                                <span class="span1 pc-cr-incluir-span">
                                    <a href="javascript://void(0);" title="Incluír" class="button blueB" style="margin-top:-3px; height:26px; width:38px;" onClick="centroRespLnctIncluir('form_rcbt');"> <img src="images/icons/light/add.png" style="margin-top:5px;" class="icon"></a>
                                </span>

                            </div>

                            <div class="formRow ctr_plc_container">

                            </div>

                            <div class="linha" style="margin-top:10px;"></div>  <!-- Linha deve estar no ultimo formRow -->
                                            
			        </div>

                    <!-- fim aba 2 --------------------------------------------------------------------------------------------------------------->

			    </div>

            </div>

        </div>
                 
    </div>  <!-- fluid -->
                   
    </form> 
               
    </div><!-- Fim dialog --> 

    <!--
    ======================================================================
    INCLUIR PAGAMENTOS
    ======================================================================
    -->
    <div id="dialog-pgto" style="height:auto; padding:0;" title="" class="modal">

    <form id="form_pgto" class="dialog">

    <input type="hidden" name="funcao" id="form_pgto_funcao" value="">
    <input type="hidden" name="lancamento_id" id="form_pgto_lancamento_id" value="">
    <input type="hidden" name="conta_id_ini" id="form_pgto_conta_id_ini" value="">
    <input type="hidden" name="valor_ini" id="form_pgto_valor_ini" value="">
    <input type="hidden" name="tipo" id="form_pgto_tipo_lnct" value="P">
    <!--<input type="hidden" name="compensado" id="form_pgto_compensado" value="0">-->
    <input type="hidden" name="ct_resp_lancamentos" id="form_pgto_ctr_plc_lnct" value="">

	    <!-- campos adicionais para lançamento recorrente -->
    <input type="hidden" name="lancamento_recorrente_id" id="form_pgto_lnct_rcr_id" value="">
    <input type="hidden" name="dia_mes" id="form_pgto_dia_mes" value="">
    <input type="hidden" name="dt_venc_ref" id="form_pgto_dt_venc_ref" value="">
    <input type="hidden" name="qtd_dias" id="form_pgto_qtd_dias" value="">
    <input type="hidden" name="frequencia" id="form_pgto_frequencia" value="">

    <div class="fluid">

        <div class="span12">

            <div class="tab-bs">
               
                <ul class="nav nav-tabs" id="abas-form_pgto">
			        <li class="active"><a data-target="#aba-lnct-pgto" class="lnctValid-P" data-toggle="tab">Lançamento</a></li>
			        <li><a data-target="#aba-ctr-plc-pgto" class="catCtValid-P"  data-toggle="tab">Categoria / Centro de Custo</a></li>
			    </ul>

                <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			    <div class="tab-content">

                    <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane active" id="aba-lnct-pgto">

                        <div class="formRow">
                            <span class="span6">
                                <label>Descrição:</label>
                                <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="form_pgto_dscr"/>
                            </span>
                                <span class="span6 input-autocomplete-container">
                                <label>Pagar para:</label>
                                <input style="margin-left: 0px;" type="text" name="form_pgto_favorecido_id" id="form_pgto_favorecido" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." data-tp-lnct="P" data-form-id="form_pgto"/>
                                <input type="hidden" name="favorecido_id" id="form_pgto_favorecido_id" value=""/>
                            </span>
                            </div>

                            <div class="formRow">
                                <span class="span6 input-autocomplete-container">
                                <label>Conta financeira:</label>
                                <input style="margin-left: 0px;" type="text" name="form_pgto_conta_id" id="form_pgto_conta" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                <input type="hidden" name="conta_id" value="" id="form_pgto_conta_id"/>
                            </span>
                                <span class="span2">
                                <label>Vencimento:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_vencimento" id="form_pgto_dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate"/>
                            </span>
                                <span class="span4">
                                <label>Valor:</label>
                                <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_pgto_valor" class="required moeda" onBlur="vlParcelaPcCrAtualizar('form_pgto');plcCtrValorAtualizar('form_pgto');" value=""/>
                            </span>
                        </div>

                            <div class="formRow">
                            <span class="span8" >
           
                                <br />
                                <!--<label>Arquivos:</label>-->
                                <div id="form_pgto_container" >
                                <!--
                                <a id="pickfiles" href="javascript:;" >Anexar</a>
                                <a id="form_pgto_uploadfiles" href="javascript:;" style="display:none;"></a>
                                -->
                                </div>
             
                                <div id="form_pgto_filelist" class="controlB scroll bgUpload tipN" original-title="Clique em anexar ou arraste os arquivos até aqui." align="left">	
                                </div>
                                <a id="form_pgto_pickfiles" href="javascript:;" >
                                <div class="vertical-text" >Anexar &nbsp;<img src="images/icons/dark/paperclip.png" class="clips" align="middle"/>
                                </div>
                                </a>
                                <!--<pre id="console"></pre>-->
                            </span>
                            <span class="span2">
                                <label>Pago: &nbsp;&nbsp;&nbsp;</label>
                                <input type="checkbox" name="compensado" data-dt-id="form_pgto_dt_compensacao" value="1" class="ckb-compensado" id="form_pgto_compensado"/>
                            </span>
                            <span class="span2">
                                <label>Compensação:</label>
                                <input style="margin-right: 0px;" type="text" name="dt_compensacao" id="form_pgto_dt_compensacao" value="" class="required datepicker maskDate" disabled/>
                                <a href="javascript://void(0);" onclick="javascript:recibo('form_pgto','P');" title="" class="wContentButton basic" style="margin:-2px 2px -2px 6px; width:35px;">Recibo</a>
                            </span>
                        </div>
      
                        <!--============ MAIS OPÇÕES ============-->
                        <div class="title closed inactive MaisOpcoes" align="left" > 
                            <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  
                        </div>

                        <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
	                    <div class="formRow">
	                        <span class="span2">
                                    <label style="width:100%; text-align:left;">Competência:</label>
                                    <input style="margin-left: 0px; text-align:center;" type="text" name="dt_competencia" id="form_pgto_dt_competencia" value="<?php echo date('m/Y')?>" class="required monthpicker" readonly/>
                            </span>
                            <span class="span2">
                                <label>Emissão:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_emissao" id="form_pgto_dt_emissao" value="<?php echo date('d/m/Y')?>" class="datepicker maskDate"/>
 	                        </span>
                            <span class="span3">
                                <label>Documento:</label>
                                <select name="documento_id" id="form_pgto_documento_id">
                                    <option value=""></option>
                                    <?php
                                    $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                    foreach($array_documentos as $documento){
                                    echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                    }
                                    ?>
                                </select>
                            </span>
                            <span class="span3">
                                <label>Forma de pagamento:</label>
                                <select name="forma_pgto_id" id="form_pgto_forma_pgto_id">
                                <option value=""></option>
                                <?php
                                $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                foreach($array_forma_pgto as $forma_pgto){
                                    echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                }
                                ?>
                                </select>
                            </span>  
                            <span class="span2">
                                <label>Compensação:</label>
                                <select name="auto_lancamento" id="form_pgto_auto_lancamento">
                                <option value="M">Manual</option>
                                <option value="A">Automática</option>
                                </select>
                            </span>      

                        </div>

                        <div class="linha" style="margin-top:10px;" id="form_pgto_linha_01"></div>

                        <div class="formRow">

			                        <!--
                            <span class="span3">
                                <label>Sábados e Domingos:</label>
                                <select name="sab_dom" id="form_pgto_sab_dom">
                                <option value="0">Desconsiderar</option>
                                <option value="1">Considerar</option>
                                </select>
                            </span>
                            -->
                            <span class="span4" id="form_pgto_span_frequencia">
                            <label>Frequência:</label>
                            <select name="frequencia" onChange="frequenciaAtualizar(this,'form_pgto');">
                                <option value="P"> Personalizado </option>
                                <option value="1"> Diariamente </option>
                                <option value="7"> Semanal </option>
                                <option value="30" selected="selected"> Mensal </option>
                                <option value="60"> Bimestral </option>
                                <option value="90"> Trimestral </option>
                                <option value="120"> Quadrimestral </option>
                                <option value="180"> Semestral </option>
                                <option value="360"> Anual </option>
                            </select>
                            </span>
                            <span class="span1" id="form_pgto_span_dias" style="display:none">
                            <label>Dias:</label>
                            <input style="margin-left: 0px;" type="text" name="qtd_dias" value="1" class="required maskNum"/>
                            </span>
                            <span class="span4" id="form_pgto_span_parcelas">
                            <label>Parcelas:</label>
                            <input style="margin-left: 0px; text-align:right;" type="text" name="qtd_parcelas" id="form_pgto_qtd_parcelas" value="1" class="required maskNum" onBlur="valorParcelaAtualizar('form_pgto')"/>
                            </span>
                            <span class="span4" id="form_pgto_span_vl_parcela">
                                <label>Valor da parcela:</label>
                                <input style="margin-left: 0px; text-align:right;" type="text" name="valor_parcela" id="form_pgto_valor_parcela" class="required moeda" value="0,00" readonly/>
                            </span>
         
                        </div>

		                        <!--
                        <div class="linha" style="margin-top:10px;"></div>
       
                        <div class="formRow">
                            <span class="span4">
                            <label>Juros:</label>
                            <input style="margin-left: 0px;text-align:right;" type="text" name="vl_juros" id="form_pgto_vl_juros" class="moeda" value=""/>
                            </span>
                            <span class="span4">
                            <label>Multa:</label>
                            <input style="margin-left: 0px;text-align:right;" type="text" name="vl_multa" id="form_pgto_vl_multa" class="moeda" value=""/>
                            </span>
                            <span class="span4">
                            <label>Desconto:</label>
                            <input style="margin-left: 0px;text-align:right;" type="text" name="vl_desconto" id="form_pgto_vl_desconto" class="moeda" value=""/>
                            </span>
                        </div>
		                        -->
      
                        <div class="linha" style="margin-top:10px;"></div>

                        <div class="formRow">
                            <span class="span12">
                            <label>Observação:</label>
                                <textarea name="observacao" rows="4" cols="auto" id="form_pgto_obs"></textarea> 
                            </span>                                                                                                    
                        </div>
       
                           
                        <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->             

                        </div>    <!-- Fim do Body Mais Opções -->                         

			        </div> 
                    
                    <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                    <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane" id="aba-ctr-plc-pgto">
                      
                            <div class="formRow">

                                <span class="span4 input-autocomplete-container">
                                    <label>Categoria:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_pgto_pl_conta_id" id="form_pgto_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar.." size="" />
                                    <input type="hidden" id="form_pgto_pl_conta_id" value="0" />
                                </span>
                                <span class="span4 input-autocomplete-container">
                                    <label>Centro de custo:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_pgto_ct_resp_id" id="form_pgto_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar.." size="" />
                                    <input type="hidden" id="form_pgto_ct_resp_id" value="0" />
                                </span>

                                <span class="span2 pc-cr-valor-span">
                                    <label>Valor:</label>
                                    <input style="" type="text" id="form_pgto_ct_resp_valor" value="0,00" class="required moeda" onblur="valorPorcentagem('form_pgto');" />
                                </span>
                                <span class="span1 pc-cr-prct-span">
                                    <label>(%):</label>
                                    <input style="" type="text" name="porcentagem" id="form_pgto_ctr_plc_prct" value="100" class="required porcentagem" onblur="porcentagemValor('form_pgto');" onkeyup="FormatarPorcentagem('form_pgto')"/>
                                </span>
                  
                                <span class="span1 pc-cr-incluir-span">
                                    <a href="javascript://void(0);" title="Incluír" class="button blueB" style="margin-top:-3px;height:26px; width:38px;" onClick="centroRespLnctIncluir('form_pgto');"><img src="images/icons/light/add.png" style="margin-top:5px;" class="icon"></a>
                                </span>

                            </div>

                            <div class="formRow ctr_plc_container">

                            </div>

                            <div class="linha" style="margin-top:10px;"></div>  <!-- Linha deve estar no ultimo formRow -->
                                            
			        </div>

                    <!-- fim aba 2 --------------------------------------------------------------------------------------------------------------->

			    </div>

            </div>

        </div>
                 
    </div>  <!-- fluid -->
                   
    </form> 
               
    </div><!-- Fim dialog --> 

    <!-- 
    ======================================================================
    INCLUIR TRANSFERÊNCIA
    ======================================================================
    -->
    <div id="dialog-trsf" style="height:auto; padding:0;" title="" class="modal">

        <form id="form_trsf" class="dialog">

            <input type="hidden" name="funcao" id="form_trsf_funcao" value="">
            <input type="hidden" name="lancamento_id" id="form_trsf_lancamento_id" value="">
            <input type="hidden" name="valor_ini" id="form_trsf_valor_ini" value="">
            <input type="hidden" name="conta_id_origem_ini" id="form_trsf_conta_id_origem_ini" value="">
            <input type="hidden" name="conta_id_destino_ini" id="form_trsf_conta_id_destino_ini" value="">
            <input type="hidden" name="tipo" id="form_trsf_tipo_lnct" value="T">
            <!--<input type="hidden" name="compensado" id="form_trsf_compensado" value="0">-->
            <input type="hidden" name="sab_dom" value="1"> <!--geralmente uma transferencia entre contas do mesmo titular são realizadas qualquer dia-->

            <div class="fluid">    
    
	            <div class="formRow">
                    <span class="span12">
                        <label>Descrição:</label>
                        <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="form_trsf_dscr"/>
                    </span>
                </div>

                <div class="formRow">
                    <span class="span6 input-autocomplete-container">
                        <label>Conta financeira de origem:</label>
                        <input style="margin-left: 0px;" type="text" name="form_trsf_conta_id_origem" id="form_trsf_conta_origem" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                        <input type="hidden" name="conta_id_origem" value="" id="form_trsf_conta_id_origem"/>
                    </span>
                    <span class="span6 input-autocomplete-container">
                        <label>Conta financeira de destino:</label>
                        <input style="margin-left: 0px;" type="text" name="form_trsf_conta_id_destino" id="form_trsf_conta_destino" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                        <input type="hidden" name="conta_id_destino" value="" id="form_trsf_conta_id_destino"/>
                    </span>            
                </div>
        
                <div class="formRow">
                    <span class="span2">
                        <label style="width:100%; text-align:left;">Competência:</label>
                        <input style="margin-left: 0px;text-align:center;" type="text" name="dt_competencia" id="form_trsf_dt_competencia" value="<?php echo date('m/Y')?>" class="required monthpickerReport" readonly>
                    </span>
                    <span class="span2">
                        <label style="width:100%; text-align:left;">Emissão:</label>
                        <input style="margin-left: 0px;text-align:center;" type="text" name="dt_emissao" id="form_trsf_dt_emissao" value="<?php echo date('d/m/Y')?>" class="datepicker">
                    </span>
                    <span class="span2">
                    <label style="width:100%; text-align:left;">Vencimento:</label>
                        <input style="margin-left: 0px;" type="text" name="dt_vencimento" id="form_trsf_dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate"/>
                    </span>
                    <span class="span6">
                        <label>Valor:</label>
                        <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_trsf_valor" value="" class="required moeda"/>
                    </span>        
                </div>

                <div class="formRow">
                    <span class="span8" >
                    <br />
                    <!--<label>Arquivos:</label>-->
                    <div id="form_trsf_container" >
                        <!--
                        <a id="pickfiles" href="javascript:;" >Anexar</a>
                        <a id="form_trsf_uploadfiles" href="javascript:;" style="display:none;"></a>
                        -->
                    </div>
                    <div id="form_trsf_filelist" class="controlB scroll bgUpload tipN" original-title="Clique em anexar ou arraste os arquivos até aqui." align="left">	
                    </div>
                    <a id="form_trsf_pickfiles" href="javascript:;" >
                        <div class="vertical-text" >Anexar &nbsp;<img src="images/icons/dark/paperclip.png" class="clips" align="middle"/>
                        </div>
                    </a>
                    <!--<pre id="console"></pre>    -->
                    </span>
                    <span class="span2">
                        <label>Transferido:</label>
                        <input type="checkbox" name="compensado" data-dt-id="form_trsf_dt_compensacao" value="1" class="ckb-compensado" id="form_trsf_compensado"/>
                    </span>
                    <span class="span2">
                    <label style="width:100%; text-align:left;">Compensação:</label>
                        <input style="margin-right: 0px;" type="text" name="dt_compensacao" id="form_trsf_dt_compensacao" value="" class="required datepicker maskDate"/>
                    </span>
                </div>

                <div class="formRow">
                    <span class="span12">
                    <label>Observação:</label>
                        <textarea name="observacao" id="form_trsf_obs" rows="4" cols="auto"></textarea>
                    </span>                                                                                                    
                </div>
       
                <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->             
            </div>    <!-- fluid -->                     
                 
        </form> 
                   
    </div><!-- Fim dialog --> 
