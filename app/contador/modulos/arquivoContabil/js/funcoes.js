// JavaScript Document
var dados_global;

//NOTIFICAÇÃO
//==============================================================================================
function notificacao(situacao, mensagem) {
    $("span.aguarde, div.aguarde").css("display", "none");
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
      url: 'modulos/arquivoContabil/php/funcoes.php', //url para onde será enviada as informações digitadas
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

$(document).ready(function(e) {

/*
===========================================================================================
REDESENHAR DATA TABLE LANÇAMENTOS
===========================================================================================
*/

oTable = $('.tblplanoContas').dataTable({
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

function dTable_tblplanoContas(){
	oTable = $('.tblplanoContas').dataTable({
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
	ativarCROT('t');/* Reaplicando Chackbox, Radio e Title */
}


/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

	//===== UI dialog - CONEXÃO CONTADOR E CLIENTE  =====//
	
	$( "#dialog-convite-contador" ).dialog({
		autoOpen: false,
		modal: true,
		Width: 'auto',
		position: { my: "top", at: "top+5%", of: window },
		buttons: {	
			Convidar: function() {
				convite_contador(); 
			},
			Cancelar:	function() {
				$( this ).dialog( "close" );
			}			
		}
	});
	
	$( "#opener-convite-contador" ).click(function() {
	    formLimpar('formConviteContador');
	    $("#dialog-convite-contador").dialog("open");
		return false;
	});	
	

    //===== UI dialog - REENVIAR CONVITES CONTADOR E CLIENTE  =====//
    $('.reenviarConvites').live("click",function(e){ 

	e.preventDefault(); 
	var id_list = $(this).attr('href'); 

	$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Sim",
			click: function() { reenviar_convite(id_list);  $("#dialog-alerta").dialog("close");}
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Deseja reenviar o convite?");

		$('#dialog-alerta').dialog('open');
		
	});
	
    //===== UI dialog - EXCLUIR CONEXÃO CONTADOR E CLIENTE  =====//
    $('.excluirConexao').live("click",function(e){ 

	e.preventDefault(); 
	var cliente_id = $(this).attr('href');
	var cliente_row_id = $(this).data('cliente-row-id');

	$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Sim",
			click: function () { cancelar_conexoes(cliente_id, cliente_row_id); $("#dialog-alerta").dialog("close"); }
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Deseja encerrar a conexão com o cliente?");

		$('#dialog-alerta').dialog('open');
		
	});
	
	//===== UI dialog - CONEXÃO CONTADOR E CLIENTE  =====//
	
	$( "#dialog-gerar-arquivo" ).dialog({
		autoOpen: false,
		modal: true,
		width: '570px',
		buttons: {	
			Gerar: function() {
				visualizar_lancamentos();
				$( this ).dialog( "close" );				 
			},
			Cancelar:	function() {
				$( this ).dialog( "close" );
			}			
		}
	});
	
	$( "#opener-gerar-arquivo" ).click(function() {
		$( "#dialog-gerar-arquivo" ).dialog( "open" ); 
		return false;
	});	

/*
========================================================================================================================
CHECAR TODOS - Contas Financeiras
========================================================================================================================
*/

	$('#contasChecarTodos').click(function(){
		var checked = this.checked;
		$('#contas-financeiras > input:checkbox').attr('checked',checked);
	})

	$('#contas-financeiras > input:checkbox').click(function(){
		if(!this.checked)
			$('#contasChecarTodos').attr('checked',false);
	})

/*
========================================================================================================================
CHECAR TODOS - Tratamento Contábil
========================================================================================================================
*/

	$('#tratamento-check-all').click(function () {
	    var checked = this.checked;
	    $('#tratamento-contabil > input:checkbox').attr('checked', checked);
	})

	$('#tratamento-contabil > input:checkbox').click(function () {
	    if (!this.checked)
	        $('#tratamento-check-all').attr('checked', false);
	})

/*
================================================================================================
ALTERNAR TABELAS DE CONFIGURAÇÃO
================================================================================================
*/

	$('input[name="tp_config"]').on('change', function () {
	    var tpConfig = $(this).val();
	    $('#btn-nova-categoria').css('display', 'none');
	    if (tpConfig == 1) {
	        $('#tbl-plc').css('display', 'none');
	        $('#tbl-modelo-plc').css('display', 'none');
	        $('#tbl-fav').css('display', 'none');
	        $('#tbl-cf').css('display', 'table');
	    } else if (tpConfig == 2) {
	        $('#tbl-cf').css('display', 'none');
	        $('#tbl-fav').css('display', 'none');
	        $('#tbl-plc').css('display', 'table');
	        $('#tbl-modelo-plc').css('display', 'table');
	        $('#btn-nova-categoria').css('display', 'inline-block');
	    } else {
	        $('#tbl-cf').css('display', 'none');
	        $('#tbl-plc').css('display', 'none');
	        $('#tbl-modelo-plc').css('display', 'none');
	        $('#tbl-fav').css('display', 'table');
	    }
	})

});	

/*
================================================================================================
SALVAR CONFIGURAÇÃO PLANO DE CONTAS
================================================================================================
*/

function salvar_config_pl(){
	//	if($('#salvarConfigPl').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#salvarConfigPl').serialize(); 	

		console.log(params);
	//}
	$.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/arquivoContabil/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, 
	  cache: true,
      success: function(data){ //alert(data);
	
		var dados = JSON.parse(data);
		
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
		}

	  	},
	  
    })	
}


function salvar_config_contas(){
	//	if($('#salvarConfigPl').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#salvarConfigContas').serialize(); 	

		console.log(params);
	//}
	$.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/arquivoContabil/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, 
	  cache: true,
      success: function(data){ //alert(data);
	
		var dados = JSON.parse(data);
		
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
		}

	  	},
	  
    })	
}

