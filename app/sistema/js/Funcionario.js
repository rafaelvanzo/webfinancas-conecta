var crud = false;

$(document).ready(function () {

    //Modal funcionário
    $("#modal-funcionario").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                $('#form-funcionario').submit();
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#open-modal-funcionario").click(function (e) {
        e.preventDefault();
        $('#form-funcionario').attr('action', 'Create');
        FormReset('form-funcionario');
        $('#modal-funcionario').dialog("option", "title", 'Novo Funcionário');
        $("#modal-funcionario").dialog("open");
    })

    //Incluir/Editar funcionário
    $('#form-funcionario').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            $("span.aguarde, div.aguarde").css("display", "block");
            
            var action = $('#form-funcionario').attr('action');

            var data = $(this).serialize();

            $('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Funcionario&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableFuncionarios.fnDraw();
                        notificacao(1, data.msg);
                    } else {
                        notificacao(2, data.msg);
                        $("#modal-funcionario").dialog("open");
                    }
                    $("span.aguarde, div.aguarde").css("display", "none");
                },
            })
        }
    });

    //Exibir funcionário
    $('#dTableFuncionarios').on('click', '.exibir-funcionario', function (e) {

        e.preventDefault();

        var id = $(this).attr('href');

        $.ajax({
            url: 'php/Route.php?Controller=Funcionario&Action=Details',
            data: {
                id: id
            },
            dataType: 'json',
            success: function (data) {
                
                FormReset('form-funcionario');

                $('#funcionario-id').val(data.id);
                $('#funcao_id01').val(data.funcao_id);
                $('#funcao').val(data.funcao);
                $('#funcao').prop('disabled', 'true');
                $('#funcao_id01_cg').css('display', 'block');
                $('#nome').val(data.nome);
                $('#nome-pai').val(data.nome_pai);
                $('#nome-mae').val(data.nome_mae);
                $('#dt-nasc').val(data.dt_nasc);
                $('#cidade-nasc').val(data.cidade_nasc);
                $('#uf-nasc').val(data.uf_nasc);
                $('#sexo').val(data.sexo);
                $('#raca').val(data.raca);
                $('#estado-civil').val(data.estado_civil);
                $('#deficiente').val(data.deficiente);
                $('#instrucao').val(data.instrucao);
                $('#rg').val(data.rg);
                $('#rg-emissor').val(data.rg_emissor);
                $('#rg-dt-emissao').val(data.rg_dt_emissao);
                $('#cpf').val(data.cpf);
                $('#pis').val(data.pis);
                $('#pis-dt-inscricao').val(data.pis_dt_inscricao);
                $('#carteira').val(data.carteira);
                $('#carteira-dt-emissao').val(data.carteira_dt_emissao);
                $('#observacao').val(data.observacao);
                $('#status').val(data.status);

                $('#logradouro').val(data.logradouro);
                $('#numero').val(data.numero);
                $('#bairro').val(data.bairro);
                $('#cidade').val(data.cidade);
                $('#uf').val(data.uf);
                $('#cep').val(data.cep);
                $('#complemento').val(data.complemento);
                $('#referencia').val(data.referencia);
                
                $('#tel01').val(data.tel01);
                $('#tel02').val(data.tel02);
                $('#email01').val(data.email01);
                $('#email02').val(data.email02);

                $('#dt-exame-admissional').val(data.dt_exame_admissional);
                $('#dt-admissao').val(data.dt_admissao);
                $('#dt-demissao').val(data.dt_demissao);
                $('#salario').val(data.salario);
                $('#tp-salario').val(data.tp_salario);
                $('#desconto-transporte').val(data.desconto_transporte);
                $('#primeiro-emprego-ano').val(data.primeiro_emprego_ano);
                $('#adicional-noturno').val(data.adicional_noturno);
                $('#sindicalizado').val(data.sindicalizado);
                $('#sindicato').val(data.sindicato);
                $('#insalubridade').val(data.insalubridade);
                $('#optante-fgts').val(data.optante_fgts);
                $('#cod-banco-fgts').val(data.cod_banco_fgts);

                $("span.aguarde, div.aguarde").css("display", "none");

                $('#modal-funcionario').dialog("option", "title", 'Editar Funcionário');

                $('#form-funcionario').attr('action', 'Edit');

                $("#modal-funcionario").dialog("open");
            },
        })
    });

    //Excluir funcionário
    $('#dTableFuncionarios').on('click', '.excluir-funcionario', function (e) {
        e.preventDefault();
        var funcionarioId = $(this).attr('href');
        $('#link-exc-' + funcionarioId).parent().parent().attr('id', 'tbl-func-row-' + funcionarioId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirFuncionario(funcionarioId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirFuncionario = function (funcionarioId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Funcionario&Action=Delete',
            data: { id: funcionarioId},
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableFuncionarios").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-func-row-' + funcionarioId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Data table funcionários
    var dTableFuncionarios = $('#dTableFuncionarios').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Funcionario&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bInfo: false,
        //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
        aoColumns: [
            { "mData": "funcionario", "sClass": "updates newUpdate" },
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

    $('#dTableFuncionarios > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

    //Mais opções
    $('#dTableFuncionarios').on('change', '.mais-opcoes', function (e) {
        var opcao = $(this).val();

        var funcionarioId = $(this).data('funcionario-id');
        var nome = $(this).data('funcionario-nome');

        if (opcao == 1) {

            FormFaltasReset('Create');
            
            $('#nome-funcionario-falta').text(nome);
        
            $('#funcionario-id-falta').val(funcionarioId);

            $("#modal-faltas").dialog("open");

        } else if (opcao == 2) {

            FormHoraExtraReset('Create');

            $('#nome-func-hora-extra').text(nome);

            $('#func-id-hora-extra').val(funcionarioId);

            $("#modal-hora-extra").dialog("open");

        } else if (opcao == 3) {

            FormSalarioReset('Create');

            $('#nome-func-salario').text(nome);

            $('#func-id-salario').val(funcionarioId);

            $("#modal-salario").dialog("open");

        } else if (opcao == 4) {

            FormAltFuncaoReset('Create');

            $('#nome-func-alt-funcao').text(nome);

            $('#func-id-alt-funcao').val(funcionarioId);

            $("#modal-alt-funcao").dialog("open");

        } else if (opcao == 5) {

            FormSindicatoReset('Create');

            $('#nome-func-sindicato').text(nome);

            $('#func-id-sindicato').val(funcionarioId);

            $("#modal-sindicato").dialog("open");

        } else if (opcao == 6) {

            FormAfastamentoReset('Create');

            $('#nome-func-afastamento').text(nome);

            $('#func-id-afastamento').val(funcionarioId);

            $("#modal-afastamento").dialog("open");

        } else if (opcao == 7) {

            FormFeriasReset('Create');

            $('#nome-func-ferias').text(nome);

            $('#func-id-ferias').val(funcionarioId);

            $("#modal-ferias").dialog("open");

        } else if (opcao == 8) {

            FormDependenteReset('Create');

            $('#nome-func-dependente').text(nome);

            $('#func-id-dependente').val(funcionarioId);

            $("#modal-dependente").dialog("open");

        }

        $(this).val(0);
    });

    //Faltas-------------------------------------------------------------------------------------------------------------------------------------------

    //Resetar form falta
    var FormFaltasReset = function (action) {
        if (action == 'Create') {
            $('#form-faltas').attr('action', 'Create');
            $('#btn-incluir-falta > span').text('Incluir');
            $('#falta-id').val('');
            $('#dt-falta').val('');
            $('#justificado').val('');
        } else {
            $('#form-faltas').attr('action', 'Edit');
            $('#btn-incluir-falta > span').text('Editar');
        }
    }

    $('#btn-limpar-falta').on('click', function (e) {
        FormFaltasReset('Create');
    });

    //Modal faltas
    $("#modal-faltas").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });
    
    //Incluír/Editar falta
    $('#btn-incluir-falta').on('click', function (e) {
        //$(this).button('loading');
        $('#form-faltas').submit();
    });

    $('#form-faltas').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-faltas').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Falta&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableFaltas.fnDraw();
                        //notificacao(1, data.msg);
                        FormFaltasReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-falta').button('reset');
                },
            })
        }
    });

    //Exibir faltas
    $('#div-faltas').on('click', '.exibir-falta', function (e) {
        e.preventDefault();

        FormFaltasReset('Edit');

        var faltaId = $(this).data('falta-id');
        var dtFalta = $(this).data('dt-falta');
        var justificado = $(this).data('justificado');

        $('#dt-falta').val(dtFalta);
        $('#falta-id').val(faltaId);
        $('#justificado').val(justificado);
    });

    //Data table faltas
    var dTableFaltas = $('#dTableFaltas').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Falta&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dt_falta", "sClass": "updates newUpdate" },
            { "mData": "justificado", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableFaltas > thead').remove(); //remove o thead

    //Excluir faltas
    $('#dTableFaltas').on('click', '.excluir-falta', function (e) {
        e.preventDefault();
        var faltaId = $(this).attr('href');
        $('#link-exc-' + faltaId).parent().parent().attr('id', 'tbl-falta-row-' + faltaId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirFalta(faltaId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirFalta = function (faltaId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Falta&Action=Delete',
            data: { id: faltaId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableFaltas").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-falta-row-' + faltaId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Horas extras -------------------------------------------------------------------------------------------------------------------------------------------

    //Modal hora extra
    $("#modal-hora-extra").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Resetar form hora extra
    var FormHoraExtraReset = function (action) {
        if (action == 'Create') {
            $('#form-hora-extra').attr('action', 'Create');
            $('#btn-incluir-hora-extra > span').text('Incluir');
            $('#hora-extra-id').val('');
            $('#qtd-hora-extra').val('');
            $('#dt-hora-extra').val('');
            $('#percent-hora-extra').val('');
        } else {
            $('#form-hora-extra').attr('action', 'Edit');
            $('#btn-incluir-hora-extra > span').text('Editar');
        }
    }

    $('#btn-limpar-hora-extra').on('click', function (e) {
        FormHoraExtraReset('Create');
    });

    //Modal hora extra
    $("#modal-hora-extra").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar hora extra
    $('#btn-incluir-hora-extra').on('click', function (e) {
        //$(this).button('loading');
        $('#form-hora-extra').submit();
    });

    $('#form-hora-extra').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-hora-extra').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=HoraExtra&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableHoraExtra.fnDraw();
                        //notificacao(1, data.msg);
                        FormHoraExtraReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-hora-extra').button('reset');
                },
            })
        }
    });

    //Exibir hora extra
    $('#div-hora-extra').on('click', '.exibir-hora-extra', function (e) {
        e.preventDefault();

        FormHoraExtraReset('Edit');

        var horaExtraId = $(this).data('hora-extra-id');
        var dtHoraExtra = $(this).data('dt-hora-extra');
        var qtdHoraExtra = $(this).data('qtd-hora-extra');
        var percentHoraExtra = $(this).data('percent-hora-extra');
        
        $('#hora-extra-id').val(horaExtraId);
        $('#dt-hora-extra').val(dtHoraExtra);
        $('#qtd-hora-extra').val(qtdHoraExtra);
        $('#percent-hora-extra').val(percentHoraExtra);
    });

    //Data table hora extra
    var dTableHoraExtra = $('#dTableHoraExtra').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=HoraExtra&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dtHoraExtra", "sClass": "updates newUpdate" },
            { "mData": "qtdHoraExtra", "sClass": "updates newUpdate" },
            { "mData": "percentual", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableFaltas > thead').remove(); //remove o thead

    //Excluir hora extra
    $('#dTableHoraExtra').on('click', '.excluir-hora-extra', function (e) {
        e.preventDefault();
        var horaExtraId = $(this).attr('href');
        $('#link-exc-hora-extra-' + horaExtraId).parent().parent().attr('id', 'tbl-hora-extra-row-' + horaExtraId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirHoraExtra(horaExtraId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirHoraExtra = function (horaExtraId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=HoraExtra&Action=Delete',
            data: { id: horaExtraId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableHoraExtra").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-hora-extra-row-' + horaExtraId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Alteração salarial-------------------------------------------------------------------------------------------------------------------------------------------

    //Resetar form salário
    var FormSalarioReset = function (action) {
        if (action == 'Create') {
            $('#form-salario').attr('action', 'Create');
            $('#btn-incluir-salario > span').text('Incluir');
            $('#salario-id').val('');
            $('#dt-alteracao').val('');
            $('#valor-salario').val('');
        } else {
            $('#form-salario').attr('action', 'Edit');
            $('#btn-incluir-salario > span').text('Editar');
        }
    }

    $('#btn-limpar-salario').on('click', function (e) {
        FormSalarioReset('Create');
    });

    //Modal salario
    $("#modal-salario").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar salario
    $('#btn-incluir-salario').on('click', function (e) {
        //$(this).button('loading');
        $('#form-salario').submit();
    });

    $('#form-salario').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-salario').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Salario&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableSalario.fnDraw();
                        //notificacao(1, data.msg);
                        FormSalarioReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-salario').button('reset');
                },
            })
        }
    });

    //Exibir salario
    $('#div-salario').on('click', '.exibir-salario', function (e) {
        e.preventDefault();

        FormSalarioReset('Edit');

        var salarioId = $(this).data('salario-id');
        var dtAlteracao = $(this).data('dt-alteracao');
        var valor = $(this).data('valor');

        $('#dt-alteracao').val(dtAlteracao);
        $('#salario-id').val(salarioId);
        $('#valor-salario').val(valor);
    });

    //Data table salario
    var dTableSalario = $('#dTableSalario').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Salario&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dt_alteracao", "sClass": "updates newUpdate" },
            { "mData": "valor", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableSalario > thead').remove(); //remove o thead

    //Excluir salario
    $('#dTableSalario').on('click', '.excluir-salario', function (e) {
        e.preventDefault();
        var salarioId = $(this).attr('href');
        $('#link-exc-' + salarioId).parent().parent().attr('id', 'tbl-salario-row-' + salarioId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirSalario(salarioId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirSalario = function (salarioId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Salario&Action=Delete',
            data: { id: salarioId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableSalario").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-salario-row-' + salarioId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Alteração de função -----------------------------------------------------------------------------------------------------------------------------

    //Resetar form alteração de função
    var FormAltFuncaoReset = function (action) {
        if (action == 'Create') {
            $('#form-alt-funcao').attr('action', 'Create');
            $('#btn-incluir-alt-funcao > span').text('Incluir');
            $('#input-funcao-id02').val('');
            $('#funcao_id02').val('');
            $('#dt-alteracao-alt-funcao').val('');
            $('#valor-alt-funcao').val('');
            $('span.check-green').css('display', 'none');
            $('.input-buscar').attr('disabled', false);
        } else {
            $('#form-alt-funcao').attr('action', 'Edit');
            $('#btn-incluir-alt-funcao > span').text('Editar');
        }
    }

    $('#btn-limpar-alt-funcao').on('click', function (e) {
        FormAltFuncaoReset('Create');
    });

    //Modal alteração de função
    $("#modal-alt-funcao").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar alteração de função
    $('#btn-incluir-alt-funcao').on('click', function (e) {
        //$(this).button('loading');
        $('#form-alt-funcao').submit();
    });

    $('#form-alt-funcao').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-alt-funcao').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Funcao&Action=' + action + 'AltFuncao',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableAltFuncao.fnDraw();
                        //notificacao(1, data.msg);
                        FormAltFuncaoReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-alt-funcao').button('reset');
                },
            })
        }
    });

    //Exibir alteração de função
    $('#div-alt-funcao').on('click', '.exibir-alt-funcao', function (e) {
        e.preventDefault();

        FormAltFuncaoReset('Edit');

        var alteracaoId = $(this).data('alteracao-id');
        var funcaoId = $(this).data('funcao-id');
        var funcao = $(this).data('funcao');
        var dtAlteracao = $(this).data('dt-alteracao');

        $('#alt-funcao-id').val(alteracaoId);
        $('#funcao_id02').val(funcaoId);
        $('#input-funcao-id02').val(funcao);
        $('#dt-alteracao-alt-funcao').val(dtAlteracao);

        $('#funcao_id02_cg').css('display', 'block');
        $('#input-funcao-id02').attr('disabled', true);
    });

    //Data table alteração de função
    var dTableAltFuncao = $('#dTableAltFuncao').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Funcao&Action=DataTableAltFuncao',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dt_alteracao", "sClass": "updates newUpdate" },
            { "mData": "funcao", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableAltFuncao > thead').remove(); //remove o thead

    //Excluir alteração de função
    $('#dTableAltFuncao').on('click', '.excluir-alt-funcao', function (e) {
        e.preventDefault();
        var altFuncaoId = $(this).attr('href');
        $('#link-exc-' + altFuncaoId).parent().parent().attr('id', 'tbl-alt-funcao-row-' + altFuncaoId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirAltFuncao(altFuncaoId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirAltFuncao = function (altFuncaoId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Funcao&Action=DeleteAltFuncao',
            data: { id: altFuncaoId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableAltFuncao").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-alt-funcao-row-' + altFuncaoId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Autocomplete função
    var IncluirFuncaoAc = function (nome, campo_id) {
        //$("span.aguarde, div.aguarde").css("display", "block");
        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Funcao&Action=IncluirFuncaoAc',
            data: { nome: nome },
            dataType: 'json',
            success: function (data) {
                //data = JSON.parse(data);
                document.getElementById(campo_id).value = data.funcao_id;
                //$("span.aguarde, div.aguarde").css("display", "none");
            },
        });
    }

    $(".funcao_buscar").autocomplete({
        minLength: 0,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache ) {
            //response( cache[ term ] );
            //return;
            //}
            $.getJSON('php/Route.php?Controller=Funcao&Action=AutoCompleteFuncao', request, function (data, status, xhr) {
                //cache[ term ] = data;
                response(data);
            });
        },
        search: function (event, ui) {
            var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
            $('#' + campo_id + '_aguarde').css('display', 'block');
        },
        response: function (event, ui) {
            var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
            $('#' + campo_id + '_aguarde').css('display', 'none');
            //if(ui.content.length==0){
            //alert('nenhum resultado encontrado');
            //}
            //alert('resposta');
        },
        select: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id).val(ui.item.id);
            if (ui.item.id == "add")
                IncluirFuncaoAc(ui.item.value, campo_id);
            $('#' + campo_id + '_cg').css('display', 'block');
            $(this).attr('disabled', 'disabled');
            fadeOut($(this).attr('id'));
        }
    });

    //Contribuição sindical -------------------------------------------------------------------------------------------------------------------------------------------

    //Resetar form sindicato
    var FormSindicatoReset = function (action) {
        if (action == 'Create') {
            $('#form-sindicato').attr('action', 'Create');
            $('#btn-incluir-sindicato > span').text('Incluir');
            $('#sindicato-id').val('');
            $('#guia-sindicato').val('');
            $('#dt-contribuicao').val('');
            $('#valor-sindicato').val('');
            $('#nome-sindicato').val('');
        } else {
            $('#form-sindicato').attr('action', 'Edit');
            $('#btn-incluir-sindicato > span').text('Editar');
        }
    }

    $('#btn-limpar-sindicato').on('click', function (e) {
        FormSindicatoReset('Create');
    });

    //Modal sindicato
    $("#modal-sindicato").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar sindicato
    $('#btn-incluir-sindicato').on('click', function (e) {
        //$(this).button('loading');
        $('#form-sindicato').submit();
    });

    $('#form-sindicato').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-sindicato').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Sindicato&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableSindicato.fnDraw();
                        //notificacao(1, data.msg);
                        FormSindicatoReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-sindicato').button('reset');
                },
            })
        }
    });

    //Exibir sindicato
    $('#div-sindicato').on('click', '.exibir-sindicato', function (e) {
        e.preventDefault();

        FormSindicatoReset('Edit');

        var sindicatoId = $(this).data('sindicato-id');
        var guia = $(this).data('guia');
        var dtContribuicao = $(this).data('dt-contribuicao');
        var valor = $(this).data('valor');
        var sindicato = $(this).data('sindicato');

        $('#guia-sindicato').val(guia);
        $('#dt-contribuicao').val(dtContribuicao);
        $('#sindicato-id').val(sindicatoId);
        $('#valor-sindicato').val(valor);
        $('#nome-sindicato').val(sindicato);
    });

    //Data table sindicato
    var dTableSindicato = $('#dTableSindicato').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Sindicato&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "guia", "sClass": "updates newUpdate" },
            { "mData": "dt_contribuicao", "sClass": "updates newUpdate" },
            { "mData": "valor", "sClass": "updates newUpdate" },
            { "mData": "sindicato", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableSindicato > thead').remove(); //remove o thead

    //Excluir sindicato
    $('#dTableSindicato').on('click', '.excluir-sindicato', function (e) {
        e.preventDefault();
        var sindicatoId = $(this).attr('href');
        $('#link-exc-' + sindicatoId).parent().parent().attr('id', 'tbl-sindicato-row-' + sindicatoId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirSindicato(sindicatoId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirSindicato = function (sindicatoId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Sindicato&Action=Delete',
            data: { id: sindicatoId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableSindicato").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-sindicato-row-' + sindicatoId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Afastamento -------------------------------------------------------------------------------------------------------------------------------------------

    //Resetar form afastamento
    var FormAfastamentoReset = function (action) {
        if (action == 'Create') {
            $('#form-afastamento').attr('action', 'Create');
            $('#btn-incluir-afastamento > span').text('Incluir');
            $('#afastamento-id').val('');
            $('#motivo').val('');
            $('#dt-ocorrencia').val('');
            $('#dt-alta').val('');
        } else {
            $('#form-afastamento').attr('action', 'Edit');
            $('#btn-incluir-afastamento > span').text('Editar');
        }
    }

    $('#btn-limpar-afastamento').on('click', function (e) {
        FormAfastamentoReset('Create');
    });

    //Modal afastamento
    $("#modal-afastamento").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar afastamento
    $('#btn-incluir-afastamento').on('click', function (e) {
        //$(this).button('loading');
        $('#form-afastamento').submit();
    });

    $('#form-afastamento').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-afastamento').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Afastamento&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableAfastamento.fnDraw();
                        //notificacao(1, data.msg);
                        FormAfastamentoReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-afastamento').button('reset');
                },
            })
        }
    });

    //Exibir afastamento
    $('#div-afastamento').on('click', '.exibir-afastamento', function (e) {
        e.preventDefault();

        FormAfastamentoReset('Edit');

        var afastamentoId = $(this).data('afastamento-id');
        var motivo = $(this).data('motivo');
        var dtOcorrencia = $(this).data('dt-ocorrencia');
        var dtAlta = $(this).data('dt-alta');

        $('#afastamento-id').val(afastamentoId);
        $('#motivo').val(motivo);
        $('#dt-ocorrencia').val(dtOcorrencia);
        $('#dt-alta').val(dtAlta);
    });

    //Data table afastamento
    var dTableAfastamento = $('#dTableAfastamento').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Afastamento&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "motivo", "sClass": "updates newUpdate" },
            { "mData": "dt_ocorrencia", "sClass": "updates newUpdate" },
            { "mData": "dt_alta", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableAfastamento > thead').remove(); //remove o thead

    //Excluir afastamento
    $('#dTableAfastamento').on('click', '.excluir-afastamento', function (e) {
        e.preventDefault();
        var afastamentoId = $(this).attr('href');
        $('#link-exc-' + afastamentoId).parent().parent().attr('id', 'tbl-afastamento-row-' + afastamentoId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirAfastamento(afastamentoId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirAfastamento = function (afastamentoId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Afastamento&Action=Delete',
            data: { id: afastamentoId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableAfastamento").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-afastamento-row-' + afastamentoId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Férias -------------------------------------------------------------------------------------------------------------------------------------------

    //Resetar form férias
    var FormFeriasReset = function (action) {
        if (action == 'Create') {
            $('#form-ferias').attr('action', 'Create');
            $('#btn-incluir-ferias > span').text('Incluir');
            $('#ferias-id').val('');
            $('#dt-periodo-ini').val('');
            $('#dt-periodo-fim').val('');
            $('#dt-ferias-ini').val('');
            $('#dt-ferias-fim').val('');
        } else {
            $('#form-ferias').attr('action', 'Edit');
            $('#btn-incluir-ferias > span').text('Editar');
        }
    }

    $('#btn-limpar-ferias').on('click', function (e) {
        FormFeriasReset('Create');
    });

    //Modal ferias
    $("#modal-ferias").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar ferias
    $('#btn-incluir-ferias').on('click', function (e) {
        //$(this).button('loading');
        $('#form-ferias').submit();
    });

    $('#form-ferias').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-ferias').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Ferias&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableFerias.fnDraw();
                        //notificacao(1, data.msg);
                        FormFeriasReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-ferias').button('reset');
                },
            })
        }
    });

    //Exibir ferias
    $('#div-ferias').on('click', '.exibir-ferias', function (e) {
        e.preventDefault();

        FormFeriasReset('Edit');

        var feriasId = $(this).data('ferias-id');
        var dtPeriodoIni = $(this).data('dt-periodo-ini');
        var dtPeriodoFim = $(this).data('dt-periodo-fim');
        var dtFeriasIni = $(this).data('dt-ferias-ini');
        var dtFeriasFim = $(this).data('dt-ferias-fim');

        $('#ferias-id').val(feriasId);
        $('#dt-periodo-ini').val(dtPeriodoIni);
        $('#dt-periodo-fim').val(dtPeriodoFim);
        $('#dt-ferias-ini').val(dtFeriasIni);
        $('#dt-ferias-fim').val(dtFeriasFim);
    });

    //Data table ferias
    var dTableFerias = $('#dTableFerias').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Ferias&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dt_periodo_ini", "sClass": "updates newUpdate" },
            { "mData": "dt_periodo_fim", "sClass": "updates newUpdate" },
            { "mData": "dt_ferias_ini", "sClass": "updates newUpdate" },
            { "mData": "dt_ferias_fim", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableFerias > thead').remove(); //remove o thead

    //Excluir ferias
    $('#dTableFerias').on('click', '.excluir-ferias', function (e) {
        e.preventDefault();
        var feriasId = $(this).attr('href');
        $('#link-exc-' + feriasId).parent().parent().attr('id', 'tbl-ferias-row-' + feriasId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirFerias(feriasId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirFerias = function (feriasId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Ferias&Action=Delete',
            data: { id: feriasId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableFerias").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-ferias-row-' + feriasId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

    //Dependente -------------------------------------------------------------------------------------------------------------------------------------------

    //Resetar form dependente
    var FormDependenteReset = function (action) {
        if (action == 'Create') {
            $('#form-dependente').attr('action', 'Create');
            $('#btn-incluir-dependente > span').text('Incluir');
            $('#dependente-id').val('');
            $('#dep-nome').val('');
            $('#dep-dt-nascimento').val('');
            $('#dep-dt-registro').val('');
            $('#dep-cartorio').val('');
            $('#dep-sexo').val('');
        } else {
            $('#form-dependente').attr('action', 'Edit');
            $('#btn-incluir-dependente > span').text('Editar');
        }
    }

    $('#btn-limpar-dependente').on('click', function (e) {
        FormDependenteReset('Create');
    });

    //Modal dependente
    $("#modal-dependente").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Fechar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    //Incluír/Editar dependente
    $('#btn-incluir-dependente').on('click', function (e) {
        //$(this).button('loading');
        $('#form-dependente').submit();
    });

    $('#form-dependente').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            //$("span.aguarde, div.aguarde").css("display", "block");

            var action = $('#form-dependente').attr('action');

            var data = $(this).serialize();

            //$('#modal-funcionario').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Dependente&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        crud = true;
                        dTableDependente.fnDraw();
                        //notificacao(1, data.msg);
                        FormDependenteReset('Create');
                    } else {
                        //notificacao(2, data.msg);
                        //$("#modal-funcionario").dialog("open");
                    }
                    //$("span.aguarde, div.aguarde").css("display", "none");
                    $('#btn-incluir-dependente').button('reset');
                },
            })
        }
    });

    //Exibir dependente
    $('#div-dependente').on('click', '.exibir-dependente', function (e) {
        e.preventDefault();

        FormDependenteReset('Edit');

        var dependenteId = $(this).data('dependente-id');
        var nome = $(this).data('nome');
        var dtNascimento = $(this).data('dt-nascimento');
        var dtRegistro = $(this).data('dt-registro');
        var cartorio = $(this).data('cartorio');
        var sexo = $(this).data('sexo');

        $('#dependente-id').val(dependenteId);
        $('#dep-nome').val(nome);
        $('#dep-dt-nascimento').val(dtNascimento);
        $('#dep-dt-registro').val(dtRegistro);
        $('#dep-cartorio').val(cartorio);
        $('#dep-sexo').val(sexo);
    });

    //Data table dependente
    var dTableDependente = $('#dTableDependente').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Dependente&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "nome", "sClass": "updates newUpdate" },
            { "mData": "dt_nascimento", "sClass": "updates newUpdate" },
            { "mData": "dt_registro", "sClass": "updates newUpdate" },
            { "mData": "cartorio", "sClass": "updates newUpdate" },
            { "mData": "sexo", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate" }
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

    //$('#dTableDependente > thead').remove(); //remove o thead

    //Excluir dependente
    $('#dTableDependente').on('click', '.excluir-dependente', function (e) {
        e.preventDefault();
        var dependenteId = $(this).attr('href');
        $('#link-exc-' + dependenteId).parent().parent().attr('id', 'tbl-dependente-row-' + dependenteId);
        $("#dialog-alerta").dialog("option", "buttons", [
        {
            text: "Sim",
            click: function () { ExcluirDependente(dependenteId); $("#dialog-alerta").dialog("close"); }
        },
        {
            text: "Não",
            click: function () { $("#dialog-alerta").dialog("close"); }
        }
        ]);
        $('#dialog-alerta').html("<br/> Confirmar exclusão?");
        $('#dialog-alerta').dialog('open');
    });

    var ExcluirDependente = function (dependenteId) {

        $("span.aguarde, div.aguarde").css("display", "block");

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Dependente&Action=Delete',
            data: { id: dependenteId },
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    var tabela = $("#dTableDependente").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-dependente-row-' + dependenteId));
                    tabela.fnDeleteRow(indice);
                }
                notificacao(data.status, data.msg);
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

})

//Formatar porcentagem
function Porcentagem(inputId) {

    setTimeout(function () {

        var num, centena;

        num = $('#' + inputId).val();

        num = num.replace(/\./g, '');

        centena = num.substr(0, 4);

        if (parseInt(centena) == 1000) {

            num = 100;

        } else {

            num = num.substr(0, 2) + '.' + num.substr(2);

        }

        $('#' + inputId).val(num);

    }, 50);

}

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