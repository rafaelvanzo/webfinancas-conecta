<div id="dialog-editar-plano" style="height:auto; padding:0;" title="Editar Plano">

	<?php			
  $db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas', 'W2BSISTEMAS', 'webfinancas');

  $cliente_id = $_SESSION['cliente_id'];					
  $dados_plano = mysqli_fetch_assoc(mysqli_query($db_wf,'select plano_id, periodo, dia_vencimento from clientes_planos where cliente_id = '.$cliente_id));
  ?>
   
  <form id="form_plano_editar">
   
    <input type="hidden"  name="funcao" value="planoEditar" />
    <input type="hidden"  name="plano_id" value="<?php echo $dados_plano['plano_id']; ?>" />
    <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $usuario_id; ?>" />
    <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $cliente_id; ?>" />
    <input type="hidden" id="modulos" name="modulos" value="" />

    
    <!-- TODOS OS VALOR DOS MÓDILOS MARCADOS -->   
    
  										<table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget">
                        <thead>
                            <tr>
                                <td width="50%">Tipo de Plano</td>
                                <td width="50%">Vencimento</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>                           
                                  <select class="select_ano" id="tp_plano" name="tp_plano" style="width: auto;" onchange="trocar_periodo();">                       
                                    <option value="1" <?php if($dados_plano['periodo'] == '1'){ echo 'selected="selected"'; } ?> >Mensal</option>													
                                    <option value="2" <?php if($dados_plano['periodo'] == '2'){ echo 'selected="selected"'; } ?> >Trimestral</option>
                                    <option value="3" <?php if($dados_plano['periodo'] == '3'){ echo 'selected="selected"'; } ?> >Semestral</option>													
                                    <option value="4" <?php if($dados_plano['periodo'] == '4'){ echo 'selected="selected"'; } ?> >Anual</option>
                                  </select>
                                  <input type="hidden" id="tp_plano_atual" name="tp_plano_atual" value="<?php echo $dados_plano['periodo']; ?>" />
                                </td>
                                <td>
                                	<?php $dia_vencimento = $dados_plano['dia_vencimento']; if($dia_vencimento < '10'){ $dia_vencimento = '0'.$dia_vencimento; }else{ $dia_vencimento; } ?>
																	<?php echo '<b>Dia '.$dia_vencimento.'</b>'; ?>
                                  <input type="hidden" name="diaVencimento" id="diaVencimento" value="<?php echo $dados_plano['dia_vencimento']; ?>" />
                                </td>
                            </tr>
                        </tbody>
                       <thead>
                            <tr style="border-top:1px solid #CCC;">
                                <td colspan="2">Produto(s)</td>
                            </tr>
                        </thead>
                    </table> 
              
              				<?php 
											$plano_id = $dados_plano['plano_id']; //
											$dados_plano_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome, vl_mensal,	vl_trimestral,	vl_semestral,	vl_anual from planos where id = '.$plano_id)); 
											?>
                    
							<div class="scroll"  id="produtos" style="height: 236px;">
              		<div class="updates">
                  
                  	<!-- Modulo -->
									<div class="newUpdate">
									                  
										<span class="lReceita" style="padding-top: 10px;  padding-left: 0; font-size: 12px; ">
                      <img src="images/zero.png" align="middle"> &nbsp;<?php	echo $dados_plano_nome['nome'];	?> &nbsp;&nbsp;
										</span>
									
										<div style="float:right; padding:10px 5px; font-size:12px;"> 
                          
													<span class="1" <?php if($dados_plano['periodo'] != '1'){ echo 'style="display:none;"'; }?> >
														 		<?php echo "R$ ".number_format($dados_plano_nome['vl_mensal'],2,',','.')." / mês"; ?>
                                <input type="hidden" id="plvalor1" value="<?php echo $dados_plano_nome['vl_mensal']; ?>" />
                          </span>
                          
                          <span class="2" <?php if($dados_plano['periodo'] != '2'){ echo 'style="display:none;"'; }?> >
                                <?php echo "R$ ".number_format($dados_plano_nome['vl_trimestral'],2,',','.')." / trimestre"; ?>
			                          <input type="hidden" id="plvalor2" value="<?php echo $dados_plano_nome['vl_trimestral']; ?>" />                                
                          </span>
                          
                          <span class="3" <?php if($dados_plano['periodo'] != '3'){ echo 'style="display:none;"'; }?> >
                                <?php echo "R$ ".number_format($dados_plano_nome['vl_semestral'],2,',','.')." / semestre"; ?>
                                <input type="hidden" id="plvalor3" value="<?php echo $dados_plano_nome['vl_semestral']; ?>" />
                          </span>
                          
                          <span class="4" <?php if($dados_plano['periodo'] != '4'){ echo 'style="display:none;"'; }?> >
                                <?php echo "R$ ".number_format($dados_plano_nome['vl_anual'],2,',','.')." / ano"; ?>
			                          <input type="hidden" id="plvalor4" value="<?php echo $dados_plano_nome['vl_anual']; ?>" />
                          </span>

                    </div>
										 
									</div>
									<!-- Fim modulo -->	
  								<?php 					
									
									/* ======== Quantidade de registros ========*/
									$m_plano_modulo = mysqli_query($db_wf,'select id from planos_modulos where situacao = 1');
									$num_plano_modulo = mysqli_num_rows($m_plano_modulo);

									$m_cliente_modulo = mysqli_query($db_wf,'select grupo_id from clientes_modulos where cliente_id = '.$cliente_id);
									$num_cliente_modulo = mysqli_num_rows($m_cliente_modulo);
									
									$totaModulos = $num_plano_modulo + $num_cliente_modulo;
									
									/* ======== Restrições de modulos antigos do cliente ========*/
									
									if($totaModulos > 0){
										
										$restricao = '';
										
										while($dados_cliente_modulo = mysqli_fetch_assoc($m_cliente_modulo)){
											
											$restricao .= ' and grupo_id != '.$dados_cliente_modulo['grupo_id'];
											
										} 
									/* ======== Modulos do cliente ========*/
									$m_cliente_modulo = mysqli_query($db_wf,'select modulo_id, ano, grupo_id, valor from clientes_modulos where cliente_id = '.$cliente_id);
									
											while($dados_cl_modulo = mysqli_fetch_assoc($m_cliente_modulo)){		
													$modulo_id = $dados_cl_modulo['modulo_id'];
													$dados_pl_modulo = mysqli_fetch_assoc(mysqli_query($db_wf,'select id, nome, descricao, vl_mensal,	vl_trimestral,	vl_semestral,	vl_anual, situacao from planos_modulos where id= '.$modulo_id));
													
											$num +=1;																	
                  		?>
                      <!-- Modulos -->
                      <?php $situacao = $dados_pl_modulo['situacao']; ?>
                      <div class="newUpdate tipS <?php if($dados_pl_modulo['situacao'] == 0){ echo 'plInativo'.$modulo_id; } ?>" original-title="<?php echo $dados_pl_modulo['descricao']; ?>">
           
                        <span class="lReceita" style="padding-top: 10px; padding-left: 0; font-size: 12px; ">
                          <input type="checkbox" name="<?php echo 'modulo'.$num; ?>" id="<?php echo $modulo_id; ?>"  value="<?php echo $modulo_id.'-m'.$num; ?>" checked="checked" onclick="trocar_periodo();"/>&nbsp; 
                          	<?php if($situacao == 1){ ?> <img src="images/subIcon3.png" align="middle"> <?php }else{ ?> <img src="images/subIcon2.png" align="middle"> <?php } ?>
															&nbsp; <?php echo $dados_pl_modulo['nome']; ?> &nbsp;&nbsp;
                        </span>
                      
                        <div style="float:right; padding:10px 5px; font-size:12px;">
                        
                          <span class="1" <?php if($dados_plano['periodo'] != '1'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_pl_modulo['vl_mensal'],2,',','.')." / mês"; ?>
                                <input type="hidden" id="<?php echo $modulo_id.'-m'.$num.'1'; ?>" value="<?php echo $dados_pl_modulo['vl_mensal']; ?>" />
                          </span>
                          
                          <span class="2" <?php if($dados_plano['periodo'] != '2'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_pl_modulo['vl_trimestral'],2,',','.')." / trimestre"; ?>
                                <input type="hidden" id="<?php echo $modulo_id.'-m'.$num.'2'; ?>" value="<?php echo $dados_pl_modulo['vl_trimestral']; ?>" />
                          </span>
                          
                          <span class="3" <?php if($dados_plano['periodo'] != '3'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_pl_modulo['vl_semestral'],2,',','.')." / semestre"; ?>
                                <input type="hidden" id="<?php echo $modulo_id.'-m'.$num.'3'; ?>" value="<?php echo $dados_pl_modulo['vl_semestral']; ?>" />
                          </span>
                          
                          <span class="4" <?php if($dados_plano['periodo'] != '4'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_pl_modulo['vl_anual'],2,',','.')." / ano"; ?>
                                <input type="hidden" id="<?php echo $modulo_id.'-m'.$num.'4'; ?>" value="<?php echo $dados_pl_modulo['vl_anual']; ?>" />
                          </span>
                            
                        </div>
                         
                      </div>
    
                      <!-- Fim modulos -->			
										<?php 
                    } 
                    
									
									/* ======================================*/
										$m_plano_modulo = mysqli_query($db_wf,'select id, nome, descricao, vl_mensal,	vl_trimestral,	vl_semestral,	vl_anual from planos_modulos where situacao = 1'.$restricao);
										while($dados_plano_modulo = mysqli_fetch_assoc($m_plano_modulo)){
											$id_modulo = $dados_plano_modulo['id'];
											//$cli_modulo = mysqli_fetch_assoc(mysqli_query($db_wf,'select id, modulo_id, valor from clientes_modulos where cliente_id = '.$cliente_id.' and modulo_id = '.$id_modulo));

											$num +=1;																	
                  		?>
                      <!-- Modulos -->
                      <div class="newUpdate tipS" original-title="<?php echo $dados_plano_modulo['descricao']; ?>">
           
                        <span class="lReceita" style="padding-top: 10px; padding-left: 0; font-size: 12px; ">
                          <input type="checkbox" name="<?php echo 'modulo'.$num; ?>" id="<?php echo $id_modulo; ?>"  value="<?php echo $id_modulo.'-m'.$num; ?>" onclick="trocar_periodo();"/>&nbsp;
                          <img src="images/subIcon3.png" align="middle">
                          &nbsp;<?php echo $dados_plano_modulo['nome']; ?> &nbsp;&nbsp;
                        </span>
                      
                        <div style="float:right; padding:10px 5px; font-size:12px;">
                        
                          <span class="1" <?php if($dados_plano['periodo'] != '1'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_plano_modulo['vl_mensal'],2,',','.')." / mês"; ?>
                                <input type="hidden" id="<?php echo $id_modulo.'-m'.$num.'1'; ?>" value="<?php echo $dados_plano_modulo['vl_mensal']; ?>" />
                          </span>
                          
                          <span class="2" <?php if($dados_plano['periodo'] != '2'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_plano_modulo['vl_trimestral'],2,',','.')." / trimestre"; ?>
                                <input type="hidden" id="<?php echo $id_modulo.'-m'.$num.'2'; ?>" value="<?php echo $dados_plano_modulo['vl_trimestral']; ?>" />
                          </span>
                          
                          <span class="3" <?php if($dados_plano['periodo'] != '3'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_plano_modulo['vl_semestral'],2,',','.')." / semestre"; ?>
                                <input type="hidden" id="<?php echo $id_modulo.'-m'.$num.'3'; ?>" value="<?php echo $dados_plano_modulo['vl_semestral']; ?>" />
                          </span>
                          
                          <span class="4" <?php if($dados_plano['periodo'] != '4'){ echo 'style="display:none;"'; }?> >
                                <?php echo "+ R$ ".number_format($dados_plano_modulo['vl_anual'],2,',','.')." / ano"; ?>
                                <input type="hidden" id="<?php echo $id_modulo.'-m'.$num.'4'; ?>" value="<?php echo $dados_plano_modulo['vl_anual']; ?>" />
                          </span>
                            
                        </div>
                         
                      </div>
    
                      <!-- Fim modulos -->			
										<?php 
                    } 
                    ?>
                    			<input type="hidden" id="numTotal" name="numTotal" value="<?php echo $num; ?>" />                    
								<?php 
                  } 
                ?>
              </div>
            </div>
          
          <?php 
						$vl_total_modulo = mysqli_fetch_assoc(mysqli_query($db_wf,'select sum(valor) as valor from clientes_modulos where cliente_id = '.$cliente_id)); 
					?>
          
			           <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget" style="border-top:1px solid #CCC; border-bottom:1px solid #CCC;">
                        <thead>
                        		 <tr height="20">
                            		<td colspan="2">                                	 
                                	<img src="images/zero.png" align="middle" class="tipS" original-title="Sistema contratado."> Azul &nbsp;&nbsp;
                                	<img src="images/subIcon3.png" align="middle" class="tipS" original-title="Valores e reajustes atuais."> Verde &nbsp;&nbsp;
                                	<img src="images/subIcon2.png" align="middle" class="tipS" original-title="Valores e reajustes antigos."> Cinza
																</td>                         
                            </tr>
                         </thead>   
                         <tbody>
                            <tr height="40">
                            		<td width="30%"><h6>Total</h6></td>
                                <td width="70%"><h6 class="valorTotal"></h6>
																</td>                         
                            </tr>
                        </tbody>
                    </table> 
                    
       
  	</form>

</div>