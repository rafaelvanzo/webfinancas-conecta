<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Perfil do Usuário</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs -->
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="javascript://" style="cursor: default;">Minha Conta</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Perfil do Usuário</a>
                 </li>
            </ul>
	</div>  
</div> <!-- Fim Breadcrumbs -->


    
 <!--   <div class="line"></div>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
         <!-- Notifications 
        <div class="nNote nWarning hideit" style="display:none;">
            <p></p>
        </div>
        <div class="nNote nInformation hideit" style="display:none;">
            <p></p>
        </div>   
        <div class="nNote nSuccess hideit" style="display:none;">
            <p></p>
        </div>  
        <div class="nNote nFailure hideit" style="display:none;">
            <p></p>
        </div>-->
    		
        <!-- =================== Palco =================== -->
 <!-- Organiza o layout -->   
 <div class="fluid">
 		<div class="span7">

		 <?php 
			$db_w2b = new mysqli('mysql.web2business.com.br', 'web2business', 'W2BSISTEMAS', 'web2business');
			$cliente_id = $_SESSION['cliente_id'];
			$dados_perfil_usuario = mysqli_fetch_assoc(mysqli_query($db_w2b,'select inscricao, cpf_cnpj, nome, email, logradouro, numero, complemento, bairro, cidade, uf, cep, telefone from clientes where id = '.$cliente_id));			
			$db_w2b->close(); 
			?>  
			<form id="form_usuario_editar" class="form">
      <input type="hidden" name="funcao" value="usuariosEditar">
      <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>" />
      
                  
                <div class="widget">
                    <div class="title"><img src="images/icons/dark/adminUser.png" alt="" class="titleIcon"><h6>Dados do Usuário</h6></div>                   
                    	<div class="fluid">
                    
                         <div class="formRowB">
                            <span class="span6">
                                <label>Nome:</label>
                                <input style="margin-left: 0px;" type="text" name="nome" value="<?php echo $dados_perfil_usuario['nome']; ?>" class="required"/>
                            </span>
                            <span class="span2">
                                <label>Inscrição:</label>
                                 <select name="inscricao" class="inscricao sFormRowB" id="inscIncluir" onChange="cpfCnpj('inscIncluir');">
                                  <option value="CPF" <?php if($dados_perfil_usuario['inscricao'] == "CPF"){ echo 'selected="selected"'; } ?> >CPF</option>
                                  <option value="CNPJ" <?php if($dados_perfil_usuario['inscricao'] == "CNPJ"){ echo 'selected="selected"'; } ?> >CNPJ</option>
                                </select>
                            </span>
                            <span class="span4">
                                <label>CPF / CNPJ</label> 																																				                      
                                <input type="text" name="cpf_cnpj" value="<?php echo $dados_perfil_usuario['cpf_cnpj']; ?>" class="cpf_cnpj <?php if($dados_perfil_usuario['inscricao'] == "CNPJ"){ echo 'maskCnpj'; }else{ echo 'maskCpf'; } ?> required"/>
                            </span>

                         </div>

                         <div class="formRowB"> 
                            <span class="span6">
                                <label>Logradouro:</label>
                                <input type="text" name="logradouro" value="<?php echo $dados_perfil_usuario['logradouro']; ?>" class="required"/>
                            </span>
                            <span class="span2">
                                <label>Nº:</label>
                                <input type="text" name="numero" value="<?php echo $dados_perfil_usuario['numero']; ?>" class="required"/>
                            </span>
                            <span class="span4">
                             <label>Complemento:</label>
                                <input type="text" name="complemento" value="<?php echo $dados_perfil_usuario['complemento']; ?>" />
                            </span>

                        </div>
                        
                          <div class="formRowB"> 
                            <span class="span5">
                                <label>Bairro:</label>
                                <input type="text" name="bairro" value="<?php echo $dados_perfil_usuario['bairro']; ?>" class="required"/>
                            </span>
                            <span class="span5">
                                <label>Cidade:</label>
                                <input type="text" name="cidade" value="<?php echo $dados_perfil_usuario['cidade']; ?>" class="required"/>
                            </span>
                            <span class="span2"> 
                               <label>UF:</label>
                                <select name="uf" class="sFormRowB required">
																<?php 																
																	$m_uf = mysql_query("select uf from uf");
																	while($uf = mysql_fetch_assoc($m_uf)){
																		if($dados_perfil_usuario['uf'] == $uf['uf']){ $select = 'selected="selected"'; }else{$select="";}
																		echo "<option value=".$uf[uf]." ".$select." >".$uf[uf]."</option>";
																	}
                                ?>
                                </select>
                            </span>
                            

                        </div>
                         
                         <div class="formRowB">
                         		<span class="span4">
                                <label>CEP:</label>
                                <input type="text" name="cep" value="<?php echo $dados_perfil_usuario['cep']; ?>" class="maskCep required"/>
                            </span>
                           <span class="span4">
                                <label>E-mail:</label>
                                <input type="text" name="email" value="<?php echo $dados_perfil_usuario['email']; ?>" class="email required"/>
                            </span>
                            <span class="span4">
                                <label>Telefone:</label>
                                <input type="text" name="telefone" value="<?php echo $dados_perfil_usuario['telefone']; ?>" class="maskPhone required"/>
                            </span> 
                          </div>  
                                                                         
                         <div class="formRow" align="center">
                            <span class="span12">                                      	    
                                <a href="javascript://" title="" class="button greenB" style="margin: 5px;" onClick="usuario_editar();" ><span>Salvar</span></a>                          
                            </span>                                                                                                    
                         </div>
  
                    </div> <!-- Fluid -->
                </div> <!-- Widget -->
     
        </form>

  		</div>   
   		<div class="span5">

		<?php 
		
		$db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas', 'W2BSISTEMAS', 'webfinancas');
		
		$cliente_trial = mysqli_fetch_assoc(mysqli_query($db_wf,'select dt_cadastro from clientes_trial where cliente_id =  13'));
		if(!empty($cliente_trial)){ ?>
        
             <div class="widget" id="planoContratado">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Plano Contratado</h6></div>
									<div id="produtos" style="height: 374px;  background-color:#F5F5F5;" align="center">              			
                  	<!-- Contagem Regressiva --> 
                   <br><br>
                    <h5>Período de degustação</h5>
                   <br>
                    O seu período de degustação acaba em
                  	<table style="height: 41px; width:236px; margin-left: auto; margin-right: auto; ">
                    	<thead>
                      	<tr>
                        	<td width="25%" align="center"><b>Dias</b></td>
                          <td width="25%" align="center"><b>Horas</b></td>
                          <td width="25%" align="center"><b>Min.</b></td>
                          <td width="25%" align="center"><b>Seg.</b></td>
                        </tr>
                      </thead>
      								<tbody>
                      	<tr>
                        	<td colspan="4"><div class="digits"></div></td>
                        </tr>
                      </tbody>    
                    </table>    
                    <br><br>
                    <a href="javascript://" title="" class="button blueB" style="margin:5px; color:#FFF;"  id="opener-editar-plano"><span>Contratar</span></a>
                           	 			
                  </div>                    
        		</div> <!-- Widget -->

		<?php }else{ ?>
    	
          <fieldset>
          
          <?php
			
					$cliente_id = $_SESSION['cliente_id'];					
					$dados_plano = mysqli_fetch_assoc(mysqli_query($db_wf,'select plano_id, vl_plano, periodo, dia_vencimento from clientes_planos where cliente_id = '.$cliente_id));
					?>

                   <div class="widget" id="planoContratado">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Plano Contratado</h6></div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget">
                        <thead>
                        <!--    <tr>
                                <td width="50%">Tipo de Plano</td>
                                <td width="50%">Vencimento</td>
                            </tr>-->
                        </thead> 
                        <tbody>
                            <tr>
                                <td width="50%">
                                Tipo de Plano
                                <h5 id="tpPlano">
                                	<?php
                                  	if($dados_plano['periodo'] == '1'){ 
																			echo "Mensal"; 
																		}elseif($dados_plano['periodo'] == '2'){ 
																			echo "Trimestral"; 
																		}elseif($dados_plano['periodo'] == '3'){ 
																			echo "Semestral"; 
																		}elseif($dados_plano['periodo'] == '4'){ 
																			echo "Anual";  
																		}
																	?>
                                 </h5> 
                                </td>
                                <td width="50%">
                                	 Vencimento
                                   
                                   <h5>Todo o dia <span id="diaVencimento"> <?php $dia_vencimento = $dados_plano['dia_vencimento']; if($dia_vencimento < '10'){ echo '0'.$dia_vencimento; }else{ echo $dia_vencimento; } ?> </span> </h5>
                                </td>
                            </tr>
                        </tbody>
                       <thead>
                            <tr style="border-top:1px solid #CCC;">
                                <td colspan="2">Produto(s)</td>
                            </tr>
                        </thead>
                    </table> 
                    
							<div id="produtos" class="scroll" style="height: 234px;">
              		<div class="updates">
                  
                  	<!-- Modulo -->
									<div class="newUpdate">
									                  
										<span class="lReceita" style="padding-top: 10px;  padding-left: 0; font-size: 12px; ">
											<?php 
											$plano_id = $dados_plano['plano_id'];
											$dados_plano_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome from planos where id = '.$plano_id)); 
											?>
											<img src="images/zero.png" align="middle" class="tipS" original-title="Sistema contratado."> &nbsp; <?php echo $dados_plano_nome['nome']; ?> 
											 
										</span>
									
										<div style="float:right; padding:10px 5px; font-size:12px;"> 
                          <?php
														$valor_plano = number_format($dados_plano['vl_plano'],2,',','.');
                            if($dados_plano['periodo'] == '1'){ 
                              echo "R$ ".$valor_plano." / mês"; 
                            }elseif($dados_plano['periodo'] == '2'){ 
                              echo "R$ ".$valor_plano." / trimestre"; 
                            }elseif($dados_plano['periodo'] == '3'){ 
                              echo "R$ ".$valor_plano." / semestre"; 
                            }elseif($dados_plano['periodo'] == '4'){ 
                              echo "R$ ".$valor_plano." / ano";  
                            }
                          ?> 
                    </div>
										 
									</div>
									<!-- Fim modulo -->	
  								<?php 					
										$m_plano_modulo = mysqli_query($db_wf,'select modulo_id, valor from clientes_modulos where cliente_id = '.$cliente_id);		
									
									if($num_plano_modulo = mysqli_num_rows($m_plano_modulo) > 0){
										
										while($dados_plano_modulo = mysqli_fetch_assoc($m_plano_modulo)){	
											$modulo_id = $dados_plano_modulo['modulo_id'];
											$dados_plano_modulo_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome, situacao from planos_modulos where id = '.$modulo_id)); 
                  ?>                    
                  	<!-- Modulo -->
									<div class="newUpdate">
       
                  <span class="lReceita" style="padding-top: 10px; padding-left: 0; font-size: 12px; ">
                    <?php if($dados_plano_modulo_nome['situacao'] == 1){ ?> <img src="images/subIcon3.png" align="middle" class="tipS" original-title="O módulo que seguem valores e reajustes atuais."> <?php }else{ ?> <img src="images/subIcon2.png" align="middle" class="tipS" original-title="O módulo que seguem valores e reajustes antigos."> <?php } ?> &nbsp; <?php echo $dados_plano_modulo_nome['nome']; ?>
                  </span>
									
										<div style="float:right; padding:10px 5px; font-size:12px;">
                    		<?php
														$valor_modulos = number_format($dados_plano_modulo['valor'],2,',','.');
                            if($dados_plano['periodo'] == '1'){ 
                              echo "+ R$ ".$valor_modulos." / mês"; 
                            }elseif($dados_plano['periodo'] == '2'){ 
                              echo "+ R$ ".$valor_modulos." / trimestre"; 
                            }elseif($dados_plano['periodo'] == '3'){ 
                              echo "+ R$ ".$valor_modulos." / semestre"; 
                            }elseif($dados_plano['periodo'] == '4'){ 
                              echo "+ R$ ".$valor_modulos." / ano";  
                            }
                          ?>  
                     </div>
										 
									</div>
									<!-- Fim modulo -->			
                 <?php 
								 		} 
									}
								 ?>
              </div>
              
            </div>
          
          <?php 
						$vl_total_modulo = mysqli_fetch_assoc(mysqli_query($db_wf,'select sum(valor) as valor from clientes_modulos where cliente_id = '.$cliente_id)); 
					?>
          
			           <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget" style="border-top:1px solid #CCC;">
                        <tbody>
                            <tr height="40">
                            		<td width="50%"><a href="javascript://" title="" class="button greenB" style="margin:5px; color:#FFF;"  id="opener-editar-plano"><span>Editar</span></a></td>
                                <td width="50%">Valor Total
                                	<h5 id="vlTotal">
                                  <?php
																	$valor_total = number_format($vl_total_modulo['valor'] + $dados_plano['vl_plano'],2,',','.');
																	
																		if($dados_plano['periodo'] == '1'){ 
																			echo "R$ ".$valor_total." / mês"; 
																		}elseif($dados_plano['periodo'] == '2'){ 
																			echo "R$ ".$valor_total." / trimestre"; 
																		}elseif($dados_plano['periodo'] == '3'){ 
																			echo "R$ ".$valor_total." / semestre"; 
																		}elseif($dados_plano['periodo'] == '4'){ 
																			echo "R$ ".$valor_total." / ano";  
																		}
																	?>                                    
                                  </h5>
																</td>                         
                            </tr>
                        </tbody>
                    </table> 
                    
        </div> <!-- Widget -->
        
            </fieldset>       
            
	  <?php } ?>      
		
  </div> 
  
    
