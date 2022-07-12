			<div role="main" class="main">

				<section class="page-top">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<h2>Central de Ajuda</h2>
							</div>
						</div>
					</div>
				</section>
 
 <div class="container">

					<div class="row">
						<div class="col-md-3 push-bottom">
							<aside class="sidebar">

							<section class="toggle <?php if( $_GET['id_registro'] == 'geralSaldos' || $_GET['id_registro'] == 'geralProgramacao' || $_GET['id_registro'] == 'geralGraficoEntradasSaidasMensal' || $_GET['id_registro'] == 'geralMovimentacaoFinanceira' || $_GET['id_registro'] == 'geralGraficoEntradasSaidasAnual'){ echo 'active';}?>">
								<label>Geral</label>
								<div class="toggle-content" >
									<ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/geralSaldos" <?php if($_GET['id_registro']  == 'geralSaldos'){ echo 'class="active"'; } ?> >Saldos</a></li>
                  <li><a href="/centralAjuda/geralProgramacao" <?php if($_GET['id_registro']  == 'geralProgramacao'){ echo 'class="active"'; } ?> >Programação</a></li>
                  <li><a href="/centralAjuda/geralGraficoEntradasSaidasMensal" <?php if($_GET['id_registro']  == 'geralGraficoEntradasSaidasMensal'){ echo 'class="active"'; } ?> >Gráfico Entradas x Saídas (MENSAL)</a></li>
                  <li><a href="/centralAjuda/geralMovimentacaoFinanceira" <?php if($_GET['id_registro']  == 'geralMovimentacaoFinanceira'){ echo 'class="active"'; } ?> >Gráfico Movimentação Financeira (MENSAL)</a></li>
                  <li><a href="/centralAjuda/geralGraficoEntradasSaidasAnual" <?php if($_GET['id_registro']  == 'geralGraficoEntradasSaidasAnual'){ echo 'class="active"'; } ?> >Gráfico Entradas x Saídas (ANUAL)</a></li>
								</ul>
								</div>
							</section>
                
              <section class="toggle <?php if( $_GET['id_registro'] == 'lancamentosGeral' || $_GET['id_registro'] == 'lancamentosVisualizarLancamentos' || $_GET['id_registro'] == 'filtrarLancamentos' || $_GET['id_registro'] == 'lancamentosNovoRecebimento' || $_GET['id_registro'] == 'lancamentosNovoPagamento' || $_GET['id_registro'] == 'lancamentosNovaTransferencia' || $_GET['id_registro'] == 'addPcontasCentroR' || $_GET['id_registro'] == 'lancamentosEditarLancamentos' || $_GET['id_registro'] == 'lancamentosEditarTransferencias' || $_GET['id_registro'] == 'lancamentosEditarPCeCR' || $_GET['id_registro'] == 'excluirLancamentos'){ echo 'active';}?>">
								<label>Lançamentos</label>
								<div class="toggle-content">  
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/lancamentosGeral" <?php if($_GET['id_registro']  == 'lancamentosGeral'){ echo 'class="active"'; } ?> >Estrutura Geral</a></li>
                  <li><a href="/centralAjuda/filtrarLancamentos" <?php if($_GET['id_registro']  == 'filtrarLancamentos'){ echo 'class="active"'; } ?> >Filtrar Lançamentos</a></li>
                  <li><a href="/centralAjuda/lancamentosNovoRecebimento" <?php if($_GET['id_registro']  == 'lancamentosNovoRecebimento'){ echo 'class="active"'; } ?> >Novo Recebimento</a></li>
                  <li><a href="/centralAjuda/lancamentosNovoPagamento" <?php if($_GET['id_registro']  == 'lancamentosNovoPagamento'){ echo 'class="active"'; } ?> >Novo Pagamento</a></li>
                  <li><a href="/centralAjuda/lancamentosNovaTransferencia" <?php if($_GET['id_registro']  == 'lancamentosNovaTransferencia'){ echo 'class="active"'; } ?> >Nova Transferência</a></li>
								 	<li><a href="/centralAjuda/addPcontasCentroR" <?php if($_GET['id_registro']  == 'addPcontasCentroR'){ echo 'class="active"'; } ?> >Adicionar Valores a um Plano de Contas / Centro de Responsabilidade</a></li>
                  <li><a href="/centralAjuda/lancamentosEditarLancamentos" <?php if($_GET['id_registro']  == 'lancamentosEditarLancamentos'){ echo 'class="active"'; } ?> >Editar Recebimentos e Pagamentos</a></li>
                  <li><a href="/centralAjuda/lancamentosEditarTransferencias" <?php if($_GET['id_registro']  == 'lancamentosEditarTransferencias'){ echo 'class="active"'; } ?> >Editar Transferências</a></li>
                  <li><a href="/centralAjuda/lancamentosEditarPCeCR" <?php if($_GET['id_registro']  == 'lancamentosEditarPCeCR'){ echo 'class="active"'; } ?> >Editar Plano de Contas e Centro de Responsabilidade</a></li>
                  <li><a href="/centralAjuda/excluirLancamentos" <?php if($_GET['id_registro']  == 'excluirLancamentos'){ echo 'class="active"'; } ?> >Excluir Recebimentos, Pagamentos e Transferências</a></li>
								</ul>
              	</div>
							</section>  
              
             <section class="toggle  <?php if( $_GET['id_registro'] == 'programacaoGeralRPT' || $_GET['id_registro'] == 'ProgramacaofiltrarLancamentos' || $_GET['id_registro'] == 'novaContaReceber' || $_GET['id_registro'] == 'novaContaPagar' || $_GET['id_registro'] == 'novaTransferenciaProg' || $_GET['id_registro'] == 'novaContaReceberR' || $_GET['id_registro'] == 'novaContaPagarR' || $_GET['id_registro'] == 'programacaoQuitarRPT' || $_GET['id_registro'] == 'programacaoEditarLancamentos' || $_GET['id_registro'] == 'programacaoEditarPCeCR' || $_GET['id_registro'] == 'excluirProgramacao'){ echo 'active';}?>">
								<label>Programação</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/programacaoGeralRPT" <?php if($_GET['id_registro']  == 'programacaoGeralRPT'){ echo 'class="active"'; } ?> >Estrutura Geral</a></li>
                  <li><a href="/centralAjuda/ProgramacaofiltrarLancamentos" <?php if($_GET['id_registro']  == 'ProgramacaofiltrarLancamentos'){ echo 'class="active"'; } ?> >Filtrar Lançamentos</a></li>
                  <li><a href="/centralAjuda/novaContaReceber" <?php if($_GET['id_registro']  == 'novaContaReceber'){ echo 'class="active"'; } ?> >Nova Conta a Receber <br />(Recebimento Programado)</a></li>
                  <li><a href="/centralAjuda/novaContaPagar" <?php if($_GET['id_registro']  == 'novaContaPagar'){ echo 'class="active"'; } ?> >Nova Conta a Pagar <br />(Pagamento Programado)</a></li>
                  <li><a href="/centralAjuda/novaTransferenciaProg" <?php if($_GET['id_registro']  == 'novaTransferenciaProg'){ echo 'class="active"'; } ?> >Nova Transferência Programada</a></li>
								 	<li><a href="/centralAjuda/novaContaReceberR" <?php if($_GET['id_registro']  == 'novaContaReceberR'){ echo 'class="active"'; } ?> >Novo Recebimento Recorrente</a></li>
                  <li><a href="/centralAjuda/novaContaPagarR" <?php if($_GET['id_registro']  == 'novaContaPagarR'){ echo 'class="active"'; } ?> >Novo Pagamento Recorrente</a></li>
									<li><a href="/centralAjuda/programacaoQuitarRPT" <?php if($_GET['id_registro']  == 'programacaoQuitarRPT'){ echo 'class="active"'; } ?> >Compensar (QUITAR) Manualmente as Contas a Receber, Contas a Pagar e Transferências Programdas</a></li>
                  <li><a href="/centralAjuda/programacaoEditarLancamentos" <?php if($_GET['id_registro']  == 'programacaoEditarLancamentos'){ echo 'class="active"'; } ?> >Editar Lançamentos Programados e Recorrentes </a></li>
                  <li><a href="/centralAjuda/programacaoEditarPCeCR" <?php if($_GET['id_registro']  == 'programacaoEditarPCeCR'){ echo 'class="active"'; } ?> >Editar Plano de Contas e Centro de Responsabilidade</a></li>
                  <li><a href="/centralAjuda/excluirProgramacao" <?php if($_GET['id_registro']  == 'excluirProgramacao'){ echo 'class="active"'; } ?> >Excluir Lançamentos Programados e Recorrentes</a></li>
								</ul>

              	</div>
							</section>
              
               <section class="toggle  <?php if( $_GET['id_registro'] == 'PlanejamentoOrcamentoFinanceiro' || $_GET['id_registro'] == 'PlanejamentoOrcamentoFinanceiroEditar' || $_GET['id_registro'] == 'PlanejamentoOrcamentoEmpenho' || $_GET['id_registro'] == 'PlanejamentoOrcamentoEmpenhoR' || $_GET['id_registro'] == 'PlanejamentoOrcamentoEmpenhoP' || $_GET['id_registro'] == 'PlanejamentoOrcamentoEmpenhoE' || $_GET['id_registro'] == 'excluirEmpenho'){ echo 'active';}?>">
								<label>Planejamento</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/PlanejamentoOrcamentoFinanceiro" <?php if($_GET['id_registro']  == 'PlanejamentoOrcamentoFinanceiro'){ echo 'class="active"'; } ?> >Novo Orçamento Financeiro</a></li>
                  <li><a href="/centralAjuda/PlanejamentoOrcamentoFinanceiroEditar" <?php if($_GET['id_registro']  == 'PlanejamentoOrcamentoFinanceiroEditar'){ echo 'class="active"'; } ?> >Editar Orçamento Financeiro</a></li>                  
                  <li><a href="/centralAjuda/PlanejamentoOrcamentoEmpenhoR" <?php if($_GET['id_registro']  == 'PlanejamentoOrcamentoEmpenhoR'){ echo 'class="active"'; } ?> >Novo Empenho Recebimento</a></li>
                  <li><a href="/centralAjuda/PlanejamentoOrcamentoEmpenhoP" <?php if($_GET['id_registro']  == 'PlanejamentoOrcamentoEmpenhoP'){ echo 'class="active"'; } ?> >Novo Empenho Pagamento</a></li>
                  <li><a href="/centralAjuda/PlanejamentoOrcamentoEmpenhoE" <?php if($_GET['id_registro']  == 'PlanejamentoOrcamentoEmpenhoE'){ echo 'class="active"'; } ?> >Editar Empenho de Recebimento e Pagamento</a></li>
                  <li><a href="/centralAjuda/excluirEmpenho" <?php if($_GET['id_registro']  == 'excluirEmpenho'){ echo 'class="active"'; } ?> >Excluír Empenho</a></li>
                </ul>

              	</div>
							</section>                  

  						<section class="toggle  <?php if( $_GET['id_registro'] == 'favorecidoGeral' || $_GET['id_registro'] == 'favorecidoNovo' || $_GET['id_registro'] == 'favorecidoEditar' || $_GET['id_registro'] == 'excluirFavorecido'){ echo 'active';}?>">
								<label>Favorecido</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/favorecidoGeral" <?php if($_GET['id_registro']  == 'favorecidoGeral'){ echo 'class="active"'; } ?> >Estrutura Geral</a></li>
                  <li><a href="/centralAjuda/favorecidoNovo" <?php if($_GET['id_registro']  == 'favorecidoNovo'){ echo 'class="active"'; } ?> >Novo Favorecido</a></li>                  
                  <li><a href="/centralAjuda/favorecidoEditar" <?php if($_GET['id_registro']  == 'favorecidoEditar'){ echo 'class="active"'; } ?> >Editar Favorecido</a></li>
                  <li><a href="/centralAjuda/excluirFavorecido" <?php if($_GET['id_registro']  == 'excluirEmpenho'){ echo 'class="active"'; } ?> >Excluír Favorecido</a></li>
                </ul>
                
								</div>
							</section>

							<section class="toggle  <?php if( $_GET['id_registro'] == 'centroGeral' || $_GET['id_registro'] == 'centroNovo' || $_GET['id_registro'] == 'centroEditar' || $_GET['id_registro'] == 'excluirCentro'){ echo 'active';}?>">
								<label>Centro de Responsábilidade</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/centroGeral" <?php if($_GET['id_registro']  == 'centroGeral'){ echo 'class="active"'; } ?> >Estrutura Geral</a></li>
                  <li><a href="/centralAjuda/centroNovo" <?php if($_GET['id_registro']  == 'centroNovo'){ echo 'class="active"'; } ?> >Novo Centro</a></li>                  
                  <li><a href="/centralAjuda/centroEditar" <?php if($_GET['id_registro']  == 'centroEditar'){ echo 'class="active"'; } ?> >Editar Centro</a></li>
                  <li><a href="/centralAjuda/excluirCentro" <?php if($_GET['id_registro']  == 'excluirCentro'){ echo 'class="active"'; } ?> >Excluír Centro</a></li>
                </ul>

              	</div>
							</section>
              
              <section class="toggle  <?php if( $_GET['id_registro'] == 'planoGeral' || $_GET['id_registro'] == 'planoNovo' || $_GET['id_registro'] == 'planoEditar' || $_GET['id_registro'] == 'excluirPlano'){ echo 'active';}?>">
								<label>Plano de Contas</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/planoGeral" <?php if($_GET['id_registro']  == 'planoGeral'){ echo 'class="active"'; } ?> >Estrutura Geral</a></li>
                  <li><a href="/centralAjuda/planoNovo" <?php if($_GET['id_registro']  == 'planoNovo'){ echo 'class="active"'; } ?> >Novo Plano de Contas</a></li>                  
                  <li><a href="/centralAjuda/planoEditar" <?php if($_GET['id_registro']  == 'planoEditar'){ echo 'class="active"'; } ?> >Editar Plano de Contas</a></li>
                  <li><a href="/centralAjuda/excluirPlano" <?php if($_GET['id_registro']  == 'excluirPlano'){ echo 'class="active"'; } ?> >Excluír Plano de Contas</a></li>
                </ul>
                
              	</div>
							</section>
              
                <section class="toggle  <?php if( $_GET['id_registro'] == 'contasFinanceirasGeral' || $_GET['id_registro'] == 'contasFinanceiraNovo' || $_GET['id_registro'] == 'contasFinanceiraEditar' || $_GET['id_registro'] == 'excluirContaFinanceira'){ echo 'active';}?>">
								<label>Contas Financeiras</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/contasFinanceirasGeral" <?php if($_GET['id_registro']  == 'contasFinanceirasGeral'){ echo 'class="active"'; } ?> >Estrutura Geral</a></li>
                  <li><a href="/centralAjuda/contasFinanceiraNovo" <?php if($_GET['id_registro']  == 'contasFinanceiraNovo'){ echo 'class="active"'; } ?> >Novo Plano de Contas</a></li>                  
                  <li><a href="/centralAjuda/contasFinanceiraEditar" <?php if($_GET['id_registro']  == 'contasFinanceiraEditar'){ echo 'class="active"'; } ?> >Editar Plano de Contas</a></li>
                  <li><a href="/centralAjuda/excluirContaFinanceira" <?php if($_GET['id_registro']  == 'excluirContaFinanceira'){ echo 'class="active"'; } ?> >Excluír Plano de Contas</a></li>
                </ul> 

              	</div>
							</section>
              
               <section class="toggle  <?php if( $_GET['id_registro'] == 'relatorioFluxoCaixa' || $_GET['id_registro'] == 'relatorioMvFinanceira' || $_GET['id_registro'] == 'relatorioSaldoContasFin' || $_GET['id_registro'] == 'relatorioContasReceber' || $_GET['id_registro'] == 'relatorioContasPagar' || $_GET['id_registro'] == 'relatorioPContas' || $_GET['id_registro'] == 'relatorioCentroResp' || $_GET['id_registro'] == 'relatorioPContasCentroResp'){ echo 'active';}?>">
								<label>Relatórios</label>
								<div class="toggle-content">   
                <ul class="nav nav-list primary push-bottom">
                  <li><a href="/centralAjuda/relatorioFluxoCaixa" <?php if($_GET['id_registro']  == 'relatorioFluxoCaixa'){ echo 'class="active"'; } ?> >Fluxo de Caixa</a></li>
                  <li><a href="/centralAjuda/relatorioMvFinanceira" <?php if($_GET['id_registro']  == 'relatorioMvFinanceira'){ echo 'class="active"'; } ?> >Movimentação Financeira </a></li>                  
                  <li><a href="/centralAjuda/relatorioSaldoContasFin" <?php if($_GET['id_registro']  == 'relatorioSaldoContasFin'){ echo 'class="active"'; } ?> >Saldo Contas Financeiras </a></li>
                  <li><a href="/centralAjuda/relatorioContasReceber" <?php if($_GET['id_registro']  == 'relatorioContasReceber'){ echo 'class="active"'; } ?> >Contas à Receber</a></li>
                  <li><a href="/centralAjuda/relatorioContasPagar" <?php if($_GET['id_registro']  == 'relatorioContasPagar'){ echo 'class="active"'; } ?> >Contas à Pagar </a></li>
                  <li><a href="/centralAjuda/relatorioPContas" <?php if($_GET['id_registro']  == 'relatorioPContas'){ echo 'class="active"'; } ?> >Plano de Contas </a></li>                  
                  <li><a href="/centralAjuda/relatorioCentroResp" <?php if($_GET['id_registro']  == 'relatorioCentroResp'){ echo 'class="active"'; } ?> >Centro de Responsábilidade </a></li>
                  <li><a href="/centralAjuda/relatorioPContasCentroResp" <?php if($_GET['id_registro']  == 'relatorioPContasCentroResp'){ echo 'class="active"'; } ?> >Plano de Contas x Centro de Responsabilidade </a></li>
                </ul> 

              	</div>
							</section>

							</aside>
						</div>
            
						<div class="col-md-9">

						<?php 
						
						if(!empty($_GET['id_registro'])){ 
							$pagina = $_GET['id_registro'];	include("site/modulos/centralAjuda/paginas/".$pagina.".php"); 
						}else{
							include("site/modulos/centralAjuda/paginas/inicial.html"); 
						}
						?>

						</div>

					</div>

				</div>

			</div> <!-- Fim Main -->
      
      