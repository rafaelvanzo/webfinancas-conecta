<!-- Funcionários -->
<div id="modal-funcionario" style="height:auto; padding:0;" title="Novo Funcionário" class="modal">

    <form id="form-funcionario" class="dialog" action="Create">

        <input type="hidden" value="" name="id" id="funcionario-id"/>

        <div class="fluid">

            <div class="span12">

                <div class="tab-bs">
                    <ul class="nav nav-tabs" id="abas-form-funcionario">
	                    <li class="active"><a data-target="#aba-1" data-toggle="tab">Dados Pessoais</a></li>
	                    <li><a data-target="#aba-2" data-toggle="tab">Contato</a></li>
                        <li><a data-target="#aba-3" data-toggle="tab">Registro</a></li>
                    </ul>

                    <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

                    <div class="tab-content">

                        <!-- aba 1 --------------------------------------------------------------------------------------------------------------->

                        <div class="tab-pane active" id="aba-1" style="max-width:800px;">

                            <div class="formRow">
                                 <span class="span10 input-autocomplete-container">
                                    <label>Função:</label>
                                    <input type="text" name="funcao_id01" class="funcao_buscar input-buscar" value="" id="funcao" required/>
                                    <input type="hidden" name="funcao_id" id="funcao_id01" value="" />
                                </span>
                                <span class="span2">
                                    <label>Status:</label>
                                    <select name="status" id="status">
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </span>
                            </div>

                             <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div>

                            <div class="formRow">
                                <span class="span5">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" value="" class="required" id="nome"/>
                                </span>
                                 <span class="span2">
                                    <label>Data nasc.:</label>
                                    <input type="text" name="dt_nasc" class="required datepickerFullWidth maskDate" value="" id="dt-nasc"/>
                                </span>
                                <span class="span3">
                                    <label>Cidade nasc.:</label>
                                    <input type="text" name="cidade_nasc" value="" id="cidade-nasc"/>
                                </span>
                                <span class="span2">
                                    <label>UF nasc.:</label>
                                    <select name="uf_nasc" id="uf-nasc">
                                    <?php 
                                    $m_uf = mysql_query("select uf from uf");
                                    while($uf = mysql_fetch_assoc($m_uf)){
                                        echo "<option value=".$uf[uf].">".$uf[uf]."</option>";
                                    }
                                    ?>
                                    </select>                    
                                </span>
                            </div>

                            <div class="formRow">
                                <span class="span3">
                                    <label>CPF</label>
                                    <input type="text" name="cpf" class="cpf_cnpj maskCpf" value="" id="cpf"/>
                                </span>
                                <span class="span3">
                                    <label>RG:</label>
                                    <input type="text" name="rg" value="" class="required" id="rg"/>
                                </span>
                                <span class="span3">
                                    <label>Orgão emissor RG:</label>
                                    <input type="text" name="rg_emissor" value="" class="required" id="rg-emissor"/>
                                </span>
                                <span class="span3">
                                    <label>Data de emissão RG:</label>
                                    <input type="text" name="rg_dt_emissao" value="" class="required datepickerFullWidth maskDate" id="rg-dt-emissao"/>
                                </span>
                                 
                               
                            </div>

                            <div class="formRow">
                                <span class="span3">
                                    <label>PIS:</label>
                                    <input type="text" name="pis" value="" id="pis"/>
                                </span>
                                <span class="span3">
                                    <label>Data de insc. do PIS:</label>
                                    <input type="text" name="pis_dt_inscricao" class="datepickerFullWidth maskDate" value="" id="pis-dt-inscricao"/>
                                </span>    
                                <span class="span3">
                                    <label>Cart. profissional:</label>
                                    <input type="text" name="carteira" value="" id="carteira"/>
                                </span>
                                <span class="span3">
                                    <label>Data emissão carteira:</label>
                                    <input type="text" name="carteira_dt_emissao" class="datepickerFullWidth maskDate" value="" id="carteira-dt-emissao"/>
                                </span>
                            </div>

                            <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div>

                            <div class="formRow">
                                 <span class="span2">
                                    <label>Sexo:</label>
                                    <select name="sexo" id="sexo">
                                        <option value=""></option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Feminino</option>
                                    </select>
                                </span>
                                <span class="span2">
                                    <label>Cor/Raça:</label>
                                    <select name="raca" id="raca">
                                        <option value=""></option>
                                        <option value="1">Branco</option>
                                        <option value="2">Negro</option>
                                        <option value="3">Amarelo</option>
                                        <option value="4">Pardo</option>
                                        <option value="5">Índio</option>
                                    </select>
                                </span>
                                <span class="span2">
                                    <label>Deficiente:</label>
                                    <select name="deficiente" id="deficiente">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                                 <span class="span3">
                                    <label>Estado civil:</label>
                                    <select name="estado_civil" id="estado-civil">
                                        <option value=""></option>
                                        <option value="1">Solteiro</option>
                                        <option value="2">Casado</option>
                                        <option value="3">Divorciado</option>
                                        <option value="4">Viúvo</option>
                                    </select>
                                </span>
                                <span class="span3">
                                    <label>Grau de instrução:</label>
                                    <select name="instrucao" id="instrucao">
                                        <option value=""></option>
                                        <option value="1">1º Grau Incompleto</option>
                                        <option value="2">1º Grau Completo</option>
                                        <option value="3">2º Grau Incompleto</option>
                                        <option value="4">2º Grau Completo</option>
                                        <option value="5">Superior Incompleto</option>
                                        <option value="6">Superior Completo</option>
                                        <option value="7">Pós Graduado</option>
                                        <option value="8">Mestrado</option>
                                        <option value="9">Doutorado</option>
                                        <option value="10">Pós Doutorado</option>
                                    </select>
                                </span>

                                
                                 
                            </div>

                            <div class="formRow">
                                 <span class="span6">
                                    <label>Nome do Pai:</label>
                                    <input type="text" name="nome_pai" value="" id="nome-pai"/>
                                </span>
                                <span class="span6">
                                    <label>Nome da Mãe:</label>
                                    <input type="text" name="nome_mae" value="" id="nome-mae"/>
                                </span>  
                                
                            </div>

                             <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div>

                           <div class="formRow">
                                <span class="span12">
                                    <label>Observação:</label>
                                    <textarea name="observacao" rows="2" id="observacao"></textarea>
                                </span>
                            </div>

                        </div>

                        <!-- fim aba 1 --------------------------------------------------------------------------------------------------------------->

                        <!-- aba 2 --------------------------------------------------------------------------------------------------------------->

                        <div class="tab-pane" id="aba-2" style="max-width:800px;">                           

                            <div class="formRow">
                                <span class="span6">
                                    <label>Logradouro:</label>
                                    <input type="text" name="logradouro" value="" id="logradouro"/>
                                </span>
                                <span class="span2">
                                    <label>Nº:</label>
                                    <input type="text" name="numero" value="" id="numero"/>
                                </span>
                                <span class="span4">
                                    <label>Complemento:</label>
                                    <input type="text" name="complemento" value="" id="complemento"/>
                                </span>
                                
                            </div>

                            <div class="formRow"> 
                                <span class="span4">
                                    <label>Bairro:</label>
                                    <input type="text" name="bairro" value="" id="bairro"/>
                                </span>
                                <span class="span4">
                                    <label>Cidade:</label>
                                    <input type="text" name="cidade" value="" id="cidade"/>
                                </span>
                                <span class="span2">
                                    <label>UF:</label>
                                    <select name="uf" id="uf">
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
                                    <input type="text" name="cep" value="" class="maskCep" id="cep"/>
                                </span>
                            </div>

                            <div class="linha" style="margin-top: 10px; display: block;" id="form_rcbt_linha_01"></div>

                            <div class="formRow">
                                <span class="span6">
                                    <label>E-mail 01:</label>
                                    <input type="text" name="email01" value="" id="email01"/>
                                </span>
                                <span class="span6">
                                    <label>E-mail 02:</label>
                                    <input type="text" name="email02" value="" id="email02"/>
                                </span>  
                            </div>

                             <div class="formRow">
                                  
                                <span class="span4">
                                    <label>Tel 01:</label>
                                    <input type="text" name="tel01" value="" class="maskPhone" id="tel01"/>
                                </span>
                                <span class="span4">
                                    <label>Tel 02:</label>
                                    <input type="text" name="tel02" value="" class="maskPhone" id="tel02"/>
                                </span>
                                 <span class="span4">
                                    <label>Referência:</label>
                                    <input type="text" name="referencia" value="" id="referencia"/>
                                </span>
                            </div>

                        </div>
                
                        <!-- fim aba 2 --------------------------------------------------------------------------------------------------------------->

                        <!-- aba 3 --------------------------------------------------------------------------------------------------------------->

                        <div class="tab-pane" id="aba-3" style="max-width:800px;">

                            <div class="formRow">
                                
                                <span class="span3">
                                    <label>Data exam admissional:</label>
                                    <input type="text" name="dt_exame_admissional" value="" class="datepickerFullWidth maskDate" id="dt-exame-admissional"/>
                                </span>
                                <span class="span3">
                                    <label>Data admissão:</label>
                                    <input type="text" name="dt_admissao" value="" class="datepickerFullWidth maskDate" id="dt-admissao"/>
                                </span>
                                <span class="span3">
                                    <label>Data demissão:</label>
                                    <input type="text" name="dt_demissao" value="" class="datepickerFullWidth maskDate" id="dt-demissao"/>
                                </span>
                                <span class="span3">
                                    <label>1º emprego do ano?</label>
                                    <select name="primeiro_emprego_ano" id="primeiro-emprego-ano">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                            </div>

                            <div class="formRow">
                                <span class="span4">
                                    <label>Desconta vale Transp.?</label>
                                    <select name="desconto_transporte" id="desconto-transporte">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                                <span class="span4">
                                    <label>Adicional noturno?</label>
                                    <select name="adicional_noturno" id="adicional-noturno">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                                <span class="span4">
                                    <label>Insalubridade?</label>
                                    <select name="insalubridade" id="insalubridade">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                                
                            </div>

                            <div class="formRow">
                                <span class="span2">
                                    <label>Sindicalizado:</label>
                                    <select name="sindicalizado" id="sindicalizado">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                                <span class="span4">
                                    <label>Sindicato:</label>
                                    <input type="text" name="sindicato" value="" id="sindicato"/>
                                </span>
                                <span class="span3">
                                    <label>Optante FGTS?</label>
                                    <select name="optante_fgts" id="optante-fgts">
                                        <option value=""></option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </span>
                                <span class="span3">
                                    <label>Cod. banco:</label>
                                    <input type="text" name="cod_banco_fgts" value="" id="cod-banco-fgts"/>
                                </span>
                            </div>

                            <div class="formRow">
                                 <span class="span4">
                                    <label>Tipo de salário:</label>
                                    <select name="tp_salario" id="tp-salario">
                                        <option value=""></option>
                                        <option value="1">Mensal</option>
                                        <option value="2">Quinzenal</option>
                                        <option value="3">Semanal</option>
                                        <option value="4">Diário</option>
                                        <option value="5">Horário</option>
                                        <option value="6">Tarefa</option>
                                        <option value="7">Outros</option>
                                    </select>
                                </span>
                                <span class="span4">
                                    <label>Salário atual:</label>
                                    <input type="text" name="salario" value="" class="moeda" id="salario"/>
                                </span> 
                            </div>

                        </div>
                
                        <!-- fim aba 3 --------------------------------------------------------------------------------------------------------------->

                    </div>
                </div>
            </div>
        </div>
    
    </form>

