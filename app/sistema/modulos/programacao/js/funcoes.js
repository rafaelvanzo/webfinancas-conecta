// JavaScript Document
var dados_global;
var crud = false;

/*
========================================================================================================================
REQUISICAO AJAX
========================================================================================================================
*/

function ajax_jquery(params, funcao_retorno) {

    /*
    params += "&bd_web_financas="+$('#bd_web_financas').val();
    params += "&id_usuario="+$('#id_usuario').val();
    params += "&id_dependente="+$('#id_dependente').val();
    */

    $.ajax({

        type: 'post', //Tipo do envio das informações GET ou POST
        url: 'modulos/programacao/php/funcoes.php', //url para onde será enviada as informações digitadas
        data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
        cache: true,

        beforeSend: function () {
            //Ação que será executada após o envio, no caso, chamei um gif loading para dar a impressão de garregamento na página
            //carregando();
        },

        //function(data) vide item 4 em $.get $.post
        success: function (data) {
            dados_global = data;
            eval("(" + funcao_retorno + ")");
        },

        // Se acontecer algum erro é executada essa função
        error: function (erro) {
        }

    })

}

/*
===========================================================================================
MENSAGEM D ENOTIFICAÇÃO
===========================================================================================
*/

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

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

$(document).ready(function (e) {

    //===== UI dialog - Incluir recebimento =====//

    $("#dialog-rcbt-rcr-incluir").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {                 
                lancamentoIncluir("form_rcbt", "dialog-rcbt-rcr-incluir");
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#opener-rcbt-rcr-incluir").click(function () {
        lancamentosLimpar('form_rcbt');
        $("#dados").data("form-ativo", "form_rcbt");
        $("#dialog-rcbt-rcr-incluir").dialog("open");
        return false;
    });

    //===== UI dialog - Editar recebimento =====//

    $("#dialog-rcbt-rcr-editar").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                lancamentoEditar("form_rcbt_editar", "dialog-rcbt-rcr-editar");
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#opener-rcbt-rcr-editar").click(function () {
        $("#dados").data("form-ativo", "form_rcbt_editar");
        $("#dialog-rcbt-rcr-editar").dialog("open");
        return false;
    });

    //===== UI dialog - Incluir pagamento =====//

    $("#dialog-pgto-rcr-incluir").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                lancamentoIncluir("form_pgto", "dialog-pgto-rcr-incluir");
            },
            Cancelar: function () {
                $(this).dialog("close");
                lancamentosLimpar('form_pgto');
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#opener-pgto-rcr-incluir").click(function () {
        lancamentosLimpar('form_pgto');
        $("#dados").data("form-ativo", "form_pgto");
        $("#dialog-pgto-rcr-incluir").dialog("open");
        return false;
    });

    //===== UI dialog - Editar pagamento =====//

    $("#dialog-pgto-rcr-editar").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                lancamentoEditar("form_pgto_editar", "dialog-pgto-rcr-editar");
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });

    $("#opener-pgto-rcr-editar").click(function () {
        $("#dados").data("form-ativo", "form_pgto_editar");
        $("#dialog-pgto-rcr-editar").dialog("open");
        return false;
    });

});

/*
========================================================================================================================
AUTO COMPLETAR
========================================================================================================================
*/

$(document).ready(function () {

    //======== CENTRO DE RESPONSABILIDADE ===================
    //var cache3 = {};

    $(".centro_resp_buscar").autocomplete({
        minLength: 1,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache3 ) {
            //response( cache3[ term ] );
            //return;
            //}
            $.getJSON("modulos/lancamento/paginas/centro_resp_buscar.php", request, function (data, status, xhr) {
                //cache3[ term ] = data;
                response(data);
            });
        },
        search: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id + '_aguarde').css('display', 'block');
        },
        response: function (event, ui) {
            //alert(ui);
            var campo_id = $(this).attr('name');
            $('#' + campo_id + '_aguarde').css('display', 'none');
            if (ui.content.length == 0) {
                //alert('nenhum resultado encontrado');
            }
            //alert('resposta');
        },
        select: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id).val(ui.item.id);
            $('#' + campo_id + '_cg').css('display', 'block');
        }
    });
    //======== FIM COMPLETAR CENTRO DE RESPONSABILIDADE =============

    //======== PLANO DE CONTAS ===================
    //var cache4 = {};

    $(".plano_contas_buscar").autocomplete({
        minLength: 1,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache4 ) {
            //response( cache4[ term ] );
            //return;
            //}
            $.getJSON("modulos/lancamento/paginas/plano_contas_buscar.php", request, function (data, status, xhr) {
                //cache4[ term ] = data;
                response(data);
            });
        },
        search: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id + '_aguarde').css('display', 'block');
        },
        response: function (event, ui) {
            //alert(ui);
            var campo_id = $(this).attr('name');
            $('#' + campo_id + '_aguarde').css('display', 'none');
            if (ui.content.length == 0) {
                //alert('nenhum resultado encontrado');
            }
            //alert('resposta');
        },
        select: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id).val(ui.item.id);
            $('#' + campo_id + '_cg').css('display', 'block');
        }
    });
    //======== FIM COMPLETAR PLANO DE CONTAS =============


});

