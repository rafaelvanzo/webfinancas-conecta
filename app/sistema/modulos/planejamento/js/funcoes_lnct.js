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
      url: 'modulos/planejamento/php/funcoes_lnct.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      success: function(data){
				eval("("+funcao_retorno+")");
	  	},

      error: function(erro){
      }
	  
    })

}

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

$(document).ready(function(e) {

	//===== UI dialog - Incluir recebimento =====//

	$( "#dialog-rcbt-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		buttons: {			
			Salvar: function() {
				recebimentosIncluir();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		 beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-rcbt-incluir" ).click(function() {
		lancamentosLimpar('form_rcbt');
		//$('.lnct_dados').css('display','block');
		//$('.lnct_ctr_plc').css('display','none');
		$("#dialog-rcbt-incluir" ).dialog( "open" ); 
		return false;
	});

	//===== UI dialog - Editar recebimento =====//
	
	$( "#dialog-rcbt-editar" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		/*
		//botões são definidos na função exibir
		buttons: {
			Salvar: function() {
				recebimentosEditar();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		*/
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') );}  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-rcbt-editar" ).click(function() {
		$( "#dialog-rcbt-editar" ).dialog( "open" );
		return false;
	});
	
	//===== UI dialog - Incluir pagamento =====//

	$( "#dialog-pgto-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		buttons: {
			Salvar: function() {
				pagamentosIncluir();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-pgto-incluir" ).click(function() {
		lancamentosLimpar('form_pgto');
		$( "#dialog-pgto-incluir" ).dialog( "open" );
		return false;
	});
	
	//===== UI dialog - Editar pagamento =====//

	$( "#dialog-pgto-editar" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
/*
		buttons: {
			Salvar: function() {
				pagamentosEditar();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
*/		
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') );}  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-pgto-editar" ).click(function() {
		$( "#dialog-pgto-editar" ).dialog( "open" );
		return false;
	});

});

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
AUTO COMPLETAR
========================================================================================================================
*/

$(document).ready(function(){

	//======== FAVORECIDO RECEBIMENTO ===================
	//var cache = {};
	
	$( ".favorecido_buscar" ).autocomplete({
		minLength: 1,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			$.getJSON( "modulos/lancamento/paginas/favorecidos_buscar.php", request, function( data, status, xhr ) {
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
	  }
	});
	//======== FIM COMPLETAR FAVORECIDO RECEBIMENTO =============


	//======== CONTA RECEBIMENTO ===================
	//var cache2 = {};
	
	$( ".conta_buscar" ).autocomplete({
		minLength: 1,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache2 ) {
				//response( cache2[ term ] );
				//return;
			//}
			$.getJSON( "modulos/lancamento/paginas/contas_buscar.php", request, function( data, status, xhr ) {
				//cache2[ term ] = data;
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
	  }
	});
	//======== FIM COMPLETAR CONTA RECEBIMENTO =============
	
	//======== CENTRO DE RESPONSABILIDADE ===================
	//var cache3 = {};
	
	$( ".centro_resp_buscar" ).autocomplete({
		minLength: 1,
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
	  }
	});
	//======== FIM COMPLETAR CENTRO DE RESPONSABILIDADE =============

	//======== PLANO DE CONTAS ===================
	//var cache4 = {};
	
	$( ".plano_contas_buscar" ).autocomplete({
		minLength: 1,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache4 ) {
				//response( cache4[ term ] );
				//return;
			//}
			$.getJSON( "php/plano_contas_buscar.php", request, function( data, status, xhr ) {
				//cache4[ term ] = data;
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
	  }
	});
	//======== FIM COMPLETAR PLANO DE CONTAS =============
	

});

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

$(document).ready(function(){
	oTable = $('.dTableLancamentos').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"aaSorting": [[ 0, "asc" ]], //inicializa a tabela ordenada pela coluna especificada
		'aoColumnDefs': [
			//{ 'bSortable': false, 'aTargets': [ 0,1,2,3,4 ] },
			{ "bVisible": false, "aTargets": [ 0 ] } //torna uma coluna invisivel
		],
		"oLanguage": {
			"sLengthMenu": "<span>Mostrar:</span> _MENU_",
			"sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
		}
	});

	/*
	oTableLancamentos = $('.dTableLancamentos').dataTable();
	oTableLancamentos.fnSetColumnVis( 0, false );
	oTableLancamentos.fnSort( [ [0,'asc'] ] );
	*/

});


