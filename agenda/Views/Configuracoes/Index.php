	<section role="main" class="content-body card-margin">

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== -->  

<!--	<div class="rows">

		<div class="col-lg-6">

					<section class="cards">

							<header class="panel-heading">

											<button class="mb-xs mt-xs mr-xs btn btn-primary text-left" onclick="modalOpen('Create', 'ModalUsuarios')"><i class="fa fa-user"></i> Novo usuário</button>

								<div class="panel-actions">
									<h4>Usuários</h4>
								</div>
 
							</header>

							<div class="panel-body">

						<table id="datatable-ajax-usuarios" class="table table-striped" data-url="Configuracoes/ListarUsuarios">

										<thead>

												<tr>
													<th>Id</th>
													<th>Nome</th>
													<th>E-mail</th>
													<th>Opções</th>
												</tr>

										</thead>

										<tbody></tbody>

								</table>


							</div>

						</section>

	</div>
	
</div> -->

	

<div class="rows">

		<div class="col-lg-6">


							<section id="dataTable" class="panel">

							<header class="panel-heading">													                               
               
								<button class="mb-xs mt-xs mr-xs btn btn-success" onclick="modalOpen('Create', 'ModalDoutor'); "><i class="fa fa-user-md"></i> Novo doutor</button>

								<div class="panel-actions">
									<h4>Doutores</h4>
								</div>
 
							</header>

							<div class="panel-body">

							<table id="datatable-ajax-doutor" class="table table-striped" data-url="Configuracoes/ListarDoutor">

										<thead>

												<tr>
													<th>Id</th>
													<th>Nome</th>
													<th>E-mail</th>
													<th>Opções</th>
												</tr>

										</thead>

										<tbody></tbody>

								</table>

							</div>

						</section>


	</div>


		<div class="col-lg-6">

					<section class="cards">

							<header class="panel-heading">

											<button class="mb-xs mt-xs mr-xs btn btn-warning text-left" onclick="modalOpen('Create', 'ModalConsultas')"><i class="fa fa-plus"></i> Adicionar </button>

								<div class="panel-actions">
									<h4>Consultas / Procedimentos</h4>
								</div>
 
							</header>

							<div class="panel-body">

							<table id="datatable-ajax-consultas" class="table table-striped" data-url="Configuracoes/ListarConsultas">

										<thead>

												<tr>
													<th>Id</th>
													<th>Tipo</th>
													<th>Descricao</th>
													<th>Opções</th>
												</tr>

										</thead>

										<tbody></tbody>

								</table>

							</div>

						</section>

	</div>

	
</div>

<!-- ========================================== -->
<!-- ------------------ Modal ----------------- -->
<!-- ========================================== -->   

<?php require('Modal.php'); ?>

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== --> 
