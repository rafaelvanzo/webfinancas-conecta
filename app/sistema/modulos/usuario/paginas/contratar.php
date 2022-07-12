<!-- <script> alert(window.innerWidth); </script> --> 
<?php
$db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas', 'W2BSISTEMAS', 'webfinancas');
$cliente_trial = mysqli_fetch_assoc(mysqli_query($db_wf,'select dt_cadastro from clientes_trial where cliente_id = '.$cliente_id));
if(empty($cliente_trial)){ 
    echo "<script>location.href='https://www.webfinancas.com/sistema'</script>";
    break;
}
?>
<div align="center" style="margin-bottom:-50px;"><img src="images/logo_webfinancas_fundo_branco.png" align="middle" /></div>

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2></h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div> 
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
                 <li>
                      <a href="javascript://" style="cursor: default;">Perfil do Usuário</a>
                 </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Contratar</a>
                 </li>
            </ul>
	</div>  
</div> <!-- Fim Breadcrumbs -->


    
 <!--   <div class="line"></div>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
        <!-- Notifications -->
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
        </div>
    		
        <!-- =================== Palco =================== -->

 <!-- Organiza o layout -->  
  <!-- Organiza o layout -->   
   <div class="fluid">
                  		<div class="span12" align="center"> 
                      <br />
                      
                      
                      <div style="max-width: 580px; margin-left:auto; margin-right:auto; text-align:justify;">
                      
                      <span class="uDone" style="max-width: 600px; cursor: default;">
                            <a href="javascript://">
                              <strong>Preencha os dados abaixo e clique no botão Próximo. </strong></a>
                      </span>	
                        <br />
                      <span class="uDone" style="max-width: 600px; cursor: default;">
                            <a href="javascript://">
                              <strong>Selecione o tipo de plano e módulos que gostaria de contratar e clique em Contratar. </strong></a>
                      </span>
                      
   </div>
 
  
 	<div class="fluid">
 
  	<div id="divContratar" class="span12">
  		<div class="widget">
            <div class="title"><img src="images/icons/dark/imagesList.png" alt="" class="titleIcon"><h6>Contratar</h6></div>
            
     	<?php 
			$db_w2b = new mysqli('mysql.web2business.com.br', 'web2business', 'W2BSISTEMAS', 'web2business');
			$cliente_id = $_SESSION['cliente_id'];
			$dados_perfil_usuario = mysqli_fetch_assoc(mysqli_query($db_w2b,'select inscricao, cpf_cnpj, nome, email, email_fin, logradouro, numero, complemento, bairro, cidade, uf, cep, telefone from clientes where id = '.$cliente_id));			
			$db_w2b->close(); 
			?>         
            
			<form id="wizard2" method="post" class="form ui-formwizard" novalidate>
    
                <fieldset class="step ui-formwizard-content" id="w2first" style="display: block;">
                  
      <input type="hidden" name="funcao" value="usuariosEditar">
      <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>" />
                    <h1>1º Passo</h1>
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
                                <input type="text" name="numero" value="<?php echo $dados_perfil_usuario['numero']; ?>"/>
                            </span>
                            <span class="span4">
                             <label>Complemento:</label>
                                <input type="text" name="complemento" value="<?php echo $dados_perfil_usuario['complemento']; ?>" class="required"/>
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
										 <option value="">Estado</option>
										 <option value="AC">AC</option>
										 <option value="AL">AL</option>
										 <option value="AM">AM</option>
										 <option value="AP">AP</option>
										 <option value="BA">BA</option>
										 <option value="CE">CE</option>
										 <option value="DF">DF</option>
										 <option value="ES">ES</option>
										 <option value="GO">GO</option>
										 <option value="MA">MA</option>
										 <option value="MG">MG</option>
										 <option value="MS">MS</option>
										 <option value="MT">MT</option>
										 <option value="PA">PA</option>
										 <option value="PB">PB</option>
										 <option value="PE">PE</option>
										 <option value="PI">PI</option>
										 <option value="PR">PR</option>
										 <option value="RJ">RJ</option>
										 <option value="RN">RN</option>
										 <option value="RO">RO</option>
										 <option value="RR">RR</option>
										 <option value="RS">RS</option>
										 <option value="SC">SC</option>
										 <option value="SE">SE</option>
										 <option value="SP">SP</option>
										 <option value="TO">TO</option>
                                </select>
                            </span>
                            

                        </div>
                         
                         <div class="formRowB">
                         		<span class="span3">
                                <label>CEP:</label>
                                <input type="text" name="cep" value="<?php echo $dados_perfil_usuario['cep']; ?>" class="maskCep required"/>
                            </span>
                           <span class="span3">
                                <label>E-mail:</label>
                                <input type="text" name="email" value="<?php echo $dados_perfil_usuario['email']; ?>" class="email required"/>
                            </span>
                             <span class="span3">
                                <label>E-mail Fatura:</label>
                                <input type="text" name="email_fin" value="<?php echo $dados_perfil_usuario['email_fin']; ?>" class="email required"/>
                            </span>
                            <span class="span3">
                                <label>Telefone:</label>
                                <input type="text" name="telefone" value="<?php echo $dados_perfil_usuario['telefone']; ?>" class="maskPhone required"/>
                            </span> 
                          </div>  
                                     
                </fieldset>
                
                 <fieldset id="w2confirmation" class="step ui-formwizard-content" style="display: none;">
                    
                    <h1>2º Passo</h1>
    
  <?php			
  $db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas', 'W2BSISTEMAS', 'webfinancas');
  $cliente_id = $_SESSION['cliente_id'];					
  //$dados_plano = mysqli_fetch_assoc(mysqli_query($db_wf,'select plano_id, periodo, dia_vencimento from clientes_planos where cliente_id = '.$cliente_id));
	$dados_plano = array("periodo"=>1);
  ?>
    
    
    <input type="hidden"  name="funcao" value="planoEditar" />
    <input type="hidden"  name="plano_id" value="<?php echo 1;//$dados_plano['plano_id']; ?>" />
    <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $usuario_id; ?>" />
    <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $cliente_id; ?>" />
    <input type="hidden" id="modulos" name="modulos" value="" />

    
    <!-- TODOS OS VALOR DOS MÓDILOS MARCADOS -->   
    
  										<table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget">
                        <thead>
                            <tr>
                                <td colspan="2">Tipo de Plano</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">                           
                            <select class="select_ano" id="tp_plano" name="tp_plano" style="width: auto;" onchange="trocar_periodo();">                       
															<option value="1" <?php if($dados_plano['periodo'] == '1'){ echo 'selected="selected"'; } ?> >Mensal</option>													
															<option value="2" <?php if($dados_plano['periodo'] == '2'){ echo 'selected="selected"'; } ?> >Trimestral</option>
															<option value="3" <?php if($dados_plano['periodo'] == '3'){ echo 'selected="selected"'; } ?> >Semestral</option>													
															<option value="4" <?php if($dados_plano['periodo'] == '4'){ echo 'selected="selected"'; } ?> >Anual</option>
														</select>
                            <input type="hidden" id="tp_plano_atual" name="tp_plano_atual" value="<?php echo 1;//$dados_plano['periodo']; ?>" />
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
                    $plano_id = 1;//$dados_plano['plano_id'];
                    //$dados_plano_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome, vl_mensal,	vl_trimestral,	vl_semestral,	vl_anual from planos where id = '.$plano_id)); 
                    $dados_plano_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome, vl_mensal,	vl_trimestral,	vl_semestral,	vl_anual from planos where id = 1'));
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
                                	<img src="images/subIcon3.png" align="middle" class="tipS" original-title="O módulo segue valores e reajustes atuais."> Verde &nbsp;&nbsp;
                                	<img src="images/subIcon2.png" align="middle" class="tipS" original-title="O módulo segue valores e reajustes antigos."> Cinza
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
                    
                </fieldset>
                
               
				<div class="wizButtons"> 
                    <div class="status" id="status2"></div>
					<span class="wNavButtons">
          	            <input class="button redB ui-wizard-content ui-formwizard-button" value="Cancelar" type="button" style="margin-right: 10px;" onclick="CancelarContratacao();">
                        <input class="basic ui-wizard-content ui-formwizard-button" id="back2" value="Voltar" type="reset" disabled="disabled">
                        <input class="blueB ml10 ui-wizard-content ui-formwizard-button" id="next2" value="Próximo" type="submit" onclick="trocar_periodo();">
                    </span>
				
        </div>
		
    	</form>
      
			<div class="data" id="w2"></div>
        </div>
 		</div>
  </div> 
    
  </div><!-- Fim Fluid --> 
  
 	<!-- ====== Fim do Palco ====== -->
  
   <!-- ====== *** UI Dialogs *** ====== -->
  
    <?php include("dialog_contratar.php"); ?>
    
  <!-- ====== *** Fim UI Dialogs *** ====== -->
  
	</div> 
</div> 
<!-- Rodapé Personalizado -->
<br />
<div class="wrapper" align="center">Web Finanças © 2011-<?php echo date('Y'); ?>. Todos os direitos reservados.</a></div>