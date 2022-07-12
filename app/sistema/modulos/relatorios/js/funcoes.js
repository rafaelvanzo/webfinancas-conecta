// JavaScript Document

//var funcaoRetorno;

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
        url: 'modulos/relatorios/php/funcoes.php', //url para onde será enviada as informações digitadas
        data: params, //parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação
        //cache: true, //funciona paenas para requisição via get

        //beforeSend: function(){
        //},

        success: function (data) {
            eval("(" + funcao_retorno + ")");
            //funcaoRetorno();
        },

        //error: function(erro){
        //}

    })

}

/*
===========================================================================================
FILTRO GERAL
===========================================================================================
*/

function relatorioFiltrar(filtro_opcoes) {
    $("#dialog-alerta").dialog("option", "buttons", [
	{
	    text: "OK",
	    click: function () { $("#dialog-alerta").dialog("close"); }
	}
    ]);
    var array_filtro = [];
    if (filtro_opcoes['periodo']) {
        array_filtro['periodo'] = periodoFiltro();
        if (!array_filtro['periodo']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione o período.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['periodo_limite']) {
        array_filtro['periodo_limite'] = periodoLimiteFiltro();
        if (!array_filtro['periodo_limite']) {
            //$('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione o período.");
            //$('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['cf']) {
        array_filtro['cf'] = contasFiltro();
        if (!array_filtro['cf']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione as contas financeiras.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['nivel_plc']) {
        array_filtro['nivel_plc'] = nivelPlcFiltro();
        if (!array_filtro['nivel_plc']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left; padding-: 5px;'/> &nbsp; Selecione o nível do plano de contas");
            $('#dialog-alerta').dialog('open');
            return false;
        } else if (array_filtro['nivel_plc'] == 0) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left; padding-: 5px;'/> &nbsp; Não há nenhum plano de contas cadastrado.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['nivel_ctr']) {
        array_filtro['nivel_ctr'] = nivelCtrFiltro();
        if (!array_filtro['nivel_ctr']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione o nível do centro de responsabilidade.");
            $('#dialog-alerta').dialog('open');
            return false;
        } else if (array_filtro['nivel_ctr'] == 0) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left; padding-: 5px;'/> &nbsp; Não há nenhum centro de responsabilidade cadastrado.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['lnct_situacao']) {
        array_filtro['lnct_situacao'] = lnctSituacaoFiltro();
        if (!array_filtro['lnct_situacao']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione a situação dos lançamentos.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['tipo_fc']) { //tipo do fluxo de caixa (diário ou mensal)
        array_filtro['tipo_fc'] = tipoFcFiltro();
        if (!array_filtro['tipo_fc']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione o tipo do fluxo de caixa.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['rad_analitico_sintetico']) { //relatório analítico ou sintético
        array_filtro['rad_analitico_sintetico'] = radAnaliticoSintetico();
        if (!array_filtro['rad_analitico_sintetico']) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Selecione o tipo sintético ou analítico.");
            $('#dialog-alerta').dialog('open');
            return false;
        }
    }
    if (filtro_opcoes['orcamento']) { //orçamento para o fluxo de caixa mensal
        array_filtro['orcamento'] = orcamento();
        //if(!array_filtro['orcamento']){
        //$('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Nenhum orçamento foi selecionado.");
        //$('#dialog-alerta').dialog('open');
        //return false;
        //}
    }
    if (filtro_opcoes['favorecido']) { //favorecido para contas à receber / pagar
        array_filtro['favorecido'] = favorecido();
        //if(!array_filtro['orcamento']){
        //$('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Nenhum favorecido foi selecionado.");
        //$('#dialog-alerta').dialog('open');
        //return false;
        //}
    }
    if (filtro_opcoes['ctr']) { //centro resp para centro de responsabilidade
        array_filtro['centro_resp_id'] = centro_resp();
        //if(!array_filtro['orcamento']){
        //$('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; Nenhum favorecido foi selecionado.");
        //$('#dialog-alerta').dialog('open');
        //return false;
        //}
    }
    if (filtro_opcoes['agp_ctr']) { //tipo do fluxo de caixa (diário ou mensal)
        array_filtro['agp_ctr'] = agpCtrFiltro();
    }

    if (filtro_opcoes['dt_emissao']) {
        array_filtro['dt_emissao'] = dtEmissao();
    }



    return array_filtro;
}

/*
===========================================================================================
FILTRO NÍVEL DO PLANO DE CONTAS
===========================================================================================
*/

function nivelPlcFiltro() {
    var nivel = $('.nivel_plc:checked').val();
    return nivel;
}

/*
===========================================================================================
FILTRO NÍVEL DO CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function nivelCtrFiltro() {
    var nivel = $('.nivel_ctr:checked').val();
    return nivel;
}

/*
===========================================================================================
FILTRO SITUAÇÃO DOS LANÇAMENTOS
===========================================================================================
*/

function lnctSituacaoFiltro() {
    var lnct_situacao = $('.lnct_situacao:checked').val();
    return lnct_situacao;
}

/*
===========================================================================================
FILTRO TIPO FLUXO DE CAIXA
===========================================================================================
*/

function tipoFcFiltro() {
    //var tipo_fc = $('.tipo_fc:checked').val();
    var tipo_fc = $('input[name="periodo_radio"]:checked').val();
    return tipo_fc;
}

/*
===========================================================================================
FILTRO CONTAS FINANCEIRAS
===========================================================================================
*/

$(document).ready(function (e) {
    /*	
        $('#contasChecarTodos').click(function(e) {
        if($(this).is(':checked')){
                $('#contasFinanceiras div.checker span').each(function() {
            $(this).addClass('checked');
          });
            }else{
                $('#contasFinanceiras div.checker span').each(function() {
            $(this).removeClass('checked');
          });
            }
      });
    */

    $('#contasChecarTodos').click(function (e) {
        var checkedStatus = this.checked;
        $('#contasFinanceiras div.checker span input:checkbox').each(function () {
            this.checked = checkedStatus;
            if (checkedStatus == this.checked) {
                $(this).closest('.checker > span').removeClass('checked');
            }
            if (this.checked) {
                $(this).closest('.checker > span').addClass('checked');
            }
        });
    });



});

function contasFiltro() {
    var array_contas_id = new Array();
    var contas_id = "";
    $('#contasFinanceiras div.checker span.checked input[type="checkbox"]').each(function () {
        array_contas_id.push($(this).val());
    });
    if (array_contas_id.length == 0) {
        contas_id = false;
    } else {
        contas_id = array_contas_id.join(',');
    }
    return contas_id;
}


function dtEmissao() {
    return $('#uniform-dtEmissao span.checked input[type="checkbox"]').val();
}

/*
===========================================================================================
FILTRO PERÍODO
===========================================================================================
*/

function periodoFiltro() {
    var jsonObj = {};
    var dt_ini_valida = $('#periodoDtIni').is(':visible');
    var dt_fim_valida = $('#periodoDtFim').is(':visible');
    var mes_ini_valido = $('#periodoMesIni').is(':visible');
    var mes_fim_valido = $('#periodoMesFim').is(':visible');
    var periodo = $('input[name="periodo_radio"]:checked').val();
    jsonObj.dtCompIni = $('#mes_comp_ini').val();
    jsonObj.dtCompFim = $('#mes_comp_fim').val();
    jsonText = false;
    if (periodo == "data") {
        jsonObj.periodo = periodo;
        jsonObj.dt_ini = $('#periodoDtIni').val();
        jsonObj.dt_fim = $('#periodoDtFim').val();
        if ((dt_ini_valida && jsonObj.dt_ini == "") || (dt_fim_valida && jsonObj.dt_fim == "")) {
            jsonText = false;
        } else {
            jsonText = JSON.stringify(jsonObj);
        }
    }
    else if (periodo == "mes") {
        jsonObj.periodo = periodo;

        var mesAnoIni = $('#periodoMesIni').val();
        var mesAnoFim = $('#periodoMesFim').val();
        if ((mes_fim_valido && mesAnoFim == "") || (mes_ini_valido && mesAnoIni == "")) {
            jsonText = false;
        } else {
            mesAnoIni = mesAnoIni.split('/');
            jsonObj.mes = mesAnoIni[0];
            jsonObj.ano = mesAnoIni[1];

            mesAnoFim = mesAnoFim.split('/');
            jsonObj.mesFim = mesAnoFim[0];
            jsonObj.anoFim = mesAnoFim[1];

            jsonText = JSON.stringify(jsonObj);
        }

    }
    else if (periodo == "ano") {
        jsonObj.periodo = periodo;
        jsonObj.ano = $('#periodoAno').val();
        jsonText = JSON.stringify(jsonObj);
    }

    return jsonText;
}

/*
===========================================================================================
FILTRO LIMITE DE PERÍODO
===========================================================================================
*/

function periodoLimiteFiltro() {

    var tipo_fc = $('input[name="periodo_radio"]:checked').val();//var tipo_fc = $('.tipo_fc:checked').val();
    var tp_relatorio = $('.tp_relatorio:checked').val();
    var periodo = periodoFiltro();
    periodo = JSON.parse(periodo);

    //valida se a data inicial é menor ou igual à data final
    if (periodo.periodo == "data") {
        var dt_ini = periodo.dt_ini;
        var dt_fim = periodo.dt_fim;
        dt_ini = dt_ini.split('/');
        dt_fim = dt_fim.split('/');
        var _dt_ini = new Date(dt_ini[2], dt_ini[1], dt_ini[0]);
        var _dt_fim = new Date(dt_fim[2], dt_fim[1], dt_fim[0]);
        var qtd_dias = Math.ceil((_dt_fim - _dt_ini) / 86400000);
    }
    else if (periodo.periodo == "mes") {
        var dt_ini = periodo.ano + '-' + periodo.mes + '01';
        var dt_fim = periodo.anoFim + '-' + periodo.mesFim + '01';
        var _dt_ini = new Date(periodo.ano, periodo.mes, '01');
        if ($('#periodoMesFim').is(':visible')) {
            var _dt_fim = new Date(periodo.anoFim, periodo.mesFim, '01');
            var qtd_dias = Math.ceil((_dt_fim - _dt_ini) / 86400000);
        } else {
            var qtd_dias = 0;
        }
    }
    else {
        qtd_dias = 0;
    }

    if (qtd_dias >= 0) {

        if (tipo_fc == "data" && tp_relatorio != "simei" && qtd_dias > 31) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; O período para o relatório diário não pode ser superior à 30 dias.");
            $('#dialog-alerta').dialog('open');
            return false;
        } else if (tipo_fc == "data" && tp_relatorio == "simei" && qtd_dias > 365) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; O período deste relatório não pode ser superior a um ano.");
            $('#dialog-alerta').dialog('open');
            return false;
        } else if (tipo_fc == "mes" && dt_ini != dt_fim && tp_relatorio == "fluxoCaixaN") {
            var orct_id = $("#orct_id").val();
            if (orct_id != 0) {
                $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; O período para o relatório com orçamento não pode ser superior a um mês.");
                $('#dialog-alerta').dialog('open');
                return false;
            } else
                return true;

        } else if (tipo_fc == "mes" && qtd_dias > 365) {
            $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; O período para o relatório mensal não pode ser superior a um ano.");
            $('#dialog-alerta').dialog('open');
            return false;
        } else {
            return true;
        }

    } else {

        $('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; A data final deve ser maior ou igual à data inicial.");
        $('#dialog-alerta').dialog('open');
        return false;

    }

}

/*
===========================================================================================
FILTRO ORÇAMENTO
===========================================================================================
*/

function orcamento() {

    var tipo_fc = tipoFcFiltro();
    if (tipo_fc == "mes") {
        var orct_id = $("#orct_id").val();
        return orct_id;
        //if(orct_id == ""){
        //return false
        //}else{
        //return orct_id;
        //}
    }

}

/*
===========================================================================================
FILTRO FAVORECIDO
===========================================================================================
*/

function favorecido() {

    var favorecido_id = document.getElementById('favorecido_rcbt_id').value;
    return favorecido_id;
    //if(favorecido_id == ""){
    //return false
    //}else{
    //return favorecido_id;
    //}

}

/*
===========================================================================================
FILTRO CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function centro_resp() {

    var ctr_id = document.getElementById('ctr_id').value;
    return ctr_id;
    //if(favorecido_id == ""){
    //return false
    //}else{
    //return favorecido_id;
    //}

}

/*
===========================================================================================
FILTRO AGRUPAR CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function agpCtrFiltro() {

    var agp_ctr = document.getElementById('agp_ctr').checked;
    if (agp_ctr)
        return 1;
    return 0;

}

//FILTRO ANALÍTICO OU SINTÉTICO
//===========================================================================================

function radAnaliticoSintetico() {

    var tipo = $('.rad-analitico-sintetico:checked').val();
    return tipo;

}

/*
===========================================================================================
GERAR RELATÓRIO
===========================================================================================
*/

function relatorioGerar(tp_print) {
    //$('#relatorio').html("");
    $('#relatorio').empty();
    var tp_relatorio = $('.tp_relatorio:checked').val();
    if (tp_relatorio == "contasFinanceirasSaldo") {
        contasFinanceirasSaldo(tp_print);
    } else if (tp_relatorio == "contasFinanceirasExtrato") {
        contasFinanceirasExtrato(tp_print);
    } else if (tp_relatorio == "plc") {
        planoContas(tp_print);
    } else if (tp_relatorio == "centroResp") {
        centroResp(tp_print);
    } else if (tp_relatorio == "movimentoFinanceiro") {
        movimentoFinanceiro(tp_print);
    } else if (tp_relatorio == "plcCtr") {
        planoContasCentroResp(tp_print);
    } else if (tp_relatorio == "fluxoCaixa") {
        fluxoCaixa(tp_print);
    } else if (tp_relatorio == "fluxoCaixaN") {
        fluxoCaixaN(tp_print);
    } else if (tp_relatorio == "dre") {
        dre(tp_print);
    } else if (tp_relatorio == "rcbts") {
        rcbts_pgtos('R', tp_print);
    } else if (tp_relatorio == "pgtos") {
        rcbts_pgtos('P', tp_print);
    } else if (tp_relatorio == "simei") {
        simei(tp_print);
    } else if (tp_relatorio == "carneLeao") {
        CarneLeao(tp_print);
    } else if (tp_relatorio == "historicoLancamento") {
        HistoricoLancamentos(tp_print);
    }
}

/*
===========================================================================================
RELATÓRIO SALDO DAS CONTAS FINANCEIRAS
===========================================================================================
*/

function contasFinanceirasSaldo(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            //funcao:"contasFinanceirasSaldo",
            contas_financeiras: filtro['cf'],
            periodo: filtro['periodo'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val("contasFinanceirasSaldo");
        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'contasFinanceirasSaldoRetorno(data)');

        //funcaoRetorno = function(){
        //alert(data);
        //var dados = JSON.parse(data);
        //$('#relatorio').html(dados.relatorio);
        //alert(dados.relatorio);
        //$("span.aguarde, div.aguarde").css("display","none");
        //}

    }

}

//function contasFinanceirasSaldoRetorno(data){
//alert(data);
//var dados = JSON.parse(data);
//$('#relatorio').html(dados.relatorio);
//alert(dados.relatorio);
//$("span.aguarde, div.aguarde").css("display","none");
//}

/*
===========================================================================================
RELATÓRIO EXTRATO DAS CONTAS FINANCEIRAS
===========================================================================================
*/

function contasFinanceirasExtrato(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            funcao: "contasFinanceirasExtrato",
            contas_financeiras: filtro['cf'],
            periodo: filtro['periodo']
        };

        //requisição ajax
        $("span.aguarde, div.aguarde").css("display", "block");
        ajax_jquery(params, 'contasFinanceirasExtratoRetorno(data)');

    }

}

