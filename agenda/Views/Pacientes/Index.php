	<section role="main" class="content-body">

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== -->  

					<section id="dataTable" class="panel">

							<header class="panel-heading">
								<!--<div class="panel-actions">
									<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
									<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
								</div>-->
													                               
                <button class="mb-xs mt-xs mr-xs btn btn-primary" onclick="modalOpen('Create', 'ModalCreate')"><i class="fa fa-plus"></i> Novo Paciente</button>
								
 
							</header>

							<div class="panel-body">

								<table id="datatable-ajax" class="table table-striped" data-url="Pacientes/Listar">

										<thead>

												<tr>
													<th>Id</th>
													<th>Nome</th>
													<th>Email</th>
													<th>Telefone</th>
													<th>Celular</th>
													<th>Opções</th>
												</tr>

										</thead>

										<tbody></tbody>

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
