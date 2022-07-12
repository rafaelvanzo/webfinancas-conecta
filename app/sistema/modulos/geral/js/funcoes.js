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
      url: 'modulos/lancamento/php/funcoes.php', //url para onde será enviada as informações digitadas
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
      error: function(erro){
      }
	  
    })

}

$(document).ready(function(e) {

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

	//===== UI dialog - Incluir recebimento =====//

	$( "#dialog-rcbt-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {			
			Quitar: function() {
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
		buttons: {
			Salvar: function() {
				recebimentosEditar();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
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
		buttons: {
			Quitar: function() {
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
		buttons: {
			Salvar: function() {
				pagamentosEditar();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') );}  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-pgto-editar" ).click(function() {
		$( "#dialog-pgto-editar" ).dialog( "open" );
		return false;
	});

	//===== UI dialog - Incluir transferência =====//

	$( "#dialog-trans-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Quitar: function() {
				transferenciasIncluir();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
	});
	
	$( "#opener-trans-incluir" ).click(function() {
		lancamentosLimpar('form_trans');
		$( "#dialog-trans-incluir" ).dialog( "open" );
		return false;
	});
	
	//===== UI dialog - Editar transferência =====//

	$( "#dialog-trans-editar" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Salvar: function() {
				transferenciasEditar();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
	});
	
	$( "#opener-trans-editar" ).click(function() {
		$( "#dialog-trans-editar" ).dialog( "open" );
		return false;
	});
    
});

/*
========================================================================================================================
AUTO COMPLETAR
========================================================================================================================
*/

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
			$.getJSON( "modulos/lancamento/paginas/centro_resp_buscar.php", request, function( data, status, xhr ) {
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
			$.getJSON( "modulos/lancamento/paginas/plano_contas_buscar.php", request, function( data, status, xhr ) {
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

/*
========================================================================================================================
CHECAR TODOS
========================================================================================================================
*/
	
	$('#checkAllCf').click(function(e) {
		var checkedStatus = this.checked;
		$('#contasFinanceiras div.checker span input:checkbox').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	});
	
	$('#checkAllTpLnct').click(function(e) {
		var checkedStatus = this.checked;
		$('#tpLnct div.checker span input:checkbox').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	});

	
	$('.checkCf').click(function(e) {
		document.getElementById("checkAllCf").checked = false;
		$('#checkAllCf').closest('.checker > span').removeClass('checked');
	});
	
	
	$('.checkLnct').click(function(e) {
		document.getElementById("checkAllTpLnct").checked = false;
		$('#checkAllTpLnct').closest('.checker > span').removeClass('checked');
	});	

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

	oTable = $('.dTableLancamentos').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
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
	oTableLancamentos = $('.dTableLancamentos').dataTable();
	oTableLancamentos.fnSetColumnVis( 0, false );
	oTableLancamentos.fnSort( [ [0,'asc'] ] );
	*/

//});

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
===========================================================================================
Ativar Checkbox, Radio e Title estilizados
===========================================================================================
*/

function ativarCROT(ref,id_obj){

	if(ref == "t"){

		$(".tipN").tipsy({gravity: "n",fade: true});
		$(".tipS").tipsy({gravity: "s",fade: true});
		$(".tipW").tipsy({gravity: "w",fade: true});
		$(".tipE").tipsy({gravity: "e",fade: true});
		
		for(id in id_obj){
			$(id+" input:checkbox, input:radio, input:file").uniform();
		}
		
	}else{

		$(".tipN").tipsy({gravity: "n",fade: true});
		$(".tipS").tipsy({gravity: "s",fade: true});
		$(".tipW").tipsy({gravity: "w",fade: true});
		$(".tipE").tipsy({gravity: "e",fade: true});
		//$("input:checkbox, input:radio, input:file").uniform();

	}
}

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
			{ "bVisible": false, "aTargets": [ 0 ] } //torna uma coluna invisivel
		],
		"oLanguage": {
			"sLengthMenu": "<span>Mostrar:</span> _MENU_",
			"sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
		}
	});
	var id_obj = ['#lancamentos'];
	ativarCROT('t',id_obj);/* Reaplicando Chackbox, Radio e Title */
	
}

/*
===========================================================================================
INCLUÍR RECEBIMENTO
===========================================================================================
*/

function recebimentosIncluir(){
	if($('#form_rcbt').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_rcbt').serialize();
		var params_busca = filtroParams();
		params += '&filtro='+params_busca;
		$("#dialog-rcbt-incluir").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"recebimentosIncluirRetorno()");
	}
}

function recebimentosIncluirRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('#nomeConta').html(dados.nome_conta);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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

function recebimentosEditar(){
	if($('#form_rcbt_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_rcbt_editar').serialize();
		var params_busca = filtroParams();
		params += '&filtro='+params_busca;
		$("#dialog-rcbt-editar").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"recebimentosEditarRetorno()");
	}
}

function recebimentosEditarRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	$('#lancamentos').html(dados.lancamentos);
	if(dados.situacao==1){
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('#nomeConta').html(dados.nome_conta);
		dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
		var linha = celula.parentNode;
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
	var params_busca = filtroParams();
	params += '&filtro='+params_busca;
	ajax_jquery(params,"recebimentosExcluirRetorno("+indice+")");
}

function recebimentosExcluirRetorno(indice){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		//var tabela = $(".dTableLancamentos").dataTable();
		//tabela.fnDeleteRow(indice);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
		var params = $('#form_pgto').serialize();
		var params_busca = filtroParams();
		params += '&filtro='+params_busca;
		$("#dialog-pgto-incluir").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"pagamentosIncluirRetorno()");
	}
}

function pagamentosIncluirRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('#nomeConta').html(dados.nome_conta);
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

function pagamentosEditar(){
	if($('#form_pgto_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_pgto_editar').serialize();
		var params_busca = filtroParams();
		params += '&filtro='+params_busca;
		$("#dialog-pgto-editar").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"pagamentosEditarRetorno()");
	}
}

function pagamentosEditarRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('#nomeConta').html(dados.nome_conta);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
		var linha = celula.parentNode;
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
	var params_busca = filtroParams();
	params += '&filtro='+params_busca;
	ajax_jquery(params,"pagamentosExcluirRetorno("+indice+")");
}

function pagamentosExcluirRetorno(indice){
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		//var tabela = $(".dTableLancamentos").dataTable();
		//tabela.fnDeleteRow(indice);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
INCLUÍR TRANSFERÊNCIA
===========================================================================================
*/

function transferenciasIncluir(){
	if($('#form_trans').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_trans').serialize();
		var params_busca = filtroParams();
		params += '&filtro='+params_busca;
		$("#dialog-trans-incluir").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"transferenciasIncluirRetorno()");
	}
}

function transferenciasIncluirRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('#nomeConta').html(dados.nome_conta);
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
EDITAR TRANSFERÊNCIA
===========================================================================================
*/

function transferenciasEditar(){
	if($('#form_trans_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_trans_editar').serialize();
		var params_busca = filtroParams();
		params += '&filtro='+params_busca;
		$("#dialog-trans-editar").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"transferenciasEditarRetorno()");
	}
}

function transferenciasEditarRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	$('#lancamentos').html(dados.lancamentos);
	if(dados.situacao==1){
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		$('#nomeConta').html(dados.nome_conta);
		dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
EXCLUÍR TRANSFERÊNCIA
===========================================================================================
*/

$(document).ready(function(){
	$('.transferenciasExcluir').live("click",function(e){

		e.preventDefault();

		var tabela = $(".dTableLancamentos").dataTable();
		var lancamento_id = $(this).attr('href');
		var celula = $(this).parent()[0];
		var linha = celula.parentNode;
		var indice = tabela.fnGetPosition(linha);

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { transferenciasExcluir(lancamento_id,indice); $("#dialog-alerta").dialog("close");}
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

function transferenciasExcluir(lancamento_id,indice){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=transferenciasExcluir&lancamento_id="+lancamento_id;
	var params_busca = filtroParams();
	params += '&filtro='+params_busca;
	ajax_jquery(params,"transferenciasExcluirRetorno("+indice+")");
}

function transferenciasExcluirRetorno(indice){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao==1){
		$('#contasSaldo').html(dados.contas_saldo);
		$('.saldoTotal').html(dados.saldo_total);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$('#lancamentos').html(dados.lancamentos);
		dTable();
		//var tabela = $(".dTableLancamentos").dataTable();
		//tabela.fnDeleteRow(indice);
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
	//alert($("#"+form+" div.ct_resp_lancamento").length);
	$("#"+form+" div.boxScroll").remove();
	$("#"+form+" input[name='ct_resp_lancamentos']").val("");
	$('.lnct_dados').css('display','block');
	$('.lnct_ctr_plc').css('display','none');
	$('span.check-green').css('display','none');
	//cor do titulo ativo #2B6893
	//cor do titulo inativo #595959
	
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
EXIBIR LANÇAMENTO
===========================================================================================
*/

function lancamentosExibir(lancamento_id,tp_lancamento){
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=lancamentosExibir";
	params += "&lancamento_id="+lancamento_id;
	params += "&tp_lancamento="+tp_lancamento;
	ajax_jquery(params,"lancamentosExibirRetorno()");
}

function lancamentosExibirRetorno(){
  //alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.lancamento.tipo=="R"){
		lancamentosLimpar('form_rcbt_editar');
		var form_id = "#form_rcbt_editar";
		var dialog_id = "#dialog-rcbt-editar";
		$(form_id+" input[name='lancamento_id']").val(dados.lancamento.id);
		$(form_id+" input[name='conta_id_ini']").val(dados.lancamento.conta_id);
		$(form_id+" input[name='valor_ini']").val(dados.lancamento.valor);
		$(form_id+" input[name='descricao']").val(dados.lancamento.descricao);
		$(form_id+" input[name='favorecido_rcbt_editar_id']").val(dados.lancamento.favorecido);
		$(form_id+" input[name='favorecido_id']").val(dados.lancamento.favorecido_id);
		$(form_id+" input[name='conta_rcbt_editar_id']").val(dados.lancamento.conta);
		$(form_id+" input[name='conta_id']").val(dados.lancamento.conta_id);
		$(form_id+" input[name='dt_emissao']").val(dados.lancamento.dt_emissao);
		$(form_id+" input[name='dt_vencimento']").val(dados.lancamento.dt_vencimento);
		$(form_id+" input[name='dt_compensacao']").val(dados.lancamento.dt_compensacao);
		$(form_id+" input[name='valor']").val(dados.lancamento.valor);
		$(form_id+" select[name='documento_id']").val(dados.lancamento.documento_id);
		$(form_id+" select[name='forma_pgto_id']").val(dados.lancamento.forma_pgto_id);
		$(form_id+" textarea[name='observacao']").val(dados.lancamento.observacao);
		ctrPlcLancamentosExibir("form_rcbt_editar",dados.ctr_plc_lancamentos);
		$(form_id+' span.check-green').eq(0).css('display','block');
		$(form_id+' span.check-green').eq(1).css('display','block');
	}else if(dados.lancamento.tipo=="P"){
		lancamentosLimpar('form_pgto_editar');
		var form_id = "#form_pgto_editar";
		var dialog_id = "#dialog-pgto-editar";
		$(form_id+" input[name='lancamento_id']").val(dados.lancamento.id);
		$(form_id+" input[name='conta_id_ini']").val(dados.lancamento.conta_id);
		$(form_id+" input[name='valor_ini']").val(dados.lancamento.valor);
		$(form_id+" input[name='descricao']").val(dados.lancamento.descricao);
		$(form_id+" input[name='favorecido_pgto_editar_id']").val(dados.lancamento.favorecido);
		$(form_id+" input[name='favorecido_id']").val(dados.lancamento.favorecido_id);
		$(form_id+" input[name='conta_pgto_editar_id']").val(dados.lancamento.conta);
		$(form_id+" input[name='conta_id']").val(dados.lancamento.conta_id);
		$(form_id+" input[name='dt_emissao']").val(dados.lancamento.dt_emissao);
		$(form_id+" input[name='dt_vencimento']").val(dados.lancamento.dt_vencimento);
		$(form_id+" input[name='dt_compensacao']").val(dados.lancamento.dt_compensacao);
		$(form_id+" input[name='valor']").val(dados.lancamento.valor);
		$(form_id+" select[name='documento_id']").val(dados.lancamento.documento_id);
		$(form_id+" select[name='forma_pgto_id']").val(dados.lancamento.forma_pgto_id);
		$(form_id+" textarea[name='observacao']").val(dados.lancamento.observacao);
		ctrPlcLancamentosExibir("form_pgto_editar",dados.ctr_plc_lancamentos);
		$(form_id+' span.check-green').eq(0).css('display','block');
		$(form_id+' span.check-green').eq(1).css('display','block');
	}else{
		lancamentosLimpar('form_trans_editar');
		var form_id = "#form_trans_editar";
		var dialog_id = "#dialog-trans-editar";
		$(form_id+" input[name='lancamento_id']").val(dados.lancamento.id);
		$(form_id+" input[name='conta_id_origem_ini']").val(dados.lancamento.conta_id_origem);
		$(form_id+" input[name='conta_id_destino_ini']").val(dados.lancamento.conta_id_destino);
		$(form_id+" input[name='conta_id_origem']").val(dados.lancamento.conta_id_origem);
		$(form_id+" input[name='conta_id_destino']").val(dados.lancamento.conta_id_destino);
		$(form_id+" input[name='valor_ini']").val(dados.lancamento.valor);
		$(form_id+" input[name='valor']").val(dados.lancamento.valor);
		$(form_id+" input[name='descricao']").val(dados.lancamento.descricao);
		$(form_id+" input[name='conta_trans_id_origem_editar']").val(dados.lancamento.conta_origem);
		$(form_id+" input[name='conta_trans_id_destino_editar']").val(dados.lancamento.conta_destino);
		$(form_id+" input[name='dt_emissao']").val(dados.lancamento.dt_emissao);
		$(form_id+" input[name='dt_vencimento']").val(dados.lancamento.dt_vencimento);
		$(form_id+" input[name='dt_compensacao']").val(dados.lancamento.dt_compensacao);
		$(form_id+" textarea[name='observacao']").val(dados.lancamento.observacao);
		$(form_id+' span.check-green').css('display','block');
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
PESQUISAR LANÇAMENTOS
===========================================================================================
*/

function lancamentosFiltrar(){
	$("span.aguarde, div.aguarde").css("display","block");
	var filtro = filtroParams();
	var params = {
		funcao: 'lancamentosFiltrar',
		filtro: filtro
	}
	ajax_jquery(params,"lancamentosFiltrarRetorno(data)");
}

function lancamentosFiltrarRetorno(data){
	//alert(data);
	//var dados = JSON.parse(data);
	$('#lancamentos').html(data);
	dTable();
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
PEGAR PARÂMETROS DO FILTRO
===========================================================================================
*/

function filtroParams(){

	//data
	var flt_dt_ativo = document.getElementsByClassName('dtFltAtivo')[0].value;
	var dt_ini = "";
	var dt_fim = "";
	var dt_mes = "";
	if(flt_dt_ativo=='periodo'){
		dt_ini = document.getElementById("dt_ini").value;
		dt_fim = document.getElementById("dt_fim").value;
	}else{
		dt_mes = document.getElementById("dt_ini_m").value;
	}

	//contas financeiras
	var array_contas_id = new Array();
	$('#contasFinanceiras div.checker span.checked input[type="checkbox"]').each(function() {
		array_contas_id.push($(this).val());
	});
	var flt_contas = array_contas_id.join(',');
	
	//tipo de lançamento
	var array_tp_lnct = new Array();
	$('#tpLnct div.checker span.checked input[type="checkbox"]').each(function() {
		array_tp_lnct.push("'"+$(this).val()+"'");
	});
	var flt_tp_lnct = array_tp_lnct.join(',');

	var flt_valor = document.getElementById("vl_pesq").value;
	var flt_centro_resp = document.getElementById("ct_resp_pesq").value;
	var flt_plano_contas = document.getElementById("pl_cnt_pesq").value;
	var flt_favorecido = document.getElementById("fav_pesq").value;
	var flt_parcelado = document.getElementById("prcl_pesq").checked
	var flt_documento = document.getElementById("doc_pesq").value;
	var flt_forma_pgto = document.getElementById("forma_pgto_pesq").value;
	//var flt_descricao = document.getElementById("dscr_pesq").value;

	//parametros
	var params = {
		//funcao: "lancamentosFiltrar",
		dt_ativo: flt_dt_ativo,
		dt_ini: dt_ini,
		dt_fim: dt_fim,
		dt_mes: dt_mes,
		conta_id: flt_contas,
		tp_lnct: flt_tp_lnct,
		valor: flt_valor,
		//descricao: flt_descricao,
		centro_resp_id: flt_centro_resp,
		plano_contas_id: flt_plano_contas,
		favorecido_id: flt_favorecido,
		documento_id: flt_documento,
		forma_pgto_id: flt_forma_pgto,
		parcelado: flt_parcelado
	};

	params = JSON.stringify(params);
	
	return params;
	
}

/*
===========================================================================================
MUDAR TIPO DE DATA DO FILTRO
===========================================================================================
*/

function mudaDtFlt(de_dtFlt,para_dtFlt){
	$('#'+de_dtFlt).removeClass('dtFltAtivo');
	$('#'+para_dtFlt).addClass('dtFltAtivo');
	$('.'+de_dtFlt).val('');
}

/*
===========================================================================================
LIMPAR FILTRO DA PESQUISA
===========================================================================================
*/

function fltLimpar(){
	$("#formPesq input:text").each(function(index, element) {
    $(this).val('');
  });

	$("#formPesq select").each(function(index, element) {
    $(this).val('');
  });
	
	$('#formPesq div.checker span input:checkbox').each(function() {
		this.checked = false;
		$(this).closest('.checker > span').removeClass('checked');
	});	

	$("#formPesq input.buscar:hidden").each(function(index, element) {
    $(this).val('');
  });
	
	$('#formPesq span.check-green').css('display','none');

	//retira contadores e exibe icone de lista
	var counters = document.getElementsByClassName('countList');
	var icons = document.getElementsByClassName('iconList');
	var i;
	for(i=0;i<counters.length;i++){
		counters[i].style.display = 'none';
		icons[i].style.display = 'block';
	}
}

/*
===========================================================================================
CALCULAR FILTROS SELECIONADOS
===========================================================================================
*/

function filtroCalcular(lista_class){ 

	if(lista_class){

		var selected = 0;
		var icon_list, count_list;
		
		if(lista_class=='listItens1'){
	
			icon_list = "iconList1";
			count_list = "countList1";
			$('#contasFinanceiras div.checker span input:checkbox').each(function() {
				var checkedStatus = this.checked
				if (checkedStatus == true) {
					selected ++;
				}
			})
	
		}else if(lista_class=='listItens2'){
	
			icon_list = "iconList2";
			count_list = "countList2";
			$('#tpLnct div.checker span input:checkbox').each(function() {
				var checkedStatus = this.checked
				if (checkedStatus == true) {
					selected ++;
				}
			})
	
		}else if(lista_class=='listItens3'){
	
			icon_list = "iconList3";
			count_list = "countList3";
			var ctr = document.getElementById("ct_resp_pesq").value;
			var plc = document.getElementById("pl_cnt_pesq").value;
			if(ctr!=0){
				selected += 1;
			}
			if(plc!=0){
				selected += 1;
			}
	
		}else if(lista_class=='listItens5'){
	
			icon_list = "iconList5";
			count_list = "countList5";
			var fav = document.getElementById("fav_pesq").value;
			var valor = document.getElementById("vl_pesq").value;
			var doc = document.getElementById("doc_pesq").value;
			var forma_pgto = document.getElementById("forma_pgto_pesq").value;
			var prcl = document.getElementById("prcl_pesq").checked;
			
			if(fav!=0){selected += 1;}
			if(valor!="0,00" && valor!=""){selected += 1;}
			if(doc!=""){selected += 1;}
			if(forma_pgto!=""){selected += 1;}
			if(prcl==true){selected += 1;}
			
		}
	
		if(selected>0){
			document.getElementById(count_list).innerHTML = selected;
			document.getElementById(icon_list).style.display = 'none';
			document.getElementById(count_list).style.display = 'block';
		}else{
			document.getElementById(count_list).innerHTML = '0';
			document.getElementById(count_list).style.display = 'none';
			document.getElementById(icon_list).style.display = 'block';
		}		
	}
}

/*
===========================================================================================
SELECIONAR FILTROS
===========================================================================================
*/

function filtroSelecionar(lista_class){
	if(lista_class){
		filtroCalcular(lista_class);
		$('.'+lista_class).hide();
	}
	lancamentosFiltrar();
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
				lancamentos[i] = {'ctr_plc_lancamento_id':lancamentos[i].ctr_plc_lancamento_id,'plano_contas_id':plano_contas_id,'conta':conta,'centro_resp_id':centro_resp_id,'centro':centro,'valor':valor_novo,'porcentagem':porcentagem,'operacao':lancamentos[i].operacao};
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

function centroRespLnctIncluir(form){
	var validacao = ctRespValidar(form);
	if(validacao){
	//alert($("#ct_resp_valor_"+form).txtToValor());
	//$("#ct_resp_buscar_"+form).addClass("required");
	//var validator = $("#"+form).validate();
	//if(validator.element("#ct_resp_buscar_"+form)){
		//$("span.aguarde, div.aguarde").css("display","block");
		var lancamentos = $("#ct_resp_lancamentos_"+form).val();
		//alert(lancamentos);
		if(lancamentos == ""){
			lancamentos = [];
		}else{
			lancamentos = JSON.parse(lancamentos);
		}
		var qtd_lancamentos = lancamentos.length;
		var conta = $("#pl_conta_buscar_"+form).val();
		var plano_contas_id = $("#pl_conta_id_"+form).val();
		var centro = $("#ct_resp_buscar_"+form).val();
		var centro_resp_id = $("#ct_resp_id_"+form).val();
		var valor = $("#ct_resp_valor_"+form).val();
		var porcentagem = $("#ctr_plc_prct_"+form).val();
		lancamentos.push({'ctr_plc_lancamento_id':0,'plano_contas_id':plano_contas_id,'conta':conta,'centro_resp_id':centro_resp_id,'centro':centro,'valor':valor,'porcentagem':porcentagem,'operacao':1});
		lancamentos = JSON.stringify(lancamentos);
		$("#ct_resp_lancamentos_"+form).val(lancamentos);
		var qtd_registros = $("#ct_resp_exibir_"+form+" li").length;
		if(qtd_registros==0){
			$("#"+form+" div.ctr_plc_container").append('<div class="boxScroll"><ul class="partners" id="ct_resp_exibir_'+form+'"></ul></div>');
		}
		$("#ct_resp_exibir_"+form).append('<li class="ct_resp_lancamento" id="ct_resp_lancamento'+qtd_lancamentos+'"><div class="floatL"><img src="images/icons/updateDone.png" alt="" style="padding-top: 2px; padding-right: -3px;"> <br /><img src="images/icons/updateDone.png" alt="" style="padding-top: 4px; padding-right: -3px;"></div><div class="pInfo" align="left"><strong>PC: </strong>'+conta+'<br /><strong>CR: </strong>'+centro+'</div><div class="floatR"><span style="display: inline-table; vertical-align: top;">R$ '+valor+' <br />'+porcentagem+'%</span><span style="display: inline-table;"><a href="javascript://void(0);" title="Excluír" class="smallButton" style="margin-top: 5px; margin-left: 5px; width: 14px;" onClick="centroRespLnctExcluir(\''+form+'\',\''+qtd_lancamentos+'\')"><img src="images/icons/dark/close.png" alt=""></a></span></div></li>');		
		$('#pl_conta_id_'+form+'_cg').css('display','none');
		$('#ct_resp_id_'+form+'_cg').css('display','none');
		//limpa os campos do centro de responsabilidade e plano de contas após incluir um valor
		$("#pl_conta_buscar_"+form).val("");
		$("#pl_conta_id_"+form).val("0");
		$("#ct_resp_buscar_"+form).val("");
		$("#ct_resp_id_"+form).val("0");
		$("#ct_resp_valor_"+form).val("0,00");
		$("#ctr_plc_prct_"+form).val("0,00");
		//alert(valor_acumulado);
		//alert(lancamentos);
	//}
	}
	//$("#ct_resp_buscar_"+form).removeClass("required");
}

/*
===========================================================================================
EXCLUIR LANÇAMENTO DO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function centroRespLnctExcluir(form,lancamento_id){
	var lancamentos = $("#ct_resp_lancamentos_"+form).val();
	lancamentos = JSON.parse(lancamentos);
	lancamentos[lancamento_id].operacao = 3;
	lancamentos = JSON.stringify(lancamentos);
	$("#ct_resp_lancamentos_"+form).val(lancamentos);
	$("#"+form+" li#ct_resp_lancamento"+lancamento_id).remove();
	var qtd_registros = $("#ct_resp_exibir_"+form+" li").length;
	if(qtd_registros==0){
		$("#"+form+" div.boxScroll").remove();
	}
	//alert(lancamentos);
}

/*
===========================================================================================
EXIBIR LANÇAMENTOS DO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function ctrPlcLancamentosExibir(form,ctr_plc_lancamentos){
	//alert(ctr_plc_lancamentos);
	var lancamentos = JSON.parse(ctr_plc_lancamentos);
	var jsonObj = [];
	var total_lancamentos = lancamentos.length;
	if(total_lancamentos>0){
		$("#"+form+" div.ctr_plc_container").append('<div class="boxScroll"><ul class="partners" id="ct_resp_exibir_'+form+'"></ul></div>');
	}
	for(var i=0;i<total_lancamentos;i++){
		var ctr_plc_lancamento_id = lancamentos[i].ctr_plc_lancamento_id;
		var conta = lancamentos[i].conta;
		var plano_contas_id = lancamentos[i].plano_contas_id;
		var centro = lancamentos[i].centro;
		var centro_resp_id = lancamentos[i].centro_resp_id;
		var valor = lancamentos[i].valor;
		var porcentagem = lancamentos[i].porcentagem;
		$("#ct_resp_exibir_"+form).append('<li class="ct_resp_lancamento" id="ct_resp_lancamento'+i+'"><div class="floatL"><img src="images/icons/updateDone.png" alt="" style="padding-top: 2px; padding-right: -3px;"> <br /><img src="images/icons/updateDone.png" alt="" style="padding-top: 4px; padding-right: -3px;"></div><div class="pInfo" align="left"><strong>PC: </strong>'+conta+'<br /><strong>CR: </strong>'+centro+'</div><div class="floatR"><span style="display: inline-table; vertical-align: top;">R$ '+valor+' <br />'+porcentagem+'%</span><span style="display: inline-table;"><a href="javascript://void(0);" title="Excluír" class="smallButton" style="margin-top: 5px; margin-left: 5px; width: 14px;" onClick="centroRespLnctExcluir(\''+form+'\',\''+i+'\')"><img src="images/icons/dark/close.png" alt=""></a></span></div></li>');
		jsonObj.push({'ctr_plc_lancamento_id':ctr_plc_lancamento_id,'plano_contas_id':plano_contas_id,'conta':conta,'centro_resp_id':centro_resp_id,'centro':centro,'valor':valor,'porcentagem':porcentagem,'operacao':2});
	}
	jsonTxt = JSON.stringify(jsonObj);
	$("#ct_resp_lancamentos_"+form).val(jsonTxt);
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
===========================================================================================
AJUDA - TOUR
===========================================================================================
*/
/* ============ Ajuda Inicial ============*/
/*
 TOURS = {

    ajuda: function (){
      var steps = [{
        content: '<p>Visualize o <b>saldo</b> e limite de crédito de todas as conta financeira. </p>',
			  highlightTarget: true,
				//closeButton: true,
        nextButton: true,
        target: $('#AjudaInicial1'),
        my: 'top center',
        at: 'bottom center'
      }, {
        content: '<p>Utilizando a barra de rolagem você poderá visualizar todas as suas contas financeiras.</p>',
        highlightTarget: true,
				//closeButton: true,
        nextButton: true,
        target: $('#AjudaInicial2'),
        my: 'left center',
        at: 'right center'
      }, {
        content: '<p>Clicando aqui você poderá visualizar o valor total do SALDO e CRÉDITO disponível em suas contas financeiras.</p>',
        highlightTarget: true,
				//closeButton: true,
        nextButton: true,
        target: $('#AjudaInicial3'),
        my: 'left center',
        at: 'right center'
      }]

      var tour = new Tourist.Tour({
        steps: steps,
        tipClass: 'Bootstrap',
        tipOptions:{ showEffect: 'slidein' }
      });
      tour.start();
      return tour; //Para incializar o plugIn automaticamente deixe o RETURN TOUR comentado
    }	//, // end tour1
  }

   $(function(){

    $('.bt_ajudaInteligente2').click(function(e){
      var btn = $(e.target);
      var tour = TOURS[btn.attr('data-tour')]();
			//alert("teste");

     btn.prop('disabled', true)
      tour.bind('stop', function(){
        btn.prop('disabled', false)
      }); 

      return false;
    });
		
 });
*/
 // ======== Ajuda Iniciar com a página ========
/*
  var steps = [{
  // this is a step object
  content: '<p>Hey user, check this out!</p>',
  highlightTarget: true,
  nextButton: true,
  target: $('#AjudaInicial1'),
  my: 'top center',
  at: 'bottom center'
}]

$(window).load(function(){
	var tour = new Tourist.Tour({
  steps: steps,
  tipOptions:{ showEffect: 'slidein' }
	});
	tour.start();
}); */