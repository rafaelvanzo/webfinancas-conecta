   					<!-- Caixa de Dialogo Alterar Senha -->  
              <div id="dialog-cancelar" style="height: auto;  padding:0; text-align: center;" title="Cancelar">

             <form id="formCancelar" action="#" class="dialog">
             <input type="hidden" name="funcao" value="cancelar" />
              
              <div class="fluid">
                            <div class="formRow">
                                <span class="span12">
                                	- Após o cancelamento o sistema ficará ativo até o término do período contratado. <br />
                                  - Após a suspensão do serviço os dados permaneceram em nossos servidores até 30 dias após término do período contratado. <br />
                                  - O cliente poderá reativar a sua conta acessando o sistema utilizando o login e senha já cadastrados até 30 dias após o término do período contratado, desde de que aceitem os termos e paguem por mais pelo período que desejam contratar.
                                </span>
                            </div>                    
                            
                            <div class="formRow">
                                <span class="span12">
                                   <label>Usuário:</label>
                                   <input type="text" name="usuario" value="<?php echo $_SESSION['email'];?>" disabled="disabled" />
                                   <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario_id'];?>" />
                                </span>
                             </div>

                             <div class="formRow">
                                <span class="span12">
                                    <label> Qual o motivo do cancelamento? *</label>
                                    <textarea name="motivo" rows="5"></textarea>
                                </span>
                            </div>
                            
                            <div class="formRow">
                                <span class="span12">
                                     <label> Senha para confirmar: *</label>
                                     <input type="password" id="repetir_senha" name="repetir_senha" maxlength="12" />
                                </span>
                            </div>

                    </div>  <!-- fluid --> 
              
              </form>
              </div><!-- Fim dialog --> 
