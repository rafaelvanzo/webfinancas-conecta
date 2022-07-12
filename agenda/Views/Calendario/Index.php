	<section role="main" class="content-body">

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== -->  


					<section  class="panel">

							<header class="panel-heading">
								

								<?php if(empty($_SESSION['usuarioDoutor'])) { ?>

									<div class="panel-actions">
										<select name="CalendarioDoutor" class="form-control CalendarioDoutor">
											<?php  echo CalendarioController::doutorListar($dbUsuario); ?>
										</select>
									</div>

								<?php } ?>

								<?php if($_SESSION['Tipo'] != 2){ ?>					                               
                				
									<button class="mb-xs mt-xs mr-xs btn btn-primary zeraForm" onclick="modalOpen('Create', 'ModalAddEvento')"><i class="fa fa-plus"></i> Novo agendamento</button>								
 
								<?php } ?>

							</header>

							<div class="panel-body">


							<div id="calendar"  data-calendar-url-base="Calendario/Visualizar/" data-calendar-url="Calendario/Visualizar/<?php echo $_SESSION['usuarioDoutor']; ?>"></div>

							</div>

						</section>

                       
<!-- ========================================== -->
<!-- ------------------ Modal ----------------- -->
<!-- ========================================== -->   
	
<?php require_once('modal.php'); ?>

<!-- ========================================== -->
<!-- ------------------ Palco ----------------- -->
<!-- ========================================== --> 
