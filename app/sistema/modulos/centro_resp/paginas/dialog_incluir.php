   						<!-- Caixa de Dialogo Conta a Receber -->
              <div id="dialog-centro-resp-incluir" style="height:auto; padding:0;" title="Novo Centro">
								
                <form id="form_centro_resp" class="dialog">
                	<input type="hidden" name="funcao" value="centroRespIncluir">
	                  <input type="hidden" name="cod_centro" value="" />
  	                <input type="hidden" name="nivel" value="" />
	                  <input type="hidden" name="posicao" value="" />
                    <div class="toggle acc" style="margin:0;">      
                    <!--  <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/frames.png" alt="" class="titleIcon"><h6>Dados do Centro</h6></div> -->
                        <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
    								                      
                           <!--       <div class="widget" style="margin:0;">  -->
                                     <div class="fluid">      
                      
                                              <div class="formRow">
                                              <!--    <span class="span4">
                                                      <label>Código da Conta:</label>
                                                      <input style="margin-left: 0px;" type="text" name="cod_conta" value="" class="required"/>
                    													 </span> -->
                     														<span class="span6 input-autocomplete-container">
                                                     <label>Centro Pai:</label>
                                                      <input type="text" name="buscar_centro_pai_id" value="" id="nm_ctr_pai" class="centro_resp_buscar_ctr ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">
                                                      <input type="hidden" name="centro_pai_id" value="" id="buscar_centro_pai_id"> 
                                                </span>
                                                <span class="span6">
                                                    <label>Nome do Centro:</label>
                                                    <input type="text" name="nome" value="" class="required"/>
                                                </span>
                                                <span class="span2" style="display:none">
                                                    <label>Código de Ref.:</label>
                                                    <input type="text" name="cod_ref" value="" onkeydown="Mascara(this,Integer);" onkeypress="Mascara(this,Integer)" onkeyup="Mascara(this,Integer)"/>
                                                </span>
                                              </div>

                                               <div class="formRow" style="display:none">
                                                <span class="span6">
                                                      <label>Tipo de Centro:</label>
                                                      <select name="tp_centro" class="required">
                                                        <option value="1" selected>(A) Analítico</option>
                                                        <option value="2">(S) Sintético</option>
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
