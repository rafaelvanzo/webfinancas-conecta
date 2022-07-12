<!-- Convite Contador -->

<div id="dialog-add-mensagem" style="height: auto; padding:0; text-align: center; display:none;" title="Nova solicitação">
  <form id="formAddMensagem" action="#" class="dialog"> 
    <input type="hidden" name="funcao" value="addMensagem" />
      
<div class="fluid">
                 
    <div class="formRow">

        <span class="span12">
<br />
             <select name="solicitacao" class="solicitacao required" >
                 <option value="">Selecione o tipo de solicitação..</option>
                 <option value="1">Admissão de empregados</option>
                 <option value="2">Recálculo de impostos e contribuições</option>
                 <option value="3">Rescisão contratual</option>
                 <option value="4">Outras solicitações / dúvidas</option>
             </select>

         </span>

</div>

<div class="1" style="display:none;">
      <div class="formRow">
          <span class="span12 input-autocomplete-container">
              <label>Nome do funcionário:</label>
            <input style="margin-left: 0px;" type="text" name="form_rcbt_funcionario_id" id="form_rcbt_funcionarios" value="" class="funcionarios_buscar input-buscar required" placeholder="Preencha para localizar..." data-tp-lnct="R" data-form-id="form_rcbt" />
            <input type="hidden" name="funcionario_id" id="form_rcbt_funcionario_id" value="" />
         </span>
    </div>
</div>

<div class="2" style="display:none;">

      <div class="formRow">
          <span class="span12">
            <select class="recalculo required" name="recalculo_tp" style="margin-top:9px;">
                 <option value="" selected="selected">Selecione o recálculo..</option>
				<option value="1">CONFINS</option>   
				<option value="2">CSLL</option>   
				<option value="3">DAS</option>
				<option value="4">FGTS</option>
				<option value="5">GPS</option>   
				<option value="6">IRPJ</option>   
				<option value="7">ISS</option>   
				<option value="8">PIS</option>
            </select>
         </span>
 </div>
 <div class="formRow">          
           <span class="span12 GPS"  style="display:none;">
            <select class="tpGPS required" name="recalculo_tp_gps" style="margin-top:9px;">
                <option value="" >Selecione o tipo do GPS..</option>
				<option value="1">GPS Trabalhador</option>   
				<option value="2">GPS Trabalhador autonômo</option>   
				<option value="3">GPS Trabalhador doméstica</option>
            </select>
         </span>

</div>
<div class="formRow">          

          <span class="span6">
              <label style="width:100%;">Data de competência</label>
              <input type="text" name="recalculo_dt_competencia" placeholder="__/____" class="required datepickerM monthpicker maskDate"/>    
          </span>
          <span class="span6">
              <label style="width:100%;">Data de pagamento</label>
              <input type="text" name="recalculo_dt_pgto" placeholder="__/__/____"  class="required datepicker maskDate" />
          </span>
    </div>

</div>
<div class="3" style="display:none;">
      <div class="formRow">

          <span class="span6 input-autocomplete-container">
              <label>Nome do funcionário</label>
            <input style="margin-left: 0px;" type="text" name="form_rcbt_rescisao_funcionario_id" id="form_rcbt_rescisao_funcionario" value="" class="funcionarios_buscar input-buscar required" placeholder="Preencha para localizar..." data-tp-lnct="R" data-form-id="form_rcbt" />
            <input type="hidden" name="rescisao_funcionario_id" id="form_rcbt_rescisao_funcionario_id" value="" />
         </span>
          <span class="span6">
               <label style="width:100%;">Data de demissão</label>
            <input type="text" class="datepicker maskDate required" name="rescisao_data" placeholder="__/__/____" >
         </span>
</div>
<div class="formRow">          
           <span class="span6">
              <label>Modalidade de aviso</label>
            <select class="required" name="rescisao_modalidade" style="margin-top:-1px;">
                <option value="">Selecione a modalidade de aviso..</option>
                <option value="1">Indenizado</option>
                <option value="2">Trabalhado</option>
                <option value="3">Quebra / Término de contrato</option>
            </select>
         </span>

          <span class="span6">
              <label>Solicitante</label>
            <select class="required" name="rescisao_solicitante" style="margin-top:-1px;">
                <option value="">Selecione o solicitante..</option>
                <option value="1">Colaborador</option>
                <option value="2">Empregador</option>
            </select>
         </span>
    </div>

</div>
<div class="4" style="display:none;">
      <div class="formRow">
         <span class="span12">
            <input type="text" class="required" name="assunto" placeholder="Preencha o assunto">
         </span>
    </div>
</div>

      <div class="formRow">
        <span class="span12">
            <textarea name="mensagem" class="required" placeholder="Mensagem"  rows="15" cols="80"></textarea>
       </span>
      </div>
      <div class="formRow">
          <span class="span12">
              <a href="#" class="wContentButton bluewB" style="color:#FFFFFF;margin-top:5px;" onclick="mensagem_add();">Enviar</a>
              <br />
          </span>
      </div>
    
    </div>  <!-- fluid --> 
  </form>
</div><!-- Fim dialog --> 

<!-- Convite Contador -->