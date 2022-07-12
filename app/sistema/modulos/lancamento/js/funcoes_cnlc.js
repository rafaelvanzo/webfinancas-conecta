// JavaScript Document

/*
========================================================================================================================
REQUISICAO AJAX
========================================================================================================================
*/
var dados_global;

function ajax_jquery(params,funcao_retorno){

		/*
		params += "&bd_web_financas="+$('#bd_web_financas').val();
		params += "&id_usuario="+$('#id_usuario').val();
		params += "&id_dependente="+$('#id_dependente').val();
		*/
		
    $.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/lancamento/php/funcoes_cnlc.php', //url para onde será enviada as informações digitadas
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
MENSAGEM DE NOTIFICAÇÃO
===========================================================================================
*/

function notificacao(situacao,mensagem){
	if(situacao==1){
		$('.nSuccess p').html(mensagem);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 4000);
	}else{
		$('.nWarning p').html(mensagem);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 4000);
	}
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

function dTable(table_class){
	var oTable = $('.'+table_class).dataTable({
		"bFilter": true,
		"bInfo": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"bPaginate": false,
		"bSort": false,
		"sDom": 't',
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

function linhaIndice(link_id,tabela_class){
	var tabela = $("."+tabela_class).dataTable();
	var celula = document.getElementById(link_id).parentNode;
	var linha = celula.parentNode;
	var indice = tabela.fnGetPosition(linha);
	return indice;
}

/*
===========================================================================================
INCLUÍR LANÇAMENTOS DO EXTRATO BANCÁRIO IMPORTADO
===========================================================================================
*/

function extratoIncluir(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=extratoIncluir&cliente_id="+document.getElementById("cliente_id").value;
	params += "&usuario_id="+document.getElementById("usuario_id").value;
	params += "&conta_id_import="+document.getElementById("conta_id_import").value;
	params += "&tp_arq="+$("#dados").data("tp_arq");
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
		    if (data.status) {
		        $('#saldo_banco').html('Saldo no Banco até ' + data.dt_saldo_banco + ' <span class="tp_extrato_divider"></span> <strong>R$ ' + data.vl_saldo_banco + '</strong>');
		        document.getElementById("lancamentos").innerHTML = data.lancamentos;
		        dTable('dTableExtratoBanco');
		        ativarCROT('', 'dTableExtratoBanco');/* Reaplicando Chackbox, Radio e Title */
		        if (data.qtd_lnct > 0) {
		            $('#extrato-banco-widget').css('border-bottom', '0px');
		        } else {
		            $('#extrato-banco-widget').css('border-bottom', '1px solid #cdcdcd');
		        }
		        $('.nSuccess p').html(data.notificacao);
		        $('.nSuccess').slideDown();
		        setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
		    } else {
		        $('.nWarning p').html(data.notificacao);
		        $('.nWarning').slideDown();
		        setTimeout(function () { $('.nWarning').slideUp() }, 4000);
		    }

			$("span.aguarde, div.aguarde").css("display","none");
		},
	});
}

/*
===========================================================================================
EXCLUÍR LANÇAMENTOS
===========================================================================================
*/

function alertaExcluir(lnct_cnlc_id,table_class){
	var indice = linhaIndice('link_excluir_'+lnct_cnlc_id,table_class);
		
	$( "#dialog-alerta" ).dialog( "option", "buttons", [
	{
		text: "Sim",
		click: function() { lancamentosExcluir(lnct_cnlc_id,indice,table_class); $("#dialog-alerta").dialog("close");}
	},
	{
		text: "Não",
		click: function() { $("#dialog-alerta").dialog("close"); }
	}		
	]);
	
	$('#dialog-alerta').html("<br/> Deseja realmente excluír o registro selecionado?");
	
	$('#dialog-alerta').dialog('open');
}

function lancamentosExcluir(lnct_cnlc_id,indice,table_class){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=lancamentosExcluir&lnct_cnlc_id="+lnct_cnlc_id;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
			$('.nSuccess p').html(data.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			var tabela = $("."+table_class).dataTable();
			tabela.fnDeleteRow(indice);
			$("span.aguarde, div.aguarde").css("display","none");
		},
	});
}

/*
===========================================================================================
EXCLUÍR LANÇAMENTOS EM LOTE
===========================================================================================
*/

function alertaExcluirLote(table_class,ckb_class){
	var array_lncts_id = new Array();
	var lncts_id = "";
	$('.'+ckb_class+' div.checker span.checked input[type="checkbox"]').each(function() {
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
			click: function() { lnctExcluirLote(lncts_id,table_class); $("#dialog-alerta").dialog("close");}
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

function lnctExcluirLote(lncts_id,table_class){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=lnctExcluirLote";
	params += "&lncts_id="+lncts_id;
	//var params_busca = $('#form_busca').serialize();
	//var params = params_rcbt_editar+'&'+params_busca;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
			var indice;
			var array_lncts_id;
			var array_len;
			var tabela = $('.'+table_class).dataTable();
			array_lncts_id = lncts_id.split(",");
			array_len = array_lncts_id.length;
			for(i=0;i<array_len;i++){
				indice = linhaIndice('link_excluir_'+array_lncts_id[i]);
				tabela.fnDeleteRow(indice);
			}
			$('.nSuccess p').html(data.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			$("span.aguarde, div.aguarde").css("display","none");
		},
	});
}

/*
===========================================================================================
INCLUÍR BOLETOS DO ARQUIVO DE RETORNO IMPORTADO
===========================================================================================
*/

function boletosIncluir(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=boletosIncluir&cliente_id="+document.getElementById("cliente_id").value;
	params += "&usuario_id="+document.getElementById("usuario_id").value;
	params += "&conta_id_import="+document.getElementById("conta_id_import").value;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function (data) {
		    if (data.status) {
		        document.getElementById("extrato-arq-retorno").innerHTML = data.boletos;
		        dTable('dTableBoletos');
		        ativarCROT('', 'dTableBoletos');
		        if (data.qtd_blt > 0) {
		            $('#arq-retorno-widget').css('border-bottom', '0px');
		        } else {
		            $('#arq-retorno-widget').css('border-bottom', '1px solid #cdcdcd');
		        }
		        $('.nSuccess p').html(data.notificacao);
		        $('.nSuccess').slideDown();
		        setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
		    } else {
		        $('.nWarning p').html(data.notificacao);
		        $('.nWarning').slideDown();
		        setTimeout(function () { $('.nWarning').slideUp() }, 3000);
		    }
		    $("span.aguarde, div.aguarde").css("display", "none");
		},
	})
}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function lancamentosLimpar(form){
	var validator = $('#'+form).validate();
	validator.resetForm();
	$("#"+form+" div.boxScroll").remove();

	//limpa centro de custo e categoria
	$("#"+form+"_ctr_plc_lnct").val("");
	$("#"+form+"_pl_conta_id").val(0);
	$("#"+form+"_ct_resp_id").val(0);

	$("#"+form+" span.check-green").css('display','none');
	//$('#parcelas').attr('class','span3');
	//$('#dias').css('display','none');
	$("#"+form+" .input-buscar").attr('disabled',false);

	//resetar abas
	$('#'+form+' div.MaisOpcoes').attr('class','title closed MaisOpcoes normal');
	$('#'+form+' div.body:eq(0)').css('display','none');
	
	//reseta checkbox switch e data de compensaçao
	$('#'+form+'_compensado').bootstrapSwitch('state', true, true);
	$('#'+form+'_dt_compensacao').attr('disabled',false);
	
	//limpar arquivos anexados
	$("#"+form+"_filelist").html('');
	array_uploader[0].splice();
	array_uploader[1].splice();
	array_uploader[2].splice();
	$('#dados').data(form+'_file-upload-queue',0); //essa linha deve vir obrigatoriamente após o splice poque ele também dispara os evento FilesRemoved e QueueChanged
}

