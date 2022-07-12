<!--
======================================================================
INCLUIR NOVA CONTA FINANCEIRA
======================================================================
-->

 <div id="dialog-message-conta-incluir" style="height:auto; padding:0;" title="Nova Conta Financeira" class="modal">
								
            <form id="form_contas" class="dialog">
                <input type="hidden" name="funcao" value="contasIncluir">

               <div class="fluid">      
                <div class="formRow">
                      <span class="span6">
                          <label>Descrição:</label>
                          <input type="text" name="descricao" value="" class="required"/>
                      </span>
                      <span class="span6 input-autocomplete-container">
                            <label>Banco:</label>
                          <input type="text" name="bancos_buscar" value="" id="bancos_buscar_inc" class="bancos_buscar_id ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">
                          <input type="hidden" name="banco_id" value="" id="bancos_buscar">
                      </span>
                   </div>
    
                   <div class="formRow">                       
                      <span class="span2">
                          <label>Agência:</label>
                          <input type="text" name="agencia" value=""/>
                      </span>
                       <span class="span1">
                            <label>DV:</label>
                            <input type="text" name="agencia_dv" value="" maxlength="2"/>
                        </span>
                      <span class="span2">
                          <label>Nº Conta:</label>
                          <input type="text" name="numero" value="" />
                      </span>
                       <span class="span1">
                            <label>DV:</label>
                            <input type="text" name="numero_dv" value="" maxlength="2"/>
                        </span>
                      <span class="span2">
                          <label>Saldo inicial:</label>
                          <input type="text" name="vl_saldo_inicial" value="0" class="moeda required"/>
                      </span>
                       <span class="span2">
                          <label>Crédito:</label>
                          <input type="text" name="limite_credito" id="limite_credito_inc" value="0" class="moeda required"/>
                      </span>
                       <span class="span2">
                          <label>Créd. utilizado:</label>
                          <input type="text" name="credito_usado" id="credito_usado_inc" value="0" class="moeda required"/>
                      </span>
                  </div>
                  
                      <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->  
                   
                   <div class="formRow">
                      
                     <span class="span4">
                          <label>Contato:</label>
                          <input type="text" name="contato" value="" />
                      </span>
                      <span class="span4">
                          <label>E-mail:</label>
                          <input type="text" name="contato_email" value="" />
                      </span>
                      <span class="span4">
                          <label>Telefone:</label>
                          <input type="text" name="contato_tel" value="" class="maskPhone"/>
                      </span>                                                  
                    </div>  
                     
                 <div class="formRow"> 
                      <span class="span12">
                         <label>Observação:</label>  
                         <textarea name="observacao" cols="auto" rows="2"></textarea>
                       </span>
                  </div> 
                </div>

		<!--=====================================-->
 
		<!--============ MAIS OPÇÕES ============-->

    <div class="title closed inactive MaisOpcoes" align="left"> 
    	<a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  
    </div>

    <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
