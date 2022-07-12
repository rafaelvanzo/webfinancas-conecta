// JavaScript Document
var iDisplayStart = 0;
var crud = false;

/*
===========================================================================================
MENSAGEM D ENOTIFICAÇÃO
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
	$("input:checkbox, input:radio, input:file").uniform();
	
		}
}

/*
===========================================================================================
REDESENHAR DATA TABLE LANÇAMENTOS
===========================================================================================
*/
/*
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
	//ativarCROT('t',id_obj);// Reaplicando Chackbox, Radio e Title 
}
*/

/*
===========================================================================================
ÍNDICE DE LINHA
===========================================================================================
*/

function linhaIndice(link_id,tabela_class){
	var tabela = $("."+tabela_class).dataTable();
	var celula = document.getElementById(link_id).parentNode.parentNode;
	var linha = celula.parentNode;
	var indice = tabela.fnGetPosition(linha);
	return indice;
}

/*
===========================================================================================
ATUALIZAR FREQUÊNCIA
===========================================================================================
*/

function frequenciaAtualizar(select_frequencia,form_id){
	var frequencia = select_frequencia.value;
	if(frequencia=='P'){
		$('#'+form_id+'_span_parcelas').attr('class','span3');
		$('#'+form_id+'_span_dias').css('display','inline-block');
	}else{
		if( $('#'+form_id+'_span_dias').is(':visible') ){
			$('#'+form_id+'_span_parcelas').attr('class','span4');
			$('#'+form_id+'_span_dias').css('display','none');
		}
	}
}

/*
===========================================================================================
ATUALIZAR VALOR DA PARCELA
===========================================================================================
*/

function moeda(id){

  $("#"+id).priceFormat({
    prefix: "",
    centsSeparator: ",",
    thousandsSeparator: "."
  });

}

