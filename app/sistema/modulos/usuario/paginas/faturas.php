<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Faturas</h2>
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
                 <li class="">
                      <a href="perfilUsuario">Perfil do usuário</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Faturas</a>
                 </li>
            </ul>
	</div>  
</div> <!-- Fim Breadcrumbs -->

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />

<div class="wrapper">        	    
     <a href="perfilUsuario" title="" class="button redB" style="margin: 5px;"><img src="images/icons/light/arrowLeft.png" alt="" class="icon"><span>Voltar</span></a>
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
 
 
                   <div class="widget">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Todas as Faturas</h6></div>
                   <table cellpadding="0" cellspacing="0" width="100%" class="display dTableFaturas">
                        <thead>
                            <tr>
                            		<th></td>
                            		<th width="60">período</td>
                                <th width="auto">Fatura</td>
                                <th width="100">Situação</td>
                                <th width="115">Valor</td>
                                <th width="60">Vencimento</td>
                            </tr>
                        </thead>
                        <tbody>
                    

                  <?php
									$db_w2b = new mysqli('mysql.web2business.com.br', 'web2business', 'W2BSISTEMAS', 'web2business');
									$db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas02', 'W2BSISTEMAS', 'webfinancas02');
									$cliente_id = $_SESSION['cliente_id'];
									//$faturas = mysqli_query($db_w2b,'select valor from faturas where cliente_id = '.$cliente_id);
									//if(mysqli_num_rows($faturas)>0){
									
										$faturas = mysqli_query($db_w2b,'select sequencial, lancamento_id, compensado, valor, dt_referencia, dt_vencimento from faturas where cliente_id = '.$cliente_id.' and sistema_id = 1 order by id desc');
										while($fatura = mysqli_fetch_assoc($faturas)){
										$valor = $fatura['valor'];
										$valor = number_format($valor,2,',','.');
										
										$dt_referencia = explode("-",$fatura["dt_referencia"]);
										$m_ref =$dt_referencia[1];
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
													if($fatura['dt_vencimento'] < date('Y-m-d')){ $situacao = 'Atrasado'; $class = 'class="red"'; }else{ $situacao = 'Aguardando'; }	
											}else{  $situacao = 'Pago'; $class = 'class="blue"';}
										echo '
											<!-- Modulo -->
											<tr class="gradeA">
												<td align="center">
													',$fatura['dt_vencimento'],'
												</td>
												<td align="center">
													<div style="color:#999; line-height:18px; padding-top: 5px;" ><span style="font-size:20px; font-weight:bold;" >',$mes_ref,'</span><br>',$ano_ref,'</div>
												</td>
												<td>
													<span style="font-size:14px;" ',$class,'>Web Finanças  </span>
												</td>
												<td>
													<div style="font-size:14px; width:100px;" align="center" ',$class,'> ',$situacao,' </div>
												</td>
												<td>
													<div style="font-size:15px; width: 115px;" align="center" ',$class,'> R$ '.$valor.' </div>
												</td>
												<td align="center">
													<div style="color:#999; line-height:15px; padding-top: 5px;"><span style="font-size:20px; font-weight:bold;">'.(substr($dia,0,2)).'</span><br>',$mes,' / ',(substr($ano,2,2)),'</div>
												</td>
											<!-- Fim modulo -->			
										'; 
										} 
									//}
									$db_w2b->close();
									$db_wf->close();
									?>
            
            </tbody>
      </table> 
                    
        </div> <!-- Widget -->
  
</div> <!-- Fim Fluid --> 
 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
    
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 