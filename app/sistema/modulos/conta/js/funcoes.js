// JavaScript Document

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
      url: 'modulos/conta/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,
			
      beforeSend: function(){
      },

      success: function(data){
				//dados_global = data;
				//eval("("+funcao_retorno+")");
				funcaoRetorno(data);
	  	},

      error: function(erro){
      }
	  
    })

}

/*
===========================================================================================
MENSAGEM DE NOTIFICAÇÃO
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
FORMATAÇÃO
========================================================================================================================
*/
/*
$(document).ready(function(e) {
	$('.moeda').priceFormat({
			prefix: '',
			centsSeparator: ',',
			thousandsSeparator: '.'
	});
});
*/


/*
===========================================================================================
REPOCIOCINAR ABAS DO DIALOG
===========================================================================================
*/
function resetAbasDialog(id){
	$('#'+id+' div.menu_body:eq(0)').slideDown(1);
	$('#'+id+' .acc .title:eq(0)').show().css({color:"#2B6893"});
	$('#'+id+' div.menu_body:eq(1)').slideUp(1);
	$('#'+id+' .acc .title:eq(1)').show().css({color:"#666"}); 
}

/*
========================================================================================================================
JANELAS
========================================================================================================================
*/

$(document).ready(function(e) {
 
	//===== UI dialog - Incluir conta =====//
  
	$( "#dialog-message-conta-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				contasIncluir();
				//$( this ).dialog( "close" );
			},		
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-conta-incluir" ).click(function() {
		contasLimpar('form_contas');
		$( "#dialog-message-conta-incluir" ).dialog( "open" );
		return false;
	});		

	//===== UI dialog - Editar conta =====//
	
	$( "#dialog-message-conta-editar" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				contasEditar(false);
				//$( this ).dialog( "close" );
			},	
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-conta-editar" ).click(function() {
		$( "#dialog-message-conta-editar" ).dialog( "open" );
		return false;
	});

    //===== UI dialog - Incluir nova remessa =====//

	$("#dialog-message-arquivo-remessa-incluir").dialog({
	    autoOpen: false,
	    modal: true,
	    position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
	    resizable: 'false',
	    buttons: {
	        Salvar: function () {
	            gerarRemessa();
	            $(this).dialog("close");
	            $('input[type=checkbox]').attr('checked', false);
	            $('#listaBoletos').html('<tr height="50" align="center"><td  colspan="5" align="center"> Selecione uma conta financeira para visualizar os boletos. </td></tr>');
	            $('#agencia').html('');
	            $('#totalBoletos').html('');
	            $('#conta').html('');
	            $('#qtdBoletos').html('');
	        },
	        Cancelar: function () {
	            $(this).dialog("close");
	            $('select').val('');
	            $('input[type=checkbox]').attr('checked', false);
	            $('#listaBoletos').html('<tr height="50" align="center"><td  colspan="5" align="center"> Selecione uma conta financeira para visualizar os boletos. </td></tr>');
	            $('#agencia').html('');
	            $('#totalBoletos').html('');
	            $('#conta').html('');
	            $('#qtdBoletos').html('');
	        }
	    },
	    beforeClose: function (event, ui) { resetAbasDialog($(this).attr('id')); }  //resetar a posição das abas dentro do dialog
	});

	$("#opener-arquivo-remessa-incluir").click(function () {
	    $("#dialog-message-arquivo-remessa-incluir").dialog("open");
	    return false;
	});

});

/*
===========================================================================================
REDESENHAR DATA TABLE CONTAS
===========================================================================================
*/