function salvar_config_plano(){
	//	if($('#salvarConfigPl').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#salvarConfigPlano').serialize(); 	
	//}
	$.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/arquivoContabil/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, 
	  cache: true,
      success: function(data){ //alert(data);
	
		var dados = JSON.parse(data);
		
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
		}

	  	},
	  
    })	
}

function salvar_config_favorecido(){
	//	if($('#salvarConfigPl').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#salvarConfigFavorecido').serialize(); 	

		console.log(params);
	//}
	$.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/arquivoContabil/php/funcoes.php', //url para onde será enviada as informações digitadas
      data: params, 
	  cache: true,
      success: function(data){ //alert(data);
	
		var dados = JSON.parse(data);
		
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
		}

	  	},
	  
    })	
}

/*
================================================================================================
VISUALIZAR LANÇAMENTOS CONTABILIDADE
================================================================================================
*/

function ValidarForm(filtro){

	//validar contas financeiras selecionadas
	if(filtro['cf']){
		var contCf = 0;
		$('#contas-financeiras > input:checkbox:checked').each(function(index, element) {
			contCf++;
		});
		if(contCf==0){
			alert('Selecione as contas financeiras');
			return false;
		}
	}
	
	//validar período
	if(filtro['periodo']){
		var mes_ini = $('#mes-ini').val();
		var mes_fim = $('#mes-fim').val();
		if(mes_ini=='' || mes_fim==''){
			alert('Selecione os meses inicial e final');
			return false;
		}

		mes_ini = mes_ini.split('/');
		mes_fim = mes_fim.split('/');
		var dt_ini = new Date(mes_ini[1],mes_ini[0],'01');
		var dt_fim = new Date(mes_fim[1],mes_fim[0],'01');
		var qtd_dias = Math.ceil( (dt_fim - dt_ini) / 86400000 );
		if(qtd_dias < 0){
			alert('O mês final deve ser maior ou igual ao mês inicial')
			return false;
		}
	}

	
	//validar tratamento contábil
	if (filtro['tratamentoContabil']) {
	    var rtc = $('#tratamento-contabil-rcbt > input:radio:checked').val();
	    var ptc = $('#tratamento-contabil-pgto > input:radio:checked').val();
	    if (!rtc || !ptc){
	        alert('Selecione o tipo de tratamento contábil');
	        return false;
	    }
	}
    /*
	if (filtro['tratamentoContabil']) {
	    var contTC = 0;
	    $('#tratamento-contabil > input:checkbox:checked').each(function (index, element) {
	        contTC++;
	    });
	    if (contTC == 0) {
	        alert('Selecione o tipo de tratamento contábil');
	        return false;
	    }
	}
    */

	return true;

}

