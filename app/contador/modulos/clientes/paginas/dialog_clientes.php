<!-- Convite Contador -->

<div id="dialog-informativo" style="height: auto; padding:0; text-align: center; display:none; overflow:hidden;" title="Novo informativo">
  <form id="formInfo" action="#" class="dialog"> 
   <!-- <input type="hidden" id="campoId" name="id" value="" /> -->   
<div class="fluid">
    
    <br />

    <h6 align="left" style="padding-left:15px;">Dados de Acesso ao sistema</h6>
    
    <div class="linha" style="margin-top:5px;"></div>
    
                         <div class="formRow">
                             <span class="span6">
                                <label>E-mail (login):</label>
                                <input type="text" name="email" value="" class="email required">
                            </span>
                             <span class="span6 mSenha">
                                <label>Senha:</label>
                                <input type="password" name="senha" value="" class="senha required">
                            </span>
                         </div>
    
    <br />
    
    <h6 align="left" style="padding-left:15px;">Dados ClIente</h6>
    
      <div class="linha" style="margin-top:5px;"></div>
                  
                         <div class="formRow">
                            <span class="span6">
                                <label>Nome:</label>
                                <input style="margin-left: 0px;" type="text" name="nome" value="" class="nome required">
                            </span>
                            <span class="span2">
                                <label>Inscrição:</label>
                                 <select name="inscricao" class="inscricao " id="inscIncluir" onchange="cpfCnpj('inscIncluir');">
                                  <option value="CPF">CPF</option>
                                  <option value="CNPJ">CNPJ</option>
                                </select>
                            </span>
                            <span class="span4">
                                <label>CPF / CNPJ:</label> 																																				                      
                                <input type="text" name="cpf_cnpj" value="" class="cpf_cnpj maskCpf required" placeholder="___.___.___-__">
                            </span>

                         </div>

           <div class="linha" style="margin-top:5px;"></div>

                         <div class="formRow"> 
                            <span class="span6">
                                <label>Logradouro:</label>
                                <input type="text" name="logradouro" value="" class="logradouro required">
                            </span>
                            <span class="span2">
                                <label>Nº:</label>
                                <input type="text" name="numero" value="" class="numero required">
                            </span>
                            <span class="span4">
                             <label>Complemento:</label>
                                <input type="text" name="complemento" value="" class="complemento">
                            </span>

                        </div>

         <div class="linha" style="margin-top:5px;"></div>                
           
                          <div class="formRow"> 
                            <span class="span5">
                                <label>Bairro:</label>
                                <input type="text" name="bairro" value="" class="bairro required">
                            </span>
                            <span class="span5">
                                <label>Cidade:</label>
                                <input type="text" name="cidade" value="" class="cidade required">
                            </span>
                            <span class="span2"> 
                               <label>UF:</label>
                                <select name="uf" class="sFormRow uf required">
                                    <option value=""></option>
								    <option value="AC">AC</option><option value="AL">AL</option><option value="AM">AM</option><option value="AP">AP</option><option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES" selected="selected">ES</option><option value="GO">GO</option><option value="MA">MA</option><option value="MG">MG</option><option value="MS">MS</option><option value="MT">MT</option><option value="PA">PA</option><option value="PB">PB</option><option value="PE">PE</option><option value="PI">PI</option><option value="PR">PR</option><option value="RJ">RJ</option><option value="RN">RN</option><option value="RO">RO</option><option value="RR">RR</option><option value="RS">RS</option><option value="SC">SC</option><option value="SE">SE</option><option value="SP">SP</option><option value="TO">TO</option>                                
                                </select>
                           </span>
                          
                        </div>
                         
           <div class="linha" style="margin-top:5px;"></div>

                         <div class="formRow">
                             <span class="span4">
                                <label>CEP:</label>
                                <input type="text" name="cep" value="" class="maskCep cep required" placeholder="_____-__">
                            </span>
                            <span class="span4">
                                <label>Telefone:</label>
                                <input type="text" name="tel" value="" class="maskPhone telefone required" placeholder="(__) ____-_____">
                            </span>
                             <span class="span4">
                                <label>Celular:</label>
                                <input type="text" name="cel" value="" class="maskPhone celular" placeholder="(__) ____-_____">
                            </span>
                          </div>

<?php 
if($_SESSION['cliente_id'] == 342){
?>
            <div class="linha" style="margin-top:5px;"></div>

                         <div class="formRow">
                             <span class="span6">
                                <label>Agenda:</label>
                                <select name="agenda" value="" class="agenda required" >
                                    <option value="0">Inativo</option>
                                    <option value="1">Ativo</option>
                                </select>
                            </span>
                          </div>              
<?php 
}
?>
                      <div class="linha" style="margin-top:5px;"></div>

                         <div class="formRow">
                            
                            <span class="span12">
                                <label>Observação:</label>
                                <textarea name="observacao" rows="5" class="observacao"></textarea>
                            </span>
                            
                          </div>
    
    </div>  <!-- fluid --> 

  </form>
</div><!-- Fim dialog --> 

<!-- Convite Contador -->

<!-- 
ALTERAR SENHA DO CLIENTE
=================================================================================== 
-->

<div id="dialog-alterar-senha-cliente" style="height: auto; padding:0; text-align: center; display:none;" title="Alterar Senha Do Cliente">
  <form id="form-alterar-senha-cliente" action="#" class="dialog">
    <input type="hidden" name="funcao" value="alterarSenha" />
    <div class="fluid">
        <div class="formRow">
            <span class="span12">
                <label> Usuário:</label>
                <select id="usuario-cliente">
                </select>
            </span>
        </div>
        <div class="formRow">
            <span class="span12">
                <label> Nova Senha:</label>
                <input type="password" id="senha-cliente" name="senha" maxlength="20"/>
            </span>
        </div>
        <div class="formRow">
            <span class="span12">
                <label> Confirme a nova Senha:</label>
                <input type="password" id="repetir-senha-cliente" name="repetirSenha" maxlength="20" />
            </span>
        </div>
    </div>  <!-- fluid --> 
      <br />
  </form>
</div><!-- Fim dialog --> 