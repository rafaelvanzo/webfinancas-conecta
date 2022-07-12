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
      url: 'modulos/planoContas/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      beforeSend: function(){
        //Ação que será executada após o envio, no caso, chamei um gif loading para dar a impressão de garregamento na página
		//carregando();
      },

	  //function(data) vide item 4 em $.get $.post
      success: function(data){
        $('#carregando').html("");
				dados_global = data;
				eval("("+funcao_retorno+")");
	  	},

      // Se acontecer algum erro é executada essa função
      error: function(erro){
		$('#carregando').html("");
      }
	  
    })

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
========================================================================================================================
JANELAS
========================================================================================================================
*/

$(document).ready(function(e) {
 
	//===== UI dialog - Incluir conta =====//
  
	$( "#dialog-message-planoContas-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				planoContasIncluir();
				//$( this ).dialog( "close" );
			},	
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-planoContas-incluir" ).click(function() {
		planoContasLimpar('form_planoContas');
		$( "#dialog-message-planoContas-incluir" ).dialog( "open" );
		return false;
	});		

	//===== UI dialog - Editar conta =====//
	
	$( "#dialog-message-planoContas-editar" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				planoContasEditar();
				//$( this ).dialog( "close" );
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-planoContas-editar" ).click(function() {
		$( "#dialog-message-planoContas-editar" ).dialog( "open" );
		return false;
	});		

});


/*
===========================================================================================
REDESENHAR DATA TABLE planoContas
===========================================================================================
*/

function dTable(){
	oTable = $('.tblplanoContas').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"iDisplayLength": -1, //Mostra todas os registros sem páginar
		"bLengthChange": false, //Oculta o select que exibe a quantidade de reistros que podem ser visualizados
		"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"aaSorting": [[ 0, "asc" ]], //inicializa a tabela ordenada pela coluna especificada
		'aoColumnDefs': [
			{ "bVisible": false, "aTargets": [ 0 ] } //torna uma coluna invisivel
		],		
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

function planoContasIncluir(){
	if($('#form_planoContas').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_planoContas').serialize();
		$("#dialog-message-planoContas-incluir").dialog( "close" );
		ajax_jquery(params,"planoContasIncluirRetorno()");
		$('#buscar_conta_pai_id').val("");
	}
}

function planoContasIncluirRetorno(){
	var dados = eval("("+dados_global+")"); 
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#planoContas').html(dados.planoContas);
		dTable();
		$("span.aguarde, div.aguarde").css("display","none");	
	}else{
		$("#dialog-alerta").dialog("option","buttons",[{text: "OK",click:function(){$("#dialog-alerta").dialog("close");$("#dialog-message-planoContas-incluir").dialog("open")}}]);
		$('#dialog-alerta').html(dados.notificacao);
		$("span.aguarde, div.aguarde").css("display","none");
		$('#dialog-alerta').dialog('open');
	}
}
 
/*
===========================================================================================
EXIBIR PLANO DE CONTAS
===========================================================================================
*/

function planoContasExibir(planoContas_id, tpConta){ 
	$("span.aguarde, div.aguarde").css("display","block");
    var params = "funcao=planoContasExibir";
	params += "&planoContas_id="+planoContas_id;
	ajax_jquery(params,"planoContasExibirRetorno("+tpConta+")");
}

function planoContasExibirRetorno(tpConta){
  //alert(dados_global);
	planoContasLimpar('form_planoContas');
	var dados = eval("("+dados_global+")");
	$("#form_planoContas_editar input[name='plano_contas_id']").val(dados.id);
	$("#form_planoContas_editar input[name='cod_conta']").val(dados.cod_conta);
	$("#form_planoContas_editar input[name='cod_ref']").val(dados.cod_ref);
	$("#cod_ref_ini").val(dados.cod_ref);
	$("#form_planoContas_editar input[name='nome']").val(dados.nome);
	$("#form_planoContas_editar input[name='conta_snt']").val(dados.conta_snt);
	$("#form_planoContas_editar select[name='tp_conta']").val(dados.tp_conta);
	$("#form_planoContas_editar textarea[name='descricao']").val(dados.descricao);
	$("#form_planoContas_editar select[name='situacao']").val(dados.situacao);
	$("#form_planoContas_editar select[name='clfc_fc']").val(dados.clfc_fc);
	$("#form_planoContas_editar select[name='clfc_dre']").val(dados.clfc_dre);
	$("#form_planoContas_editar select[name='tpCategoria']").val(dados.tpCategoria);
	$("#nm_plc_pai_edit").val(dados.conta_pai_nome);
	$("#conta_pai_id_edit").val(dados.conta_pai_id);
	$("#conta_pai_id_ini").val(dados.conta_pai_id);
	if(dados.conta_pai_id==0){
		$('#nm_plc_pai_edit').attr('disabled',false);
	}else{ 
		$('#nm_plc_pai_edit').attr('disabled',true);
		$('#form_planoContas_editar span.check-green').css('display','block');
	}
	$("#form_planoContas_editar input[name='nivel']").val(dados.nivel);
	$("#form_planoContas_editar input[name='posicao']").val(dados.posicao);
	$( "#dialog-message-planoContas-editar" ).dialog( "open" );
	if(dados.qtd_sub_contas>0){
		$("#tp_conta_a").attr('disabled',true);
	}else if(dados.qtd_lancamentos>0){
		$("#tp_conta_s").attr('disabled',true);
	}else{		
		$("#tp_conta_a").attr('disabled',false);
		$("#tp_conta_s").attr('disabled',false);
	}

	if (tpConta == 1)
	    $('#div-ckb-dedutivel02').css('display', 'block');
	else
        $('#div-ckb-dedutivel02').css('display', 'none');
	

	if (dados.dedutivel == 1)
	    $('#ckb-dedutivel02').bootstrapSwitch('state', true, true);
	else
	    $('#ckb-dedutivel02').bootstrapSwitch('state', false, true);

	$("span.aguarde, div.aguarde").css("display", "none");
}


