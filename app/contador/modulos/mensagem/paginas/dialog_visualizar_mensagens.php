<!-- dialog mensagens -->
<style>

</style>
<div id="dialog-visualizar-mensagem" style="height: auto; padding:0; text-align: center; display:none;" title="">
  <form id="formVisualizarMensagem" action="#" class="dialog"> 
      <input type="hidden" class="chat_categoria_id" name="chat_categoria_id" value="" />
      <input type="hidden" class="qtd_msg_inicio" name="qtd_msg_inicio" value="0" />
      <input type="hidden" id="qtd_msg" name="qtd_msg" value="10" />

      <div class="fluid">

<div class="tab-bs">
               
<ul class="nav nav-tabs" id="abas-dialog-mensagens">
	<li class="aba1  active"><a data-target="#aba-mensagens" data-toggle="tab" aria-expanded="true">Mensagens</a></li>
	<li class="aba2"><a data-target="#aba-solicitacao" data-toggle="tab" aria-expanded="false">Detalhes da solicitação</a></li>
</ul>

<div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

<div class="tab-content">

        <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

		<div class="aba1 tab-pane active" id="aba-mensagens"> 
            <span class="alertaNovaMsg" style="display:none;"><b>Você recebeu novas mensagens</b></span>
      <div class="chat">

                  

              <ul id="chatMsg" >
                  <!-- <li class="chat_dt">25 de Julho de 2016</li> 
                  <li class="chat_li">
                      <div class="chat_left">
                        <b>Fulano de tal</b> <span class="chat_hora_right">16:10</span> <br />
                        <p> Olá, Rafael Tudo bom?Olá, Rafael Tudo bom? </p>
                      </div>
                  </li>               
              
                  <li class="chat_li">
                      <div class="chat_right">
                        <span class="chat_hora_left">16:10</span> <span class="chat_nome_right"><b>Rafael Vanzo</b></span> <br /> 
                        <p> Tudo ótimo e com você? </p>
                    </div>
                  </li>  -->
              </ul>

       </div>        
          
      <div class="formRow">
        <span class="span12 ">
            <textarea class="textoMsg" name="mensagem" placeholder="Mensagem"  rows="5" cols="80"></textarea>
            <a href="javascript:void(0)" class="wContentButton bluewB bt_textarea_msg" style="color:#FFFFFF;margin-top:0px;" onclick="enviarMensagem();">Enviar</a>
            <br />
         </span>
          
        </div>
    
   
</div> 
                    
<!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

<!-- aba 2 --------------------------------------------------------------------------------------------------------------->

<div class="tab-pane aba2" id="aba-solicitacao">
                
    <div id="detalhes-solicitacao" >


    </div>
        
        
	</div>
        <!-- fim aba 2 --------------------------------------------------------------------------------------------------------------->
</div>


    </div>  <!-- fluid --> 
  </form>
</div><!-- Fim dialog --> 

<!-- dialog mensagens -->