<!-- Custeio -->
<div id="modal-custeio" style="height:auto; padding:0;" title="Custeio" class="modal">

    <form id="form-custeio" class="dialog" action="Create">

        <input type="hidden" value="" name="id" id="custeio-id"/>

        <div class="fluid">

            <div class="span12">

                        <div class="tab-pane active" id="aba-1" style="max-width:800px;">

                            <div class="formRow">
                                 <span class="span5 input-autocomplete-container">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" class="funcao_buscar input-buscar" value="" id="funcao" required/>
                                    <input type="hidden" name="nomeId" class="input-buscar-hidden" id="nome" value="" />
                                </span>
                                <span class="span2">
                                    <label>Custo por :</label>
                                    <select name="custoHoraDia">
                                        <option value="1">Minutos</option>
                                        <option value="2">Horas</option>
                                        <option value="3">Dias</option>
                                    </select>
                                </span>

                                <span class="span2">
                                    <label>Quantidade :</label>
                                    <input type="text" name="qtd" value="0" required/>
                                </span>
                                <span class="span3">
                                    <label>Valor p/ comparação:</label>
                                    <input type="text" name="valorComparacao" value="0,00" class="moeda" />
                                </span>
                               
                            </div>

                             <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div>

                           <!-- <div class="formRow">        
                                 <span class="span4">
                                    <label>Despesa adicional:</label>
                                    <input type="text" name="material1" class="" value="" />
                                </span>
                                <span class="span2">
                                    <label>Vencimento:</label><input type="text" name="dt_vencimento1" class=" maskDate " value=""/>
                                </span> 
                                <span class="span1">
                                    <label>Qtd.:</label>
                                    <input type="text" name="qtdMaterial1" class="qtdCalculo1" value="1" onkeyup="calculoCusto('1', '1');"/>
                                </span>
                                <span class="span2">
                                    <label>Custo unitário:</label>
                                    <input type="text" name="custoUnitario1" class="moeda custoCalculo1" value="0,00" onkeyup="calculoCusto('1', '1');"/>
                                </span>
                                <span class="span3">
                                    <label>Valor:</label>
                                    <input type="text" name="valor1" value="0,00" class="moeda valorFinal1" readonly="readonly"/>
                                </span>                                
                            </div> -->
                            
                            <div class="formRow listMaterial">        
                                 <span class="span12">
                                    <a href="javascript://" title="" class="button blueB addMaterial" style="width: 100%;"><span style="color: #fff;">Adicionar custos</span></a>
                                </span>
                            </div>                        


                </div>
            </div>
        </div>
    
    </form>

</div>
<!-- Fim dialog -->


<!-- LANÇAMENTOS DE CUSTO -->
<div id="modal-lancamentos" style="height:auto; padding:0;" title="" class="modal">

    <form id="form-lancamentos" class="dialog" action="EditLancamentos">

        <div class="fluid">

            <div class="span12">

                        <div class="tab-pane active" id="aba-1" style="max-width:800px;">

                            <div class="formRow">
                                <span class="span6">
                                    <label>Qtd. Horas trabalhados:</label>
                                    <input type="text" class="hora" name="hora" placeholder="Qtd. de horas" value="176" required>
                                </span>
                                <span class="span6">
                                    <label> Qtd. Dias trabalhados:</label>
                                    <input type="text" class="dia" name="dia" placeholder="Qtd. de dias" value="22" required>
                                </span>
                            </div>


                            <div style="overflow: auto; max-height: 400px;">
                                <table class="display dataTable">
                                    <th class="updates" style="text-align:left; padding-left:10px;">Categoria</th>
                                    <th class="updates" style="text-align:left; padding-left:10px;">Descrição</th>
                                    <th class="updates" style="text-align:right; padding-right:10px;">Vencimento</th>
                                    <th class="updates" style="text-align:right; padding-right:10px;">Valor</th>
                                    <tbody id="lancamentos"></tbody>
                                </table>
                            </div>

                            
                            
                            
                            <div class="formRow listLancamentos">        
                                 <span class="span12">
                                    <a href="javascript://" title="" class="button blueB addLancamentos" style="width: 100%;"><span style="color: #fff;">Adicionar</span></a>
                                </span>
                            </div>


                </div>
            </div>
        </div>
    
    </form>

</div>
<!-- Fim dialog -->
