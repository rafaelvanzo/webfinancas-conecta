
<!-- ==== Modal Create Evento ==== -->
<div id="ModalCreate" class="modal fade"  role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-lg" role="document">


    <div class="modal-content">         


             <form id="FormCreate" action="" data-action-create="Pacientes/Create" data-action-details="Pacientes/Details" data-action-edit="Pacientes/Edit" data-msg-sucesso="Paciente cadastrado com sucesso." novalidate="novalidate" enctype="multipart/form-data">

              
				<div class="panel-heading">

                    <button type="button" class="close LimparForm Cancelar" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h4 class="panel-title">Pacientes</h4>

              </div>    


              	<div class="panel-body"> 

                  <div class="form-group">
                    
                    <div class="col-sm-9">                            
                                
                            <label class="control-label">Nome <span class="required" aria-required="true">*</span></label>

                            <input type="text" name="nome" class="form-control" placeholder="Nome completo" required="" aria-required="true" >

                            <input type="hidden" name="tp_favorecido" value="1" >

                        </div> 

                        <div class="col-sm-3">                            
                                
                            <label class="control-label">Data nascimento </label>

                            <input type="text" name="dtNascimento" class="form-control Data" placeholder="__/__/____" value="" data-plugin-datepicker data-plugin-masked-input data-input-mask="99/99/9999" >

                        </div> 
                    
                    </div>

                  <div class="form-group">	


                        <div class="col-sm-8">                            
                                
                            <label class="control-label">E-mail </label>

                            <input type="email" name="email" class="form-control " placeholder="E-mail" >

                        </div>                         

                        <div class="col-sm-4">                            
                                
                            <label class="control-label">CPF <span class="required" aria-required="true">*</span></label>
                            
                            <input type="text" name="cpf_cnpj" class="form-control cpf_cnpj" placeholder="___.___.___-__" required="" aria-required="true" data-plugin-masked-input data-input-mask="999.999.999-99" autocomplete="off">

                            <input type="hidden" name="inscricao" value="cpf" >

                        </div> 
                        

                    </div> 	          

                    <div class="form-group">	

                        <div class="col-sm-6">                            
                                
                            <label class="control-label">Rua </label>

                            <input type="text" name="logradouro" class="form-control" placeholder="Logradouro"  >

                        </div>  

                        <div class="col-sm-3">                            
                                
                            <label class="control-label">Número </label>

                            <input type="text" name="numero" class="form-control" placeholder="número" >

                        </div>  

                         <div class="col-sm-3">                            
                                
                            <label class="control-label">Complemento </label>

                            <input type="text" name="complemento" class="form-control" placeholder="complemento" >

                        </div>                   

                    </div>

                    <div class="form-group">	

                        <div class="col-sm-6">                            
                                
                            <label class="control-label">Cidade </label>

                            <input type="text" name="cidade" class="form-control"  aria-required="true" >

                        </div>  

                        <div class="col-sm-3">                            
                                
                            <label class="control-label">UF </label>

                            <select type="text" name="uf" class="form-control" >
                                <option value="AC">AC</option><option value="AL">AL</option><option value="AM">AM</option><option value="AP">AP</option><option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option><option value="MA">MA</option><option value="MG">MG</option><option value="MS">MS</option><option value="MT">MT</option><option value="PA">PA</option><option value="PB">PB</option><option value="PE">PE</option><option value="PI">PI</option><option value="PR">PR</option><option value="RJ">RJ</option><option value="RN">RN</option><option value="RO">RO</option><option value="RR">RR</option><option value="RS">RS</option><option value="SC">SC</option><option value="SE">SE</option><option value="SP">SP</option><option value="TO">TO</option>
                            </select>

                        </div>  

                         <div class="col-sm-3">                            
                                
                            <label class="control-label">CEP </label>

                            <input type="text" name="cep" class="form-control" placeholder="_____-___"  data-plugin-masked-input data-input-mask="99999-999">

                        </div>                   

                    </div>

                    <div class="form-group">	

                        <div class="col-sm-6">                            
                                
                            <label class="control-label">Telefone </label>

                            <input type="text" name="telefone" class="form-control" placeholder="(__) ____-____" data-plugin-masked-input data-input-mask="(99) 9999-9999">

                        </div>  

                        <div class="col-sm-6">                            
                                
                            <label class="control-label">Celular </label>

                            <input type="text" name="celular" class="form-control"  placeholder="(__) ____-____" data-plugin-masked-input data-input-mask="(99) 99999-9999">

                        </div> 

                    </div>

                     <div class="form-group">	

                        <div class="col-sm-12">
                                
                            <label class="control-label">Observação </label>
                    
                            <textarea name="Observacao" class="form-control"></textarea>
                        
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
<div id="ModalDelete" class="modal fade" tabindex="-1" role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-sm" role="document">


    <div class="modal-content">         


            <form id="FormDelete" action="" data-action-delete="Pacientes/Delete" data-msg-sucesso="Cadastro excluído com sucesso." novalidate="novalidate">


                <div class="modal-heading">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h4 class="panel-title">Excluir</h4>

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

                                    <button type="button" class="btn btn-primary SalvarForm ExcluirConfirm" >Confirmar</button>

                                    <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    
                    </div>

                 </div>

              </div>        

          
        </form>


    </div><!-- /.modal-content -->


  </div><!-- /.modal-dialog -->


</div><!-- /.modal -->

