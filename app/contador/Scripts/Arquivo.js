var crud = false;

$(document).ready(function () {

    //Resetar form arquivo
    var FormArquivosReset = function (action) {
        if (action == 'Create') {
            $('#modal-arquivo').dialog("option", "title", 'Novo Documento');
            $('#form-arquivo').attr('action', 'Create');
            $('#arquivo-id').val('');
            $('#tipo-doc').val('');
            $('#classificacao-doc').val('');
            $('#dt-competencia').val('');
            $('#dt-visualizacao').val('');
            array_uploader[0].splice();
            $('#form_arquivo_filelist > li').remove();
            $('#dados').data('form-arquivo_file-upload-queue', 0);
        } else {
            //$('#form-arquivo').attr('action', 'Edit');
            //$('#btn-incluir-arquivo > span').text('Editar');
        }
    }

    $('#btn-limpar-arquivo').on('click', function (e) {
        FormArquivosReset('Create');
    });

    //Modal arquivo
    $("#modal-arquivo").dialog({
        autoOpen: false,
        modal: true,
        width: 'auto',
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                $('#form-arquivo').submit();
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#open-modal-arquivo").click(function (e) {
        e.preventDefault();
        if ($('#cliente_id').val() == 0) {
            alert('Selecione um cliente');
            return;
        }
        FormArquivosReset('Create');
        $("#modal-arquivo").dialog("open");
        //usado pelo plupload
        $('#dados').data('form-id-ativo', 'form_arquivo');
    })
    
    //Incluír/Editar arquivo
    $('#btn-incluir-arquivo').on('click', function (e) {
        //$(this).button('loading');
        $('#form-arquivo').submit();
    });

    $('#form-arquivo').on('submit', function (e) {
        
        e.preventDefault();

        if ($(this).valid()) {
            $('#modal-arquivo').dialog("close");
            $("span.aguarde, div.aguarde").css("display", "block");
            array_uploader[0].start();
            /*
            //var file_queue = $('#dados').data('form_arquivo_file-upload-queue');
            //if (file_queue > 0) {
              //  alert('Nenhum arquivo foi selecionado.');
                //return false;
            //}

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-arquivo').attr('action');

            var data = $(this).serialize();

            //$('#modal-arquivo').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Arquivo&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        //dTableArquivos.fnDraw();
                        //notificacao(1, data.msg);
                        //FormArquivosReset('Create');
                        //Inicia upload
                        array_uploader[0].start();
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-arquivo').button('reset');
                },
            })
            */
        }
    });

    //Exibir arquivos
    $('#div-arquivo').on('click', '.exibir-arquivo', function (e) {
        e.preventDefault();

        FormArquivosReset('Edit');

        var arquivoId = $(this).data('arquivo-id');
        var dtArquivo = $(this).data('dt-arquivo');
        var justificado = $(this).data('justificado');

        $('#dt-arquivo').val(dtArquivo);
        $('#arquivo-id').val(arquivoId);
        $('#justificado').val(justificado);
    });

    //Data table arquivos
    var dTableArquivos = $('#dTableArquivos').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Arquivo&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bFilter: false,
        //bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dt_cadastro", "sClass": "updates newUpdate", "className": "dt-center" },
            { "mData": "arquivo", "sClass": "updates newUpdate" },
            { "mData": "tp_documento", "sClass": "updates newUpdate dt-center" },
            { "mData": "classificacao", "sClass": "updates newUpdate dt-center" },
            { "mData": "dt_competencia", "sClass": "updates newUpdate dt-center" },
            { "mData": "visualizado", "sClass": "updates newUpdate dt-center" },
            { "mData": "opcoes", "sClass": "updates newUpdate dt-center" }
        ],
        oLanguage: {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        },
        fnServerParams: function (aoData) {

            //var filtro = filtroParams();
            var filtro = {
                cliente_id: $('#cliente_id').val()
            };
            filtro = JSON.stringify(filtro);

            if (crud) {
                var oSettings = this.fnSettings();
                oSettings._iDisplayStart = iDisplayStart;
            } else {
                var oSettings = this.fnSettings();
                iDisplayStart = oSettings._iDisplayStart;
            }

            aoData.push({ "name": "filtro", "value": filtro }, { "name": "iDisplayStart", "value": iDisplayStart });

        },
        fnDrawCallback: function () {
            crud = false;
            //$('#btn-filtro').button('reset');
        }
    });

    //$('#dTableArquivos > thead').remove(); //remove o thead

    //Renderiza datatable após selecionar cliente
    $('#cliente_id, #cliente_id_cr').on('change', function () {
        dTableArquivos.fnDraw();
    });

    //Renderiza datatable após limpar cliente
    $('#cliente_id_cr').on('click', function () {
        dTableArquivos.fnDraw();
    });

    //Excluir arquivos
    $('#dTableArquivos').on('click', '.excluir-arquivo', function (e) {
        e.preventDefault();
        var arquivoId = $(this).attr('href');
        $('#link-exc-' + arquivoId).parent().parent().attr('id', 'tbl-arquivo-row-' + arquivoId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirArquivo(arquivoId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirArquivo = function (arquivoId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Arquivo&Action=Delete',
            data: { id: arquivoId, cliente_id: $('#cliente_id').val() },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableArquivos").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-arquivo-row-' + arquivoId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    /*
========================================================================================================================
PLUPLOAD
========================================================================================================================
*/

    /*
    1 - Definir array contendo a declaração dos uploaders
    2 - Definir array com nome dos forms para servir de prfixo para cada campo
    3 - Definir uma variável para o loop for tomando cuidado com variáveis que já possam existir em todo o javascript utilizado no sistema e na página de maneira que sejam evitados conflitos
    4 - As funções dentro do método init do plugin são executadas em tempo de execução, portanto foi definido um controle de qual formulário está ativo para utilizar a id correta do form
    */

    array_uploader = new Array();
    array_form = new Array();
    array_form = ['form_arquivo'];

    for (iForm = 0; iForm < array_form.length; iForm++) {

        array_uploader[iForm] = new plupload.Uploader({
            runtimes: 'html5',//'html5,flash,silverlight,html4',
            chunk_size: "200kb", //'200kb'
            browse_button: array_form[iForm] + '_pickfiles', // you can pass in id...
            container: document.getElementById(array_form[iForm] + '_container'), // ... or DOM Element itself

            url: "../php/upload.php",

            filters: {
                //drop_element : 'drop-target',
                //browse_button : 'drop-target',
                max_file_size: '10mb',
                mime_types: [
					{ title: "Image files", extensions: "jpg,gif,png" },
					{ title: "Zip files", extensions: "zip" },
					{ title: "Rar files", extensions: "rar" },
					{ title: "Pdf files", extensions: "pdf" },
					{ title: "Xml files", extensions: "xml" },
					{ title: "Xls files", extensions: "xls,xlsx" },
					{ title: "Doc files", extensions: "doc,docx" },
                    { title: "Txt files", extensions: "txt" },
{ title: "Pdf files", extensions: "pdf" },
{ title: "Pdf files", extensions: "pdf" },
{ title: "Pdf files", extensions: "pdf" },
                ]
            },

            //Drag & Drop
            drop_element: array_form[iForm] + '_filelist',

            // Flash settings
            flash_swf_url: '/plupload/js/Moxie.swf',

            // Silverlight settings
            silverlight_xap_url: '/plupload/js/Moxie.xap',

            init: {
                PostInit: function () {
                    document.getElementById(array_form[iForm - 1] + '_filelist').innerHTML = '';

                    //document.getElementById('form_rcbt_uploadfiles').onclick = function() {
                    //uploader.start();
                    //return false;
                    //};

                    $('#dados').data(array_form[iForm - 1] + '_file-upload-queue', 0);
                },

                FilesRemoved: function (up, files) {
                    //alert(files.length);
                    var form_id_ativo = $('#dados').data('form-id-ativo');
                    $('#dados').data(form_id_ativo + '_file-upload-queue', files.length - 1);
                },

                FilesAdded: function (up, files) {
                    var form_id_ativo = $('#dados').data('form-id-ativo');
                    var queue = $('#dados').data(form_id_ativo + '_file-upload-queue');
                    var total_files = files.length;
                    var arq, file;
                        
                    if (queue < 0) { queue = 0; }//Para acertar o erro de não aparecer o icone quando tenta anexar arquivo novamente sem atualizar a página.

                    for (i = queue; i < total_files; i++) {
                        file = files[i];
                        
                        //Pega o tipo de arquivo
                        arq = file.name.split('.').pop();

                        document.getElementById(form_id_ativo + '_filelist').innerHTML += '<li class="listaArquivos tipS" original-title="' + file.name + '" id="' + file.id + '" align="center"><img src="../../sistema/images/icons/arquivos/delete.png" class="delete" onclick="ExcluirArquivo(\'form_arquivo\',\'' + file.id + '\',0)"><b></b><a href="php/uploads/' + file.name + '" class="' + file.id + '" download target="_blank" style="pointer-events:none;"><img src="../../sistema/images/icons/arquivos/icon-' + arq + '.png" width="30" class="listaArquivosImg"/><br/>(' + plupload.formatSize(file.size) + ') </a></li>';
                        
                        var Nome = $('#nomesArquivos').val();
                        var NomesArquivos = Nome + '<li>' + file.name + '</li>';
                        $('#nomesArquivos').val(NomesArquivos);
                        
                    }
                    $('#dados').data(form_id_ativo + '_file-upload-queue', total_files);
                    
                    //ativarCROT('t');
                    //uploader.start();
                },

                BeforeUpload: function (up, file) {
                    //var form_ordem_ativo = $('#dados').data('form-ordem-ativo');
                    array_uploader[0].settings.multipart_params = {
                        cliente_id: document.getElementById("cliente_id").value,
                        tp_documento_id: $('#tipo-doc').val(),
                        classificacao_id: $('#classificacao-doc').val(),
                        dt_competencia: $('#dt-competencia').val(),
                        dt_visualizacao: $('#dt-visualizacao').val(),
                        //"lancamento_id": $("#dados").data("lancamento_id"),
                    };

                    //Verifica se o tipo de documento é pagamento.
                    if ($('#tipo-doc').val() === '1') {
                        
                        //Remove a extensão do arquivo e deixa só o nome.
                        var NomeArq = file.name;
                        NomeArq = NomeArq.substring(0, NomeArq.lastIndexOf('.'));
                        
                        //Envio de SMS
                        var ClienteId = document.getElementById("cliente_id").value;
                        var TPDoc = $('#tipo-doc').val();
                        var CDoc = $('#classificacao-doc').val();
                        var DTC = $('#dt-competencia').val();
                        var DTV = $('#dt-visualizacao').val();

                        //Envio SMS
                        EnvioSMS(ClienteId, TPDoc, CDoc, DTC, DTV, NomeArq);
                    }
                },

                UploadProgress: function (up, file) {
                    if (file.percent < 100) { var percentual = file.percent + '%'; } else { var percentual = '<img src="../../sistema/images/icons/updateDone.png" class="ok">'; $('.' + file.id).css("pointer-events", ""); }
                    if (document.getElementById(file.id))
                        document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = percentual;
                },

                UploadComplete: function (up, files) {
                    $("span.aguarde, div.aguarde").css("display", "none");       

                    crud = true;
                    dTableArquivos.fnDraw();
                    FormArquivosReset('Create');                    
                    notificacao(1, 'Documento incluído com sucesso');


                    //Dados para Envio de Email
                    var ClienteId = document.getElementById("cliente_id").value;
                    var nomesArquivos = $('#nomesArquivos').val();

                    //Envio de email
                    ListaEnvio(ClienteId, nomesArquivos);

                    $('#nomesArquivos').val('');


                },

                Error: function (up, err) {
                    document.getElementById($('#dados').data('form-id-ativo') + '_console').innerHTML += "\nError #" + err.code + ": " + err.message;
                },
            }

        });

        array_uploader[iForm].init();
    }

    //Download arquivo
    $('#dTableArquivos').on('click', '.download', function (e) {
        e.preventDefault();
        var nome = $(this).data('nome');
        var nomeOrg = $(this).data('nome-org');
        window.open('php/Route.php?Controller=Arquivo&Action=DownloadArquivo&nome=' + nome + '&nome_org=' + nomeOrg, '_self');
    });

})

//Mensagem de notificação
function notificacao(situacao, mensagem) {
    if (situacao == 1) {
        $('.nSuccess p').html(mensagem);
        $('.nSuccess').slideDown();
        setTimeout(function () { $('.nSuccess').slideUp() }, 4000);
    } else {
        $('.nWarning p').html(mensagem);
        $('.nWarning').slideDown();
        setTimeout(function () { $('.nWarning').slideUp() }, 4000);
    }
}

//Restar form
function FormReset(form) {

    var validator = $('#' + form).validate();
    validator.resetForm();
    $("#" + form + " input[name='banco_id']").val("");
    $('span.check-green').css('display', 'none');

    //resetar abas
    $('#abas-' + form + ' a:first').tab('show');

    //$('#' + form + ' div.MaisOpcoes').attr('class', 'title closed MaisOpcoes normal');
    //$('#' + form + ' div.body:eq(0)').css('display', 'none');
}

//Excluir arquivo
function ExcluirArquivo(form_id, arquivo_id, situacao) {

    if (situacao == 1) {

        $("span.aguarde, div.aguarde").css("display", "block");
        //var params = 'funcao=anexoExcluir';
        //params += '&anexo_id=' + anexo_id;
        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Arquivo&Action=ExcluirArquivo',
            data: { arquivo_id: arquivo_id },
            cache: true,
            dataType: 'json',
            success: function (data) {
                $("span.aguarde, div.aguarde").css("display", "none");
            }
        })

    } else {
        
        var files = array_uploader[0].files;

        for (var i = 0; i < files.length; i++) {
            if (files[i].id == arquivo_id) {
                array_uploader[0].removeFile(files[i]);
            }
        }
    }

    $('#' + arquivo_id).remove();
}


//Envio SMS
function EnvioSMS(ClienteId, TPDoc, CDoc, DTC, DTV, NomeArq) {

    $.ajax({
        async:true,
        type: 'Post',                                                                                   //Tipo de envio GET ou POST
        url: 'php/Route.php?Controller=Arquivo&Action=EnvioSMSEmail',                                   //Caminho do arquivo no servidor que ira receber e retornar as informações
        data: { ClienteId: ClienteId, TPDoc: TPDoc, CDoc: CDoc, DTC: DTC, DTV: DTV, NomeArq: NomeArq }  //Envia as informações para o servidor
    });
}


//Envio Email
function ListaEnvio(ClienteId, NomeArquivos) {

    $.ajax({
        async: true,
        type: 'Post',                                                                           //Tipo de envio GET ou POST
        url: 'php/Route.php?Controller=Arquivo&Action=ListaEnvio',                              //Caminho do arquivo no servidor que ira receber e retornar as informações
        data: { ClienteId: ClienteId, NomeArquivos: NomeArquivos }    //Envia as informações para o servidor
    });
}