/*
===========================================================================================
CONFIGURAR DIALOG PARA INCLUSÃO OU EDIÇÃO
===========================================================================================
*/

function dialogConfig(dialog_id,form_id,opr,is_rcr,tp_lnct,compensado){

	//Define as funções de acordo com o tipo de lançamento
	if(is_rcr){
		var form_func_imprimir_boleto = 'boletosImprimirRcr';
		var form_func_edit_compensar = 'lancamentoRcrCompensar';
		var form_func_edit_salvar = 'lancamentoRcrEditar';
	}else{
		var form_func_imprimir_boleto = 'boletosImprimir';
		var form_func_edit_compensar = 'lancamentoCompensar';
		var form_func_edit_salvar = 'lancamentoEditar';
	}
	//Define title das janelas de acordo com o tipo de lançamento
	if(tp_lnct=='R'){
		var dialog_title_add = 'Nova Conta À Receber';
		if(compensado){
			var dialog_title_edit = 'Editar Conta Recebida';
		}else{
			var dialog_title_edit = 'Editar Conta À Receber';
		}
		$('#dados').data('form-ordem-ativo',0); //usado pelo plupload
	}else if(tp_lnct=='P'){
		var dialog_title_add = 'Nova Conta À Pagar';
		if(compensado){
			var dialog_title_edit = 'Editar Conta Paga';
		}else{
			var dialog_title_edit = 'Editar Conta À Pagar';
		}
		$('#dados').data('form-ordem-ativo',1); //usado pelo plupload
	}else{
		var dialog_title_add = 'Nova Transferência Entre Contas';
		var dialog_title_edit = 'Editar Transferência';
		$('#dados').data('form-ordem-ativo',2); //usado pelo plupload
	}

	//usado pelo plupload
	$('#dados').data('form-id-ativo',form_id);

	if(opr=='add'){ //Configura formulário para incluir lançamento

		$( '#'+dialog_id ).dialog( "option", "title", dialog_title_add );
		
		$("#"+form_id+"_lancamento_id").val("0");

		//Altera botôes da janela
		$('#'+dialog_id).dialog( "option", "buttons", [

			{
			    text: 'Salvar', click: function () {

			        if (AutoIncluirCategoriaECentroCusto(form_id)) {

			            $(".lnctValid-" + tp_lnct).trigger("click"); //Muda a ABA da janela

			            if ($('#' + form_id).valid()) {

			                $("#" + form_id + "_funcao").val('lancamentoIncluir');

			                $("#" + form_id + "_compensado").val(1);

			                lancamentoIncluir(form_id, dialog_id, tp_lnct, true);
			            }
			        }
				}
			},

			{ text: "Cancelar",click: function() { $('#'+dialog_id).dialog("close"); } },

		]);
		
	}else if(opr=='edit'){ //Configura formulário para editar lançamento

		//Altera title da janela
		$( '#'+dialog_id ).dialog( "option", "title", dialog_title_edit );

		//Altera botôes da janela
		var array_btn = [];

		array_btn.push(
			//Botão Salvar - função lancamentoEditar()
			{
			    text: "Salvar", click: function () {

			        if (AutoIncluirCategoriaECentroCusto(form_id)) {

			            $(".lnctValid-" + tp_lnct).trigger("click"); //Muda a ABA da janela

			            if ($('#' + form_id).valid()) {

			                var compensar = $('#' + form_id + '_compensado').attr('checked');

			                $('#' + form_id + '_funcao').val(form_func_edit_salvar);

			                $('#' + dialog_id).dialog("close");

			                lancamentoEditar(form_id, dialog_id, tp_lnct, is_rcr);
			            }
			        }
				}
			}
		);

		array_btn.push(
			//Botão Cancelar
			{ text: "Cancelar",click: function() { $('#'+dialog_id).dialog("close"); } }
		);
		
		$( '#'+dialog_id ).dialog( "option", "buttons", array_btn );

	}else if(opr=='boleto'){ //Configura formulário para gerar boleto
		
		$('#'+form_id+'_funcao').val(form_func_imprimir_boleto);
		
	}else if(opr=='qtr'){ //Configura formulário para compensar lançamento
		
		$('#'+form_id+'_funcao').val(form_func_edit_compensar);

		$('#'+form_id+'_compensado').bootstrapSwitch('state', true, true);

	}

}

/*
===========================================================================================
NOVO LANÇAMENTO
===========================================================================================
*/

function novoLancamento(tp_lnct,lnct_id,fit_id){
    $("#dados").data('lnct_cnlc_id', lnct_id);
    $("#dados").data('fit_id', fit_id);
	var data = document.getElementById("data_"+lnct_id).innerHTML;
	var dscr = document.getElementById("dscr_"+lnct_id).innerHTML;
	var valor = document.getElementById("vl_"+lnct_id).innerHTML;
	var cf_id = document.getElementById("cf_id_"+lnct_id).innerHTML;
	var cf_dscr = document.getElementById("cf_dscr_"+lnct_id).innerHTML;

	//envar estes argumentos como parâmetro para a função novoLancamento
	if(tp_lnct=="R"){
		var form_id = 'form_rcbt';
		var dialog_id = 'dialog-rcbt';
	}else if(tp_lnct=="P"){
		var form_id = 'form_pgto';
		var dialog_id = 'dialog-pgto';
	}else{
		var form_id = 'form_trsf';
		var dialog_id = 'dialog-trsf';
	}

	lancamentosLimpar(form_id);
	dialogConfig(dialog_id,form_id,'add',0,tp_lnct,1);

	if(tp_lnct=='R' || tp_lnct=='P'){

		document.getElementById(form_id+"_conta").value = cf_dscr;
		document.getElementById(form_id+"_conta").disabled = true;
		document.getElementById(form_id+"_conta_id").value = cf_id;
		$('#'+form_id+' span.check-green').eq(1).css('display','block');
		
	}else{
		
		if(tp_lnct=="TR"){

			document.getElementById(form_id+"_conta_destino").value = cf_dscr;
			document.getElementById(form_id+"_conta_destino").disabled = true;
			document.getElementById(form_id+"_conta_id_destino").value = cf_id;
			$('#'+form_id+' span.check-green').eq(1).css('display','block');

		}else{

			document.getElementById(form_id+"_conta_origem").value = cf_dscr;
			document.getElementById(form_id+"_conta_origem").disabled = true;
			document.getElementById(form_id+"_conta_id_origem").value = cf_id;
			$('#'+form_id+' span.check-green').eq(0).css('display','block');

		}

	}

	document.getElementById(form_id+"_dscr").value = dscr;
	document.getElementById(form_id+"_dt_emissao").value = data;
	document.getElementById(form_id+"_dt_vencimento").value = data;
	document.getElementById(form_id+"_dt_compensacao").value = data;
	document.getElementById(form_id+"_valor").value = valor;

	$("#"+dialog_id).dialog( "open" );

}

/*
===========================================================================================
INCLUÍR LANCAMENTO
===========================================================================================
*/
 