function contasFinanceirasExtratoRetorno(data) {
    //alert(data);
    var dados = JSON.parse(data);
    $('#relatorio').html(dados.relatorio);
    //alert(dados.relatorio);
    $("span.aguarde, div.aguarde").css("display", "none");
}

/*
===========================================================================================
RELATÓRIO PLANO DE CONTAS
===========================================================================================
*/

function planoContas(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['nivel_plc'] = true;
    filtro_opcoes['cf'] = true;
    filtro_opcoes['lnct_situacao'] = true;
    filtro_opcoes['periodo'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            //funcao:"planoContas",
            nivel: filtro['nivel_plc'],
            contas_financeiras: filtro['cf'],
            lancamento_situacao: filtro['lnct_situacao'],
            periodo: filtro['periodo'],
            tp_print: tp_print
        };
        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#tp_report").val("planoContas");

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");

    }

}

//function planoContasRetorno(data){
//alert(data);
//var dados = JSON.parse(data);
//$('#relatorio').html(dados.relatorio);
//alert(dados.relatorio);
//$("span.aguarde, div.aguarde").css("display","none");
//}

/*
===========================================================================================
RELATÓRIO CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function centroResp(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['nivel_ctr'] = true;
    filtro_opcoes['cf'] = true;
    filtro_opcoes['lnct_situacao'] = true;
    filtro_opcoes['ctr'] = true;
    filtro_opcoes['periodo'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            //funcao:"centroResp",
            nivel: filtro['nivel_ctr'],
            contas_financeiras: filtro['cf'],
            lancamento_situacao: filtro['lnct_situacao'],
            centro_resp_id: filtro['centro_resp_id'],
            periodo: filtro['periodo'],
            tp_print: tp_print
        };
        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#tp_report").val("centroResp");

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");

    }

}

//function centroRespRetorno(data){
//alert(data);
//var dados = JSON.parse(data);
//$('#relatorio').html(dados.relatorio);
//alert(dados.relatorio);
//$("span.aguarde, div.aguarde").css("display","none");
//}

/*
===========================================================================================
RELATÓRIO MOVIMENTAÇÃO FINANCEIRA
===========================================================================================
*/

