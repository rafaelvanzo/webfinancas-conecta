
<!-- ==== Modal Create Evento ==== -->
<div id="ModalReagendadas" class="modal fade"  role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-block modal-block-lg" role="document">


    <div class="modal-content">         


             <form id="FormCreate" action="" data-action-create="Pacientes/Create" data-action-details="Pacientes/Details" data-action-edit="Pacientes/Edit" data-msg-sucesso="Paciente cadastrado com sucesso." novalidate="novalidate" enctype="multipart/form-data">

              
				<div class="panel-heading">

                    <button type="button" class="close LimparForm Cancelar" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h4 class="panel-title">Consultas Reagendadas</h4>

              </div>    


              	<div class="panel-body"> 

                        <table  class="table table-striped" >

										<thead>

												<tr>
													<th>Data</th>
													<th>Nome do paciente</th>
													<th>Tipo de consulta</th>
													<th>Tipo de plano</th>	
												</tr>

										</thead>

										<tbody class="modalReagendadas"></tbody>

								</table>

                </div>
                                
            <div class="modal-footer">

              	<div class="row">

                        <div class="col-md-12 text-right">                   

                                        <button type="button" class="btn btn-default" data-dismiss="modal" >Fechar</button>
                        
                        </div>

                    </div>

              </div>        

          
        </form>


    </div><!-- /.modal-content -->


  </div><!-- /.modal-dialog -->


</div><!-- /.modal -->