function lancamentoIncluir(form_id,dialog_id,tp_lnct,compensado){
	
	$("span.aguarde, div.aguarde").css("display","block");
	//if(tp_lnct=='R'||tp_lnct=='P')
		//centroRespLnctIncluir(form_id);
	var lnct_cnlc_id = $("#dados").data('lnct_cnlc_id');
	var fit_id = $("#dados").data('fit_id');
	var params = $('#'+form_id).serialize();
	params += '&lnct_cnlc_id=' + lnct_cnlc_id;
	params += '&fit_id=' + fit_id;
	$('#'+dialog_id).dialog( "close" );
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		dataType: 'json',
		cache: false,
		success: function(data){
			var dados = data;
			if(dados.situacao==1){
				//$('#lancamentos').html(dados.lancamentos);
				//dTable();
				if(compensado){
					//$('#contasSaldo').html(dados.contas_saldo);
					$('.saldoTotal').html(dados.saldo_total);
				}

				//inicia upload do anexo se houver
				var file_queue = $('#dados').data(form_id+'_file-upload-queue');
				if(file_queue>0){
					$("#dados").data("notificacao-sucesso",dados.notificacao);
					$("#dados").data("lancamento_id",dados.lancamento_id);
					if(tp_lnct=='R')
						array_uploader[0].start();
					else if(tp_lnct=='P')
						array_uploader[1].start();
					else
						array_uploader[2].start();
				}else{
					notificacao(1,dados.notificacao);
				}

				var tp_extrato = $("input:radio[name='tp_extrato']:checked").val();
				if(tp_extrato=='1')
					var tabela_class = "dTableExtratoBanco";
				else
					var tabela_class = "dTableBoletos";
				var tabela = $("."+tabela_class).dataTable();
				var indice = tabela.fnGetPosition( document.getElementById('tbl-lnct-row-'+lnct_cnlc_id) );
				tabela.fnDeleteRow(indice);

			}else{
				notificacao(2,dados.notificacao);
			}
			$("span.aguarde, div.aguarde").css("display","none");
		},
	})
}

/*
===========================================================================================
MAPEAR LANÇAMENTOS EXISTENTES SELECIONADOS E NA BUSCA POR PERÍODO
===========================================================================================
*/

function lnctExistMapear(input_type){
	var lncts_id = '';

	$('#boxLnctSugerido input[type="'+input_type+'"]').each(function(i,e){
		lncts_id += $(this).val()+',';
	});
	$('#boxLnctSelected input[type="'+input_type+'"]').each(function(i,e){
		lncts_id += $(this).val()+',';
	});
	
	$("#dados").data('lncts_exist_id',lncts_id);
}

/*
===========================================================================================================
BUSCAR LANÇAMENTOS EXISTENTES NO WEB FINANÇAS PARA CONCILIAR COM O EXTRATO BACÁRIO E ARQUIVO DE RETORNO
===========================================================================================================
*/

function lnctExistBuscar(){
	var dt_ini = $('#dt_ini').val();
	var dt_fim = $('#dt_fim').val();
	var _dt_ini = dt_ini.split('/');
	var _dt_fim = dt_fim.split('/');
	var _dt_ini = new Date(_dt_ini[2],_dt_ini[1],_dt_ini[0]);
	var _dt_fim = new Date(_dt_fim[2],_dt_fim[1],_dt_fim[0]);
	var qtd_dias = Math.ceil( (_dt_fim - _dt_ini) / 86400000 );
	var input_type =  $('#dados').data("input_type_cnlc");
	var	lnct_cnlc_id = $("#dados").data("lnct_cnlc_id");
	if(qtd_dias>=0){
		$("span.aguarde, div.aguarde").css("display","block");
		var tp_lnct = $("#dados").data("tp_lnct_exist");
		var cf_id = $("#dados").data("cf_id_exist");
		var lncts_exist_id = $("#dados").data('lncts_exist_id');
		
		var params = "funcao=lnctExistBuscar"+'&dt_ini='+dt_ini+'&dt_fim='+dt_fim+'&tp_lnct='+tp_lnct+'&cf_id='+cf_id+'&lncts_exist_id='+lncts_exist_id+'&input_type='+input_type+'&lnct_cnlc_id='+lnct_cnlc_id;
		$.ajax({
			type: 'post',
			url: 'modulos/lancamento/php/funcoes_cnlc.php',
			data: params,
			cache: true,
			success: function(data){
				if(data!=""){
					$('#boxLnctSugerido').html(data);
					lnctExistMapear(input_type);
				}else{
					var qtd_lncts_sugest = $('#boxLnctSugerido ul').html();
					if(qtd_lncts_sugest==""){
						$('#boxLnctSugerido').html('<div style="padding:10px">Nenhum registro encontrado</div>');
					}
				}
				$("span.aguarde, div.aguarde").css("display","none");
			},
		})
	}else{
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Fechar",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);
		$('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left;'/> &nbsp; A data final deve ser maior ou igual à data inicial.");
		$('#dialog-alerta').dialog('open');
	}
}

/*
===============================================================================================================
EXIBIR LANÇAMENTO DO EXTRATO BANCÁRIO OU ARQUIVO DE RETORNO PARA BUSCA DE LANÇAMENTO EXISTENTE NO WEB FINANÇAS
===============================================================================================================
*/

function lnctExist(cf_id,lnct_cnlc_id,tp_lnct,input_type){
	$("span.aguarde, div.aguarde").css("display","block");

	$("#dados").data("input_type_cnlc",input_type);
	$("#dados").data("tp_lnct_exist",tp_lnct);
	$("#dados").data("cf_id_exist",cf_id);
	$("#dados").data("vl_cnlc",document.getElementById("vl_"+lnct_cnlc_id).innerHTML);
	$("#dados").data("dt_vencimento_cnlc",document.getElementById("data_"+lnct_cnlc_id).innerHTML);

	var params = "funcao=lnctSugest";
	params += "&dt_vencimento="+document.getElementById("data_"+lnct_cnlc_id).innerHTML;
	params += "&valor="+document.getElementById("vl_"+lnct_cnlc_id).innerHTML;
	params += "&cf_id="+cf_id;
	params += "&tp_lnct="+tp_lnct;
	params += "&lnct_cnlc_id="+lnct_cnlc_id;
	params += "&input_type="+input_type;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(dados){
			//lista os lançamentos existentes no Web Finanças sugeridos dentro da janela de busca
			document.getElementById("boxLnctSugerido").innerHTML = dados;
		
			//coloca descrição, vencimento e valor do lançamento de conciliação no topo da janela de busca
			var data = document.getElementById("data_"+lnct_cnlc_id).innerHTML;
			var dscr = document.getElementById("dscr_"+lnct_cnlc_id).innerHTML;
			var valor = document.getElementById("vl_"+lnct_cnlc_id).innerHTML;
			document.getElementById("dscr_cnlc").innerHTML = '<u>'+dscr+'</u>';
			document.getElementById("dt_vencimento_cnlc").innerHTML = data;
			document.getElementById("vl_cnlc").innerHTML = valor;
			
			//armazena qual é a id do lançamento de conciliação exibido na janela de busca
			$("#dados").data("lnct_cnlc_id",lnct_cnlc_id);
			
			lnctBuscaLimpar();
			lnctExistMapear(input_type);
			$("span.aguarde, div.aguarde").css("display","none");
			$("#dialog-lnct-buscar" ).dialog( "open" );
		},
	})
}

/*
===========================================================================================
SELECIONAR LANÇAMENTOS EXISTENTES E MOVER PARA O BOX DE LANÇAMENTOS SELECIONADOS
===========================================================================================
*/

