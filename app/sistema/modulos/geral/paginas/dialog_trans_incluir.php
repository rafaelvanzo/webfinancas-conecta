<div id="dialog-trans-incluir" style="height:auto; padding:0;" title="Incluír Nova Transferência">
  
  <form id="form_trans" class="dialog">
    <input type="hidden" name="funcao" value="transferenciasIncluir">
    <input type="hidden" name="tipo" value="T">
    <input type="hidden" name="compensado" value="1">
      <div class="toggle acc" style="margin:0;">      
        <div class="title" style="color: rgb(43, 104, 147);"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"><h6>Dados do Lançamento</h6></div>
          <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
                            
             <!--       <div class="widget" style="margin:0;">  -->
                       <div class="fluid">      
        
                               <div class="formRow">
                                  <span class="span12">
                                      <label>Descrição:</label>
                                      <input style="margin-left: 0px;" type="text" name="descricao" value="" class="required"/>
                                  </span>
                                </div>

                                <div class="formRow">
                                    <span class="span6">
                                        <label>Conta de origem:</label>
                                        <input style="margin-left: 0px;" type="text" name="conta_trans_id_origem" value="" class="required conta_buscar" placeholder="Preencha para localizar..." />
                                        <input type="hidden" name="conta_id_origem" value="" id="conta_trans_id_origem"/>
                                    </span>
                                    <span class="span6">
                                        <label>Conta de destino:</label>
                                        <input style="margin-left: 0px;" type="text" name="conta_trans_id_destino" value="" class="required conta_buscar" placeholder="Preencha para localizar..." />
                                        <input type="hidden" name="conta_id_destino" value="" id="conta_trans_id_destino"/>
                                    </span>
                                 </div>
                                <div class="formRow">
                                    <span class="span2">
                                        <label>Emissão:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_emissao" value="<?php echo date('d/m/Y')?>" class="required datepicker"/>
                                    </span>
                                    <span class="span2">
                                        <label>Vencimento:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_vencimento" value="<?php echo date('d/m/Y')?>" class="required datepicker"/>
                                    </span>
                                    <span class="span2">
                                        <label>Compensaçao:</label>
                                        <input style="margin-left: 0px;" type="text" name="dt_compensacao" value="<?php echo date('d/m/Y')?>" class="required datepicker"/>
                                    </span>
                                    <span class="span6">
                                        <label>Valor:</label>
                                        <input style="margin-left: 0px;" type="text" name="valor" value="" class="required moeda"/>
                                    </span>
                                 </div>

                        </div>  <!-- fluid -->               
                <!--   </div> widget -->               
           </div>
       
         <!--
         <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/frames.png" alt="" class="titleIcon"><h6>Plano de Contas</h6></div>
         <div class="menu_body" style="color: rgb(64, 64, 64); display: none;">
           Plano de contas
         </div>
        
        <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/files.png" alt="" class="titleIcon"><h6>Centro de Responsabilidade</h6></div>
        <div class="menu_body" style="display: none; color: rgb(64, 64, 64);">
          Centro de custo
        </div>                      
        -->
        <div class="title" style="color: rgb(64, 64, 64);"><img src="images/icons/dark/clipboard.png" alt="" class="titleIcon"><h6>Observação</h6></div>
        <div class="menu_body" style="color: rgb(64, 64, 64); display: none;">
          <textarea name="observacao" rows="15" cols=""></textarea>
        </div>
         
      </div>
	</form> 
                   
</div>
