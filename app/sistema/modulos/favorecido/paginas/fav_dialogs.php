<!--
======================================================================
IMPORTAR FAVORECIDOS
======================================================================
-->

<div id="dialog-fav-import" style="height:auto;padding:0;display:none;max" title="Importar Favorecidos">
  <div class="fluid">
    <div class="formRow" style="background-color:;">
        <span class="span12">
					<a href="modulos/favorecido/Planilha_Modelo.xls">Planilha Modelo</a>
        </span>
    </div>  
		<div id="fav_uploader"></div>
		<div id="fav_import" style="display:none">
    	<form id="form_fav_import">
      	<input type="hidden" name="funcao" value="favorecidosImportarFim"/>
        <div class="formRow" style="background-color:#FFF;height:40px;padding:8px 10px 8px 10px">
          Selecione o nome do campo para cada coluna de acordo com a planilha importada.
        </div>
        <div class="formRow" style="padding:0px;overflow-x:scroll;max-width:800px;">
          <div id="fav_import_listar">
          <!--
          <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
            <tbody>
              <tr>
                <td style="min-width:150px">
                  <select>
                    <option>Nome</option>
                    <option>CPF</option>
                    <option>Email</option>
                    <option>Telefone</option>
                  </select>
                </td>
                <td style="min-width:150px">
                  <select>
                    <option>Nome</option>
                    <option>CPF</option>
                    <option>Email</option>
                    <option>Telefone</option>
                  </select>
                </td>
                <td style="min-width:150px">
                  <select>
                    <option>Nome</option>
                    <option>CPF</option>
                    <option>Email</option>
                    <option>Telefone</option>
                  </select>
                </td>
                <td style="min-width:150px">
                  <select>
                    <option>Nome</option>
                    <option>CPF</option>
                    <option>Email</option>
                    <option>Telefone</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Fabio</td>
                <td>110.661.377-57</td>
                <td>fabio_lm@hotmail.com</td>
                <td>(27)98811-7561</td>
              </tr>
            </tbody>
          </table>
          -->
          </div>
        </div>
        <div class="formRow" style="border-bottom:solid 1px #ccc;background-color:#FFF;height:40px;padding:8px 10px 8px 10px">
          <input type="checkbox" name="removeHeaderCkb" id="removeHeaderCkb" onClick="removeHeader();"> Retirar cabeçalho da tabela.
        </div>
			</form>
		</div>
  </div><!-- Fim fluid -->
</div><!-- Fim dialog -->

<!--
======================================================================
INCLUÍR FAVORECIDOS
======================================================================
-->
<!-- Caixa de Dialogo Conta a Receber -->
<div id="dialog-message-favorecido-incluir" style="height:auto; padding:0;" title="Novo Favorecido" class="modal">

<form id="form_fav" class="dialog">
    <input type="hidden" name="funcao" value="favorecidosIncluir">

<div class="fluid">    

