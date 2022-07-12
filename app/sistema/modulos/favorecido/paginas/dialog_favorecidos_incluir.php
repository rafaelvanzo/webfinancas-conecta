   						<!-- Caixa de Dialogo Conta a Receber -->
              <div id="dialog-message-favorecido-incluir" style="height:auto; padding:0;" title="Novo Favorecido">
								
                <form id="form_favorecidos" class="dialog">
                	<input type="hidden" name="funcao" value="favorecidosIncluir">
                    <div class="toggle acc" style="margin:0;">      
                      <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Favorecido</h6></div>
                        <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
    								                      
                           <!--       <div class="widget" style="margin:0;">  -->
                                     <div class="fluid">      

                                              <div class="formRow">
                                                  <span class="span4">
                                                      <label>Nome:</label>
                                                      <input type="text" name="nome" value="" class="required"/>
                                                  </span>
                                                  <span class="span2">
                                                  		<label>Inscrição:</label>
                                                      <select name="inscricao" class="inscricao" id="inscIncluir" onchange="cpfCnpj('inscIncluir');">
                                                        <option value="cpf" >CPF</option>
                                                        <option value="cnpj" >CNPJ</option>
                                                      </select>
                                                  </span>
                                                  <span class="span3">
                                                  		<label>CPF / CNPJ</label>
                                                  		<input type="text" name="cpf_cnpj" class="cpf_cnpj maskCpf" value=""/>
                                                  </span>
                                                  <span class="span3">
                                                  		<label>Tipo:</label>
                                                      <select name="tp_favorecido" class="required">
                                                        <option value="1">Cliente</option>
                                                        <option value="2">Fornecedor</option>
                                                        <option value="3" selected>Cliente / Fornecedor</option>
                                                      </select>
                                                  </span>
                                               </div>

                                               <div class="formRow">
                                                  <span class="span6">
                                                      <label>Logradouro:</label>
                                                      <input type="text" name="logradouro" value=""/>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Nº:</label>
                                                      <input type="text" name="numero" value=""/>
                                                  </span>
                                                  <span class="span4">
                                                   <label>Complemento:</label>
                                                   <input type="text" name="complemento" value=""/>
                                                  </span>
                                                  
                                              </div>
                                              
                                                <div class="formRow"> 
                                                	<span class="span4">
                                                      <label>Bairro:</label>
                                                      <input type="text" name="bairro" value=""/>
                                                  </span>
                                                  <span class="span4">
                                                      <label>Cidade:</label>
                                                      <input type="text" name="cidade" value=""/>
                                                  </span>
                                                  <span class="span2">
                                                     <label>UF:</label>
                                                      <select name="uf">
																											<?php 
                                                      $m_uf = mysql_query("select uf from uf");
                                                      while($uf = mysql_fetch_assoc($m_uf)){
                                                        echo "<option value=".$uf[uf].">".$uf[uf]."</option>";
                                                      }
                                                      ?>
                                                      </select>
                                                  </span>
                                                  <span class="span2">
                                                      <label>CEP:</label>
                                                      <input type="text" name="cep" value="" class="maskCep"/>
                                                  </span>
                                              </div>
                                               
                                               <div class="formRow">
                                                 <span class="span4">
                                                      <label>E-mail:</label>
                                                      <input type="text" name="email" value="" />
                                                  </span>
                                                  <span class="span4">
                                                      <label>Telefone:</label>
                                                      <input type="text" name="telefone" value="" class="maskPhone"/>
                                                  </span>    
                                                   <span class="span4">
                                                      <label>Celular:</label>
                                                      <input type="text" name="celular" value="" class="maskPhone"/>
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
                     
                    	  <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/clipboard.png" alt="" class="titleIcon"><h6>Dados Bancários</h6></div>
                        <div class="menu_body" style="color: rgb(64, 64, 64); display: none;">
                                  
                                  <div class="fluid">      
                                              
                                                <div class="formRow"> 
                                                	<span class="span6 input-autocomplete-container">
                                                      <label>Banco:</label>
                                                      <input type="text" style="margin-left: 0px;"  name="bancos_buscar" value="" class="bancos_buscar_id ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                                                      <input type="hidden" name="banco_id" value="" id="bancos_buscar">
                                                  </span>
                                                   <span class="span2">
                                                     <label>Tipo conta:</label>
                                                      <select name="tp_conta">
                                                        <option value="cc">Corrente</option>
                                                        <option value="pp">Poupança</option>
                                                      </select>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Agência:</label>
                                                      <input type="text" name="agencia" value="" class=""/>
                                                  </span>
                                                  <span class="span2">
                                                     <label>Conta:</label>
                                                      <input type="text" name="conta" value="" class=""/>
                                                  </span>

                                              </div>

                                      </div>  <!-- fluid -->  
                                          
                      		 </div>
                       
                    </div>
						</form> 
						                     
              </div><!-- Fim dialog --> 
