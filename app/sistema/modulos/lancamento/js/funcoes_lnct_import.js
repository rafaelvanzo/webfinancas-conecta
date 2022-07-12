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
      url: 'modulos/lancamento/php/funcoes_lnct_import.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      beforeSend: function(){
        //Ação que será executada após o envio, no caso, chamei um gif loading para dar a impressão de garregamento na página
		//carregando();
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
===========================================================================================
MUDAR SELEÇÃO RADIO PARA MÊS E PERÍODO
===========================================================================================
*/

function changeRadio(radioId){
	/*
	//var conta_id_atual = $(".conta_id").val();
	$(".conta_id").val(conta_id);
	//alert($("#cId"+conta_id).attr('checked'));
	$("#uniform-cId"+conta_id_atual+" span").removeClass('checked');
	$("#cId"+conta_id_atual).attr("checked",false);
	
	$("#uniform-cId"+conta_id+" span").addClass('checked');
	$("#cId"+conta_id).attr("checked",true);
	*/
	if(radioId=="input_lnct_buscar"){
		var i;
		var lnctSugest = document.getElementsByClassName("lnctSugest");
		var qtdLnct = lnctSugest.length;
		for(i=0;i<qtdLnct;i++){
			lnctSugest[i].checked = false;
		}
		//alert(lnctSugest.length);
	}else{
		document.getElementById("input_lnct_buscar").value = "";
		document.getElementById("lnct_buscar_id").value = "";
		document.getElementById("lnct_buscar_id_cg").style.display = "none";
	}
}

/*
===========================================================================================
REPOCIOCINAR ABAS DO DIALOG
===========================================================================================
*/

function resetAbasDialog(id){
	$('#'+id+' div.menu_body:eq(0)').slideDown(1);
	$('#'+id+' div.menu_body_buscar:eq(0)').slideDown(1);
	$('#'+id+' .acc .title:eq(0)').show().css({color:"#2B6893"});
	$('#'+id+' div.menu_body:eq(1)').slideUp(1);
	$('#'+id+' div.menu_body_buscar:eq(1)').slideUp(1);
	$('#'+id+' .acc .title:eq(1)').show().css({color:"#666"}); 
}

/*
===========================================================================================
REDESENHAR DATA TABLE LANÇAMENTOS
===========================================================================================
*/

function dTable(){
	oTable = $('.dTableLancamentos').dataTable({
		"bInfo": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bPaginate": false,
		"bSort": false,
		//"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"aaSorting": [[ 0, "asc" ]], //inicializa a tabela ordenada pela coluna especificada
		'aoColumnDefs': [
			{ "bVisible": false, "aTargets": [ 0 ] }, //torna uma coluna invisivel
			//{ 'bSortable': false, 'aTargets': [ 0,1,3,4,5 ] }, //define quais colunas não terão ordenação
		],
		"oLanguage": {
			//"sLengthMenu": "<span>Mostrar:</span> _MENU_",
			"sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
		}
	});
}

/*
===========================================================================================
ÍNDICE DE LINHA
===========================================================================================
*/

function linhaIndice(link_id){
	var tabela = $(".dTableLancamentos").dataTable();
	var celula = document.getElementById(link_id).parentNode;
	var linha = celula.parentNode;
	var indice = tabela.fnGetPosition(linha);
	return indice;
}

/*
===========================================================================================
INCLUÍR LANÇAMENTOS IMPORTADOS
===========================================================================================
*/

function lancamentosIncluir(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=lancamentosIncluir&cliente_id="+document.getElementById("cliente_id").value;
	params += "&usuario_id="+document.getElementById("usuario_id").value;
	ajax_jquery(params,"lancamentosIncluirRetorno(data)");
}

