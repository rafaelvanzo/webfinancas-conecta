<!--
======================================================================
INCLUIR RECEBIMENTOS RECORRENTES
======================================================================
-->

<div id="dialog-rcbt-rcr-incluir" style="height:auto; padding:0;" title="Novo Recebimento Recorrente" class="modal">

    <form id="form_rcbt" class="dialog">

    <input type="hidden" name="funcao" value="lancamentoIncluir">
    <input type="hidden" name="tipo" value="R">
	<input type="hidden" name="ct_resp_lancamentos" id="form_rcbt_ctr_plc_lnct" value="">

    <div class="fluid">

        <div class="span12">

            <div class="tab-bs">
               
                <ul class="nav nav-tabs" id="abas-form_rcbt">
			        <li class="active lnctValid"><a data-target="#aba-lnct-rcbt" data-toggle="tab">Lançamento</a></li>
			        <li><a data-target="#aba-ctr-plc-rcbt" data-toggle="tab">Categoria / Centro de Custo</a></li>
			    </ul>

                <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			    <div class="tab-content">

                    <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane active" id="aba-lnct-rcbt"> 

                        <div class="formRow">
                            <span class="span6">
                                <label>Descrição:</label>
                                <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                            </span>
                            <span class="span6 input-autocomplete-container">
                                <label>Receber de:</label>
                                <input style="margin-left: 0px;" type="text" name="favorecido_rcbt_id" id="fav_rcbt_rcr" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." data-tp-lnct="R" data-form-id="form_rcbt"/>
                                <input type="hidden" name="favorecido_id" id="favorecido_rcbt_id" value=""/>
                            </span>
                            </div>
    
                            <div class="formRow">
                            <span class="span6 input-autocomplete-container">
                                <label>Conta financeira:</label>
                                <input style="margin-left: 0px;" type="text" name="conta_rcbt_id" id="cf_rcbt_rcr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                <input type="hidden" name="conta_id" value="" id="conta_rcbt_id"/>
                            </span>
                            <span class="span6">
                                <label>Valor:</label>
                                <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_rcbt_valor" class="required moeda" onBlur="plcCtrValorAtualizar('form_rcbt')"/>
                            </span>
                        </div>
        
                        <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->  
        
                        <div class="formRow">
                            <span class="span4">
                                <label>Competência:</label>
                                <input style="margin-left: 0px;text-align:center;" type="text" name="dt_competencia" id="form_rcbt_dt_competencia" value="" class="required monthpicker" readonly/>
                            </span>
                            <span class="span4">
                                <label>Emissão:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_emissao" id="form_rcbt_dt_emissao" class="required datepickerFullWidth maskDate"/>
                            </span>
                            <span class="span4">
                                <label>Compensação:</label>
                                <select name="auto_lancamento" id="form_rcbt_auto_lancamento">
                                <option value="M">Manual</option>
                                <option value="A">Automática</option>
                                </select>
                            </span>
                        </div>

                        <div class="formRow">
			                        <!--
                            <span class="span5">
                                <label>Sábados e Domingos:</label>
                                <select name="sab_dom">
                                <option value="0">Desconsiderar</option>
                                <option value="1">Considerar</option>
                                </select>
                            </span>
                            -->

                            <span class="span4">
                            <label>Frequência:</label>
                            <select name="frequencia" id="form_rcbt_frequencia" class="frequencia" data-form-id="form_rcbt">
                                <option value="0"> Personalizado </option>
                                <option value="7"> Semanal </option>
                                <option value="30" selected="selected"> Mensal </option>
                                <option value="60"> Bimestral </option>
                                <option value="90"> Trimestral </option>
                                <option value="120"> Quadrimestral </option>
                                <option value="180"> Semestral </option>
                                <option value="360"> Anual </option>
                            </select>
                            </span>
                            <span class="span4" id="form_rcbt_span_qtd_dias" style="display:none">
                            <label>Dias de intervalo:</label>
                            <input style="margin-left: 0px;" type="text" name="qtd_dias" id="form_rcbt_intervalo" value="1" class="required maskNum"/>
                            </span>
                            <span class="span4" id="form_rcbt_span_dia_vencimento">
                            <label>Dia de vencimento:</label>
                            <select name="dia_mes" id="form_rcbt_dia_mes" class="required dia_mes" data-form-id="form_rcbt">
                                <option value="">  </option>
                                <option value="1">  01 </option>
                                <option value="2">  02 </option>
                                <option value="3">  03 </option>
                                <option value="4">  04 </option>
                                <option value="5">  05 </option>
                                <option value="6">  06 </option>
                                <option value="7">  07 </option>
                                <option value="8">  08 </option>
                                <option value="9">  09 </option>
                                <option value="10">  10 </option>
                                <option value="11">  11 </option>
                                <option value="12">  12 </option>
                                <option value="13">  13 </option>
                                <option value="14">  14 </option>
                                <option value="15">  15 </option>
                                <option value="16">  16 </option>
                                <option value="17">  17 </option>
                                <option value="18">  18 </option>
                                <option value="19">  19 </option>
                                <option value="20">  20 </option>
                                <option value="21">  21 </option>
                                <option value="22">  22 </option>
                                <option value="23">  23 </option>
                                <option value="24">  24 </option>
                                <option value="25">  25 </option>
                                <option value="26">  26 </option>
                                <option value="27">  27 </option>
                                <option value="28">  28 </option>                                                                                                                                            
                                <option value="29">  29 </option>                                                                                                                                            
                                <option value="30">  30 </option>                                                                                                                                            
                                <option value="31">  31 </option>  
                            </select>
                            <select name="dia_semana" id="form_rcbt_dia_semana" style="display:none;text-align:center;" class="required dia_semana" data-form-id="form_rcbt">
                                <option value="">  </option>
                                <option value="1">  Segunda </option>
                                <option value="2">  Terça </option>
                                <option value="3">  Quarta </option>
                                <option value="4">  Quinta </option>
                                <option value="5">  Sexta </option>
                                <option value="6">  Sábado </option>
                                <option value="7">  Domingo </option>
                            </select>
                            </span>
                            <span class="span4">
                                <label>Inicio:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_inicio" id="form_rcbt_dt_inicio" class="required dt_inicio" readonly/>	
                            </span>
                            </div>
      
                        <!--============ MAIS OPÇÕES ============-->
                        <div class="title closed inactive MaisOpcoes" align="left" > 
                        <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

                        <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
                            <div class="formRow">
                                <span class="span6">
                                    <label>Documento:</label>
                                    <select name="documento_id">
                                        <option value=""></option>
                                        <?php
                                        $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                        foreach($array_documentos as $documento){
                                        echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                                <span class="span6">
                                    <label>Forma de Pagamento:</label>
                                    <select name="forma_pgto_id">
                                        <option value=""></option>
                                        <?php
                                        $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                        foreach($array_forma_pgto as $forma_pgto){
                                        echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                                <br><br><br><br>
                            </div>

                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->  
       
                            <div class="formRow">
                                <span class="span12">
                                <label>Observação:</label>
                                    <textarea name="observacao" rows="2" cols="auto"></textarea> 
                                </span>                                                                                                    
                            </div>
       
                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                        </div>    <!-- Fim do Body Mais Opções -->

			        </div> 
                    
                    <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                    <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane" id="aba-ctr-plc-rcbt">
                      
                            <div class="formRow">

                                <span class="span4 input-autocomplete-container">
                                    <label>Categoria:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_rcbt_pl_conta_id" id="form_rcbt_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                    <input type="hidden" id="form_rcbt_pl_conta_id" value="0" />
                                </span>
                                <span class="span4 input-autocomplete-container">
                                    <label>Centro de custo:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_rcbt_ct_resp_id" id="form_rcbt_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                    <input type="hidden" id="form_rcbt_ct_resp_id" value="0" />
                                </span>

                                <span class="span2 pc-cr-valor-span">
                                    <label>Valor:</label>
                                    <input style="" type="text" id="form_rcbt_ct_resp_valor" value="0,00" class="required moeda" onblur="valorPorcentagem('form_rcbt');" />
                                </span>
                                <span class="span1 pc-cr-prct-span">
                                    <label>(%):</label>
                                    <input style="" type="text" name="porcentagem" value="00.0" id="form_rcbt_ctr_plc_prct" class="required porcentagem" onblur="porcentagemValor('form_rcbt');" onkeyup="FormatarPorcentagem('form_rcbt')"/>
                                </span>
                  
                                <span class="span1 pc-cr-incluir-span">
                                    <a href="javascript://void(0);" title="Incluír" class="button blueB" style="margin-top:-3px; height:26px; width:38px;" onClick="centroRespLnctIncluir('form_rcbt');"><img src="images/icons/light/add.png" style="margin-top:5px;" class="icon"></a>
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
EDITAR RECEBIMENTOS RECORRENTES
======================================================================
-->

<div id="dialog-rcbt-rcr-editar" style="height:auto; padding:0;" title="Editar Recebimento Recorrente" class="modal">

    <form id="form_rcbt_editar" class="dialog">

    <input type="hidden" name="funcao" value="lancamentoEditar">
    <input type="hidden" name="tipo" id="form_rcbt_editar_tipo" value="R">
    <input type="hidden" name="lancamento_id" id="form_rcbt_editar_lancamento_id" value="">
	<input type="hidden" name="ct_resp_lancamentos" id="form_rcbt_editar_ctr_plc_lnct" value="">
    <input type="hidden" name="dt_alterada" id="form_rcbt_editar_dt_alterada" value="0">

    <div class="fluid">

        <div class="span12">

            <div class="tab-bs">
               
                <ul class="nav nav-tabs" id="abas-form_rcbt_editar">
			        <li class="active lnctValid-RE"><a data-target="#aba-lnct-rcbt-editar" data-toggle="tab">Lançamento</a></li>
			        <li><a data-target="#aba-ctr-plc-rcbt-editar" data-toggle="tab">Categoria / Centro de Custo</a></li>
			    </ul>

                <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			    <div class="tab-content">

                    <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane active" id="aba-lnct-rcbt-editar">

                        <div class="formRow">
                            <span class="span6">
                                <label>Descrição:</label>
                                <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="form_rcbt_editar_dscr"/>
                            </span>
                            <span class="span6 input-autocomplete-container">
                                <label>Receber de:</label>
                                <input style="margin-left: 0px;" type="text" name="form_rcbt_editar_favorecido_id" id="form_rcbt_editar_favorecido" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." data-tp-lnct="R" data-form-id="form_rcbt_editar"/>
                                <input type="hidden" name="favorecido_id" id="form_rcbt_editar_favorecido_id" value=""/>
                            </span>
                            </div>
    
                            <div class="formRow">
                            <span class="span6 input-autocomplete-container">
                                <label>Conta financeira:</label>
                                <input style="margin-left: 0px;" type="text" name="form_rcbt_editar_conta_id" id="form_rcbt_editar_conta" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                <input type="hidden" name="conta_id" value="" id="form_rcbt_editar_conta_id"/>
                            </span>
                            <span class="span6">
                                <label>Valor:</label>
                                <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_rcbt_editar_valor" class="required moeda" onBlur="plcCtrValorAtualizar('form_rcbt_editar')"/>
                            </span>
                        </div>
        
                        <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->   
        
                        <div class="formRow">
                            <span class="span4">
                                <label>Competência:</label>
                                <input style="margin-left: 0px;text-align:center;" type="text" name="dt_competencia" id="form_rcbt_editar_dt_competencia" value="" class="required monthpicker" readonly/>
                            </span>
                            <span class="span4">
                                <label>Emissão:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_emissao" id="form_rcbt_editar_dt_emissao" class="required datepickerFullWidth maskDate"/>
                            </span>
                            <span class="span4">
                                <label>Compensação:</label>
                                <select name="auto_lancamento" id="form_rcbt_editar_auto_lancamento">
                                <option value="M">Manual</option>
                                <option value="A">Automática</option>
                                </select>
                            </span>
                        </div>

                        <div class="formRow">
			                        <!--
                            <span class="span5">
                                <label>Sábados e Domingos:</label>
                                <select name="sab_dom">
                                <option value="0">Desconsiderar</option>
                                <option value="1">Considerar</option>
                                </select>
                            </span>
                            -->

                            <span class="span4">
                            <label>Frequência:</label>
                            <select name="frequencia" id="form_rcbt_editar_frequencia" class="frequencia_editar" data-form-id="form_rcbt_editar">
                                <option value="0"> Personalizado </option>
                                <option value="7"> Semanal </option>
                                <option value="30" selected="selected"> Mensal </option>
                                <option value="60"> Bimestral </option>
                                <option value="90"> Trimestral </option>
                                <option value="120"> Quadrimestral </option>
                                <option value="180"> Semestral </option>
                                <option value="360"> Anual </option>
                            </select>
                            </span>
                            <span class="span4" id="form_rcbt_editar_span_qtd_dias" style="display:none">
                            <label>Dias de intervalo:</label>
                            <input style="margin-left: 0px;" type="text" name="qtd_dias" value="1" id="form_rcbt_editar_qtd_dias" class="required maskNum"/>
                            </span>
                            <span class="span4" id="form_rcbt_editar_span_dia_vencimento">
                            <label>Dia de vencimento:</label>
                            <select name="dia_mes" id="form_rcbt_editar_dia_mes" class="required dia_mes_editar" data-form-id="form_rcbt_editar">
                                <option value="">  </option>
                                <option value="1">  01 </option>
                                <option value="2">  02 </option>
                                <option value="3">  03 </option>
                                <option value="4">  04 </option>
                                <option value="5">  05 </option>
                                <option value="6">  06 </option>
                                <option value="7">  07 </option>
                                <option value="8">  08 </option>
                                <option value="9">  09 </option>
                                <option value="10">  10 </option>
                                <option value="11">  11 </option>
                                <option value="12">  12 </option>
                                <option value="13">  13 </option>
                                <option value="14">  14 </option>
                                <option value="15">  15 </option>
                                <option value="16">  16 </option>
                                <option value="17">  17 </option>
                                <option value="18">  18 </option>
                                <option value="19">  19 </option>
                                <option value="20">  20 </option>
                                <option value="21">  21 </option>
                                <option value="22">  22 </option>
                                <option value="23">  23 </option>
                                <option value="24">  24 </option>
                                <option value="25">  25 </option>
                                <option value="26">  26 </option>
                                <option value="27">  27 </option>
                                <option value="28">  28 </option>                                                                                                                                            
                                <option value="29">  29 </option>                                                                                                                                            
                                <option value="30">  30 </option>                                                                                                                                            
                                <option value="31">  31 </option>  
                            </select>
                            <select name="dia_semana" id="form_rcbt_editar_dia_semana" style="display:none;text-align:center;" class="required dia_semana_editar" data-form-id="form_rcbt_editar">
                                <option value="">  </option>
                                <option value="1">  Segunda </option>
                                <option value="2">  Terça </option>
                                <option value="3">  Quarta </option>
                                <option value="4">  Quinta </option>
                                <option value="5">  Sexta </option>
                                <option value="6">  Sábado </option>
                                <option value="7">  Domingo </option>
                            </select>
                            </span>
                            <span class="span4">
                                <label>Inicio:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_inicio" id="form_rcbt_editar_dt_inicio" class="required dt_inicio_editar" data-form-id="form_rcbt_editar" readonly/>
                            </span>
                            </div>
                                       
                        <!--============ MAIS OPÇÕES ============-->

                        <div class="title closed inactive MaisOpcoes" align="left" > 
                            <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  
                        </div>

                        <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
                            <div class="formRow">
                                <span class="span6">
                                    <label>Documento:</label>
                                    <select name="documento_id" id="form_rcbt_editar_documento_id">
                                        <option value=""></option>
                                        <?php
                                        $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                        foreach($array_documentos as $documento){
                                        echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                                <span class="span6">
                                    <label>Forma de Pagamento:</label>
                                    <select name="forma_pgto_id" id="form_rcbt_editar_forma_pgto_id">
                                        <option value=""></option>
                                        <?php
                                        $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                        foreach($array_forma_pgto as $forma_pgto){
                                        echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                                <br><br><br><br>
                            </div>

                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->   
       
                            <div class="formRow">
                                <span class="span12">
                                <label>Observação:</label>
                                    <textarea name="observacao" rows="2" cols="auto" id="form_rcbt_editar_obs"></textarea>
                                </span>                                                                                                    
                            </div>
       
                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->             
                 
                        </div>    <!-- Fim do Body Mais Opções -->                        

			        </div> 
                    
                    <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                    <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane" id="aba-ctr-plc-rcbt-editar">
                      
                            <div class="formRow">

                                <span class="span4 input-autocomplete-container">
                                    <label>Categoria:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_rcbt_editar_pl_conta_id" id="form_rcbt_editar_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                    <input type="hidden" id="form_rcbt_editar_pl_conta_id" value="0" />
                                </span>
                                <span class="span4 input-autocomplete-container">
                                    <label>Centro de custo:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_rcbt_editar_ct_resp_id" id="form_rcbt_editar_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                    <input type="hidden" id="form_rcbt_editar_ct_resp_id" value="0" />
                                </span>


                                <span class="span2 pc-cr-valor-span">
                                    <label>Valor:</label>
                                    <input style="" type="text" id="form_rcbt_editar_ct_resp_valor" value="0,00" class="required moeda" onblur="valorPorcentagem('form_rcbt_editar');" />
                                </span>
                                <span class="span1 pc-cr-prct-span">
                                    <label>(%):</label>
                                    <input style="" type="text" name="porcentagem" value="00.0" id="form_rcbt_editar_ctr_plc_prct" class="required porcentagem" onblur="porcentagemValor('form_rcbt_editar');" onkeyup="FormatarPorcentagem('form_rcbt_editar')"/>
                                </span>
                  
                                <span class="span1 pc-cr-incluir-span">
                                    <a href="javascript://void(0);" title="Incluír" class="button blueB" style="margin-top:-3px; height:26px; width:38px;" onClick="centroRespLnctIncluir('form_rcbt_editar');"><img src="images/icons/light/add.png" style="margin-top:5px;" class="icon"></a>
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
INCLUIR PAGAMENTOS RECORRENTES
======================================================================
-->

<div id="dialog-pgto-rcr-incluir" style="height:auto; padding:0;" title="Novo Pagamento Recorrente" class="modal">

    <form id="form_pgto" class="dialog">
    
    <input type="hidden" name="funcao" value="lancamentoIncluir">
    <input type="hidden" name="tipo" value="P">
	<input type="hidden" name="ct_resp_lancamentos" id="form_pgto_ctr_plc_lnct" value=""> 

    <div class="fluid">

        <div class="span12">

            <div class="tab-bs">
               
                <ul class="nav nav-tabs" id="abas-form_pgto">
			        <li class="active lnctValid-PI"><a data-target="#aba-lnct-pgto" data-toggle="tab">Lançamento</a></li>
			        <li><a data-target="#aba-ctr-plc-pgto" data-toggle="tab">Categoria / Centro de Custo</a></li>
			    </ul>

                <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			    <div class="tab-content">

                    <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane active" id="aba-lnct-pgto"> 

                        <div class="formRow">
                            <span class="span6">
                                <label>Descrição:</label>
                                <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                            </span>
                            <span class="span6 input-autocomplete-container">
                                <label>Pagar para:</label>
                                <input style="margin-left: 0px;" type="text" name="favorecido_pgto_id" id="fav_pgto_rcr" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." data-tp-lnct="P" data-form-id="form_pgto"/>
                                <input type="hidden" name="favorecido_id" id="favorecido_pgto_id" value=""/>
                            </span>
                            </div>

                            <div class="formRow">
                            <span class="span6 input-autocomplete-container">
                                <label>Conta financeira:</label>
                                <input style="margin-left: 0px;" type="text" name="conta_pgto_id" id="cf_pgto_rcr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                <input type="hidden" name="conta_id" value="" id="conta_pgto_id"/>
                            </span>
                            <span class="span6">
                                <label>Valor:</label>
                                <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_pgto_valor" class="required moeda" onBlur="plcCtrValorAtualizar('form_pgto')"/>
                            </span>
                        </div>
        
                        <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->  
        
                        <div class="formRow">
                            <span class="span4">
                                <label>Competência:</label>
                                <input style="margin-left: 0px;text-align:center;" type="text" name="dt_competencia" id="form_pgto_dt_competencia" value="" class="required monthpicker" readonly/>
                            </span>
                            <span class="span4">
                                <label>Emissão:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_emissao" id="form_pgto_dt_emissao" class="required datepickerFullWidth maskDate"/>
                            </span>
                            <span class="span4">
                                <label>Compensação:</label>
                                <select name="auto_lancamento" id="form_pgto_auto_lancamento">
                                <option value="M">Manual</option>
                                <option value="A">Automática</option>
                                </select>
                            </span>
                            </div>
         
                            <div class="formRow">
			                        <!--
                            <span class="span5">
                                <label>Sábados e Domingos:</label>
                                <select name="sab_dom">
                                <option value="0">Desconsiderar</option>
                                <option value="1">Considerar</option>
                                </select>
                            </span>
                            -->
                            <span class="span4">
                            <label>Frequência:</label>
                            <select name="frequencia" id="form_pgto_frequencia" class="frequencia" data-form-id="form_pgto">
                                <option value="0"> Personalizado </option>
                                <option value="7"> Semanal </option>
                                <option value="30" selected="selected"> Mensal </option>
                                <option value="60"> Bimestral </option>
                                <option value="90"> Trimestral </option>
                                <option value="120"> Quadrimestral </option>
                                <option value="180"> Semestral </option>
                                <option value="360"> Anual </option>
                            </select>
                            </span>
                            <span class="span4" id="form_pgto_span_qtd_dias" style="display:none">
                            <label>Dias de intervalo:</label>
                            <input style="margin-left: 0px;" type="text" name="qtd_dias" id="form_pgto_qtd_dias" value="1" class="required maskNum"/>
                            </span>
                            <span class="span4" id="form_pgto_span_dia_vencimento">
                            <label>Dia de vencimento:</label>
                            <select name="dia_mes" id="form_pgto_dia_mes" class="required dia_mes" data-form-id="form_pgto">
                                <option value="">  </option>
                                <option value="1">  01 </option>
                                <option value="2">  02 </option>
                                <option value="3">  03 </option>
                                <option value="4">  04 </option>
                                <option value="5">  05 </option>
                                <option value="6">  06 </option>
                                <option value="7">  07 </option>
                                <option value="8">  08 </option>
                                <option value="9">  09 </option>
                                <option value="10">  10 </option>
                                <option value="11">  11 </option>
                                <option value="12">  12 </option>
                                <option value="13">  13 </option>
                                <option value="14">  14 </option>
                                <option value="15">  15 </option>
                                <option value="16">  16 </option>
                                <option value="17">  17 </option>
                                <option value="18">  18 </option>
                                <option value="19">  19 </option>
                                <option value="20">  20 </option>
                                <option value="21">  21 </option>
                                <option value="22">  22 </option>
                                <option value="23">  23 </option>
                                <option value="24">  24 </option>
                                <option value="25">  25 </option>
                                <option value="26">  26 </option>
                                <option value="27">  27 </option>
                                <option value="28">  28 </option>                                                                                                                                            
                                <option value="29">  29 </option>                                                                                                                                            
                                <option value="30">  30 </option>                                                                                                                                            
                                <option value="31">  31 </option>  
                            </select>
                            <select name="dia_semana" id="form_pgto_dia_semana" style="display:none;text-align:center;" class="required dia_semana" data-form-id="form_pgto">
                                <option value="">  </option>
                                <option value="1">  Segunda </option>
                                <option value="2">  Terça </option>
                                <option value="3">  Quarta </option>
                                <option value="4">  Quinta </option>
                                <option value="5">  Sexta </option>
                                <option value="6">  Sábado </option>
                                <option value="7">  Domingo </option>
                            </select>
                            </span>
                            <span class="span4">
                                <label>Inicio:</label>
                                <input style="margin-left: 0px;" type="text" name="dt_inicio" id="form_pgto_dt_inicio" class="required dt_inicio" readonly/>
                            </span>
                            </div>
 
                        <!--============ MAIS OPÇÕES ============-->
                        <div class="title closed inactive MaisOpcoes" align="left" > 
                        <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

                        <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    
                            <div class="formRow">
                                <span class="span6">
                                    <label>Documento:</label>
                                    <select name="documento_id">
                                        <option value=""></option>
                                        <?php
                                        $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                        foreach($array_documentos as $documento){
                                        echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                                <span class="span6">
                                    <label>Forma de Pagamento:</label>
                                    <select name="forma_pgto_id">
                                        <option value=""></option>
                                        <?php
                                        $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                        foreach($array_forma_pgto as $forma_pgto){
                                        echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                                <br><br><br><br>
                            </div>
        
                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->  
        
                            <div class="formRow">
                                <span class="span12">
                                <label>Observação:</label>
                                    <textarea name="observacao" rows="2" cols="auto"></textarea> 
                                </span>                                                                                                    
                            </div>
       
                            <!--=====================================-->                       
                           
                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                        </div>    <!-- Fim do Body Mais Opções -->

			        </div> 
                    
                    <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                    <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			        <div class="tab-pane" id="aba-ctr-plc-pgto">
                      
                            <div class="formRow">

                                <span class="span4 input-autocomplete-container">
                                    <label>Categoria:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_pgto_pl_conta_id" id="form_pgto_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                    <input type="hidden" id="form_pgto_pl_conta_id" value="0" />
                                </span>
                                <span class="span4 input-autocomplete-container">
                                    <label>Centro de custo:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_pgto_ct_resp_id" id="form_pgto_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                    <input type="hidden" id="form_pgto_ct_resp_id" value="0" />
                                </span>

                                <span class="span2 pc-cr-valor-span">
                                    <label>Valor:</label>
                                    <input style="" type="text" id="form_pgto_ct_resp_valor" value="0,00" class="required moeda" onblur="valorPorcentagem('form_pgto');" />
                                </span>
                                <span class="span1 pc-cr-prct-span">
                                    <label>(%):</label>
                                    <input style="" type="text" name="porcentagem" value="00.0" id="form_pgto_ctr_plc_prct" class="required porcentagem" onblur="porcentagemValor('form_pgto');" onkeyup="FormatarPorcentagem('form_pgto')"/>
                                </span>
                  
                                <span class="span1 pc-cr-incluir-span">
                                    <a href="javascript://void(0);" title="Incluír" class="button blueB" style="margin-top:-3px; height:26px; width:38px;" onClick="centroRespLnctIncluir('form_pgto');"><img src="images/icons/light/add.png" style="margin-top:5px;" class="icon"></a>
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
EDITAR PAGAMENTOS RECORRENTES
======================================================================
-->

<div id="dialog-pgto-rcr-editar" style="height:auto; padding:0;" title="Editar Pagamento Recorrente" class="modal">

    <form id="form_pgto_editar" class="dialog">

        <input type="hidden" name="funcao" value="lancamentoEditar">
        <input type="hidden" name="tipo" id="form_pgto_editar_tipo" value="P">
        <input type="hidden" name="lancamento_id" id="form_pgto_editar_lancamento_id" value="">
        <input type="hidden" name="ct_resp_lancamentos" id="form_pgto_editar_ctr_plc_lnct" value="">
        <input type="hidden" name="dt_alterada" id="form_pgto_editar_dt_alterada" value="0">

        <div class="fluid">

            <div class="span12">

                <div class="tab-bs">
               
                    <ul class="nav nav-tabs" id="abas-form_pgto_editar">
			            <li class="active lnctValid-PE"><a data-target="#aba-lnct-pgto-editar" data-toggle="tab">Lançamento</a></li>
			            <li><a data-target="#aba-ctr-plc-pgto-editar" data-toggle="tab">Categoria / Centro de Custo</a></li>
			        </ul>

                    <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

			        <div class="tab-content">

                        <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

			            <div class="tab-pane active" id="aba-lnct-pgto-editar">

                            <div class="formRow">
                                <span class="span6">
                                    <label>Descrição:</label>
                                    <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="form_pgto_editar_dscr"/>
                                </span>
                                <span class="span6 input-autocomplete-container">
                                        <label>Pagar para:</label>
                                        <input style="margin-left: 0px;" type="text" name="form_pgto_editar_favorecido_id" id="form_pgto_editar_favorecido" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." data-tp-lnct="P" data-form-id="form_pgto_editar"/>
                                        <input type="hidden" name="favorecido_id" id="form_pgto_editar_favorecido_id" value=""/>
                                </span>
                                </div>
    
                                <div class="formRow">
                                <span class="span6 input-autocomplete-container">
                                    <label>Conta financeira:</label>
                                    <input style="margin-left: 0px;" type="text" name="form_pgto_editar_conta_id" id="form_pgto_editar_conta" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                    <input type="hidden" name="conta_id" value="" id="form_pgto_editar_conta_id"/>
                                </span>
                                <span class="span6">
                                    <label>Valor:</label>
                                    <input style="margin-left: 0px;text-align:right;" type="text" name="valor" id="form_pgto_editar_valor" class="required moeda" onBlur="plcCtrValorAtualizar('form_pgto_editar')"/>
                                </span>
                            </div>
        
                            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->   
        
                            <div class="formRow">
                                <span class="span4">
                                    <label>Competência:</label>
                                    <input style="margin-left: 0px;text-align:center;" type="text" name="dt_competencia" id="form_pgto_editar_dt_competencia" value="" class="required monthpicker" readonly/>
                                </span>
                                <span class="span4">
                                    <label>Emissão:</label>
                                    <input style="margin-left: 0px;" type="text" name="dt_emissao" id="form_pgto_editar_dt_emissao" class="required datepickerFullWidth maskDate"/>
                                </span>
                                <span class="span4">
                                    <label>Compensação:</label>
                                    <select name="auto_lancamento" id="form_pgto_editar_auto_lancamento">
                                    <option value="M">Manual</option>
                                    <option value="A">Automática</option>
                                    </select>
                                </span>
                            </div>

                            <div class="formRow">
			                            <!--
                                <span class="span5">
                                    <label>Sábados e Domingos:</label>
                                    <select name="sab_dom">
                                    <option value="0">Desconsiderar</option>
                                    <option value="1">Considerar</option>
                                    </select>
                                </span>
                                -->

                                <span class="span4">
                                <label>Frequência:</label>
                                <select name="frequencia" id="form_pgto_editar_frequencia" class="frequencia_editar" data-form-id="form_pgto_editar">
                                    <option value="0"> Personalizado </option>
                                    <option value="7"> Semanal </option>
                                    <option value="30" selected="selected"> Mensal </option>
                                    <option value="60"> Bimestral </option>
                                    <option value="90"> Trimestral </option>
                                    <option value="120"> Quadrimestral </option>
                                    <option value="180"> Semestral </option>
                                    <option value="360"> Anual </option>
                                </select>
                                </span>
                                <span class="span4" id="form_pgto_editar_span_qtd_dias" style="display:none">
                                <label>Dias de intervalo:</label>
                                <input style="margin-left: 0px;" type="text" name="qtd_dias" value="1" id="form_pgto_editar_qtd_dias" class="required maskNum"/>
                                </span>
                                <span class="span4" id="form_pgto_editar_span_dia_vencimento">
                                <label>Dia de vencimento:</label>
                                <select name="dia_mes" id="form_pgto_editar_dia_mes" class="required dia_mes_editar" data-form-id="form_pgto_editar">
                                    <option value="">  </option>
                                    <option value="1">  01 </option>
                                    <option value="2">  02 </option>
                                    <option value="3">  03 </option>
                                    <option value="4">  04 </option>
                                    <option value="5">  05 </option>
                                    <option value="6">  06 </option>
                                    <option value="7">  07 </option>
                                    <option value="8">  08 </option>
                                    <option value="9">  09 </option>
                                    <option value="10">  10 </option>
                                    <option value="11">  11 </option>
                                    <option value="12">  12 </option>
                                    <option value="13">  13 </option>
                                    <option value="14">  14 </option>
                                    <option value="15">  15 </option>
                                    <option value="16">  16 </option>
                                    <option value="17">  17 </option>
                                    <option value="18">  18 </option>
                                    <option value="19">  19 </option>
                                    <option value="20">  20 </option>
                                    <option value="21">  21 </option>
                                    <option value="22">  22 </option>
                                    <option value="23">  23 </option>
                                    <option value="24">  24 </option>
                                    <option value="25">  25 </option>
                                    <option value="26">  26 </option>
                                    <option value="27">  27 </option>
                                    <option value="28">  28 </option>                                                                                                                                            
                                    <option value="29">  29 </option>                                                                                                                                            
                                    <option value="30">  30 </option>                                                                                                                                            
                                    <option value="31">  31 </option>  
                                </select>
                                <select name="dia_semana" id="form_pgto_editar_dia_semana" style="display:none;text-align:center;" class="required">
                                    <option value="">  </option>
                                    <option value="1">  Segunda </option>
                                    <option value="2">  Terça </option>
                                    <option value="3">  Quarta </option>
                                    <option value="4">  Quinta </option>
                                    <option value="5">  Sexta </option>
                                    <option value="6">  Sábado </option>
                                    <option value="7">  Domingo </option>
                                </select>
                                </span>
                                <span class="span4">
                                    <label>Inicio:</label>
                                    <input style="margin-left: 0px;" type="text" name="dt_inicio" id="form_pgto_editar_dt_inicio" class="required dt_inicio_editar" data-form-id="form_pgto_editar" readonly/>
                                </span>
                                </div>
                                       
                            <!--============ MAIS OPÇÕES ============-->
                            <div class="title closed inactive MaisOpcoes" align="left" > 
                            <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

                            <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
                                <div class="formRow">
                                    <span class="span6">
                                        <label>Documento:</label>
                                        <select name="documento_id" id="form_pgto_editar_documento_id">
                                            <option value=""></option>
                                            <?php
                                            $array_documentos = $db->fetch_all_array("select * from documentos order by nome");
                                            foreach($array_documentos as $documento){
                                            echo '<option value="'.$documento['id'].'">'.$documento['nome'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </span>
                                    <span class="span6">
                                        <label>Forma de Pagamento:</label>
                                        <select name="forma_pgto_id" id="form_pgto_editar_forma_pgto_id">
                                            <option value=""></option>
                                            <?php
                                            $array_forma_pgto = $db->fetch_all_array("select * from forma_pagamento order by forma");
                                            foreach($array_forma_pgto as $forma_pgto){
                                            echo '<option value="'.$forma_pgto['id'].'">'.$forma_pgto['forma'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </span>
                                    <br><br><br><br>
                                </div>
        
                                <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->   
       
                                <div class="formRow">
                                    <span class="span12">
                                    <label>Observação:</label>
                                    <textarea name="observacao" rows="2" cols="auto" id="form_pgto_editar_obs"></textarea>
                                    </span>                                                                                                    
                                </div>
       
                                <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                            </div>    <!-- Fim do Body Mais Opções -->                          

			            </div> 
                    
                        <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                        <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

			            <div class="tab-pane" id="aba-ctr-plc-pgto-editar">
                      
                                <div class="formRow">

                                    <span class="span4 input-autocomplete-container">
                                        <label>Categoria:</label>
                                        <input style="margin-left: 0px;" type="text" name="form_pgto_editar_pl_conta_id" id="form_pgto_editar_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                        <input type="hidden" id="form_pgto_editar_pl_conta_id" value="0" />
                                    </span>
                                    <span class="span4 input-autocomplete-container">
                                        <label>Centro de custo:</label>
                                        <input style="margin-left: 0px;" type="text" name="form_pgto_editar_ct_resp_id" id="form_pgto_editar_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size="" />
                                        <input type="hidden" id="form_pgto_editar_ct_resp_id" value="0" />
                                    </span>

                                    <span class="span2 pc-cr-valor-span">
                                        <label>Valor:</label>
                                        <input style="" type="text" id="form_pgto_editar_ct_resp_valor" value="0,00" class="required moeda" onblur="valorPorcentagem('form_pgto_editar');" />
                                    </span>
                                    <span class="span1 pc-cr-prct-span">
                                        <label>(%):</label>
                                        <input style="" type="text" name="porcentagem" value="00.0" id="form_pgto_editar_ctr_plc_prct" class="required porcentagem" maxlength="6" onblur="porcentagemValor('form_pgto_editar');" onkeyup="FormatarPorcentagem('form_pgto_editar')"/>
                                    </span>
                  
                                    <span class="span1 pc-cr-incluir-span">
                                        <a href="javascript://void(0);" title="Incluír" class="button blueB" style="margin-top:-3px; height:26px; width:38px;" onClick="centroRespLnctIncluir('form_pgto_editar');"><img src="images/icons/light/add.png" style="margin-top:5px;" class="icon"></a>
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