function movimentoFinanceiro(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    filtro_opcoes['dt_emissao'] = true;
    //filtro_opcoes['lnct_situacao'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            //funcao:"movimentoFinanceiro",
            periodo: filtro['periodo'],
            contas_financeiras: filtro['cf'],
            dt_emissao: filtro['dt_emissao'],
            //lancamento_situacao:filtro['lnct_situacao'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val("movimentoFinanceiro");

        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'movimentoFinanceiroRetorno(data)');

    }

}

//function movimentoFinanceiroRetorno(data){
//alert(data);
//var dados = JSON.parse(data);
//$('#relatorio').html(dados.relatorio);
//alert(dados.relatorio);
//$("span.aguarde, div.aguarde").css("display","none");
//}

/*
===========================================================================================
RELATÓRIO PLANO DE CONTAS X CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function planoContasCentroResp(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    filtro_opcoes['nivel_plc'] = true;
    filtro_opcoes['nivel_ctr'] = true;
    filtro_opcoes['lnct_situacao'] = true;
    filtro_opcoes['ctr'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            //funcao:"planoContasCentroResp",
            nivel_ctr: filtro['nivel_ctr'],
            nivel_plc: filtro['nivel_plc'],
            contas_financeiras: filtro['cf'],
            lancamento_situacao: filtro['lnct_situacao'],
            periodo: filtro['periodo'],
            centro_resp_id: filtro['centro_resp_id'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val("planoContasCentroResp");

        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'planoContasCentroRespRetorno(data)');

    }

}

//function planoContasCentroRespRetorno(data){
//alert(data);
//var dados = JSON.parse(data);
//$('#relatorio').html(dados.relatorio);
//dTablePcCr();
//alert(dados.relatorio);
//$("span.aguarde, div.aguarde").css("display","none");
//}

/*
===========================================================================================
RELATÓRIO FLUXO DE CAIXA
===========================================================================================
*/

