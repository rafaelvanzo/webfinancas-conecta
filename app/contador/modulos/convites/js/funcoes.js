// JavaScript Document
var dados_global;

/*
========================================================================================================================
REQUISICAO AJAX
========================================================================================================================
*/

function ajax_jquery(params,funcao_retorno){

		/*
		params += "&bd_web_financas="+$('#bd_web_financas').val();
		params += "&id_usuario="+$('#id_usuario').val();
		params += "&id_dependente="+$('#id_dependente').val();
		*/
		
    $.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/convites/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      beforeSend: function(){
      },

      success: function(data){
        $('#carregando').html("");
				dados_global = data;
				eval("("+funcao_retorno+")");
	  	},

      // Se acontecer algum erro é executada essa função
      //error: function(erro){
      //}
	  
    })

}

$(document).ready(function (e) {

 
    /*
    ========================================================================================================================
    DIALOGS
    ========================================================================================================================
    */

    //===== UI dialog - CONEXÃO CONTADOR E CLIENTE  =====//

    $("#dialog-convite-contador").dialog({
        autoOpen: false,
        modal: true,
        width: '300px',
        position: { my: "top", at: "top+5%", of: window },
        buttons: {
            Convidar: function () {
                convite_contador();
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        }
    });

    $("#opener-convite-contador").click(function () {
        formLimpar('formConviteContador');
        $("#dialog-convite-contador").dialog("open");
        return false;
    });


    //===== UI dialog - REENVIAR CONVITES CONTADOR E CLIENTE  =====//
    $('.reenviarConvites').live("click", function (e) {

        e.preventDefault();
        var id_list = $(this).attr('href');

        $("#dialog-alerta").dialog("option", "buttons", [
            {
                text: "Sim",
                click: function () { reenviar_convite(id_list); $("#dialog-alerta").dialog("close"); }
            },
            {
                text: "Não",
                click: function () { $("#dialog-alerta").dialog("close"); }
            }
        ]);

        $('#dialog-alerta').html("<br/> Deseja reenviar o convite?");

        $('#dialog-alerta').dialog('open');

    });

    //===== UI dialog - EXCLUIR CONEXÃO CONTADOR E CLIENTE  =====//
    $('.excluirConexao').live("click", function (e) {

        e.preventDefault();
        var cliente_id = $(this).attr('href');
        var cliente_row_id = $(this).data('cliente-row-id');

        $("#dialog-alerta").dialog("option", "buttons", [
            {
                text: "Sim",
                click: function () { cancelar_conexoes(cliente_id, cliente_row_id); $("#dialog-alerta").dialog("close"); }
            },
            {
                text: "Não",
                click: function () { $("#dialog-alerta").dialog("close"); }
            }
        ]);

        $('#dialog-alerta').html("<br/> Deseja encerrar a conexão com o cliente?");

        $('#dialog-alerta').dialog('open');

    });

    //===== UI dialog - ACEITAR CONEXÃO CONTADOR E CLIENTE  =====//

    $('.aceitarConvite').live("click", function (e) {

        e.preventDefault();
        var cliente_id = $(this).attr('href');
        var convite_row_id = $(this).data('convite-row-id');

        $("#dialog-alerta").dialog("option", "buttons", [
            {
                text: "Sim",
                click: function () { aceitar_conexoes(cliente_id, convite_row_id); $("#dialog-alerta").dialog("close"); }
            },
            {
                text: "Não",
                click: function () { $("#dialog-alerta").dialog("close"); }
            }
        ]);

        $('#dialog-alerta').html("<br/> Deseja se conectar ao cliente?");

        $('#dialog-alerta').dialog('open');

    });

    //===== UI dialog - EXCLUIR CONVITE  =====//
    $('.excluirConvite').live("click", function (e) {

        e.preventDefault();
        var cliente_id = $(this).attr('href');
        var convite_row_id = $(this).data('convite-row-id');

        $("#dialog-alerta").dialog("option", "buttons", [
            {
                text: "Sim",
                click: function () { ConviteExcluir(cliente_id, convite_row_id); $("#dialog-alerta").dialog("close"); }
            },
            {
                text: "Não",
                click: function () { $("#dialog-alerta").dialog("close"); }
            }
        ]);

        $('#dialog-alerta').html("<br/> Confirmar exclusão do convite?");

        $('#dialog-alerta').dialog('open');

    });

   

});

    /*
    ================================================================================================
    CONEXÃO CONTADOR E CLIENTE 
    ================================================================================================
    */

    function convite_contador() {
        if ($('#formConviteContador').valid()) {
            $("span.aguarde, div.aguarde").css("display", "block");
            var params = $('#formConviteContador').serialize();
            ajax_jquery(params, "retorno_convite_contador()");
            $("#dialog-convite-contador").dialog("close");
            formLimpar('formConviteContador');
        }
    }

    function retorno_convite_contador() {
        var dados = eval("(" + dados_global + ")");
        $("span.aguarde, div.aguarde").css("display", "none");
        if (dados.situacao == 1) {
            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);
            $('#convites').html(dados.listar_convites); // Atualiza a lista de convites
        } else if (dados.situacao == 2) {
            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
        }
    }


    /*
    ================================================================================================
    REENVIAR CONVITE
    ================================================================================================
    */

    function reenviar_convite(id_list) {
        $("span.aguarde, div.aguarde").css("display", "block");
        var params = "funcao=reenviarConvite&id_list=" + id_list;
        ajax_jquery(params, "retorno_reenviar_convite()");
    }

    function retorno_reenviar_convite() {
        //alert(dados_global);
        var dados = eval("(" + dados_global + ")");
        $("span.aguarde, div.aguarde").css("display", "none");
        if (dados.situacao == 1) {
            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);
            $('#clientes').html(dados.listar_clientes); /* Atualiza a lista de conexões */
        } else if (dados.situacao == 2) {
            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
        }
    }

    /*
    ================================================================================================
    CANCELAR CONEXÃO
    ================================================================================================
    */

    function cancelar_conexoes(cliente_id, cliente_row_id) {
        $("span.aguarde, div.aguarde").css("display", "block");
        var params = "funcao=cancelarConexoes&cliente_id=" + cliente_id;
        ajax_jquery(params, "retorno_cancelar_conexoes(" + cliente_row_id + ")");
    }

    function retorno_cancelar_conexoes(cliente_row_id) {
        alert(dados_global);
        var dados = eval("(" + dados_global + ")");
        $("span.aguarde, div.aguarde").css("display", "none");
        if (dados.situacao == 1) {
            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);
            var tabela = $(".dTableClientes").dataTable();
            var indice = tabela.fnGetPosition(document.getElementById('cliente-row-' + cliente_row_id));
            tabela.fnDeleteRow(indice);
        } else if (dados.situacao == 2) {
            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
        }
    }

    /*
    ================================================================================================
    ACEITAR CONEXÃO
    ================================================================================================
    */

    function aceitar_conexoes(cliente_id, convite_row_id) {
        $("span.aguarde, div.aguarde").css("display", "block");
        var params = "funcao=AceitarConexoes&cliente_id=" + cliente_id;
        ajax_jquery(params, "retorno_aceitar_conexoes(" + convite_row_id + ")");
    }

    function retorno_aceitar_conexoes(convite_row_id) {
        //alert(dados_global);
        var dados = eval("(" + dados_global + ")");
        $("span.aguarde, div.aguarde").css("display", "none");
        if (dados.situacao == 1) {
            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);
            $('#clientes').html(dados.lista_conexoes);
        } else if (dados.situacao == 2) {
            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
        }
        //Remove convite após aceitação ou verificação de que o convite for excluído pelo cliente
        var tabela = $(".dTableConvites").dataTable();
        var indice = tabela.fnGetPosition(document.getElementById('convite-row-' + convite_row_id));
        tabela.fnDeleteRow(indice);
    }

    /*
    ================================================================================================
    CANCELAR CONVITE
    ================================================================================================
    */

    function ConviteExcluir(cliente_id, convite_row_id) {
        $("span.aguarde, div.aguarde").css("display", "block");
        var params = "funcao=ConviteExcluir&cliente_id=" + cliente_id;
        ajax_jquery(params, "RetornoConviteExcluir(" + convite_row_id + ")");
    }

    function RetornoConviteExcluir(convite_row_id) {
        //alert(dados_global);
        var dados = eval("(" + dados_global + ")");
        $("span.aguarde, div.aguarde").css("display", "none");
        if (dados.situacao == 1) {
            var tabela = $(".dTableConvites").dataTable();
            var indice = tabela.fnGetPosition(document.getElementById('convite-row-' + convite_row_id));
            tabela.fnDeleteRow(indice);
            $('.nSuccess p').html(dados.notificacao);
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);
        } else if (dados.situacao == 2) {
            $('.nWarning p').html(dados.notificacao);
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
        }
    }


