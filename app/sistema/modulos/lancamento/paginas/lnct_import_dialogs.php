<!--------------------------------------------------------------------------------------------------------
IMPORTAR LANÇAMENTOS
-->

<div id="dialog-lnct-importar" style="height:auto;padding:0;display:none" title="Importar Lançamentos">
  <div class="fluid">
    <div class="formRow" style="background-color:;">
        <span class="span12">
          <a href="modulos/lancamento/Planilha_Modelo_Lancamentos_Importacao.xls">Planilha Modelo</a>
        </span>
    </div>
    <form id="form_lnct_import">
      <!--
      <div class="formRow">
          <span class="span12 input-autocomplete-container">
            <label>Conta financeira:</label>
            <input style="margin-left: 0px;" type="text" name="conta_id_import" id="input_conta_import" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
            <input type="hidden" name="" value="" id="conta_id_import"/>
          </span>
      </div>
      -->
      <div id="lnct_uploader"></div>
    </form>
  </div><!-- Fim fluid -->
</div><!-- Fim dialog -->

<!--------------------------------------------------------------------------------------------------------
INCLUIR RECEBIMENTO
-->

<div id="dialog-rcbt-incluir" style="height:auto; padding:0;display:none" title="Novo Recebimento">
  
  <form id="form_rcbt" class="dialog">
    <input type="hidden" name="funcao" value="recebimentosIncluir">
    <input type="hidden" name="tipo" value="R">
    <input type="hidden" name="ct_resp_lancamentos" id="ct_resp_lancamentos_form_rcbt" value="">
    <input type="hidden" name="compensado" value="1">

    <div class="toggle acc first" style="margin:0;">
      <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Lançamento</h6></div>
        <div class="menu_body" style="color: rgb(64, 64, 64); padding:0;" >
                          
            <!-- <div class="widget" style="border: 0; margin: 0;"> --> 
                     <div class="fluid">      
      
                              <div class="formRow">
                                  <span class="span6">
                                      <label>Descrição:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="rcbt_dscr"/>
                                  </span>
                                  <span class="span6 input-autocomplete-container">
                                      <label>Receber de:</label>
                                      <input style="" type="text" name="favorecido_rcbt_id" id="fav_rcbt" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="favorecido_id" id="favorecido_rcbt_id" value=""/>
                                  </span>
                               </div>

                               <div class="formRow">
                                  <span class="span6 input-autocomplete-container">
                                      <label>Conta financeira:</label>
                                      <input style="margin-left: 0px;" type="text" name="conta_rcbt_id" id="conta_rcbt_dscr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="conta_id" value="" id="conta_rcbt_id"/>
                                  </span>
                                  <span class="span3">
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
                                  <span class="span3">
                                      <label>Forma de pagamento:</label>
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
                              </div>
                              <div class="formRow">
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Competência:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_competencia" value="" class="required monthpicker" id="rcbt_dt_competencia" readonly/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Emissão:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_emissao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="rcbt_dt_emissao"/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Vencimento:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="rcbt_dt_vencimento"/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Compensaçao:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_compensacao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="rcbt_dt_compensacao"/>
                                  </span>
                                  <span class="span4">
                                      <label>Valor:</label>
                                      <input style="" type="text" name="valor" id="valor_form_rcbt" value="" class="required moeda" onBlur="plcCtrValorAtualizar('form_rcbt');"/>
                                  </span>
                               </div>
                               
                               <div class="formRow">
                                  <span class="span12">
                                    <label>Observação:</label>
                                     <textarea name="observacao" rows="3" cols="auto"></textarea> 
                                  </span>                                                                                                    
                               </div>

                      </div>  <!-- fluid -->               
                              
         </div>
      
      <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Plano de Contas / Centro de Responsabilidade</h6></div>
        <div class="menu_body" style="color: rgb(64, 64, 64); ">

           <div class="fluid">
               <div class="formRow ctr_plc_container">
                  <span class="span3 input-autocomplete-container">
                      <label>Conta:</label>
                      <input style="margin-left: 0px;" type="text" name="pl_conta_id_form_rcbt" id="pl_conta_buscar_form_rcbt" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar..." size=""/>
                      <input type="hidden" id="pl_conta_id_form_rcbt" value="0"/>
                  </span>
                  <span class="span3 input-autocomplete-container">
                      <label>Centro:</label>
                      <input style="margin-left: 0px; width" type="text" name="ct_resp_id_form_rcbt" id="ct_resp_buscar_form_rcbt" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar..." size=""/>
                      <input type="hidden" id="ct_resp_id_form_rcbt" value="0"/>
                  </span>
                  
                  <span class="span2 pc-cr-valor-span">
                      <label>Valor:</label>
                      <input style="" type="text" id="ct_resp_valor_form_rcbt" value="0,00" class="required moeda" onblur="valorPorcentagem('form_rcbt');" />
                  </span>                                  
                  <span class="span2 pc-cr-prct-span">
                      <label>(%):</label>
                      <input style="" type="text" name="porcentagem" value="0,00" id="ctr_plc_prct_form_rcbt" class="required moeda" maxlength="6" onblur="porcentagemValor('form_rcbt');"/>
                  </span>
                  
                  <span class="span2 pc-cr-incluir-span">
                      <a href="javascript://void(0);" title="Incluír" class="smallButton" onClick="centroRespLnctIncluir('form_rcbt');"><img src="images/icons/dark/check.png" alt=""></a>
                  </span>
                  <div class="clear"></div>
               </div>
           </div>             				

        </div>                      
    </div>
  </form> 