function fluxoCaixa(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['periodo_limite'] = true;
    filtro_opcoes['nivel_plc'] = true;
    filtro_opcoes['tipo_fc'] = true;
    filtro_opcoes['orcamento'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        if (filtro['tipo_fc'] == 'data') {
            var _funcao = 'fluxoCaixaDiario';
        } else {
            var _funcao = 'fluxoCaixaMensal';
        }

        //parametros
        var params = {
            //funcao:_funcao,
            periodo: filtro['periodo'],
            nivel_plc: filtro['nivel_plc'],
            orcamento_id: filtro['orcamento'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val(_funcao);

        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'fluxoCaixaRetorno(data)');
    }

}

//function fluxoCaixaRetorno(data){
//alert(data);
//var dados = JSON.parse(data);
//$('#relatorio').html(dados.relatorio);
//dTablePcCr();
//alert(dados.relatorio);
//$("span.aguarde, div.aguarde").css("display","none");
//}

/*
===========================================================================================
RELATÓRIO FLUXO DE CAIXA NOVO
===========================================================================================
*/

function fluxoCaixaN(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['periodo_limite'] = true;
    filtro_opcoes['tipo_fc'] = true;
    filtro_opcoes['rad_analitico_sintetico'] = true;
    filtro_opcoes['orcamento'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        if (filtro['tipo_fc'] == 'data') {
            var _funcao = 'fluxoCaixaDiarioN';
        } else {
            var _funcao = 'fluxoCaixaMensalN';
        }

        //parametros
        var params = {
            periodo: filtro['periodo'],
            orcamento_id: filtro['orcamento'],
            detalhamento: filtro['rad_analitico_sintetico'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val(_funcao);

        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'fluxoCaixaRetorno(data)');
    }

}

/*
===========================================================================================
RELATÓRIO DRE
===========================================================================================
*/

function dre(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['periodo_limite'] = true;
    filtro_opcoes['ctr'] = true;
    filtro_opcoes['rad_analitico_sintetico'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        /*
		if(filtro['tipo_fc']=='data'){
			var _funcao = 'fluxoCaixaDiarioN';
		}else{
			var _funcao = 'fluxoCaixaMensalN';
		}
		*/

        var _funcao = 'dre';

        //parametros
        var params = {
            periodo: filtro['periodo'],
            centro_resp_id: filtro['centro_resp_id'],
            detalhamento: filtro['rad_analitico_sintetico'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val(_funcao);

        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'fluxoCaixaRetorno(data)');
    }

}

/*
===========================================================================================
RELATÓRIO SIMEI
===========================================================================================
*/

function simei(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['periodo_limite'] = true;
    filtro_opcoes['cf'] = true;
    //filtro_opcoes['nivel_plc'] = true;
    //filtro_opcoes['tipo_fc'] = true;
    //filtro_opcoes['orcamento'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        /*
		if(filtro['tipo_fc']=='data'){
			var _funcao = 'fluxoCaixaDiarioN';
		}else{
			var _funcao = 'fluxoCaixaMensalN';
		}
		*/

        var _funcao = 'simei';

        //parametros
        var params = {
            //funcao:_funcao,
            periodo: filtro['periodo'],
            contas_financeiras: filtro['cf'],
            //nivel_plc:filtro['nivel_plc'],
            //orcamento_id:filtro['orcamento'],
            tp_print: tp_print
        };

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#tp_report").val(_funcao);

        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'fluxoCaixaRetorno(data)');
    }

}

/*
===========================================================================================
RELATÓRIO RECEBIMENTOS E PAGAMENTOS
===========================================================================================
*/

function rcbts_pgtos(tp_lancamento, tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    filtro_opcoes['lnct_situacao'] = true;
    filtro_opcoes['favorecido'] = true;
    filtro_opcoes['ctr'] = true;
    filtro_opcoes['agp_ctr'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            //funcao:"rcbts_pgtos",
            periodo: filtro['periodo'],
            contas_financeiras: filtro['cf'],
            lancamento_situacao: filtro['lnct_situacao'],
            favorecido_id: filtro['favorecido'],
            centro_resp_id: filtro['centro_resp_id'],
            tp_lancamento: tp_lancamento,
            agp_ctr: filtro['agp_ctr'],
            tp_print: tp_print
        };
        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#tp_report").val("rcbts_pgtos");

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#formPdf").submit();

        //$("span.aguarde, div.aguarde").css("display","block");
    }
}