//Visualizar lançamentos CONTABILIDADE
function visualizar_lancamentos(){

	var filtro = {'cf':true,'periodo':true,'tratamentoContabil':true};
	
	var validacao = ValidarForm(filtro);

	if(!validacao)
		return false;

    //Retornar id das contas financeiras
	var array_cf_id = new Array();
	$('#contas-financeiras > input:checkbox:checked').each(function(index, element) {
		array_cf_id.push(this.value);
	});
	var cf_id = array_cf_id.join(',');

    //Retornar tratamento contábil selecionado
	var rtc = $('input[name="rtc"]:checked').val();
	var ptc = $('input[name="ptc"]:checked').val();
    
	$("span.aguarde, div.aguarde").css("display", "block");
	var params = $('#form1').serialize();
	params += '&funcao=visualizarLancamentos';
	params += '&cf_id=' + cf_id;
	params += '&rtc=' + rtc;
	params += '&ptc=' + ptc;

	$.ajax({
		  
    type: 'post', //Tipo do envio das informações GET ou POST
    url: 'modulos/arquivoContabil/php/funcoes.php', //url para onde será enviada as informações digitadas
    data: params, 
	  cache: true,
    success: function(data){   
		
			//alert(data);
	
			var dados = JSON.parse(data); 
			
			//alert(dados.palco);
			
			$("span.aguarde, div.aguarde").css("display","none");
	
			if(dados.situacao == 1){
				//$('.nSuccess p').html(dados.notificacao);		
				//$('.nSuccess').slideDown();		
				//setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
				$('#div-lotes').html(dados.palco);
			}else if(dados.situacao == 2){
				$('.nWarning p').html(dados.notificacao);		
				$('.nWarning').slideDown(); 
				setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
			}

		}

  })

}

/*
================================================================================================
GERAR ARQUIVO CONTÁBIL
================================================================================================
*/

function gerar_arquivo_contabil(){
	$("span.aguarde, div.aguarde").css("display","block");

	var formData = new FormData();
	var loteNum, lancamentoId;

	formData.append('funcao', 'gerarArquivoContabil');
	formData.append('cliente_id', $('#cliente_id').val());
	formData.append('cf_id', $('#cf_id').val());
	formData.append('mes_ini', $('#mes_ini').val());
	formData.append('mes_fim', $('#mes_fim').val());
	formData.append('totalLote', $('#totalLote').val());

	$('.tbl-lote').each(function () {
	    var arrayLancamento = [];
	    loteNum = $(this).data('lote');
	    $('#tbl-lote-' + loteNum + ' > tbody > tr').each(function () {
	        lancamentoId = $(this).data('lancamento-id');
	        _tipo = $('#tipo-' + lancamentoId).val();
	        _data = $('#data-' + lancamentoId).val();
	        _credito = $('#credito-' + lancamentoId).val();
	        _debito = $('#debito-' + lancamentoId).val();
	        _valor = $('#valor-' + lancamentoId).val();
	        _descricao = $('#descricao-' + lancamentoId).val();
	        arrayLancamento.push({ id: lancamentoId, tipo: _tipo, data: _data, credito: _credito, debito: _debito, valor: _valor, descricao: _descricao });
	    })
	    formData.append('lote' + loteNum, JSON.stringify(arrayLancamento));
	})

    /*
	var formData = new FormData();
	var arrayLancamento = [];
	for (var i = 0; i < 500; i++) {
	    arrayLancamento.push({id:i, data:'11/11/2015', credito: '123', debito:'456', valor: '1.250,92', descricao: 'Lançamentos de teste do Web Finanças'});
	}
	formData.append('funcao', 'gerarArquivoContabil');
	formData.append('cliente_id', '171');
	formData.append('cf_id', '2');
	formData.append('mes_ini', '08/2015');
	formData.append('mes_fim', '08/2015');
	formData.append('totalLote', '1');
	formData.append('lancamentos', JSON.stringify(arrayLancamento));
    */

	//var params = $('#form_gerar_arquivo').serialize();
    
	$.ajax({
		type: 'post',
		url: 'modulos/arquivoContabil/php/funcoes.php',
		processData: false,
		contentType: false,
		data: formData,//params, 
		cache: true,
		success: function(data){ 
		    //alert(data);//$('#div-lotes').html(data);
			var data = JSON.parse(data);
			$("span.aguarde, div.aguarde").css("display","none");
			if(data.situacao == 1){
				$('.nSuccess p').html(data.notificacao);		
				$('.nSuccess').slideDown();		
				setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
				var win = window.open(data.download, '_self');	
				win.focus();
			}else if(data.situacao == 2){
				$('.nWarning p').html(data.notificacao);		
				$('.nWarning').slideDown(); 
				setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
			}
	 	},
	})
}