/*
===========================================================================================
REDESENHAR DATA TABLE LANÇAMENTOS
===========================================================================================
*/

function dTable() {
    /*
    oTable = $('.dTable').dataTable({
        "bJQueryUI": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sDom": '<"itemsPerPage"fl>t<"F"ip>',
        //"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
        "oLanguage": {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        }
    });
    ativarCROT('t');// Reaplicando Chackbox, Radio e Title
    */
}

/*
===========================================================================================
ALTERAR FREQUÊNCIA DO RECEBIMENTO
===========================================================================================
*/

$(document).ready(function () {

    //$('#dt_competencia_editar').change(function(){
    //$('#dt_alterada').val(1);
    //})

    $('.dt_inicio_editar').change(function () {
        var form_id = $(this).data("form-id");
        $('#' + form_id + '_dt_alterada').val(1);
    })

    $('.dia_mes, .dia_semana, .dia_mes_editar, .dia_semana_editar').change(function () {
        var form_id = $(this).data("form-id");
        $('#' + form_id + '_dt_inicio').val('');
        $('#' + form_id + '_dt_alterada').val(1);
    })

    $('.frequencia, .frequencia_editar').change(function () {
        var form_id = $(this).data("form-id");
        $('#' + form_id + '_dt_alterada').val(1);
        if ($(this).val() == '0') {
            $('#' + form_id + '_dia_mes').val('');
            $('#' + form_id + '_dia_semana').val('');
            $('#' + form_id + '_dt_inicio').val('');
            $('#' + form_id + '_span_dia_vencimento').css('display', 'none');
            $('#' + form_id + '_span_qtd_dias').css('display', 'inline-block');
        } else if ($(this).val() == 7) {
            $('#' + form_id + '_dia_mes').val('');
            $('#' + form_id + '_qtd_dias input[name="qtd_dias"]').val('');
            $('#' + form_id + '_dt_inicio').val('');
            $('#' + form_id + '_dia_mes').css('display', 'none');
            $('#' + form_id + '_span_qtd_dias').css('display', 'none');
            $('#' + form_id + '_span_dia_vencimento').css('display', 'inline-block');
            $('#' + form_id + '_dia_semana').css('display', 'block');
        } else {
            $('#' + form_id + '_dia_semana').val('');
            $('#' + form_id + '_qtd_dias input[name="qtd_dias"]').val('');
            $('#' + form_id + '_dt_inicio').val('');
            $('#' + form_id + '_dia_semana').css('display', 'none');
            $('#' + form_id + '_span_qtd_dias').css('display', 'none');
            $('#' + form_id + '_span_dia_vencimento').css('display', 'inline-block');
            $('#' + form_id + '_dia_mes').css('display', 'block');
        }
    })

    function desabilitaDia(date) {
        var form_id = $("#dados").data("form-ativo");
        if ($('#' + form_id + '_frequencia').val() == '0') {
            var hoje = new Date();
            if (date > hoje) {
                return [true];
            }
            return [false];
        } else if ($('#' + form_id + '_frequencia').val() == '7') {
            var hoje = new Date();
            var dia_vencimento = $('#' + form_id + '_dia_semana').val();
            if (dia_vencimento == 7)
                dia_vencimento = 0;
            if ((date.getDay() == dia_vencimento) && (date > hoje)) {
                return [true];
            }
            return [false];
        } else {
            var hoje = new Date();
            var dia_vencimento = $('#' + form_id + '_dia_mes').val();
            var qtd_dias = (new Date(date.getYear(), date.getMonth() + 1, 0)).getDate(); //o parametro zero faz retornar o ultimo dia do mes anterior, por isso é necessario especificar o mes seguinte e assim pegar a quantidade de dias do mes atual
            if (parseInt(dia_vencimento) > parseInt(qtd_dias)) {
                if ((date.getDate() == qtd_dias) && (date > hoje)) {
                    return [true];
                }
                return [false];
            } else {
                if ((date.getDate() == dia_vencimento) && (date > hoje)) {
                    return [true];
                }
                return [false];
            }
        }
    }

    $(".dt_inicio, .dt_inicio_editar").datepicker({
        beforeShowDay: desabilitaDia,
        changeMonth: true,
        changeYear: true,
        dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        //defaultDate: +7,
        autoSize: true,
        //appendText: '(dd-mm-yyyy)',
        dateFormat: 'dd/mm/yy',
    });

})

