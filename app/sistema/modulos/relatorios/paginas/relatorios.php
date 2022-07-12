<!-- <script> alert(window.innerWidth); </script> --> 

 		<!-- Título -->
    <div class="titleArea noPrint">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Relatórios</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>
            <!-- === Cotação === -->      
            <?php //include("modulos/cambio/paginas/cambio.php"); ?>
            <!-- === Fim Cotação === --> 
        </div>
    </div>    
    <!-- Fim título -->  
    

    <!-- Breadcrumbs -->
    <!--
    <div class="wrapper noPrint">
       <div class="bc" style="margin:2px 0 0 0;">
                <ul id="breadcrumbs" class="breadcrumbs">
                     <li class="">
                          <a href="javascript://" style="cursor: default;">Geral</a>
                     </li>
                     <li class="current">
                          <a href="javascript://" style="cursor: default;">Relatórios</a>
                     </li>
                </ul>
      </div>  
    </div> --><!-- Fim Breadcrumbs -->
    
	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
      <div class="noPrint">
      
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


      <form action="" id="formRelatorios"class="form">
       
      <fieldset>
            <div class="widget">
                <div class="title"><img src="images/icons/dark/settings2.png" alt="" class="titleIcon" /><h6>Filtro de Relatórios</h6></div>
         
            <!-- ======== -->
               
          <div class="fluid">  <!-- Fluid -->
          
           <div class="formRow">
                      
                    <span class="span4">
                    
                      <label><b>Selecione o Relatório: </b></label>
                      <div class="selecionar">
                        <!--<input type="radio" name="tpRelatorio" value="simei" class="tp_relatorio" onclick="mudarRelatorio('simei','todas');"/> <b>SIMEI</b> <br />-->
                        <!--<input type="radio" name="tpRelatorio" value="fluxoCaixa" class="tp_relatorio" onclick="mudarRelatorio('fluxoCaixa','todas');"/> <b>Fluxo de Caixa</b> <br />-->
                        <input type="radio" name="tpRelatorio" value="fluxoCaixaN" class="tp_relatorio" onclick="mudarRelatorio('fluxoCaixaN','todas');"/> <b>Fluxo de Caixa</b> <br />
                        <input type="radio" name="tpRelatorio" value="dre" class="tp_relatorio" onclick="mudarRelatorio('dre','3,4');"/> <b>DRE</b> <br />
                        <input type="radio" name="tpRelatorio" value="movimentoFinanceiro" class="tp_relatorio" onclick="mudarRelatorio('movimentacaoFinanceira','todas');"/> <b>Movimentação Financeira</b> <br />
                        <input type="radio" name="tpRelatorio" value="contasFinanceirasSaldo" class="tp_relatorio" onclick="mudarRelatorio('saldoContasFinanceiras','2,4');"/> <b>Saldo Contas Financeiras</b> <br />
                        <input type="radio" name="tpRelatorio" value="rcbts" class="tp_relatorio" onclick="mudarRelatorio('rcbts','todas');"/> <b>Recebimentos</b> <br />
                        <input type="radio" name="tpRelatorio" value="pgtos" class="tp_relatorio" onclick="mudarRelatorio('pgtos','todas');"/> <b>Pagamentos</b> <br />
                        <input type="radio" name="tpRelatorio" value="plc" class="tp_relatorio" onclick="mudarRelatorio('plc','todas');"/> <b>Categorias</b> <br />
                        <!--<input type="radio" name="tpRelatorio" value="contasFinanceirasExtrato" class="tp_relatorio" onclick="mudarRelatorio('extratoContasFinanceiras');"/> <b>Extrato Contas Financeiras</b> <br />-->
                        <input type="radio" name="tpRelatorio" value="centroResp" class="tp_relatorio" onclick="mudarRelatorio('centroResp','todas');"/> <b>Centro de Custo</b> <br />
                        <input type="radio" name="tpRelatorio" value="plcCtr" class="tp_relatorio" onclick="mudarRelatorio('plcCtr','todas');"/> <b>Categorias x Centro de Custo</b> <br />
                        <input type="radio" name="tpRelatorio" value="carneLeao" class="tp_relatorio" onclick="mudarRelatorio('carneLeao','3,4,5');"/> <b>Carnê Leão</b> <br />
                        <input type="radio" name="tpRelatorio" value="historicoLancamento" class="tp_relatorio" onclick="mudarRelatorio('historicoLancamento', 'todas');"/> <b>Histórico de Lançamentos</b> <br />
                      </div>
    
                      <br />
    
                    </span> 

                    <span class="span5">
                        <label><b>Descrição do Relatório:</b></label>
                        <div id="descricaoRelatorios" class="selecionar">
                        
                            <div class="plcDscr" style="display:none;">
                                <b>Categorias</b> <br />
                                Visualize os valores agrupados pela natureza dos lançamentos.
                            </div>
                       
                            <div class="movimentacaoFinanceiraDscr" style="display:none;">
                                <b>Movimentação Financeira</b> <br />
                                Visualize todos os lançamentos que já foram e ainda não foram Realizar.
                            </div>
                        
                            <div class="saldoContasFinanceirasDscr" style="display:none;">
                                <b>Saldo das Contas Financeiras</b> <br />
                                Visualize os saldos das Contas Financeiras.
                            </div>
                        
                            <div class="extratoContasFinanceirasDscr" style="display:none;">
                                <b>Extrato das Contas Financeiras</b> <br />
                                Visualize todos os lançamentos efetuados em suas contas financeiras.
                            </div>
                        
                            <div class="centroRespDscr" style="display:none;">
                                <b>Centro de Custo</b> <br />
                                Visualize os valores agrupados em cada Centro de Custo.
                            </div>
    
                            <div class="plcCtrDscr" style="display:none;">
                                <b>Categorias x Centro de Custo</b> <br />
                                Visualize os valores agrupados por Categorias e Centro de Custo.
                            </div>
    
                            <div class="fluxoCaixaDscr fluxoCaixaNDscr" style="display:none;">
                                <b>Fluxo de Caixa</b> <br />
                                Visualize o fluxo de receitas e despesas realizadas e previstas agrupadas por um período contínuo.
                            </div>

                            <div class="dreDscr" style="display:none;">
                                <b>DRE</b> <br />
                                Visualize o demonstrativo de resultado.
                            </div>

                            <div class="rcbtsDscr" style="display:none;">
                                <b>Recebimentos</b> <br />
                                Visualize todos os seus recebimentos dentro de um período.
                            </div>
                        
                            <div class="pgtosDscr" style="display:none;">
                                <b>Pagamentos</b> <br />
                                Visualize todos os seus pagamentos dentro de um período.
                            </div>

                            <div class="simeiDscr" style="display:none;">
                                <b>SIMEI</b> <br />
                                Visualize o resumo de pagamentos referentes ao SIMPLES do Micro Empreendedor Individual.
                            </div>

                            <div class="carneLeaoDscr" style="display:none;">
                                <b>Carnê Leão</b> <br />
                                Visualize os valores referentes ao cálculo de imposto de renda para pessoa física.
                            </div>

                            <div class="historicoLancamentoDscr" style="display:none;">
                                <b>Histórico de Lançamento</b> <br />
                                Visualize alterações realizadas em lançamentos desde sua criação. <br />
                                A última atualização do lançamento está destacada na cor cinza, sendo seguida pelas alterações anteriores. <br />
                                O marcador <span style="color:red;font-size:20px">&bull;</span> indica que o lançamento foi excluído. <br />
                                Apenas recebimentos e pagamentos podem ser visualizados.
                            </div>

                        </div> 
                        <br />
                    </span>

                    <span class="span3">
                        <span id="periodo-data">
                            <label style="width:100%; display:none" class="lblData filtro-periodo"><input type="radio" name="periodo_radio" id="cId1" value="data" style="padding-top: 25px;" /><b>Data: </b></label>
                            <input type="text" class="datepicker maskDate dt_1 filtro-periodo" placeholder="Data inicial" id="periodoDtIni" onClick="changeRadio('1');" style="display:none"/>
                            <input type="text" class="datepicker maskDate dt_2 filtro-periodo" placeholder="Data final" id="periodoDtFim" onClick="changeRadio('1');" style="display:none"/>
                            <br />
                        </span>
                        <span id="periodo-mes">
                            <label style="width:100%; display:none" class="lblMes filtro-periodo"><input type="radio" name="periodo_radio" id="cId2" value="mes"/><b>Mês: </b></label>                      
                            <input name="dt_ini_m" id="periodoMesIni" type="text" class="monthpicker datepickerM dt_3 filtro-periodo" placeholder="Início" onclick="changeRadio('2');" style="display:none" readonly/>
                            <input name="dt_fim_m" id="periodoMesFim" type="text" class="monthpicker datepickerM dt_4 filtro-periodo" placeholder="Fim" onclick="changeRadio('2');" style="display:none" readonly/>
                            <br />
                        </span>
                        <span id="periodo-ano">
                            
                            <label style="width:100%; display:none" class="lblAno filtro-periodo"><input type="radio" name="periodo_radio" id="cId3" value="ano"/><b>Ano: </b></label>                      
                            <select name="ano" id="periodoAno" class="dt_5 filtro-periodo" style="display:none" onclick="changeRadio('3');">
                                <?php
                                $anoIni = 2010;
                                $anoFim = date('Y');
                                $selected = '';
                                for($i=$anoIni;$i<=$anoFim;$i++){
                                    if($i==$anoFim)
                                        $selected = 'selected';
                                    echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                                }
                                ?>
                            </select>
                        </span>
                    </span>
                        
                  </div> 
          
               
               <div class="formRow relatorioCnfg" style="display:none" id="filtro">
               
                   <div class="fluid">
                     
                     <span class="span3 cnfg plcCnfg centroRespCnfg movimentacaoFinanceiraCnfg extratoContasFinanceirasCnfg plcCtrCnfg saldoContasFinanceirasCnfg rcbtsCnfg pgtosCnfg simeiCnfg carneLeaoCnfg historicoLancamentoCnfg">
                        <label><b>Conta Financeira:</b></label>
                        <div class="selecionar">
                          <input type="checkbox" id="contasChecarTodos"/> <b> Todas as Contas </b> <br />
                          <span id="contasFinanceiras">
                          <?php
                          $array_contas_financeiras = $db->fetch_all_array("
                            select id, descricao
                            from contas
                            order by descricao
                          ");
                          foreach($array_contas_financeiras as $conta_financeira){
                            echo '<input type="checkbox" value="'.$conta_financeira[id].'" class="contaCheckbox"/> <b>'.$conta_financeira[descricao].'</b><br />';
                          }											
                          ?>
                          </span>
                        </div>
                        <br />
                     </span>

                     <span class="span3 cnfg plcCnfg plcCtrCnfg fluxoCaixaCnfg">
                       <label><b>Categorias:</b></label>
                        <div class="selecionar">
                          <!--<input type="radio" name="nivel" value="0" checked="checked"/> Nenhum <br />-->
                          <?php 
                          $qtd_niveis = $db->fetch_assoc("select Max(nivel) as nivel from plano_contas");
                          $qtd_niveis = $qtd_niveis['nivel'];
                          $nivel = 1;
                          if($qtd_niveis>0){
                            while($nivel <= $qtd_niveis){ 
                              echo '<input type="radio" name="nivel_plc" class="nivel_plc" value="'.$nivel.'"/> Nível '.$nivel.' <br />';
                              ++ $nivel;
                            }
                          }else{
                            echo '<input type="radio" name="nivel_plc" class="nivel_plc" value="0" checked/> Nenhum <br />';
                          }
                          ?>
                        </div>
                        <br />
                     </span>
                      
                     <span class="span3 cnfg fluxoCaixaCnfg">
                       <label><b>Tipo:</b></label>
                       <div class="selecionar">
                         <input type="radio" name="tipo_fc" value="d" class="tipo_fc"/> Diário <br />
                         <input type="radio" name="tipo_fc" value="m" class="tipo_fc"/> Mensal <br />
                       </div>
                       <br />
                     </span>
    
                      <span class="span3 cnfg centroRespCnfg plcCtrCnfg">
                       <label><b>Centro de Custo:</b></label>
                        <div class="selecionar" id="selectCtrNivel">
                          <!--<input type="radio" name="nivel" value="0" checked="checked"/> Nenhum <br />-->
                          <?php
                          $qtd_niveis_ctr = $db->fetch_assoc("select Max(nivel) as nivel from centro_resp");
                          $qtd_niveis_ctr = $qtd_niveis_ctr['nivel'];
                          $nivel_ctr = 1;
                          if($qtd_niveis_ctr>0){
                            while($nivel_ctr <= $qtd_niveis_ctr){ 
                              echo '<input type="radio" name="nivel_ctr" class="nivel_ctr" value="'.$nivel_ctr.'"/> Nível '.$nivel_ctr.' <br />';
                              ++ $nivel_ctr;
                            }
                          }else{
                            echo '<input type="radio" name="nivel_ctr" class="nivel_ctr" value="0" checked/> Nenhum <br />';
                          }
                          ?>
                        </div>
                        <br />
                      </span>
                      
                      <span class="span3 cnfg plcCnfg centroRespCnfg extratoContasFinanceirasCnfg plcCtrCnfg rcbtsCnfg pgtosCnfg">
                       <label><b>Situação Lançamentos: </b></label>
                        <div class="selecionar">
                              <span class="span_lnct_sit"><input type="radio" name="situacao" value="3" class="lnct_situacao"/> Á Realizar / Realizado <br /></span>
                              <input type="radio" name="situacao" value="0" class="lnct_situacao"/> Á Realizar <br />
                              <input type="radio" name="situacao" value="1" class="lnct_situacao"/> Realizado <br />
                        </div>
                        <br />
                     </span>
    

					 <span class="span3 cnfg movimentacaoFinanceiraCnfg">
                       <label><input type="checkbox" name="dt_emissao" id="dtEmissao" value="1"> <b>Exibir por data de emissão </b></label>
                     </span>

                   </div>
                     
               </div>

               <div class="formRow cnfg fluxoCaixaCnfg fluxoCaixaNCnfg rcbtsCnfg pgtosCnfg centroRespCnfg plcCtrCnfg dreCnfg" id="filtroRow2" style="display:none;padding-bottom:20px">

                 <span class="span3 cnfg input-autocomplete-container orct_container_relatorio fluxoCaixaCnfg fluxoCaixaNCnfg">
                   <label><b>Orçamento: </b></label>
                   <input type="text" name="orct_id" id="orcamento_buscar" class="orcamentos_buscar input-buscar"/>
                   <input type="hidden" name="orcamento_id" id="orct_id" value=""/>
                 </span>
                 
                 <span class="span3 cnfg input-autocomplete-container orct_container_relatorio centroRespCnfg rcbtsCnfg pgtosCnfg plcCtrCnfg dreCnfg">
                   <label><b>Centro de Custo:</b></label>
                   <input type="text" name="ctr_id" id="centro_buscar" class="centro_resp_buscar_report input-buscar"/>
                   <input type="hidden" name="centro_id" id="ctr_id" value="0"/>
                 </span>
                
                 <span class="span3 cnfg input-autocomplete-container orct_container_relatorio rcbtsCnfg pgtosCnfg">
                   <label><b>Favorecido: </b></label>
                   <input type="text" name="favorecido_rcbt_id" id="favorecidos_buscar" class="favorecidos_buscar_report input-buscar"/>
                   <input type="hidden" name="favorecido_id" id="favorecido_rcbt_id" value=""/>
                 </span>
                   
                 <span class="span3 cnfg rcbtsCnfg pgtosCnfg">
                     <label style="width: 100%;" class="lblMes"><b>Competência: </b></label>
                     <input name="mes_comp_ini" id="mes_comp_ini" type="text" class="monthpicker datepickerM" placeholder="Início" readonly/>
                     <input name="mes_com_fim" id="mes_comp_fim" type="text" class="monthpicker datepickerM" placeholder="Fim" readonly/>
                 </span>

                 <span class="span3 cnfg fluxoCaixaNCnfg dreCnfg">
                    <label><b>Tipo:</b></label>
                    <div class="selecionar">
                        <input type="radio" name="radAnaliticoSintetico" value="sintetico" class="rad-analitico-sintetico" checked/> Sintético <br />
                        <input type="radio" name="radAnaliticoSintetico" value="analitico" class="rad-analitico-sintetico"/> Analítico <br />
                    </div>
                    <br />
                 </span>
                   
               </div>
               
               <div class="formRow cnfg" id="filtroRow3" style="display:none;padding-top:20px;padding-bottom:20px">

                   <span class="span3 cnfg">
                       <input type="checkbox" name="agpCtr" id="agp_ctr"/>
                       <label for="agp_ctr">Agrupar lançamentos por centro de custo</label>
                   </span>
                
               </div>
                
               <div class="formRow " align="center" >
                 <span class="span12">
                   <a href="javascript://void(0);" title="" class="button greenB" style="margin: 5px;" onClick="relatorioGerar('t')"><img src="images/icons/light/chart8.png" alt="" class="icon"><span>Exibir Relatório</span></a>
                   <a href="javascript://void(0);" title="" class="button blueB" style="margin: 5px;" onClick="relatorioGerar('d')"><img src="images/icons/light/download3.png" alt="" class="icon"><span>Download Relatório</span></a>
                   <a href="javascript://void(0);" title="" class="button redB" style="margin: 5px;" onclick="filtroRelatoriosLimpar('formRelatorios');"><img src="images/icons/light/close.png" alt="" class="icon"><span>Limpar Filtro</span></a>	
                 </span>
               </div> <!-- Fim formRow -->                  
              
          <!-- ======== -->      
              
              </div>  <!-- fim Fluid -->
              
          </div> <!-- Fim widget -->
        </fieldset>  
          
      </form>

		 <br /><br />
     <!-- 
      <div class="widget">
        <div class="title"><img src="images/icons/dark/frames.png" alt="" class="titleIcon" /><h6>Recebimentos</h6></div>
        <table cellpadding="0" cellspacing="0" class="display pc_cr"> 
          <thead>
            <tr bgcolor="#F0F0F0" align="center">
              <td>Favorecido</td>
              <td>Descrição</td>
              <td>Valor</td>
              <td>Vencimento</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Fabio Moreto</td>
              <td>Web Finanças - Contratação</td>
              <td>R$ 59,90</td>
              <td>19/08/2014</td>
            </tr>
          </tbody>
        </table>
      </div>
    -->
    <!--
      <div class="widget">
        <div class="title"><img src="images/icons/dark/frames.png" alt="" class="titleIcon" /><h6>Plano de Contas x Centro de Responsabilidade</h6></div>
          <table cellpadding="0" cellspacing="0" class="display pc_cr"> 
              <thead>
                  <tr>
                      <td>P.C. / C.R.</td>
                      <td><b>1 - Web Finanças</b></td>
                      <td><b>2 - Fatura Expressa</b></td>
                  </tr>
              </thead>
              <tbody>
                  <tr>
                      <td><b>1 - Ativo</b></td>
                      <td>R$ 10.000,00</td>
                      <td>R$ 10.000,00</td>                        
                  </tr>
                  <tr>
                      <td><b>2 - Passivo</b></td>
                      <td>R$ 500,00</td>
                      <td>R$ 500,00</td>                        
                  </tr>
              </tbody>
          </table>
      
      </div>
    -->
    <!--
      <div class="widget">
        <div class="title"><img src="images/icons/dark/frames.png" alt="" class="titleIcon" /><h6>Fluxo de Caixa</h6></div>
          <div style="overflow:auto;margin-left:200px;">
          <table cellpadding="0" cellspacing="0" class="display pc_cr"> 
              <thead>
                  <tr bgcolor="#F5F5F5">
                      <td style="position:absolute;left:0"><b>Saldo anterior</b> <font style="font-size:11px;">(Caixa + Bancos)</font></td>
                      <td colspan="2" align="right" valign="" style="position:static;width:600px"><b>R$ 10.000,00</b></td>
                      <td colspan="2" align="right" ><b>R$ 10.000,00</b></td>
                      <td colspan="2" align="right"><b>R$ 10.000,00</b></td>
                      <td colspan="2" align="right"><b>R$ 10.000,00</b></td>
                      <td colspan="2" align="right" ><b>R$ 10.000,00</b></td>
                      <td colspan="2" align="right"><b>R$ 10.000,00</b></td>
                      <td colspan="2" align="right"><b>R$ 10.000,00</b></td>
                  <tr bgcolor="#F0F0F0">
                      <td style="position:absolute;left:0" rowspan="2" align="center"><b>Plano de contas / Período</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Junho</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Julho</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Agosto</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Setembro</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Outubro</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Novembro</b></td>
                      <td colspan="2" align="center" style="width:300px"><b>Dezembro</b></td>
                  </tr>  
                  <tr bgcolor="#F0F0F0">
                      <td style="position:absolute;left:0" align="center"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Realizado</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Previsto / Realizado</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Previsto / Realizado</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Previsto</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Previsto</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Previsto</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Orçamento</b></td>
                      <td align="center" style="width:150px"><b>Previsto</b></td>
                  </tr>
              </thead>
              <tbody>
                  <tr bgcolor="#F5F5F5">
                      <td style="position:absolute;left:0"><b>1 - Ativo</b></td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right" >R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                  </tr>
                  <tr>
                      <td style="position:absolute;left:0"><b>1.1 - Web Finanças</b></td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right" >R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                  </tr>
                  <tr>
                      <td style="position:absolute;left:0"><b>1.2 - Hospedagem</b></td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right" >R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                  </tr>
                  <tr bgcolor="#F5F5F5">
                      <td style="position:absolute;left:0"><b>2 - Passivo</b></td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right" >R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                  </tr>
                  <tr>
                      <td style="position:absolute;left:0"><b>2.1 - Servidor</b></td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right" >R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                  </tr>
                  <tr>
                      <td style="position:absolute;left:0"><b>2.2 - Domínios</b></td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                  </tr>
                  <tr bgcolor="#F5F5F5">
                      <td style="position:absolute;left:0"><b>Saldo final</b> <font style="font-size:11px;">(Caixa + Bancos)</font></td>
                      <td colspan="2" align="right"><b>R$ 19.500,00</b></td>
                      <td colspan="2" align="right" ><b>R$ 0,00</b></td>
                      <td colspan="2" align="right"><b>R$ 0,00</b></td>
                      <td colspan="2" align="right"><b>R$ 0,00</b></td>
                      <td colspan="2" align="right"><b>R$ 0,00</b></td>
                      <td colspan="2" align="right"><b>R$ 0,00</b></td>
                      <td colspan="2" align="right"><b>R$ 0,00</b></td>
                  </tr>
              </tbody>
          </table>
        </div>
      </div>
    -->  
    <!--
      <div class="widget">
        <div class="title"><img src="images/icons/dark/frames.png" alt="" class="titleIcon" /><h6>Fluxo de Caixa</h6></div>
          <table cellpadding="0" cellspacing="0" class="display pc_cr"> 
              <thead>
                  <tr bgcolor="#F5F5F5">
                      <td><b>Saldo anterior</b> <font style="font-size:11px;">(Caixa + Bancos)</font></td>
                      <td align="right"><b>R$ 10.000,00</b></td>
                      <td align="right" ><b>R$ 10.000,00</b></td>
                      <td align="right"><b>R$ 10.000,00</b></td>
                  </tr>
                  <tr bgcolor="#F0F0F0">
                      <td rowspan="2" align="center"><b>Plano de contas / Período</b></td>
                      <td align="center"><b>01/06/2014</b></td>
                      <td align="center" ><b>02/06/2014</b></td>
                      <td align="center"><b>03/06/2014</b></td>
                  </tr>  
                  <tr bgcolor="#F0F0F0">
                      <td align="center"><b>Realizado</b></td>
                      <td align="center"><b>Previsto / Realizado</b></td>
                      <td align="center"><b>Previsto</b></td>
                  </tr>            
              </thead>
              <tbody>              
                  <tr bgcolor="#F5F5F5">
                      <td><b>1 - Ativo</b></td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                      <td align="right">R$ 10.000,00</td>
                  </tr>
                  <tr>
                      <td><b>1.1 - Web Finanças</b></td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                  </tr>
                  <tr>
                      <td><b>1.2 - Hospedagem</b></td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                      <td align="right">R$ 5.000,00</td>
                  </tr>
                  <tr bgcolor="#F5F5F5">
                      <td><b>2 - Passivo</b></td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                      <td align="right">R$ 500,00</td>
                  </tr>
                  <tr>
                      <td><b>2.1 - Servidor</b></td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                  </tr>
                  <tr>
                      <td><b>2.2 - Domínios</b></td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                      <td align="right">R$ 250,00</td>
                  </tr>
                  <tr bgcolor="#F5F5F5">
                      <td><b>Saldo final</b> <font style="font-size:11px;">(Caixa + Bancos)</font></td>
                      <td align="right"><b>R$ 19.500,00</b></td>
                      <td align="right" ><b>R$ 0,00</b></td>
                      <td align="right"><b>R$ 0,00</b></td>
                  </tr>
              </tbody>
          </table>
      
      </div>
    -->
    
    <?php
    // ======================================================================================= RELATÓRIO MENSAL ===========================================================
    
    /*
		
     	$ano = date("Y");
		
      $dt_hoje = date('Y-m-d');
      $hora_relatorio = date('H:i');
      $data_relatorio = date('d/m/Y');
      $relatorio = "";
      
      //dados para teste
      $mes_atual = date('m');
      $ano_atual = date('Y');
      $array_meses = array(6,7,8);
      $ano = 2014;
      $dt_ini = '2014-06-01';
      $dt_fim = '2014-09-01';
      //fim dados para teste
    
      $array_saldo_anterior = array();
      
      //calculo do saldo anterior
      $query_receita = '
        select sum(valor) valor
        from lancamentos
        where tipo = "R"
          and dt_compensacao < "'.$dt_ini.'"
          and compensado = 1
      ';
      $receita = $db->fetch_assoc($query_receita);
      
      $query_despesa = '
        select sum(valor) valor
        from lancamentos
        where tipo = "P"
          and dt_compensacao < "'.$dt_ini.'"
          and compensado = 1
      ';
      $despesa = $db->fetch_assoc($query_despesa);
    
      $saldo_anterior = $receita['valor'] - $despesa['valor'];
      $array_saldo_anterior[] = $saldo_anterior;
    
      //duplica o array de saldo anterior para fazer a atualização mês a mês
      //$array_saldo_final = $array_saldo_anterior;
    
      //tabela temporária para lançamentos recorrentes
      $db->query("
        CREATE TEMPORARY TABLE lancamentos_rcr_temp (
          id int(11),
          conta_id int(11),
          dt_vencimento date NOT NULL,
          valor decimal(10,2) NOT NULL,
          frequencia int(3),
          dia_mes int(1)
        ) ENGINE=MEMORY
      ");
    
      //situação dos lançamentos
      $lancamento_situacao = $array_dados['lancamento_situacao'];
      $compensado = "";
      if($lancamento_situacao==0){
        $compensado = "and l.compensado = 0";
      }elseif($lancamento_situacao==1){
        $compensado = "and l.compensado = 1";
      }
    
      //contas financeiras do relatório	
      //$array_cf_id = explode(',',$array_dados["contas_financeiras"]);
    
      //nível do plano de contas
      $nivel_plc = 2;//$array_dados['nivel_plc'];
      
      //nível do centro de responsabilidade
      $nivel_ctr = $array_dados['nivel_ctr'];
    
      //busca lançamentos recorrentes
      if($lancamento_situacao!=1){
    
          $query_lancamentos_rcr = mysql_query("
            select id 
            from lancamentos_recorrentes
            where dt_vencimento <= '".$dt_fim."'
          ");
        
          while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
         
            $lancamento_rcr = $db->fetch_assoc("
              select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
              from lancamentos_recorrentes 
              where id = ".$lancamento['id']
            );
            
            $dt_vencimento = date($lancamento_rcr['dt_vencimento']);
        
            while($dt_vencimento <= $dt_fim){
            
              if($dt_vencimento >= $dt_ini){
                $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
              }
              
              if($lancamento_rcr['frequencia']>=30){
              
                $frequencia = $lancamento_rcr['frequencia']/30;
                $dia_vencimento = $lancamento_rcr['dia_mes'];
                $dt_vencimento_atual = explode('-',$dt_vencimento);
                $mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[0]);
                $qtd_dias_mes = date('t',$mes_prox_venc);
        
                if( $qtd_dias_mes < $dia_vencimento ){
                  $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[0]);
                  $dt_vencimento = date('Y-m-d',$dt_vencimento);
                }else{
                  $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[0]);
                  $dt_vencimento = date('Y-m-d',$dt_vencimento);
                }
              
              }else{
        
                $dt_vencimento_atual = explode('-',$dt_vencimento);
                $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
                $dt_vencimento = date('Y-m-d',$dt_vencimento);
        
              }
            
              $lancamento_rcr['dt_vencimento'] = $dt_vencimento;
            
            }
    
        }
      }
      //fim da busca por lançamentos recorrentes
    
      //busca valores para saldo do plano de contas
      $array_valores = array();	
    
      $array_contas_analiticas = $db->fetch_all_array('
        select id, hierarquia
        from plano_contas
        where tp_conta = 1
      ');
    
      $i = 0; //índice para referenciar o mês
    
      foreach($array_meses as $mes){
        
        foreach($array_contas_analiticas as $conta_analitica){
        
          $conta_analitica_id = $conta_analitica['id'];
          $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
      
          $valor_plc = 0; //valor do plano de contas acumulado por período
    
          //lançamentos compensados
          $valor_compensado = $db->fetch_assoc('
            select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
            from ctr_plc_lancamentos cpl
            join lancamentos l on cpl.lancamento_id = l.id
            where cpl.plano_contas_id = '.$conta_analitica_id.'
              and l.compensado = 1
              and month(l.dt_compensacao) = '.$mes.'
              and year(l.dt_compensacao) = '.$ano.'
            group by cpl.tp_lancamento');
          $valor_plc += $valor_compensado['valor'];
          ($valor_compensado['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_compensado['valor'] : $array_saldo_final[$i] -= $valor_compensado['valor'];
    
          //lançamentos abertos e não vencidos
          if($mes >= $mes_atual && $ano >= $ano_atual){
            
            //recebimentos ou pagamentos programados
            $valor_aberto = $db->fetch_assoc('
              select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
              from ctr_plc_lancamentos cpl
              join lancamentos l on cpl.lancamento_id = l.id
              where cpl.plano_contas_id = '.$conta_analitica_id.'
                and l.compensado = 0
                and month(l.dt_vencimento) = '.$mes.'
                and year(l.dt_vencimento) = '.$ano.'
                and l.dt_vencimento >= "'.$dt_hoje.'"
              group by cpl.tp_lancamento');
            $valor_plc += $valor_aberto['valor'];
            ($valor_aberto['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_aberto['valor'] : $array_saldo_final[$i] -= $valor_aberto['valor'];
            
            //recebimentos ou pagamentos empenhados
            $valor_empenho = $db->fetch_assoc('
              select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
              from ctr_plc_lancamentos_plnj cpl
              join lancamentos_plnj l on cpl.lancamento_plnj_id = l.id
              where cpl.plano_contas_id = '.$conta_analitica_id.'
                and month(l.dt_vencimento) = '.$mes.'
                and year(l.dt_vencimento) = '.$ano.'
                and l.dt_vencimento >= "'.$dt_hoje.'"
              group by cpl.tp_lancamento');
            $valor_plc += $valor_empenho['valor'];
            ($valor_empenho['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_empenho['valor'] : $array_saldo_final[$i] -= $valor_empenho['valor'];
            
          }
    
          //lançamentos recorrentes
          $valor_rcr = $db->fetch_assoc('
            select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
            from ctr_plc_lancamentos_rcr cpl
            join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
            where cpl.plano_contas_id = '.$conta_analitica_id.'
              and month(l.dt_vencimento) = '.$mes.'
              and year(l.dt_vencimento) = '.$ano.'
            group by cpl.tp_lancamento');
          $valor_plc += $valor_rcr['valor'];
          ($valor_rcr['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_rcr['valor'] : $array_saldo_final[$i] -= $valor_rcr['valor'];
    
          //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
          foreach($conta_hierarquia as $conta_id){
            if(isset($array_valores[$conta_id][$i])){
              $array_valores[$conta_id][$i] += $valor_plc;
            }else{
              $array_valores[$conta_id][$i] = $valor_plc;
            }
          }
          
        }//fim busca valores para o plano de contas
    
        //atualiza o saldo inicial do período seguinte(dia ou mês)
        $array_saldo_final[$i] += $array_saldo_anterior[$i];
        $array_saldo_anterior[$i+1] = $array_saldo_final[$i];
        
        //incrementa o mês
        $i++;
      }//fim do for dos meses
    
      //retira a última posição do array de saldo anterior que fica além do período especificado
      array_pop($array_saldo_anterior);
    
      //busca valores do orçamento(somente para fluxo de caixa mensal)
      $mes_numero = array(1,2,3,4,5,6,7,8,9,10,11,12);
      $mes_texto = array('jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez');
      $array_meses_txt = str_replace($mes_numero, $mes_texto, $array_meses);
      $array_meses_txt = join(',',$array_meses_txt);
      foreach($array_contas_analiticas as $conta_analitica){
        $conta_analitica_id = $conta_analitica['id'];
        $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
        $orcamento = $db->fetch_assoc("select ".$array_meses_txt." from orcamentos_plnj_vl where orcamento_id = 1 and plano_contas_id = ".$conta_analitica_id);
        $i = 0;
        foreach($orcamento as $valor){
          foreach($conta_hierarquia as $conta_id){
            if(isset($array_orcamento_valores[$conta_id][$i])){
              $array_orcamento_valores[$conta_id][$i] += $valor;
            }else{
              $array_orcamento_valores[$conta_id][$i] = $valor;
            }
          }
          $i++;
        }
      }
    
      //monta tabela
      $array_plano_contas = $db->fetch_all_array('
        select plc.id, cod_ref, cod_conta, nome, tp_conta, plc.nivel
        from plano_contas plc
        left join ctr_plc_lancamentos cpl on plc.id = cpl.plano_contas_id
        group by plc.id
        having plc.nivel <= '.$nivel_plc.'
        order by cod_conta
        '
      );
    
      $array_orcamento = array();
      
      $linhas = '';
      
      foreach($array_plano_contas as $plano_contas){
        $linhas .= '
          <tr>
            <td align="left"><b>'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</b></td>
        ';
        for($i=0;$i<count($array_meses);$i++){
          $valor = $array_valores[$plano_contas['id']][$i];
          $valor_orcamento = $array_orcamento_valores[$plano_contas['id']][$i];
          $linhas .= '
            <td align="right">R$ '.$db->valorFormat($valor_orcamento).'</td>
            <td align="right">R$ '.$db->valorFormat($valor).'</td>
          ';
        }
        $linhas .= '</tr>';
      }
    
      $cabecalho_saldo_total = '
        <tr bgcolor="#F5F5F5">
          <td><b>Saldo anterior</b><div style="font-size:11px;display:inline"> (Caixa + Bancos)</div></td>
      ';
      foreach($array_saldo_anterior as $saldo_total){
        ($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
        $cabecalho_saldo_total .= '<td colspan="2" align="right" style="color:'.$cor.'"><b>R$ '.$db->valorFormat($saldo_total).'</b></td>';
      }
      $cabecalho_saldo_total .= "</tr>";
      $tbl_cabecalho = $cabecalho_saldo_total;//.$tbl_cabecalho;
      //fim monta cabeçalho da tabela
      
      //monta cabeçalho secundário da tabela
      $tbl_cabecalho .= '<tr bgcolor="#F0F0F0">';
      $tbl_cabecalho .= '<td rowspan="2" align="center"><b>Plano de contas / Período</b></td>';
      foreach($array_meses as $mes){
        if($mes==6){$nome_mes='Junho';}elseif($mes==7){$nome_mes='Julho';}else{$nome_mes='Agosto';}
        $tbl_cabecalho .= '<td colspan="2" align="center"><b>'.$nome_mes.'</b></td>';
      }
      $tbl_cabecalho .= "</tr>";
      $tbl_cabecalho .= '<tr bgcolor="#F0F0F0">';
      
      for($i=0;$i<count($array_meses);$i++){
        $tbl_cabecalho.= '<td align="center"><b>Orçamento</b></td>';
        if($array_meses[$i]<$mes_atual){
          $tbl_cabecalho.= '<td align="center"><b>Realizado</b></td>';
        }elseif($array_meses[$i]==$mes_atual){
          $tbl_cabecalho.= '<td align="center"><b>Previsto / Realizado</b></td>';
        }else{
          $tbl_cabecalho.= '<td align="center"><b>Previsto</b></td>';
        }
      }
      $tbl_cabecalho .= "</tr>";
      //fim monta cabeçalho secundário da tabela
      
      $rodape_saldo_total = '
        <tr bgcolor="#F5F5F5">
          <td><b>Saldo final</b><span style="font-size:11px;"> (Caixa + Bancos)</span></td>
      ';
      foreach($array_saldo_final as $saldo_total){
        ($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
        $rodape_saldo_total .= '<td colspan="2" align="right" style="color:'.$cor.'"><b> R$ '.$db->valorFormat($saldo_total).'</b></td>';
      }
      $rodape_saldo_total .= "</tr>";
      $tbl_rodape = $rodape_saldo_total;//.$tbl_rodape;
      //fim monta saldo final no rodapé da tabela
    
      $relatorio = '
      <div class="widget">
        <div class="title"><img src="images/icons/dark/frames.png" alt="" class="titleIcon" /><h6>Fluxo de Caixa</h6></div>
        <table cellpadding="0" cellspacing="0" class="display pc_cr"> 
          <thead>
            '.$tbl_cabecalho.'
          </thead>
          <tbody>
            '.$linhas.$tbl_rodape.'
          </tbody>
        </table>
      </div>
      ';//fim monta tabela
    
      $db->query("truncate table lancamentos_rcr_temp");
    
      echo $relatorio;
    */	
    
    // ======================================================================================= RELATÓRIO DIÁRIO ===========================================================
    
    /*
      $dt_hoje = date('Y-m-d');
      $hora_relatorio = date('H:i');
      $data_relatorio = date('d/m/Y');
      $relatorio = "";
      
      //dados para teste
      
      //data de inicio e fim
      //relatório mensal ou diário
      
      $dt_ini = '2014-06-01';
      $dt_fim = '2014-06-04';
      //fim dados para teste
    
      $array_saldo_anterior = array();
      
      //calculo do saldo anterior
      $query_receita = '
        select sum(valor) valor
        from lancamentos
        where tipo = "R"
          and dt_compensacao < "'.$dt_ini.'"
          and compensado = 1
      ';
      $receita = $db->fetch_assoc($query_receita);
      
      $query_despesa = '
        select sum(valor) valor
        from lancamentos
        where tipo = "P"
          and dt_compensacao < "'.$dt_ini.'"
          and compensado = 1
      ';
      $despesa = $db->fetch_assoc($query_despesa);
    
      $saldo_anterior = $receita['valor'] - $despesa['valor'];
      $array_saldo_anterior[] = $saldo_anterior;
    
      //duplica o array de saldo anterior para fazer a atualização mês a mês
      //$array_saldo_final = $array_saldo_anterior;
    
      //tabela temporária para lançamentos recorrentes
      $db->query("
        CREATE TEMPORARY TABLE lancamentos_rcr_temp (
          id int(11),
          conta_id int(11),
          dt_vencimento date NOT NULL,
          valor decimal(10,2) NOT NULL,
          frequencia int(3),
          dia_mes int(1)
        ) ENGINE=MEMORY
      ");
    
      //situação dos lançamentos
      $lancamento_situacao = $array_dados['lancamento_situacao'];
      $compensado = "";
      if($lancamento_situacao==0){
        $compensado = "and l.compensado = 0";
      }elseif($lancamento_situacao==1){
        $compensado = "and l.compensado = 1";
      }
    
      //contas financeiras do relatório	
      //$array_cf_id = explode(',',$array_dados["contas_financeiras"]);
    
      //nível do plano de contas
      $nivel_plc = 2;//$array_dados['nivel_plc'];
    
      //busca lançamentos recorrentes
      if($lancamento_situacao!=1){
    
          $query_lancamentos_rcr = mysql_query("
            select id 
            from lancamentos_recorrentes
            where dt_vencimento <= '".$dt_fim."'
          ");
        
          while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
         
            $lancamento_rcr = $db->fetch_assoc("
              select id, conta_id, dt_vencimento, valor, frequencia, dia_mes
              from lancamentos_recorrentes 
              where id = ".$lancamento['id']
            );
            
            $dt_vencimento = date($lancamento_rcr['dt_vencimento']);
        
            while($dt_vencimento <= $dt_fim){
            
              if($dt_vencimento >= $dt_ini){
                $db->query_insert('lancamentos_rcr_temp',$lancamento_rcr);
              }
              
              if($lancamento_rcr['frequencia']>=30){
              
                $frequencia = $lancamento_rcr['frequencia']/30;
                $dia_vencimento = $lancamento_rcr['dia_mes'];
                $dt_vencimento_atual = explode('-',$dt_vencimento);
                $mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[0]);
                $qtd_dias_mes = date('t',$mes_prox_venc);
        
                if( $qtd_dias_mes < $dia_vencimento ){
                  $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[0]);
                  $dt_vencimento = date('Y-m-d',$dt_vencimento);
                }else{
                  $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[0]);
                  $dt_vencimento = date('Y-m-d',$dt_vencimento);
                }
              
              }else{
        
                $dt_vencimento_atual = explode('-',$dt_vencimento);
                $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
                $dt_vencimento = date('Y-m-d',$dt_vencimento);
        
              }
            
              $lancamento_rcr['dt_vencimento'] = $dt_vencimento;
            
            }
    
        }
      }
      //fim da busca por lançamentos recorrentes
    
      //busca valores para saldo do plano de contas
      $array_valores = array();	
      $array_dias = array();
    
      $array_contas_analiticas = $db->fetch_all_array('
        select id, hierarquia
        from plano_contas
        where tp_conta = 1
      ');
    
      $dia = strtotime($dt_ini);
      $dia_fim = strtotime($dt_fim);
      $i = 0; //índice para referenciar os dias no array de saldo final
    
      while($dia<=$dia_fim){
    
        $dia = date('Y-m-d',$dia);
        $array_dias[] = $dia;
        
      
        foreach($array_contas_analiticas as $conta_analitica){
        
          $conta_analitica_id = $conta_analitica['id'];
          $conta_hierarquia = explode(',',$conta_analitica['hierarquia']);
      
          $valor_plc = 0; //valor do plano de contas acumulado por período
      
          //lançamentos compensados
            $valor_compensado = $db->fetch_assoc('
              select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
              from ctr_plc_lancamentos cpl
              join lancamentos l on cpl.lancamento_id = l.id
              where cpl.plano_contas_id = '.$conta_analitica_id.'
                and l.compensado = 1
                and l.dt_compensacao = "'.$dia.'"
              group by cpl.tp_lancamento');
    
            $valor_plc += $valor_compensado['valor'];
            ($valor_compensado['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_compensado['valor'] : $array_saldo_final[$i] -= $valor_compensado['valor'];
      
          //lançamentos abertos e não vencidos
            
            //recebimentos ou pagamentos programados
              $valor_aberto = $db->fetch_assoc('
                select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
                from ctr_plc_lancamentos cpl
                join lancamentos l on cpl.lancamento_id = l.id
                where cpl.plano_contas_id = '.$conta_analitica_id.'
                  and l.compensado = 0
                  and l.dt_vencimento = "'.$dia.'"
                group by cpl.tp_lancamento');
    
              $valor_plc += $valor_aberto['valor'];
              ($valor_aberto['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_aberto['valor'] : $array_saldo_final[$i] -= $valor_aberto['valor'];
            
            //recebimentos ou pagamentos empenhados
              $valor_empenho = $db->fetch_assoc('
                select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
                from ctr_plc_lancamentos_plnj cpl
                join lancamentos_plnj l on cpl.lancamento_plnj_id = l.id
                where cpl.plano_contas_id = '.$conta_analitica_id.'
                  and l.dt_vencimento = "'.$dia.'"
                group by cpl.tp_lancamento');
                
              $valor_plc += $valor_empenho['valor'];
              ($valor_empenho['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_empenho['valor'] : $array_saldo_final[$i] -= $valor_empenho['valor'];
            
          //lançamentos recorrentes (não precisa especificar a data, já foi feito na inserção da tabela temporária)
            $valor_rcr = $db->fetch_assoc('
              select sum(IFNULL(cpl.valor,0)) valor, cpl.tp_lancamento
              from ctr_plc_lancamentos_rcr cpl
              join lancamentos_rcr_temp l on cpl.lancamento_rcr_id = l.id
              where cpl.plano_contas_id = '.$conta_analitica_id.'
              group by cpl.tp_lancamento');
                
            $valor_plc += $valor_rcr['valor'];
            ($valor_rcr['tp_lancamento']=='R')? $array_saldo_final[$i] += $valor_rcr['valor'] : $array_saldo_final[$i] -= $valor_rcr['valor'];
      
          //array de valores agrupados por período(dia ou mês) para cada conta do plano de contas
            foreach($conta_hierarquia as $conta_id){
              if(isset($array_valores[$conta_id][$i])){
                $array_valores[$conta_id][$i] += $valor_plc;
              }else{
                $array_valores[$conta_id][$i] = $valor_plc;
              }
            }
              
        }//fim busca valores para o plano de contas
        
        //atualiza o saldo inicial do período seguinte(dia ou mês)
        $array_saldo_final[$i] += $array_saldo_anterior[$i];
        $array_saldo_anterior[$i+1] = $array_saldo_final[$i];
    
        $i++;
        $dia = strtotime("+1 days",strtotime($dia));
    
      }
    
      $qtd_dias = count($array_dias);
        
      //retira a última posição do array de saldo anterior que fica além do período especificado
      array_pop($array_saldo_anterior);
    
      //monta valores do plano de contas na tabela
        $array_plano_contas = $db->fetch_all_array('
          select plc.id, cod_ref, cod_conta, nome, tp_conta, plc.nivel
          from plano_contas plc
          left join ctr_plc_lancamentos cpl on plc.id = cpl.plano_contas_id
          group by plc.id
          having plc.nivel <= '.$nivel_plc.'
          order by cod_conta
          '
        );
      
        $linhas = '';
        
        foreach($array_plano_contas as $plano_contas){
          $linhas .= '
            <tr>
              <td align="left"><b>'.$plano_contas['cod_conta'].' - '.$plano_contas['nome'].'</b></td>
          ';
          
          for($i=0;$i<$qtd_dias;$i++){
            $valor = $array_valores[$plano_contas['id']][$i];
            $linhas .= '
              <td align="right">R$ '.$db->valorFormat($valor).'</td>
            ';
          }
          
          $linhas .= '</tr>';
        }
      //fim monta valores do plano de contas na tabela
    
      //monta cabeçalho da tabela
        $cabecalho_saldo_total = '
          <tr bgcolor="#F5F5F5">
            <td><b>Saldo anterior</b><div style="font-size:11px;display:inline"> (Caixa + Bancos)</div></td>
        ';
        foreach($array_saldo_anterior as $saldo_total){
          ($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
          $cabecalho_saldo_total .= '<td align="right" style="color:'.$cor.'"><b>R$ '.$db->valorFormat($saldo_total).'</b></td>';
        }
        $cabecalho_saldo_total .= "</tr>";
        $tbl_cabecalho = $cabecalho_saldo_total;
      //fim monta cabeçalho da tabela
      
      //monta cabeçalho secundário da tabela
        $tbl_cabecalho .= '<tr bgcolor="#F0F0F0">';
        $tbl_cabecalho .= '<td rowspan="2" align="center" id="pcp"><b>Plano de contas / Período</b></td>';
        for($i=0;$i<$qtd_dias;$i++){ //quandidade de dias
          $dia = $array_dias[$i];
          $dia = $db->sql_to_data($dia);
          $tbl_cabecalho .= '<td align="center"><b>'.$dia.'</b></td>';
        }
        $tbl_cabecalho .= "</tr>";
        $tbl_cabecalho .= '<tr bgcolor="#F0F0F0">';
        
        $hoje = strtotime(date('Y-m-d'));
        for($i=0;$i<$qtd_dias;$i++){
          $dia = $array_dias[$i];
          $dia = strtotime($dia);
          if($dia<$hoje){
            $tbl_cabecalho.= '<td align="center"><b>Realizado</b></td>';
          }elseif($dia==$hoje){
            $tbl_cabecalho.= '<td align="center"><b>Previsto / Realizado</b></td>';
          }else{
            $tbl_cabecalho.= '<td align="center"><b>Previsto</b></td>';
          }
        }
        $tbl_cabecalho .= "</tr>";
      //fim monta cabeçalho secundário da tabela
      
      $rodape_saldo_total = '
        <tr bgcolor="#F5F5F5">
          <td><b>Saldo final</b><span style="font-size:11px;"> (Caixa + Bancos)</span></td>
      ';
      foreach($array_saldo_final as $saldo_total){
        ($saldo_total>=0)? $cor = "#009900" : $cor = "#FF0000";
        $rodape_saldo_total .= '<td align="right" style="color:'.$cor.'"><b> R$ '.$db->valorFormat($saldo_total).'</b></td>';
      }
      $rodape_saldo_total .= "</tr>";
      $tbl_rodape = $rodape_saldo_total;//.$tbl_rodape;
      //fim monta saldo final no rodapé da tabela
    
      $relatorio = '
      <div class="widget">
        <div class="title"><img src="images/icons/dark/frames.png" alt="" class="titleIcon" /><h6>Fluxo de Caixa</h6></div>
        <table cellpadding="0" cellspacing="0" class="display pc_cr"> 
          <thead>
            '.$tbl_cabecalho.'
          </thead>
          <tbody>
            '.$linhas.$tbl_rodape.'
          </tbody>
        </table>
      </div>
      ';//fim monta tabela
    
      $db->query("truncate table lancamentos_rcr_temp");
    
      echo $relatorio;
    */
    ?>
  
  	</div>
  

    <div id="relatorio">
    </div>
    <!-- ====== Fim do Palco ====== -->
 
	</div> 
</div> 

<form target="_blank" method="post" action="exibirRelatorio" id="formPdf"> <!-- action="php/MPDF/examples/relatorios.php" -->
	<input type="hidden" name="funcao" value="" id="tp_report"/>
	<input type="hidden" name="params" value="" id="report_params"/>
</form>