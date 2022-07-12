<!--
======================================================================
INCLUIR RECEBIMENTOS
======================================================================
-->
<!-- Caixa de Dialogo Conta a Receber -->
<div id="dialog-rcbt-incluir" style="height:auto; padding:0;" title="Novo Recebimento" class="modal">

  <form id="form_rcbt" class="dialog">
    <input type="hidden" name="funcao" value="recebimentosIncluir">
    <input type="hidden" name="tipo" value="R">
    <input type="hidden" name="ct_resp_lancamentos" id="form_rcbt_ctr_plc_lnct" value="">

<div class="fluid">

<!--=====================================-->
  
           <div class="formRow">
              <span class="span6">
                  <label>Descrição:</label>
                  <input type="text" name="descricao" value="" class="required"/>
              </span>
              <span class="span4">
                  <label>Valor:</label>
                  <input type="text" name="valor" id="form_rcbt_valor" value="0,00" class="required moeda"/>
              </span>
              <span class="span2">
                  <label>Vencimento:</label>
                  <input type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate"/>
              </span>
           </div>

           
       <div class="formRow">
    	 <span class="span6 input-autocomplete-container">
              <label>Categoria:</label>
         	<input style="margin-left: 0px;" type="text" name="form_rcbt_pl_conta_id" id="form_rcbt_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar..." size=""/>
              <input type="hidden" id="form_rcbt_pl_conta_id" value="0"/>
          </span>
          <span class="span6 input-autocomplete-container">
              <label>Centro de custo:</label>
              <input style="margin-left: 0px; width" type="text" name="form_rcbt_ct_resp_id" id="form_rcbt_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar..." size=""/>
              <input type="hidden" id="form_rcbt_ct_resp_id" value="0"/>
          </span>
       </div>
                  
       
       <div class="formRow">
          <span class="span12">
            <label>Observação:</label>
             <textarea name="observacao" rows="2" cols="auto"></textarea> 
          </span>                                                                                                    
       </div>
        
                 
    </div>  <!-- fluid -->                 
  </form>            
</div><!-- Fim dialog --> 



<!-- ==============================================================================================================================
EDITAR RECEBIMENTO -->
<div id="dialog-rcbt-editar" style="height:auto; padding:0;" title="Editar Recebimento" class="modal">

  <form id="form_rcbt_editar" class="dialog">

      <input type="hidden" name="funcao" value="recebimentosEditar">
      <input type="hidden" name="lancamento_id" value="">
      <input type="hidden" name="tipo" value="R" />
      <input type="hidden" name="ct_resp_lancamentos" id="form_rcbt_editar_ctr_plc_lnct" value="">

<div class="fluid">    

<!--=====================================-->
  
           <div class="formRow">
              <span class="span6">
                  <label>Descrição:</label>
                  <input type="text" name="descricao" value="" class="required"/>
              </span>
              <span class="span4">
                  <label>Valor:</label>
                   <input type="text" name="valor" id="form_rcbt_editar_valor" value="" class="required moeda"/>
              </span>
              <span class="span2">
                  <label>Vencimento:</label>
                   <input type="text" name="dt_vencimento" value="" class="required datepicker maskDate"/>
              </span>
           </div>

           
       <div class="formRow">
    	 <span class="span6 input-autocomplete-container">
              <label>Categoria:</label>
                <input style="margin-left: 0px;" type="text" name="form_rcbt_editar_pl_conta_id" id="form_rcbt_editar_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
                <input type="hidden" id="form_rcbt_editar_pl_conta_id" value="0"/>
          </span>
          <span class="span6 input-autocomplete-container">
              <label>Centro de custo:</label>
               <input style="margin-left: 0px;" type="text" name="form_rcbt_editar_ct_resp_id" id="form_rcbt_editar_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
                        <input type="hidden" id="form_rcbt_editar_ct_resp_id" value="0"/>
          </span>
       </div>
                  
       
       <div class="formRow">
          <span class="span12">
            <label>Observação:</label>
             <textarea name="observacao" rows="2" cols="auto"></textarea> 
          </span>                                                                                                    
       </div>
        
                 
    </div>  <!-- fluid -->                 
  </form>            
</div><!-- Fim dialog --> 