<!--=====================================-->
                    
                    <div class="fluid">      

                                    <div class="formRow">
                                        <span class="span2">
                                            <label>Carnê leão:</label>
                                            <input type="checkbox" name="carne_leao" value="1" class="ckb-bootstrap" id="ckb-carne-leao01" />
                                        </span>
                                    </div>

                        <br />

                                    <div class="linha"></div>


                                 <div class="formRow">
                                    <!--
                                    <span class="span2 boletoCnfg">
                                       <label>Nº Sequencial:</label>
                                       <input type="text" name="sequencial" value="" class="maskNum" maxlength="11"/>
                                    </span>
                                    -->
                                     <span class="span2" id="span_carteira">                                         
                                       <label>Carteira</label>
                                        <select name="carteira">
                                          <option value=""></option>
                                          <option value="11">11</option>
                                          <option value="16">16</option>
                                          <option value="17">17</option>
                                          <option value="18">18</option>
                                        </select>
                                    </span>

                                    <span class="span2 boletoCnfg" id="variacao">
                                       <label>Variação:</label>
                                        <input type="text" name="variacao" value="" class="maskNum"/>
                                    </span>
                                    <span class="span2 boletoCnfg" id="convenio">
                                       <label>Convênio:</label>
                                        <input type="text" name="convenio" value="" class="maskNum" maxlength="7"/>
                                    </span>
                                    <span class="span2 boletoCnfg" id="modalidade" style="display:none">
                                       <label>Modalidade:</label>
                                       <input type="text" name="modalidade" value="01" class="maskNum" maxlength="2"/>
                                    </span>
                                
                                </div>
                                <div class="formRow">
                                    
                                     <?php 
			                            $db_w2b = new mysqli('mysql.web2business.com.br', 'web2business', 'W2BSISTEMAS', 'web2business');
			                            $cliente_id = $_SESSION['cliente_id'];
			                            $dados_perfil_usuario = mysqli_fetch_assoc(mysqli_query($db_w2b,'select inscricao, cpf_cnpj, nome from clientes where id = '.$cliente_id));			
			                            $db_w2b->close(); 
			                         ?> 
                                         
                                      <span class="span6" id="nomeTitular">
                                       <label>Nome do Títular:</label>
                                        <input type="text" name="nomeTitular" value="<?php echo $dados_perfil_usuario['nome']; ?>" />
                                    </span>
                                    <span class="span2">
                                        <label>Inscrição:</label>
                                        <select name="inscricao" class="inscricao" id="inscIncluir" onchange="cpfCnpj('inscIncluir');">
                                        <option value="CPF" <?php if($dados_perfil_usuario['inscricao'] == "CPF"){ echo 'selected="selected"'; } ?> >CPF</option>
                                        <option value="CNPJ" <?php if($dados_perfil_usuario['inscricao'] == "CNPJ"){ echo 'selected="selected"'; } ?> >CNPJ</option>
                                        </select>
                                    </span>
                                    <span class="span4">
                                        <label>CPF / CNPJ</label>
                                        <input type="text" name="cpf_cnpj" class="cpf_cnpj  <?php if($dados_perfil_usuario['inscricao'] == "CNPJ"){ echo 'maskCnpj'; }else{ echo 'maskCpf'; } ?> cpfCnpjValid" value="<?php echo $dados_perfil_usuario['cpf_cnpj']; ?>" />
                                    </span>

                                 </div>
                        
                                <div class="formRow">
                                    <span class="span3">
                                    <label>Custo de emissão:</label>
                                    <input type="text" name="custo_emissao" value="0,00" class="moeda"/>
                                </span>
                                    <span class="span3">
                                    <label>Custo de compensação:</label>
                                    <input type="text" name="custo_compensacao" value="0,00" class="moeda"/>
                                </span>
                                    <span class="span3">
                                        <label>Valor da multa:</label>
                                        <input type="text" name="multa" value="00,00%" class="maskPct"/>
                                    </span>
                                     <span class="span3">
                                        <label>Valor do juros mensal:</label>
                                        <input type="text" name="juros" value="00,00%" class="maskPct"/>
                                    </span>
                                  </div>

                        <div class="linha"></div>  <!-- Linha -->   

                                  <div class="formRow">
                                    <span class="span6">
                                       <label>Mensagens para o cliente:</label>
                                       <input type="text" name="msg1" value="" class="" placeholder="Mensagem 1"/>
                                    </span>
                                    <span class="span6">
                                       <label>Instruções para o caixa:</label>
                                       <input type="text" name="inst1" value="" class="" placeholder="Instrução 1"/>
                                    </span>
                                    <div class="fluid">
                                      <span class="span6">
                                         <input type="text" name="msg2" value="" class="" placeholder="Mensagem 2"/>
                                      </span>
                                      <span class="span6">
                                         <input type="text" name="inst2" value="" class="" placeholder="Instrução 2"/>
                                      </span> 
                                    </div>
                                    <div class="fluid">
                                      <span class="span6">
                                         <input type="text" name="msg3" value="" class="" placeholder="Mensagem 3"/>
                                      </span>
                                      <span class="span6">
                                         <input type="text" name="inst3" value="" class="" placeholder="Instrução 3"/>
                                      </span> 
                                    </div>
                                  </div>
                        
                        <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->    
                        
                    </div>  <!-- fluid -->  
                            