/*
================================================================================================
DOWNLOAD DE DOCUMENTOS
================================================================================================
*/

function DocumentosDownload(){
	
	var filtro = {'cf':true,'periodo':true,'tratamentoContabil':false};

	var validacao = ValidarForm(filtro);

	if(!validacao)
		return false;

	$("span.aguarde, div.aguarde").css("display","block");
	
	var array_cf_id = new Array();
	$('#contas-financeiras > input:checkbox:checked').each(function(index, element) {
    array_cf_id.push(this.value);
  });
	var cf_id = array_cf_id.join(',');

	var params = $('#form1').serialize();
	params += '&cf_id='+array_cf_id;
	params += '&funcao=DocumentosDownload';

	$.ajax({
		type: 'post',
		url: 'modulos/arquivoContabil/php/funcoes.php',
		data: params,
		cache: true,
		success: function(data){ //alert(data);
			$("span.aguarde, div.aguarde").css("display","none");	
			data = JSON.parse(data); //alert(data.notificacao);
			if(data.situacao == 1){
				//$('.nSuccess p').html(data.notificacao);
				//$('.nSuccess').slideDown();		
				//setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
				var win = window.open(data.documentos, '_self');
				win.focus();
			}else if(data.situacao == 2){
				$('.nWarning p').html(data.notificacao);		
				$('.nWarning').slideDown(); 
				setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
				}
			}
	})

}

/*
===========================================================================================
HISTÓRICO DA REMESSA CONTÁBIL
===========================================================================================
*/

function RemessaHistorico(){

	var filtro = {'cf':true,'periodo':true,'tratamentoContabil':false};

	var validacao = ValidarForm(filtro);

	if(!validacao)
		return false;

	var array_cf_id = new Array();
	$('#contas-financeiras > input:checkbox:checked').each(function(index, element) {
    array_cf_id.push(this.value);
  });
	var cf_id = array_cf_id.join(',');

	var mes_ini = $('#mes-ini').val();
	var mes_fim = $('#mes-fim').val();

	$("#historico-mes-ini").val(mes_ini);
	$("#historico-mes-fim").val(mes_fim);
	$("#historico-cf").val(cf_id);

	$("#formHistorico").submit();

}

//CARREGAR PLANO DE CONTAS
//-----------------------------------------------------------------------------------------

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

//LIMPAR FORM CATEGORIA
//------------------------------------------------------------------------------------------------------------------

function ResetFormCategoria(action) {

    var validator = $('#form_planoContas').validate();
    validator.resetForm();
    $('span.check-green').css('display', 'none');
    $("#nm_plc_pai").attr('disabled', false);
    $('#conta_pai_id').val(0);

    if (action == 'create') {
        $('#modal-categoria').dialog('option', 'title', 'Nova Categoria');
        $('#funcao-categoria').val('planoContasIncluir');
    } else {
        $('#modal-categoria').dialog('option', 'title', 'Editar Categoria');
        $('#funcao-categoria').val('planoContasEditar');
    }

    $('#ckb-dedutivel01').attr('checked', false);
}