</div>

<!--------------------------------------------------------------------------------------------------------
INCLUIR PAGAMENTO
-->

<div id="dialog-pgto-incluir" style="height:auto; padding:0;display:none" title="Novo Pagamento">
  
  <form id="form_pgto" class="dialog">
    <input type="hidden" name="funcao" value="pagamentosIncluir">
    <input type="hidden" name="tipo" value="P">
    <input type="hidden" name="ct_resp_lancamentos" id="ct_resp_lancamentos_form_pgto" value="">
    <input type="hidden" name="compensado" value="1">

      <div class="toggle acc" style="margin:0;">      
        <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Lançamento</h6></div>
          <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
                            
             <!--       <div class="widget" style="margin:0;">  -->
                       <div class="fluid">      
        
                                <div class="formRow">
                                    <span class="span6">
                                        <label>Descrição:</label>
                                        <input type="text" name="descricao" value="" class="required" id="pgto_dscr"/>
                                    </span>
                                    <span class="span6 input-autocomplete-container">
                                        <label>Pagar para:</label>
                                        <input type="text" name="favorecido_pgto_id" id="fav_pgto" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." />
                                        <input type="hidden" name="favorecido_id" id="favorecido_pgto_id" value=""/>
                                    </span>
                                 </div>

                                 <div class="formRow">
                                    <span class="span6 input-autocomplete-container">
                                        <label>Conta financeira:</label>
                                        <input style="margin-left: 0px;" type="text" name="conta_pgto_id" id="conta_pgto_dscr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                        <input type="hidden" name="conta_id" value="" id="conta_pgto_id"/>
                                    </span>
                                    <span class="span3">
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
                                    <span class="span3">
                                        <label>Forma de pagamento:</label>
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
                                </div>
                                <div class="formRow">
                                    <span class="span2">
                                        <label style="width:100%; text-align:left;">Competência:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_competencia" value="" class="required monthpicker" id="pgto_dt_competencia"/>
                                    </span>
                                    <span class="span2">
                                        <label style="width:100%; text-align:left;">Emissão:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_emissao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="pgto_dt_emissao"/>
                                    </span>
                                    <span class="span2">
                                        <label style="width:100%; text-align:left;">Vencimento:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="pgto_dt_vencimento"/>
                                    </span>
                                    <span class="span2">
                                        <label style="width:100%; text-align:left;">Compensaçao:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_compensacao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="pgto_dt_compensacao"/>
                                    </span>
                                    <span class="span4">
                                        <label>Valor:</label>
                                        <input style="margin-left: 0px;" type="text" name="valor" id="valor_form_pgto" value="" class="required moeda" onBlur="plcCtrValorAtualizar('form_pgto');"/>
                                    </span>
                                 </div>
                                                                                
                                 <div class="formRow">
                                    <span class="span12">
                                      <label>Observação:</label>
                                       <textarea name="observacao" rows="3" cols="auto"></textarea> 
                                    </span>                                                                                                    
                                 </div>

                        </div>  <!-- fluid -->               
                <!--   </div> widget -->               
           </div>

        <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Plano de Contas / Centro de Responsabilidade</h6></div>
          <div class="menu_body" style="color: rgb(64, 64, 64); ">
             <div class="fluid">
                <div class="formRow ctr_plc_container">
                    <span class="span3 input-autocomplete-container">
                        <label>Conta:</label>
                        <input style="margin-left: 0px;" type="text" name="pl_conta_id_form_pgto" id="pl_conta_buscar_form_pgto" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar..." size=""/>
                        <input type="hidden" id="pl_conta_id_form_pgto" value="0"/>
                    </span>
                    <span class="span3 input-autocomplete-container">
                        <label>Centro:</label>
                        <input style="margin-left: 0px;" type="text" name="ct_resp_id_form_pgto" id="ct_resp_buscar_form_pgto" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar..." size=""/>
                        <input type="hidden" id="ct_resp_id_form_pgto" value="0"/>
                    </span>
                    <span class="span2 pc-cr-valor-span">
                        <label>Valor:</label>
                        <input style="" type="text" id="ct_resp_valor_form_pgto" value="0,00" class="required moeda" onblur="valorPorcentagem('form_pgto');" />
                    </span>                                  
                    <span class="span2 pc-cr-prct-span">
                        <label>(%):</label>
                        <input style="" type="text" name="porcentagem" value="0,00" id="ctr_plc_prct_form_pgto" class="required moeda" maxlength="6" onblur="porcentagemValor('form_pgto');"/>
                    </span>
                    <span class="span2 pc-cr-incluir-span">
                        <a href="javascript://void(0);" title="Incluír" class="smallButton" onClick="centroRespLnctIncluir('form_pgto');"><img src="images/icons/dark/check.png" alt=""></a>
                    </span>
                    <div class="clear"></div>
                 </div>
              </div>                   				
          </div>
         
      </div>
	</form> 
                   