function lnctExistAdd(origem_request,dados_lnct){

	var lnct_id;
	var input_type = $('#dados').data("input_type_cnlc");
	var lnct_cnlc_id = $('#dados').data("lnct_cnlc_id");
	
	if(origem_request=='busca'){
		if(input_type=='checkbox'){
			$('#boxLnctSugerido input[type="checkbox"]:checked').each(function(i,e){
				lnct_id = $(this).val();
				$('#li_lnct_sugest_'+lnct_id).appendTo('#ul_lnct_selected');
				$('#link_sugest_edit_'+lnct_id).css('display','block');
			})
		}else{
			lnct_id = $('#boxLnctSugerido input[type="radio"]:checked').val();
			$('#li_lnct_sugest_'+lnct_id).appendTo('#ul_lnct_selected');
			$('#link_sugest_edit_'+lnct_id).css('display','block');
		}
	}else{ //autocomplete
	    var lnct_sugest = '<li id="li_lnct_sugest_' + dados_lnct.id + '"><div class="floatL"><input type="' + input_type + '" name="lnct_sugest_id" class="lnctSugest" data-is_rcr="' + dados_lnct.is_rcr + '" data-tp_lnct="' + dados_lnct.tipo + '" data-conta_id_origem="' + dados_lnct.conta_id_origem + '" id="ckb_' + dados_lnct.id + '" value="' + dados_lnct.id + '" checked/> <span id="lnct_exist_dscr_' + dados_lnct.id + '"> ' + dados_lnct.dt_vencimento + ' - ' + dados_lnct.dscr + ' </span> <div style="text-align:left;">' + dados_lnct.nome + '</div> </div> <div class="floatR"> R$ <span id="vl_sugest_' + dados_lnct.id + '">' + dados_lnct.valor + '</span> <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf tipS" id="link_sugest_edit_' + dados_lnct.id + '" onclick="lancamentosExibir(\'' + dados_lnct.tipo + '\',' + lnct_cnlc_id + ',' + dados_lnct.id + ',' + dados_lnct.is_rcr + ',\'\')" style="width:10px;margin-top:0px;"><img src="images/icons/dark/pencil.png" width="10"></a></div></li>';
		$('#ul_lnct_selected').append(lnct_sugest);
	}
 
	vlTotalLnctExist(input_type); 
	lnctExistMapear(input_type);
	
}

/*
===========================================================================================
CALCULAR VALOR TODAL SELECIONADO DE LANÇAMENTOS EXISTENTES
===========================================================================================
*/

function vlTotalLnctExist(input_type){
	
	var lnct_id;
	var vl_total = 0;
	var qtd_lnct = 0;
	var vl_dif = 0;
	var tp_lnct_cnlc = $("#dados").data("tp_lnct_exist");
	var vl_cnlc = $('#vl_cnlc').html();
	vl_cnlc = txtToValor(vl_cnlc);
	
	$('#boxLnctSelected input[type="'+input_type+'"]:checked').each(function(i,e){
		lnct_id = $(this).val();
		vl_lnct_exist = $('#vl_sugest_'+lnct_id).html();
		vl_lnct_exist = txtToValor(vl_lnct_exist);
		vl_total += vl_lnct_exist;
		qtd_lnct++;
	})
	if(qtd_lnct>0){
		vl_dif = vl_cnlc - vl_total;
		if(tp_lnct_cnlc=='R'){
			if(vl_dif>0){
				$('#vl_dif').parent().css('color','green');
			}else if(vl_dif<0){
				$('#vl_dif').parent().css('color','red');
			}else{
				$('#vl_dif').parent().css('color','');
			}
		}else{
			if(vl_dif>0){
				$('#vl_dif').parent().css('color','red');
			}else if(vl_dif<0){
				$('#vl_dif').parent().css('color','green');
			}else{
				$('#vl_dif').parent().css('color','');
			}		
		}
	}else{
		$('#vl_dif').parent().css('color','');
	}
	vl_total = number_format(vl_total,2,',','.');
	vl_dif = number_format(vl_dif,2,',','.');
	$('#qtd_lnct_selected').html(qtd_lnct);
	$('#vl_total_selected').html(vl_total);
	$('#vl_dif').html(vl_dif);

}

/*
===========================================================================================
VALIDAR SELEÇÃO LANÇAMENTO EXISTENTE
===========================================================================================
*/

function lnctExistValidar(opcao_qtr){
	var i;
	var array_lnct_sugest_id = [];
	var lncts_exist_id = "";
	var is_rcr = 0;
	var id,tp_lnct,conta_id_origem,valor;
	var vl_cnlc = txtToValor($('#vl_cnlc').html());
	var vl_total_selected = 0;
	var input_type =  $('#dados').data("input_type_cnlc");

	$('#boxLnctSelected input[type="'+input_type+'"]:checked').each(function(i,e){
		is_rcr = $(this).data('is_rcr');
		id = $(this).val();
		tp_lnct = $(this).data('tp_lnct');
		conta_id_origem = $(this).data('conta_id_origem');
		valor = $('#vl_sugest_'+id).html();
		array_lnct_sugest_id.push({'id':id,'is_rcr':is_rcr,'tipo':tp_lnct,'conta_id_origem':conta_id_origem,'valor':valor});
		vl_total_selected += txtToValor(valor);
	});

	if(array_lnct_sugest_id.length>0){
		lncts_exist_id = JSON.stringify(array_lnct_sugest_id);
	}

	if(lncts_exist_id==""){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Ok",
			click: function() { $("#dialog-alerta").dialog("close");}
		}
		]);
		$('#dialog-alerta').html("<br/> Selecione um lançamento para conciliar.");
		$('#dialog-alerta').dialog('open');
	}else if(vl_cnlc !== vl_total_selected){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Ok",
			click: function() { $("#dialog-alerta").dialog("close");}
		}
		]);
		$('#dialog-alerta').html("<br/> O valor total selecionado é diferente do valor de conciliação.");
		$('#dialog-alerta').dialog('open');
	}else{
	    conciliarLancamento(lncts_exist_id, input_type);
		$( "#dialog-lnct-buscar" ).dialog( "close" );
	}
}

/*
===========================================================================================
QUITAR LANÇAMENTO EXISTENTE
===========================================================================================
*/

function conciliarLancamento(lncts_exist_id, input_type) {
	$("span.aguarde, div.aguarde").css("display","block");
	var cf_id_exist = $("#dados").data("cf_id_exist");
	var lnct_cnlc_id = $("#dados").data("lnct_cnlc_id");
	var tp_lnct_exist = $("#dados").data("tp_lnct_exist");
	var vl_cnlc = $("#dados").data("vl_cnlc");
	var dt_vencimento_cnlc = $("#dados").data("dt_vencimento_cnlc");
	var fit_id = $("#dados").data("fit_id");
	var tabela, indice;
	var params = "funcao=conciliarLancamento";
	params += "&lnct_cnlc_id="+lnct_cnlc_id;
	params += "&lncts_exist_id="+lncts_exist_id;
	params += '&tipo='+tp_lnct_exist;
	params += '&vl_cnlc='+vl_cnlc
	params += '&conta_id='+cf_id_exist;
	params += '&dt_vencimento_cnlc=' + dt_vencimento_cnlc;
	params += '&fit_id=' + fit_id;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
			if(data.situacao==1){
				if(input_type=='checkbox'){
					tabela = $(".dTableExtratoBanco").dataTable();
				}else{
					tabela = $(".dTableBoletos").dataTable();
				}
				indice = linhaIndice('link_excluir_'+lnct_cnlc_id,'dTableBoletos');
				tabela.fnDeleteRow(indice);
				$('.saldoTotal').html('R$ '+data.saldo_total);
				$('.nSuccess p').html(data.notificacao);
				$('.nSuccess').slideDown();
				setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			}else{
				$('.nWarning p').html(data.notificacao);
				$('.nWarning').slideDown();
				setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
			}
			$("span.aguarde, div.aguarde").css("display","none");	
		}
	});
}

/*
===========================================================================================
QUITAR LANÇAMENTO EXISTENTE DIRETAMENTE DO EXTRATO BANCÁRIO OU ARQUIVO DE RETORNO
===========================================================================================
*/