function lancamentosIncluirRetorno(data){
	//alert(data);
	var _data = JSON.parse(data);
	document.getElementById("lancamentos").innerHTML = _data.lancamentos;
	dTable();
	ativarCROT();/* Reaplicando Chackbox, Radio e Title */
	$('.nSuccess p').html(_data.notificacao);
	$('.nSuccess').slideDown();
	setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
EXCLUÍR LANÇAMENTOS
===========================================================================================
*/

function alertaExcluir(lnct_import_id){
	var indice = linhaIndice('link_excluir_'+lnct_import_id);
		
	$( "#dialog-alerta" ).dialog( "option", "buttons", [
	{
		text: "Sim",
		click: function() { lancamentosExcluir(lnct_import_id,indice); $("#dialog-alerta").dialog("close");}
	},
	{
		text: "Não",
		click: function() { $("#dialog-alerta").dialog("close"); }
	}		
	]);
	
	$('#dialog-alerta').html("<br/> Deseja realmente excluír o registro selecionado?");
	
	$('#dialog-alerta').dialog('open');
}

function lancamentosExcluir(lnct_import_id,indice){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=lancamentosExcluir&lnct_import_id="+lnct_import_id;
	ajax_jquery(params,"lancamentosExcluirRetorno(data,"+indice+")");
}

function lancamentosExcluirRetorno(dados,indice){
	//alert(dados);
	var _dados = JSON.parse(dados);
	$('.nSuccess p').html(_dados.notificacao);
	$('.nSuccess').slideDown();
	setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
	var tabela = $(".dTableLancamentos").dataTable();
	tabela.fnDeleteRow(indice);
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
EXCLUÍR LANÇAMENTOS EM LOTE
===========================================================================================
*/

function alertaExcluirLote(){
	var array_lncts_id = new Array();
	var lncts_id = "";
	$('.lnctCheckbox div.checker span.checked input[type="checkbox"]').each(function() {
		array_lncts_id.push($(this).val());
	});
	if(array_lncts_id.length==0){
		lncts_id = false;
	}else{
		lncts_id = array_lncts_id.join(',');	
	}
	if(lncts_id){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { lnctExcluirLote(lncts_id); $("#dialog-alerta").dialog("close");}
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);
		$('#dialog-alerta').html("<br/> Deseja realmente excluír os registros selecionados?");
		$('#dialog-alerta').dialog('open');
	}else{
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "OK",
			click: function() { $("#dialog-alerta").dialog("close");}
		},
		]);
		$('#dialog-alerta').html("<br/> Nenhum registro foi selecionado.");
		$('#dialog-alerta').dialog('open');
	}
}

function lnctExcluirLote(lncts_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=lnctExcluirLote";
	params += "&lncts_id="+lncts_id;
	//var params_busca = $('#form_busca').serialize();
	//var params = params_rcbt_editar+'&'+params_busca;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_lnct_import.php',
		data: params,
		cache: true,
		success: function(data){
			var dados = JSON.parse(data);
			var indice;
			var array_lncts_id;
			var array_len;
			var tabela = $(".dTableLancamentos").dataTable();
			array_lncts_id = lncts_id.split(",");
			array_len = array_lncts_id.length;
			for(i=0;i<array_len;i++){
				indice = linhaIndice('link_excluir_'+array_lncts_id[i]);
				tabela.fnDeleteRow(indice);
			}
			$('.nSuccess p').html(dados.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			$("span.aguarde, div.aguarde").css("display","none");
		},
	});
}

/*
===========================================================================================
NOVO LANÇAMENTO
===========================================================================================
*/