<!--=====================================-->                   
                           
                            
                 
          </div>  <!-- fluid -->                 
  </form>
</div><!-- Fim dialog --> 

<!--
======================================================================
EDITAR CONTA FINANCEIRA
======================================================================
-->

<div id="dialog-message-conta-editar" style="height:auto; padding:0;" title="Editar Conta Financeira" class="modal">
								
                <form id="form_contas_editar" class="dialog">
                		<input type="hidden" name="funcao" value="contasEditar">
                		<input type="hidden" name="conta_id" value="">
                		<input type="hidden" name="limite_credito_ini" value="">
                		<input type="hidden" name="credito_usado_ini" id="credito_usado_ini" value="">
                    <input type="hidden" name="vl_saldo_inicial_ini" value="">

               <div class="fluid">      
                 <div class="formRow">
                                                  <span class="span6">
                                                      <label>Descrição:</label>
                                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                                  </span>
                                                  <span class="span6 input-autocomplete-container">
                                                  		<label>Banco:</label>
                                                      <input type="text" name="bancos_buscar_editar" id="bancos_buscar_edit"  value="" class="bancos_buscar_id ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                                                      <input type="hidden" name="banco_id" value="" id="bancos_buscar_editar">
                                                     <!-- <select name="banco_id" class="required">
                                                                <option value=""></option>
                                                                <?php /*
                                                                $array_bancos = $db->fetch_all_array("select * from bancos order by nome");
                                                                foreach($array_bancos as $banco){
                                                                    echo '<option value="'.$banco[id].'">'.$banco[nome].'</option>';
                                                                } */
                                                                ?>
                                                      </select>-->
                                                  </span>
                                               </div>

                                               <div class="formRow"> 
                                                  
                                                  <span class="span2">
                                                      <label>Agência:</label>
                                                      <input type="text" name="agencia" value=""/>
                                                  </span>
                                                   <span class="span1">
                                                      <label>DV:</label>
                                                      <input type="text" name="agencia_dv" value="" maxlength="2"/>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Nº Conta:</label>
                                                      <input type="text" name="numero" value=""/> 
                                                  </span>
                                                   <span class="span1">
                                                      <label>DV:</label>
                                                      <input type="text" name="numero_dv" value="" maxlength="2"/>
                                                  </span>
                                                  <span class="span2">
                                                      <label>Saldo inicial:</label>
                                                      <input type="text" name="vl_saldo_inicial" value="0" class="moeda required" />
                                                  </span>
                                                   <span class="span2">
                                                      <label>Crédito:</label>
                                                      <input type="text" name="limite_credito" id="limite_credito_edit" value="0" class="moeda required"/>
                                                  </span>
                                                   <span class="span2">
                                                      <label>Créd. utilizado:</label>
                                                      <input type="text" name="credito_usado" id="credito_usado_edit" value="0" class="moeda required"/>
                                                  </span>
                                              </div>
                                              
                                                  <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->  
                                               
                                               <div class="formRow">
                                                  
                                                 <span class="span4">
                                                      <label>Contato:</label>
                                                      <input type="text" name="contato" value="" />
                                                  </span>
                                                  <span class="span4">

                                                      <label>E-mail:</label>
                                                      <input type="text" name="contato_email" value="" />
                                                  </span>
                                                  <span class="span4">
                                                      <label>Telefone:</label>
                                                      <input type="text" name="contato_tel" value="" class="maskPhone"/>
                                                  </span>                                                  
                                                </div> 
                                                 
                                             <div class="formRow"> 
                                                  <span class="span12">
                                                     <label>Observação:</label>
													<textarea name="observacao" cols="auto" rows="2"></textarea>
                                                   </span>
                                              </div> 
                  

<!--=====================================-->
 
