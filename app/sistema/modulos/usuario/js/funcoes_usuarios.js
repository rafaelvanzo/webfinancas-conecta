var crud = false;

$(document).ready(function (e) {

    //USUÁRIOS
    //=====================================================================================================================================

    //start: abas
    $('#abas a:first').tab('show');
    //end: abas

    //start: limpar formulário
    var ResetFormUsuario = function (action) {
        var validator = $('#form-usuario').validate();
        validator.resetForm();
        $('#usuario-id').val('');
        $("#form-usuario")[0].reset();
        if (action == 'create') {
            $('#dialog-usuario').dialog('option', 'title', 'Novo Usuário');
            $('#form-funcao').val('CreateUsuario');
            $('#senha-novo-usuario').attr('disabled', false);
            $('#div-senha').css('display', 'block');
        } else {
            $('#dialog-usuario').dialog('option', 'title', 'Editar Usuário');
            $('#form-funcao').val('EditUsuario');
            $('#senha-novo-usuario').attr('disabled', true);
            $('#div-senha').css('display', 'none');
        }
    }
    //end: limpar formulário

    //start: notificacao
    var Notificar = function (status, msg) {
        $("span.aguarde, div.aguarde").css("display", "none");
        if (status == 1) {
            $('.nSuccess p').html(msg);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
        } else if (status == 2) {
            $('.nWarning p').html(msg);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 3000);
        }
    }
    //end: notificacao

    //start: dialog usuário
    $("#dialog-usuario").dialog({
        autoOpen: false,
        modal: true,
        width: 'auto',
        position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function() {
                $('#form-usuario').submit();
            },	
            Cancelar: function() {
                $( this ).dialog( "close" );
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });
	
    $("#btn-novo-usuario").click(function (e) {
        e.preventDefault();
        ResetFormUsuario('create');
        $( "#dialog-usuario" ).dialog( "open" );
    });
    //end: dialog usuário

    //start: incluir/editar usuário
    $('#form-usuario').on('submit', function (e) {

        e.preventDefault();
        
        if ($('#form-usuario').valid()) {

            $("span.aguarde, div.aguarde").css("display", "block");

            var data = $('#form-usuario').serialize();

            $("#dialog-usuario").dialog("close");

            $.ajax({
                url: 'modulos/usuario/php/funcoes.php',
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableUsuarios.fnDraw();
                        Notificar(data.status, data.msg);
                    } else {
                        $("span.aguarde, div.aguarde").css("display", "none");
                        $("#dialog-usuario").dialog("open");
                        alert(data.msg);
                        //var $erro = '<ul style="display:block;"><li><label class="error">' + data.erro + '</label></li></ul>';
                        //$('#erro-validation-usuario').html($erro);
                    }
                },
                error: function (data) {
                    $("span.aguarde, div.aguarde").css("display", "none");
                    $("#dialog-usuario").dialog("open");
                    //$btn.button('reset');
                    //alert(data.responseText);
                    //var errors = data.responseText;
                    //console.log(errors);
                },
            });
        }

    });
    //end: incluir/editar usuário

    //start: exibir usuário
    $('#dTableUsuarios').on('click', '.exibir-usuario', function (e) {

        e.preventDefault();

        $("span.aguarde, div.aguarde").css("display", "block");

        var usuarioId = $(this).data('usuario-id');

        $.ajax({
            url: 'modulos/usuario/php/funcoes.php',
            type: 'post',
            dataType: 'json',
            data: { funcao: 'DetailsUsuario', usuarioId: usuarioId },
            success: function (data) {

                ResetFormUsuario('edit');

                $('#usuario-id').val(data.id);
                $('#nome').val(data.nome);
                $('#email').val(data.email);
                $('#grupo').val(data.grupo_id);
                $('#situacao').val(data.situacao);

                $("span.aguarde, div.aguarde").css("display", "none");

                $("#dialog-usuario").dialog("open");

            },
            error: function (data) {
                $("span.aguarde, div.aguarde").css("display", "none");
                $("#dialog-usuario").dialog("open");
                //$btn.button('reset');
                //alert(data.responseText);
                //var errors = data.responseText;
                //console.log(errors);
            },
        });

    });
    //end: exibir usuário

    //start: excluir usuário
    var ExcluirUsuario = function (usuarioId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            url: 'modulos/usuario/php/funcoes.php',
            type: 'post',
            dataType: 'json',
            data: { funcao: 'DeleteUsuario', usuarioId: usuarioId },
            success: function (data) {
                Notificar(data.status, data.msg);
                var indice = dTableUsuarios.fnGetPosition(document.getElementById('tbl-usuario-row-' + usuarioId));
                dTableUsuarios.fnDeleteRow(indice);
            },
            error: function (data) {
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        });
    }

    $('#dTableUsuarios').on('click', '.excluir-usuario', function (e) {

        e.preventDefault();

        var usuarioId = $(this).attr('href');

        $('#link-exc-' + usuarioId).parent().parent().attr('id', 'tbl-usuario-row-' + usuarioId);

        $("#dialog-alerta").dialog("option", "buttons", [
		{
		    text: "Sim",
		    click: function () { ExcluirUsuario(usuarioId); $("#dialog-alerta").dialog("close"); }
		},
		{
		    text: "Não",
		    click: function () { $("#dialog-alerta").dialog("close"); }
		}
        ]);

        $('#dialog-alerta').html("<br/> Confirmar exclusão?");

        $('#dialog-alerta').dialog('open');

    });
    //end: excluir usuário

    //start: datatable usuários
    var dTableUsuarios = $('#dTableUsuarios').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'modulos/usuario/php/funcoes.php?funcao=DataTableUsuarios',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bInfo: false,
        //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
        aoColumns: [
            { "mData": "usuario", "sClass": "updates newUpdate" },
            //{ "mData": "options", "sClass": "actions" },
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

            aoData.push(/*{ "name": "filtro", "value": filtro },*/ { "name": "iDisplayStart", "value": iDisplayStart });

        },
        fnDrawCallback: function () {
            crud = false;
            //$('#btn-filtro').button('reset');
        }
    });

    $('#dTableUsuarios > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

    //end: datatable usuários

    //GRUPOS DE USUÁRIO
    //============================================================================================================================================

    //start: limpar formulário de grupo
    var ResetFormGrupo = function (action) {
        //Resetar validação
        var validator = $('#form-grupo').validate();
        validator.resetForm();
        //Resetar campos para o estado inicial
        $("#form-grupo")[0].reset();
        //Resetar checkbox no nome do módulo
        $('.modulo').data('checked', false);
        //Configurar form para inclusão ou edição
        if (action == 'create') {
            $('#dialog-grupo').dialog('option', 'title', 'Novo Grupo');
            $('#form-grupo-funcao').val('CreateGrupo');
            $('#grupo-id').val('');
        } else {
            $('#dialog-grupo').dialog('option', 'title', 'Editar Grupo');
            $('#form-grupo-funcao').val('EditGrupo');
        }
    }
    //end: limpar formulário de grupo

    //start: notificacao
    var Notificar = function (status, msg) {
        $("span.aguarde, div.aguarde").css("display", "none");
        if (status == 1) {
            $('.nSuccess p').html(msg);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
        } else if (status == 2) {
            $('.nWarning p').html(msg);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 3000);
        }
    }
    //end: notificacao

    //start: dialog grupo
    $("#dialog-grupo").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                $('#form-grupo').submit();
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#btn-novo-grupo").click(function (e) {
        e.preventDefault();
        ResetFormGrupo('create');
        $("#dialog-grupo").dialog("open");
    });
    //end: dialog grupo

    //start: retornar grupos de usuário para o modal de novo usuário
    var AddOptionGrupo = function (grupos) {
        $('#grupo').html('');
        $('#grupo').append('<option value=""></option><option value="1">Administrador</option>');
        for (var grupo in grupos) {
            $('#grupo').append('<option value="' + grupos[grupo].id + '">' + grupos[grupo].nome + '</option>');
        }
    }

    $.ajax({
        url: 'modulos/usuario/php/funcoes.php',
        type: 'post',
        dataType: 'json',
        data: { funcao: 'GetGrupos' },
        success: function (data) {
            //console.log(JSON.stringify(data));
            AddOptionGrupo(data)
        },
        error: function (data) {
        },
    });
    //end: retornar grupos de usuário para o modal de novo usuário

    //start: incluir/editar grupo
    $('#form-grupo').on('submit', function (e) {

        e.preventDefault();

        if ($('#form-grupo').valid()) {

            $("span.aguarde, div.aguarde").css("display", "block");

            var data = $('#form-grupo').serialize();

            $("#dialog-grupo").dialog("close");

            //Pega as permissões selecionadas
            var permissoes = [];
            $('.role-id').each(function () {
                if ($(this).is(':checked')) {
                    permissoes.push({ 'id': $(this).val() });
                }
            })
            permissoes = JSON.stringify(permissoes);

            $.ajax({
                url: 'modulos/usuario/php/funcoes.php',
                type: 'post',
                dataType: 'json',
                data: {
                    funcao: $('#form-grupo-funcao').val(),
                    id: $('#grupo-id').val(),
                    nome: $('#nome-grupo').val(),
                    permissoes: permissoes
                },
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableGrupos.fnDraw();
                        Notificar(data.status, data.msg);
                        AddOptionGrupo(data.grupos);
                    } else {
                        $("span.aguarde, div.aguarde").css("display", "none");
                        $("#dialog-grupo").dialog("open");
                        alert(data.msg);
                        //var $erro = '<ul style="display:block;"><li><label class="error">' + data.erro + '</label></li></ul>';
                        //$('#erro-validation-grupo').html($erro);
                    }
                },
                error: function (data) {
                    $("span.aguarde, div.aguarde").css("display", "none");
                    $("#dialog-grupo").dialog("open");
                    //$btn.button('reset');
                    //alert(data.responseText);
                    //var errors = data.responseText;
                    //console.log(errors);
                },
            });
        }

    });
    //end: incluir/editar grupo

    //start: exibir grupo
    $('#dTableGrupos').on('click', '.exibir-grupo', function (e) {

        e.preventDefault();

        $("span.aguarde, div.aguarde").css("display", "block");

        var grupoId = $(this).data('grupo-id');

        $.ajax({
            url: 'modulos/usuario/php/funcoes.php',
            type: 'post',
            dataType: 'json',
            data: { funcao: 'DetailsGrupo', grupoId: grupoId },
            success: function (data) {

                ResetFormGrupo('edit');

                $('#grupo-id').val(data.id);
                $('#nome-grupo').val(data.nome);

                for (var roleId in data.permissoes) {
                    $('#role-' + data.permissoes[roleId]).prop('checked', true);
                }

                //start: Define estado checked inicial do modulo
                $('.modulo').each(function (e) {
                    var moduloClass = $(this).data('modulo-class');
                    var fullChecked = true;
                    $('.' + moduloClass).each(function (e) {
                        if (!$(this).prop('checked')) {
                            fullChecked = false;
                            $('#' + moduloClass).data('checked', false);
                        }
                    });
                    if (fullChecked)
                        $('#' + moduloClass).data('checked', true);
                });
                //end: Define estado checked inicial do modulo
                
                $("span.aguarde, div.aguarde").css("display", "none");

                $("#dialog-grupo").dialog("open");

            },
            error: function (data) {
                $("span.aguarde, div.aguarde").css("display", "none");
                $("#dialog-grupo").dialog("open");
            },
        });

    });
    //end: exibir grupo

    //start: excluir grupo
    var ExcluirGrupo = function (grupoId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            url: 'modulos/usuario/php/funcoes.php',
            type: 'post',
            dataType: 'json',
            data: { funcao: 'DeleteGrupo', grupoId: grupoId },
            success: function (data) {
                Notificar(data.status, data.msg);
                var indice = dTableGrupos.fnGetPosition(document.getElementById('tbl-grupo-row-' + grupoId));
                dTableGrupos.fnDeleteRow(indice);
            },
            error: function (data) {
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        });
    }

    $('#dTableGrupos').on('click', '.excluir-grupo', function (e) {

        e.preventDefault();

        var grupoId = $(this).attr('href');

        $('#link-exc-' + grupoId).parent().parent().attr('id', 'tbl-grupo-row-' + grupoId);

        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirGrupo(grupoId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);

        $('#dialog-alerta').html("<br/> Confirmar exclusão?");

        $('#dialog-alerta').dialog('open');

    });
    //end: excluir grupo

    //start: datatable grupos
    var dTableGrupos = $('#dTableGrupos').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'modulos/usuario/php/funcoes.php?funcao=DataTableGrupos',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bInfo: false,
        //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
        aoColumns: [
            { "mData": "grupo", "sClass": "updates newUpdate" },
            //{ "mData": "options", "sClass": "actions" },
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

            aoData.push(/*{ "name": "filtro", "value": filtro },*/ { "name": "iDisplayStart", "value": iDisplayStart });

        },
        fnDrawCallback: function () {
            crud = false;
            //$('#btn-filtro').button('reset');
        }
    });

    $('#dTableGrupos > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

    //end: datatable grupos

    //start: retornar permissões de usuário
    $.ajax({
        url: 'modulos/usuario/php/funcoes.php',
        type: 'post',
        dataType: 'json',
        data: { funcao: 'GetModulos' },
        success: function (data) {
            //console.log(JSON.stringify(data));
            var i = 1;
            for (var modulo in data) {
                var nomeModulo = '<td style="text-align:left;"><span class="modulo" data-modulo-class="modulo-' + i + '" style="cursor:pointer;" data-checked="false" id="modulo-' + i + '">' + data[modulo].modulo + '</span></td>';
                var roles = '';
                for (var role in data[modulo].permissoes) {
                    roles += '<td><input type="checkbox" value="' + data[modulo].permissoes[role] + '" class="modulo-' + i + ' role-id" id="role-' + data[modulo].permissoes[role] + '" data-modulo-id="modulo-' + i + '"></td>'
                }
                $('#tbl-modulos').append('<tr>' + nomeModulo + roles + '</tr>');
                i++;
            }
        },
        error: function (data) {
        },
    });
    //end: retornar permissões de usuário

    //start: selecionar roles do módulo clicado
    $('#tbl-modulos').on('click', '.modulo', function () {
        var moduleClass = $(this).data('modulo-class');
        var isChecked = $(this).data('checked');
        var checked = false;
        if (!isChecked) {
            checked = true;
            $(this).data('checked', true);
        } else {
            checked = false;
            $(this).data('checked', false);
        }
        $(':checkbox.' + moduleClass).each(function () {
            document.getElementById(this.id).checked = checked;
        });
    });

    $('#tbl-modulos').on('click', ':checkbox', function () {
        var moduloId = $(this).data('modulo-id');
        if ($(this).prop('checked'))
            $('#' + moduloId).data('checked', true);
        else
            $('#' + moduloId).data('checked', false);

    });
    //end: selecionar roles do módulo clicado

    

});