/*
===========================================================================================
VALIDA DATA DE INÍCIO E DATA DE COMPETÊNCIA
===========================================================================================
*/

function validaCmpt(dt_cmpt_id, dt_ini_id) {
    var dt_cmpt = document.getElementById(dt_cmpt_id).value;
    var dt_ini = document.getElementById(dt_ini_id).value;

    var arr_dt_cmpt = dt_cmpt.split("/");
    var arr_dt_ini = dt_ini.split("/");

    dt_cmpt = new Date();
    dt_ini = new Date();

    dt_cmpt.setFullYear(arr_dt_cmpt[1], arr_dt_cmpt[0], "1");
    dt_ini.setFullYear(arr_dt_ini[2], arr_dt_ini[1], "1");

    if (dt_cmpt <= dt_ini) {
        return 1;
    } else {
        $("#dialog-alerta").dialog("option", "buttons", [
		{
		    text: "Fechar",
		    click: function () { $("#dialog-alerta").dialog("close"); }
		}
        ]);
        $('#dialog-alerta').html("<br/> A competência deve ser menor ou igual à data inicial.");
        $('#dialog-alerta').dialog('open');
        return 0;
    }
}

/*
===========================================================================================
INCLUÍR LANÇAMENTO
===========================================================================================
*/

function lancamentoIncluir(form_id, dialog_id) {
    $(".lnctValid").trigger("click"); //Muda a ABA da janela 
    if ($('#' + form_id).valid()) {
        if (validaCmpt(form_id + '_dt_competencia', form_id + '_dt_inicio')) {
            //centroRespLnctIncluir(form_id);
            $("span.aguarde, div.aguarde").css("display", "block");
            var params = $('#' + form_id).serialize();
            $("#" + dialog_id).dialog("close");
            //alert(params);
            ajax_jquery(params, "lancamentoIncluirRetorno()");
            lancamentosLimpar(form_id);
        }
    }
}

function lancamentoIncluirRetorno() {
    var dados = JSON.parse(dados_global);
    if (dados.status == 1) {
        crud = true;
        dTable.fnDraw();
        
    }
    notificacao(dados.status, dados.notificacao);
    $("span.aguarde, div.aguarde").css("display", "none");
}

/*
===========================================================================================
EDITAR LANÇAMENTO
===========================================================================================
*/

function lancamentoEditar(form_id, dialog_id) {
    if ($('#' + form_id).valid()) {
        if (validaCmpt(form_id + '_dt_competencia', form_id + '_dt_inicio')) {
            //centroRespLnctIncluir(form_id);
            $("span.aguarde, div.aguarde").css("display", "block");
            var params = $('#' + form_id).serialize();
            $("#" + dialog_id).dialog("close");
            //alert(params);
            ajax_jquery(params, "lancamentoEditarRetorno()");
        }
    }
}

function lancamentoEditarRetorno() {
    var dados = JSON.parse(dados_global);
    if (dados.status == 1) {
        crud = true;
        dTable.fnDraw();
    }
    notificacao(dados.status, dados.notificacao);
    $("span.aguarde, div.aguarde").css("display", "none");
}


/*
===========================================================================================
EXCLUÍR LANÇAMENTO
===========================================================================================
*/