</div>
<!-- Fim dialog -->

<!-- Faltas -->
<div id="modal-faltas" style="height:auto;padding:0;" title="Faltas" class="modal">

    <form id="form-faltas" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="funcionario-id-falta"/>

        <input type="hidden" value="" name="id" id="falta-id"/>

        <div class="fluid">
        
            <br />
            
            <div class="formRow">
                <span class="span12">
                    <h4 id="nome-funcionario-falta"></h4>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span4">
                    <label>Data da falta:</label>
                    <input type="text" name="dt_falta" value="" class="required datepickerFullWidth maskDate" id="dt-falta"/>
                </span>
                <span class="span4">
                    <label>Justificado?</label>
                    <select name="justificado" id="justificado" class="required">
                        <option value=""></option>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" style="margin-top:28px;" id="btn-incluir-falta"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" style="margin-top:28px;" id="btn-limpar-falta"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">
            
                    <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                        <div id="div-faltas">
                            <table class="display" id="dTableFaltas">
                                <thead>
                                    <tr>
                                        <th>Data Da Falta</th>
                                        <th>Justificado</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
		                    </table>
                        </div>
                    </div>                
            
            </div>

        </div>

    </form>

</div>
<!-- Fim modal faltas -->

<!-- Modal hora extra -->
<div id="modal-hora-extra" style="height:auto;padding:0;" title="Horas Extras" class="modal">

    <form id="form-hora-extra" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-hora-extra"/>

        <input type="hidden" value="" name="id" id="hora-extra-id"/>

        <div class="fluid">
        
            <br />
            
            <div class="formRow">
                <span class="span12">
                    <h4 id="nome-func-hora-extra"></h4>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span2">
                    <label>Data:</label>
                    <input type="text" name="dt_hora_extra" value="" class="required datepickerFullWidth maskDate" id="dt-hora-extra"/>
                </span>
                <span class="span2">
                    <label>Qtd. horas:</label>
                    <input type="text" name="qtd_hora_extra" value="" class="required maskNum" id="qtd-hora-extra"/>
                </span>
                <span class="span2">
                    <label>Percentual:</label>
                    <input type="text" name="percentual" value="" class="required porcentagem" id="percent-hora-extra" onkeyup="Porcentagem('percent-hora-extra')"/>
                </span>
                <span class="span2" style="text-align:left;">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" style="margin-top:28px;" id="btn-incluir-hora-extra"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2" style="text-align:center;">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" style="margin-top:28px;" id="btn-limpar-hora-extra"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">
            
                    <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                        <div id="div-hora-extra">
                            <table class="display" id="dTableHoraExtra">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Qtd. horas</th>
                                        <th>Percentual</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
		                    </table>
                        </div>
                    </div>                
            
            </div>

        </div>

    </form>

