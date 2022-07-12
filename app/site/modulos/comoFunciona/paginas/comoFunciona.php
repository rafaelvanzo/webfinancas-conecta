			<div role="main" class="main">

				<section class="page-top">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<h2>Como Funciona</h2>
							</div>
						</div>
					</div>
				</section>
 
 <div class="container">

					<div class="row">
						<div class="col-md-3">
							<aside class="sidebar">

								<h4>Recursos do Sistema</h4>
								<ul class="nav nav-list primary push-bottom">
                  <li><a href="comoFunciona/controleFinanceiro" <?php if(empty($_GET['id_registro']) || $_GET['id_registro']  == 'controleFinanceiro'){ echo 'class="active"'; } ?> >Controle Financeiro</a></li>
                  <li><a href="comoFunciona/contasReceber" <?php if($_GET['id_registro'] == 'contasReceber'){ echo 'class="active"'; } ?> >Contas a Receber</a></li>
									<li><a href="comoFunciona/contasPagar" <?php if($_GET['id_registro'] == 'contasPagar'){ echo 'class="active"'; } ?> >Contas a Pagar</a></li>
                  <li><a href="comoFunciona/transferenciaContas" <?php if($_GET['id_registro'] == 'transferenciaContas'){ echo 'class="active"'; } ?>  >Transferências entre contas</a></li>
                  <li><a href="comoFunciona/programacao" <?php if($_GET['id_registro'] == 'programacao'){ echo 'class="active"'; } ?>  >Programação de Recebimentos e Pagamentos</a></li>
									<li><a href="comoFunciona/lancamentosRecorrentes" <?php if($_GET['id_registro'] == 'lancamentosRecorrentes'){ echo 'class="active"'; } ?>  >Lançamentos Recorrentes</a></li>
									<li><a href="comoFunciona/favorecidos" <?php if($_GET['id_registro'] == 'favorecidos'){ echo 'class="active"'; } ?>  >Gerenciamento de Favorecidos</a></li>
									<li><a href="comoFunciona/contasFinanceiras" <?php if($_GET['id_registro'] == 'contasFinanceiras'){ echo 'class="active"'; } ?>  >Contas Financeiras</a></li>
									<li><a href="comoFunciona/centroResponsabilidade" <?php if($_GET['id_registro'] == 'centroResponsabilidade'){ echo 'class="active"'; } ?>  >Centro de Responsábilidade</a></li>
									<li><a href="comoFunciona/planoContas" <?php if($_GET['id_registro'] == 'planoContas'){ echo 'class="active"'; } ?>  >Plano de Contas (Categorias)</a></li>
									<li><a href="comoFunciona/relatorios" <?php if($_GET['id_registro'] == 'relatorios'){ echo 'class="active"'; } ?>  >Relatórios</a></li>
                  <li><a href="comoFunciona/seguranca" <?php if($_GET['id_registro'] == 'seguranca'){ echo 'class="active"'; } ?>  >Segurança</a></li>   
                  <li><a href="comoFunciona/politicaPrivacidade" <?php if($_GET['id_registro'] == 'politicaPrivacidade'){ echo 'class="active"'; } ?>  >Política de Privacidade</a></li>                      
								</ul>

							</aside>
						</div>
						<div class="col-md-9">

						<?php 
						
						if(!empty($_GET['id_registro'])){ 
							$pagina = $_GET['id_registro'];	include("site/modulos/comoFunciona/paginas/".$pagina.".php"); 
						}else{
							include("site/modulos/comoFunciona/paginas/controleFinanceiro.php"); 
						}
						?>

						</div>

					</div>

				</div>

			</div> <!-- Fim Main -->
      
      