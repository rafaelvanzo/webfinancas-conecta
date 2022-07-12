<!-- ============================================= -->
<!-- ------------------ USUÁRIOS ----------------- -->
<!-- ============================================= -->  

<div id="ModalUsuarios" class="modal fade" tabindex="-1" role="dialog">
                      
  <div class="modal-dialog modal-block modal-block-md" role="document">

    <div class="modal-content">         

            <form id="FormUsuario" action="" data-action-create="Configuracoes/CreateUsuarios" data-action-details="Configuracoes/DetailsUsuarios" data-action-edit="Configuracoes/EditUsuarios" data-msg-sucesso="Cadastro realizado com sucesso." novalidate="novalidate">

            <input type="hidden" name="Tipo" value="1">


              <div class="modal-heading">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="panel-title">Gerenciar usuários</h4>

              </div>    
            
            <div class="modal-body">
   

							<div class="form-group">

               	<div class="col-sm-12">

                        <label class="control-label">Nome <span class="required" aria-required="true">*</span></label>

                        <input type="text" name="nome" class="form-control" placeholder="Nome" required="" aria-required="true" >

            </div>              

        </div>

        <div class="form-group">

                    <div class="col-sm-6">

                                <label class="control-label">E-mail <span class="required" aria-required="true">*</span></label>

                                <input type="email" name="email" class="form-control" placeholder="E-mail" required="" aria-required="true" >

                      </div>


                    <div class="col-sm-6">

                                <label class="control-label">Senha <span class="required" aria-required="true">*</span></label>

                                <input type="text" name="senhaShow" class="form-control" placeholder="Senha" required="" aria-required="true" >

                      </div>

                </div>               

          </div>      

					<div class="modal-footer">

                            <div class="row">
                    
                                    <div class="col-md-12 text-right">                   
                        
                                            <button type="button" class="btn btn-primary SalvarForm" >Salvar</button>
            
                                            <button type="button" class="btn btn-default LimparForm Cancelar" data-dismiss="modal">Cancelar</button>
                        
                                    </div>
                    
                        </div>

              	</div>              
          
        </form>


    </div><!-- /.modal-content -->
  
	</div><!-- /.modal-dialog -->

</div><!-- /.modal -->


<!-- ==== Modal Delete ==== -->
<div id="ModalDeleteUsuarios" class="modal fade" tabindex="-1" role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-sm" role="document">


    <div class="modal-content">         


            <form id="FormDelete" action="" data-action-delete="Configuracoes/DeleteUsuarios" data-msg-sucesso="Cadastro excluído com sucesso." novalidate="novalidate">


            <div class="modal-heading">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="panel-title">Excluir usuários</h4>

            </div>    


            <div class="modal-body">                     

            <div class="modal-wrapper">

                    <div class="modal-text">

                        <p>Você deseja excluir o registro <b class="NomeRegistro" style="font-size:14px;"></b> ?</p>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

            <div class="row">

                    <div class="col-md-12 text-right">                   

                                    <button type="button" class="btn btn-primary SalvarForm" >Confirmar</button>

                                    <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    
                    </div>

            </div>

            </div>        

          
        </form>


    </div><!-- /.modal-content -->


  </div><!-- /.modal-dialog -->


</div><!-- /.modal -->




<!-- =========================================== -->
<!-- ------------------ DOUTOR ----------------- -->
<!-- =========================================== -->  


<div id="ModalDoutor" class="modal fade" tabindex="-1" role="dialog">
                      
  <div class="modal-dialog modal-block modal-block-md" role="document">

    <div class="modal-content">         

            <form id="FormDoutor" action="" data-action-create="Configuracoes/CreateUsuarios" data-action-details="Configuracoes/DetailsUsuarios" data-action-edit="Configuracoes/EditUsuarios" data-msg-sucesso="Categoria alterada com sucesso." novalidate="novalidate">

                <input type="hidden" name="Tipo" value="2">

              <div class="modal-heading">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="panel-title">Gerenciar doutores</h4>

              </div>    
            
            <div class="modal-body">
   

							<div class="form-group">

               	<div class="col-sm-8">

                                    <label class="control-label">Nome <span class="required" aria-required="true">*</span></label>

                                    <input type="text" name="nome" class="form-control" placeholder="Nome" required="" aria-required="true" >

                        </div>  

                <div class="col-sm-4">		

                                     <label class="control-label">Cor na agenda <span class="required" aria-required="true">*</span></label>								
                                  
                                    <input type="color" name="color" class="form-control" value="#0088cc">

                    </div>

                    </div>

                    <div class="form-group">

                        <div class="col-sm-6">

                                    <label class="control-label">E-mail <span class="required" aria-required="true">*</span></label>

                                    <input type="email" name="email" class="form-control" placeholder="E-mail" required="" aria-required="true" >

                    </div>


                        <div class="col-sm-6">

                                    <label class="control-label">Senha <span class="required" aria-required="true">*</span></label>

                                    <input type="text" name="SenhaShow" class="form-control" placeholder="Senha" required="" aria-required="true" >

                    </div>

                </div>               

          </div>      

					<div class="modal-footer">

                    <div class="row">
            
                            <div class="col-md-12 text-right">                   
                
                                                <button type="button" class="btn btn-primary SalvarForm" >Salvar</button>
                
                                                <button type="button" class="btn btn-default LimparForm Cancelar" data-dismiss="modal">Cancelar</button>
                
                            </div>
            
                </div>

              	</div>              
          
        </form>


    </div><!-- /.modal-content -->
  
	</div><!-- /.modal-dialog -->