$(document).ready(function (e) {

//MODAL CATEGORIA
//------------------------------------------------------------------------------------------------------------------

    $("#modal-categoria").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                planoContasIncluir();
                //$( this ).dialog( "close" );
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        }
    });

    $("#btn-nova-categoria").click(function (e) {
        e.preventDefault();
        ResetFormCategoria('create');
        $("#modal-categoria").dialog("open");
    });


//AUTOCOMPLETAR CATEGORIA
//------------------------------------------------------------------------------------------------------------------

    var plc_render_item = function (ul, item) {
        //Add the ui-state-disabled class and don't wrap in <a> if value is empty
        if (item.tp_ctr_plc == 1) {
            if (item.dedutivel == 1)
                return $("<li>").append("<a style='color:green;'>" + item.label + "</a>").appendTo(ul);
            else
                return $("<li>").append("<a>" + item.label + "</a>").appendTo(ul);
        } else {
            return $("<li class='ui-state-disabled'>").append("<a>" + item.label + "</a>").appendTo(ul);
        }
    }

    //var cache = {};

    $(".plano_contas_buscar_plc").autocomplete({
        minLength: 0,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache ) {
            //response( cache[ term ] );
            //return;
            //}
            $.getJSON("https://app.webfinancas.com/contador/php/plano_contas_buscar.php", request, function (data, status, xhr) {
                //cache[ term ] = data;
                response(data);
            });
        },
        search: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id + '_aguarde').css('display', 'block');
        },
        response: function (event, ui) {
            //alert(ui);
            var campo_id = $(this).attr('name');
            $('#' + campo_id + '_aguarde').css('display', 'none');
            if (ui.content.length == 0) {
                //alert('nenhum resultado encontrado');
            }
            //alert('resposta');
        },
        select: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id).val(ui.item.id);
            $('#' + campo_id + '_cg').css('display', 'block');
            $(this).attr('disabled', 'disabled');
            fadeOut($(this).attr('id'));
        }
    });//.data("ui-autocomplete")._renderItem = plc_render_item;

    $(".plano_contas_buscar_plc").click(function () {
        var campo_id = $(this).attr('id');
        $("#" + campo_id).autocomplete("search");
    })