<!-- ==============================================================================================================================
INCLUÍR PAGAMENTO -->
<div id="dialog-pgto-incluir" style="height:auto; padding:0;" title="Novo Pagamento" class="modal">

  <form id="form_pgto" class="dialog">
    <input type="hidden" name="funcao" value="pagamentosIncluir">
    <input type="hidden" name="tipo" value="P">
    <input type="hidden" name="ct_resp_lancamentos" id="form_pgto_ctr_plc_lnct" value="">
<div class="fluid">    

<!--=====================================-->
  
           <div class="formRow">
              <span class="span6">
                  <label>Descrição:</label>
                  <input type="text" name="descricao" value="" class="required"/>
              </span>
              <span class="span4">
                  <label>Valor:</label>
                   <input type="text" name="valor" id="form_pgto_valor" value="0,00" class="moeda"/>
              </span>
              <span class="span2">
                  <label>Vencimento:</label>
                    <input type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate"/>
              </span>
           </div>

           
       <div class="formRow">
    	 <span class="span6 input-autocomplete-container">
              <label>Categoria:</label>
                <input style="margin-left: 0px;" type="text" name="form_pgto_pl_conta_id" id="form_pgto_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar..." size=""/>
                <input type="hidden" id="form_pgto_pl_conta_id" value="0"/>
          </span>
          <span class="span6 input-autocomplete-container">
              <label>Centro de custo:</label>
              <input style="margin-left: 0px;" type="text" name="form_pgto_ct_resp_id" id="form_pgto_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar..." size=""/>
                        <input type="hidden" id="form_pgto_ct_resp_id" value="0"/>
          </span>
       </div>
                  
       
       <div class="formRow">
          <span class="span12">
            <label>Observação:</label>
             <textarea name="observacao" rows="2" cols="auto"></textarea> 
          </span>                                                                                                    
       </div>
        
                 
    </div>  <!-- fluid -->                 
  </form>            
</div><!-- Fim dialog --> 

<!-- ==============================================================================================================================
EDITAR PAGAMENTO -->
<div id="dialog-pgto-editar" style="height:auto; padding:0;" title="Editar Pagamento" class="modal">

  <form id="form_pgto_editar" class="dialog">

      <input type="hidden" name="funcao" value="pagamentosEditar">
      <input type="hidden" name="lancamento_id" value="">
      <input type="hidden" name="tipo" value="P" />
      <input type="hidden" name="ct_resp_lancamentos" id="form_pgto_editar_ctr_plc_lnct" value="">
<div class="fluid">    

<!--=====================================-->
  
           <div class="formRow">
              <span class="span6">
                  <label>Descrição:</label>
                  <input type="text" name="descricao" value="" class="required"/>
              </span>
              <span class="span4">
                  <label>Valor:</label>
                   <input type="text" name="valor" id="form_pgto_editar_valor" value="" class="required moeda"/>
              </span>
              <span class="span2">
                  <label>Vencimento:</label>
                    <input type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker maskDate"/>
              </span>
           </div>

           
       <div class="formRow">
    	 <span class="span6 input-autocomplete-container">
              <label>Categoria:</label>
               <input style="margin-left: 0px;" type="text" name="form_pgto_editar_pl_conta_id" id="form_pgto_editar_pl_conta_buscar" value="" class="plano_contas_buscar input-buscar" placeholder="Localizar..." size=""/>
               <input type="hidden" id="form_pgto_editar_pl_conta_id" value="0"/>
          </span>
          <span class="span6 input-autocomplete-container">
              <label>Centro de custo:</label>
             <input style="margin-left: 0px;" type="text" name="form_pgto_editar_ct_resp_id" id="form_pgto_editar_ct_resp_buscar" value="" class="centro_resp_buscar input-buscar" placeholder="Localizar..." size=""/>
              <input type="hidden" id="form_pgto_editar_ct_resp_id" value="0"/>
          </span>
       </div>
                  
       
       <div class="formRow">
          <span class="span12">
            <label>Observação:</label>
             <textarea name="observacao" rows="2" cols="auto"></textarea> 
          </span>                                                                                                    
       </div>
        
                 
    </div>  <!-- fluid -->                 
  </form>            
</div><!-- Fim dialog --> 