function lnctSugestQtr(lnct_id,lnct_cnlc_id,cf_id,tp_lnct,valor,data,input_type,is_rcr,conta_id_origem,fit_id){
	$("span.aguarde, div.aguarde").css("display","block");
	$("#dados").data("cf_id_exist",cf_id);
	$("#dados").data("lnct_cnlc_id",lnct_cnlc_id);
	$("#dados").data("tp_lnct_exist",tp_lnct);
	$("#dados").data("vl_cnlc",valor);
	$("#dados").data("dt_vencimento_cnlc", data);
	$("#dados").data("fit_id", fit_id);
	lncts_exist_id = JSON.stringify([{'id':lnct_id,'is_rcr':is_rcr,'tipo':tp_lnct,'conta_id_origem':conta_id_origem,'valor':valor}]);
	conciliarLancamento(lncts_exist_id, input_type);
}

/*
===========================================================================================
EXIBIR LANÇAMENTO EXISTENTE NO WEB FINANÇAS PARA EDIÇÃO
===========================================================================================
*/
 
function lancamentosExibir(tp_lnct,lnct_cnlc_id,lancamento_id,is_rcr,lnct_edit_orig_req){ 

    $("span.aguarde, div.aguarde").css("display", "block");
    $('#dados').data('lnct_edit_orig_req', lnct_edit_orig_req);

    var params = "funcao=lancamentosExibir";
	params += "&lancamento_id="+lancamento_id;
	params += "&rcr="+is_rcr;

	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(dados){

			if(tp_lnct=="R"){
				var form_id = 'form_rcbt';
				var dialog_id = 'dialog-rcbt';
			}else if(tp_lnct=="P"){
				var form_id = 'form_pgto';
				var dialog_id = 'dialog-pgto';
			}else{
				var form_id = 'form_trsf';
				var dialog_id = 'dialog-trsf';
			}

			lancamentosLimpar(form_id);
			dialogConfig(dialog_id,form_id,'edit',is_rcr,tp_lnct,0);

			if(dados.lancamento.dt_compensacao=='00/00/0000'){
				var dt_compensacao = $('#data_'+lnct_cnlc_id).html();
			}else{
				var dt_compensacao = dados.lancamento.dt_compensacao;
			}
			
			if(tp_lnct=='T'){

				$("#"+form_id+"_conta_id_origem").val(dados.lancamento.conta_id_origem);
				$("#"+form_id+"_conta_id_destino").val(dados.lancamento.conta_id_destino);
				$("#"+form_id+"_conta_id_origem_ini").val(dados.lancamento.conta_id_origem);
				$("#"+form_id+"_conta_id_destino_ini").val(dados.lancamento.conta_id_destino);
				$("#"+form_id+"_conta_origem").val(dados.lancamento.conta_origem);
				$("#"+form_id+"_conta_destino").val(dados.lancamento.conta_destino);

			}else{
				
				$("#"+form_id+"_favorecido").val(dados.lancamento.favorecido);
				$("#"+form_id+"_favorecido_id").val(dados.lancamento.favorecido_id);
				$("#"+form_id+"_conta").val(dados.lancamento.conta);
				$("#"+form_id+"_conta_id").val(dados.lancamento.conta_id);
				$("#"+form_id+"_conta_id_ini").val(dados.lancamento.conta_id);
				$("#"+form_id+"_sab_dom").val(dados.lancamento.sab_dom);
				$("#"+form_id+"_documento_id").val(dados.lancamento.documento_id);
				$("#"+form_id+"_forma_pgto_id").val(dados.lancamento.forma_pgto_id);
				ctrPlcLancamentosExibir(form_id,dados.ctr_plc_lancamentos,2,3);
				$('#'+form_id+' .favorecido_buscar').attr('disabled',true);
				
				//se for lançamento recorrente atribui mais alguns campos no formulário
				if(is_rcr){
					$('#'+form_id+'_lnct_rcr_id').val(dados.lancamento.id);
					$('#'+form_id+'_dia_mes').val(dados.lancamento.dia_mes);
					$('#'+form_id+'_dt_venc_ref').val(dados.lancamento.dt_venc_ref);
					$('#'+form_id+'_qtd_dias').val(dados.lancamento.qtd_dias);
					$('#'+form_id+'_frequencia').val(dados.lancamento.frequencia);
				}
				
			}

			$("#"+form_id+"_dscr").val(dados.lancamento.descricao);
			$("#"+form_id+"_lancamento_id").val(dados.lancamento.id);
			$("#"+form_id+"_valor").val(dados.lancamento.valor);
			$("#"+form_id+"_valor_ini").val(dados.lancamento.valor);
			$("#"+form_id+"_dt_competencia").val(dados.lancamento.dt_competencia);
			$("#"+form_id+"_dt_emissao").val(dados.lancamento.dt_emissao);
			$("#"+form_id+"_dt_vencimento").val(dados.lancamento.dt_vencimento);
            $("#"+form_id+"_dt_compensacao").val(dt_compensacao);
			$("#"+form_id+"_auto_lancamento").val(dados.lancamento.auto_lancamento);
			$("#"+form_id+"_obs").val(dados.lancamento.observacao);
			$('#'+form_id+' .conta_buscar').attr('disabled',true);
			$('#'+form_id+' span.check-green').eq(0).css('display','block');
			$('#'+form_id+' span.check-green').eq(1).css('display','block');

			$('#'+dialog_id).dialog("open");

			$("span.aguarde, div.aguarde").css("display","none");

		}

	});
}

/*
===========================================================================================
EDITAR LANÇAMENTO EXISTENTE
===========================================================================================
*/

function lancamentoEditar(form_id,dialog_id,tp_lnct_exist,is_rcr){
	//if($('#'+form_id).valid()){
		$("#dialog-rcbt").dialog( "close" );
		$("span.aguarde, div.aguarde").css("display","block");

		if(tp_lnct_exist=='R'||tp_lnct_exist=='P'){
			//centroRespLnctIncluir(form_id);
			var favorecido = $('#'+form_id+'_favorecido').val();
		}

		//pega valor dos campos na janela de inclusão/edição de lançamento para atualizar os dados do lançemento na lista de lançamentos selecionados para conciliação na janela de busca de lançamento existente
		var lnct_edit_orig_req = $('#dados').data('lnct_edit_orig_req');
		var lnct_id = $('#'+form_id+'_lancamento_id').val();
		var lnct_cnlc_id = $('#dados').data("lnct_cnlc_id");
		var vl_lnct = $('#'+form_id+'_valor').val();
		var dt_vencimento = $('#'+form_id+'_dt_vencimento').val();
		var dscr = dt_vencimento+' - '+$('#'+form_id+'_dscr').val();
		var input_type = $('#dados').data("input_type_cnlc");
		var params = $('#'+form_id).serialize();

		$.ajax({
			type: 'post',
			url: 'modulos/lancamento/php/funcoes_cnlc.php',
			data: params,
			cache: true,
			success: function(dados){
				var _dados = JSON.parse(dados);
				if(_dados.situacao==1){
					if(lnct_edit_orig_req!=''){ //editar lançamento sugerido ao clicá-lo na tabela com lista de lançamentos do extrato
						dscr = 'Sugestão: '+dscr+' - '+favorecido+' - R$ '+vl_lnct;
						$('#'+lnct_edit_orig_req+'lnct_exist_dscr_'+lnct_id).html(dscr);
					}else{ //editar lançamento sugerido na janela de busca de lançamento existente
						$('#vl_sugest_'+lnct_id).html(vl_lnct);
						$('#lnct_exist_dscr_'+lnct_id).html(dscr); 
						vlTotalLnctExist(input_type);
						if(is_rcr==1){
							$('#ckb_'+lnct_id).data('is_rcr',0);
							$('#ckb_'+lnct_id).val(_dados.lnct_prog_id);
							$('#li_lnct_sugest_'+lnct_id).attr('id','li_lnct_sugest_'+_dados.lnct_prog_id);
							$('#vl_sugest_'+lnct_id).attr('id','vl_sugest_'+_dados.lnct_prog_id);
							$('#link_sugest_edit_'+lnct_id).attr('onclick','lancamentosExibir(\''+tp_lnct_exist+'\','+lnct_cnlc_id+','+_dados.lnct_prog_id+','+is_rcr+',\''+lnct_edit_orig_req+'\')');
							$('#link_sugest_edit_'+lnct_id).attr('id','link_sugest_edit_'+_dados.lnct_prog_id);
							$('#ckb_'+lnct_id).attr('id','ckb_'+_dados.lnct_prog_id);
						}
						lnctExistMapear(input_type);
					}
					$('.nSuccess p').html(_dados.notificacao);
					$('.nSuccess').slideDown();
					setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
				}else{
					$('.nWarning p').html(_dados.notificacao);
					$('.nWarning').slideDown();
					setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
				}
				$("span.aguarde, div.aguarde").css("display","none");
			},
		});
	//}
}