</div>

<!--------------------------------------------------------------------------------------------------------
INCLUIR TRANSFERENCIA
-->

<div id="dialog-trans-incluir" style="height:auto; padding:0;display:none" title="Nova Transferência">
  
  <form id="form_trans" class="dialog">
    <input type="hidden" name="funcao" value="transferenciasIncluir">
    <input type="hidden" name="tipo" value="T">
    <input type="hidden" name="compensado" value="1">

      <div class="toggle acc" style="margin:0;">      
        <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Lançamento</h6></div>
          <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
            <div class="fluid">      
             <div class="formRow">
                <span class="span12">
                    <label>Descrição:</label>
                    <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="trans_dscr"/>
                </span>
              </div>
              <div class="formRow">
                  <span class="span6 input-autocomplete-container">
                      <label>Conta financeira de origem:</label>
                      <input style="margin-left: 0px;" type="text" name="conta_trans_id_origem" id="conta_trans_org_dscr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                      <input type="hidden" name="conta_id_origem" value="" id="conta_trans_id_origem"/>
                  </span>
                  <span class="span6 input-autocomplete-container">
                      <label>Conta financeira de destino:</label>
                      <input style="margin-left: 0px;" type="text" name="conta_trans_id_destino" id="conta_trans_dest_dscr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                      <input type="hidden" name="conta_id_destino" value="" id="conta_trans_id_destino"/>
                  </span>
               </div>
              <div class="formRow">
                  <span class="span2">
                      <label style="width:100%; text-align:left;">Emissão:</label>
                      <input style="margin-left: 0px;" type="text" name="dt_emissao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="trans_dt_emissao"/>
                  </span>
                  <span class="span2">
                      <label style="width:100%; text-align:left;">Vencimento:</label>
                      <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="trans_dt_vencimento"/>
                  </span>
                  <span class="span2">
                      <label style="width:100%; text-align:left;">Compensaçao:</label>
                      <input style="margin-left: 0px;" type="text" name="dt_compensacao" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="trans_dt_compensacao"/>
                  </span>
                  <span class="span6">
                      <label>Valor:</label>
                      <input style="margin-left: 0px;" type="text" name="valor" value="" class="required moeda" id="valor_form_trans"/>
                  </span>
               </div>
               <div class="formRow">
                  <span class="span12">
                    <label>Observação:</label>
                     <textarea name="observacao" rows="3" cols="auto"></textarea> 
                  </span>                                                                                                    
               </div>
            </div>
           </div>
      </div>
	</form> 
</div>

<!--------------------------------------------------------------------------------------------------------
INCLUIR LANÇAMENTOS EM LOTE
-->