<!--=====================================-->
  
	    <div class="formRow">
            <span class="span4">
                <label>Nome:</label>
                <input type="text" name="nome" value="" class="required"/>
            </span>
            <span class="span2">
                <label>Inscrição:</label>
                <select name="inscricao" class="inscricao" id="inscIncluir" onchange="cpfCnpjChangeMask('inscIncluir');">
                  <option value="cpf" >CPF</option>
                  <option value="cnpj" >CNPJ</option>
                </select>
            </span>
            <span class="span3">
                <label>CPF / CNPJ</label>
                <input type="text" name="cpf_cnpj" class="cpf_cnpj maskCpf <?php if($_SESSION['carne_leao'] == 1){ echo "required";} ?>" value=""/>
            </span>
            <span class="span3">
                <label>Tipo:</label>
                <select name="tp_favorecido" class="required">
                  <option value="1">Cliente</option>
                  <option value="2">Fornecedor</option>
                  <option value="3" selected>Cliente / Fornecedor</option>
                </select>
            </span>
         </div>
         
         
         <div class="formRow">
           <span class="span4">
                <label>E-mail:</label>
                <input type="text" name="email" value="" />
            </span>
            <span class="span2">
                <label>Data Nasc.:</label>
                <input type="text" name="dtNascimento" value=""  class="maskDate"/>
            </span>  
            <span class="span3">
                <label>Telefone:</label>
                <input type="text" name="telefone" value="" class="maskPhone"/>
            </span>    
             <span class="span3">
                <label>Celular:</label>
                <input type="text" name="celular" value="" class="maskPhone"/>
            </span>
          </div>  
                      
         
         <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->    

         <div class="formRow">
            <span class="span6">
                <label>Logradouro:</label>
                <input type="text" name="logradouro" value=""/>
            </span>
            <span class="span2">
                <label>Nº:</label>
                <input type="text" name="numero" value=""/>
            </span>
            <span class="span4">
             <label>Complemento:</label>
             <input type="text" name="complemento" value=""/>
            </span>
            
        </div>
        
          <div class="formRow"> 
            <span class="span4">
                <label>Bairro:</label>
                <input type="text" name="bairro" value=""/>
            </span>
            <span class="span4">
                <label>Cidade:</label>
                <input type="text" name="cidade" value=""/>
            </span>
            <span class="span2">
               <label>UF:</label>
                <select name="uf">
                <?php 
                $m_uf = mysql_query("select uf from uf");
                while($uf = mysql_fetch_assoc($m_uf)){
                  echo "<option value=".$uf[uf].">".$uf[uf]."</option>";
                }
                ?>
                </select>
            </span>
            <span class="span2">
                <label>CEP:</label>
                <input type="text" name="cep" value="" class="maskCep"/>
            </span>
        </div>
                                         
<!--=====================================-->
 
<!--============ MAIS OPÇÕES ============-->
<div class="title closed inactive MaisOpcoes" align="left"> 
<a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

    <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
<!--=====================================-->

    <div class="formRow">
       <span class="span6 input-autocomplete-container">
            <label>Categoria (Cliente):</label>
            <input style="margin-left: 0px;" type="text" name="form_fav_pl_conta_id_01" id="form_fav_pl_conta_buscar_01" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
            <input type="hidden" name="cliente_plc_id" id="form_fav_pl_conta_id_01" value="0"/>
        </span>
        <span class="span6 input-autocomplete-container">
            <label>Centro de custo (Cliente):</label>
            <input style="margin-left: 0px;" type="text" name="form_fav_ct_resp_id_01" id="form_fav_ct_resp_buscar_01" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
            <input type="hidden" name="cliente_ctr_id" id="form_fav_ct_resp_id_01" value="0"/>
        </span>
    </div>   

    <div class="formRow">
       <span class="span6 input-autocomplete-container">
            <label>Categoria (Fornecedor):</label>
            <input style="margin-left: 0px;" type="text" name="form_fav_pl_conta_id_02" id="form_fav_pl_conta_buscar_02" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
            <input type="hidden" name="fornecedor_plc_id" id="form_fav_pl_conta_id_02" value="0"/>
        </span>
        <span class="span6 input-autocomplete-container">
            <label>Centro de custo (Fornecedor):</label>
            <input style="margin-left: 0px;" type="text" name="form_fav_ct_resp_id_02" id="form_fav_ct_resp_buscar_02" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
            <input type="hidden" name="fornecedor_ctr_id" id="form_fav_ct_resp_id_02" value="0"/>
        </span>
    </div>   

    <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->    

		<div class="formRow">

            <span class="span6 input-autocomplete-container">
                <label>Banco:</label>
                <input type="text" style="margin-left: 0px;"  name="bancos_buscar" value="" class="bancos_buscar_id ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                <input type="hidden" name="banco_id" value="" id="bancos_buscar">
            </span>
            <span class="span2">
                <label>Agência:</label>
                <input type="text" name="ag" value="" class=""/>
            </span>
            <span class="span2">
               <label>Conta:</label>
                <input type="text" name="conta" value="" class=""/>
            </span>
            <span class="span2">
               <label>Tipo conta:</label>
                <select name="tp_conta">
                  <option value="cc">CC</option>
                  <option value="pp">Poup.</option>
                </select>
            </span>
        </div>
         <div class="formRow">
            <span class="span12">
              <label>Observação:</label>
               <textarea name="observacao" rows="3" cols="auto"></textarea> 
            </span>                                                                                                    
         </div>
       