</div>
<!-- Fim hora extra -->

<!-- Alteração salarial -->
<div id="modal-salario" style="height:auto;padding:0;" title="Alteração Salarial" class="modal">

    <form id="form-salario" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-salario"/>

        <input type="hidden" value="" name="id" id="salario-id"/>

        <div class="fluid">
        
            <br />
            
            <div class="formRow">
                <h4 id="nome-func-salario"></h4>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span4">
                    <label>Data da alteração:</label>
                    <input type="text" name="dt_alteracao" value="" class="required datepickerFullWidth maskDate" id="dt-alteracao"/>
                </span>
                <span class="span4">
                    <label>Valor:</label>
                    <input type="text" name="valor" value="" class="required moeda" id="valor-salario" style="text-align:right"/>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" style="margin-top:28px;" id="btn-incluir-salario"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" style="margin-top:28px;" id="btn-limpar-salario"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">
            
                    <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                        <div id="div-salario">
                            <table class="display" id="dTableSalario">
                                <thead>
                                    <tr>
                                        <th>Data da alteração</th>
                                        <th>Valor</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
		                    </table>
                        </div>
                    </div>                
            
            </div>

        </div>

    </form>

</div>
<!-- Fim alteração salarial -->

<!-- Alteração de função -->
<div id="modal-alt-funcao" style="height:auto;padding:0;" title="Alteração De Função" class="modal">

    <form id="form-alt-funcao" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-alt-funcao" />

        <input type="hidden" value="" name="id" id="alt-funcao-id" />

        <div class="fluid">

            <br />

            <div class="formRow">
                <h4 id="nome-func-alt-funcao"></h4>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span3">
                    <label>Data da alteração:</label>
                    <input type="text" name="dt_alteracao" value="" class="required datepickerFullWidth maskDate" id="dt-alteracao-alt-funcao" />
                </span>
                <span class="span5 input-autocomplete-container">
                    <label>Função:</label>
                    <input type="text" name="funcao_id02" value="" class="required funcao_buscar input-buscar" id="input-funcao-id02" />
                    <input type="hidden" name="funcao_id" id="funcao_id02" value="" />
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" style="margin-top:28px;" id="btn-incluir-alt-funcao"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" style="margin-top:28px;" id="btn-limpar-alt-funcao"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">

                <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                    <div id="div-alt-funcao">
                        <table class="display" id="dTableAltFuncao">
                            <thead>
                                <tr>
                                    <th>Data da alteração</th>
                                    <th>Função</th>
                                    <th>Opções</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
