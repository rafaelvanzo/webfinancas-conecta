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
      url: 'modulos/centro_resp/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      beforeSend: function(){
        //Ação que será executada após o envio, no caso, chamei um gif loading para dar a impressão de garregamento na página
      },

	  //function(data) vide item 4 em $.get $.post
      success: function(data){
				dados_global = data;
				eval("("+funcao_retorno+")");
	  	},

      // Se acontecer algum erro é executada essa função
      error: function(erro){
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
  
	$( "#dialog-centro-resp-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				centroRespIncluir();
				//$( this ).dialog( "close" );
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-centro-resp-incluir" ).click(function() {
		centroRespLimpar('form_centro_resp');
		$( "#dialog-centro-resp-incluir" ).dialog( "open" );
		return false;
	});		

	//===== UI dialog - Editar conta =====//
	
	$( "#dialog-centro-resp-editar" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		buttons: {
			Salvar: function() {
				centroRespEditar();
				//$( this ).dialog( "close" );
			},	
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-centro-resp-editar" ).click(function() {
		$( "#dialog-centro-resp-editar" ).dialog( "open" );
		return false;
	});		

});


/*
===========================================================================================
REDESENHAR DATA TABLE planoContas
===========================================================================================
*/

function dTable(){
	oTable = $('.tblCentroResp').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"iDisplayLength": -1, //Mostra todas os registros sem páginar
		"bLengthChange": false, //Oculta o select que exibe a quantidade de reistros que podem ser visualizados
		"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		"columnHeader": false,
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

function centroRespIncluir(){
	if($('#form_centro_resp').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_centro_resp').serialize();
		$("#dialog-centro-resp-incluir").dialog("close");
		ajax_jquery(params,"centroRespIncluirRetorno()");
		
	}
}

function centroRespIncluirRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#centroResp').html(dados.centro_resp);
		dTable();
		$("span.aguarde, div.aguarde").css("display","none");
	}else{
		$("#dialog-alerta").dialog("option","buttons",[{text: "OK",click:function(){$("#dialog-alerta").dialog("close");$("#dialog-centro-resp-incluir").dialog("open")}}]);
		$('#dialog-alerta').html(dados.notificacao);
		$("span.aguarde, div.aguarde").css("display","none");
		$('#dialog-alerta').dialog('open');
	}
}
 
/*
===========================================================================================
EXIBIR
===========================================================================================
*/

function centroRespExibir(centro_resp_id){ 
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=centroRespExibir";
	params += "&centro_resp_id="+centro_resp_id;
	ajax_jquery(params,"centroRespExibirRetorno()");
}

function centroRespExibirRetorno(){
  //alert(dados_global);
	centroRespLimpar('form_centro_resp_editar');
	var dados = eval("("+dados_global+")");
	$("#form_centro_resp_editar input[name='centro_resp_id']").val(dados.id);
	$("#form_centro_resp_editar input[name='cod_centro']").val(dados.cod_centro);
	$("#form_centro_resp_editar input[name='cod_ref']").val(dados.cod_ref);
	$("#cod_ref_ini").val(dados.cod_ref);
	$("#form_centro_resp_editar input[name='nome']").val(dados.nome);
	$("#form_centro_resp_editar select[name='tp_centro']").val(dados.tp_centro);
	$("#form_centro_resp_editar textarea[name='descricao']").val(dados.descricao);
	$("#form_centro_resp_editar select[name='situacao']").val(dados.situacao);
	$("#nm_ctr_pai_id_edit").val(dados.centro_pai_nome);
	$("#centro_pai_id_edit").val(dados.centro_pai_id);
	$("#centro_pai_id_ini").val(dados.centro_pai_id);
	if(dados.centro_pai_id==0){	
		$('#nm_ctr_pai_id_edit').attr('disabled',false);	
	}else{ 
		$('#nm_ctr_pai_id_edit').attr('disabled',true); 
		$('#form_centro_resp_editar span.check-green').css('display','block');
	}
	$("#form_centro_resp_editar input[name='nivel']").val(dados.nivel);
	$("#form_centro_resp_editar input[name='posicao']").val(dados.posicao);
	if(dados.qtd_sub_centros>0){
		$("#tp_centro_a").attr('disabled',true);
	}else if(dados.qtd_lancamentos>0){
		$("#tp_centro_s").attr('disabled',true);
	}else{		
		$("#tp_centro_a").attr('disabled',false);
		$("#tp_centro_s").attr('disabled',false);
	}
	
	$( "#dialog-centro-resp-editar" ).dialog( "open" );
	$("span.aguarde, div.aguarde").css("display","none");
}


/*
===========================================================================================
EDITAR
===========================================================================================
*/

function centroRespEditar(){ 
	if($('#form_centro_resp_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_centro_resp_editar').serialize();
		$("#dialog-centro-resp-editar").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"centroRespEditarRetorno()");
	}
}

function centroRespEditarRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('#centroResp').html(dados.centro_resp);
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		dTable();
		$("span.aguarde, div.aguarde").css("display","none");
	}else{
		//$("#dialog-alerta").dialog("option","buttons",[{text: "OK",click:function(){$("#dialog-alerta").dialog("close");$("#dialog-centro-resp-editar").dialog("open")}}]);
		//$('#dialog-alerta').html(dados.notificacao);
		$("span.aguarde, div.aguarde").css("display","none");
		//$('#dialog-alerta').dialog('open');
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
}

/*
===========================================================================================
EXCLUÍR
===========================================================================================
*/

$(document).ready(function(){
	$('.CentroRespExcluir').live("click",function(e){

		e.preventDefault();

		var tabela = $('.tblCentroResp').dataTable(); 
		var centro_resp_id = $(this).attr('href');
		var celula = $(this).parent()[0];
		var linha = celula.parentNode;
		var indice = tabela.fnGetPosition(linha);

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { centroRespExcluir(centro_resp_id,indice); $("#dialog-alerta").dialog("close");}
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

function centroRespExcluir(centro_resp_id,indice){ 
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=centroRespExcluir&centro_resp_id="+centro_resp_id;
	ajax_jquery(params,"centroRespExcluirRetorno("+indice+")"); 
}

function centroRespExcluirRetorno(indice){  //alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		//var tabela = $(".dTable").dataTable();
		//tabela.fnDeleteRow(indice);
		$('#centroResp').html(dados.centro_resp);
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

function centroRespLimpar(form){

	var validator = $('#'+form).validate();
	validator.resetForm();
	$('#buscar_centro_pai_id').val("");
	$('span.check-green').css('display','none');
	$("#nm_ctr_pai").attr('disabled',false);
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
	
	$( ".centro_resp_buscar_ctr" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			$.getJSON( "modulos/centro_resp/paginas/centro_resp_buscar.php", request, function( data, status, xhr ) {
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

	$( ".centro_resp_buscar_ctr" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//======== FIM COMPLETAR PLANO DE CONTAS - CONDIGO PAI ID =============

	/*
	========================================================================================================================
	DATA TABLE
	========================================================================================================================
	*/

	oTable = $('.tblCentroResp').dataTable({
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

});