<!--=====================================-->                       
                           
    <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->             
                 </div>    <!-- Fim do Body Mais Opções -->                      
                 
          </div>  <!-- fluid -->                 
  </form>            
</div><!-- Fim dialog --> 

<!--
======================================================================
EDITAR FAVORECIDOS
======================================================================
-->

<div id="dialog-message-favorecido-editar" style="height:auto; padding:0;" title="Editar Favorecido" class="modal">
  
  <form id="form_fav_editar" class="dialog">
      <input type="hidden" name="funcao" value="favorecidosEditar">
      <input type="hidden" name="favorecido_id" value="">

                       <div class="fluid">      

                                <div class="formRow">
                                    <span class="span4">
                                        <label>Nome:</label>
                                        <input style="margin-left: 0px;" type="text" name="nome" value="" class="required"/>
                                    </span>
                                    <span class="span2">
                                        <label>Inscrição:</label>
                                        <select name="inscricao" class="inscricao" id="inscEditar" onchange="cpfCnpjChangeMask('inscEditar');">
                                          <option value="cpf" >CPF</option>
                                          <option value="cnpj" >CNPJ</option>
                                        </select>
                                    </span>
                                    <span class="span3">
                                        <label>CPF / CNPJ</label>
                                        <input type="text" name="cpf_cnpj" class="cpf_cnpj cpfCnpjValid <?php if($_SESSION['carne_leao'] == 1){ echo "required";} ?>" id="cpf_cnpj" value="" onkeypress="checkCpfCnpj();" />
                                    </span>
                                    <span class="span3">
                                        <label>Tipo:</label>
                                        <select name="tp_favorecido" class="required">
                                          <option value="1">Cliente</option>
                                          <option value="2">Fornecedor</option>
                                          <option value="3">Cliente / Fornecedor</option>
                                        </select>
                                    </span>
                                 </div>
                                                                  
                                 <div class="formRow">
                                   <span class="span4">
                                        <label>E-mail:</label>
                                        <input type="text" name="email" value="" />
                                    </span>
                                    <span class="span2">
                                        <label>Data Nasc.:</label>
                                        <input type="text" name="dtNascimento" value="" class="datepicker maskDate"/>
                                    </span> 
                                    <span class="span3">
                                        <label>Telefone:</label>
                                        <input type="text" name="telefone" value="" class="maskPhone"/>
                                    </span>    
                                     <span class="span3">
                                        <label>Celular:</label>
                                        <input type="text" name="celular" value="" class="maskPhone"/>
                                    </span>
                                  </div>  

    <div class="linha"></div>

                                 <div class="formRow"> 
                                    <span class="span6">
                                        <label>Logradouro:</label>
                                        <input type="text" name="logradouro" value=""/>
                                    </span>
                                    <span class="span2">
                                        <label>Nº:</label>
                                        <input type="text" name="numero" value=""/>
                                    </span>
                                    <span class="span4">
                                     <label>Complemento:</label>
                                        <input type="text" name="complemento" value=""/>
                                    </span>
                                    
                                </div>
                                
                                  <div class="formRow"> 
                                    <span class="span4">
                                        <label>Bairro:</label>
                                        <input type="text" name="bairro" value=""/>
                                    </span>
                                    <span class="span4">
                                        <label>Cidade:</label>
                                        <input type="text" name="cidade" value=""/>
                                    </span>
                                    <span class="span2">
                                       <label>UF:</label>
                                        <select name="uf">
                                       <?php 
                                        $m_uf = mysql_query("select uf from uf");
                                         while($uf = mysql_fetch_assoc($m_uf)){
                                          echo "<option value=".$uf[uf].">".$uf[uf]."</option>";
                                         }
                                       ?> 
                                        </select>
                                    </span>
                                    <span class="span2">
                                        <label>CEP:</label>
                                        <input type="text" name="cep" value="" class="maskCep"/>
                                    </span>
                                </div>

