<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Empresa</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php //include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs 
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

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>
    
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
			$dados_perfil_usuario = mysqli_fetch_assoc(mysqli_query($db_w2b,'select inscricao, cpf_cnpj, nome, email, email_fin, logradouro, numero, complemento, bairro, cidade, uf, cep, telefone from clientes where id = '.$cliente_id));			
			$db_w2b->close(); 
			?>  
			<form id="form_usuario_editar" class="form">
      <input type="hidden" name="funcao" value="usuariosEditar">
      <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>" />
      
                  
                <div class="widget">
                    <div class="title"><img src="images/icons/dark/adminUser.png" alt="" class="titleIcon"><h6>Dados do Usuário</h6></div>                   
                    	<div class="fluid">
                          <div class="formRowB"> 
                            <span class="span8">                            
                                <label style="float:none">Logo recibo:</label>
                                <div id="container">
                                    
                                    <img id="pickfiles" class="img" src="<?php echo $_SESSION['logo_recibo']; ?>" align="center"> 
                                </div>
                            </span>
                           <!-- <span class="span2">
                                <label style="float:none">Contador:</label>
                                <input type="checkbox" name="contador" value="1" class="ckb-contador" <?php //if($_SESSION['contador_acesso']==1) echo 'checked';?>/>
                            </span> 
                            <span class="span2">
                                <label style="float:none">Carnê Leão:</label>
                                <input type="checkbox" name="carne_leao" value="1" class="ckb-carne-leao" <?php if($_SESSION['carne_leao']==1) echo 'checked';?>/>
                            </span>-->
                          </div>
                    
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
                                <label>CPF / CNPJ:</label> 																																				                      
                                <input type="text" name="cpf_cnpj" value="<?php echo $dados_perfil_usuario['cpf_cnpj']; ?>" class="cpf_cnpj <?php if($dados_perfil_usuario['inscricao'] == "CNPJ"){ echo 'maskCnpj'; }else{ echo 'maskCpf'; } ?> required cpfCnpjValid"/>
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
                                <option value=""></option>
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
                            <span class="span2">
                                <label>CEP:</label>
                                <input type="text" name="cep" value="<?php echo $dados_perfil_usuario['cep']; ?>" class="maskCep required"/>
                            </span>
                            <span class="span2">
                                <label>Telefone:</label>
                                <input type="text" name="telefone" value="<?php echo $dados_perfil_usuario['telefone']; ?>" class="maskPhone required"/>
                            </span>
                             <span class="span4">
                                <label>E-mail:</label>
                                <input type="text" name="email" value="<?php echo $dados_perfil_usuario['email']; ?>" class="email required"/>
                            </span>
                             <span class="span4">
                                <label>E-mail Fatura:</label>
                                <input type="text" name="email_fin" value="<?php echo $dados_perfil_usuario['email_fin']; ?>" class="email required"/>
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
		$cliente_trial = mysqli_fetch_assoc(mysqli_query($db_wf,'select dt_cadastro from clientes_trial where cliente_id = '.$cliente_id));
		if(!empty($cliente_trial)){  ?>
        
             <div class="widget" id="planoContratado">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Plano Contratado</h6></div>
									<div id="produtos" style="height: 374px;  background-color:#F5F5F5;" align="center">              			
                          <!-- Contagem Regressiva --> 
                         <br><br><br>
                          <h5>Período de degustação</h5>
                         <br>
                          O seu período de degustação acabará em:
                          <table style="height: 41px; width:238px; margin-left: auto; margin-right: auto; ">
                            <thead>
                              <tr>
                                <td width="25%" align="center"><b>Dias</b></td>
                                <td width="25%" align="center"><b>Horas</b></td>
                                <td width="25%" align="center"><b>Min.</b></td>
                                <td width="25%" align="center"><b>Seg.</b></td>
                              </tr>
                            </thead>
                            <tbody valign="middle">
                              <tr>
                                <td colspan="4"><div class="digits"></div></td>
                              </tr>
                            </tbody>    
                          </table>    
                          <br><br>
                          <a href="contratar" title="" class="button blueB" style="margin:5px; color:#FFF;"><span>Contratar</span></a>
                        </div>                    
        		</div> <!-- Widget -->

		<?php }else{ ?>
    	
          <fieldset>
          
          <?php
			
					$cliente_id = $_SESSION['cliente_id'];					
					$dados_plano = mysqli_fetch_assoc(mysqli_query($db_wf,'select plano_id, vl_plano, periodo, dia_vencimento from clientes_planos where cliente_id = '.$cliente_id));
					?>

                   <div class="widget" id="planoContratado" style="height:410px;">
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
                                   
                                   <h5>Dia <span id="diaVencimento"> <?php $dia_vencimento = $dados_plano['dia_vencimento']; if($dia_vencimento < '10'){ echo '0'.$dia_vencimento; }else{ echo $dia_vencimento; } ?> </span> </h5>
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


<?php
if(empty($cliente_trial)){
?>
 <!-- Organiza o layout -->
 <div class="fluid">

  <div class="span12">

			<form action="" class="form">
          <fieldset>

                   <div class="widget">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Faturas em Aberto</h6></div>
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
                    
							<div style="height:58px; padding-top: 1px;">
              		<div class="updates">
                  <?php
									$db_w2b = new mysqli('mysql.web2business.com.br', 'web2business', 'W2BSISTEMAS', 'web2business');
									$db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas02', 'W2BSISTEMAS', 'webfinancas02');
									$cliente_id = $_SESSION['cliente_id'];
									$faturas = mysqli_query($db_w2b,'select valor from faturas where cliente_id = '.$cliente_id.' and contratacao = 0 and compensado = 0');
									if(mysqli_num_rows($faturas)>0){
										$valor = 0;
										while($fatura = mysqli_fetch_assoc($faturas)){
											$valor += $fatura["valor"];
										}
										$valor = number_format($valor,2,',','.');
										$fatura = mysqli_fetch_assoc(mysqli_query($db_w2b,'select sequencial, lancamento_id, dt_referencia, dt_vencimento, compensado from faturas where cliente_id = '.$cliente_id.' and contratacao = 0 and compensado = 0 and sistema_id = 1 order by id desc limit 0,1'));
										
										$dt_referencia = explode("-",$fatura["dt_referencia"]);
										$m_ref = $dt_referencia[1];
										$ano_ref = $dt_referencia[0];	
										if($m_ref == 01){ $mes_ref = 'Jan';}
										elseif($m_ref == 02){ $mes_ref = 'Fev';}
										elseif($m_ref == 03){ $mes_ref = 'Mar';}
										elseif($m_ref == 04){ $mes_ref = 'Abr';}
										elseif($m_ref == 05){ $mes_ref = 'Mai';}
										elseif($m_ref == 06){ $mes_ref = 'Jun';}
										elseif($m_ref == 07){ $mes_ref = 'Jul';}
										elseif($m_ref == 08){ $mes_ref = 'Ago';}
										elseif($m_ref == 09){ $mes_ref = 'Set';}
										elseif($m_ref == 10){ $mes_ref = 'Out';}
										elseif($m_ref == 11){ $mes_ref = 'Nov';}
										else{ $mes_ref = 'Dez';}
											
										$dt_vencimento = explode("-",$fatura["dt_vencimento"]);
										$dia = $dt_vencimento[2];
										$m = $dt_vencimento[1];
										$ano = $dt_vencimento[0];
										if($m == 01){ $mes = 'Jan';}
										elseif($m == 02){ $mes = 'Fev';}
										elseif($m == 03){ $mes = 'Mar';}
										elseif($m == 04){ $mes = 'Abr';}
										elseif($m == 05){ $mes = 'Mai';}
										elseif($m == 06){ $mes = 'Jun';}
										elseif($m == 07){ $mes = 'Jul';}
										elseif($m == 08){ $mes = 'Ago';}
										elseif($m == 09){ $mes = 'Set';}
										elseif($m == 10){ $mes = 'Out';}
										elseif($m == 11){ $mes = 'Nov';}
										else{ $mes = 'Dez';}
										$boleto_id = mysqli_fetch_assoc(mysqli_query($db_wf,"select id from boletos where lancamento_id = ".$fatura["lancamento_id"]));
										$chave = '1-591013-'.$fatura["lancamento_id"].'-'.$boleto_id["id"].'-'.$fatura["sequencial"];
										$boleto_link = "https://www.webfinancas.com/sistema/modulos/boleto/boletos_imprimir.php?k=".$chave."&vbe";
										
										if($fatura['compensado'] == '0'){   
													if($fatura['dt_vencimento'] < date('Y-m-d')){ $situacao = 'Atrasado'; $class = 'red'; }else{ $situacao = 'Aguardando'; $class = 'grey';}	
											}
										
										echo '
											<!-- Modulo -->
											<div class="newUpdate">
												<div class="uDate" align="center"><span class="uDay">',$mes_ref,'</span>',$ano_ref,'</div>
												<span class="lReceita '.$class.'" style="padding-top: 10px; font-size: 14px;">
													Web Finanças
												</span>
												<a href="javascript://void(0);" title="" class="button greenB" style="float: right; margin: 5px 0 5px 32px; color:#FFF;" onClick="boletoPerfilUsuario(\''.$boleto_link.'\');" ><span>Boleto</span></a>
												<div class="uDate" align="center" style="float:right; width: auto;"><span class="uDay">',$dia,'</span>',$mes,' / ',(substr($ano,2,2)),'</div>
												<div style="float:right; padding:10px 27px 10px 20px;  font-size:14px; width: 115px;" align="center" class="'.$class.'"><b> R$ '.$valor.' </b></div>
												<div style="float:right; padding-top:10px; font-size:14px; width:100px;" align="center" class="'.$class.'" > '.$situacao.' </div>
											</div>
											<!-- Fim modulo -->			
										';
									}else{ echo '<div align="center" style="padding-top: 20px; color: #999;">Você está em dia com o Web Finanças.</div>'; }
									$db_w2b->close();
									$db_wf->close();
									?>
                  
                  	<!-- Modulo 
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
                  -->
									<!-- Fim modulo -->			
                  
                  <!-- Modulo 
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
                  -->
									<!-- Fim modulo -->		
                  
                  <!-- Modulo 
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
                  -->
									<!-- Fim modulo -->	
								
              </div>
            </div>
                    
        </div> <!-- Widget -->
            
            </fieldset>       
        </form>

  </div> 

</div> 

<a href="faturas" class="wContentButton bluewB">Todas as Faturas</a>
<!-- Fim Fluid -->
<?php
}
?>

 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
    <?php include("dialog_editar_plano.php"); ?>
    
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 