<div id="dialog-lnct-lote" style="height:auto; padding:0;display:none" title="Incluír Lançamentos em Lote">
  
  <form id="form_lote" class="dialog">
    <input type="hidden" name="funcao" value="lnctLoteIncluir">
    <input type="hidden" name="tipo" value="">
    <input type="hidden" name="ct_resp_lancamentos" id="ct_resp_lancamentos_form_lote" value="">
    <input type="hidden" name="compensado" value="1">

    <div class="toggle acc first" style="margin:0;">

      <div class="formRow" style="background-color: rgb(254, 255, 210);">
          <span class="span12">
            Atenção! O preenchimento dos campos será aplicado a todos os lançamentos selecionados. <br>
            Para editar cada lançamento individualmente, utilize as opções disponíveis no canto direito de cada linha da tabela.
          </span>
      </div>

      <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Lançamento</h6></div>
      
        <div class="menu_body" style="color: rgb(64, 64, 64); padding:0;display:block"> 
                          
            <!-- <div class="widget" style="border: 0; margin: 0;"> --> 
                     <div class="fluid">
                              <div class="formRow">
                                  <span class="span6">
                                      <label>Descrição:</label>
                                      <input type="text" name="descricao"/>
                                  </span>
                                  <span class="span6 input-autocomplete-container">
                                      <label>Cliente / Fornecedor:</label>
                                      <input style="" type="text" name="favorecido_lote_id" id="fav_lote" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="favorecido_id" id="favorecido_lote_id" value=""/>
                                  </span>
                               </div>

                               <div class="formRow">
                                  <span class="span6 input-autocomplete-container">
                                      <label>Conta financeira:</label>
                                      <input style="margin-left: 0px;" type="text" name="conta_lote_id" id="conta_lote_dscr" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="conta_id" value="" id="conta_lote_id"/>
                                  </span>
                                  <span class="span3">
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
                                  <span class="span3">
                                      <label>Forma de pagamento:</label>
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
                              </div>
                              <div class="formRow">
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Competência:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_competencia" value="" class="monthpicker"readonly/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Emissão:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_emissao" class="datepicker maskDate"/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Vencimento:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_vencimento" class="datepicker maskDate"/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Compensaçao:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_compensacao" class="datepicker maskDate"/>
                                  </span>
                                  <span class="span4">
                                      <label>Valor:</label>
                                      <input style="" type="text" name="valor" id="valor_form_lote" class="moeda" onBlur="plcCtrValorAtualizar('form_lote');"/>
                                  </span>
                               </div>
                               
                               <div class="formRow">
                                  <span class="span12">
                                    <label>Observação:</label>
                                     <textarea name="observacao" rows="3" cols="auto"></textarea> 
                                  </span>                                                                                                    
                               </div>

                      </div>  <!-- fluid -->               
                              
        </div>

      	<div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Plano de Contas / Centro de Responsabilidade</h6></div>
        
        <div class="menu_body" style="color: rgb(64, 64, 64); ">
        
           <div class="fluid">
               <div class="formRow">
                  <span class="span6 input-autocomplete-container">
                      <label>Conta:</label>
                      <input style="width:290px" type="text" name="pl_conta_id_form_lote" id="pl_conta_buscar_form_lote" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar..." size=""/>
                      <input type="hidden" name="plc_id" id="pl_conta_id_form_lote" value="0"/>
                  </span>
                  <span class="span6 input-autocomplete-container">
                      <label>Centro:</label>
                      <input style="width:290px" type="text" name="ct_resp_id_form_lote" id="ct_resp_buscar_form_lote" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar..." size=""/>
                      <input type="hidden" name="ctr_id" id="ct_resp_id_form_lote" value="0"/>
                  </span>
									<!--
                  <span class="span2 pc-cr-valor-span">
                      <label>Valor:</label>
                      <input type="text" id="ct_resp_valor_form_lote" value="0,00" class="required moeda" onblur="valorPorcentagem('form_lote');" readonly/>
                  </span>
                  <span class="span2 pc-cr-prct-span">
                      <label>(%):</label>
                      <input type="text" name="porcentagem" value="100,00" id="ctr_plc_prct_form_lote" class="required moeda" maxlength="6" onblur="porcentagemValor('form_lote');" readonly/>
                  </span>

                  <span class="span2 pc-cr-incluir-span">
                      <a href="javascript://void(0);" title="Incluír" class="smallButton" onClick="centroRespLnctIncluir('form_lote');"><img src="images/icons/dark/check.png" alt=""></a>
                  </span>
									-->                  
                  <div class="clear"></div>
               </div>
           </div>
           
        </div>
         
    </div>
  </form> 
</div>

<!--------------------------------------------------------------------------------------------------------
DIALOG TESTE
-->
<!--
<div id="dialog-teste" style="height:auto; padding:0;display:none" title="Teste">  
    <div class="toggle acc first" style="margin:0;">  
    
        <div class="title" id="toggleOpened" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Aba1</h6></div>
        <div class="menu_body" style="color: rgb(64, 64, 64); ">
           <div class="fluid">
               <div class="formRow">
                  <span class="span12">
									  Aba 1
                  </span>
                  <div class="clear"></div>
               </div>
           </div>             				
        </div>  

      
      	<div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Aba2</h6></div>
        <div class="menu_body" style="color: rgb(64, 64, 64); "> 
           <div class="fluid">
               <div class="formRow">
                  <span class="span12">
									  Aba 2
                  </span>
                  <div class="clear"></div>
               </div>
           </div>             				
        </div>  
                            
    </div>
</div>
-->