<!--============ MAIS OPÇÕES ============-->
<div class="title closed inactive MaisOpcoes" align="left"> 
<a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

    <div class="body" style="display: block;"> <!-- Body Mais Opções -->

                                    <div class="formRow">
                                        <span class="span2">
                                            <label>Carnê leão:</label>
                                            <input type="checkbox" name="carne_leao" value="1" class="ckb-bootstrap" id="ckb-carne-leao02" />
                                        </span>
                                    </div>

                        <br />

                                    <div class="linha"></div>


     <div class="formRow">
<!--
            <span class="span2">
               <label>Nº Sequencial:</label>
               <input type="text" name="sequencial" value="" class="maskNum" maxlength="11"/>
            </span>
-->                                    
            <span class="span2" id="span_carteira_edit">
              <label>Carteira</label>
              <select name="carteira">
                <option value=""></option>
                <option value="11">11</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
              </select>
            </span>

            <span class="span2 boletoCnfg" id="variacao_edit" style="display:none">
               <label>Variação:</label>
                <input type="text" name="variacao" value="" class="maskNum"/>
            </span>
            <span class="span2 boletoCnfg" id="convenio_edit" style="display:none">
               <label>Convênio:</label>
                <input type="text" name="convenio" value="" class="maskNum" maxlength="7"/>
            </span>
            <span class="span2 boletoCnfg" id="modalidade_edit" style="display:none">
               <label>Modalidade:</label>
               <input type="text" name="modalidade" value="01" class="maskNum" maxlength="2"/>
            </span>

          </div>
        <div class="formRow">
                                         
            <span class="span6" id="nomeTitular">
            <label>Nome do Títular:</label>
            <input type="text" name="nomeTitular" value="" />
        </span>
        <span class="span2">
            <label>Inscrição:</label>
            <select name="inscricao" class="inscricao" id="inscEditar" onchange="cpfCnpj('inscEditar');">
            <option value="CPF" >CPF</option>
            <option value="CNPJ">CNPJ</option>
            </select>
        </span>
        <span class="span4">
            <label>CPF / CNPJ</label>
            <input type="text" name="cpf_cnpj" class="cpf_cnpj maskCpf cpfCnpjValid " value="" />
        </span>

        </div>

        <div class="formRow">
                    <span class="span3">
                    <label>Custo de emissão:</label>
                    <input type="text" name="custo_emissao" value="0,00" class="moeda"/>
                </span>
                    <span class="span3">
                    <label>Custo de compensação:</label>
                    <input type="text" name="custo_compensacao" value="0,00" class="moeda"/>
                </span>
                    <span class="span3">
                        <label>Valor da multa:</label>
                        <input type="text" name="multa" value="00,00%" class="maskPct"/>
                    </span>
                        <span class="span3">
                        <label>Valor do juros mensal:</label>
                        <input type="text" name="juros" value="00,00%" class="maskPct"/>
                    </span>
                    </div>

        <div class="linha"></div>  <!-- Linha --> 

          <div class="formRow">
            <span class="span6">
               <label>Mensagens para o cliente:</label>
               <input type="text" name="msg1" value="" class="" placeholder="Mensagem 1"/>
            </span>
            <span class="span6">
               <label>Instruções para o caixa:</label>
               <input type="text" name="inst1" value="" class="" placeholder="Instrução 1"/>
            </span>
            <div class="fluid">
              <span class="span6">
                 <input type="text" name="msg2" value="" class="" placeholder="Mensagem 2"/>
              </span>
              <span class="span6">
                 <input type="text" name="inst2" value="" class="" placeholder="Instrução 2"/>
              </span> 
            </div>
            <div class="fluid">
              <span class="span6">
                 <input type="text" name="msg3" value="" class="" placeholder="Mensagem 3"/>
              </span>
              <span class="span6">
                 <input type="text" name="inst3" value="" class="" placeholder="Instrução 3"/>
              </span> 
            </div> 

      </div>

        <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow --> 
<!--=====================================-->                           
                            
                 
          </div>  <!-- fluid -->                 
  </form>                  
</div><!-- Fim dialog --> 

