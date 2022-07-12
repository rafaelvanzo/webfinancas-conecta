<!-- Arquivos -->
<div id="modal-arquivo" style="height:auto;padding:0;" title="Arquivos" class="modal">

    <form id="form-arquivo" class="dialog" action="Create">

        <input type="hidden" value="" name="id" id="arquivo-id" />

        <div class="fluid">

            <div class="formRow">
                <span class="span6">
                    <label>Tipo:</label>
                    <select name="tipo" id="tipo-doc">
                        <option value=""></option>
                        <option value="1">Conta à pagar</option>
                        <option value="2">Outros documentos</option>
                    </select>
                </span>
                <span class="span6">
                    <label>Classificação:</label>
                    <select name="classificacao" id="classificacao-doc">
                        <option value=""></option>
						<option value="1">Dep. Contábil</option>
						<option value="2">Dep. Fiscal</option>
						<option value="3">Dep. Fiscal</option>
						<option value="4">Dep. Fiscalização - Alvará</option>
						<option value="5">Dep. Fiscalização - Documentos</option>
						<option value="6">Dep. Fiscalização - Registros</option>
						<option value="7">Dep. Pessoal</option>
						<option value="8">Dep. Pessoal - Admissão</option>
						<option value="9">Dep. Pessoal - Diversos</option>
						<option value="10">Dep. Pessoal - Férias</option>
						<option value="11">Dep. Pessoal - Recisão</option>
						<option value="12">Lexdata Contabilidade</option>
						<option value="13">Livro Caixa</option>
						<option value="14">Outro Documento</option>
                    </select>
                </span>
            </div>

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
                            Anexar &nbsp;<img src="images/icons/dark/paperclip.png" class="clips" align="middle" />
                        </div>
                    </a>
                    <!--<pre id="console"></pre>    -->
                </span>
            </div>

        </div>

    </form>

</div>
<!-- Fim arquivos -->