/*
===========================================================================================
LIMPAR FORMULÁRIO DE BUSCA PARA LANÇAMENTOS EXISTENTES
===========================================================================================
*/

function lnctBuscaLimpar(){
	$('#boxLnctSelected').html('<ul class="partners" id="ul_lnct_selected"></ul>');
	document.getElementById("input_lnct_buscar").value = "";
	document.getElementById("qtd_lnct_selected").innerHTML = "0";
	document.getElementById("vl_total_selected").innerHTML = "0,00";
	$('#vl_dif').html('0,00').parent().css('color','black');
	$('#form_lnct_buscar span.check-green').css('display','none');
	$('#input_lnct_buscar').attr('disabled',false);
}

/*
===========================================================================================
RESETAR UPLOAD DE EXTRATO
===========================================================================================
*/

function uploadExtratoReset(tp_extrato){
	$("#lnct_filelist").html("");
	if(tp_extrato=='extrato')
		document.getElementById("dialog-extrato-importar").previousSibling.firstChild.innerHTML = 'Importar Extrato Bancário';
	else
		document.getElementById("dialog-extrato-importar").previousSibling.firstChild.innerHTML = 'Importar Arquivo de Retorno';
	$("#dialog-extrato-importar").dialog( "open" );
}

/*
===========================================================================================
RESETAR UPLOAD DE ARQUIVO DE RETORNO
===========================================================================================
*/

function uploadArqRetReset(){
	var uploader = $("#arq-ret-uploader").pluploadQueue();
	//lancamentosLimpar('form-arq-ret-import');
	//document.getElementById("arq-ret-uploader").style.display = 'none';
	$('.plupload_total_status').css('display','inline');
	$('.plupload_total_file_size').css('display','inline');
	$("#dialog-arq-ret-import").dialog("option", "buttons", { "Fechar": function() {$( this ).dialog( "close" );} });
	$(".plupload_buttons").css("display", "inline");
	$('.plupload_upload_status').css('display','none');
	$('#arq-ret-uploader > div.plupload input').css('z-index','99999');
	//$("#input_conta_arq_ret_import").val("");
	//$('span.check-green').css('display','none');
	//$('.input-buscar').attr('disabled',false);
	$("#dialog-arq-ret-import").dialog( "open" );
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
	dTable('dTableExtratoBanco');
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
	dTable('dTableExtratoBanco');
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
	dTable('dTableExtratoBanco');
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
INCLUÍR LANÇAMENTO NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function centroRespLnctIncluir(form){
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
/*
function ctrPlcLancamentosExibir(form,ctr_plc_lancamentos){
	var lancamentos = JSON.parse(ctr_plc_lancamentos);
	if(lancamentos.length>0){
		$('#'+form+'_pl_conta_buscar').val(lancamentos[0].conta);
		$('#'+form+'_ct_resp_buscar').val(lancamentos[0].centro);
		$('#'+form+'_pl_conta_id').val(lancamentos[0].plano_contas_id);
		$('#'+form+'_ct_resp_id').val(lancamentos[0].centro_resp_id);
		if(lancamentos[0].plano_contas_id>0){
			$("#"+form+' span.check-green').eq(2).css('display','block');
			$("#"+form+' .plano_contas_buscar').attr('disabled',true);
		}
		if(lancamentos[0].centro_resp_id>0){
			$("#"+form+' span.check-green').eq(3).css('display','block');
			$("#"+form+' .centro_resp_buscar').attr('disabled',true);
		}
	}
}
*/
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
/*
function txtToValor(valor){
	var txt = valor;
	txt = txt.replace(/\./g, '');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
}
*/
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

function ativarCROT(ref,table_class){
	
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
		$("."+table_class+" input:checkbox").uniform();
	}
}

/*
===========================================================================================
VALIDAR SELEÇÃO DE CONTA FINANCEIRA
===========================================================================================
*/

function cfValidar(){
	var conta_id_import = $('#conta_id_import').val();
	if(conta_id_import=='0' || conta_id_import==''){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [ { text: "Fechar", click: function() { $("#dialog-alerta").dialog("close"); }	} ]);
		$('#dialog-alerta').html("<br/> Selecione uma conta financeira para realizar a importação.");
		$('#dialog-alerta').dialog('open');
		return false;
	}
	return true;
}


