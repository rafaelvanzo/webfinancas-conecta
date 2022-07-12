
<style type="text/css"> .modal-open .select2-container--open { z-index: 999999 !important; width:100% !important; } </style>

<!-- ==== Modal Create Evento ==== -->
<div id="ModalAddEvento" class="modal fade"  role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-lg" role="document">


    <div class="modal-content">         


             <form id="FormAddevento" action="" data-action-create="Calendario/Create" data-action-details="Calendario/Details" data-action-edit="Calendario/Edit" data-msg-sucesso="Agendamento realizado com sucesso." novalidate="novalidate" enctype="multipart/form-data">
            
                <input type="hidden" name="Id" class="idConsulta">
              
				<div class="panel-heading">

                    <button type="button" class="close LimparForm Cancelar" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h4 class="panel-title">Agendamento</h4>

              </div>    


              	<div class="panel-body"> 


                  <div class="form-group">	


                        <div class="col-sm-12">
                            
                                
                            <label class="control-label">Situação <span class="required" aria-required="true">*</span></label>

                            <select name="Situacao" class="form-control situacao-change" required="" aria-required="true" >
                                <option value="0">Aguardando</option>
                                <option value="1">Atendido</option>
                                <option value="2">Faltou</option>
                                <option value="3">Reagendada</option>
                            </select>

                        </div>  
                
                </div>

                   <div class="form-group">	

                        <div class="col-sm-8">
                                
                                <label class="control-label">Doutor <span class="required" aria-required="true">*</span></label>
                        
                                <select name="IdDoutor" class="changeHorario selectTwo idDoutor" data-plugin-selectTwo required="" aria-required="true" data-select-url="Calendario/Doutor"></select>                            
                            
                            </div>  

                        <div class="col-sm-4">
                                
                            <label class="control-label">Tipo de pgto <span class="required" aria-required="true">*</span></label>
                    
                            <select  name="TipoPlano" class="form-control">
                                <option value="1">Particular</option>
                                <option value="2">Plano de saúde</option>                                
                            </select>        
                        
                        </div>                        

                    </div>


                  <div class="form-group">	


                        <div class="col-sm-12">
                            
                                
                            <label class="control-label">Consulta/Procedimento <span class="required" aria-required="true">*</span></label>
                    
                             <select name="IdConsulta" class="changeHorario selectTwo procedimento" data-plugin-selectTwo required="" aria-required="true" data-select-url="Calendario/configConsultaProc">
     
                             </select>
                        
                        </div>  

                </div>

                <div class="form-group">	

                        <div class="col-sm-4">
                                
                                <label class="control-label">Data <span class="required" aria-required="true">*</span></label>
                               
                                <input type="text" name="Data" class="changeHorario form-control Data" placeholder="__/__/____" required="" aria-required="true" value="" data-plugin-datepicker data-plugin-masked-input data-input-mask="99/99/9999">
                            
                            </div>  
    
                            <div class="col-sm-4">
                                    
                                <label class="control-label">horário <span class="required" aria-required="true">*</span></label>
                        
                                <select name="Horario" class="form-control horario" required="" aria-required="true" ></select>
                            
                            </div>  

                             <div class="col-sm-4">
                                    
                                <label class="control-label">Valor <span class="required" aria-required="true">*</span></label>
                        
                                <input type="text" name="Valor" class="form-control text-right money" required="" aria-required="true" ></select>
                            
                            </div> 

                    </div> 	          

                    <div class="form-group">	

                        <div class="col-sm-12">
                                
                            <label class="control-label">Paciente <span class="required" aria-required="true">*</span></label>                    

                                <select name="IdFavorecido" class="selectTwo situacao-change-favorecido" data-plugin-selectTwo required="" aria-required="true" data-select-url="Calendario/Favorecidos" style="width:100%"></select>
                        
                        </div>  

                       
                    
                    </div>      

                    <div class="form-group">	

                        <div class="col-sm-12">
                                
                            <label class="control-label">Responsável / Convênio </label>
                                
                                <select name="IdResponsavel" class="selectTwo situacao-change-responsavel" data-plugin-selectTwo data-select-url="Calendario/Favorecidos" style="width:100%"></select>

                        </div>                         

                    </div>
		

                     <div class="form-group">	

                        <div class="col-sm-12">
                                
                            <label class="control-label">Observação </label>
                    
                            <textarea name="Observacao" class="form-control" rows="5"></textarea>
                        
                        </div>  

                    </div>


                    <div class="form-group">	

                        <div class="col-sm-12">
                                
                            <label class="control-label"></label>

                                <?php if($_SESSION['Tipo'] != 2){ ?>

                                    <a href="javscript://" class="mb-xs mt-xs mr-xs btn btn-primary btn-block AddPaciente" >Add paciente / responsável</a> 
                                    
                                <?php } ?>
                        </div>                         

                    </div>


                </div>
                                
            <div class="modal-footer">

              	<div class="row">

                        <div class="col-md-6 text-left">            

                                <?php if($_SESSION['Tipo'] != 2){ ?>
                                       
                                        <button type="button" class="btn btn-danger ExcluirAgendamento" data-delete-id="" data-delete-nome="">Excluir</button>

                                       <!-- <a href="https://api.whatsapp.com/send?phone=" target="_blank" class="btn btn-success" >Enviar Whatsapp</a> -->
                                <?php } ?>                                        
                                        
                        </div>

                        <div class="col-md-6 text-right">                   

                             <?php if($_SESSION['Tipo'] != 2){ ?>

                                        <button type="button" class="btn btn-primary SalvarForm" >Salvar</button>
                             <?php } ?>

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


            <form id="FormDelete" action="" data-action-delete="Calendario/Delete" data-msg-sucesso="Cadastro excluído com sucesso." novalidate="novalidate">


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

                                    <button type="button" class="btn btn-primary SalvarForm ExcluirConfirm" data-form-limpar="false">Confirmar</button>

                                    <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    
                    </div>

                 </div>

              </div>        

          
        </form>


    </div><!-- /.modal-content -->


  </div><!-- /.modal-dialog -->