//CHECKBOX BOOTSTRAP
//------------------------------------------------------------------------------------------------------------------
    /*
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
    //});

});

//INCLUÍR CATEGORIA
//------------------------------------------------------------------------------------------------------------------

function planoContasIncluir() {

    if ($('#form_planoContas').valid()) {

        $("span.aguarde, div.aguarde").css("display", "block");

        var data = $('#form_planoContas').serialize();

        $('#modal-categoria').dialog("close");

        $.ajax({
            type: 'post',
            url: 'modulos/arquivoContabil/php/funcoes.php',
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.situacao == 1) {
                    //crud = true;
                    //dTableFuncionarios.fnDraw();
                    $('#plano-contas').html(data.planoContas.planoContas);
                    $('#total-categorias').val(data.planoContas.totalCategorias);
                    notificacao(1, data.notificacao);
                } else {
                    notificacao(2, data.notificacao);
                    $("#modal-categoria").dialog("open");
                    //$("#dialog-alerta").dialog("option", "buttons", [{ text: "OK", click: function () { $("#dialog-alerta").dialog("close"); $("#dialog-message-planoContas-incluir").dialog("open") } }]);
                    //$('#dialog-alerta').html(dados.notificacao);
                    $("span.aguarde, div.aguarde").css("display", "none");
                    //$('#dialog-alerta').dialog('open');
                }
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }
}

//EXIBIR CATEGORIA
//------------------------------------------------------------------------------------------------------------------

function planoContasExibir(planoContas_id, tpConta, clienteId) {

    $("span.aguarde, div.aguarde").css("display", "block");

    var data = {
        funcao: 'planoContasExibir',
        planoContas_id: planoContas_id,
        clienteId: clienteId
    };

    $.ajax({
        type: 'post',
        url: 'modulos/arquivoContabil/php/funcoes.php',
        data: data,
        dataType: 'json',
        success: function (data) {

            ResetFormCategoria('details');
            var dados = data;
            $("#form_planoContas input[name='plano_contas_id']").val(dados.id);
            $("#form_planoContas input[name='cod_conta']").val(dados.cod_conta);
            $("#form_planoContas input[name='cod_ref']").val(dados.cod_ref);
            $("#cod_ref_ini").val(dados.cod_ref);
            $("#form_planoContas input[name='nome']").val(dados.nome);
            $("#form_planoContas input[name='conta_snt']").val(dados.conta_snt);
            $("#form_planoContas select[name='tp_conta']").val(dados.tp_conta);
            $("#form_planoContas textarea[name='descricao']").val(dados.descricao);
            $("#form_planoContas select[name='situacao']").val(dados.situacao);
            $("#form_planoContas select[name='clfc_fc']").val(dados.clfc_fc);
			$("#form_planoContas select[name='clfc_dre']").val(dados.clfc_dre);
			$("#form_planoContas select[name='tpCategoria']").val(dados.tpCategoria);
            $("#nm_plc_pai").val(dados.conta_pai_nome);
            $("#buscar_conta_pai_id").val(dados.conta_pai_id);
            $("#conta_pai_id_ini").val(dados.conta_pai_id);

            if (dados.conta_pai_id == 0) {
                $('#nm_plc_pai').attr('disabled', false);
            } else {
                $('#nm_plc_pai').attr('disabled', true);
                $('#form_planoContas span.check-green').css('display', 'block');
            }
            $("#form_planoContas input[name='nivel']").val(dados.nivel);
            $("#form_planoContas input[name='posicao']").val(dados.posicao);

            if (dados.qtd_sub_contas > 0) {
                $("#tp_conta_a").attr('disabled', true);
            } else if (dados.qtd_lancamentos > 0) {
                $("#tp_conta_s").attr('disabled', true);
            } else {
                $("#tp_conta_a").attr('disabled', false);
                $("#tp_conta_s").attr('disabled', false);
            }

            if (dados.dedutivel == 1)
                $('#ckb-dedutivel01').attr('checked', 'checked');

            if (tpConta == 1)
                $('#div-ckb-dedutivel').css('display', 'block');
            else
                $('#div-ckb-dedutivel').css('display', 'none');

            $("span.aguarde, div.aguarde").css("display", "none");

            $('#modal-categoria').dialog("open");
        },
    })

}

//EDITAR CATEGORIA
//------------------------------------------------------------------------------------------------------------------

function planoContasEditar() {

    if ($('#form_planoContas').valid()) {

        $("span.aguarde, div.aguarde").css("display", "block");

        var data = $('#form_planoContas').serialize();

        $('#modal-categoria').dialog("close");

        $.ajax({
            type: 'post',
            url: 'modulos/arquivoContabil/php/funcoes.php',
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.situacao == 1) {
                    //crud = true;
                    //dTableFuncionarios.fnDraw();
                    $('#plano-contas').html(data.planoContas.planoContas);
                    $('#total-categorias').val(data.planoContas.totalCategorias);
                    notificacao(1, data.notificacao);
                } else {
                    notificacao(2, data.notificacao);
                    $("#modal-categoria").dialog("open");
                }
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }
}

//EXCLUÍR CATEGORIA
//------------------------------------------------------------------------------------------------------------------

$(document).ready(function () {

    $('.planoContasExcluir').live("click", function (e) {

        e.preventDefault();

        var planoContas_id = $(this).attr('href');
        var clienteId = $(this).data('cliente-id');

        $("#dialog-alerta").dialog("option", "buttons", [
		{
		    text: "Sim",
		    click: function () { planoContasExcluir(planoContas_id, clienteId); $("#dialog-alerta").dialog("close"); }
		},
		{
		    text: "Não",
		    click: function () { $("#dialog-alerta").dialog("close"); }
		}
        ]);

        $('#dialog-alerta').html("<br/> Confirmar exclusão?");

        $('#dialog-alerta').dialog('open');

    });

})

function planoContasExcluir(planoContas_id, clienteId) {

    $("span.aguarde, div.aguarde").css("display", "block");

    var data = {
        funcao: 'planoContasExcluir',
        planoContas_id: planoContas_id,
        clienteId: clienteId
    };

    $.ajax({
        type: 'post',
        url: 'modulos/arquivoContabil/php/funcoes.php',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.situacao == 1) {
                $('#plano-contas').html(data.planoContas.planoContas);
                $('#total-categorias').val(data.planoContas.totalCategorias);
                notificacao(1, data.notificacao);
            } else {
                notificacao(2, data.notificacao);
            }
            $("span.aguarde, div.aguarde").css("display", "none");
        },
    });

}


/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

    //===== UI dialog - ADD INFORAMTIVO  =====//
	
	$( "#dialog-bloqueio" ).dialog({
		autoOpen: false,
		modal: true,
		width: '500px',
		position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false'
	});


/*
========================================================================================================================
BLOQUEAR LANÇAMENTO DE CADA CLIENTE
========================================================================================================================
*/
function abrirJanelaBloqueio(clienteId) {

	//Desabilitar button enquando estiver carregando as informações do banco de dados.
	$("button").attr("disabled", true);

	//Atribui Id do cliente para quando o select for alterado, ter a referencia de qual é a id do cliente
	$('.anoBloqueio').attr('data-clienteid', clienteId);

    //Altera o título
    $("#dialog-bloqueio").dialog("option", "title", "Bloquear/Liberar Lancamentos de clientes");

    $("span.aguarde, div.aguarde").css("display", "block");

	var params = {
        funcao: 'BlqLiberarLancExibir',
		ano: $('.anoBloqueio').val(),		
		clienteId: clienteId
    };


    $.ajax({
        type: 'get',
        url: 'modulos/arquivoContabil/php/funcoes.php',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {

				//console.log(dados); 
				
				$.each(dados, function(key, val) { 
					
						var element = $('.' + key);
						element.attr('data-bloqueado', val);

						if(val === 1)
						{		

							element.addClass('bloqueado').html('Bloqueado');

						}else{

							element.removeClass('bloqueado').html('Liberado');

						}
					
					});  
				

			$("span.aguarde, div.aguarde").css("display", "none");

			$("#dialog-bloqueio").dialog("open");
			
        },
        error: function (erro) {
            //alert(erro);
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.nWarning p').html("Erro: Por favor tente novamente.");
            $('.nWarning').slideDown();
            setTimeout(function () { $('.nWarning').slideUp() }, 5000);

			$("#dialog-bloqueio").dialog("close");
			
        }
	})
	
	//Habilita os buttons novamente.
	$("button").attr("disabled", false);

}