<!--
======================================================================
INCLUIR NOVA REMESSA
======================================================================
-->

 <div id="dialog-message-arquivo-remessa-incluir" style="height:auto; padding:0;" title="Nova Remessa" class="modal">
								
            <form id="form_remessa" class="dialog">
                <input type="hidden" name="funcao" value="gerarRemessa">

                <h6 style="text-align:left;margin-left:15px; margin-top:10px;">Dados da Remessa</h6>

                <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                <p style="font-size:10px;text-align:left;margin-left:15px; margin-bottom:10px; margin-top:-8px;">Selecione os boletos que serão incluidos nessa remessa.</p>

               <div class="fluid">
                <div class="formRow">
                    <span class="span6"> 
  
                          <select name="conta_id" id="bancosId" onchange="visualizarBoletos();">
                              <option value="">Selecione uma conta financeira..</option>
                             <?php 
                             $array_contas = $db->fetch_all_array("SELECT id, banco_id, descricao FROM contas WHERE banco_id != 0"); 
                             foreach($array_contas as $contas){
                                 
                                 $bancos = $db->fetch_assoc("SELECT nome FROM bancos WHERE id =".$contas['banco_id']);
                                 
                                 echo "<option value=".$contas['id']."  style='line-height:20px;'>".$contas['descricao']." - ( ".$bancos['nome']." )</option>";
                             }      
                                   
                                   ?>
                          </select>

                      </span>  
                    <span class="span3" style="text-align:left;">                          
                                <b>Agência:</b> <span id="agencia"></span>
                            <br /><b>Total:</b> <span id="totalBoletos"></span>
                      </span> 
                    <span class="span3" style="text-align:left;">                          
                                <b>conta:</b> <span id="conta"></span>
                            <br /><b>Qtd.:</b> <span id="qtdBoletos"></span>
                      </span>                      
                   </div>
                   <div class="formRow">                       
                      <span class="span12" align="left">
                          <input type="checkbox" name="outrasRemessas"  id="outrasRemessas" value="1" onclick="visualizarBoletos();"/> <i>Visualizar boletos incluidos em outras remessas.</i>
                      </span>
                  </div>
                  
                   <br />

                   <h6 style="text-align:left;margin-left:15px;">Boletos da Remessa</h6>

                      <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                   <p style="font-size:10px;text-align:left;margin-left:15px; margin-top:-8px; margin-bottom:10px;">Selecione o banco para visualizar e gerar a remessa.</p>

                <div class="formRow">                
                          
                     <span class="span12">

                               <table cellpadding="0" cellspacing="0" border="0" width="100%" class="sTable" >     
                                    <tr class="gradeA">                          
                                          <th align="center" width="50"">Emissão</th>
                                          <th align="left"> &nbsp;&nbsp;&nbsp;&nbsp; Favorecido</th>
                                          <th align="left" width="50">Vencimento</th>
                                          <th align="center" width="100">Valor</th>
                                          <th align="center" width="30"> <input type="checkbox" class="checkAll" value="check1" title="Marcar todos os boletos" />  <img src="images/icons/tableArrows.png" alt="" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>
                                      </tr>                                  
                               </table>  
                         
                         <div class="selecionar" style="height:223px; max-height:400px; padding:0 0 0 0;">

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" max-height="250" class="sTable" >     
                                   <thead>
                                      <tr class="gradeA">                          
                                          <th align="center" width="10"></th>
                                          <th align="left"></th>
                                          <th align="left" width="60"></th>
                                          <th align="center" width="80"></th>
                                          <th width="30"></th>
                                      </tr>
                                  </thead>              
                                   <tbody id="listaBoletos" style="overflow-y:scroll;" >
                                    
                                       <tr height='50' align='center'><td  colspan='5' align='center'> Selecione uma conta financeira para visualizar os boletos. </td></tr>

                                 </tbody>
                             </table>

                             </div>

                      </span>                                         
               </div> 
            </div>

                <br />
                <div class="linha"></div>   <!--Linha deve estar no ultimo formRow -->
        </div>
		<!--=====================================-->
 
	               <!-- <div class="linha"></div>   Linha deve estar no ultimo formRow -->   
                 
          </div>  <!-- fluid -->                 
  </form>
</div><!-- Fim dialog --> 