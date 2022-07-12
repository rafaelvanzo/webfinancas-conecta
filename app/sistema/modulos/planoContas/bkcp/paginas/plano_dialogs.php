<!--
======================================================================
INCLUIR PLANO DE CONTAS
======================================================================
-->

   <div id="dialog-message-planoContas-incluir" style="height:auto; padding:0;" title="Nova Categoria" class="modal">
								
    <form id="form_planoContas" class="dialog">
        <input type="hidden" name="funcao" value="planoContasIncluir">
	    <input type="hidden" name="cod_conta" value="" />
  	    <input type="hidden" name="nivel" value="" />
	    <input type="hidden" name="posicao" value="" />

        <div class="fluid">
                    
                <div class="formRow">                  
                <span class="span6 input-autocomplete-container">
                    <label>Categoria Pai:</label>
                    <input type="text" name="buscar_conta_pai_id" id="nm_plc_pai" class="plano_contas_buscar_plc ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                    <input type="hidden" name="conta_pai_id" value="" id="buscar_conta_pai_id">
                </span>
                <span class="span6">
                    <label>Nome da Categoria:</label>
                    <input type="text" name="nome" value="" class="required"/>
                </span>
                <span class="span2" style="display:none">
                    <label>Código de Referência:</label>
                    <input type="text" name="cod_ref" value=""  onkeydown="Mascara(this,Integer);" onkeypress="Mascara(this,Integer)" onkeyup="Mascara(this,Integer)" />
                </span>
                </div>

                <div class="formRow">
                    <span class="span6">
                    <label>Classificação Fluxo de Caixa:</label>
                    <select name="clfc_fc">
                        <option value=""></option>
                        <option value="1">Entradas Operacionais</option>
                        <option value="2">Saídas Operacionais</option>
                        <option value="3">Investimentos</option>
                        <option value="4">Resgate de Investimentos</option>
                        <option value="5">Receitas Financeiras</option>
                        <option value="6">Financiamentos</option>
                        <option value="7">Pagamentos dos Financiamentos</option>
                        <option value="8">Despesas Financeiras</option>
                        <option value="9">Aporte dos Sócios</option>
                        <option value="10">Pagamento aos sócios</option>
                        <option value="11">Entrada de Tesouraria</option>
                        <option value="12">Saída de Tesouraria</option>
                    </select>
                    </span>
                    <span class="span6">
                    <label>Classificação DRE:</label>
                    <select name="clfc_dre">
                        <option value=""></option>
                        <option value="1">Receitas Operacionais</option>
                        <option value="2">Receitas Financeiras</option>
                        <!--<option value="3">Despesas Operacionais</option>-->
                        <option value="4">Despesas Financeiras</option>
                        <option value="5">Despesas Variáveis</option>
                        <option value="6">Despesas Fixas</option>
                        <option value="7">Custos da Produção - CP</option>
                        <option value="8">Custos da Mercadoria Vendida - CMV</option>
                        <option value="9">Custos do Serviço Prestado - CSP</option>
                        <option value="10">Impostos S/ Vendas</option>
                        <option value="11">Impostos S/ Lucro</option>
                    </select>
                    </span>
                                                                </div>

                <div class="formRow" style="display:none">
                    <span class="span6">
                        <label>Tipo de Categoria:</label>
                        <select name="tp_conta" class="required">
                        <option value="1" selected>(A) Analítico</option>
                        <option value="2">(S) Sintético</option>
                        </select>
                    </span>
                </div>
                  
                <div class="formRow"> 
                    <span class="span12">
                        <label>Descrição:</label>
                        <textarea name="descricao" cols="auto" rows="2"></textarea>
                    </span>
                </div>
                
                <?php
                if($_SESSION['carne_leao']){
                ?>
                <div class="formRow" style="margin-bottom:10px">
                    <span class="span2">
                        <label>Dedutível:</label>
                        <input type="checkbox" name="dedutivel" value="1" class="ckb-bootstrap" id="ckb-dedutivel01" />
                    </span>
                </div>
                <?php    
                }
                ?>
                
            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->                            
                 
        </div>  <!-- fluid -->                 
    </form>                  
</div><!-- Fim dialog --> 

