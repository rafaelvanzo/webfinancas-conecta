// JavaScript Document
var dados_global;


$(document).ready(function(e) {

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/
    function formLimpar(form) {
        var validator = $('#' + form).validate();
        validator.resetForm();

}


/*
========================================================================================================================
FORMATAR CHECKBOX
========================================================================================================================
*/

	$("input:checkbox").uniform();

/*
========================================================================================================================
DATA TABLE - COMPLEMENTOS
========================================================================================================================
*/

    //Data table mensagens
	dTableInfo = $('.dTableInformativo').dataTable({
	    bProcessing: true,
	    bServerSide: true,
	    sAjaxSource: 'modulos/informativo/php/funcoes.php?funcao=DataTableAjax',
	    "bJQueryUI": true,
	    "bAutoWidth": false,
	    "sPaginationType": "full_numbers",
	    //bInfo: false,
	    //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
	    aoColumns: [
            { "mData": "msg", "sClass": "updates newUpdate" },
            //{ "mData": "options", "sClass": "actions" },
	    ],
	    "oLanguage": {
	        "sLengthMenu": "<span>Mostrar:</span> _MENU_",
	        "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
	    },
	   // "fnServerParams": function (aoData) {
	
	    //},
	    fnDrawCallback: function () {
	        crud = false;
	        //$('#btn-filtro').button('reset');
	    }
	});

	$('.dTableInformativo > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

    //Redesenhar
	//dTableInfo.fnDraw();


/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

    //===== UI dialog - ADD INFORAMTIVO  =====//
	
	$( "#dialog-informativo" ).dialog({
		autoOpen: false,
		modal: true,
		position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false'
	/*,	buttons: {
		    Salvar: function () {
		        $(this).dialog("close");
		    },
		    Cancelar: function () {
		        $(this).dialog("close");
		    } 
		},*/
	});

    /*	
	$('#opener-informativo').live("click", function (e) {
	    $("#dialog-informativo").dialog("open");
		return false;
	});
    */
    
});

/*
========================================================================================================================
ADD INFORMATIVO
========================================================================================================================
*/

//Abrir novo dialog
function addDialog() {

    //Limpar o formulário
    formLimpar('formInfo');

   //Altera os botão de ação do dialog
    $("#dialog-informativo").dialog("option", "buttons", [
    {
        text: "Salvar",
        click: function () {

            //Validação dos campos do formulário
            if ($('#formInfo').valid()) {

                addInfo();
                $("#dialog-informativo").dialog("close");

            } // Fim da validação dos campos do formulário
        }
    },
    {
        text: "Cancelar",
        click: function () { $("#dialog-informativo").dialog("close"); }
    }
    ]);



    //Altera o título
    $("#dialog-informativo").dialog("option", "title", "Novo Informativo");
    //Abre o dialog
    $("#dialog-informativo").dialog("open");

    //Não executar uma ação default no link ou no botão
    event.preventDefault();

}

//Adiciona as informações no db
function addInfo() {

        var params = $('#formInfo').serialize(); //alert(params);   

        $.ajax({
            type: 'post',
            url: 'modulos/informativo/php/funcoes.php?funcao=addInfo',
            data: params,
            dataType: 'json',
            cache: true,
            success: function (dados) {
                //alert(dados.msg);

                dTableInfo.fnDraw();
                $("span.aguarde, div.aguarde").css("display", "none");

                $('.nSuccess p').html(dados.notificacao);
                $('.nSuccess').slideDown();
                setTimeout(function () { $('.nSuccess').slideUp() }, 5000);

            },
            error: function (erro) {
                //alert(erro);
                $("span.aguarde, div.aguarde").css("display", "none");

                $('.nWarning p').html(dados.notificacao);
                $('.nWarning').slideDown();
                setTimeout(function () { $('.nWarning').slideUp() }, 5000);

                $("#dialog-informativo").dialog("open");
            }
        })
 
}


/*
========================================================================================================================
VISUALIZAR INFORMATIVO
========================================================================================================================
*/
function visualizarInfo(id) {

    //Limpar o formulário
    formLimpar('formInfo');

    //Altera os botão de ação do dialog
    $("#dialog-informativo").dialog("option", "buttons", [
    {
        text: "Salvar",
        click: function () { 
           
            //Validação dos campos do formulário
            if ($('#formInfo').valid()) {

            editarInfo(id); 
            $("#dialog-informativo").dialog("close"); 

            } // Fim da validação dos campos do formulário
        }
    },
    {
        text: "Cancelar",
        click: function () { $("#dialog-informativo").dialog("close"); }
    }
    ]);


    //Altera o título
    $("#dialog-informativo").dialog("option", "title", "Editar Informativo");

    $("span.aguarde, div.aguarde").css("display", "block");

    var params = 'funcao=visualizarInfo&id=' + id; //alert(params);   

    $.ajax({
        type: 'get',
        url: 'modulos/informativo/php/funcoes.php',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {

            $("span.aguarde, div.aguarde").css("display", "none");

            $("#dialog-informativo").dialog("open");

            $('.titulo').val(dados.titulo);
            $('.dt_inicio').val(dados.dt_inicio);            
            $('.situacao').val(dados.situacao);
            $('.descricao').val(dados.descricao);

            if (dados.dt_final != '00/00/0000') {
                $('.dt_final').val(dados.dt_final);
            }

        },
        error: function (erro) {
            //alert(erro);            
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);

            $("#dialog-informativo").dialog("open");
        }
    })

}

/*
========================================================================================================================
EDITAR INFORMATIVO
========================================================================================================================
*/
function editarInfo(id) {

    $("#dialog-informativo").dialog("close");

    $("span.aguarde, div.aguarde").css("display", "block");

    var params = $('#formInfo').serialize(); //alert(params);   

    $.ajax({
        type: 'post',
        url: 'modulos/informativo/php/funcoes.php?funcao=editarInfo&id=' + id,
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {

            dTableInfo.fnDraw();
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);

        },
        error: function (erro) {
            //alert(erro);
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);

            $("#dialog-informativo").dialog("open");
        }
    })

}

/*
========================================================================================================================
EXCLUIR INFORMATIVO
========================================================================================================================
*/
//Botão excluir chamar janela de exclusão
function excluirDialog(id) {

    $("#dialog-alerta").dialog("option", "buttons", [
    {
        text: "Sim",
        click: function () { excluirInfo(id); $("#dialog-alerta").dialog("close"); }
    },
    {
        text: "Não",
        click: function () { $("#dialog-alerta").dialog("close"); }
    }
    ]);

    $('#dialog-alerta').html("<br/> Deseja realmente excluír o registro selecionado?");

    $('#dialog-alerta').dialog('open');

}


function excluirInfo(id) {

    $("span.aguarde, div.aguarde").css("display", "block");

    var params = 'id=' + id; //alert(params);   

    $.ajax({
        type: 'get',
        url: 'modulos/informativo/php/funcoes.php?funcao=excluirInfo',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {

            dTableInfo.fnDraw();
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);                
        },
        error: function (erro) {
            //alert(erro);
            $("span.aguarde, div.aguarde").css("display", "none");
            
            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
        }
    })

}

