<!-- Arquivos -->
<div id="modal-arquivo" style="height:auto;padding:2px; " title="Exportar arquivo para IRPF / Carne Leão" class="modal">

    	<form id="form_remessa" class="dialog">
		
		<input type="hidden" name="cliente_id" class="cliente_id">
		<input type="hidden" name="profissional_CPF" class="profissional_CPF">
		
                <h6 style="text-align:left;margin-left:15px; margin-top:10px;">Dados do Profissional</h6>

                <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                <p style="font-size:10px;text-align:left;margin-left:15px; margin-bottom:10px; margin-top:-8px;">
				As informações exibidas do profissional são as do cadastro principal do cliente.
				</p>

               <div class="fluid">
                <div class="formRow">

                    <span class="span6" style="text-align:left;">                          
                                <b>Nome:</b> <br> <span class="Nome"></span>
                      </span> 
                    <span class="span3" style="text-align:left;">                          
                                <b>CPF:</b> <br> <span class="CPF"></span>
                      </span> 

					<span class="span3" style="text-align:left;">                          
								<b>Programa:</b> 
								<select name="AnoDeclaracao" class="AnoDeclaracao">
									
                                    <?php
                                    //Exportação para CARNE LEÃO
                                    $dt_carneLeao = date('Y');
                                    $dt_fim_carneLeao = 2022;
                                    
                                        $anoDecOpt = '';
                                        
                                        while($dt_carneLeao >= $dt_fim_carneLeao){                                           

                                            $anoDecOpt .= '<option value="'.$dt_carneLeao.'">Carne Leão '.$dt_carneLeao.'</option>';

                                            $dt_carneLeao -= 1;
                                        }
                                        

                                        $dt = 2021;//date('Y'); 
                                        $dt_fim = 2016;

                                        while($dt >= $dt_fim){                                           

                                            $anoDecOpt .= '<option value="'.$dt.'">IRPF '.$dt.'</option>';

                                            $dt -= 1;
                                        }

                                        

                                        echo $anoDecOpt;
                                    ?>
                                    
								</select>
                    </span> 
                   </div>
                      
					<div class="formRow">

						<a href="javascript://" title="" class="wContentButton bluewB" onClick="ExportarArquivo()"; style="color:#fff">Exportar movimento para IRPF</a>

                    </span> 
					
					<br>
                   
				   </div>
						 

						 <div class="Erro"></div>
				
                                                      
               </div> 
            </div>

                <br>
                <div class="linha"></div>   <!--Linha deve estar no ultimo formRow -->
        </form>




</div>
<!-- Fim arquivos -->