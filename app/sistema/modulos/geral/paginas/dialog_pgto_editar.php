   						<!-- Caixa de Dialogo Conta a Receber -->
              <div id="dialog-pgto-editar" style="height:auto; padding:0;" title="Editar Pagamento">

                <form id="form_pgto_editar" class="dialog">

                		<input type="hidden" name="funcao" value="pagamentosEditar">
										<input type="hidden" name="compensado" value="1">
                		<input type="hidden" name="lancamento_id" value="">
                    <input type="hidden" name="tipo" value="P" />
						  			<input type="hidden" name="conta_id_ini" value="" />
						  			<input type="hidden" name="valor_ini" value="" />

										<div class="toggle acc" style="margin:0;">
                      <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Lançamento</h6></div>
                        <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
    								                      
                           <!--       <div class="widget" style="margin:0;">  -->
                                     <div class="fluid">      
                                              <div class="formRow">
                                                  <span class="span6">
                                                      <label>Descrição:</label>
                                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                                  </span>
                                                  <span class="span6">
                                                      <label>Pagar para:</label>
                                                 		  <input style="margin-left: 0px;" type="text" name="favorecido_rcbt_editar_id" value="" class="required favorecido_buscar" placeholder="Preencha para localizar..." />
                                                      <input type="hidden" name="favorecido_id" id="favorecido_rcbt_editar_id" value=""/>
                                                  </span>
                                               </div>

                                               <div class="formRow">
                                                  <span class="span6">
                                                      <label>Conta:</label>
                                                      <input style="margin-left: 0px;" type="text" name="conta_rcbt_editar_id" value="" class="required conta_buscar" placeholder="Preencha para localizar..." />
                                                      <input type="hidden" name="conta_id" value="" id="conta_rcbt_editar_id"/>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Emissão:</label>
                                                      <input style="margin-left: 0px;" type="text" name="dt_emissao" value="<?php echo date('d/m/Y')?>" class="required datepicker"/>
                                                  </span>
                                                  <span class="span4">
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
                                              </div>
                                              <div class="formRow">
                                                  <span class="span2">
                                                      <label>Vencimento:</label>
                                                      <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker"/>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Compensaçao:</label>
                                                      <input style="margin-left: 0px;" type="text" name="dt_compensacao" value="<?php echo date('d/m/Y')?>" class="required datepicker"/>
                                                  </span>
                                                  <span class="span4">
                                                      <label>Valor:</label>
                                                      <input style="margin-left: 0px;" type="text" name="valor" value="" class="required moeda"/>
                                                  </span>
                                                  <span class="span4">
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

                                      </div>  <!-- fluid -->               
                              <!--   </div> widget -->               
                         </div>
                     
                       <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/frames.png" alt="" class="titleIcon"><h6>Plano de Contas</h6></div>
                        <div class="menu_body" style="color: rgb(64, 64, 64); display: none;">
                           Plano de contas
                       </div>
                      
                      <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Centro de Responsabilidade</h6></div>
                        <div class="menu_body" style="display: none; color: rgb(64, 64, 64);">
                   				Centro de custo
                        </div>                      
                     
                        <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/clipboard.png" alt="" class="titleIcon"><h6>Observação</h6></div>
                        <div class="menu_body" style="color: rgb(64, 64, 64); display: none;">
                        	<textarea name="observacao" rows="15" cols=""></textarea>
                       </div>
                       
                    </div>						
                    
             		</form>
						                     
              </div><!-- Fim dialog --> 