function novoLancamento(tp_lnct,lnct_id){
	$('#dados').data('lnct_import_id',lnct_id);
	var data = document.getElementById("data_"+lnct_id).innerHTML;
	var data_c = document.getElementById("data_c_"+lnct_id).innerHTML;
	var dscr = document.getElementById("dscr_"+lnct_id).innerHTML;
	var valor = document.getElementById("vl_"+lnct_id).innerHTML;
	if(tp_lnct=="R"){
		lancamentosLimpar('form_rcbt');
		document.getElementById("rcbt_dscr").value = dscr;
		document.getElementById("rcbt_dt_competencia").value = data_c.substring(3,10);
		document.getElementById("rcbt_dt_emissao").value = data_c;
		document.getElementById("rcbt_dt_vencimento").value = data;
		document.getElementById("rcbt_dt_compensacao").value = data;
		document.getElementById("valor_form_rcbt").value = valor;
		//$('#form_rcbt span.check-green').eq(1).css('display','block');
		$("#dialog-rcbt-incluir" ).dialog( "open" );
	}else if(tp_lnct=="P"){
		lancamentosLimpar('form_pgto');
		document.getElementById("pgto_dscr").value = dscr;
		document.getElementById("pgto_dt_competencia").value = data_c.substring(3,10);
		document.getElementById("pgto_dt_emissao").value = data_c;
		document.getElementById("pgto_dt_vencimento").value = data;
		document.getElementById("pgto_dt_compensacao").value = data;
		document.getElementById("valor_form_pgto").value = valor;
		//$('#form_pgto span.check-green').eq(1).css('display','block');
		$("#dialog-pgto-incluir" ).dialog( "open" );
	}else{
		lancamentosLimpar('form_trans');
		document.getElementById("trans_dscr").value = dscr;
		document.getElementById("trans_dt_emissao").value = data_c;
		document.getElementById("trans_dt_vencimento").value = data;
		document.getElementById("trans_dt_compensacao").value = data;
		document.getElementById("valor_form_trans").value = valor;
		$("#dialog-trans-incluir" ).dialog( "open" );
	}
}

/*
===========================================================================================
INCLUÍR RECEBIMENTO
===========================================================================================
*/

function recebimentosIncluir(){
	if($('#form_rcbt').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var lnct_import_id = $('#dados').data('lnct_import_id');
		var indice = linhaIndice("link_excluir_"+lnct_import_id);
		var params_rcbt = $('#form_rcbt').serialize();
		//var params_busca = $('#form_busca').serialize();
		//var params = params_rcbt+'&'+params_busca;
		var params = params_rcbt;
		params += "&lnct_import_id="+lnct_import_id;
		$("#dialog-rcbt-incluir").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"recebimentosIncluirRetorno(data,"+indice+")");
	}
}

function recebimentosIncluirRetorno(dados,indice){
	var _dados = JSON.parse(dados);
	if(_dados.situacao==1){
		$('.nSuccess p').html(_dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		//$('#lancamentos').html(_dados.lancamentos);
		//dTable();
		//$('#contasSaldo').html(dados.contas_saldo);
		//$('.saldoTotal').html(dados.saldo_total);
		//$('#nomeConta').html(dados.nome_conta);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
	}else{
		$('.nWarning p').html(_dados.notificacao);
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
		var lnct_import_id = $('#dados').data('lnct_import_id');
		var indice = linhaIndice("link_excluir_"+lnct_import_id);
		var params_pgto = $('#form_pgto').serialize();
		//var params_busca = $('#form_busca').serialize();
		//var params = params_rcbt+'&'+params_busca;
		var params = params_pgto;
		params += "&lnct_import_id="+lnct_import_id;
		$("#dialog-pgto-incluir").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"pagamentosIncluirRetorno(data,"+indice+")");
	}
}