<!-- Fim alteração de função -->

<!-- Contribuição sindical -->
<div id="modal-sindicato" style="height:auto;padding:0;" title="Contribuição Sindical" class="modal">

    <form id="form-sindicato" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-sindicato" />

        <input type="hidden" value="" name="id" id="sindicato-id" />

        <div class="fluid">

            <br />

            <div class="formRow">
                <h4 id="nome-func-sindicato"></h4>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span3">
                    <label>Nº Guia:</label>
                    <input type="text" name="guia" value="" class="required" id="guia-sindicato" />
                </span>
                <span class="span2">
                    <label>Data:</label>
                    <input type="text" name="dt_contribuicao" value="" class="required datepickerFullWidth maskDate" id="dt-contribuicao" />
                </span>
                <span class="span2">
                    <label>Valor:</label>
                    <input type="text" name="valor" value="" class="required moeda" id="valor-sindicato" style="text-align:right" />
                </span>
                <span class="span5">
                    <label>Sindicato:</label>
                    <input type="text" name="sindicato" value="" class="required" id="nome-sindicato" style="text-align:right" />
                </span>
            </div>

            <div class="formRow">
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" id="btn-incluir-sindicato"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" id="btn-limpar-sindicato"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <br />

            <div class="linha"></div>

            <div class="formRow">

                <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                    <div id="div-sindicato">
                        <table class="display" id="dTableSindicato">
                            <thead>
                                <tr>
                                    <th>Guia</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Sindicato</th>
                                    <th>Opções</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
