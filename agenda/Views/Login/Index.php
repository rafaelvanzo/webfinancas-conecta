        <!-- start: page -->
        <style>
        body{
            /*background: url("assets/images/bg_login.jpg");*/
			background-color: #fff;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer{
            background:#ffffff;
        }
        </style>
<?php
if($params['Id'] !== '1'){
?>
		<section class="body-sign">


        	<div class="modal fade in" id="myModalLogin" tabindex="-1" role="dialog" scrool=no aria-labelledby="myModalLabel" aria-hidden="true" style="display:block; overflow-y:auto;">
				<div class="modal-block modal-block-sm">
					<div class="modal-content">
 <?php
}
 ?>
                      <div class="modal-header">
                        <h1 class="modal-title" id="myModalLabel" align="center"><img src="Assets/images/<?php echo $Config["Login"]["logo"];?>" /> </h4>
                      </div>
                      
                      <div class="modal-body">
                        
                            <form id="Login" >

                            <div class="alert alert-warning hidden">
							    <p class="m-none text-weight-semibold h6 instrucoes">Preencha o e-mail de cadastro para receber uma nova senha!</p>
						    </div>

							    <div class="form-group mb-lg">
								    <label>E-mail</label>
								    <div class="input-group input-group-icon">
									    <input name="email" type="text" class="form-control input-lg email" required/>
									    <span class="input-group-addon">
										    <span class="icon icon-lg">
											    <i class="fa fa-user"></i>
										    </span>
									    </span>
								    </div>
							    </div>

							    <div class="form-group mb-lg Senha">
								    <div class="clearfix">
									    <label class="pull-left">Senha</label>
									
								    </div>
								    <div class="input-group input-group-icon">
									    <input name="senha" type="password" class="form-control input-lg" required/>
									    <span class="input-group-addon">
										    <span class="icon icon-lg">
											    <i class="fa fa-lock"></i>
										    </span>
									    </span>
								    </div>
							    </div>
                                                   
                          </div>
                      
                          <div class="modal-footer">
                      		    <div class="row">
								    <div class="col-sm-6 text-left" >
										    <a href="javascript://" for="RememberMe" class="recuperarSenha">Esqueceu a senha?</a>
                                            <a href="javascript://" for="RememberMe" class="voltarLogin hidden">Voltar para o Login?</a>
								    </div>
								    <div class="col-sm-6 text-right">
									    <button type="button" class="btn btn-primary Login" onClick="Logar('Login');">Entrar</button>
									    <button type="button" class="btn btn-primary hidden resetarSenha" onClick="RecuperarSenha('Login');">Solicitar senha</button>
								    </div>
							    </div>
                          </div>
                    
                       </form>

                  </div>

<?php
if($params['Id'] !== '1'){
?>
                  
				</div>

                <p align="center" style="font-size:11px; color:#808080; ">Desenvolvido por Web 2 Business.</p>

			</div>
 
      <!-- =========================== Modal ============================== -->  


            

<!-- Mascará fade in background -> <div class="modal-backdrop fade in"></div>-->

</section>
<!-- end: page -->

<?php
}
?>