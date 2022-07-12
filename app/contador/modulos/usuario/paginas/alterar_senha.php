   					<!-- Caixa de Dialogo Alterar Senha -->  
              <div id="dialog-alterar-senha" style="height: auto;  padding:0; text-align: center;" title="Alterar senha">

             <form id="formAlterarSenha" action="#" class="dialog">
             <input type="hidden" name="funcao" value="senhaAlterar" />
              
              <div class="fluid">
                                             
                            <div class="formRow">
                                <span class="span12">
                                   <label>Usuário:</label>
                                   <input type="text" name="usuario" value="<?php echo $_SESSION['email'];?>" disabled="disabled" />
                                   <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario_id'];?>"
                                </span>
                             </div>

                             <div class="formRow">
                                <span class="span12">
                                    <label> Nova Senha: *</label>
                                    <input type="password" id="senha" name="senha" maxlength="12"/>
                                </span>
                            </div>
                            
                            <div class="formRow">
                                <span class="span12">
                                     <label> Confirme a nova Senha: *</label>
                                     <input type="password" id="repetir_senha" name="repetir_senha" maxlength="12" />
                                </span>
                            </div>

                    </div>  <!-- fluid --> 
              
              </form>
              </div><!-- Fim dialog --> 