$(document).ready(function () {

    $('.lancamentoExcluir').live("click", function (e) {

        e.preventDefault();

        var lnct_id = $(this).attr('href');

        $('#link-exc-' + lnct_id).parent().parent().attr('id', 'tbl-lnct-row-' + lnct_id);

        $("#dialog-alerta").dialog("option", "buttons", [
		{
		    text: "Sim",
		    click: function () { lancamentoExcluir(lnct_id); $("#dialog-alerta").dialog("close"); }
		},
		{
		    text: "Não",
		    click: function () { $("#dialog-alerta").dialog("close"); }
		}
        ]);

        $('#dialog-alerta').html("<br/> Deseja realmente excluír o registro selecionado?");

        $('#dialog-alerta').dialog('open');

    });

})

function lancamentoExcluir(lnct_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
    var params = "funcao=lancamentoExcluir&lancamento_id=" + lnct_id;
    ajax_jquery(params, "lancamentoExcluirRetorno(" + lnct_id + ")");
}

function lancamentoExcluirRetorno(lnct_id) {
    var dados = JSON.parse(dados_global);

    if (dados.status == 1) {
        var tabela = $("#dTableLnct").dataTable();
        var indice = tabela.fnGetPosition(document.getElementById('tbl-lnct-row-' + lnct_id));
        tabela.fnDeleteRow(indice);
    }

    notificacao(dados.status, dados.notificacao);

    $("span.aguarde, div.aguarde").css("display", "none");
}


/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function lancamentosLimpar(form) {
    var validator = $('#' + form).validate();

    /*
    var validator = $('#' + form).validate({
        ignore: [],
        rules: {
            form_rcbt_dia_mes: {
                required: '#form_rcbt_dia_mes:visible'
            },
            form_rcbt_intervalo: {
                required: '#form_rcbt_intervalo:visible'
            }
        }
    });
    */
    validator.resetForm();
    $("#" + form + " div.boxScroll").remove();

    //limpa centro de custo e categoria
    $("#" + form + "_ctr_plc_lnct").val("");
    $("#" + form + "_pl_conta_id").val(0);
    $("#" + form + "_ct_resp_id").val(0);

    $('span.check-green').css('display', 'none');
    $('#qtd_dias, #dia_semana').css('display', 'none');
    $('#dia_vencimento').css('display', 'inline-block');
    $('#dia_mes').css('display', 'block');
    $('.input-buscar').attr('disabled', false);

    //resetar abas
    $('#abas-' + form + ' a:first').tab('show');

    //resetar mais opções
    $('#' + form + ' div.MaisOpcoes').attr('class', 'title closed MaisOpcoes normal');
    $('#' + form + ' div.body:eq(0)').css('display', 'none');
}

/*
===========================================================================================
EXIBIR LANÇAMENTO
===========================================================================================
*/

function lancamentoExibir(lancamento_id, form_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
    $("#dados").data("form-ativo", form_id);//parâmetro para beforeShowDay
    var params = "funcao=lancamentoExibir";
    params += "&lancamento_id=" + lancamento_id;
    ajax_jquery(params, "lancamentoExibirRetorno('" + form_id + "')");
}