function dTable(){
	oTable = $('.tblContas').dataTable({
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
	ativarCROT('t');/* Reaplicando Chackbox, Radio e Title */
}

/*
===========================================================================================
INCLUÍR
===========================================================================================
*/

function contasIncluir(){
	if($('#form_contas').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_contas').serialize();
		$("#dialog-message-conta-incluir").dialog( "close" );

		funcaoRetorno = function (data) {
		    var dados = JSON.parse(data);
		    if (dados.status == 1) {
		        $('#contas').html(dados.contas);
		        dTable();
		    }
		    notificacao(dados.status, dados.notificacao);
			$("span.aguarde, div.aguarde").css("display", "none"); 
		}

		ajax_jquery(params);
	}
}

/*
===========================================================================================
EXIBIR CONTA
===========================================================================================
*/

function contasVisualizar(conta_id){
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=contasVisualizar";
	params += "&conta_id="+conta_id;
	funcaoRetorno = function(data){
		contasLimpar('form_contas_editar');
		var dados = JSON.parse(data);
		$("#form_contas_editar input[name='conta_id']").val(dados.conta.id);
		$("#form_contas_editar input[name='descricao']").val(dados.conta.descricao);
		if(dados.conta.banco_id!=0){
			boletoCnfg(dados.banco_codigo,'_edit',dados.conta.carteira);
			//$("#span_carteira_edit").html(dados.span_carteira);
			//$("#span_carteira_edit select").val(dados.conta.carteira);
			$("#form_contas_editar input[name='convenio']").val(dados.conta.convenio);
			$("#form_contas_editar input[name='variacao']").val(dados.conta.variacao);
			$("#form_contas_editar input[name='sequencial']").val(dados.conta.sequencial);			
			$("#form_contas_editar input[name='modalidade']").val(dados.conta.modalidade);
			$("#form_contas_editar input[name='custo_emissao']").val(dados.conta.custo_emissao);
			$("#form_contas_editar input[name='custo_compensacao']").val(dados.conta.custo_compensacao);
			$("#form_contas_editar input[name='multa']").val(dados.conta.multa);
			$("#form_contas_editar input[name='juros']").val(dados.conta.juros);
			$("#form_contas_editar input[name='msg1']").val(dados.conta.msg1);
			$("#form_contas_editar input[name='msg2']").val(dados.conta.msg2);
			$("#form_contas_editar input[name='msg3']").val(dados.conta.msg3);
			$("#form_contas_editar input[name='inst1']").val(dados.conta.inst1);
			$("#form_contas_editar input[name='inst2']").val(dados.conta.inst2);
			$("#form_contas_editar input[name='inst3']").val(dados.conta.inst3);
			$("#form_contas_editar input[name='bancos_buscar_editar']").val(dados.conta.bancoNome);
			$("#form_contas_editar input[name='banco_id']").val(dados.conta.banco_id);
			$("#bancos_buscar_edit").attr('disabled',true);
			$('span.check-green').css('display','block');
			var arr_emite_boleto = new Array('001','104','756','033','021');
			if( arr_emite_boleto.indexOf(dados.banco_codigo)!== -1 )
				$('#dados').data('emite_boleto','1');
		}
		$("#form_contas_editar input[name='nomeTitular']").val(dados.conta.nomeTitular);
		$("#form_contas_editar select[name='inscricao']").val(dados.conta.inscricao);
		$("#form_contas_editar input[name='cpf_cnpj']").val(dados.conta.cpf_cnpj);
		$("#form_contas_editar input[name='agencia']").val(dados.conta.agencia);
		$("#form_contas_editar input[name='agencia_dv']").val(dados.conta.agencia_dv);
		$("#form_contas_editar input[name='numero']").val(dados.conta.numero);
		$("#form_contas_editar input[name='numero_dv']").val(dados.conta.numero_dv);
		$("#form_contas_editar input[name='vl_saldo_inicial']").val(dados.conta.vl_saldo_inicial);
		$("#form_contas_editar input[name='vl_saldo_inicial_ini']").val(dados.conta.vl_saldo_inicial);	
		$("#form_contas_editar input[name='limite_credito']").val(dados.conta.limite_credito);
		$("#form_contas_editar input[name='limite_credito_ini']").val(dados.conta.limite_credito);
		$("#form_contas_editar input[name='credito_usado']").val(dados.conta.credito_usado);
		$("#form_contas_editar input[name='credito_usado_ini']").val(dados.conta.credito_usado);
		$("#form_contas_editar input[name='contato']").val(dados.conta.contato);
		$("#form_contas_editar input[name='contato_email']").val(dados.conta.contato_email);
		$("#form_contas_editar input[name='contato_tel']").val(dados.conta.contato_tel);
		$("#form_contas_editar textarea[name='observacao']").val(dados.conta.observacao);
		$( "#dialog-message-conta-editar" ).dialog( "open" );
		$("span.aguarde, div.aguarde").css("display", "none");

		if (dados.conta.carne_leao==1)
		    $('#ckb-carne-leao02').bootstrapSwitch('state', true, true);
		else
		    $('#ckb-carne-leao02').bootstrapSwitch('state', false, true);

	}
	ajax_jquery(params);
}

/*
===========================================================================================
EDITAR CONTA
===========================================================================================
*/

function contasEditar(credito_valido){
	if(!credito_valido){
		credito_valido = validaCredito('edit');
	}
	if(credito_valido){
		if($('#form_contas_editar').valid()){
			$("span.aguarde, div.aguarde").css("display","block");
			var params = $('#form_contas_editar').serialize();
			$("#dialog-message-conta-editar").dialog( "close" );
			//alert(params);
			ajax_jquery(params);
			
			funcaoRetorno = function(data){
				var dados = JSON.parse(data);
				if(dados.status==1){
					$('#contas').html(dados.contas);
					dTable();
				}
				$("span.aguarde, div.aguarde").css("display", "none");
				notificacao(dados.status, dados.notificacao);
			}
		}
	}
}

/*
===========================================================================================
EXCLUÍR
===========================================================================================
*/

$(document).ready(function(){
	$('.contasExcluir').live("click",function(e){

		e.preventDefault();

		var conta_id = $(this).attr('href');

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { contasExcluir(conta_id); $("#dialog-alerta").dialog("close");}
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Deseja realmente excluír o registro selecionado?");

		$('#dialog-alerta').dialog('open');
	
	});	
})

function contasExcluir(conta_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=contasExcluir&conta_id="+conta_id;
	funcaoRetorno = function(data){
		var dados = JSON.parse(data)
		if(dados.status==1){
			var tabela = $(".tblContas").dataTable();
			var linha = document.getElementById('row'+dados.conta_id);
			var indice = tabela.fnGetPosition(linha);
			tabela.fnDeleteRow(indice);
		}
		notificacao(dados.status, dados.notificacao);
		$("span.aguarde, div.aguarde").css("display","none");
	}
	ajax_jquery(params);
}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function contasLimpar(form){

	var validator = $('#'+form).validate();
	validator.resetForm();
	$("#"+form+" input[name='banco_id']").val("");
	$('span.check-green').css('display','none');
	$('.input-buscar').attr('disabled',false);

	$("#"+form+" input[name='vl_saldo_inicial']").val('0,00');
	$("#"+form+" input[name='limite_credito']").val('0,00');
	$("#"+form+" input[name='credito_usado']").val('0,00');
	$("#dados").data('emite_boleto', '0');

    //resetar abas
	//$('#abas-' + form + ' a:first').tab('show');

    //resetar mais opções
	$('#' + form + ' div.MaisOpcoes').attr('class', 'title closed MaisOpcoes normal');
	$('#' + form + ' div.body:eq(0)').css('display', 'none');

    //reseta checkbox switch e data de compensaçao
	$('#ckb-carne-leao-01').bootstrapSwitch('state', false, true);

}

/*
===========================================================================================
ATUALIZA CAMPOS DE CONFIGURAÇÃO DO BOLETO
===========================================================================================
*/
 
function boletoCnfg(cod_banco,dialog,carteira){
	var params = "funcao=carteira&cod_banco="+cod_banco;
	funcaoRetorno = function(data){
		$('#span_carteira'+dialog).html(data);
		var boleto_cnfg = document.getElementsByClassName("boletoCnfg");
		var i = 0;
		while(i<boleto_cnfg.length){
			boleto_cnfg[i].style.display = 'none';
			i++;
		}
		switch (cod_banco){
			case "001":
				document.getElementById("variacao"+dialog).style.display = 'block';
				document.getElementById("convenio"+dialog).style.display = 'block';
			    break;
			case "104":
				document.getElementById("convenio"+dialog).style.display = 'block';
			    break;
			case "756":
				document.getElementById("modalidade"+dialog).style.display = 'block';
				document.getElementById("convenio"+dialog).style.display = 'block';
			    break;
			case "033":
				document.getElementById("convenio"+dialog).style.display = 'block';
				break;
		    case "237":
		        document.getElementById("convenio" + dialog).style.display = 'block';
		        break;
		}
		if(carteira!='')
			$("#span_carteira"+dialog+" select").val(carteira);
	}
	ajax_jquery(params);
}

/*
===========================================================================================
VALIDA CRÉDITO UTILIZADO
===========================================================================================
*/

function validaCredito(form){
	var limite_credito = txtToValor(document.getElementById("limite_credito_"+form).value);
	var credito_usado = txtToValor(document.getElementById("credito_usado_"+form).value);
	var credito_usado_ini = txtToValor(document.getElementById("credito_usado_ini").value);

	var validacao = function(credito_usado,limite_credito){
		if( credito_usado > limite_credito){
			$( "#dialog-alerta" ).dialog( "option", "buttons", [{text: "Fechar",click: function() { $("#dialog-alerta").dialog("close"); }}]);
			$('#dialog-alerta').html("<br/> O crédito utilizado não pode ser maior do que o limite de crédito.");
			$("#dialog-alerta").dialog("open");
			return false;
		}else{
			return true;
		}
	}

	if( form=='edit' && credito_usado != credito_usado_ini){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [{text: "Sim",click: function() { if(validacao(credito_usado,limite_credito)){contasEditar(true); $("#dialog-alerta").dialog("close");} }},{text: "Não",click: function() { $("#dialog-alerta").dialog("close"); }}]);
		$('#dialog-alerta').html("<br/> O crédito utilizado é atualizado automaticamente pelo sistema. <br/> Alterar manualmente pode acarretar divegência de valores. <br> Deseja realmente modificá-lo?");
		$('#dialog-alerta').dialog('open');
	}else{
		return validacao(credito_usado,limite_credito);
	}
}

/*
===========================================================================================
CONVERTER TEXTO PARA VALOR
===========================================================================================
*/

function txtToValor(valor){
	var txt = valor;
	txt = txt.replace(/\./g, '');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
}

$(document).ready(function(){

/*
========================================================================================================================
AUTO COMPLETAR
========================================================================================================================
*/

	//bancos = [
		//{"label":"Banco do Brasil","id":"1"}
	//];

	//======== COMPLETAR - PLANO DE CONTAS - CONDIGO PAI ID ===================
	//var cache = {};
	
	$( ".bancos_buscar_id" ).autocomplete({
		minLength: 0,
		source: bancos,
		/*
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			$.getJSON( "modulos/conta/paginas/contas_financeiras_banco_buscar.php", request, function( data, status, xhr ) {
				//cache[ term ] = data;
				response( data );
			});
		},
		*/
		search: function( event, ui ) {
			var campo_id = $(this).attr('name');
			$('#'+campo_id+'_aguarde').css('display','block');
		},
		response: function( event, ui ) {
			//alert(ui);
			var campo_id = $(this).attr('name');
			$('#'+campo_id+'_aguarde').css('display','none');
			if(ui.content.length==0){
				//alert('nenhum resultado encontrado');
			}
			//alert('resposta');
		},
		select: function( event, ui ) {
			var campo_id = $(this).attr('name');
			$('#'+campo_id).val(ui.item.id);
	 		$('#'+campo_id+'_cg').css('display','block');
			$(this).attr('disabled',true);
			fadeOut($(this).attr('id'));
			if($(this).attr('id')=='bancos_buscar_inc'){var dialog = ''}else{var dialog = '_edit'};
			boletoCnfg(ui.item.codigo,dialog,'');
			$('#dados').data('emite_boleto',ui.item.emite_boleto);
			//document.getElementById('emite_boleto').value = ui.item.emite_boleto;
	  }
	});
	
	$( ".bancos_buscar_id" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//======== FIM COMPLETAR PLANO DE CONTAS - CONDIGO PAI ID =============

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

	oTable = $('.tblContas').dataTable({
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
	
	var dTableRemessas = $('.tblRemessas').dataTable({
	    "bJQueryUI": true,
	    "bAutoWidth": false,
	    "bSort": false,
	    "aaSorting": [[0, "desc"]], //inicializa a tabela ordenada pela coluna especificada
	    "sPaginationType": "full_numbers",
	    "sDom": '<"itemsPerPage"fl>t<"F"ip>',
	    "oLanguage": {
	        "sLengthMenu": "<span>Mostrar:</span> _MENU_",
	        "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
	    }
	});
/*
========================================================================================================================
VALIDAÇÃO DO BANCO PARA EMITIR BOLETO
========================================================================================================================
*/

	$('#dados').data('emite_boleto', '0');


});

/*
===========================================================================================
VISUALIZAR BOLETOS (JANELA)
===========================================================================================
*/

function visualizarBoletos() {

        //Desmarca automáticamente o input ChekcAll dos boletos.
        $(".checkAll").prop("checked", false); 

    var id = $('#bancosId').val();
    var outrasRemessas = $('#outrasRemessas').attr('checked');
    
    if (outrasRemessas == 'checked') { oR = '1'; } else { oR = '0'; }
   
    var params = "funcao=visualizarBoletos&id=" + id + "&or=" + oR;
    
    ajax_jquery(params);


    // alert(id + ' - ' + oR);

    funcaoRetorno = function (data) {

        var dados = JSON.parse(data); 
        if (dados.situacao == 1) {            
             //alert(dados.agencia + " - " + dados.conta);

            //Preenche os números
            $('#agencia').html(dados.agencia);
            $('#conta').html(dados.conta);
            $('#totalBoletos').html(dados.totalBoletos);
            $('#qtdBoletos').html(dados.qtdBoletos);

            $('#listaBoletos').html(dados.listaBoletos);
        }
    }
}

/*
===========================================================================================
GERAR ARQUIVO DE REMESSA - BOLETOS
===========================================================================================
*/
function gerarRemessa() {
    $("span.aguarde, div.aguarde").css("display", "block");
    var params = $('#form_remessa').serialize(); //alert(params);
    ajax_jquery(params); 

    funcaoRetorno = function (data) {
        //alert(data);
        var dados = JSON.parse(data); 
        visualizarBoletos();
        listarRemessa();
        window.open('https://app.webfinancas.com/sistema/modulos/boleto/download.php?download=' + dados.nome_arquivo, '_parent');
        $("span.aguarde, div.aguarde").css("display", "none");
    }
}

/*
===========================================================================================
GERAR ARQUIVO DE REMESSA ATRAVÉS DO BOTÃO DA TABELA - BOLETOS
===========================================================================================
*/
function gerarRemessaBotao(contaId, bancoId, remessa_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
    var params = "funcao=gerarRemessaBotao&conta_id=" + contaId + "&banco_id=" + bancoId + "&remessa_id=" + remessa_id; //alert(params);
    ajax_jquery(params);

    funcaoRetorno = function (data) {
        //alert(data);
        var dados = JSON.parse(data);
        window.open('https://app.webfinancas.com/sistema/modulos/boleto/download.php?download=' + dados.nome_arquivo, '_parent');
        $("span.aguarde, div.aguarde").css("display", "none");
    }
}

/*
===========================================================================================
LISTAR REMESSA
===========================================================================================
*/

function listarRemessa() {
    var params = "funcao=listarRemessa";
    ajax_jquery(params);

    funcaoRetorno = function (data) {
        var dados = JSON.parse(data);
        $('#listarRemessa').html(dados);
       // var tabela = $(".tblContas").dataTable();
       // tabela.fnDraw();
    }
}

/*
========================================================================================================================
CHECKBOX BOOTSTRAP
========================================================================================================================
*/
$(document).ready(function(){

    $(".ckb-bootstrap").bootstrapSwitch({
        'state': true,
        'size': 'mini',
        'onText': 'Sim',
        'offText': 'Não',
        'inverse': true,
        'onColor': 'success',
        'offColor': 'warning',
        'labelWidth': 1
        /*
        'onSwitchChange': function (event, state) {
            var dt_id = $(this).data('dt-id');
            if (state) {
                $('#' + dt_id).attr('disabled', false);
            } else {
                $('#' + dt_id).attr('disabled', true);
                $('#' + dt_id).val('');
            }
        }
        */
    });
    
});


/*
========================================================================================================================
JQUERY VALIDATE CPF/CNPJ (VERIFICAÇÃO)
========================================================================================================================
*/
//Aplica o Plugin de verificação do CPF / CNPJ

$(function () {
    var $cnpj = $(".cpf_cnpj").attr("name");
    var $params = { debug: false, rules: {}, messages: {} };
    $params['rules'][$cnpj] = "cpf_cnpj";
    $params['messages'][$cnpj] = "*Número inválido.";

    /* ===== FORMS =====*/
    $("#form_contas").validate($params);
    $("#form_contas_editar").validate($params);
});


/*
========================================================================================================================
CHECKALL REMESSA
========================================================================================================================
*/
//Marcar checbox
function chbox(m){
    //alert($('.chall'+m).is(":checked")); 
    if ($('.chall'+m).is(":checked") == false){
        $('.' + m).each(   function () {  $(this).prop('checked', false);  });
    }else{
         $('.' + m).each(  function () {  $(this).prop('checked', true);   });
    }
}