</div><!-- /.modal -->



<!-- ==== Modal Pacientes ==== -->
<div id="ModalCreate" class="modal fade"  role="dialog">

	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-lg" role="document">


    <div class="modal-content">         


             <form id="FormCreate" action="Pacientes/Create"  data-msg-sucesso="Paciente cadastrado com sucesso." novalidate="novalidate" enctype="multipart/form-data">

              
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

                            <input type="email" name="email" class="form-control" placeholder="Nome completo" >

                        </div>  

                        <div class="col-sm-4">                            
                                
                            <label class="control-label">CPF </label>
                            
                            <input type="text" name="cpf_cnpj" class="form-control cpf_cnpj" placeholder="___.___.___-__" data-plugin-masked-input data-input-mask="999.999.999-99">

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

                            <select type="text" name="uf" class="form-control"  required="" aria-required="true" >
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

                                        <button type="button" class="btn btn-primary" onClick="addPaciente();" >Salvar</button>

                                        <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                        
                        </div>

                    </div>

              </div>        

          
        </form>


    </div><!-- /.modal-content -->


  </div><!-- /.modal-dialog -->


</div><!-- /.modal -->






<!-- ==== Modal Pacientes ==== -->
<div id="ModalCPf" class="modal fade"  role="dialog">
	<!-- Adiciona o tamanho do modal -->
  <div class="modal-dialog modal-block modal-block-sm" role="document">

    <div class="modal-content">  

             <form id="FormCpf" action="Pacientes/AtualizarCPF"  data-msg-sucesso="Cpf atualizado com sucesso." novalidate="novalidate" enctype="multipart/form-data">
              
				<div class="panel-heading">

                    <button type="button" class="close LimparForm Cancelar" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h4 class="panel-title">Preenchimento de CPF</h4>

              </div>    


              	<div class="panel-body"> 

                  <div class="form-group">	

                        <div class="col-sm-12 showFavorecido">                            
                                
                            <label class="control-label">CPF de <span class="nomeFav"></span> ( Paciente )</label>
                            
                            <input type="text" name="favorecido_cpf" class="form-control cpf_cnpj favorecido_cpf" placeholder="___.___.___-__" data-plugin-masked-input data-input-mask="999.999.999-99"  required="" aria-required="true">

                            <input type="hidden" name="favorecido_id" class="fav_id" value="" >

                        </div> 
                        

                    </div> 	          


                    <div class="form-group showResponsavel">	

                        <div class="col-sm-12">                            
                                
                            <label class="control-label">CPF de <span class="nomeResp"></span> ( Responsável ) </label>
                            
                            <input type="text" name="responsavel_cpf" class="form-control cpf_cnpj responsavel_cpf" placeholder="___.___.___-__" data-plugin-masked-input data-input-mask="999.999.999-99"  required="" aria-required="true">

                            <input type="hidden" name="responsavel_id" class="resp_id" value="" >

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