//RELATÓRIO CARNÊ LEÃO
//===========================================================================================

function CarneLeao(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['periodo_limite'] = true;
    filtro_opcoes['cf'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            periodo: filtro['periodo'],
            contas_financeiras: filtro['cf'],
            tp_print: tp_print
        };
        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#tp_report").val("CarneLeao");

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#formPdf").submit();

        //$("span.aguarde, div.aguarde").css("display","block");
    }
}

//HISTÓRICO DE LANÇAMENTOS
//=========================================================================================

function HistoricoLancamentos(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        //parametros
        var params = {
            periodo: filtro['periodo'],
            contas_financeiras: filtro['cf'],
            tp_print: tp_print
        };
        params = JSON.stringify(params);
        $("#report_params").val(params);

        $("#tp_report").val("HistoricoLancamentos");

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        $("#formPdf").submit();

        //$("span.aguarde, div.aguarde").css("display","block");
    }
}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function filtroRelatoriosLimpar(form) {

    var validator = $('#' + form).validate();
    validator.resetForm();
    /* Rezeta o filtro dos relatórios */
    $(".checked").removeClass('checked');
    $("[type=radio]").attr("checked", false);
    $("[type=checkbox]").attr("checked", false);
    $("#descricaoRelatorios div").css('display', 'none');
    $('span.check-green').css('display', 'none');
    document.getElementById('orct_id').value = '';
    document.getElementById('favorecido_rcbt_id').value = '';
    /*
        $('#form_contas').find(':input').each(function() {
                switch(this.type) {
                        case 'password':
                        case 'select-multiple':
                        case 'select-one':
                        case 'text':
                        case 'textarea':
                                $(this).val('');
                                break;
                        case 'checkbox':
                        case 'radio':
                                this.checked = false;
                }
        });
    */

}

