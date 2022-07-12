   						<!-- Caixa de Dialogo Conta a Receber -->
              <div id="dialog-centro-resp-editar" style="height:auto; padding:0;" title="Editar Centro">
								
                <form id="form_centro_resp_editar" class="dialog">
                		<input type="hidden" name="funcao" value="centroRespEditar">
                		<input type="hidden" name="centro_resp_id" value="">
                    <input type="hidden" name="centro_pai_id_ini" id="centro_pai_id_ini" value="">
                    <input type="hidden" name="cod_ref_ini" id="cod_ref_ini" value="">
   	                <input type="hidden" name="nivel" value="" />
	                  <input type="hidden" name="posicao" value="" />
                    <div class="toggle acc" style="margin:0;">
                    <!--  <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/frames.png" alt="" class="titleIcon"><h6>Dados do Centro</h6></div> -->
                        <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
    								                      
                           <!--       <div class="widget" style="margin:0;">  -->
                                     <div class="fluid">      

                                              <div class="formRow">
                                                  <!--
                                                  <span class="span4">
                                                      <label>Código do Centro:</label>
                                                      <input style="margin-left: 0px;" type="text" name="cod_centro" value="" readonly/>
                                                  </span>
                                                  -->
                                                  <span class="span5 input-autocomplete-container">
                                                       <label>Centro Pai:</label>
                                                        <input type="text" name="centro_pai_id_edit" id="nm_ctr_pai_id_edit" value="" class="centro_resp_buscar_ctr ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">
                                                        <input type="hidden" name="centro_pai_id" value="" id="centro_pai_id_edit">
                                                  </span>
                                                  <span class="span5">
                                                      <label>Nome do Centro:</label>
                                                      <input type="text" name="nome" value="" class="required"/>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Situação:</label>
                                                      <select name="situacao" class="select">
                                                        <option value="1">Ativo</option>
                                                        <option value="0">Inativo</option>
                                                      </select>
                                                  </span>
                                               </div>
                                               <div class="formRow" style="display:none"> 
                                                  <span class="span6">
                                                      <label>Código de Referência:</label>
                                                      <input style="margin-left: 0px;" type="text" name="cod_ref" value="" onkeydown="Mascara(this,Integer);" onkeypress="Mascara(this,Integer)" onkeyup="Mascara(this,Integer)"/>
                                                  </span>
                                                  <span class="span4">
                                                      <label>Tipo de Centro:</label>
                                                      <select name="tp_centro" class="required">
                                                        <option value="1" id="tp_centro_a">(A) Analítico</option>
                                                        <option value="2" id="tp_centro_s">(S) Sintético</option>
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
