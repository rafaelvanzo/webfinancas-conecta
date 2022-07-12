<!-- <script> alert(window.innerWidth); </script> --> 

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
    

   <!-- Breadcrumbs 
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Conta bloqueada</a>
	               </li>
            </ul>
	</div>  
</div>  Fim Breadcrumbs -->

<h2 align="center">Conta Suspensa</h2>
    
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
   <div class="fluid">
                  		<div class="span12" align="center"> 
                      <br />
                      
                      
                      <div style="max-width: 480px; margin-left:auto; margin-right:auto; text-align:justify;">
                      
                      <span class="uAlert" style="max-width: 600px; cursor: default;">
                            <a href="javascript://">
                              <strong>O seu acesso foi temporariamente suspenso devido à existência de faturas em aberto. </strong></a>
                      </span>	
                        <br />
                      <span class="uAlert" style="max-width: 600px; cursor: default;">
                            <a href="javascript://">
                              <strong>Caso os débitos permaneçam por um período maior do que 60 dias após o vencimento, a sua licença no Web Finanças será cancelada automaticamente. </strong></a>
                      </span>
                      	<br /> <br /><br />                       
                      <span class="uDone" style="max-width: 600px; cursor: default;">
                            <a href="javascript://">
                              <strong>Para normalizar o seu acesso ao sistema, regularize sua situação efetuando o pagamento dos débitos clicando no botão <i>"Gerar Boleto"</i>. <br />
                              O acesso ao sistema só será restabelecido após a confirmação do pagamento.</strong></a>  
                      </span>
                      </div>
                      
                     		<br /><br /><br /><br />
                        <a href="javascript://void(0);" class="button redB sair" style="margin: 5px 0 5px 5px; color:#FFF;" ><span>Sair</span></a>
                      	<a href="javascript://void(0);" class="button greenB" style="margin: 5px 0 5px 5px; color:#FFF;" onClick="faturaAtrasada();"><span>Gerar Boleto</span></a>
                      </div>
                </div>
   
 <div class="fluid">
  <div class="span12">

			<form action="" class="form">
          <fieldset>

                   <div class="widget">
                    <div class="title"><img src="images/icons/dark/priceTag.png" alt="" class="titleIcon"><h6>Faturas em aberto</h6></div>
                   <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget">
                        <thead>
                            <tr>
                                <td width="auto">Fatura</td>
                                <td width="100">Situação</td>
                                <td width="115">Valor</td>
                                <td width="60">Vencimento</td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table> 
                    
							<div class="scroll" style="height: 118px; padding-top: 1px;">
              		<div class="updates">
									<?php
                      $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
											$hoje = date("Y-m-d");
                      $faturas = $db_w2b->fetch_all_array("select lancamento_id, sequencial from faturas where cliente_id = ".$_SESSION["cliente_id"]." and compensado = 0 and dt_vencimento < '".$hoje."'");
											foreach($faturas as $fatura){
												echo '
												<div class="newUpdate">
													<div class="uDate" align="center"><span class="uDay">Mai</span>2014</div>
													<span class="lReceita red" style="padding-top: 10px; font-size: 14px;">
														Fatura
													</span>										
													<div class="uDate" align="center" style="float:right; padding-right:20px;"><span class="uDay">10</span>Mai/14</div>	
													<div style="float:right; padding:10px 30px 10px 20px;  font-size:14px; width: 115px;" align="center" class="red"> R$ 69,80 / mês</div>
													<div style="float:right; padding-top:10px; font-size:14px; width:100px;" align="center" class="red"> Em aberto </div>
												</div>
												';
											}
                  ?>
                  <!-- Modulo -->
                  <!--
									<div class="newUpdate">
                    <div class="uDate" align="center"><span class="uDay">Mai</span>2014</div>
                    <span class="lReceita red" style="padding-top: 10px; font-size: 14px;">
											Fatura
										</span>										
										<div class="uDate" align="center" style="float:right; padding-right:20px;"><span class="uDay">10</span>Mai/14</div>	
                    <div style="float:right; padding:10px 30px 10px 20px;  font-size:14px; width: 115px;" align="center" class="red"> R$ 69,80 / mês</div>
                    <div style="float:right; padding-top:10px; font-size:14px; width:100px;" align="center" class="red"> Em aberto </div>
									</div>
                  -->
									<!-- Fim modulo -->
              </div>
            </div>
                    
        </div> <!-- Widget -->
  
 	<!-- ====== Fim do Palco ====== -->
  
   <!-- ====== *** UI Dialogs *** ====== -->
      
  <!-- ====== *** Fim UI Dialogs *** ====== -->
  
	</div> 
</div> 
<!-- Rodapé Personalizado -->
<br />
<div class="wrapper" align="center">Web Finanças © 2011-<?php echo date('Y'); ?>. Todos os direitos reservados.</a></div>