/*
===========================================================================================
SELECIONAR TIPO DE RELATÓRIO
===========================================================================================
*/

function mudarRelatorio(id, dt_class) {

    //exibe todo o painel de configuração
    if (id != "fluxoCaixaN" && id != "dre") {
        $('.relatorioCnfg').css('display', 'block');
    } else {
        $('.relatorioCnfg').css('display', 'none');
    }

    //esconde todas as opções de filtro
    $('.cnfg').css('display', 'none');

    //esconde todas as descrições e mostra somente a selecionada
    $('#descricaoRelatorios div').css('display', 'none');
    $('.' + id + 'Dscr').css('display', 'block');

    //exibe os filtros referentes ao tipo de relatório selecionado
    $('.' + id + 'Cnfg').css('display', 'inline-block');

    //desloca o primeiro filtro visível para a primeira posição, evitando o espaço vazio na esquerda
    $("#filtroRow2 span.cnfg:first-child:hidden").next().prependTo("#filtroRow2");

    //atribui display block para a segunda linha do filtro quando ela está visivel
    if ($('#filtroRow2').is(':visible')) {
        $('#filtroRow2').css('display', 'block');
    }

    //atribui display block para a segunda linha do filtro quando ela está visivel
    if ($('#filtroRow3').is(':visible')) {
        $('#filtroRow3').css('display', 'block');
    }

    //desloca o primeiro filtro visível para a primeira posição, evitando o espaço vazio na esquerda
    $("#filtro .fluid span.cnfg:first-child:hidden").next().prependTo("#filtro .fluid");

    if (id == "rcbts" || id == "pgtos") {
        document.querySelector(".span_lnct_sit").style.display = 'none';
    } else {
        document.querySelector(".span_lnct_sit").style.display = 'block';
    }

    //Controla exibição dos filtros para data

    $('.filtro-periodo').css('display', 'none');

    if (dt_class == 'todas') {

        //Exibe todos os filtros de data
        $('.filtro-periodo').css('display', 'inline-block');

        //Temporariamente esconde o ano porque somente o relatório carnê leão filtra por ano
        $('.lblAno,.dt_5').css('display', 'none');

        var allVisible = true;
    } else {
        var _dt_class = dt_class.split(',');
        for (var i = 0; i < _dt_class.length; i++) {
            document.querySelector(".dt_" + _dt_class[i]).style.display = 'inline-block';
        }
    }

    //Controla a exibição do label das datas

    if (!allVisible) {
        if ($('.dt_1').is(':visible') || $('.dt_2').is(':visible')) {
            document.querySelector(".lblData").style.display = 'block';
        }
        if ($('.dt_3').is(':visible') || $('.dt_4').is(':visible')) {
            document.querySelector(".lblMes").style.display = 'block';
        }
        if ($('.dt_5').is(':visible')) {
            document.querySelector(".lblAno").style.display = 'block';
            //$('#cId3').attr('checked', 'checked');
            //$('#uniform-cId3 span').addClass('checked');
        }
    }

}