/*
===========================================================================================
EDITAR planoContas
===========================================================================================
*/

function planoContasEditar(){ 
	if($('#form_planoContas_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_planoContas_editar').serialize();
		$("#dialog-message-planoContas-editar").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"planoContasEditarRetorno()");
	}
}

function planoContasEditarRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('#planoContas').html(dados.planoContas);
		dTable();
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$("span.aguarde, div.aguarde").css("display","none");
	}else{
		$("#dialog-alerta").dialog("option","buttons",[{text: "OK",click:function(){$("#dialog-alerta").dialog("close");$("#dialog-message-planoContas-editar").dialog("open")}}]);
		$('#dialog-alerta').html(dados.notificacao);
		$("span.aguarde, div.aguarde").css("display","none");
		$('#dialog-alerta').dialog('open');
	}
}


/*
===========================================================================================
EXCLUÍR
===========================================================================================
*/

$(document).ready(function(){
	$('.planoContasExcluir').live("click",function(e){

		e.preventDefault();

		var tabela = $('.tblplanoContas').dataTable();
		var planoContas_id = $(this).attr('href');
		var celula = $(this).parent()[0];
		var linha = celula.parentNode;
		var indice = tabela.fnGetPosition(linha);

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { planoContasExcluir(planoContas_id,indice); $("#dialog-alerta").dialog("close");}
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

function planoContasExcluir(planoContas_id,indice){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=planoContasExcluir&planoContas_id="+planoContas_id;
	ajax_jquery(params,"planoContasExcluirRetorno("+indice+")"); 
}

function planoContasExcluirRetorno(indice){ 
	var dados = eval("("+dados_global+")"); 
	if(dados.situacao==1){
		//var tabela = $(".tblCentroResp").dataTable();
		//tabela.fnDeleteRow(indice);
		$('#planoContas').html(dados.planoContas);
		dTable();
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}

	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function planoContasLimpar(form){

	var validator = $('#'+form).validate();
	validator.resetForm();
	$('span.check-green').css('display','none');
	$("#nm_plc_pai").attr('disabled', false);
	$('#conta_pai_id_edit, #conta_pai_id_edit').val(0);
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


$(document).ready(function(){

/*
========================================================================================================================
AUTO COMPLETAR
========================================================================================================================
*/

	//======== COMPLETAR - PLANO DE CONTAS - CONDIGO PAI ID ===================
	//var cache = {};
	
	$( ".plano_contas_buscar_plc" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			$.getJSON( "php/plano_contas_buscar.php", request, function( data, status, xhr ) {
				//cache[ term ] = data;
				response( data );
			});
		},
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
			$(this).attr('disabled','disabled');
			fadeOut($(this).attr('id'));
	  }
	});

	$( ".plano_contas_buscar_plc" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//======== FIM COMPLETAR PLANO DE CONTAS - CONDIGO PAI ID =============

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

	oTable = $('.tblplanoContas').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"iDisplayLength": -1, //Mostra todas os registros sem páginar
		"bLengthChange": false, //Oculta o select que exibe a quantidade de reistros que podem ser visualizados
		"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"aaSorting": [[ 0, "asc" ]], //inicializa a tabela ordenada pela coluna especificada
		'aoColumnDefs': [
			{ "bVisible": false, "aTargets": [ 0 ] } //torna uma coluna invisivel
		],		
		"oLanguage": {
			"sLengthMenu": "<span>Mostrar:</span> _MENU_",
			"sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
		}
		
	});

/*
========================================================================================================================
CHECKBOX BOOTSTRAP
========================================================================================================================
*/

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

//CARREGAR PLANO DE CONTAS
//==============================================================================================

function CarregarPlanoContas(clienteId) {

    if ($('input[name="modeloPlc"]:checked').length > 0) {

        $("span.aguarde, div.aguarde").css("display", "block");

        var modelo = $('input[name="modeloPlc"]:checked').val();

        $.ajax({
            type: 'post',
            url: 'modulos/arquivoContabil/php/funcoes.php',
            dataType: 'json',
            data: {
                funcao: 'CarregarPlanoContas',
                modelo: modelo,
                clienteId: clienteId
            },
            success: function (data) {
                $('#plano-contas').html(data.planoContas);
                $('#total-categorias').val(data.totalCategorias);
                notificacao(data.situacao, data.notificacao);
            },
            error: function (erro) {
                console.log(erro);
                $("span.aguarde, div.aguarde").css("display", "none");
            }
        })

    } else {

        alert('Selecione um modelo de plano de contas');

    }


}