function pagamentosIncluirRetorno(dados,indice){
	var _dados = JSON.parse(dados);
	if(_dados.situacao==1){
		$('.nSuccess p').html(_dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		//$('#lancamentos').html(_dados.lancamentos);
		//dTable();
		//$('#contasSaldo').html(dados.contas_saldo);
		//$('.saldoTotal').html(dados.saldo_total);
		//$('#nomeConta').html(dados.nome_conta);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
	}else{
		$('.nWarning p').html(_dados.notificacao);
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
		var lnct_import_id = $('#dados').data('lnct_import_id');
		var indice = linhaIndice("link_excluir_"+lnct_import_id);
		var params_trans = $('#form_trans').serialize();
		//params_trans += "&conta_id="+$('#conta_id').val(); //id da conta para exibir lançamentos
		//var params_busca = $('#form_busca').serialize();
		//var params = params_trans+'&'+params_busca;
		var params = params_trans;
		params += "&lnct_import_id="+lnct_import_id;
		$("#dialog-trans-incluir").dialog( "close" );
		ajax_jquery(params,"transferenciasIncluirRetorno(data,"+indice+")");
	}
}

function transferenciasIncluirRetorno(dados,indice){
	var _dados = JSON.parse(dados);
	if(_dados.situacao==1){
		$('.nSuccess p').html(_dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		//$('#lancamentos').html(_dados.lancamentos);
		//dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		//$('#contasSaldo').html(dados.contas_saldo);
		//$('.saldoTotal').html(dados.saldo_total);
		//$('#nomeConta').html(dados.nome_conta);
		var tabela = $(".dTableLancamentos").dataTable();
		tabela.fnDeleteRow(indice);
	}else{
		$('.nWarning p').html(_dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
INCLUÍR LANÇAMENTOS EM LOTE
===========================================================================================
*/

function alertaIncluirLote(){
	var array_lncts_id = new Array();
	var lncts_id = "";
	var r = false, p = false;
	var tipo;
	$('.lnctCheckbox div.checker span.checked input[type="checkbox"]').each(function() {
		array_lncts_id.push($(this).val());
		if( $(this).attr('class')=='R' ){r = true;}else{p = true;}
	});
	if(array_lncts_id.length==0){
		lncts_id = false;
	}else{
		lncts_id = array_lncts_id.join(',');	
	}
	if(lncts_id){
		if(r && p){
			$( "#dialog-alerta" ).dialog( "option", "buttons", [
			{
				text: "OK",
				click: function() { $("#dialog-alerta").dialog("close");}
			},
			]);
			$('#dialog-alerta').html("<br/> Selecione apenas recebimentos ou pagamentos.");
			$('#dialog-alerta').dialog('open');
		}else{
			if(r){tipo='R';}else{tipo='P';}
			$('#dados').data('tipo',tipo);
			$('#dados').data('lncts_id',lncts_id);
			$('#dialog-lnct-lote').dialog('open');
		}
	}else{
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "OK",
			click: function() { $("#dialog-alerta").dialog("close");}
		},
		]);
		$('#dialog-alerta').html("<br/> Nenhum registro foi selecionado.");
		$('#dialog-alerta').dialog('open');
	}
}

function lnctLoteIncluir(){
	if($('#form_lote').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var lncts_id = $('#dados').data('lncts_id');
		var tipo = $('#dados').data('tipo');
		var params = $('#form_lote').serialize();
		params += "&lncts_id="+lncts_id;
		params += "&tp_lnct="+tipo;
		$("#dialog-lnct-lote").dialog( "close" );
		//alert(params);
		ajax_jquery(params,"lnctLoteIncluirRetorno(data)");
	}
}

function lnctLoteIncluirRetorno(data){
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		var indice;
		var array_lncts_id;
		var array_len;
		var lncts_id = $('#dados').data('lncts_id');
		var tabela = $(".dTableLancamentos").dataTable();
		array_lncts_id = lncts_id.split(",");
		array_len = array_lncts_id.length;
		for(i=0;i<array_len;i++){
			indice = linhaIndice('link_excluir_'+array_lncts_id[i]);
			tabela.fnDeleteRow(indice);
		}
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		//$('#lancamentos').html(_dados.lancamentos);
		//dTable();
		//$('#contasSaldo').html(dados.contas_saldo);
		//$('.saldoTotal').html(dados.saldo_total);
		//$('#nomeConta').html(dados.nome_conta);
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
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
LIMPAR FORMULÁRIOS DOS LANÇAMENTOS
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
	$('.input-buscar').attr('disabled',false);
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
RESETAR UPLOAD DE EXTRATO
===========================================================================================
*/

function uploadReset(){
	var uploader = $("#lnct_uploader").pluploadQueue();
	lancamentosLimpar('form_lnct_import');
	//document.getElementById("lnct_uploader").style.display = 'none';
	$('.plupload_total_status').css('display','inline');
	$('.plupload_total_file_size').css('display','inline');
	$("#dialog-lnct-importar").dialog("option", "buttons", { "Fechar": function() {$( this ).dialog( "close" );} });
	$(".plupload_buttons").css("display", "inline");
	$('.plupload_upload_status').css('display','none');
	$("#dialog-lnct-importar").dialog( "open" );
	$('#lnct_uploader > div.plupload input').css('z-index','99999');
	$("#dialog-lnct-importar").dialog( "open" );
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
CHECAR TODOS OS LANÇAMENTOS
===========================================================================================
*/
/*
function lnctChecarTodos(){
	var checkedStatus = $("#lnctChecarTodos").attr("checked");
	if(!checkedStatus){
		checkedStatus = false;
	}
	$('.lnctCheckbox div.checker span input:checkbox').each(function() {
		this.checked = checkedStatus;
		if (checkedStatus == this.checked) {
			$(this).closest('.checker > span').removeClass('checked');
		}
		if (this.checked) {
			$(this).closest('.checker > span').addClass('checked');
		}
	});
}
*/


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
		$('.plano_contas_buscar').attr('disabled',false);
		$('.centro_resp_buscar').attr('disabled',false);
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

/*
===========================================================================================
Função Ativar Checkbox, Radio e Title estilizados
===========================================================================================
*/

function ativarCROT(ref){
	
	if(ref == "t"){
		$(".tipN").tipsy({gravity: "n",fade: true});
		$(".tipS").tipsy({gravity: "s",fade: true});
		$(".tipW").tipsy({gravity: "w",fade: true});
		$(".tipE").tipsy({gravity: "e",fade: true});
	}else{
		$(".tipN").tipsy({gravity: "n",fade: true});
		$(".tipS").tipsy({gravity: "s",fade: true});
		$(".tipW").tipsy({gravity: "w",fade: true});
		$(".tipE").tipsy({gravity: "e",fade: true});
		//$("input:checkbox, input:radio, input:file").uniform();
		$(".dTableLancamentos input:checkbox").uniform();
	}
}

$(document).ready(function(e) {

/*
========================================================================================================================
BOTÃO DE OPÇÕES
========================================================================================================================
*/

	$('.btnOpt').click(function () {
		$('.subOpt').slideToggle(100);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("btnOpt"))
		$(".subOpt").slideUp(100);
	});

/*
========================================================================================================================
PLUPLOAD
========================================================================================================================
*/

	$("#lnct_uploader").pluploadQueue({
		runtimes : 'html5,html4',
		url : 'modulos/lancamento/php/upload_import.php',
		chunk_size: "1mb",
		//max_file_size : '1mb',
		//multiple_queues: true, //restaura os botões após terminar um upload e limpar a lista
		unique_names : false,
		filters : [
			{title : "Arquivos xls", extensions : "xls"}
		]
	});
	
	var uploader = $("#lnct_uploader").pluploadQueue();
	
	uploader.bind("BeforeUpload", function(up, file) {
		//if($('#input_conta_import').valid()){
			//$("span.aguarde, div.aguarde").css("display","block");
			$('.plupload_upload_status').css('display','inline');
			$( "#dialog-lnct-importar" ).dialog("option", "buttons", {});
			up.settings.multipart_params = {"cliente_id": document.getElementById("cliente_id").value, "usuario_id": document.getElementById("usuario_id").value};
		//}else{
			//return false;
		//}
	});
	
	uploader.bind('FilesAdded', function(up, files) {
		var i = 0;
		while (i++ < up.files.length) {
				var ii = i;
				while (ii < up.files.length) {
						if (up.files[i - 1].name == up.files[ii].name) {
								uploader.removeFile(up.files[ii]);   
						} else {
								ii++;
						}
				}
		}
	});		

	uploader.bind("UploadComplete", function(up, files) {
		$("#dialog-lnct-importar" ).dialog( "close" );
		uploader.splice();
		lancamentosIncluir();
	});

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

	//===== UI dialog - Importar lançamentos =====//

	$( "#dialog-lnct-importar" ).dialog({
		autoOpen: false,
		modal: true,
		closeOnEscape: false,
		open: function(event, ui) {  $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); }
	});
	
	$( "#opener-lnct-importar-ofx" ).click(function() {
		uploadReset('ofx');
	});

	$( "#opener-lnct-importar-xls" ).click(function() {
		uploadReset('xls');
	});

	//===== UI dialog - Incluir recebimento =====//

	$( "#dialog-rcbt-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {			
			Incluír: function() {
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

	//===== UI dialog - Incluir pagamento =====//

	$( "#dialog-pgto-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Incluír: function() {
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
	
	//===== UI dialog - Incluir transferência =====//

	$( "#dialog-trans-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Incluír: function() {
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

	//===== UI dialog - Quitar lançamentos em lote =====//

	$( "#dialog-lnct-lote" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Incluír: function() {
				lnctLoteIncluir();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-lnct-lote" ).click(function() {
		lancamentosLimpar('form_lote');
		alertaIncluirLote();
		return false;
	});
	//===== UI dialog - Teste =====//

/*
	$( "#dialog-teste" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		buttons: {			
			OK: function() {
				$( this ).dialog( "close" );
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		 beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#dialog-teste" ).click(function() {
		//lancamentosLimpar('form_rcbt');
		//$('.lnct_dados').css('display','block');
		//$('.lnct_ctr_plc').css('display','none');
		$("#dialog-teste" ).dialog( "open" ); 
		return false;
	});
*/

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
DATA TABLE
========================================================================================================================
*/

	oTable = $('.dTableLancamentos').dataTable({
		"bInfo": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bPaginate": false,
		"bSort": false,
		//"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"aaSorting": [[ 0, "asc" ]], //inicializa a tabela ordenada pela coluna especificada
		'aoColumnDefs': [
			{ "bVisible": false, "aTargets": [ 0 ] }, //torna uma coluna invisivel
			//{ 'bSortable': false, 'aTargets': [ 0,1,3,4,5 ] }, //define quais colunas não terão ordenação
		],
		"oLanguage": {
			//"sLengthMenu": "<span>Mostrar:</span> _MENU_",
			"sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
		}
	});

	/*
	oTableLancamentos = $('.dTableLancamentos').dataTable();
	oTableLancamentos.fnSetColumnVis( 0, false );
	oTableLancamentos.fnSort( [ [0,'asc'] ] );
	*/

/*
========================================================================================================================
CHECAR TODOS
========================================================================================================================
*/

	$('#ckbDropDownHeader').live('click',function(e) {
		var checkedStatus = this.checked;
		$('#ckbDropDownList div.checker span input:checkbox').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
		var ckbTblHeader = $('.ckbHeaderCell div.checker span input:checkbox');
		if(checkedStatus){
				ckbTblHeader.attr("checked",true);
				ckbTblHeader.closest('.checker > span').addClass('checked');
		}else{
				ckbTblHeader.attr("checked",false);
				ckbTblHeader.closest('.checker > span').removeClass('checked');
		}
		lnctChecarTodos('');
	});
	
	$('.ckbListItem').live('click',function(e) {
		document.getElementById("ckbDropDownHeader").checked = false;
		$('#ckbDropDownHeader').closest('.checker > span').removeClass('checked');
		document.getElementById("ckbTblHeader").checked = false;
		$('#ckbTblHeader').closest('.checker > span').removeClass('checked');
		lnctChecarTodos($(this).val());
	});	

	$('.R, .P').live('click',function(e) {
		document.getElementById("ckbTblHeader").checked = false;
		$('#ckbTblHeader').closest('.checker > span').removeClass('checked');
		document.getElementById("ckbDropDownHeader").checked = false;
		$('#ckbDropDownHeader').closest('.checker > span').removeClass('checked');
	});

});

function lnctChecarTodos(tp_lnct){
	if(tp_lnct===''){
		var checkedStatus = $("#ckbTblHeader").attr("checked");
		if(!checkedStatus){
			checkedStatus = false;
		}
		$('.lnctCheckbox div.checker span input:checkbox').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
				$('.sItem div.checker span input:checkbox').each(function() {
					$(this).attr('checked',false);
					$(this).closest('.checker > span').removeClass('checked');
				});
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
				$('.sItem div.checker span input:checkbox').each(function() {
					$(this).attr('checked',true);
					$(this).closest('.checker > span').addClass('checked');
				});
			}
		});
	}else if(tp_lnct==='R'){
		var checkedStatus = $("#tpLnctCk01").attr("checked");
		if(!checkedStatus){
			checkedStatus = false;
		}
		$('.lnctCheckbox div.checker span input:checkbox.R').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	}else{
		var checkedStatus = $("#tpLnctCk02").attr("checked");
		if(!checkedStatus){
			checkedStatus = false;
		}
		$('.lnctCheckbox div.checker span input:checkbox.P').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	}
}