<!--
======================================================================
EDITAR PLANO DE CONTAS
======================================================================
-->

  <div id="dialog-message-planoContas-editar" style="height:auto; padding:0;" title="Editar Categoria" class="modal">
								
        <form id="form_planoContas_editar" class="dialog">
                <input type="hidden" name="funcao" value="planoContasEditar">
                <input type="hidden" name="plano_contas_id" value="">
            <input type="hidden" name="conta_pai_id_ini" id="conta_pai_id_ini" value="">
            <input type="hidden" name="cod_ref_ini" id="cod_ref_ini" value="">
            <input type="hidden" name="nivel" value="" />
              <input type="hidden" name="posicao" value="" />

               <div class="fluid">      
                 <div class="formRow">                                                                
                  <span class="span5 input-autocomplete-container">
                     <label>Categoria Pai:</label>
                      <input type="text" name="conta_pai_id_edit" id="nm_plc_pai_edit" value="" class="plano_contas_buscar_plc ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                      <input type="hidden" name="conta_pai_id" value="" id="conta_pai_id_edit">
                  </span>
                   <span class="span5">
                      <label>Nome da Categoria:</label>
                      <input type="text" name="nome" value="" class="required"/>
                  </span>  
                  <span class="span2">
                      <label>Situação:</label>
                      <select name="situacao">
                        <option value="0">Ativo</option>
                        <option value="1">Inativo</option>
                      </select>
                  </span>
                  
               </div>

               <div class="formRow">
                 <span class="span6">
                    <label>Classificação Fluxo de Caixa:</label>
                    <select name="clfc_fc">
                      <option value=""></option>
                      <option value="1">Entradas Operacionais</option>
                      <option value="2">Saídas Operacionais</option>
                      <option value="3">Investimentos</option>
                      <option value="4">Resgate de Investimentos</option>
                      <option value="5">Receitas Financeiras</option>
                      <option value="6">Financiamentos</option>
                      <option value="7">Pagamentos dos Financiamentos</option>
                      <option value="8">Despesas Financeiras</option>
                      <option value="9">Aporte dos Sócios</option>
                      <option value="10">Pagamento aos sócios</option>
                      <option value="11">Entrada de Tesouraria</option>
                      <option value="12">Saída de Tesouraria</option>
                    </select>
                 </span>
                 <span class="span6">
                    <label>Classificação DRE:</label>
                    <select name="clfc_dre">
                      <option value=""></option>
                      <option value="1">Receita Operacional</option>
                      <option value="2">Receitas Financeiras</option>
                      <!--<option value="3">Despesa Operacional</option>-->
                      <option value="4">Despesas Financeiras</option>
                      <option value="5">Despesas Variáveis</option>
                      <option value="6">Despesas Fixas</option>
                      <option value="7">Custos da Produção - CP</option>
                      <option value="8">Custos da Mercadoria Vendida - CMV</option>
                      <option value="9">Custo do Serviço Prestado - CSP</option>
                      <option value="10">Impostos S/ Vendas</option>
                      <option value="11">Impostos S/ Lucro</option>
                    </select>
                 </span>
               </div>

               <div class="formRow" style="display:none">
                  <span class="span4">
                      <label>Código de Referência:</label>
                      <input style="margin-left: 0px;" type="text" name="cod_ref" value="" onkeydown="Mascara(this,Integer);" onkeypress="Mascara(this,Integer)" onkeyup="Mascara(this,Integer)"/>
                  </span>
                   <span class="span6">
                      <label>Tipo de Categoria:</label>
                      <select name="tp_conta">
                        <option value="1" id="tp_conta_a">(A) Analítico</option>
                        <option value="2" id="tp_conta_s">(S) Sintético</option>
                      </select>
                  </span>
              </div>
              
                <div class="formRow"> 
                  <span class="span12">
                     <label>Descrição:</label>
                     <textarea name="descricao" cols="auto" rows="2"></textarea>
                   </span>
                </div>

                <?php
                if($_SESSION['carne_leao']){
                ?>
                   <div class="formRow" id="div-ckb-dedutivel02" style="margin-bottom:10px">
                    <span class="span2">
                        <label>Dedutível:</label>
                        <input type="checkbox" name="dedutivel" value="1" class="ckb-bootstrap" id="ckb-dedutivel02" />
                    </span>
                </div>
                <?php
                }
                ?>

                <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->
                 
          </div>  <!-- fluid -->                 
  </form>                  
</div><!-- Fim dialog --> 

