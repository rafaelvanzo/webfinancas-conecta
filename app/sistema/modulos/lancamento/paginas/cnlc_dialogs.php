<!--------------------------------------------------------------------------------------------------------
IMPORTAR EXTRATO BANCÁRIO E ARQUIVO DE RETORNO
-->

<div id="dialog-extrato-importar" style="height:auto;padding:0;display:none" title="Importar Extrato Bancário">
  <div class="fluid">
    <div class="formRow">
        <span class="span12">
          <a href="modulos/lancamento/Planilha_Modelo_Lancamentos_Conciliacao.xls">Planilha Modelo</a>
        </span>
    </div>
    <div class="formRow">
      <span class="span12">
      
        <div id="lnct_container" >
	        <a id="lnct_uploadfiles" href="javascript:;" style="display:none;"></a>      
        </div>
        
        <div id="lnct_filelist" class="controlB scroll bgUpload tipN" original-title="Clique em anexar ou arraste os arquivos até aqui." align="left">	
        </div>
        
        <a id="lnct_pickfiles" href="javascript:;" >
          <div class="vertical-text" >Anexar &nbsp;<img src="images/icons/dark/paperclip.png" class="clips" align="middle"/>
          </div>
        </a>
			
      </span>
    </div>
  </div>
</div>

<!--------------------------------------------------------------------------------------------------------
BUSCAR LANÇAMENTOS PARA EXTRATO BANCÁRIO
-->

<div id="dialog-lnct-buscar" style="height:auto; padding:0;display:none" title="Lançamento Existente">
  
  <form id="form_lnct_buscar" class="dialog">
  
      <div class="toggle acc" style="margin:0;">  

          <div class="menu_body" style="display: block; color: rgb(64, 64, 64); padding:0;" >

           <div class="fluid">  

            <div class="formRow" align="left">
              <span class="span12">
                <label>
                Conciliar com:
                <br>
                <b><span id="dscr_cnlc"></span> compensado no dia <span id="dt_vencimento_cnlc"></span> no valor de R$ <span id="vl_cnlc"></span> </b>
                </label>
              </span>
            </div>
            
            <div class="formRow"> 
            
                <span class="span12 input-autocomplete-container">
                    <label><!--<input type="radio" name="rad_lnct_exist" id="rad_lnct_buscar" value="1">--> Busca por descrição:</label>
                    <input style="margin-left: 0px;" type="text" name="lnct_buscar_id" id="input_lnct_buscar" data-box_id="boxLnctSugerido" value="" class="required lancamentos_buscar input-buscar" placeholder="Preencha para localizar..." />
                    <input type="hidden" name="lnct_buscar" value="" id="lnct_buscar_id"/>
                </span>
            
            </div>
            
            <div class="formRow">

              	<span class="span9">
	                <label>Busca por período:</label>
                  <div class="boxScroll" style="min-height:100px;min-width:400px;" id="boxLnctSugerido">
                  </div>
              	</span>

                <span class="span3" style="padding-top:18px;">
                  <input name="dt_ini" id="dt_ini" type="text" class="datepicker maskDate dtFltP" placeholder="Data inicial" value="" />
                  <input name="dt_fim" id="dt_fim" type="text" class="datepicker maskDate dtFltP" placeholder="Data final" value="" />
                  <span style="position:absolute;right:55px">
                    <a href="javascript://void(0);" original-title="Buscar" class="smallButton btTBwf tipS" onClick="lnctExistBuscar();"><img src="images/icons/dark/magnify.png" width="10"></a>
                    <a href="javascript://void(0);" original-title="Selecionar marcados" class="smallButton btTBwf tipS" onClick="lnctExistAdd('busca','','boxLnctSugerido');"><img src="images/icons/dark/add.png" width="10"></a>
                  </span>
                </span>

            </div>

            <div class="formRow">
            
              <span class="span9">
                <label>Lançamentos selecionados:</label>
                <br><br>
                <div class="boxScroll" style="min-height:130px;" id="boxLnctSelected">
                  <ul class="partners" id="ul_lnct_selected">
                  </ul>
                </div>
                <br>
              </span>
              
              <span class="span3" style="padding-top:40px">
              
                <div class="boxScroll" style="padding:5px" align="left">

                  <ul>
                  	<li><b>Nº de Lançamentos:</b></li>
                  	<li style="padding-left:5px" id="qtd_lnct_selected">0</li>
                  	<li><b>Total Selecionado: </b></li>
                  	<li style="padding-left:5px">R$ <span id="vl_total_selected">0,00</span></li>
                  	<li><b>Diferença: </b></li>
                  	<li style="padding-left:5px">R$ <span id="vl_dif">0,00</span></li>
                  </ul>
                  
                </div>

              </span>
            
            </div>
                         
          </div>
          
      </div> 
      
    </div>      
      
	</form> 
  
</div>
