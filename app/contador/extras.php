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

<!-- =================================================================================== 
DIALOG AGUARDE
-->

<span style="position: absolute; left: 50%; top: 50%; z-index: 99999; width: 50px; height: 31px; border: 0px; text-align: center; display: none;" class="aguarde"> <img src="../sistema/images/loaders/loader9.gif" alt=""> <br />Aguarde..</span>
<div class="ui-widget-overlay aguarde" style="z-index:1001;display:none"></div>


<!-- =================================================================================== 
DIALOG INSTRUÇÕES  


<div id="dialog-ajudaListar" style="max-height: 510px; padding: 0; overflow:hidden;" title="Ajuda Inteligente">       
        <div style="position:relative; width:100%; height: 38px; border-bottom:1px solid #CCC;"> 
        	<input type="text" id="pesquisaAjuda" placeholder="Preencha para localizar.." style="max-width: 280px; margin-left:auto; margin-right:auto;"/> 
        </div>    
        <div class="semRegistro" align="center" style="display: none; margin-top: 150px; margin-bottom: -170px;">Nenhum registro encontrado.</div>   
       <div id="ajudaListar" class="fluid scroll" style="height:355px;"> 
       
              
       </div>      
         
</div>  --><!-- Fim dialog --> 

