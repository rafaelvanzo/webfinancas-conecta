   						<!-- Caixa de Dialogo Conta a Receber -->
<div id="dialog-gerar-arquivo" style=" height:auto; padding:0; display:none;" title="Gerar arquivo contábil">
  
  <form id="form_visualizar_lancamentos" class="dialog">
  <input type="hidden" name="funcao" value="visualizarLancamentos" />
  <input type="hidden" name="cliente_id" value="<?php echo $id_registro; ?>" />
                             
            <!-- <div class="widget" style="border: 0; margin: 0;"> --> 
                     <div class="fluid">      
      
         <div class="formRow">
                                  <span class="span6">
                                  	  <label style="margin-right:80%">Período:</label>
                                      
                                      <input name="dt_ini" id="dt_ini" type="text" class="datepicker maskDate" placeholder="Data inicial" value="" style="float:left; margin:none; margin-top:0px"/>

                                      <input name="dt_fim" id="dt_fim" type="text" class="datepicker maskDate" placeholder="Data final" value="" width="50" style="float:left; margin:none; margin-left:5px; margin-top:0px;"/>
                                  </span>
                                  <span class="span6">
                                  
                                  	  <label>Tratamento contábil:</label>
                                      <select name="tratamentoContabil" id="tratamento-contabil">
                                      	<option value="">
                                         Selecione..
                                        </option>                                      	
                                        <option value="1">
                                         Baixa
                                        </option>
                                        <option value="2">
                                         Baixa e Financeiro
                                        </option>
                                      </select>
                                      
                                 </span>                                                                                                                         
                               </div>
       
        <div class="formRow">
          <span class="span12 controlB">
          
         <ul>
		 <?php         
              
        $contas = $db_cli->fetch_all_array('select id, banco_id, descricao from contas');
        $cfCont =0;			
        foreach($contas as $contas){
        $cfCont++;
                 $bancos = $db_cli->fetch_assoc('select nome, logo from bancos where id ='.$contas['banco_id']);	
        
                 if(empty($bancos['logo'])){$logo = "bank.png"; }else{ $logo = $bancos['logo'];}
				 
				 
				  if(strlen($contas['descricao']) > 22){
				 	$nome_conta = trim(substr($bancos['nome'], 0, 18))."...";
				 }else{
					$nome_conta = $contas['descricao'];
				 }
				 
				 if(strlen($bancos['nome']) > 22){
				 	$nome_banco = trim(substr($bancos['nome'], 0, 18))."...";
				 }else{
					$nome_banco = $bancos['nome'];
				 }
                                                     
                 //resgata os valores dentro do banco do contador
                 $contador_cf_cod = $db->fetch_assoc('select contador_cf_cod from clientes_cf where cliente_id ='.$id_registro.' and cliente_cf_id ='.$contas['id']);															
        
        echo '	
				<li style="min-width:250px; height:47px; font-weight:normal;">
					
					<span class="floatR"><input type="checkbox" name="banco'.$cfCont.'" value="'.$contas['id'].'" style="float:right;"> </span>
				
					<a href="javascript://"  >  
					  <span class="floatL">						
						<img src="'.$raiz.'images/bancos/'.$logo.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px; margin-left:-14px; margin-top:-10px;" >
					  </span>
					   <span class="floatL" style="text-align:left; padding-left:4px; margin-top:-4px;">
						<strong>'.$nome_conta.' </strong>
							<br>	'.$nome_banco.'
					   </span>		
					   
					</a>
				</li>';

        }
              
        ?>
        
        <input type="hidden" name="bancoTotal" value="<?php echo $cfCont; ?>" />
		</ul>

              </span>                                                                                                                         
           </div>
             
        </div>
           

  </div>  <!-- fluid -->         

  </form> 
             <!--   </div> widget -->     
</div><!-- Fim dialog --> 