function lancamentoExibirRetorno(form_id) {
    //alert(dados_global);
    lancamentosLimpar(form_id);
    var dados = eval("(" + dados_global + ")");
    $("#" + form_id + "_lancamento_id").val(dados.lancamento.id);
    $("#" + form_id + "_dscr").val(dados.lancamento.descricao);
    $("#" + form_id + "_favorecido").val(dados.lancamento.favorecido);
    $("#" + form_id + "_favorecido_id").val(dados.lancamento.favorecido_id);
    $("#" + form_id + "_conta").val(dados.lancamento.conta);
    $("#" + form_id + "_conta_id").val(dados.lancamento.conta_id);
    $("#" + form_id + "_sab_dom").val(dados.lancamento.sab_dom);
    $("#" + form_id + "_frequencia").val(dados.lancamento.frequencia);
    $("#" + form_id + "_qtd_dias").val(dados.lancamento.qtd_dias);
    $("#" + form_id + "_dia_semana").val(dados.lancamento.dia_semana);
    $("#" + form_id + "_dia_mes").val(dados.lancamento.dia_mes);
    $("#" + form_id + "_dt_inicio").val(dados.lancamento.dt_inicio);
    $("#" + form_id + "_dt_competencia").val(dados.lancamento.dt_competencia);
    $("#" + form_id + "_dt_emissao").val(dados.lancamento.dt_emissao);
    $("#" + form_id + "_valor").val(dados.lancamento.valor);
    $("#" + form_id + "_documento_id").val(dados.lancamento.documento_id);
    $("#" + form_id + "_forma_pgto_id").val(dados.lancamento.forma_pgto_id);
    $("#" + form_id + "_auto_lancamento").val(dados.lancamento.auto_lancamento);
    $("#" + form_id + "_obs").val(dados.lancamento.observacao);
    if (dados.lancamento.frequencia == 0) {
        $("#" + form_id + "_span_dia_vencimento").css('display', 'none');
        $("#" + form_id + "_span_qtd_dias").css('display', 'block');
    } else if (dados.lancamento.frequencia == 7) {
        $("#" + form_id + "_span_dia_mes, #" + form_id + "_span_qtd_dias").css('display', 'none');
        $("#" + form_id + "_span_dia_vencimento, #" + form_id + "_span_dia_semana").css('display', 'block');
    } else {
        $("#" + form_id + "_span_dia_semana, #" + form_id + "_span_qtd_dias").css('display', 'none');
        $("#" + form_id + "_span_dia_vencimento, #" + form_id + "_span_dia_mes").css('display', 'block');
    }
    $("#" + form_id + "_dt_alterada").val(0);
    ctrPlcLancamentosExibir(form_id, dados.ctr_plc_lancamentos);
    $('#' + form_id + ' span.check-green').eq(0).css('display', 'block');
    $('#' + form_id + ' span.check-green').eq(1).css('display', 'block');
    $('#' + form_id + ' .favorecido_buscar').attr('disabled', true);
    $('#' + form_id + ' .conta_buscar').attr('disabled', true);
    if (form_id == 'form_rcbt_editar')
        $("#dialog-rcbt-rcr-editar").dialog("open");
    else
        $("#dialog-pgto-rcr-editar").dialog("open");
    $("span.aguarde, div.aguarde").css("display", "none");
}

/*
===========================================================================================
LISTAR LANÇAMENTOS
===========================================================================================
*/

function lancamentosListar(conta_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
    $(".conta_id").val(conta_id);
    var params = "funcao=lancamentosListar&conta_id=" + conta_id;
    ajax_jquery(params, "lancamentosListarRetorno(" + conta_id + ")");
}

function lancamentosListarRetorno(conta_id) {
    //alert(dados_global);
    var dados = eval("(" + dados_global + ")");
    $('#lancamentos').html(dados.lancamentos);
    dTable();
    $("span.aguarde, div.aguarde").css("display", "none");
}

/*
===========================================================================================
ADICIONAR FAVORECIDO
===========================================================================================
*/

function favorecidosIncluir(nome, campo_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
    var params = "funcao=favorecidosIncluirAc";
    params += "&nome=" + nome;
    $.ajax({
        type: 'post',
        url: 'modulos/favorecido/php/funcoes.php',
        data: params,
        cache: true,
        success: function (data) {
            var dados = JSON.parse(data);
            document.getElementById(campo_id).value = dados.favorecido_id;
            $("span.aguarde, div.aguarde").css("display", "none");
        },
    });
}

/*
===========================================================================================
ADICIONAR CONTA
===========================================================================================
*/

function contasIncluir(descricao, campo_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
    var params = "funcao=contasIncluirAc";
    params += "&descricao=" + descricao;
    $.ajax({
        type: 'post',
        url: 'modulos/conta/php/funcoes.php',
        data: params,
        cache: true,
        success: function (data) {
            var dados = JSON.parse(data);
            document.getElementById(campo_id).value = dados.conta_id;
            $("span.aguarde, div.aguarde").css("display", "none");
        },
    });
}

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

$(document).ready(function () {

    //Data table orçamento
    dTable = $('#dTableLnct').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'modulos/programacao/php/funcoes.php?funcao=DataTableAjax',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bInfo: false,
        //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
        aoColumns: [
            { "mData": "lancamento", "sClass": "updates newUpdate" },
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

    $('#dTableLnct > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

})