</div> <!-- Fim Fluid --> 
 <!-- Organiza o layout -->   
 <div class="fluid">

  <div class="span12">

			<form action="" class="form">
          <fieldset>

                   <div class="widget">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Faturas</h6></div>
                   <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget">
                        <thead>
                            <tr>
                                <td width="auto">Fatura</td>
                                <td width="100">Situação</td>
                                <td width="115">Valor</td>
                                <td width="60">Vencimento</td>
                                <td width="80">Boleto</td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table> 
                    
							<div class="scroll" style="height: 176.5px; padding-top: 1px;">
              		<div class="updates">
                  
                  	<!-- Modulo -->
									<div class="newUpdate">
									
                    <div class="uDate" align="center"><span class="uDay">Mai</span>2014</div>
                  
                    <span class="lReceita blue" style="padding-top: 10px; font-size: 14px;">
											Plano Mensal + 1 módulo
										</span>										
										
                    <a href="#" title="" class="button greenB" style="float: right; margin: 5px 0 5px 40px; color:#FFF;" ><span>Boleto</span></a>
										
										<div class="uDate" align="center" style="float:right;"><span class="uDay">10</span>Mai/14</div>	
                    <div style="float:right; padding:10px 30px 10px 20px;  font-size:16px; width: 115px;" align="center" class="blue"> R$ 569,80 / mês</div>
                    
                    <div style="float:right; padding-top:10px; font-size:14px; width:100px;" align="center" class="blue" > Aguardando </div>
										 
									</div>
									<!-- Fim modulo -->			
                  
                  <!-- Modulo -->
									<div class="newUpdate">
									
                    <div class="uDate" align="center"><span class="uDay">Abr</span>2014</div>
                  
                    <span class="lReceita red" style="padding-top: 10px; font-size: 14px;">
											Plano Mensal + 1 módulo
										</span>										
										
                    <a href="javascript://" title="" class="button greenB" style="float: right; margin: 5px 0 5px 40px; color:#FFF;" ><span>Boleto</span></a>
										
										<div class="uDate" align="center" style="float:right;"><span class="uDay">10</span>Mai/14</div>	
                    <div style="float:right; padding:10px 30px 10px 20px;  font-size:16px; width: 115px;" align="center" class="red"> R$ 69,80 / mês</div>
                    
                    <div style="float:right; padding-top:10px; font-size:14px; width:100px;" align="center" class="red"> Atrasado </div>
										 
									</div>
									<!-- Fim modulo -->		
                  
                  <!-- Modulo -->
									<div class="newUpdate">
									
                    <div class="uDate" align="center"><span class="uDay">Mar</span>2014</div>
                  
                    <span class="lReceita green" style="padding-top: 10px; font-size: 14px;">
											Plano Mensal + 1 módulo
										</span>										
										
                    <a href="#" title="" class="button greenB" style="float: right; margin: 5px 0 5px 40px; color:#FFF; cursor: not-allowed;" ><span>Boleto</span></a>
										
										<div class="uDate" align="center" style="float:right;"><span class="uDay">10</span>Mai/14</div>	
                    <div style="float:right; padding:10px 30px 10px 20px;  font-size:16px; width: 115px;" align="center" class="green"> R$ 69,80 / mês</div>
                    
                    <div style="float:right; padding-top:10px; font-size:14px; width:100px;" align="center" class="green"> Pago </div>
										 
									</div>
									<!-- Fim modulo -->	
								
              </div>
            </div>
                    
        </div> <!-- Widget -->
            
            </fieldset>       
        </form>

  </div> 

</div> <!-- Fim Fluid --> 

 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
    <?php include("dialog_editar_plano.php"); ?>
    
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 