	<section role="main" class="content-body">

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== -->  



					<section  class="panel">

						<form id="FormRelatorios" action="Relatorios/Gerar">
						
						   
							 <div class="form-group">

                    
								<div class="col-sm-3">                            
										
									<label class="control-label">Data Inicial <span class="required" aria-required="true">*</span></label>

									<input name="DataInicial" class="form-control" placeholder="__/__/____" required="" aria-required="true" value="<?php echo date("d/m/Y", strtotime('sunday last week')); ?>" data-plugin-datepicker data-plugin-masked-input data-input-mask="99/99/9999">

								</div> 

								<div class="col-sm-3">                            
										
									<label class="control-label">Data Final <span class="required" aria-required="true">*</span></label>

									<input name="DataFinal" class="form-control" placeholder="__/__/____" required="" aria-required="true" value="<?php echo date("d/m/Y", strtotime('saturday this week')); ?>" data-plugin-datepicker data-plugin-masked-input data-input-mask="99/99/9999">

								</div> 

								<div class="col-sm-2">                            
										
									<label class="control-label">Tipo de pgto </label>

									<select name="TipoPlano" class="form-control" >
										<option value="">Todos</option>
										<option value="1">Particular</option>
                                		<option value="2">Plano de saúde</option>     
									</select>

								</div> 

								<div class="col-sm-2">                            
										
									<label class="control-label">Situação </label>

									<select name="Situacao" class="form-control" >
										<option value="">Todos</option>
										<option value="0">Aguardando</option>
										<option value="1">Atendido</option>
										<option value="2">Faltou</option>
										<option value="3">Reagendada</option>
									</select>

								</div> 

								<div class="col-sm-2">                            
										
									<label class="control-label">Consulta/Procedimento </label>

									<select name="IdConsulta" class="form-control selectTwo" data-plugin-selectTwo data-select-url="Calendario/configConsultaProc" data-placeholder="Todos"></select>

								</div> 

								
							</div>



						   <div class="form-group">

                    
								<div class="col-sm-4">                            
										
									<label class="control-label">Nome do paciente </label>

									<select name="IdFavorecido" class="form-control" data-plugin-selectTwo data-select-url="Calendario/Favorecidos" data-placeholder="Todos"></select>

								</div> 

								<div class="col-sm-4">                            
										
									<label class="control-label">Responsável </label>

									<select name="IdResponsavel" class="form-control selectTwo" data-plugin-selectTwo data-select-url="Calendario/Favorecidos" data-placeholder="Todos"></select>

								</div> 

								<div class="col-sm-4">                            
										
									<label class="control-label">Doutor </label>

									<select name="IdDoutor" class="form-control selectTwo" data-plugin-selectTwo data-select-url="Calendario/Doutor" data-placeholder="Todos"></select>

								</div> 

																
							</div>
						
	
						
							<div class="form-group">
							
								<div class="col-sm-12">
								
									<button class="btn btn-primary btn-lg btn-block gerarRelatorio" >GERAR RELATÓRIO</button>
								
								</div>
							
							</div>


						
						</form>
	
					</section>

                       




					<section id="dataTable" >

							<header class="panel-heading">

								<h2>Relatório

									<div class="btn-group pull-right" role="group">
											<a class="btn btn-default w-100 btn-success" onClick="GerarExcel();" role="button"><i class="fa fa-file-excel-o"></i> Exportar p/ Excel</a>
											
											<!--<a class="btn btn-default w-100 btn-primary" role="button"><i class="fa fa-file-pdf-o"></i> PDF</a>-->
									</div>
								
								</h2>
 
							</header>

							<div id="excel" class="panel-body">

								<table id="excel"class="table table-striped" >

										<thead>

												<tr>
													<th>Data</th>
													<th>Nome do paciente</th>
													<th>Tipo de consulta</th>
													<th>Tipo de plano</th>
													<th>Nome do responsável</th>
													<th>Doutor</th>
													<th>Situação</th>
												</tr>

										</thead>

										<tbody class="tableRelatorio"></tbody>

								</table>

							</div>

						</section>

                       
<!-- ========================================== -->
<!-- ------------------ Modal ----------------- -->
<!-- ========================================== -->   

<?php require_once('modal.php'); ?>

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== --> 
