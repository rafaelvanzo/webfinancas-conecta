<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Faturas</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs -->
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="javascript://" style="cursor: default;">Minha Conta</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Faturas</a>
                 </li>
            </ul>
	</div>  
</div> <!-- Fim Breadcrumbs -->


    
 <!--   <div class="line"></div>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
        <!-- Notifications 
        <div class="nNote nWarning hideit" style="display:none;">
            <p></p>
        </div>
        <div class="nNote nInformation hideit" style="display:none;">
            <p></p>
        </div>   
        <div class="nNote nSuccess hideit" style="display:none;">
            <p></p>
        </div>  
        <div class="nNote nFailure hideit" style="display:none;">
            <p></p>
        </div>-->
    		
        <!-- =================== Palco =================== -->
 <!-- Organiza o layout -->   
 <div class="fluid">
 		<div class="span12">

			<form action="" class="form">
          <fieldset>
               
                <div class="widget">
                    <div class="title"><img src="images/icons/dark/adminUser.png" alt="" class="titleIcon"><h6>Dados do Usuário</h6></div>                   
                    	<div class="fluid">
                    
                         <div class="formRowB">
                            <span class="span6">
                                <label>Nome:</label>
                                <input style="margin-left: 0px;" type="text" name="nome" value="" class="required"/>
                            </span>
                            <span class="span3">
                                <label>Inscrição:</label>
                                <select name="inscricao">
                                  <option value="cpf">CPF</option>
                                  <option value="cnpj">CNPJ</option>
                                </select>
                            </span>
                            <span class="span3">
                                <label>CPF / CNPJ</label>
                                <input type="text" name="cpf_cnpj" value="" class="required"/>
                            </span>
                         </div>

                         <div class="formRowB"> 
                            <span class="span6">
                                <label>Logradouro:</label>
                                <input type="text" name="logradouro" value="" class=""/>
                            </span>
                            <span class="span2">
                                <label>Nº:</label>
                                <input type="text" name="numero" value="" class=""/>
                            </span>
                            <span class="span4">
                             <label>Complemento:</label>
                                <input type="text" name="complemento" value="" class=""/>
                            </span>
                            
                        </div>
                        
                          <div class="formRowB"> 
                            <span class="span4">
                                <label>Bairro:</label>
                                <input type="text" name="bairro" value="" class=""/>
                            </span>
                            <span class="span4">
                                <label>Cidade:</label>
                                <input type="text" name="cidade" value="" class=""/>
                            </span>
                            <span class="span2">
                               <label>UF:</label>
                                <select name="uf">
                                  <option value="ES">ES</option>
                                  <option value="RJ">RJ</option>
                                  <option value="BH">BH</option>
                                </select>
                            </span>
                            <span class="span2">
                                <label>CEP:</label>
                                <input type="text" name="cep" value="" class=""/>
                            </span>
                        </div>
                         
                         <div class="formRowB">
                           <span class="span4">
                                <label>E-mail:</label>
                                <input type="text" name="email" value="" />
                            </span>
                             <span class="span4">
                                <label>E-mail de cobrança:</label>
                                <input type="text" name="email" value="" />
                            </span>
                            <span class="span2">
                                <label>Telefone:</label>
                                <input type="text" name="telefone" value="" class="maskPhone"/>
                            </span>    
                             <span class="span2">
                                <label>Celular:</label>
                                <input type="text" name="celular" value="" class="maskPhone"/>
                            </span>
                          </div>  
                                                                         
                         <div class="formRowB" align="center">
                            <span class="span12">                                      	    
                                <a href="#" title="" class="button blueB" style="margin: 5px;" id="opener-favorecido-incluir"><span>Salvar</span></a>
                                      	    
                                <!-- <a href="#" title="" class="button redB" style="margin: 5px;" id="opener-favorecido-incluir"><span>Cancelar</span></a> -->                              
                            </span>                                                                                                    
                         </div>
  
                    </div> <!-- Fluid -->
                </div> <!-- Widget -->
           
            </fieldset>       
        </form>
	
 		 </div> 

  </div> 
  
  
</div> <!-- Fim Fluid --> 
 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
    
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 