<!--=====================================-->
 
<!--============ MAIS OPÇÕES ============-->
<div class="title closed inactive MaisOpcoes" align="left"> 
<a href="#" class="button buttonMaisOpcoes"><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span>Mais Opções </span></a>  </div>

    <div class="body" style="display: block;"> <!-- Body Mais Opções -->
    		
 		<!--=====================================-->
      <div class="formRow">
         <span class="span6 input-autocomplete-container">
              <label>Categoria (Cliente):</label>
              <input style="margin-left: 0px;" type="text" name="form_fav_editar_pl_conta_id_01" id="form_fav_editar_pl_conta_buscar_01" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
              <input type="hidden" name="cliente_plc_id" id="form_fav_editar_pl_conta_id_01" value="0"/>
          </span>
          <span class="span6 input-autocomplete-container">
              <label>Centro de custo (Cliente):</label>
              <input style="margin-left: 0px;" type="text" name="form_fav_editar_ct_resp_id_01" id="form_fav_editar_ct_resp_buscar_01" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
              <input type="hidden" name="cliente_ctr_id" id="form_fav_editar_ct_resp_id_01" value="0"/>
          </span>
      </div>   
  
      <div class="formRow">
         <span class="span6 input-autocomplete-container">
              <label>Categoria (Fornecedor):</label>
              <input style="margin-left: 0px;" type="text" name="form_fav_editar_pl_conta_id_02" id="form_fav_editar_pl_conta_buscar_02" value="" class="plano_contas_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
              <input type="hidden" name="fornecedor_plc_id" id="form_fav_editar_pl_conta_id_02" value="0"/>
          </span>
          <span class="span6 input-autocomplete-container">
              <label>Centro de custo (Fornecedor):</label>
              <input style="margin-left: 0px;" type="text" name="form_fav_editar_ct_resp_id_02" id="form_fav_editar_ct_resp_buscar_02" value="" class="centro_resp_buscar input-buscar" placeholder="Preencha para localizar..." size=""/>
              <input type="hidden" name="fornecedor_ctr_id" id="form_fav_editar_ct_resp_id_02" value="0"/>
          </span>
      </div>   

      <div class="linha"></div>

      <div class="formRow">
        <span class="span6 input-autocomplete-container">
            <label>Banco:</label>
            <input type="text" style="margin-left: 0px;"  name="bancos_buscar_editar" value="" class="bancos_buscar_id ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
            <input type="hidden" name="banco_id" value="" id="bancos_buscar_editar">
        </span>
        <span class="span2">
            <label>Agência:</label>
            <input type="text" name="ag" value="" class=""/>
        </span>
        <span class="span2">
           <label>Conta:</label>
            <input type="text" name="conta" value="" class=""/>
        </span>
        <span class="span2">
           <label>Tipo conta:</label>
            <select name="tp_conta">
              <option value="cc">CC</option>
              <option value="pp">Poup.</option>
            </select>
        </span>
    </div>

     <div class="formRow">
        <span class="span12">
          <label>Observação:</label>
           <textarea name="observacao" rows="3" cols="auto"></textarea> 
        </span>                                                                                                    
     </div>
  	<!--=====================================-->

    <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->

                 </div>    <!-- Fim do Body Mais Opções -->                      
                 
          </div>  <!-- fluid -->                 
  </form>                  
</div><!-- Fim dialog --> 
