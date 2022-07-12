// JavaScript Document
var crud = false;

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
      url: 'modulos/favorecido/php/funcoes.php', //url para onde será enviada as informações digitadas
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
DIALOGS
========================================================================================================================
*/

$(document).ready(function(e) {
 
	//===== UI dialog - Incluir conta =====//
  
	$( "#dialog-message-favorecido-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				favorecidosIncluir();
				//$( this ).dialog( "close" );
			},	
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		//beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-favorecido-incluir" ).click(function() {
		favorecidosLimpar('form_fav');
		$( "#dialog-message-favorecido-incluir" ).dialog( "open" );
		return false;
	});		

	//===== UI dialog - Editar conta =====//
	
	$( "#dialog-message-favorecido-editar" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {
			Salvar: function() {
				favorecidosEditar();
				//$( this ).dialog( "close" );
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		//beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
	});
	
	$( "#opener-favorecido-editar" ).click(function() {
		$( "#dialog-message-favorecido-editar" ).dialog( "open" );
		return false;
	});		

	//===== UI dialog - Importar favorecidos =====//

	$( "#dialog-fav-import" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		closeOnEscape: false,
		open: function(event, ui) {  $(this).parent().children().children('.ui-dialog-titlebar-close').hide(); }
	});
	
	$( "#opener-fav-import" ).click(function() {
		uploadReset();
	});

});

/*
===========================================================================================
INCLUÍR
===========================================================================================
*/

