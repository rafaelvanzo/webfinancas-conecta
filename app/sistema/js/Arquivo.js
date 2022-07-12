var crud = false;

$(document).ready(function () {

    //Resetar form arquivo
    var FormArquivosReset = function (action) {
        if (action == 'Create') {
            $('#form-arquivo').attr('action', 'Create');
            $('#btn-incluir-arquivo > span').text('Incluir');
            $('#arquivo-id').val('');
            $('#dt-arquivo').val('');
            $('#justificado').val('');
        } else {
            $('#form-arquivo').attr('action', 'Edit');
            $('#btn-incluir-arquivo > span').text('Editar');
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
        $('#form-funcionario').attr('action', 'Create');
        //FormReset('form-funcionario');
        $('#modal-arquivo').dialog("option", "title", 'Novo Arquivo');
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

            var file_queue = $('#dados').data('form_arquivo_file-upload-queue');
            if (file_queue > 0) {
                alert('Nenhum arquivo foi selecionado.');
                return false;
            }

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-arquivo').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Arquivo&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableArquivos.fnDraw();
                        //notificacao(1, data.msg);
                        FormArquivosReset('Create');
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
            { "mData": "dt_cadastro", "sClass": "updates newUpdate dt-center hide-mobile" },
            { "mData": "arquivo", "sClass": "updates newUpdate break-big-name hide-mobile" },
            { "mData": "tp_documento", "sClass": "updates newUpdate dt-center hide-mobile" },
            { "mData": "classificacao", "sClass": "updates newUpdate dt-center hide-mobile" },
            { "mData": "dt_competencia", "sClass": "updates newUpdate dt-center hide-mobile" },
            { "mData": "visualizado", "sClass": "updates newUpdate dt-center hide-mobile" },
            { "mData": "opcoes", "sClass": "updates newUpdate dt-center hide-mobile" },
            { "mData": "mobile", "sClass": "updates newUpdate dt-center show-mobile" }
        ],
        oLanguage: {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        },
        fnServerParams: function (aoData) {

            //var filtro = filtroParams();

            if (crud) {
                var oSettings = this.fnSettings();
                oSettings._iDisplayStart = iDisplayStart;
            } else {
                var oSettings = this.fnSettings();
                iDisplayStart = oSettings._iDisplayStart;
            }

            //start: Período do filtro
            var data = $('#mes').val();
            data = data.split('/');
            var mes = parseInt(data[0]);
            var ano = data[1];
            //end: Período do filtro

            aoData.push(/*{ "name": "filtro", "value": filtro },*/
                    { "name": "iDisplayStart", "value": iDisplayStart },
                    { "name": "mes", "value": mes },
                    { "name": "ano", "value": ano }
                );

        },
        fnDrawCallback: function () {
            crud = false;
            //$('#btn-filtro').button('reset');
        }
    });

    //$('#dTableArquivos > thead').remove(); //remove o thead

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
            data: { id: arquivoId },
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

    //Plupload
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
            chunk_size: "1mb",
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
                    var form_id_ativo = $('#dados').data('form-id-ativo');
                    $('#dados').data(form_id_ativo + '_file-upload-queue', files.length);
                },

                FilesAdded: function (up, files) {
                    var form_id_ativo = $('#dados').data('form-id-ativo');
                    var queue = $('#dados').data(form_id_ativo + '_file-upload-queue');
                    var total_files = files.length;
                    var files_added = total_files - queue;
                    var arq, file;
                    for (var i = queue; i < total_files; i++) {
                        file = files[i];
                        //Pega o tipo de arquivo
                        arq = file.name.split('.').pop();

                        document.getElementById(form_id_ativo + '_filelist').innerHTML += '<li class="listaArquivos tipS" original-title="' + file.name + '" id="' + file.id + '" align="center"><img src="images/icons/arquivos/delete.png" class="delete" onclick="ExcluirArquivo(\'form_arquivo\',\'' + file.id + '\',0)"><b></b><a href="php/uploads/' + file.name + '" class="' + file.id + '" download target="_blank" style="pointer-events:none;"><img src="images/icons/arquivos/icon-' + arq + '.png" width="30" class="listaArquivosImg"/><br/>(' + plupload.formatSize(file.size) + ') </a></li>';
                    }
                    $('#dados').data(form_id_ativo + '_file-upload-queue', total_files);
                    //ativarCROT('t');
                    //uploader.start();
                },

                BeforeUpload: function (up, file) {
                    var form_ordem_ativo = $('#dados').data('form-ordem-ativo');
                    array_uploader[form_ordem_ativo].settings.multipart_params = {
                        "cliente_id": document.getElementById("cliente_id").value,
                        "lancamento_id": $("#dados").data("lancamento_id"),
                    };
                },

                UploadProgress: function (up, file) {
                    if (file.percent < 100) { var percentual = file.percent + '%'; } else { var percentual = '<img src="images/icons/updateDone.png" class="ok">'; $('.' + file.id).css("pointer-events", ""); }
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = percentual;
                },

                UploadComplete: function (up, files) {
                    notificacao(1, $("#dados").data("notificacao-sucesso"));
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
        var documentoId = $(this).data('id');
        window.open('php/Route.php?Controller=Arquivo&Action=DownloadArquivo&nome=' + nome + '&nome_org=' + nomeOrg + '&documento_id=' + documentoId, '_self');
        dTableArquivos.fnDraw();
    });

    //Filtrar documentos por mês
    $('#btn-pesquisar').on('click', function () {
        dTableArquivos.fnDraw();
    })
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
    }
    $('#' + arquivo_id).remove();
}