/*
===========================================================================================
MUDAR SELEÇÃO RADIO PARA MÊS E PERÍODO
===========================================================================================
*/

function changeRadio(conta_id) {
    $(".conta_id").val(conta_id);

    //start: Desmarca todos os radios
    $("#uniform-cId1 span").removeClass('checked');
    $("#cId1").attr("checked", false);

    $("#uniform-cId2 span").removeClass('checked');
    $("#cId2").attr("checked", false);

    $("#uniform-cId3 span").removeClass('checked');
    $("#cId3").attr("checked", false);
    //end: Desmarca todos os radios

    //marca o radio selecionado
    $("#uniform-cId" + conta_id + " span").addClass('checked');
    $("#cId" + conta_id).attr("checked", true);
}

/*
===========================================================================================
RENDERIZAR RELATÓRIO PLANO DE CONTAS X CENTRO DE RESPOSABILIDADE
===========================================================================================
*/

var oTable = null;

function dTablePcCr() {

    oTable = $(".pc_cr").dataTable({

        //"sScrollY": "500px",//"sScrollY": "100%",
        //"bScrollCollapse": true,
        //"bDeferRender": true,
        "sScrollX": "100%",
        "sScrollXInner": "100%",
        "bPaginate": false,
        "bAutoWidth": false,
        "bInfo": false,
        "bSort": false,
        "fnDrawCallback": function () {
            //ajustarTabela(oTable);
            setTimeout(function () {
                //oTable.fnAdjustColumnSizing();
                $("#relatorio .dataTable thead tr td").each(function (index, element) {
                    if ($(this).is(':empty')) {
                        $(this).remove();
                    }
                });
            }, 100);
        }

    });

    //fixa a primeira coluna
    new FixedColumns(oTable, { "iLeftWidth": 200 });
    //new FixedHeader(oTable);

    //ajusta alinhamento entre thead e tbody; retira thead vazio
    ajustarTabela();

}


function ajustarTabela() {
    setTimeout(function () {
        oTable.fnAdjustColumnSizing();
        $("#relatorio .dataTable thead tr td").each(function (index, element) {
            if ($(this).is(':empty')) {
                $(this).remove();
            }
        });
    }, 300);
}

//escuta redimensionamento da página para ajustar tabela
window.addEventListener('resize', function (event) {
    if (oTable != null) {
        ajustarTabela();
    }
});

/*
===========================================================================================
RENDERIZAR RELATÓRIO RECEBIMENTOS E PAGAMENTOS
===========================================================================================
*/

function dTableRcbtPgto() {

    var oTableRcbtPgto = $('.dTableLancamentos').dataTable({
        //"bJQueryUI": true,
        "bAutoWidth": false,
        "bSort": false,
        "bInfo": false,
        "bPaginate": false,
        //"sPaginationType": "full_numbers",
        "sDom": '<"itemsPerPage"fl>t<"F"ip>',
        "sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
        "aaSorting": [[0, "asc"]], //inicializa a tabela ordenada pela coluna especificada
        'aoColumnDefs': [
			{ "bVisible": false, "aTargets": [0] } //torna uma coluna invisivel
        ],
        "oLanguage": {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        }
    });

}

/*
========================================================================================================================
TESTE PDF
========================================================================================================================
*/

function pdfGerar(tp_print) {

    //filtros
    var filtro_opcoes = [];
    filtro_opcoes['periodo'] = true;
    filtro_opcoes['cf'] = true;
    //filtro_opcoes['lnct_situacao'] = true;
    filtro_opcoes['favorecido'] = true;
    var filtro = relatorioFiltrar(filtro_opcoes);

    if (filtro) {

        if (tp_print == 't') {
            $("#formPdf").attr('target', '_blank');
        } else {
            $("#formPdf").attr('target', '_self');
        }

        //parametros
        var params = {
            funcao: "rcbts_pgtos",
            periodo: filtro['periodo'],
            contas_financeiras: filtro['cf'],
            //lancamento_situacao:filtro['lnct_situacao']
            favorecido_id: filtro['favorecido'],
            tp_lancamento: "R",//tp_lancamento
            tp_print: tp_print
        };

        params = JSON.stringify(params);
        $("#params").val(params);
        $("#tp_print").val(tp_print);
        //alert(params);

        $("#formPdf").submit();

        //requisição ajax
        //$("span.aguarde, div.aguarde").css("display","block");
        //ajax_jquery(params,'rcbtsRetorno(data)');

        //window.open("php/MPDF/examples/relatorios.php", "_self");

        /*
            $.ajax({
          type: 'post',
          url: 'modulos/relatorios/php/funcoes.php',
          data: params,
          //beforeSend: function(){
          //},
          success: function(data){
                    var dados = JSON.parse(data);
                    $('#relatorio').html(dados.relatorio);
                    dTableRcbtPgto();
                    //alert(dados.relatorio);
                    $("span.aguarde, div.aguarde").css("display","none");
            },
          //error: function(erro){
          //}
        })
            */
    }
}