function favorecidosIncluir(){
	if($('#form_fav').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_fav').serialize();
		$("#dialog-message-favorecido-incluir").dialog( "close" );

		funcaoRetorno = function(data){
			var dados = JSON.parse(data);
			$('.nSuccess p').html(dados.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			//$('#favorecidos').html(dados.favorecidos);
			crud = true;
			dTable.fnDraw();
			$("span.aguarde, div.aguarde").css("display","none");	
		}

		ajax_jquery(params);
	}
}

/*
===========================================================================================
EXIBIR FAVORECIDO
===========================================================================================
*/

function favorecidosVisualizar(favorecido_id){
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=favorecidosVisualizar";
	params += "&favorecido_id="+favorecido_id;

	funcaoRetorno = function(data){
		favorecidosLimpar('form_fav_editar');
		var dados = JSON.parse(data);
		$("#form_fav_editar input[name='favorecido_id']").val(dados.id);
		$("#form_fav_editar input[name='nome']").val(dados.nome);
		$("#form_fav_editar select[name='inscricao']").val(dados.inscricao);
		$("#form_fav_editar input[name='cpf_cnpj']").val(dados.cpf_cnpj);	
		$("#form_fav_editar select[name='tp_favorecido']").val(dados.tp_favorecido);
		$("#form_fav_editar input[name='logradouro']").val(dados.logradouro);
		$("#form_fav_editar input[name='numero']").val(dados.numero);
		$("#form_fav_editar input[name='complemento']").val(dados.complemento);	
		$("#form_fav_editar input[name='bairro']").val(dados.bairro);
		$("#form_fav_editar input[name='cidade']").val(dados.cidade);
		$("#form_fav_editar select[name='uf']").val(dados.uf);
		$("#form_fav_editar input[name='cep']").val(dados.cep);
		$("#form_fav_editar input[name='email']").val(dados.email);
		$("#form_fav_editar input[name='telefone']").val(dados.telefone);
		$("#form_fav_editar input[name='celular']").val(dados.celular);
		$("#form_fav_editar textarea[name='observacao']").val(dados.observacao);
		if(dados.cliente_plc_id!=0){
			$("#form_fav_editar span.check-green").eq(0).css('display','block');
			$("#form_fav_editar_pl_conta_buscar_01").attr('disabled',true);
			$('#form_fav_editar_pl_conta_buscar_01').val(dados.cliente_plc);
			$('#form_fav_editar_pl_conta_id_01').val(dados.cliente_plc_id);
		}
		if(dados.cliente_ctr_id!=0){
			$("#form_fav_editar span.check-green").eq(1).css('display','block');
			$("#form_fav_editar_ct_resp_buscar_01").attr('disabled',true);
			$('#form_fav_editar_ct_resp_buscar_01').val(dados.cliente_ctr);
			$('#form_fav_editar_ct_resp_id_01').val(dados.cliente_ctr_id);
		}
		if(dados.fornecedor_plc_id!=0){
			$("#form_fav_editar span.check-green").eq(2).css('display','block');
			$("#form_fav_editar_pl_conta_buscar_02").attr('disabled',true);
			$('#form_fav_editar_pl_conta_buscar_02').val(dados.fornecedor_plc);
			$('#form_fav_editar_pl_conta_id_02').val(dados.fornecedor_plc_id);
		}
		if(dados.fornecedor_ctr_id!=0){
			$("#form_fav_editar span.check-green").eq(3).css('display','block');
			$("#form_fav_editar_ct_resp_buscar_02").attr('disabled',true);
			$('#form_fav_editar_ct_resp_buscar_02').val(dados.fornecedor_ctr);
			$('#form_fav_editar_ct_resp_id_02').val(dados.fornecedor_ctr_id);
		}
		if(dados.banco_id!=0){
			$("#form_fav_editar input[name='bancos_buscar_editar']").val(dados.bancoNome);
			$("#form_fav_editar input[name='banco_id']").val(dados.banco_id);
			$('span.check-green').css('display','block');
		}
		$("#form_fav_editar select[name='tp_conta']").val(dados.tp_conta);
		$("#form_fav_editar input[name='ag']").val(dados.agencia);
		$("#form_fav_editar input[name='conta']").val(dados.conta);
		$( "#dialog-message-favorecido-editar" ).dialog( "open" );
		$("span.aguarde, div.aguarde").css("display","none");
	}

	ajax_jquery(params);
}

/*
===========================================================================================
EDITAR FAVORECIDO
===========================================================================================
*/

function favorecidosEditar(){
	if($('#form_fav_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_fav_editar').serialize();
		$("#dialog-message-favorecido-editar").dialog( "close" );

		funcaoRetorno = function(data){
			//alert(dados_global);
			var dados = JSON.parse(data);
		    //$('#favorecidos').html(dados.favorecidos);
			crud = true;
			dTable.fnDraw();
			$('.nSuccess p').html(dados.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			$("span.aguarde, div.aguarde").css("display","none");
		}

		ajax_jquery(params);
	}
}

/*
===========================================================================================
EXCLUÍR
===========================================================================================
*/

$(document).ready(function(){
	$('.favorecidosExcluir').live("click",function(e){

		e.preventDefault();

		var favorecido_id = $(this).attr('href');

		$('#link-exc-' + favorecido_id).parent().parent().attr('id', 'tbl-lnct-row-' + favorecido_id);

		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { favorecidosExcluir(favorecido_id); $("#dialog-alerta").dialog("close");}
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

function favorecidosExcluir(favorecido_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=favorecidosExcluir&favorecido_id="+favorecido_id;
	funcaoRetorno = function(data){
		var dados = JSON.parse(data);
		if(dados.situacao==1){
			$('.nSuccess p').html(dados.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			var tabela = $("#dTableFavorecidos").dataTable();
			var indice = tabela.fnGetPosition(document.getElementById('tbl-lnct-row-' + favorecido_id));
			tabela.fnDeleteRow(indice);
		}else{
			$('.nWarning p').html(dados.notificacao);
			$('.nWarning').slideDown();
			setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
		}
		$("span.aguarde, div.aguarde").css("display","none");
	}
	ajax_jquery(params);
}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function favorecidosLimpar(form){

	var validator = $('#'+form).validate();
	validator.resetForm();
	$("#"+form+" input[name='banco_id']").val("");
	$('span.check-green').css('display','none');

	//resetar abas
	$('#'+form+' div.MaisOpcoes').attr('class','title closed MaisOpcoes normal');
	$('#'+form+' div.body:eq(0)').css('display','none');
}

/*
===========================================================================================
ALTERNAR ENTRE MASCARA CPF E CNPJ
===========================================================================================
*/

function cpfCnpjChangeMask(tipo){

	var id = $("#"+tipo).val();

	if(id == "cnpj"){
		var add_cpf_cnpj = "maskCnpj";
		var remove_cpf_cnpj = "maskCpf";
	}else{
		var add_cpf_cnpj = "maskCpf";
		var remove_cpf_cnpj = "maskCnpj";
	}
	
	$('.cpf_cnpj').addClass(add_cpf_cnpj);
	$('.cpf_cnpj').removeClass(remove_cpf_cnpj);

	//Ativa as mascaras abaixo		
	$(".maskCpf").mask("999.999.999-99");
	$(".maskCnpj").mask("99.999.999/9999-99");

}

function checkCpfCnpj(){ 

		var verif = $("#cpf_cnpj").val();
	
	if(verif == ""){
	
		var id = $("#inscEditar").val();
	
	if(id == "cnpj"){
		
		var add_cpf_cnpj = "maskCnpj";
		var remove_cpf_cnpj = "maskCpf";
	}else{
		var add_cpf_cnpj = "maskCpf";
		var remove_cpf_cnpj = "maskCnpj";
	}
	
	$('.cpf_cnpj').addClass(add_cpf_cnpj);
	$('.cpf_cnpj').removeClass(remove_cpf_cnpj);

	//Ativa as mascaras abaixo		
	$(".maskCpf").mask("999.999.999-99");
	$(".maskCnpj").mask("99.999.999/9999-99");
	}
}

/*
========================================================================================================================
VALIDAR SELEÇÃO DOS CAMPOS DA IMPORTAÇÃO
========================================================================================================================
*/

function selectCampoValid(){
	var formSelects = document.getElementById('form_fav_import').elements;
	var i = 1;
	var limite = formSelects.length-2;
	var valid = true;
	var optIndice;
	while( i<=limite && valid ){
		optIndice = formSelects[i].selectedIndex;
		if(optIndice<=1){
			valid = false;
		}
		i++;
	}
	if(!valid){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Ok",
			click: function() { $("#dialog-alerta").dialog("close");}
		},
		]);
		$('#dialog-alerta').html("<br/> Selecione um nome para cada campo da planilha.");
		$('#dialog-alerta').dialog('open');
	}
	return valid;
}

/*
========================================================================================================================
DEFINIR CAMPOS DA IMPORTAÇÃO
========================================================================================================================
*/

function selectCampo(selectId){
	var optIndice = document.getElementById(selectId).selectedIndex;
	var formSelects = document.getElementById('form_fav_import').elements;
	var i=1, limite=formSelects.length-2, input, optIndiceIni = $('#dados').data(selectId);
	//alert(formSelects[1].value);
	//alert(formSelects.length);
	while(i<=limite){
		input = formSelects[i];
		//alert(input[optIndice].value);
		//input[optIndice].disabled = 'true';
		if(optIndice>1){
			input[optIndice].style.display = "none";
		}
		input[optIndiceIni].style.display = "block";
		//alert(input[1].value);
		i++;
	}
}

/*
========================================================================================================================
REGISTRAR INDICE DA OPÇÃO INICIAL DO SELECT
========================================================================================================================
*/

function optionIni(selectId){
	var optIndice = document.getElementById(selectId).selectedIndex;
	$('#dados').data(selectId,optIndice);
}

/*
========================================================================================================================
RISCAR CABEÇALHO DA TABELA IMPORTADA
========================================================================================================================
*/

function removeHeader(){
	var qtd_fields = document.getElementById('tblFavImport').rows[1].cells.length;
	var ckb_status = document.getElementById('removeHeaderCkb').checked;
	var i = 0;
	if(ckb_status || ckb_status=='checked'){
		while(i<qtd_fields){
			var field = document.getElementById('tblFavImport').rows[1].cells[i];
			var content = field.innerHTML;
			field.innerHTML = '<s>'+content+'</s>';
			i++;
		}
	}else{
		while(i<qtd_fields){
			var field = document.getElementById('tblFavImport').rows[1].cells[i];
			var content = field.innerHTML;
			content = content.replace(/(<([^>]+)>)/ig,"");
			field.innerHTML = content;
			i++;
		}
	}
}

/*
===========================================================================================
RESETAR UPLOAD DE FAVORECIDO
===========================================================================================
*/

function uploadReset(){
	var uploader = $("#fav_uploader").pluploadQueue();
	$('.plupload_total_status').css('display','inline');
	$('.plupload_total_file_size').css('display','inline');
	$("#dialog-fav-import").dialog("option", "buttons", { "Cancelar": function() {$( this ).dialog( "close" );} });
	$(".plupload_buttons").css("display", "inline");
	$('.plupload_upload_status').css('display','none');
	$("#dialog-fav-import").dialog( "open" );
	$('#fav_uploader > div.plupload input').css('z-index','99999');
	document.getElementById("fav_import").style.display = 'none';
	document.getElementById("fav_uploader").style.display = 'block';
	document.getElementById("removeHeaderCkb").checked = false;
	$('#removeHeaderCkb').closest('.checker > span').removeClass('checked');
	excluirArquivos();
	$("#dialog-fav-import").dialog( "open" );
}

/*
========================================================================================================================
IMPOTAR FAVORECIDOS
========================================================================================================================
*/

function favorecidosImportar(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=favorecidosImportar";
	params += "&cliente_id="+document.getElementById("cliente_id").value;
	params += "&usuario_id="+document.getElementById("usuario_id").value;

	funcaoRetorno = function(data){
		//alert(data);
		//var dados = JSON.parse(data);
		document.getElementById("fav_import_listar").innerHTML = data;
		//dTable();
		//ativarCROT();/* Reaplicando Chackbox, Radio e Title */
		//$('.nSuccess p').html(dados.notificacao);
		//$('.nSuccess').slideDown();
		//setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$( "#dialog-fav-import" ).dialog("option", "buttons", {"Finalizar Importação":function(){favorecidosImportarFim();},"Cancelar":function(){excluirArquivos();$( "#dialog-fav-import" ).dialog("close");}});
		$("span.aguarde, div.aguarde").css("display","none");
		$("#dialog-fav-import" ).dialog( "open" );
	}
	
	ajax_jquery(params);
}

/*
========================================================================================================================
FINALIZAR IMPORTAÇÃO DE FAVORECIDOS
========================================================================================================================
*/

function favorecidosImportarFim(){
	if(selectCampoValid()){
		$("#dialog-fav-import" ).dialog( "close" );
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_fav_import').serialize();
		params += "&cliente_id="+document.getElementById("cliente_id").value;
		params += "&usuario_id="+document.getElementById("usuario_id").value;
		funcaoRetorno = function(data){
			//alert(dados_global);
			var dados = JSON.parse(data);
			$('#favorecidos').html(dados.favorecidos);
			dTable();
			$('.nSuccess p').html(dados.notificacao);
			$('.nSuccess').slideDown();
			setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
			$("span.aguarde, div.aguarde").css("display","none");
		}
		ajax_jquery(params);
	}
}

/*
========================================================================================================================
EXCLUÍR ARQUIVOS
========================================================================================================================
*/

function excluirArquivos(){
	var params = "funcao=arquivosExcluir";
	params += "&cliente_id="+document.getElementById("cliente_id").value;
	params += "&usuario_id="+document.getElementById("usuario_id").value;
	funcaoRetorno = function(data){};
	ajax_jquery(params);
}

/*
========================================================================================================================
EXCLUÍR ARQUIVOS
========================================================================================================================
*/

function favExport(){
    $("#formFavExport").submit();
/*
	var params = "funcao=favExport";
	funcaoRetorno = function(data){};
	ajax_jquery(params);
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
	
	$( ".bancos_buscar_id" ).autocomplete({
		minLength: 1,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			$.getJSON( "modulos/favorecido/paginas/contas_financeiras_banco_buscar.php", request, function( data, status, xhr ) {
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
	//======== FIM COMPLETAR PLANO DE CONTAS - CONDIGO PAI ID =============

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

	oTable = $('.tblFavorecidos').dataTable({
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

/*
========================================================================================================================
PLUPLOAD
========================================================================================================================
*/
/*
	$("#fav_uploader").pluploadQueue({
		runtimes : 'html5,html4',
		url : 'modulos/favorecido/php/upload.php',
		chunk_size: "1mb",
		//max_file_size : '1mb',
		//multiple_queues: false, //restaura os botões após terminar um upload e limpar a lista
		multi_selection: false,
		unique_names : false,
		filters : [
			{title : "Arquivos xls", extensions : "xls"}
		]
	});
	
	$('.plupload_filelist').css('height','38px');
	$('.plupload_filelist').css('overflow-y','hidden');
	$('.plupload_droptext').css('line-height','20px');

	var uploader = $("#fav_uploader").pluploadQueue();
	
	uploader.bind("BeforeUpload", function(up, file) {
		//if($('#input_conta_import').valid()){
			//$("span.aguarde, div.aguarde").css("display","block");
			$('.plupload_upload_status').css('display','inline');
			$( "#dialog-fav-import" ).dialog("option", "buttons", {});
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
	
	uploader.bind("QueueChanged", function(up, files){
		if(uploader.files.length==1){
			$("a.plupload_add").hide();
		}else{
			$("a.plupload_add").show();
		}
	});

	uploader.bind("UploadComplete", function(up, files) {
		$("#dialog-fav-import" ).dialog( "close" );
		uploader.splice();
		document.getElementById("fav_uploader").style.display = 'none';
		document.getElementById("fav_import").style.display = 'block';
		favorecidosImportar();
	});
*/	
});

/*
========================================================================================================================
DATA TABLE
========================================================================================================================
*/

$(document).ready(function () {

    //Data table orçamento
    dTable = $('#dTableFavorecidos').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'modulos/favorecidoteste/php/funcoes.php?funcao=DataTableAjax',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bInfo: false,
        //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
        aoColumns: [
            { "mData": "favorecido", "sClass": "updates newUpdate" },
            //{ "mData": "options", "sClass": "actions" },
        ],
        oLanguage: {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        },
        fnServerParams: function (aoData) {

            //var filtro = filtroParams();

            if (crud) {
                var oSettings = this.fnSettings();
                oSettings._iDisplayStart = iDisplayStart;
            } else {
                var oSettings = this.fnSettings();
                iDisplayStart = oSettings._iDisplayStart;
            }

            aoData.push(/*{ "name": "filtro", "value": filtro },*/ { "name": "iDisplayStart", "value": iDisplayStart });

        },
        fnDrawCallback: function () {
            crud = false;
            //$('#btn-filtro').button('reset');
        }
    });

    $('#dTableFavorecidos > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

})