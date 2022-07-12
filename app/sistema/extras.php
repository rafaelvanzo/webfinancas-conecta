<!-- =================================================================================== 
DIALOG ALERTA
-->

<div id="dialog-alerta" style="height: auto; padding: 0 20px 0 20px; font-weight: bold; font-size: 12px; text-align: center; vertical-align: middle;" title="Alerta"></div>

<!-- =================================================================================== 
DIALOG ALTERAR SENHA
-->

<div id="dialog-alterar-senha" style="height: auto; padding:0; text-align: center; display:none;" title="Alterar senha">
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
              <input type="password" id="senha" name="senha" maxlength="20"/>
          </span>
      </div>
      
      <div class="formRow">
          <span class="span12">
               <label> Confirme a nova Senha: *</label>
               <input type="password" id="repetir_senha" name="repetir_senha" maxlength="20" />
          </span>
      </div>
    </div>  <!-- fluid --> 
  </form>
</div><!-- Fim dialog --> 

<!-- =================================================================================== 
DIALOG AGUARDE
-->

<span style="position: absolute; left: 50%; top: 50%; z-index: 99999; width: 50px; height: 31px; border: 0px; text-align: center; display: none;" class="aguarde"> <img src="images/loaders/loader9.gif" alt=""> <br />Aguarde..</span>
<div class="ui-widget-overlay aguarde" style="z-index:1001;display:none"></div>

<!-- =================================================================================== 
DIALOG CANCELAR
-->


<div id="dialog-cancelar" style="height: auto;  padding:0; text-align: center; display:none;" title="Cancelar">

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
                       <input type="password" id="senha-cancelamento" name="senha" maxlength="12" />
                  </span>
              </div>

      </div>  <!-- fluid --> 

</form>
</div><!-- Fim dialog --> 

<!-- =================================================================================== 
DIALOG ADD FAVORECIDO AUTO COMPLETE
-->


<div id="dialog-favorecido-ac" style="height: auto;  padding:0; text-align: center; display:none;" title="Adicionar Favorecido">

<form id="formfavAC" action="#" class="dialog">
<input type="hidden" class="formOrigem" value="" />

<div class="fluid" style="min-height:148px;">                 
              
              <div class="formRow">
                  <span class="span12">
                     <label>Nome: </label>
                     <input type="text" class="favNome required" name="nome" value="" />
                    
                  </span>
               </div>
               <div class="formRow">
                  <span class="span4">
                     <label>Inscrição:</label>
                     <select id="favInsc" class="favInsc" name="inscricao" onchange="cpfCnpjChangeMask('favInsc');">
                         <option value="cpf">CPF</option>
                         <option value="cnpj">CNPJ</option>
                     </select>
                    
                  </span>
                  <span class="span8">
                     <label>CPF/CNPJ:</label>
                     <input type="text" class="favCPF_CNPJ cpf_cnpj maskCpf  <?php if($_SESSION['carne_leao']==1) echo 'required carneLeao';?>"  name="cpf_cnpj" value=""/>
                    
                  </span>
               </div>


      </div>  <!-- fluid --> 

</form>
</div><!-- Fim dialog --> 