/*
===========================================================================================
REDESENHAR DATA TABLE LANÇAMENTOS
===========================================================================================
*/

function dTable(){
	oTable = $('.dTableLancamentos').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"aaSorting": [[ 0, "asc" ]], //inicializa a tabela ordenada pela coluna especificada
		'aoColumnDefs': [
		//	{ 'bSortable': false, 'aTargets': [ 0,1,2,3,4 ] },
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
INCLUÍR RECEBIMENTO
===========================================================================================
*/

function recebimentosIncluir(){
	if($('#form_rcbt').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		centroRespLnctIncluirPlnj('form_rcbt');
		var params = $('#form_rcbt').serialize();
		$("#dialog-rcbt-incluir").dialog( "close" );
		ajax_jquery(params,"recebimentosIncluirRetorno(data)");
	}
}

function recebimentosIncluirRetorno(data){
	//alert(data);
	var dados = JSON.parse(data);
	//alert(dados.lancamento);
	//var lancamento = JSON.parse(dados.lancamento);
	if(dados.situacao==1){
		var tabela = $(".dTableLancamentos").dataTable();
		//var opcoes = '<div align="center"><a href="javascript://void(0);" original-title="Editar" class="smallButton tipS exibir" style="margin: 5px;" onClick="lancamentosExibir('+lancamento[0].lancamento_id+',\''+lancamento[0].tipo+'\')"><img src="images/icons/dark/pencil.png" alt=""></a><a href="'+lancamento[0].lancamento_id+'" original-title="Excluír" class="smallButton tipS '+lancamento[0].classe_excluir+'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></div>';
		tabela.fnAddData([
			dados.lancamento.dt_ordem,//lancamento[0].dt_ordem,
			dados.lancamento.conteudo_lnct
			//'<div align="center">'+lancamento[0].dt_vencimento+'</div>',
			//lancamento[0].descricao,
			//'<font color="#009900">'+lancamento[0].valor+'</font>',
			//opcoes
		]);
		ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
EDITAR RECEBIMENTO
===========================================================================================
*/
 
function recebimentosEditar(indice){
	if($('#form_rcbt_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		centroRespLnctIncluirPlnj('form_rcbt_editar');
		var params = $('#form_rcbt_editar').serialize();
		$("#dialog-rcbt-editar").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"recebimentosEditarRetorno(data,"+indice+")");
	}
}

function recebimentosEditarRetorno(data,indice){
	//alert(data);
	var dados = JSON.parse(data);
	//alert(dados.lancamento);
	if(dados.situacao==1){
		//var lancamento = JSON.parse(dados.lancamento);
		//remove linha anterior
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
		//adiciona nova linha 
		//var opcoes = '<div align="center"><a href="javascript://void(0);" original-title="Editar" class="smallButton tipS exibir" style="margin: 5px;" onClick="lancamentosExibir('+lancamento[0].lancamento_id+',\''+lancamento[0].tipo+'\')"><img src="images/icons/dark/pencil.png" alt=""></a><a href="'+lancamento[0].lancamento_id+'" original-title="Excluír" class="smallButton tipS '+lancamento[0].classe_excluir+'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></div>';
		tabela.fnAddData([
			dados.lancamento.dt_ordem,//lancamento[0].dt_ordem,
			dados.lancamento.conteudo_lnct
			//'<div align="center">'+lancamento[0].dt_vencimento+'</div>',
			//lancamento[0].descricao,
			//'<font color="#009900">'+lancamento[0].valor+'</font>',
			//opcoes
		]);
		ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
EXCLUÍR RECEBIMENTO
===========================================================================================
*/

$(document).ready(function(){
	$('.recebimentosExcluir').live("click",function(e){

		e.preventDefault();

		var tabela = $(".dTableLancamentos").dataTable();
		var lancamento_id = $(this).attr('href');
		var celula = $(this).parent()[0];
		var linha = celula.parentNode.parentNode;
		var indice = tabela.fnGetPosition(linha);

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { recebimentosExcluir(lancamento_id,indice); $("#dialog-alerta").dialog("close");}
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

function recebimentosExcluir(lancamento_id,indice){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=recebimentosExcluir&lancamento_id="+lancamento_id;
	ajax_jquery(params,"recebimentosExcluirRetorno(data,"+indice+")");
}

function recebimentosExcluirRetorno(data,indice){
	//alert(data);
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
INCLUÍR PAGAMENTO
===========================================================================================
*/

function pagamentosIncluir(){
	if($('#form_pgto').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		centroRespLnctIncluirPlnj('form_pgto');
		var params_pgto = $('#form_pgto').serialize();
		var params_busca = $('#form_busca').serialize();
		var params = params_pgto+'&'+params_busca;
		$("#dialog-pgto-incluir").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"pagamentosIncluirRetorno(data)");
	}
}

function pagamentosIncluirRetorno(data){
	//alert(dados_global);
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		//var lancamento = JSON.parse(dados.lancamento);
		var tabela = $(".dTableLancamentos").dataTable();
		//var opcoes = '<div align="center"><a href="javascript://void(0);" original-title="Editar" class="smallButton tipS exibir" style="margin: 5px;" onClick="lancamentosExibir('+lancamento[0].lancamento_id+',\''+lancamento[0].tipo+'\')"><img src="images/icons/dark/pencil.png" alt=""></a><a href="'+lancamento[0].lancamento_id+'" original-title="Excluír" class="smallButton tipS '+lancamento[0].classe_excluir+'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></div>';
		tabela.fnAddData([
			dados.lancamento.dt_ordem,//lancamento[0].dt_ordem,
			dados.lancamento.conteudo_lnct
			//'<div align="center">'+lancamento[0].dt_vencimento+'</div>',
			//lancamento[0].descricao,
			//'<font color="#FF0000">'+lancamento[0].valor+'</font>',
			//opcoes
		]);
		ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
EDITAR PAGAMENTO
===========================================================================================
*/

function pagamentosEditar(indice){
	if($('#form_pgto_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		centroRespLnctIncluirPlnj('form_pgto_editar');
		var params = $('#form_pgto_editar').serialize();
		$("#dialog-pgto-editar").dialog( "close" );
		ajax_jquery(params,"pagamentosEditarRetorno(data,"+indice+")");
	}
}

function pagamentosEditarRetorno(data,indice){
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		//var lancamento = JSON.parse(dados.lancamento);
		//remove linha anterior
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
		//adiciona nova linha 
		//var opcoes = '<div align="center"><a href="javascript://void(0);" original-title="Editar" class="smallButton tipS exibir" style="margin: 5px;" onClick="lancamentosExibir('+lancamento[0].lancamento_id+',\''+lancamento[0].tipo+'\')"><img src="images/icons/dark/pencil.png" alt=""></a><a href="'+lancamento[0].lancamento_id+'" original-title="Excluír" class="smallButton tipS '+lancamento[0].classe_excluir+'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></div>';
		tabela.fnAddData([
			dados.lancamento.dt_ordem,//lancamento[0].dt_ordem,
			dados.lancamento.conteudo_lnct
			//'<div align="center">'+lancamento[0].dt_vencimento+'</div>',
			//lancamento[0].descricao,
			//'<font color="#FF0000">'+lancamento[0].valor+'</font>',
			//opcoes
		]);
		ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
EXCLUÍR PAGAMENTO
===========================================================================================
*/

$(document).ready(function(){
	$('.pagamentosExcluir').live("click",function(e){

		e.preventDefault();

		var tabela = $(".dTableLancamentos").dataTable();
		var lancamento_id = $(this).attr('href');
		var celula = $(this).parent()[0];
		var linha = celula.parentNode.parentNode;
		var indice = tabela.fnGetPosition(linha);

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { pagamentosExcluir(lancamento_id,indice); $("#dialog-alerta").dialog("close");}
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

function pagamentosExcluir(lancamento_id,indice){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=pagamentosExcluir&lancamento_id="+lancamento_id;
	ajax_jquery(params,"pagamentosExcluirRetorno(data,"+indice+")");
}

function pagamentosExcluirRetorno(data,indice){
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
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

function lancamentosLimpar(form){
	var validator = $('#'+form).validate();
	validator.resetForm();
	$("#"+form+" input[name='ct_resp_lancamentos']").val("");
	$('span.check-green').css('display','none');
	$('#'+form+'_pl_conta_buscar').attr('disabled',false);
	$('#'+form+'_ct_resp_buscar').attr('disabled',false);
	$("#"+form+"_pl_conta_id").val(0);
	$("#"+form+"_ct_resp_id").val(0);
}

/*
===========================================================================================
EXIBIR LANÇAMENTO
===========================================================================================
*/
 
$(document).ready(function(){
	$('.exibir').live("click",function(e){

		var tabela = $(".dTableLancamentos").dataTable();
		var celula = $(this).parent()[0];
		var linha = celula.parentNode.parentNode;
		var indice = tabela.fnGetPosition(linha);

		$( "#dialog-rcbt-editar" ).dialog( "option", "buttons", [
		{
			text: "Salvar",
			click: function() {recebimentosEditar(indice);}
		},
		{
			text: "Cancelar",
			click: function() {$("#dialog-rcbt-editar").dialog("close");}
		}		
		]);

		$( "#dialog-pgto-editar" ).dialog( "option", "buttons", [
		{
			text: "Salvar",
			click: function() {pagamentosEditar(indice);}
		},
		{
			text: "Cancelar",
			click: function() {$("#dialog-pgto-editar").dialog("close");}
		}		
		]);

	});	
})

function lancamentosExibir(lancamento_id,tp_lancamento){
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=lancamentosExibir";
	params += "&lancamento_id="+lancamento_id;
	params += "&tp_lancamento="+tp_lancamento;
	ajax_jquery(params,"lancamentosExibirRetorno(data)");
}

function lancamentosExibirRetorno(data){
  //alert(data);
	var dados = JSON.parse(data);
	if(dados.lancamento.tipo=="R"){
		lancamentosLimpar('form_rcbt_editar');
		var form_id = "#form_rcbt_editar";
		var dialog_id = "#dialog-rcbt-editar";
		$(form_id+" input[name='lancamento_id']").val(dados.lancamento.id);
		$(form_id+" input[name='descricao']").val(dados.lancamento.descricao);
		$(form_id+" input[name='valor']").val(dados.lancamento.valor);
		$(form_id+" input[name='dt_vencimento']").val(dados.lancamento.dt_vencimento);
		$(form_id+" textarea[name='observacao']").val(dados.lancamento.observacao);
		$(form_id+'_pl_conta_buscar').attr('disabled',false);
		$(form_id+'_ct_resp_buscar').attr('disabled',false);
		ctrPlcLancamentosExibirPlnj("form_rcbt_editar", dados.ctr_plc_lancamentos);
	}else if(dados.lancamento.tipo=="P"){
		lancamentosLimpar('form_pgto_editar');
		var form_id = "#form_pgto_editar";
		var dialog_id = "#dialog-pgto-editar";
		$(form_id+" input[name='lancamento_id']").val(dados.lancamento.id);
		$(form_id+" input[name='descricao']").val(dados.lancamento.descricao);
		$(form_id+" input[name='valor']").val(dados.lancamento.valor);
		$(form_id+" input[name='dt_vencimento']").val(dados.lancamento.dt_vencimento);
		$(form_id+" textarea[name='observacao']").val(dados.lancamento.observacao);
		$(form_id+'_pl_conta_buscar').attr('disabled',false);
		$(form_id+'_ct_resp_buscar').attr('disabled',false);
		ctrPlcLancamentosExibirPlnj("form_pgto_editar", dados.ctr_plc_lancamentos);
	}
	$(dialog_id).dialog("open");
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
LISTAR LANÇAMENTOS
===========================================================================================
*/

function lancamentosListar(conta_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var conta_id_atual = $(".conta_id").val();
	$(".conta_id").each(function(){
		$(this).val(conta_id);
  });

	//alert($("#cId"+conta_id).attr('checked'));
	$("#uniform-cId"+conta_id_atual+" span").removeClass('checked');
	$("#uniform-cId"+conta_id+" span").addClass('checked');
	var params = "funcao=lancamentosListar&conta_id="+conta_id;
	ajax_jquery(params,"lancamentosListarRetorno("+conta_id+")");
}

function lancamentosListarRetorno(conta_id){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	$('#lancamentos').html(dados.lancamentos);
	dTable();
	$('#nomeConta').html(dados.nome_conta);
	$('#conta_id').val(conta_id);
	$("span.aguarde, div.aguarde").css("display","none");
}


/*
===========================================================================================
BUSCAR LANÇAMENTOS POR PERÍODO
===========================================================================================
*/

function lancamentosBuscarPeriodo(){
	$("span.aguarde, div.aguarde").css("display","block");
	var dt_ini = $('#dt_ini').val(), dt_fim = $('#dt_fim').val();
	$('#dt_ini_busca').each(function() {$(this).val(dt_ini);});
	$('#dt_fim_busca').each(function() {$(this).val(dt_fim);});
	$('#tp_busca').each(function() {$(this).val('periodo');});
	var params = $('#formBuscarPeriodo').serialize();
	ajax_jquery(params,"lancamentosBuscarPeriodoRetorno()");
}

function lancamentosBuscarPeriodoRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	$('#lancamentos').html(dados.lancamentos);
	dTable();
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
BUSCAR LANÇAMENTOS POR MÊS
===========================================================================================
*/

function lancamentosBuscarMes(){
	$("span.aguarde, div.aguarde").css("display","block");
	var mes = $('#mes').val();
	$('#mes_busca').each(function() {$(this).val(mes);});
	$('#tp_busca').each(function() {$(this).val('mes');});
	var params = $('#formBuscarMes').serialize();
	ajax_jquery(params,"lancamentosBuscarMesRetorno()");
}

function lancamentosBuscarMesRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	$('#lancamentos').html(dados.lancamentos);
	dTable();
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
ATUALIZAR VALOR DO PLANO DE CONTAS E CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function moeda(id){

  $("#"+id).priceFormat({
    prefix: "",
    centsSeparator: ",",
    thousandsSeparator: "."
  });

}

function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}	

function plcCtrValorAtualizar(form){
	//atualiza valor no campo da aba pc x cr
	var valor_total = 0;
	var ctr_plc_prct = txtToValor($('#ctr_plc_prct_'+form).val());
	if(ctr_plc_prct>0){
		valor_total = txtToValor($("#valor_"+form).val());
		var pc_cr_valor_novo = (valor_total*ctr_plc_prct)/100;
		pc_cr_valor_novo	=	number_format(pc_cr_valor_novo,2,',','.');
		$('#ct_resp_valor_'+form).val(pc_cr_valor_novo);
	}
	//atualiza valores incluídos na aba pc x cr
	var lancamentos = $("#ct_resp_lancamentos_"+form).val();
	if(lancamentos != ""){
		//alert(lancamentos);
		lancamentos = JSON.parse(lancamentos); 
		valor_total = txtToValor($("#valor_"+form).val());
		var qtd_lancamentos = lancamentos.length;
		var valor_novo = 0, porcentagem = 0, conta = 0, plano_contas_id = 0, centro = 0, centro_resp_id = 0;
		$('.ct_resp_lancamento').remove();
		for(var i=0;i<qtd_lancamentos;i++){
			if(lancamentos[i].operacao!=3){
				porcentagem = txtToValor(lancamentos[i].porcentagem);
				valor_novo = (valor_total*porcentagem)/100;
				valor_novo = number_format(valor_novo,2,',','.');
				porcentagem = number_format(porcentagem,2,',','');
				lancamentos[i].valor = valor_novo;
				conta = lancamentos[i].conta;
				plano_contas_id = lancamentos[i].plano_contas_id;
				centro = lancamentos[i].centro;
				centro_resp_id = lancamentos[i].centro_resp_id;
				lancamentos[i] = {'ctr_plc_lancamento_plnj_id':lancamentos[i].ctr_plc_lancamento_plnj_id,'plano_contas_id':plano_contas_id,'conta':conta,'centro_resp_id':centro_resp_id,'centro':centro,'valor':valor_novo,'porcentagem':porcentagem,'operacao':lancamentos[i].operacao};
				$("#ct_resp_exibir_"+form).append('<li class="ct_resp_lancamento" id="ct_resp_lancamento'+i+'"><div class="floatL"><img src="images/icons/updateDone.png" alt="" style="padding-top: 2px; padding-right: -3px;"> <br /><img src="images/icons/updateDone.png" alt="" style="padding-top: 4px; padding-right: -3px;"></div><div class="pInfo" align="left"><strong>PC: </strong>'+conta+'<br /><strong>CR: </strong>'+centro+'</div><div class="floatR"><span style="display: inline-table; vertical-align: top;">R$ '+valor_novo+' <br />'+porcentagem+'%</span><span style="display: inline-table;"><a href="javascript://void(0);" title="Excluír" class="smallButton" style="margin-top: 5px; margin-left: 5px; width: 14px;" onClick="centroRespLnctExcluir(\''+form+'\',\''+i+'\')"><img src="images/icons/dark/close.png" alt=""></a></span></div></li>');
			}
		}
		lancamentos = JSON.stringify(lancamentos);
		$("#ct_resp_lancamentos_"+form).val(lancamentos);
	}
}

/*
===========================================================================================
VALIDAR INCLUSÃO NO CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function ctRespValidar(form){
	//valida conta e centro
	var centro = $("#ct_resp_id_"+form).val();
	var conta = $("#pl_conta_id_"+form).val();
	//valida valor
	var lancamentos = $("#ct_resp_lancamentos_"+form).val();
	if(lancamentos != ""){
		lancamentos = JSON.parse(lancamentos);
		var vl_total = txtToValor($('#valor_'+form).val());
		var vl_acumulado = 0;
		var vl_lancamento = 0;
		var prct_acumulada = 0;
		var porcentagem = 0;
		var qtd_lancamentos = lancamentos.length;
		for(var i=0;i<qtd_lancamentos;i++){
			if(lancamentos[i].operacao!=3){
				vl_lancamento = txtToValor(lancamentos[i].valor);
				vl_acumulado += vl_lancamento;
				porcentagem = txtToValor(lancamentos[i].porcentagem);
				prct_acumulada += porcentagem;
			}
		}
		var ct_resp_valor = txtToValor($("#ct_resp_valor_"+form).val());
		var vl_maximo = vl_total - vl_acumulado;
		//alert(valor_acumulado);
	}
	if(centro=="0" && conta=="0"){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
			{
				text: "OK",
				click: function() { $("#dialog-alerta").dialog("close"); }
			}		
		]);

		$('#dialog-alerta').html("<br/> Selecione uma conta ou centro para inserir o valor.");

		$('#dialog-alerta').dialog('open');
		return false;
	}else if(ct_resp_valor > vl_maximo){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
			{
				text: "OK",
				click: function() { $("#dialog-alerta").dialog("close"); }
			}		
		]);

		$('#dialog-alerta').html("<br/> A soma dos valores excede o valor total do lançamento.");

		$('#dialog-alerta').dialog('open');
		return false;
	}else{
		return true;
	}
}

/*
===========================================================================================
CALCULAR VALOR E PORCENTAGEM NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

//formata a porcentagem
function porcentatem(id){

  $("#"+id).priceFormat({
    prefix: "",
    centsSeparator: ",",
  });

}

//converte valor para porcentagem
function valorPorcentagem(form){

	var valor_total = $('#valor_'+form).val(), valor_PC_CR = $('#ct_resp_valor_'+form).val(), porcentagem = 0;
	//alert(valor_PC_CR+' '+valor_total);
	valor_total = txtToValor(valor_total);
	valor_PC_CR = txtToValor(valor_PC_CR);
	//alert(valor_PC_CR+' '+valor_total);
	if(valor_PC_CR > valor_total){
		$('#ct_resp_valor_'+form).val($('#valor_'+form).val());
		$('#ctr_plc_prct_'+form).val('100,00');
	}else{
		//alert(valor_PC_CR+' '+valor_total);
		porcentagem = (valor_PC_CR*100)/valor_total;
		$('#ctr_plc_prct_'+form).val(porcentagem.toFixed(2));
		porcentatem('ctr_plc_prct_'+form);
	}

}

//converte porcentagem para valor
function porcentagemValor(form){

	var valor = $('#valor_'+form).val();
	var valor = txtToValor(valor);
		
	var valor_PC_CR = $('#ctr_plc_prct_'+form).val(); 
	valor_PC_CR = txtToValor(valor_PC_CR);
		
	if(valor_PC_CR > 100){ valor_PC_CR = 100; $('#ctr_plc_prct_'+form).val('100')} //validação
				
	var calculo = parseFloat(valor_PC_CR) / 100;
	var calculoFinal = parseFloat(valor) * calculo;

	$('#ct_resp_valor_'+form).val(calculoFinal.toFixed(2));
	moeda('ct_resp_valor_'+form);

}

/*
===========================================================================================
INCLUÍR LANÇAMENTO NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function centroRespLnctIncluirPlnj(form){
	var lancamentos = [];

	var plano_contas_id = $('#'+form+'_pl_conta_id').val();
	var centro_resp_id = $('#'+form+'_ct_resp_id').val();
	var valor = $('#'+form+'_valor').val();
	var porcentagem = 100;

	if(plano_contas_id!=0 || centro_resp_id!=0){
		lancamentos.push({'ctr_plc_lancamento_id':0,'plano_contas_id':plano_contas_id,'centro_resp_id':centro_resp_id,'valor':valor,'porcentagem':porcentagem,'operacao':1});
		lancamentos = JSON.stringify(lancamentos);
		$('#'+form+'_ctr_plc_lnct').val(lancamentos);
	}
}

/*
===========================================================================================
EXIBIR LANÇAMENTOS DO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function ctrPlcLancamentosExibirPlnj(form, ctr_plc_lancamentos) {
	var lancamentos = JSON.parse(ctr_plc_lancamentos);
	if(lancamentos.length>0){
		$('#'+form+'_pl_conta_buscar').val(lancamentos[0].conta);
		$('#'+form+'_ct_resp_buscar').val(lancamentos[0].centro);
		$('#'+form+'_pl_conta_id').val(lancamentos[0].plano_contas_id);
		$('#'+form+'_ct_resp_id').val(lancamentos[0].centro_resp_id);
		if(lancamentos[0].plano_contas_id>0){
			$("#"+form+' span.check-green').eq(0).css('display','block');
			$("#"+form+' .plano_contas_buscar').attr('disabled',true);
		}
		if(lancamentos[0].centro_resp_id>0){
			$("#"+form+' span.check-green').eq(1).css('display','block');
			$("#"+form+' .centro_resp_buscar').attr('disabled',true);
		}
	}
}

/*
===========================================================================================
CONVERTER TEXTO PARA VALOR
===========================================================================================
*/

/*
$.fn.txtToValor = function(){
	var txt = $(this).val();
	txt =	txt.replace('.','');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
}
*/

function txtToValor(valor){
	var txt = valor;
	txt = txt.replace(/\./g, '');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
}
/*
$.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}

$(document).ready(function(){
	var lastScrollTop = 0;
	var conta = 1;
	$('.scroll').scroll(function(e) {
		var st = $(this).scrollTop();
		if (st > lastScrollTop){
			//down scroll
			if(conta<3){
				conta ++;
				$(this).scrollTo('#conta'+conta);
				alert(conta);
			}		
		}else{
			//up scroll
			if(conta>1){
				conta --;
				$(this).scrollTo('#conta'+conta);
			}
		}
		lastScrollTop = st;
  });
})
*/