</div><!-- /.modal -->



<!-- ============================================== -->
<!-- ------------------ CONSULTAS ----------------- -->
<!-- ============================================== -->  



<div id="ModalConsultas" class="modal fade" tabindex="-1" role="dialog">
                      
  <div class="modal-dialog modal-block modal-block-md" role="document">

    <div class="modal-content">         

            <form id="FormGerenciarCategoria" action="" data-action-create="Configuracoes/CreateConsultas" data-action-details="Configuracoes/DetailsConsultas" data-action-edit="Configuracoes/EditConsultas" data-msg-sucesso="Categoria alterada com sucesso." novalidate="novalidate">

              <div class="modal-heading">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="panel-title">Cadastro consultas e procedimentos</h4>

              </div>    
            
            <div class="modal-body">
   

			<div class="form-group">

               	<div class="col-sm-8">

                                <label class="control-label">Descrição <span class="required" aria-required="true">*</span></label>

                                <input type="text" name="Descricao" class="form-control" placeholder="Descrição" required="" aria-required="true" >

                            </div>  

                <div class="col-sm-4">

                            <label class="control-label">Tipo <span class="required" aria-required="true">*</span></label>

                            <select name="Tipo" class="form-control">
                                <option value="1">Consulta</option>
                                <option value="2">Procedimento</option>
                            </select>

                        </div>

                </div>               

                <div class="form-group">

                        <div class="col-sm-6">

                            <label class="control-label">Intervalo de tempo <span class="required" aria-required="true">*</span></label>
														
                            <div class="input-group">												 
                              <input type="number" name="Tempo" class="form-control" placeholder="Intervalo de tempo" required="" aria-required="true" maxlength="3" value="20">                             
                              <span class="input-group-addon">
                                Minutos
                              </span>                            
                            </div>

                        </div>


                        <div class="col-sm-6">
                            
                            <label class="control-label">Valor <span class="required" aria-required="true">*</span></label>

                                <input type="text" name="Valor" class="form-control valor money text-right" placeholder="0,00" required="" aria-required="true" >

                        </div>

                </div>        

          </div>      

					<div class="modal-footer">

                    <div class="row">

                            <div class="col-md-12 text-right">                   

                                    <button type="button" class="btn btn-primary SalvarForm" >Salvar</button>

                                    <button type="button" class="btn btn-default LimparForm Cancelar" data-dismiss="modal">Cancelar</button>

                            </div>

                    </div>

              	</div>              
          
        </form>


    </div><!-- /.modal-content -->
  
	</div><!-- /.modal-dialog -->

</div><!-- /.modal -->


<!-- ==== Modal Delete ==== -->
<div id="ModalDeleteConsultas" class="modal fade" tabindex="-1" role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-sm" role="document">


    <div class="modal-content">         


            <form id="FormDeleteConsultas" action="" data-action-delete="Configuracoes/DeleteConsultas" data-msg-sucesso="Cadastro excluído com sucesso." novalidate="novalidate">


            <div class="modal-heading">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="panel-title">Excluir consultas / procedimentos</h4>

            </div>    


            <div class="modal-body">                     

            <div class="modal-wrapper">

                    <div class="modal-text">

                        <p>Você deseja excluir o registro <b class="NomeRegistro" style="font-size:14px;"></b> ?</p>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

            <div class="row">

                    <div class="col-md-12 text-right">                   

                                    <button type="button" class="btn btn-primary SalvarForm" >Confirmar</button>

                                    <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    
                    </div>

            </div>

            </div>        

          
        </form>


    </div><!-- /.modal-content -->


  </div><!-- /.modal-dialog -->


</div><!-- /.modal -->