// ========= Select para alterar o ano e exibir os botões bloquear e liberar ========= */

$('.anoBloqueio').change(function() {
	
	var clienteId = $('.anoBloqueio').data('clienteid');

	abrirJanelaBloqueio(clienteId);

});

// ========= Botão de bloquear ou liberar o acesso ao movimento ==========
$('.btn-bloquear').click(function(){
	
	var btn = $(this);
	
	btn.attr('disabled', true).addClass('bloquear-aguarde').html('Aguarde..');

	var params = {
        funcao: 'BloquearLiberarLancamentos',
		ano: $('.anoBloqueio').val(),
		mes: btn.data('mes'),
		bloqueado: btn.data('bloqueado'),
		clienteId: $('.anoBloqueio').data('clienteid')
    };

	$.ajax({
        type: 'get',
        url: 'modulos/arquivoContabil/php/funcoes.php',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {

			btn.attr('disabled', true).removeClass('bloquear-aguarde');

				if(dados.bloqueado === 1)
				{
					btn.attr('disabled', false).addClass('bloqueado').html('Bloqueado').attr('data-bloqueado', 1); 

				}else{

					btn.attr('disabled', false).removeClass('bloqueado').html('Liberado').attr('data-bloqueado', 0);; 

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
	
});

// ========= Botão de bloquear ou liberar o acesso ao movimento ==========