// JavaScript Document
var crud = false;


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
	    sAjaxSource: 'modulos/clientes/php/funcoes.php?funcao=DataTableAjax',
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

    //Validação do form alterar senha de cliente
	$('#form-alterar-senha-cliente').validate({
	    onkeyup: false,
	    onclick: false,
	    onfocusout: false,
	    rules: {
	        senha: {
	            required: true,
	            minlength: 6,
	            maxlength: 20
	        },
	        repetirSenha: {
	            equalTo: "#senha-cliente"
	        }
	    },
	    messages: {
	        senha: {
	            minlength: "A senha deve conter no mínimo 6 digitos.",
	            maxlength: "A senha deve conter no maxímo 20 digitos.",
	        },
	        repetirSenha: {
	            required: "Por favor, confirme a nova senha.",
                equalTo: "A confirmação não confere com a nova senha."
	        }
	    }
	});

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

    //===== UI dialog - ADD INFORAMTIVO  =====//
	
	$( "#dialog-informativo" ).dialog({
		autoOpen: false,
		modal: true,
		//width: 'auto',
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
    
    //Dialog alterar senha de cliente
	$("#dialog-alterar-senha-cliente").dialog({
	    autoOpen: false,
	    modal: true,
	    position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
	    resizable: 'false',
	});
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

    $('.mSenha').css("display", "block");

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
    $("#dialog-informativo").dialog("option", "title", "Novo Cliente");
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
            url: 'modulos/clientes/php/funcoes.php?funcao=add',
            data: params,
            dataType: 'json',
            cache: true,
            success: function (dados) {
                //alert(dados.msg);
                if(dados.situacao == 1){

                dTableInfo.fnDraw();
                $("span.aguarde, div.aguarde").css("display", "none");

                $('.nSuccess p').html(dados.notificacao);
                $('.nSuccess').slideDown();
                setTimeout(function () { $('.nSuccess').slideUp() }, 5000);

                } else if (dados.situacao == 2) {

                    $("span.aguarde, div.aguarde").css("display", "none");

                $('.nWarning p').html(dados.notificacao);
                $('.nWarning').slideDown();
                setTimeout(function () { $('.nWarning').slideUp() }, 5000);
                
                setTimeout(function () { $("#dialog-informativo").dialog("open"); }, 1000);

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
    $("#dialog-informativo").dialog("option", "title", "Editar Cliente");

    $("span.aguarde, div.aguarde").css("display", "block");

    var params = 'funcao=visualizar&id=' + id; //alert(params);   

    $.ajax({
        type: 'get',
        url: 'modulos/clientes/php/funcoes.php',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {

            $("span.aguarde, div.aguarde").css("display", "none");

            $("#dialog-informativo").dialog("open");

            $('.nome').val(dados.nome);
            $('.inscricao').val(dados.inscricao);
            $('.cpf_cnpj').val(dados.cpf_cnpj);
            $('.logradouro').val(dados.logradouro);
            $('.numero').val(dados.numero);
            $('.complemento').val(dados.complemento);
            $('.bairro').val(dados.bairro);
            $('.cidade').val(dados.cidade);
            $('.uf').val(dados.uf);
            $('.cep').val(dados.cep);
            $('.telefone').val(dados.telefone);
            $('.celular').val(dados.celular);
            $('.email').val(dados.email);

            $('.agenda').val(dados.agenda); 

            $('.observacao').val(dados.observacao);

            $('.mSenha').css("display", "none");

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
        url: 'modulos/clientes/php/funcoes.php?funcao=editar&id=' + id,
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

    $('#dialog-alerta').html("<br/> Deseja realmente remover o registro selecionado?");

    $('#dialog-alerta').dialog('open');

}


function excluirInfo(id) {

    $("span.aguarde, div.aguarde").css("display", "block");

    var params = 'id=' + id; //alert(params);   

    $.ajax({
        type: 'get',
        url: 'modulos/clientes/php/funcoes.php?funcao=excluir',
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

//ALTERAR SENHA
//========================================================================================================================

//Retorna os usuários do cliente
function getUsuarios(clienteId) {

    var usuarios = $.ajax({
        type: 'get',
        url: 'modulos/clientes/php/funcoes.php',
        data: {
            funcao: 'getUsuarios',
            cliente_id: clienteId
        },
        dataType: 'json',
        cache: true,
        success: function (usuarios) {
            for (u in usuarios) {
                $('#usuario-cliente').append('<option value="' + usuarios[u]['id'] + '">' + usuarios[u]['email'] + '</option>');
            }
        },
        error: function (erro) {
            console.log(erro);
        }
    });
}

//Post do formulário para alterar senha
function alterarSenha(clienteId) {

    //Limpar formulário
    $('#usuario-cliente').html('');
    $('#senha-cliente').val('');
    $('#repetir-senha-cliente').val('');
    formLimpar('form-alterar-senha-cliente');

    //Atualiza select com usuários do cliente
    getUsuarios(clienteId);

    //Função anônima para enviar post do formulário
    var salvar = function (clienteId) {

        if ($('#form-alterar-senha-cliente').valid()) {

            $("#dialog-alterar-senha-cliente").dialog("close");

            $("span.aguarde, div.aguarde").css("display", "block");

            var params = {
                cliente_id: clienteId,
                usuario_id: $('#usuario-cliente').val(),
                senha: $('#senha-cliente').val()
            };

            $.ajax({
                type: 'get',
                url: 'modulos/clientes/php/funcoes.php?funcao=alterarSenha',
                data: params,
                dataType: 'json',
                cache: true,
                success: function (dados) {

                    $("span.aguarde, div.aguarde").css("display", "none");

                    if (dados.situacao) {
                        $('.nSuccess p').html(dados.notificacao);
                        $('.nSuccess').slideDown();
                        setTimeout(function () { $('.nSuccess').slideUp() }, 4000);
                    } else {
                        $('.nWarning p').html(dados.notificacao);
                        $('.nWarning').slideDown();
                        setTimeout(function () { $('.nWarning').slideUp() }, 4000);
                    }
                },
                error: function (erro) {
                    //alert(erro);
                    $("span.aguarde, div.aguarde").css("display", "none");

                    $('.nWarning p').html(dados.notificacao);
                    $('.nWarning').slideDown();
                    setTimeout(function () { $('.nWarning').slideUp() }, 4000);
                }
            });
        }
    }

    //Configura botões da janela
    $("#dialog-alterar-senha-cliente").dialog("option", "buttons", [
    {
        text: "Salvar",
        click: function () { salvar(clienteId); }
    },
    {
        text: "Cancelar",
        click: function () { $("#dialog-alterar-senha-cliente").dialog("close"); }
    }
    ]);

    //Abre a janela
    $('#dialog-alterar-senha-cliente').dialog('open');
}

/** LOGIN ENTRE CONTABILIDADE E FINANCEIRO */
function acessarFinancas(id)
{

    $("span.aguarde, div.aguarde").css("display", "block");
	
	$.ajax({
        type: 'post',
        url: 'modulos/clientes/php/funcoes.php?funcao=login',
        data: { id: id },
        dataType: 'json',
        cache: true,
        success: function (dados) {
            
            if(dados.situacao == '1'){
                alert('Acessando financeiro do cliente ' + dados.email + '.');
                window.location.href = "../sistema";

            }else{
                alert('Não foi possível efetuar o login.');
            }
          
            $("span.aguarde, div.aguarde").css("display", "none");
        },
        error: function (erro) {
            //alert(erro);
            $("span.aguarde, div.aguarde").css("display", "none");
        }
    });
}