$(document).ready(function(e) {

/*
========================================================================================================================
DATA TABLE - COMPLEMENTOS
========================================================================================================================
*/

	//=============== TABELA EXTRATO BANCÁRIO =====================

	$('#input-search-table').bind('keyup',function(){
		var oTable = $('.dTableExtratoBanco').dataTable();
		oTable.fnFilter( $(this).val() );
	}) 

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

	//=============== TABELA ARQUIVO DE RETORNO =====================

	$('#input-search-table02').bind('keyup',function(){
		var oTable = $('.dTableBoletos').dataTable();
		oTable.fnFilter( $(this).val() );
	}) 

	$('#ckbTblHeader02').live('click',function(e) {
		var checkedStatus = this.checked;
		$('.lnctCheckbox02 div.checker span input:checkbox').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	});

	$('.B').live('click',function(e) {
		document.getElementById("ckbTblHeader02").checked = false;
		$('#ckbTblHeader02').closest('.checker > span').removeClass('checked');
	});

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

	// ================= Extrato =================================

	  var uploader = new plupload.Uploader({
		runtimes : 'html5',//'html5,flash,silverlight,html4',
		chunk_size: "1mb",
		browse_button : 'lnct_pickfiles', // you can pass in id...
		container: document.getElementById('lnct_container'), // ... or DOM Element itself

		url : "../modulos/lancamento/php/upload.php",

		filters : {
			max_file_size : '10mb',
			mime_types: [
				{title : "ofx files", extensions : "ofx"},
				{title : "xls files", extensions : "xls"},
				{title : "txt files", extensions : "txt,ret"},
				//{title : "Doc files", extensions : "doc,docx"},
			]
		},
		
		multipart_params: {
			"cliente_id": document.getElementById("cliente_id").value,
			"usuario_id": document.getElementById("usuario_id").value
		},
		
		//Select multiple files from the browse dialog
		multi_selection: false,

		//Drag & Drop
		drop_element: 'lnct_filelist',
		
		// Flash settings
		flash_swf_url : '/plupload/js/Moxie.swf',
	
		// Silverlight settings
		silverlight_xap_url : '/plupload/js/Moxie.xap',
	
		init: {
			PostInit: function() {
				document.getElementById('lnct_filelist').innerHTML = '';
	
				document.getElementById('lnct_uploadfiles').onclick = function() {
					uploader.start();
					return false;
				};
	
				$('#dados').data('lnct_file-upload-queue',0);
			},
	
			FilesRemoved: function(up, files) {
				$('#dados').data('lnct_file-upload-queue',files.length);
			},
	
			FilesAdded: function(up, files) {			
				var queue = $('#dados').data('lnct_file-upload-queue');
				var total_files = files.length;
				var files_added = total_files - queue;
				var arq, file;
				for(var i=queue;i<total_files;i++){
					file = files[i];
					//Pega o tipo de arquivo
					arq = file.name.split('.').pop();
					document.getElementById('lnct_filelist').innerHTML += '<li class="listaArquivos tipS" original-title="'+file.name+'" id="' + file.id + '" align="center"><img src="images/icons/arquivos/delete.png" class="delete"><b ></b><a href="php/uploads/'+file.name+'" class="'+file.id+'" download target="_blank" style="pointer-events:none;"><img src="images/icons/arquivos/icon-'+ arq +'.png" width="30" class="listaArquivosImg"/><br/>(' + plupload.formatSize(file.size) + ') </a></li>';
				}
				$('#dados').data('lnct_file-upload-queue',total_files);
				ativarCROT('t');
				uploader.start();
			},
	
			UploadProgress: function(up, file) {
				if(file.percent <100){ 	var percentual = file.percent+'%'; }else{  var percentual = '<img src="images/icons/updateDone.png" class="ok">'; $('.'+file.id).css("pointer-events", ""); }
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = percentual;
			},

			BeforeUpload: function(up, file) {
				var extensao = file.name.substr(file.name.lastIndexOf("."));
				if(extensao=='.xls')
					$("#dados").data("tp_arq","xls");
				else if(extensao=='.ofx')
					$("#dados").data("tp_arq","ofx");
			},

			UploadComplete: function(up, files) {
				$("#dialog-extrato-importar" ).dialog( "close" );
				var tp_extrato = $("input:radio[name='tp_extrato']:checked").val();
				if(tp_extrato==1)
					extratoIncluir();
				else
					boletosIncluir();
			},

		}
	
	});
	
	uploader.init();

	// ================= Anexo dos lançamentos =================================

	array_uploader = new Array();
	array_form = new Array();
	array_form = ['form_rcbt','form_pgto','form_trsf'];

	for(iForm=0;iForm<array_form.length;iForm++){

			array_uploader[iForm] = new plupload.Uploader({
			runtimes : 'html5',//'html5,flash,silverlight,html4',
			chunk_size: "1mb",
			browse_button : array_form[iForm]+'_pickfiles', // you can pass in id...
			container: document.getElementById(array_form[iForm]+'_container'), // ... or DOM Element itself
			
			url : "../php/upload.php",
			
			filters : {
				//drop_element : 'drop-target',
						//browse_button : 'drop-target',
				max_file_size : '10mb',
				mime_types: [
					{title : "Image files", extensions : "jpg,gif,png"},
					{title : "Zip files", extensions : "zip"},
					{title : "Rar files", extensions : "rar"},
					{title : "Pdf files", extensions : "pdf"},
					{title : "Xml files", extensions : "xml"},
					{title : "Xls files", extensions : "xls,xlsx"},
					{title : "Doc files", extensions : "doc,docx"},
				]
			},
	
			//Drag & Drop
			drop_element: array_form[iForm]+'_filelist',
			
			// Flash settings
			flash_swf_url : '/plupload/js/Moxie.swf',
		
			// Silverlight settings
			silverlight_xap_url : '/plupload/js/Moxie.xap',
		
			init: {
				PostInit: function() {
					document.getElementById(array_form[iForm-1]+'_filelist').innerHTML = '';
		
					//document.getElementById('form_rcbt_uploadfiles').onclick = function() {
						//uploader.start();
						//return false;
					//};
		
					$('#dados').data(array_form[iForm-1]+'_file-upload-queue',0);
				},
		
				FilesRemoved: function(up, files) {
					var form_id_ativo = $('#dados').data('form-id-ativo');
					$('#dados').data(form_id_ativo+'_file-upload-queue',files.length);
				},
		
				FilesAdded: function(up, files) {
					var form_id_ativo = $('#dados').data('form-id-ativo');
					var queue = $('#dados').data(form_id_ativo+'_file-upload-queue');
					var total_files = files.length;
					var files_added = total_files - queue;
					var arq, file;
					for(var i=queue;i<total_files;i++){
						file = files[i];
						//Pega o tipo de arquivo
						arq = file.name.split('.').pop();
						
						document.getElementById(form_id_ativo+'_filelist').innerHTML += '<li class="listaArquivos tipS" original-title="'+file.name+'" id="' + file.id + '" align="center"><img src="images/icons/arquivos/delete.png" class="delete" onclick="anexoExcluir(\'form_rcbt\',\''+file.id+'\',0)"><b></b><a href="php/uploads/'+file.name+'" class="'+file.id+'" download target="_blank" style="pointer-events:none;"><img src="images/icons/arquivos/icon-'+ arq +'.png" width="30" class="listaArquivosImg"/><br/>(' + plupload.formatSize(file.size) + ') </a></li>';
					}
					$('#dados').data(form_id_ativo+'_file-upload-queue',total_files);
					ativarCROT('t');
					//uploader.start();
				},

				BeforeUpload: function(up, file) {
					var form_ordem_ativo = $('#dados').data('form-ordem-ativo');
					array_uploader[form_ordem_ativo].settings.multipart_params = {
						"cliente_id": document.getElementById("cliente_id").value,
						"lancamento_id": $("#dados").data("lancamento_id"),
					};
				},
	
				UploadProgress: function(up, file) {
					if(file.percent <100){ 	var percentual = file.percent+'%'; }else{  var percentual = '<img src="images/icons/updateDone.png" class="ok">'; $('.'+file.id).css("pointer-events", ""); }
					document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = percentual;
				},
	
				UploadComplete: function(up, files) {
					notificacao(1,$("#dados").data("notificacao-sucesso"));
				},
		
				Error: function(up, err) {
					document.getElementById($('#dados').data('form-id-ativo')+'_console').innerHTML += "\nError #" + err.code + ": " + err.message;
				},
			}
		
		});
		
		array_uploader[iForm].init();
	
	}

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

	//===== UI dialog - Importar extrato e arquivo de retorno =====//

	$( "#dialog-extrato-importar" ).dialog({
		autoOpen: false,
		modal: true,
		closeOnEscape: false,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		open: function(event, ui) {  $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); },
		buttons: {			
			Fechar: function() {
				$( this ).dialog( "close" );
			}
		},
	});
	
	$( "#opener-extrato-importar" ).click(function(e) {
	    e.preventDefault();
	    if (cfValidar())
			uploadExtratoReset('extrato'); 
	});

	//===== UI dialog - Importar arquivo de retorno =====//

	$("#opener-arq-ret-import").click(function (e) {
	    e.preventDefault();
		if( cfValidar() )
			uploadExtratoReset('retorno');
	});

	//===== UI dialog - Recebimento =====//

	$( "#dialog-rcbt" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
	});
	
	$( "#opener-rcbt-incluir" ).click(function() {
		dialogConfig('dialog-rcbt','form_rcbt','add',0,'R',0);
		lancamentosLimpar('form_rcbt');
		$( "#dialog-rcbt" ).dialog( "open" );
		return false;
	});

	//===== UI dialog - Pagamento =====//

	$( "#dialog-pgto" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
	});
	
	$( "#opener-pgto-incluir" ).click(function() {
		dialogConfig('dialog-pgto','form_pgto','add',0,'P',0);
		lancamentosLimpar('form_pgto');
		$( "#dialog-pgto" ).dialog( "open" );
		return false;
	});

	//===== UI dialog - Transferência =====//

	$( "#dialog-trsf" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
	});
	
	$( "#opener-trsf-incluir" ).click(function() {
		dialogConfig('dialog-trsf','form_trsf','add',0,'T',0);
		lancamentosLimpar('form_trsf');
		$( "#dialog-trsf" ).dialog( "open" );
		return false;
	});

	//===== UI dialog - Buscar lançamentos para extrato bancário =====//

	$( "#dialog-lnct-buscar" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			//"Editar e Quitar": function() {
				//lnctExistValidar(1);
			//},
			Conciliar: function() {
				lnctExistValidar(2);
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
	});

	//===== UI dialog - Quitar lançamentos em lote =====//

	$( "#dialog-lnct-lote-quitar" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Quitar: function() {
				
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
	});

