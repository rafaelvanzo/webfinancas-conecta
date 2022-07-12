   						<!-- Caixa de Dialogo Conta a Receber -->
<div id="dialog-exemplo" style="height:auto; padding:0;" title="Novo Recebimento">
                          
            <!-- <div class="widget" style="border: 0; margin: 0;"> --> 
                     <div class="fluid">      
      
                              <div class="formRow">
                                  <span class="span6">
                                      <label>Descrição:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" id="rcbt_dscr"/>
                                  </span>
                                  <span class="span6 input-autocomplete-container">
                                      <label>Receber de:</label>
                                      <input type="text" name="favorecido_rcbt_id" id="favorecido_rcbt_id_01" value="" class="required favorecido_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="favorecido_id" id="favorecido_rcbt_id" value=""/>
                                  </span>
                               </div>
						
                               <div class="formRow">
                                  <span class="span6 input-autocomplete-container">
                                      <label>Conta financeira:</label>
                                      <input style="margin-left: 0px;" type="text" name="conta_rcbt_id" id="conta_rcbt_id_01" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="conta_id" value="" id="conta_rcbt_id"/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Vencimento:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="rcbt_dt_vencimento"/>
                                  </span>
                                  <span class="span4">
                                      <label>Valor:</label>
                                      <input style="" type="text" name="valor" id="valor_form_rcbt" value="" class="required moeda" onBlur="plcCtrValorAtualizar('form_rcbt');"/>
                                  </span>
    
                            </div>

<div class="title closed inactive MaisOpcoes" align="left"> 
 <a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

                             		<div class="body" style="display: block;">
                                   
                                    <div class="formRow">
                                  <span class="span6 input-autocomplete-container">
                                      <label>Conta financeira:</label>
                                      <input style="margin-left: 0px;" type="text" name="conta_rcbt_id" id="conta_rcbt_id_01" value="" class="required conta_buscar input-buscar" placeholder="Preencha para localizar..." />
                                      <input type="hidden" name="conta_id" value="" id="conta_rcbt_id"/>
                                  </span>
                                  <span class="span2">
                                      <label style="width:100%; text-align:left;">Vencimento:</label>
                                      <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate" id="rcbt_dt_vencimento"/>
                                  </span>
                                  <span class="span4">
                                      <label>Valor:</label>
                                      <input style="" type="text" name="valor" id="valor_form_rcbt" value="" class="required moeda" onBlur="plcCtrValorAtualizar('form_rcbt');"/>
                                  </span>
                               </div>
                               
                               <!-- Linha deve estar no ultimo formRow -->
                               <div class="linha"></div>
                             
                             </div>                          
                             
                      </div>  <!-- fluid -->                 
  
    </div>
  </form>
             <!--   </div> widget -->  
             
</div><!-- Fim dialog --> 