function valorParcelaAtualizar(form_id){
	var qtd_parcelas = $('#'+form_id+'_qtd_parcelas').val();
	if(qtd_parcelas<1){
		qtd_parcelas = 1;
		$('#'+form_id+'_qtd_parcelas').val(1);
	}
	var valor = $('#'+form_id+'_valor').val();
	valor = valor.replace(".","");
	valor = valor.replace(",",".");
	valor = parseFloat(valor);
	var valor_parcela = valor/qtd_parcelas;
	valor_parcela = valor_parcela.toFixed(2);
	$('#'+form_id+'_valor_parcela').val(valor_parcela);
	moeda(form_id+'_valor_parcela');
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

	//Beneficiário reset
	$('#form_rcbt_favorecido_dep_id').val('0');

	$('span.check-green').css('display','none');
	$("#"+form+"_span_dias").css('display','none');
	$("#"+form+"_span_parcelas").attr('class','span4');
	$('.input-buscar').attr('disabled',false);

    //resetar abas
	$('#abas-' + form + ' a:first').tab('show');

    //resetar mais opções
	$('#'+form+' div.MaisOpcoes').attr('class','title closed MaisOpcoes normal');
	$('#'+form+' div.body:eq(0)').css('display','none');
	
	//reseta checkbox switch e data de compensaçao
	$('#'+form+'_compensado').bootstrapSwitch('state', false, true);
	$('#'+form+'_dt_compensacao').attr('disabled',true);
	
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
/** 
 * Verificar se a Categoria e Centro de custo estão preenchidos. 
 **/
function CatCtValid(tp_lnct)
{ 

	if(tp_lnct == 'R' || tp_lnct == 'P'){

		if(tp_lnct == 'R'){

			var obj = jQuery.parseJSON($('#form_rcbt_ctr_plc_lnct').val());
			console.log(obj);
		}else{

			var obj = jQuery.parseJSON($('#form_pgto_ctr_plc_lnct').val());
		}

		var verificar = 0;

		if(obj == null){

			verificar = 0;
			
		}else{

			$.each(obj, function(key, value){
				
				if(value.operacao < 3){
					verificar = 1;
				}
			});

		}


		if(verificar == 0){
			alert('Por favor, preencha as Plano de Contas e o Centro de Custo.');
			$(".catCtValid-" + tp_lnct).trigger("click");
			return false;
		}else{
			return true;
		}

	}else{
		return true;
	}

}



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
		var dialog_title_add = 'Novo Recebimento';
		if(compensado){
		    var dialog_title_edit = 'Editar Recebimento';
		}else{
			var dialog_title_edit = 'Editar Recebimento';
		}
		$('#dados').data('form-ordem-ativo',0); //usado pelo plupload
	}else if(tp_lnct=='P'){
	    var dialog_title_add = 'Novo Pagamento';
		if(compensado){
		    var dialog_title_edit = 'Editar Pagamento';
		}else{
			var dialog_title_edit = 'Editar Pagamento';
		}
		$('#dados').data('form-ordem-ativo',1); //usado pelo plupload
	}else{
		var dialog_title_add = 'Nova Transferência';
		var dialog_title_edit = 'Editar Transferência';
		$('#dados').data('form-ordem-ativo',2); //usado pelo plupload
	}

	//usado pelo plupload
	$('#dados').data('form-id-ativo',form_id);
	
	//Bloqueia ou desbloqueia campos de acordo com a operação
	if(opr=='add'){
		$('#'+form_id+'_linha_01').css('display','block');
		$('#'+form_id+'_span_frequencia select').attr('disabled',false).parent().css('display','block');
		$('#'+form_id+'_qtd_parcelas').attr('disabled',false).parent().css('display','block');
		$('#'+form_id+'_valor_parcela').attr('disabled',false).parent().css('display','block');
		//$('#'+form_id+'_sab_dom').attr('disabled',false).parent().css('display','block');
		$('#'+form_id+'_auto_lancamento').attr('disabled',false).parent().css('display','block');
	}else{
		$('#'+form_id+'_linha_01').css('display','none');
		$('#'+form_id+'_span_frequencia select').attr('disabled',true).parent().css('display','none');
		$('#'+form_id+'_qtd_parcelas').attr('disabled',true).parent().css('display','none');
		$('#'+form_id+'_valor_parcela').attr('disabled',true).parent().css('display','none');
		if(compensado){
			//$('#'+form_id+'_sab_dom').attr('disabled',true).parent().css('display','none');
			$('#'+form_id+'_auto_lancamento').attr('disabled',true).parent().css('display','none');
		}else{
			//$('#'+form_id+'_sab_dom').attr('disabled',false).parent().css('display','block');
			$('#'+form_id+'_auto_lancamento').attr('disabled',false).parent().css('display','block');
		}
	}

	if(opr=='add'){ //Configura formulário para incluir lançamento

		$( '#'+dialog_id ).dialog( "option", "title", dialog_title_add );
		
		$("#"+form_id+"_lancamento_id").val("0");

		//Habilita campos frequencia, parcela e valor da parcela
		if(tp_lnct=='R'||tp_lnct=='P'){
			$('#'+form_id+'_span_frequencia select').attr('disabled',false);
			$('#'+form_id+'_qtd_parcelas').attr('disabled',false);
			$('#'+form_id+'_valor_parcela').attr('disabled',false);
		}

		//Altera botôes da janela
		$('#'+dialog_id).dialog( "option", "buttons", [
			{
			    text: 'Salvar', click: function () {					


					if (AutoIncluirCategoriaECentroCusto(form_id)) {

						//$(".lnctValid-" + tp_lnct).trigger("click"); //Muda a ABA da janela

						if ($('#' + form_id).valid()) {


							if(CatCtValid(tp_lnct)){ //Verifica se a categoria e o plano de contas estão preenchidos

							
									if(verificarBloqueioLancamento(form_id)){

											$("#" + form_id + "_funcao").val('lancamentoIncluir');

											var incluir_compensado = $('#' + form_id + '_compensado').attr('checked');

											//Dialog parcelamento com a 1ª parcela compensada
											if ($('#form_rcbt_qtd_parcelas').val() > 1 && $('#form_rcbt_dt_compensacao').val().length > 0) {

												$("#dialog-alerta").dialog("option", "buttons", [
													{
														text: "Sim",
														click: function () { lancamentoIncluir(form_id, dialog_id, tp_lnct, incluir_compensado); $("#dialog-alerta").dialog("close"); }
													},
													{
														text: "Não",
														click: function () { $("#dialog-alerta").dialog("close"); }
													}
												]);

												$('#dialog-alerta').html("<br/> Será compensada apenas a 1ª parcela.<br/> Deseja realmente prosseguir?");

												$('#dialog-alerta').dialog('open');

											} else {

												lancamentoIncluir(form_id, dialog_id, tp_lnct, incluir_compensado);
											}

									} //verificarBloqueioLancamento

							}	

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
		

		if(tp_lnct=='R' && compensado==0){
			array_btn.push(
				//Botão Imprimir Boleto - função boletosImprimir()
				{
				    text: "Imprimir Boleto", click: function () {
				        //Incluir categoria/centro de custo automaticamente
				        if (AutoIncluirCategoriaECentroCusto(form_id)) {
				            $(".lnctValid-" + tp_lnct).trigger("click"); //Muda a ABA da janela
				            if ($('#' + form_id).valid()) {
				                $('#' + form_id + '_funcao').val(form_func_imprimir_boleto);
				                $('#' + dialog_id).dialog("close");
				                boletosImprimir();
				            }
				        }
					}
				}
			);
		}
	
		array_btn.push(
			//Botão Salvar - função lancamentoEditar()
			{
			    text: "Salvar", click: function () {

			        //Incluir categoria/centro de custo automaticamente
			        if (AutoIncluirCategoriaECentroCusto(form_id)) {

					
						$(".lnctValid-" + tp_lnct).trigger("click"); //Muda a ABA da janela

			            if ($('#' + form_id).valid()) {

							if(CatCtValid(tp_lnct)){ //Verifica se a categoria e o plano de contas estão preenchidos

								if(verificarBloqueioLancamento(form_id)){

									var compensar = $('#' + form_id + '_compensado').attr('checked');
									$('#' + form_id + '_funcao').val(form_func_edit_salvar);
									$('#' + dialog_id).dialog("close");
									lancamentoEditar(form_id, dialog_id, tp_lnct, compensado, compensar);
									
								}

							}

			            }
					
					} //verificarBloqueioLancamento
					
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
INCLUÍR LANCAMENTO
===========================================================================================
*/

function lancamentoIncluir(form_id, dialog_id, tp_lnct, compensado) {

            $("span.aguarde, div.aguarde").css("display", "block");
            //if(tp_lnct=='R'||tp_lnct=='P')
            //centroRespLnctIncluir(form_id);
            var params = $('#' + form_id).serialize();	console.log(params);
            $('#' + dialog_id).dialog("close");
            $.ajax({
                type: 'post',
                url: 'modulos/lancamento/php/funcoes.php',
                data: params,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    var dados = data; 
                    if (dados.situacao == 1) {
                        //$('#lancamentos').html(dados.lancamentos);
                        crud = true;
                        dTable.fnDraw();
                        //dTable();
                        if (compensado) {
                            $('#contasSaldo').html(dados.contas_saldo);
                            $('.saldoTotal').html(dados.saldo_total);
                            $('#saldo-acumulado').html(dados.saldo);
                            $('#credito-acumulado').html(dados.credito);
                        }

                        //inicia upload do anexo se houver
                        var file_queue = $('#dados').data(form_id + '_file-upload-queue');
                        if (file_queue > 0) {
                            $("#dados").data("notificacao-sucesso", dados.notificacao);
                            $("#dados").data("lancamento_id", dados.lancamento_id);
                            if (tp_lnct == 'R')
                                array_uploader[0].start();
                            else if (tp_lnct == 'P')
                                array_uploader[1].start();
                            else
                                array_uploader[2].start();
                        } else {
                            $("span.aguarde, div.aguarde").css("display", "none");
                            notificacao(1, dados.notificacao);
                        }
                    } else {
                        $("span.aguarde, div.aguarde").css("display", "none");
						notificacao(2, dados.notificacao); 
						setTimeout(function(){ 
							$( "#dialog-pgto" ).dialog( "open" ); 
						}, 600);
						
                    }
                },
            })

}



/*
===========================================================================================
EDITAR LANÇAMENTO
===========================================================================================
*/

function lancamentoEditar(form_id,dialog_id,tp_lnct,compensado,compensar){
   // if ($('#' + form_id).valid()) {
		$("span.aguarde, div.aguarde").css("display","block");
		//if(tp_lnct=='R'||tp_lnct=='P')
			//centroRespLnctIncluir(form_id);
		var params = $('#' + form_id).serialize();
		var lancamento_id = $("#"+form_id+"_lancamento_id").val();
		$('#'+dialog_id).dialog( "close" );
		$.ajax({
			type: 'post',
			url: 'modulos/lancamento/php/funcoes.php',
			data: params,
			dataType: 'json',
			cache: true,
			success: function(data){
				if(data.situacao==1){
					//$('#lancamentos').html(data.lancamentos);
				    crud = true;
				    dTable.fnDraw();
				    //dTable();
					if( !(compensado==0 && compensar==0) ){
						$('#contasSaldo').html(data.contas_saldo);
						$('.saldoTotal').html(data.saldo_total);
						$('#saldo-acumulado').html(data.saldo);
						$('#credito-acumulado').html(data.credito);
					}
					//inicia upload do anexo se houver
					var file_queue = $('#dados').data(form_id+'_file-upload-queue');
					if(file_queue>0){
						$("#dados").data("notificacao-sucesso",data.notificacao);
						$("#dados").data("lancamento_id",lancamento_id);
						if(tp_lnct=='R')
							array_uploader[0].start();
						else if(tp_lnct=='P')
							array_uploader[1].start();
						else
							array_uploader[2].start();
					} else {
					    $("span.aguarde, div.aguarde").css("display", "none");
					    notificacao(1, data.notificacao);
					}
				}else{
				    $("span.aguarde, div.aguarde").css("display", "none");
					notificacao(2, data.notificacao);
					setTimeout(function(){ 
						$( "#dialog-pgto" ).dialog( "open" ); 
					}, 600);
				}
				
			}
		})
	//}
}

/*
===========================================================================================
EXCLUÍR LANÇAMENTO
===========================================================================================
*/

function alertaExcluir(lnct_id, tp_lnct, is_rcr, compensado, lnct_pai_id) {
    $('#link-exc-' + lnct_id).parent().parent().attr('id', 'tbl-lnct-row-' + lnct_id);



	if (verificarBloqueioLancamento(form_id = false, lnct_id)) {



		if (lnct_pai_id == 0){

			$( "#dialog-alerta" ).dialog( "option", "buttons", [
			{
				text: "Sim",
				click: function() { lnctExcluir(lnct_id,tp_lnct,is_rcr,compensado); $("#dialog-alerta").dialog("close"); }
			},
			{
				text: "Não",
				click: function() { $("#dialog-alerta").dialog("close"); }
			}		
			]);
			$('#dialog-alerta').html("<br/> Deseja realmente excluír o registro selecionado?");
			$('#dialog-alerta').dialog('open');


		} else {

			$("#dialog-alerta").dialog("option", "buttons", [
			{
				text: "Excluir",
				click: function () {

					var esteLancamento = $('#ckb-excluir-parcela-01').is(':checked');
					var aVencer = $('#ckb-excluir-parcela-02').is(':checked');
					var vencido = $('#ckb-excluir-parcela-03').is(':checked');
					
					if (aVencer || vencido) {
						lnctExcluirParcelado(lnct_id, tp_lnct, compensado, lnct_pai_id, esteLancamento, aVencer, vencido);
					} else {
						lnctExcluir(lnct_id, tp_lnct, is_rcr, compensado)
					}
					
					$("#dialog-alerta").dialog("close");
				}
			},
			{
				text: "Cancelar",
				click: function () { $("#dialog-alerta").dialog("close"); }
			}
			]);

			var dialogHtml = '<br/> Quais registro(s) deseja realmente excluír?'
			+ '<div style="text-align:left">'
			+ '<br/><input id="ckb-excluir-parcela-01" type="checkbox" checked/> <label for="ckb-excluir-parcela-01"> Esta parcela </label>'
			+ '<br/><input id="ckb-excluir-parcela-02" type="checkbox"/> <label for="ckb-excluir-parcela-02"> Parcelas à vencer </label>'
			+ '<br/><input id="ckb-excluir-parcela-03" type="checkbox"/> <label for="ckb-excluir-parcela-03"> Parcelas vencidas </label>'
			+ '</div>';

			$('#dialog-alerta').html(dialogHtml);

			$('#dialog-alerta').dialog('open');
		}
		

	} //verificarBloqueioLancamento



}

function lnctExcluir(lnct_id,tp_lnct,is_rcr,compensado){
	$("span.aguarde, div.aguarde").css("display","block");
	var params;
	if(is_rcr){
		params = 'funcao=lancamentoRcrExcluir';
	}else{
		params = 'funcao=lancamentoExcluir';
	}
	params += '&lancamento_id='+lnct_id+'&compensado='+compensado+'&tipo='+tp_lnct;
	
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
			if(data.situacao==1){
				if(compensado){
					$('#contasSaldo').html(data.contas_saldo);
					$('.saldoTotal').html(data.saldo_total);
					$('#saldo-acumulado').html(data.saldo);
					$('#credito-acumulado').html(data.credito);
				}
				if(tp_lnct=='T'){
					//$('#lancamentos').html(data.lancamentos);
				    crud = true;
				    dTable.fnDraw();
					//dTable();
				}else{
					var tabela = $(".dTableLancamentos").dataTable();
					var indice = tabela.fnGetPosition( document.getElementById('tbl-lnct-row-'+lnct_id) );
					tabela.fnDeleteRow(indice);
				}
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
	})
}


//Excluir registros parcelados
function lnctExcluirParcelado(lnct_id, tp_lnct, compensado, lnct_pai_id, este_lancamento, a_vencer, vencido) {

    $("span.aguarde, div.aguarde").css("display", "block");

    var params = 'funcao=lancamentoExcluirParcelado' + '&lancamento_id=' + lnct_id + '&lnct_pai_id=' + lnct_pai_id + '&compensado=' + compensado + '&tipo=' + tp_lnct + '&este_lancamento=' + este_lancamento + '&a_vencer=' + a_vencer + '&vencido=' + vencido;

    $.ajax({
        type: 'post',
        url: 'modulos/lancamento/php/funcoes.php',
        data: params,
        cache: true,
        dataType: 'json',
        success: function (data) {
            if (data.situacao == 1) {
                if (compensado) {
                    $('#contasSaldo').html(data.contas_saldo);
                    $('.saldoTotal').html(data.saldo_total);
                    $('#saldo-acumulado').html(data.saldo);
                    $('#credito-acumulado').html(data.credito);
                }
                if (tp_lnct == 'T') {
                    //$('#lancamentos').html(data.lancamentos);
                    crud = true;
                    dTable.fnDraw();
                    //dTable();
                } else {
                    var tabela = $(".dTableLancamentos").dataTable();
                    var indice = tabela.fnGetPosition(document.getElementById('tbl-lnct-row-' + lnct_id));
                    tabela.fnDeleteRow(indice);
                }
                $('.nSuccess p').html(data.notificacao);
                $('.nSuccess').slideDown();
                setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
            } else {
                $('.nWarning p').html(data.notificacao);
                $('.nWarning').slideDown();
                setTimeout(function () { $('.nWarning').slideUp() }, 5000);
            }
            $("span.aguarde, div.aguarde").css("display", "none");
        }
    })
}

/*
===========================================================================================
EXIBIR LANÇAMENTO
===========================================================================================
*/

function lancamentosExibir(lancamento_id, acaoDireta, form_id, dialog_id, opr, is_rcr, tp_lnct, compensado, dt_referencia) {
	$("span.aguarde, div.aguarde").css("display","block");

	var params = '';

    if(is_rcr){
        params = "funcao=lancamentosRcrExibir";
        params += "&dt_referencia=" + dt_referencia;
	}else{
		params = "funcao=lancamentosExibir";
	}
	
	params += "&lancamento_id="+lancamento_id;
	params += "&tipo="+tp_lnct;
	
	$.ajax({
		type: 'post',
		url: 'modulos/lancamento/php/funcoes.php',
		data: params,
		dataType: 'json',
		cache: true,
		success: function(dados){

			lancamentosLimpar(form_id);
			dialogConfig(dialog_id,form_id,opr,is_rcr,tp_lnct,compensado); 
			
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

				$("#"+form_id+"_favorecido_dep").val(dados.lancamento.favorecido_dep);
				$("#"+form_id+"_favorecido_dep_id").val(dados.lancamento.favorecido_id_dep);

				$("#"+form_id+"_conta").val(dados.lancamento.conta);
				$("#"+form_id+"_conta_id").val(dados.lancamento.conta_id);
				$("#"+form_id+"_conta_id_ini").val(dados.lancamento.conta_id);
				$("#"+form_id+"_sab_dom").val(dados.lancamento.sab_dom);
				$("#"+form_id+"_documento_id").val(dados.lancamento.documento_id);
				$("#"+form_id+"_forma_pgto_id").val(dados.lancamento.forma_pgto_id);
                $("#"+form_id+"_cod_banco").val(dados.lancamento.cod_banco);
				ctrPlcLancamentosExibir(form_id,dados.ctr_plc_lancamentos,3,4);
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
			$("#"+form_id+"_auto_lancamento").val(dados.lancamento.auto_lancamento);
			$("#"+form_id+"_obs").val(dados.lancamento.observacao);
			$('#'+form_id+' .conta_buscar').attr('disabled',true);
			$('#'+form_id+' span.check-green').eq(0).css('display','block');
			$('#'+form_id+' span.check-green').eq(1).css('display','block');


				var dependente = dados.lancamento.favorecido_id_dep; 

			if(dependente == undefined || dependente == 0){ 
				$('#'+form_id+' span.check-green').eq(2).css('display','none'); 
				$('#'+form_id+' span.check-dark').eq(2).css('display','block');
				$('#'+form_id+' [name=form_rcbt_favorecido_dep_id]').removeAttr('disabled'); 				
			}else{ 
				$('#'+form_id+' span.check-green').eq(2).css('display','block');  		
			}
			

			if (compensado) {
				$('#'+form_id+'_compensado').bootstrapSwitch('state', true, true);
				$("#"+form_id+"_dt_compensacao").val(dados.lancamento.dt_compensacao);
				$("#"+form_id+"_dt_compensacao").attr('disabled',false);
			}

			// 1 = Compensar direto , 2 = Gerar Boleto direto, Default = abrir dialog para edição
			if(acaoDireta == '1'){ 
				//$("#dialog-dt-compensacao").dialog("open");


				if(verificarBloqueioLancamento(form_id)){

					compensar(form_id,dialog_id,is_rcr,tp_lnct);
					
				}		
			
			
			}else if(acaoDireta == '2'){ 
				//if(is_rcr){
					//boletosImprimirRcr();
				//}else{
					boletosImprimir();
				//}
			}else{
				if(dados.anexos!='' && !is_rcr)
				    anexosExibir(form_id, dados.anexos, dados.cliente_id);
				$('#'+dialog_id).dialog("open");
			}
			$("span.aguarde, div.aguarde").css("display","none");
			
		},
		error: function(erro){
		}
	})
}

/*
===========================================================================================
COMPENSAR LANÇAMENTO
===========================================================================================
*/

function compensar(form_id,dialog_id,is_rcr,tp_lnct){ 
	if($('#'+form_id).valid()){
		//if(tp_lnct=='R'||tp_lnct=='P')
			//centroRespLnctIncluir(form_id);
		var params = $('#'+form_id).serialize();
		funcao = function(){
			if($('#dt_compensacao').valid()){
				$('#dialog-dt-compensacao').dialog( "close" );
				$('span.aguarde, div.aguarde').css("display","block");
				$.ajax({
					type: 'post',
					url: 'modulos/lancamento/php/funcoes.php',
					data: params+"&dt_compensacao="+$("#dt_compensacao input[name='dt_compensacao']").val(),
					dataType: 'json',
					cache: true,
					success: function(data){
						if(data.situacao==1){
							//$('#lancamentos').html(data.lancamentos);
						    crud = true;
						    dTable.fnDraw();
							//dTable();
							$('#contasSaldo').html(data.contas_saldo);
							$('.saldoTotal').html(data.saldo_total);
							$('#saldo-acumulado').html(data.saldo);
							$('#credito-acumulado').html(data.credito);
							$('.nSuccess p').html(data.notificacao);
							$('.nSuccess').slideDown();
							setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
						}else{
							$('.nWarning p').html(data.notificacao);
							$('.nWarning').slideDown();
							setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
						}
						$("span.aguarde, div.aguarde").css("display","none");
					}
				})
			}
		}
		$('#'+dialog_id).dialog("close");
		$('#dialog-dt-compensacao').dialog( "open" );
	}
}

//PESQUISAR LANÇAMENTOS
//===========================================================================================

function lancamentosFiltrar() {
    crud = true;
    dTable.fnDraw();
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

	//tipo de vencimento
	var array_tp_venc = new Array();
	$('#tpVenc div.checker span.checked input[type="checkbox"]').each(function() {
		array_tp_venc.push("'"+$(this).val()+"'");
	});
	var flt_tp_venc = array_tp_venc.join(',');
	
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
	var flt_compensado = document.getElementById("compensado_pesq").checked;
	var flt_aberto = document.getElementById("aberto_pesq").checked;
	var flt_nosso_numero = document.getElementById("nosso-numero-pesquisar").value;

	//parametros
	var params = {
		//funcao: "lancamentosFiltrar",
		dt_ativo: flt_dt_ativo,
		dt_ini: dt_ini,
		dt_fim: dt_fim,
		dt_mes: dt_mes,
		tp_venc: flt_tp_venc,
		tp_lnct: flt_tp_lnct,
		conta_id: flt_contas,
		valor: flt_valor,
		nosso_numero: flt_nosso_numero,
		centro_resp_id: flt_centro_resp,
		plano_contas_id: flt_plano_contas,
		favorecido_id: flt_favorecido,
		documento_id: flt_documento,
		forma_pgto_id: flt_forma_pgto,
		parcelado: flt_parcelado,
		compensado: flt_compensado,
		aberto: flt_aberto
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
EXIBIR ANEXOS
===========================================================================================
*/
 
function anexosExibir(form_id, anexos, cliente_id) {
	for(var i=0;i<anexos.length;i++){
	    $('#' + form_id + '_filelist').append('<li class="listaArquivos tipS" original-title="' + anexos[i].nome_arquivo_org + '" id="' + anexos[i].id + '" align="center"><img src="images/icons/arquivos/delete.png" class="delete" onclick="anexoExcluir(\'' + form_id + '\',' + anexos[i].id + ',1)"><b><img src="images/icons/updateDone.png" class="ok"></b><a href="uploads/cliente_' + cliente_id + '/' + anexos[i].nome_arquivo + '" class="' + anexos[i].id + '" download target="_blank"><img src="images/icons/arquivos/icon-' + anexos[i].ext + '.png" width="30" class="listaArquivosImg"/><br/>(' + anexos[i].tamanho + ' kb)</a></li>');
	}
}

/*
===========================================================================================
EXCLUÍR ANEXOS
===========================================================================================
*/

function anexoExcluir(form_id, anexo_id, situacao) {

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
	} else {

	    var formIndice;

	    if (form_id == 'form_rcbt'){
	        var files = array_uploader[0].files;
	        formIndice = 0;
	    }else if (form_id == 'form_pgto'){
	        var files = array_uploader[1].files;
	        formIndice = 1;
	    }else{
	        var files = array_uploader[2].files;
	        formIndice = 2;
	    }

	    $.each(files, function (i, file) {
	        if (file.id == anexo_id)
	            array_uploader[formIndice].removeFile(file);
	    })

	    for (var i = 0; i < files.length; i++) {
	        if (files[i].id == anexo_id)
	            array_uploader[formIndice].removeFile(files[i]);
	    }
	}

	$('#' + anexo_id).remove();

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


/*
===========================================================================================
REQUISITA ATUALIZAÇÃO DA PARCELA E PC X CR
===========================================================================================
*/

function vlParcelaPcCrAtualizar(form_id){
	valorParcelaAtualizar(form_id);
}

/*
===========================================================================================
IMPRIMIR BOLETO
===========================================================================================
*/

function boletosImprimir(){
	if($('#form_rcbt').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		//centroRespLnctIncluir('form_rcbt');
		var params = $('#form_rcbt').serialize();
		$("#dialog-rcbt").dialog("close");
		$.ajax({
			type: 'post',
			url: 'modulos/lancamento/php/funcoes.php',
			data: params,
			dataType: 'json',
			cache: true,
			success: function(data){
			    if (!data.chave) {
			        $("#dialog-alerta").dialog("option", "buttons", [
                        {
                            text: "Fechar",
                            click: function () { $("#dialog-alerta").dialog("close"); }
                        }
			        ]);
			        $('#dialog-alerta').html("<br/> A emissão de boleto está indisponível para o banco selecionado.");
			        $('#dialog-alerta').dialog('open');
			    } else {
			        var dados = data;
			        lancamentosLimpar('form_rcbt');
			        //$('#lancamentos').html(dados.lancamentos);
			        dTable.fnDraw();
			        //dTable();
			        var win = window.open("https://app.webfinancas.com/boleto/" + dados.chave, '_parent'); //a url do boleto é reescrita no htaccess da pasta raiz do Web Finanças
			        win.focus();
			    }
			},
			error: function(erro){
			}
		})
		$("span.aguarde, div.aguarde").css("display", "none");
	}
}

/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

$(document).ready(function(e) {

	//===== UI dialog - Recebimento =====//

	$( "#dialog-rcbt" ).dialog({
		autoOpen: false,
		modal: true,
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		//buttons: {
			//Salvar: function() {
				//recebimentosIncluir();
			//},	
			//Cancelar: function() {
				//$( this ).dialog( "close" );
			//}
		//},
		//beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
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
		//buttons: {
			//Salvar: function() {
				//recebimentosIncluir();
			//},	
			//Cancelar: function() {
				//$( this ).dialog( "close" );
			//}
		//},
		//beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
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
		//buttons: {
			//Salvar: function() {
				//recebimentosIncluir();
			//},	
			//Cancelar: function() {
				//$( this ).dialog( "close" );
			//}
		//},
		//beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-trsf-incluir" ).click(function() {
		dialogConfig('dialog-trsf','form_trsf','add',0,'T',0);
		lancamentosLimpar('form_trsf');
		$( "#dialog-trsf" ).dialog( "open" );
		return false;
	});
	
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

	$('#checkAllTpVenc').click(function(e) {
		var checkedStatus = this.checked;
		$('#tpVenc div.checker span input:checkbox').each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
		//desmarca lançamentos compensados no filtro
		//document.getElementById("compensado_pesq").checked = false;
		//$('#compensado_pesq').closest('.checker > span').removeClass('checked');
		//filtroCalcular('listItens5');
	});
	
	$('.checkCf').click(function(e) {
		document.getElementById("checkAllCf").checked = false;
		$('#checkAllCf').closest('.checker > span').removeClass('checked');
	});

	$('.checkLnct').click(function(e) {
		document.getElementById("checkAllTpLnct").checked = false;
		$('#checkAllTpLnct').closest('.checker > span').removeClass('checked');
	});	

	$('.checkVenc').click(function(e) {
		//document.getElementById("checkAllTpVenc").checked = false;
		//$('#checkAllTpVenc').closest('.checker > span').removeClass('checked');
		//marca lançamentos em aberto no filtro
		document.getElementById("aberto_pesq").checked = true;
		$('#aberto_pesq').closest('.checker > span').addClass('checked');
		//desmarca lançamentos compensados no filtro
		document.getElementById("compensado_pesq").checked = false;
		$('#compensado_pesq').closest('.checker > span').removeClass('checked');
		//filtroCalcular('listItens5');
	});	

	//desmarca tipos de vencimento no filtro
	$('#compensado_pesq').click(function(e) {
		//document.getElementById("checkAllTpVenc").checked = false;
		//$('#checkAllTpVenc').closest('.checker > span').removeClass('checked');
		//$('#tpVenc div.checker span input:checkbox').each(function() {
			//this.checked = false;
			//$(this).closest('.checker > span').removeClass('checked');
		//});
		//filtroCalcular('listItens4');
		document.getElementById("checkVenc01").checked = false;
		$('#checkVenc01').closest('.checker > span').removeClass('checked');
		document.getElementById("checkVenc02").checked = false;
		$('#checkVenc02').closest('.checker > span').removeClass('checked');
	});
	

//DATA TABLE
//========================================================================================================================

    //Data table orçamento
	dTable = $('.dTableLancamentos').dataTable({
	    bProcessing: true,
	    bServerSide: true,
	    sAjaxSource: 'modulos/lancamento/php/funcoes.php?funcao=DataTableAjax',
	    "bJQueryUI": true,
	    "bAutoWidth": false,
	    "sPaginationType": "full_numbers",
        //bInfo: false,
	    //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
	    aoColumns: [
            { "mData": "lancamento", "sClass": "updates newUpdate" },
            //{ "mData": "options", "sClass": "actions" },
	    ],
	    "oLanguage": {
	        "sLengthMenu": "<span>Mostrar:</span> _MENU_",
	        "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
	    },
	    fnServerParams: function (aoData) {

            var filtro = filtroParams();


            if (crud) {
                var oSettings = this.fnSettings();
                oSettings._iDisplayStart = iDisplayStart;
            } else {
                var oSettings = this.fnSettings();
                iDisplayStart = oSettings._iDisplayStart;
            }
                //iDisplayStart = this._iDisplayStart;
	        

            aoData.push({ "name": "filtro", "value": filtro }, { "name": "iDisplayStart", "value": iDisplayStart });
	    },
	    fnServerData: function ( sSource, aoData, fnCallback, oSettings ) {

	        oSettings.jqXHR = $.ajax({
	            dataType: 'json',
	            type: "POST",
	            url: sSource,
	            data: aoData,
	            success: function (data) {
	                //console.log(JSON.stringify(data));
	                fnCallback(data);
	                if (data.carne_leao==1){
	                    $('#vl-rendimento-pf').text(data.imposto.recebimentosPf);
	                    $('#vl-rendimento-pj').text(data.imposto.recebimentosPj);
	                    $('#vl-dedutivel').text(data.imposto.deducoes);
	                    $('#vl-nao-dedutivel').text(data.imposto.naoDedutivel);
	                    $('#vl-despesa').text(data.imposto.pagamentos);
	                    $('#vl-base-imposto').text(data.imposto.base);
	                    if (data.imposto.imposto == '0,00')
	                        $('#vl-imposto').text('Isento');
                        else
	                        $('#vl-imposto').text(data.imposto.imposto);
	                }
	            }
	        });

	    },
	    fnDrawCallback: function () {
	        crud = false;
	        //$('#btn-filtro').button('reset');
	    }
	});

	$('.dTableLancamentos > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
	//$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima
    

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
		'onSwitchChange': function(event, state){
			var dt_id = $(this).data('dt-id');
			if (state) {

			    var date = new Date();

			    var day = date.getDate();
			    var month = date.getMonth() + 1;
			    var year = date.getFullYear();

			    if (month < 10) month = "0" + month;
			    if (day < 10) day = "0" + day;

			    var today = day + "/" + month + "/" + year;

			    $('#' + dt_id).attr('disabled', false);
			    $('#' + dt_id).val(today);
			}else{
			    $('#' + dt_id).attr('disabled', true);			   
			    $('#' + dt_id).val('');
				
			}
		}
	});

/*
========================================================================================================================
UNIFORM
========================================================================================================================
*/

	$(".pesq_container input:checkbox").uniform();

/*
========================================================================================================================
PLUPLOAD
========================================================================================================================
*/

/*
1 - Definir array contendo a declaração dos uploaders
2 - Definir array com nome dos forms para servir de prfixo para cada campo
3 - Definir uma variável para o loop for tomando cuidado com variáveis que já possam existir em todo o javascript utilizado no sistema e na página de maneira que sejam evitados conflitos
4 - As funções dentro do método init do plugin são executadas em tempo de execução, portanto foi definido um controle de qual formulário está ativo para utilizar a id correta do form
*/

	array_uploader = new Array();
	array_form = new Array();
	array_form = ['form_rcbt','form_pgto','form_trsf'];

	for(iForm=0;iForm<array_form.length;iForm++){

			array_uploader[iForm] = new plupload.Uploader({
			runtimes : 'html5',//'html5,flash,silverlight,html4',
			chunk_size: "5mb",
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
					$('#dados').data(form_id_ativo+'_file-upload-queue',files.length - 1);
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
					if (document.getElementById(file.id))
					    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = percentual;
				},
	
				UploadComplete: function(up, files) {
				    $("span.aguarde, div.aguarde").css("display", "none");
				    notificacao(1, $("#dados").data("notificacao-sucesso"));
				},
		
				Error: function(up, err) {
					document.getElementById($('#dados').data('form-id-ativo')+'_console').innerHTML += "\nError #" + err.code + ": " + err.message;
				},
			}
		
		});
		
		array_uploader[iForm].init();
	
	}

});



/* ========== Verificar se o lançamento pode ser incluido, editado ou excluido pelo bloqueio da contabilidade ============= */
function verificarBloqueioLancamento(form_id, lnct_id = false)
{

	if(form_id !== false){

		var lancamentoId = $('#' + form_id + '_lancamento_id').val();
		var dtVencimento = $('#' + form_id + '_dt_vencimento').val();
	
	}else{
		
		var lancamentoId = lnct_id;
		var dtVencimento = 0;

	}

	var retorno = true;
	
	var params = {
        funcao: 'BlqLiberarLanc',
		lancamentoId: lancamentoId,
		dtVencimento: dtVencimento
    };


    $.ajax({
        type: 'get',
        url: 'modulos/lancamento/php/funcoes.php',
        data: params,
        dataType: 'json',
		cache: true,
		async: false,
        success: function (dados) {
					

					if(dados.situacao === 0){

						alert('Lancamentos bloqueados para o período: ' + dados.dataBloqueio);			

						retorno = false;

					}else{
						
						retorno = true;
					}					
					
        },
        error: function (erro) {
            //alert(erro);
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.nWarning p').html("Erro: Por favor tente novamente.");
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);
			
        }
	})
	
	return retorno;
}