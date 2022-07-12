   					<!-- Caixa de Dialogo Conta a Receber -->  
              <div id="dialog-message-bloqueio" style="height: auto; padding:0; font-weight: bold; font-size: 12px; text-align: center; vertical-align: middle;" title="Alerta">
             
                    <!-- Usual wizard with ajax -->
   <!--     <div class="widget">
            <div class="title"><img src="images/icons/dark/pencil.png" alt="" class="titleIcon" /><h6>Wizard with ajax form submit</h6></div> -->
			<form id="wizard1" method="post" action="submit.html" class="form">
                <fieldset class="step" id="w1first">
								 <div class="fluid">      
      
                              <div class="formRow">
                                  <span class="span6">
                                      <label>Nome:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="Rafael Ribeiro Vanzo" class="required" disabled="disabled"/>
                                  </span>
                                  <span class="span6">
                                      <label>E-mail:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="rafaelvanzo@gmail.com" class="required" disabled="disabled"/>
                                  </span>
                               </div>
      
 															<div class="formRow">
	                              <span class="span5">
                                      <label>Telefone:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="(27) 99907-7885" class="maskPhone"/>
                                  </span>                              
                                 <span class="span3">
                                        <label>Inscrição:</label>
                                        <select name="inscricao" class="inscricao" id="inscIncluir" onchange="cpfCnpj('inscIncluir');">
                                          <option value="cpf" >CPF</option>
                                          <option value="cnpj" >CNPJ</option>
                                        </select>
                                  </span>
                                     <span class="span4">
                                        <label>CPF / CNPJ</label>
                                        <input type="text" name="cpf_cnpj" class="cpf_cnpj maskCpf" value=""/>
                                   </span>                    						
                               </div>
                               
                                <div class="formRow">
                                  <span class="span6">
                                      <label>Logradouro:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                   <span class="span2">
                                      <label>Número:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span4">
                                      <label>Complemento:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                               </div>
                               
                                <div class="formRow">
                                  <span class="span4">
                                      <label>Bairro:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span3">
                                      <label>Cidade:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span2">
                                      <label>UF:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span3">
                                      <label>CEP:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                               </div>
										</div>
                
                </fieldset>
                <fieldset id="w1confirmation" class="step">
                    <div class="fluid">      
      
                              <div class="formRow">
                                  <span class="span6">
                                      <label>Nome:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" disabled="disabled"/>
                                  </span>
                                  <span class="span6">
                                      <label>E-mail:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required" disabled="disabled"/>
                                  </span>
                               </div>

                                <div class="formRow">
                                  <span class="span12">
                                      <label>Logradouro:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                               </div>
                               
                                <div class="formRow">
                                  <span class="span4">
                                      <label>Número:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span4">
                                      <label>Complemento:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span4">
                                      <label>Bairro:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                               </div>
                               
                               <div class="formRow">
                                  <span class="span4">
                                      <label>Cidade:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span4">
                                      <label>UF:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                  <span class="span4">
                                      <label>CEP:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                               </div>
								</div>
                
                </fieldset>
				<div class="wizButtons"> 
                    <div class="status" id="status1"></div>
					<span class="wNavButtons">
                        <input class="basic" id="back1" value="Cancelar" type="reset" />
                        <input class="blueB ml10" id="next1" value="Próximo" type="submit" />
                    </span>
				</div>
			</form>
			<div class="data" id="w1"></div>
      <!--  </div> -->
        
             
              </div><!-- Fim dialog --> 