/*
===========================================================================================
Ativar Checkbox, Radio e Title estilizados
===========================================================================================
*/

function ativarCROT(ref, id_obj) {

    if (ref == "t") {

        //$(".tipN").tipsy({gravity: "n",fade: true});
        //$(".tipS").tipsy({gravity: "s",fade: true});
        //$(".tipW").tipsy({gravity: "w",fade: true});
        //$(".tipE").tipsy({gravity: "e",fade: true});

        for (id in id_obj) {
            $(id + " input:checkbox, " + id + " input:radio, " + id + " input:file").uniform();
        }

    } else {

        $(".tipN").tipsy({ gravity: "n", fade: true });
        $(".tipS").tipsy({ gravity: "s", fade: true });
        $(".tipW").tipsy({ gravity: "w", fade: true });
        $(".tipE").tipsy({ gravity: "e", fade: true });
        //$("input:checkbox, input:radio, input:file").uniform();

    }
}

//AUTO COMPLETAR
//========================================================================================================================

$(document).ready(function () {

    //======== BUSCAR ORÇAMENTO =============

    //var cache = {};

    $(".orcamentos_buscar").autocomplete({
        minLength: 0,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache ) {
            //response( cache[ term ] );
            //return;
            //}
            $.getJSON("modulos/planejamento/paginas/orcamentos_buscar.php", request, function (data, status, xhr) {
                //cache[ term ] = data;
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
            $(this).attr('disabled', 'disabled');
            fadeOut($(this).attr('id'));
        }
    });

    $(".orcamentos_buscar").click(function () {
        var campo_id = $(this).attr('id');
        $("#" + campo_id).autocomplete("search");
    })
    //======== FIM BUSCAR ORÇAMENTO =============

    //======== BUSCAR FAVORECIDO =============

    //var cache = {};

    $(".favorecidos_buscar_report").autocomplete({
        minLength: 0,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache ) {
            //response( cache[ term ] );
            //return;
            //}
            $.getJSON("php/favorecidos_buscar_report.php", request, function (data, status, xhr) {
                //cache[ term ] = data;
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
            $(this).attr('disabled', 'disabled');
            fadeOut($(this).attr('id'));
        }
    });

    $(".favorecidos_buscar_report").click(function () {
        var campo_id = $(this).attr('id');
        $("#" + campo_id).autocomplete("search");
    })
    //======== FIM BUSCAR FAVORECIDO =============

    //======== CENTRO DE RESPONSABILIDADE ===================
    //var cache3 = {};

    $(".centro_resp_buscar_report").autocomplete({
        minLength: 0,
        source: centros,
        /*
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache3 ) {
				//response( cache3[ term ] );
				//return;
			//}
			$.getJSON( "php/centro_resp_buscar.php", request, function( data, status, xhr ) {
				//cache3[ term ] = data;
				response( data );
			});
		},
		*/
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
            $(this).attr('disabled', 'disabled');
            fadeOut($(this).attr('id'));
            var ctr_niveis = ui.item.niveis;
            var i = 1;
            var report_niveis = document.getElementsByClassName("nivel_ctr");
            report_niveis = report_niveis.length;
            if (ctr_niveis != report_niveis) {
                document.getElementById("selectCtrNivel").innerHTML = "";
                while (i <= ctr_niveis) {
                    document.getElementById("selectCtrNivel").innerHTML += '<input type="radio" name="nivel_ctr" class="nivel_ctr" value="' + i + '"> Nível ' + i + ' <br>';
                    i++;
                }
                $(".nivel_ctr").uniform();
            }
        }
    });

    $(".centro_resp_buscar_report").click(function () {
        var campo_id = $(this).attr('id');
        $("#" + campo_id).autocomplete("search");
    })
    //======== FIM COMPLETAR CENTRO DE RESPONSABILIDADE =============

    //ativarCROT("t",["#filtro","#filtroRow3"]);

    //NÃO RETIRAR POIS ESTÁ DANDO PROBLEMA NO FILTRO.
    $("input:checkbox, input:radio, input:file").uniform();

});


