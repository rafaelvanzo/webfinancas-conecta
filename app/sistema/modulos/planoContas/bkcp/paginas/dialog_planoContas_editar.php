   						<!-- Caixa de Dialogo Conta a Receber -->
              <div id="dialog-message-planoContas-editar" style="height:auto; padding:0;" title="Editar Conta">
								
                <form id="form_planoContas_editar" class="dialog">
                		<input type="hidden" name="funcao" value="planoContasEditar">
                		<input type="hidden" name="plano_contas_id" value="">
                    <input type="hidden" name="conta_pai_id_ini" id="conta_pai_id_ini" value="">
                    <input type="hidden" name="cod_ref_ini" id="cod_ref_ini" value="">
   	                <input type="hidden" name="nivel" value="" />
	                  <input type="hidden" name="posicao" value="" />
                    <div class="toggle acc" style="margin:0;">
                    <!-- <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/frames.png" alt="" class="titleIcon"><h6>Dados da Conta</h6></div> -->
                        <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
    								                      
                           <!--       <div class="widget" style="margin:0;">  -->
                                     <div class="fluid">      

                                              <div class="formRow">
                                                  <!--
                                                  <span class="span4">
                                                      <label>Código da Conta:</label>
                                                      <input style="margin-left: 0px;" type="text" name="cod_conta" value="" readonly/>
                                                  </span>
                                                  -->
                                                  <span class="span5 input-autocomplete-container">
                                                     <label>Conta Pai:</label>
                                                      <input type="text" name="conta_pai_id_edit" id="nm_plc_pai_edit" value="" class="plano_contas_buscar_plc ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                                                      <input type="hidden" name="conta_pai_id" value="" id="conta_pai_id_edit">
                                                  </span>
                                                  <span class="span5">
                                                      <label>Nome da Conta:</label>
                                                      <input type="text" name="nome" value="" class="required"/>
                                                  </span>                                                  
                                                  <span class="span2">
                                                      <label>Situação:</label>
                                                      <select name="situacao">
                                                        <option value="0">Ativo</option>
                                                        <option value="1">Inativo</option>
                                                      </select>
                                                  </span>
                                                  
                                               </div>

                                               <div class="formRow" style="display:none">
                                                  <span class="span4">
                                                      <label>Código de Referência:</label>
                                                      <input style="margin-left: 0px;" type="text" name="cod_ref" value="" onkeydown="Mascara(this,Integer);" onkeypress="Mascara(this,Integer)" onkeyup="Mascara(this,Integer)"/>
                                                  </span>
                                                   <span class="span6">
                                                      <label>Tipo de Conta:</label>
                                                      <select name="tp_conta">
                                                        <option value="1" id="tp_conta_a">(A) Analítico</option>
                                                        <option value="2" id="tp_conta_s">(S) Sintético</option>
                                                      </select>
                                                  </span>
                                              </div>
                                              
                                                <div class="formRow"> 
                                                  <span class="span12">
                                                     <label>Descrição:</label>
																										<textarea name="descricao" cols="auto" rows="3"></textarea>
                                                   </span>
                                              </div>

                                      </div>  <!-- fluid -->               
                              <!--   </div> widget -->               
                         </div>
                       
                    </div>
						</form> 
						                     
              </div><!-- Fim dialog --> 