/*
========================================================================================================================
AUTO COMPLETAR
========================================================================================================================
*/
    /*
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
	*/
	//======== LANÇAMENTOS ===================
	//var cache5 = {};
	
	$( ".lancamentos_buscar" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache4 ) {
				//response( cache4[ term ] );
				//return;
			//}
			$.getJSON( "modulos/lancamento/paginas/lancamentos_buscar.php?tp_lnct="+$("#dados").data("tp_lnct_exist")+"&cf_id="+$("#dados").data("cf_id_exist")+"&cf_id="+$("#dados").data("cf_id_exist"), request, function( data, status, xhr ) {
				//cache5[ term ] = data;
				response( data );
			});
		},
		search: function( event, ui ) {
			var campo_id = $(this).attr('name');
			$('#'+campo_id+'_aguarde').css('display','block'); 
		},
		response: function( event, ui ) {
 
			//exibe apenas lançamentos que ainda não tenham sido buscados ou selecionados
			var lncts_id_exist =	$("#dados").data('lncts_exist_id'); 
			if(lncts_id_exist!=""){
				lncts_id_exist = lncts_id_exist.split(',');
				var qtd_ui = ui.content.length;
				var if_exist = -1;
				var i = 0;
				while(i<qtd_ui){
					if_exist = lncts_id_exist.indexOf(ui.content[i].id);
					if(if_exist>-1){
						ui.content.splice(i,1);
						qtd_ui--;
						i--;
					}
					i++
				}
			}

			var campo_id = $(this).attr('name');
			$('#'+campo_id+'_aguarde').css('display','none');
		},
		select: function( event, ui ) {
			//var campo_id = $(this).attr('name');
			//$('#'+campo_id).val(ui.item.id);
	 		//$('#'+campo_id+'_cg').css('display','block');
			//$(this).attr('disabled','disabled');
			fadeOut($(this).attr('id'));
			var box_id = $(this).data('box_id');
			var dados_lnct = {'id':ui.item.id,'dt_vencimento':ui.item.dt_vencimento,'dscr':ui.item.dscr,'valor':ui.item.valor,'is_rcr':ui.item.is_rcr,'tipo':ui.item.tipo,'conta_id_origem':ui.item.conta_id_origem,'nome':ui.item.nome};
			lnctExistAdd('autocomplete',dados_lnct);
	  }
	});
	
	$( ".lancamentos_buscar" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//======== FIM COMPLETAR PLANO DE CONTAS =============

/*
========================================================================================================================
RECALCULA LANÇAMENTOS SELECIONADOS AO MARCAR OU DESMARCAR O CHECKBOX OU RADIO
========================================================================================================================
*/

	$('#boxLnctSelected .lnctSugest').live('change',function(){ vlTotalLnctExist( $("#dados").data("input_type_cnlc") ); });

/*
========================================================================================================================
CHECKBOX BOOTSTRAP
========================================================================================================================
*/

	$(".ckb-compensado").bootstrapSwitch({
	    'state': true,
	    'size': 'mini',
	    'onText': 'Sim',
	    'offText': 'Não',
	    'inverse': true,
	    'onColor': 'success',
	    'offColor': 'warning',
	    'labelWidth': 1,
	    'onSwitchChange': function (event, state) {
	        var dt_id = $(this).data('dt-id');
	        if (state) {
	            $('#' + dt_id).attr('disabled', false);
	        } else {
	            $('#' + dt_id).attr('disabled', true);
	            $('#' + dt_id).val('');
	        }
	    }
	});

});

/*
========================================================================================================================
INICIALIZAR SALDOS E EXTRATOS
========================================================================================================================
*/

function cnlcIniciar(cf_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=cnlcIniciar";
	params += "&cf_id="+cf_id;
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes_cnlc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
			$('#saldo_banco').html('Saldo no Banco até '+data.dt_saldo_banco+' <span class="tp_extrato_divider"></span> <strong>R$ '+data.vl_saldo_banco+'</strong>');
			$('#saldo_wf').html('Saldo no Web Finanças <span class="tp_extrato_divider"></span> <strong>R$ '+data.vl_saldo+'</strong>');

			$('#lancamentos').html(data.extrato_bancario);
			dTable('dTableExtratoBanco');
			ativarCROT('','dTableExtratoBanco');
			if(data.qtd_lnct>0){
				$('#extrato-banco-widget').css('border-bottom','0px');
			}else{
				$('#extrato-banco-widget').css('border-bottom','1px solid #cdcdcd');
			}

			$('#extrato-arq-retorno').html(data.arq_retorno);
			dTable('dTableBoletos');
			ativarCROT('','dTableBoletos');
			if(data.qtd_blt>0){
				$('#arq-retorno-widget').css('border-bottom','0px');
			}else{
				$('#arq-retorno-widget').css('border-bottom','1px solid #cdcdcd');
			}

			$("span.aguarde, div.aguarde").css("display","none");
		}
	});
}

/*
========================================================================================================================
CHECAR TODOS
========================================================================================================================
*/

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

/*
========================================================================================================================
ALTERNAR EXIBIÇÃO ENTRE EXTRATO BANCÁRIO E ARQUIVO DE RETORNO
========================================================================================================================
*/

function changeExtrato(tp_extrato){
	if(tp_extrato=='1'){
		$('.arq-retorno').css('display','none');
		$('.extrato-banco').css('display','block');
	}else{
		$('.extrato-banco').css('display','none');
		$('.arq-retorno').css('display','block');
	}
}

/*
===========================================================================================
EXIBIR ANEXOS
===========================================================================================
*/

function anexosExibir(form_id,anexosJson){
	var anexos = JSON.parse(anexosJson);
	for(var i=0;i<anexos.length;i++){
		$('#'+form_id+'_filelist').append('<li class="listaArquivos tipS" original-title="'+anexos[i].nome_arquivo_org+'" id="' + anexos[i].id + '" align="center"><img src="images/icons/arquivos/delete.png" class="delete" onclick="anexoExcluir(\''+form_id+'\','+anexos[i].id+',1)"><b><img src="images/icons/updateDone.png" class="ok"></b><a href="php/uploads/'+anexos[i].nome_arquivo+'" class="'+anexos[i].id+'" download target="_blank"><img src="images/icons/arquivos/icon-'+anexos[i].ext+'.png" width="30" class="listaArquivosImg"/><br/>('+anexos[i].tamanho+' kb)</a></li>');
	}
}

/*
===========================================================================================
EXCLUÍR ANEXOS
===========================================================================================
*/

function anexoExcluir(form_id,anexo_id,situacao){
	if(situacao==1){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = 'funcao=anexoExcluir';
		params += '&anexo_id='+anexo_id;
		$.ajax({
			type: 'post',
			url: 'modulos/lancamento/php/funcoes.php',
			data: params,
			cache: true,
			dataType: 'json',
			success: function(data){
				$("span.aguarde, div.aguarde").css("display","none");
			}
		})
	}
	$('#'+anexo_id).remove();
	/*
	if(form_id=='form_rcbt')
		uploader.splice();
	else if(form_id=='form_pgto')
		uploader2.splice();
	else
		uploader3.splice();
	$('#dados').data(form_id+'_file-upload-queue',0); //essa linha deve vir obrigatoriamente após o splice poque ele também dispara os evento FilesRemoved e QueueChanged
	*/
}
