<!-- ==== Modal Delete ==== -->
<div id="ModalAlterarSenha" class="modal fade" tabindex="-1" role="dialog">
                      <!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-sm" role="document">
    <div class="modal-content">         

            <form id="FormAlterarSenha" class="FormAlterarSenha" mehod="post" data-action-create="Login/Edit/<?php echo $_SESSION['UsuarioId']; ?>" data-msg-sucesso="Senha alterada com sucesso." novalidate="novalidate" >

              <div class="modal-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="panel-title">Alterar Senha</h4>
              </div>    
            
            <div class="modal-body"> 					
                           
                    <div class="current-user text-center" >
						<!--<img src="assets/images/!logged-user.jpg" alt="John Doe" class="img-circle user-image" style="max-width:140px;">-->
						<h2 class="user-name text-dark m-none"><?php echo $_SESSION['Nome']; ?></h2>
						<p class="user-email m-none"><?php echo $_SESSION['Email']; ?></p>
                        <br>
					</div>

                    <div class="form-group">					    
					    <div class="col-sm-12">	

								<div class="input-group input-group-icon">												
									<input class="form-control input-lg" type="password" name="senha" id="senha" placeholder="Nova senha" required>
                                    <span class="input-group-addon">
										<span class="icon icon-lg"><i class="fa fa-key"></i></span>
									</span>
								</div>
                        </div>
                    </div>
                    <div class="form-group">					    
					    <div class="col-sm-12">

								<div class="input-group input-group-icon">												
									<input class="form-control input-lg" type="password" name="senhasIgual" placeholder="Repita a nova senha" required>
                                    <span class="input-group-addon">
										<span class="icon icon-lg"><i class="fa fa-key"></i></span>
									</span>
								</div>                                     
				            
					    </div>
                    </div>
            </div>
             
            <div class="modal-footer">
                <div class="row">
			        <div class="col-md-12 text-right">                   
                        <button type="button" class="btn btn-primary SalvarForm" >Salvar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    </div>
		        </div>

              </div>        
          
        </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- ==== Modal Delete ==== -->
<div id="ModalLogout" class="modal fade" tabindex="-1" role="dialog">
                      <!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-sm" role="document">
    <div class="modal-content">         

            <form id="FormLogout"  novalidate="novalidate">

              <div class="modal-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="panel-title">Logout</h4>
              </div>    
            
            <div class="modal-body">                     

      			    <div class="modal-wrapper">
						<div class="modal-text">
							<p style="text-align:center; font-size:16px;">Você deseja realmente sair?</p>
						</div>
					</div>

            </div>
             
            <div class="modal-footer">
                <div class="row">
			        <div class="col-md-12 text-right">                   
                        <button type="button" class="btn btn-primary" onClick="Logout()">Confirmar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    </div>
		        </div>

              </div>        
          
        </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- ==== Modal Aniversários ==== -->
<div id="ModalAniversarios" class="modal fade" tabindex="-1" role="dialog">
                      <!-- Adiciona o tamanho do modal -->
  <div class="modal-block  modal-block-lg" role="document">
    <div class="modal-content">         

            <form id="FormAniversarios"  novalidate="novalidate">

              <div class="modal-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="panel-title">Aniversariantes da semana</h4>
              </div>    
            
            <div class="modal-body">                     

      			    <div class="modal-wrapper">
                  <div class="modal-text listaAniversarios">
                    
                  </div>
                </div>

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