<!-- Fim contribuição sindical -->

<!-- Afastamento -->
<div id="modal-afastamento" style="height:auto;padding:0;" title="Afastamento" class="modal">

    <form id="form-afastamento" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-afastamento" />

        <input type="hidden" value="" name="id" id="afastamento-id" />

        <div class="fluid">

            <br />

            <div class="formRow">
                <h4 id="nome-func-afastamento"></h4>
            </div>

            <div class="linha"></div>

             <div class="formRow">
                <span class="span12">
                    <label>Motivo:</label>
                    <input type="text" name="motivo" value="" class="required" id="motivo" />
                </span>
            </div>

            <div class="formRow">
                <span class="span4">
                    <label>Dt. ocorrência:</label>
                    <input type="text" name="dt_ocorrencia" value="" class="required datepickerFullWidth maskDate" id="dt-ocorrencia" />
                </span>
                <span class="span4">
                    <label>Dt. alta:</label>
                    <input type="text" name="dt_alta" value="" class="required datepickerFullWidth maskDate" id="dt-alta" />
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" id="btn-incluir-afastamento" style="margin-top:28px;"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2" style="text-align:right;">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" id="btn-limpar-afastamento" style="margin-top:28px;"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">

                <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                    <div id="div-afastamento">
                        <table class="display" id="dTableAfastamento">
                            <thead>
                                <tr>
                                    <th>Motivo</th>
                                    <th>Dt. ocorrência</th>
                                    <th>Dt. alta</th>
                                    <th>Opções</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
