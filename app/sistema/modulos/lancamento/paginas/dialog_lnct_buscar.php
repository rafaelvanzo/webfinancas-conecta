<div id="dialog-lnct-buscar" style="height:auto; padding:0;" title="Lançamento Existente">
  
  <form id="form_lnct_buscar" class="dialog">
      <div class="toggle acc" style="margin:0;">

          <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >
                            
             <!--       <div class="widget" style="margin:0;">  -->
                       <div class="fluid">

                                <div class="formRow" align="left">
                                  <span class="span12">
                                  	<label>
                                  	Conciliar com:
                                    <br>
                                    <b><span id="lnct_buscar_dscr"></span> do dia <span id="lnct_buscar_dt_venc"></span> no valor de R$ <span id="lnct_buscar_valor"></span> </b>
                                    </label>
                                	</span>
                                </div>
                                
                                <div class="formRow">
                                    <span class="span12 input-autocomplete-container">
                                        <label><input type="radio" name="rad_lnct_exist" id="rad_lnct_buscar" value="1"> Buscar lançamento:</label>
                                        <input style="margin-left: 0px;" type="text" name="lnct_buscar_id" id="input_lnct_buscar" value="" class="required lancamentos_buscar input-buscar" placeholder="Preencha para localizar..." />
                                        <input type="hidden" name="lnct_buscar" value="" id="lnct_buscar_id"/>
                                    </span>
                                </div>
                                
                                <div class="formRow">
                                    <span class="span12">
                                    
                                      <label><input type="radio" name="rad_lnct_exist" id="rad_lnct_sugest" value="2"> Lançamentos sugeridos:</label> <br><br>
                                      
                                      <div class="boxScroll" style="min-height:40px;" id="boxLnctSugerido">
                                      </div>
                                        
                                      <br>
                                        
                                    </span>


                        </div>  <!-- fluid -->               
                <!--   </div> widget -->               
           </div>
         
      </div>
	</form> 
                   
</div>
