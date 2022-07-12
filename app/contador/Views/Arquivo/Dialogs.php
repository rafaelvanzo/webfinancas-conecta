<!-- Arquivos -->
<div id="modal-arquivo" style="height:auto;padding:0;" title="Arquivos" class="modal">

    <form id="form-arquivo" class="dialog" action="Create">

        <input type="hidden" value="" name="id" id="arquivo-id" />

        <!-- ===== Váriaveis para envio de email para clientes -->
        <input type="hidden" value="" name="nomesArquivos" id="nomesArquivos" />

        <div class="fluid">

            <div class="formRow">
               
                <span class="span4">
                    <label>Tipo:</label>
                    <select name="tp_documento_id" id="tipo-doc" required>
                        <option value=""></option>
                        <option value="1">Conta à pagar</option>
                        <option value="2">Outros documentos</option>
                        <option value="3">Recálculo</option>
                    </select>
                </span>
                <span class="span4">
                    <label>Classificação:</label>
                    <select name="classificacao_id" id="classificacao-doc" required>
                        <option value=""></option>
						<option value="1">Dep. Contábil</option>
						<option value="2">Dep. Fiscal</option>
						<option value="3">Dep. Fiscalização - Alvará</option>
						<option value="4">Dep. Fiscalização - Documentos</option>
						<option value="5">Dep. Fiscalização - Registros</option>
						<option value="6">Dep. Pessoal</option>
						<option value="7">Dep. Pessoal - Admissão</option>
						<option value="8">Dep. Pessoal - Diversos</option>
						<option value="9">Dep. Pessoal - Férias</option>
						<option value="10">Dep. Pessoal - Recisão</option>
						<option value="11">Contabilidade</option>
						<option value="12">Livro Caixa</option>
						<option value="13">Outro Documento</option>
                    </select>
                </span>
                <span class="span2">
                    <label>Vencimento:</label>
                    <input type="text" name="dt_competencia" id="dt-competencia" class="datepicker maskDate" required/>
                </span>
                 <span class="span2">
                    <label>Visualização:</label>
                    <input type="text" name="dt_visualizacao" id="dt-visualizacao" class="datepicker maskDate" required/>
                </span>
            </div>
            
            <br />

            <div class="linha"></div>

            <div class="formRow">
                <span class="span12">
                    <br />
                    <!--<label>Arquivos:</label>-->
                    <div id="form_arquivo_container">
                    </div>
                    <div id="form_arquivo_filelist" class="controlB scroll bgUpload tipN" title="Clique em anexar ou arraste os arquivos até aqui." align="left">
                    </div>
                    <a id="form_arquivo_pickfiles" href="javascript:;">
                        <div class="vertical-text">
                            Anexar &nbsp;<img src="../../sistema/images/icons/dark/paperclip.png" class="clips" align="middle" />
                        </div>
                    </a>
                    <!--<pre id="console"></pre>    -->
                </span>
            </div>

        </div>

    </form>

</div>
<!-- Fim arquivos -->