<!-- Fim afastamento -->

<!-- Ferias -->
<div id="modal-ferias" style="height:auto;padding:0;" title="Férias" class="modal">

    <form id="form-ferias" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-ferias" />

        <input type="hidden" value="" name="id" id="ferias-id" />

        <div class="fluid">

            <br />

            <div class="formRow">
                <h4 id="nome-func-ferias"></h4>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span2">
                    <label>Início período:</label>
                    <input type="text" name="dt_periodo_ini" value="" class="required datepickerFullWidth maskDate" id="dt-periodo-ini" />
                </span>
                <span class="span2">
                    <label>Fim período:</label>
                    <input type="text" name="dt_periodo_fim" value="" class="required datepickerFullWidth maskDate" id="dt-periodo-fim" />
                </span>
                <span class="span2">
                    <label>Início férias:</label>
                    <input type="text" name="dt_ferias_ini" value="" class="required datepickerFullWidth maskDate" id="dt-ferias-ini" />
                </span>
                <span class="span2">
                    <label>Fim férias:</label>
                    <input type="text" name="dt_ferias_fim" value="" class="required datepickerFullWidth maskDate" id="dt-ferias-fim" />
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" id="btn-incluir-ferias" style="margin-top:28px;"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" id="btn-limpar-ferias" style="margin-top:28px;"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <div class="linha"></div>

            <div class="formRow">

                <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                    <div id="div-ferias">
                        <table class="display" id="dTableFerias">
                            <thead>
                                <tr>
                                    <th>Início período</th>
                                    <th>Fim período</th>
                                    <th>Início férias</th>
                                    <th>Fim férias</th>
                                    <th>Opções</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
<!-- Fim ferias -->

<!-- Dependentes -->
<div id="modal-dependente" style="height:auto;padding:0;" title="Dependentes" class="modal">

    <form id="form-dependente" class="dialog" action="Create">

        <input type="hidden" value="" name="funcionario_id" id="func-id-dependente" />

        <input type="hidden" value="" name="id" id="dependente-id" />

        <div class="fluid">

            <br />

            <div class="formRow">
                <h4 id="nome-func-dependente"></h4>
            </div>

            <div class="linha"></div>

            <div class="formRow">
                <span class="span8">
                    <label>Nome:</label>
                    <input type="text" name="nome" value="" class="required" id="dep-nome" />
                </span>
                <span class="span2">
                    <label>Dt. nasc.:</label>
                    <input type="text" name="dt_nascimento" value="" class="required datepickerFullWidth maskDate" id="dep-dt-nascimento" />
                </span>
                <span class="span2">
                    <label>Dt. registro:</label>
                    <input type="text" name="dt_registro" value="" class="required datepickerFullWidth maskDate" id="dep-dt-registro" />
                </span>
               
            </div>

            <div class="formRow">
                 <span class="span5">
                    <label>Cartório:</label>
                    <input type="text" name="cartorio" value="" class="required" id="dep-cartorio" />
                </span>
                <span class="span3">
                    <label>Sexo:</label>
                    <select name="sexo" id="dep-sexo">
                        <option value=""></option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Incluir" class="button blueB" id="btn-incluir-dependente" style="margin-top:28px;"> <span style="color:#ffffff;">Incluir</span></a>
                </span>
                <span class="span2">
                    <a href="javascript://void(0);" title="Limpar" class="button redB" id="btn-limpar-dependente" style="margin-top:28px;"> <span style="color:#ffffff;">Limpar</span></a>
                </span>
            </div>

            <br />

            <div class="linha"></div>

            <div class="formRow">

                <div class="widget" style="border-top:none;margin-top:10px;margin-bottom:10px;">
                    <div id="div-dependente">
                        <table class="display" id="dTableDependente">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Dt. nascimento</th>
                                    <th>Dt. registro</th>
                                    <th>Cartório</th>
                                    <th>Sexo</th>
                                    <th>Opções</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
<!-- Fim dependente -->