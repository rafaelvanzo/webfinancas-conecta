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
      url: 'modulos/contador/php/funcoes.php', //url para onde será enviada as informações digitadas
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
========================================================================================================================
FORMATAR CHECKBOX
========================================================================================================================
*/

	$("input:checkbox").uniform();

/*
========================================================================================================================
INICIALIZAR DATA TABLE
========================================================================================================================
*/

//	dTable('dTableExtratoBanco');

/*
========================================================================================================================
DATA TABLE - COMPLEMENTOS
========================================================================================================================
*/

    //Data table mensagens
	dTableMsg = $('.dTableMensagens').dataTable({
	    bProcessing: true,
	    bServerSide: true,
	    sAjaxSource: 'modulos/contador/php/funcoes.php?funcao=DataTableAjax',
	    "bJQueryUI": true,
	    "bAutoWidth": false,
	    "sPaginationType": "full_numbers",
	    //bInfo: false,
	    //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
	    aoColumns: [
            { "mData": "msg", "sClass": "updates newUpdate" },
            //{ "mData": "options", "sClass": "actions" },
	    ],
	    "oLanguage": {
	        "sLengthMenu": "<span>Mostrar:</span> _MENU_",
	        "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
	    },
	   // "fnServerParams": function (aoData) {
	
	    //},
	    fnDrawCallback: function () {
	        crud = false;
	        //$('#btn-filtro').button('reset');
	    }
	});

	$('.dTableMensagens > thead').remove(); //remove o thead
    //$('#dTableLnctTeste_wrapper').children(':first').remove(); //remove o header de pesquisa
    //$('#dTableLnctTeste_filter').remove(); //remove o campo de pesquisa
    //$('#datatable-orcamento_processing').css('top', '-50px'); //posiciona o gif processando do datatable mais para cima

    //Redesenhar
	//dTableMsg.fnDraw();


/*
========================================================================================================================
DIALOGS
========================================================================================================================
*/

	//===== UI dialog - CONVITE  =====//
	
	$( "#dialog-convite-contador" ).dialog({
		autoOpen: false,
		modal: true,
		Width: '300px',
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
	
	//$( "#opener-convite-contador" ).click(function() { 
	
	$('#opener-convite-contador').live("click",function(e){ 
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
$('.excluirConvite').live("click",function(e){ 

	e.preventDefault(); 
	var cliente_id = $(this).attr('href');
	var convite_row_id = $(this).data('convite-row-id');
	
	$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Sim",
			click: function () { cancelar_conexoes(cliente_id, convite_row_id); $("#dialog-alerta").dialog("close"); }
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Confirmar exclusão do convite?");

		$('#dialog-alerta').dialog('open');
		
	});
	
//===== UI dialog - CANCELAR CONEXÃO ATIVA  =====//
$('.excluirConexao').live("click",function(e){ 

	e.preventDefault(); 
	var cliente_id = $(this).attr('href');

	$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Sim",
			click: function () { cancelar_conexoes_ativas(cliente_id); $("#dialog-alerta").dialog("close"); }
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Deseja encerrar a conexão com o contador?");

		$('#dialog-alerta').dialog('open');
		
	});	
	
//===== UI dialog - ACEITAR CONEXÃO CONTADOR E CLIENTE  =====//
$('.aceitarConvite').live("click",function(e){ 

	e.preventDefault(); 
	var cliente_id = $(this).attr('href');
	var convite_row_id = $(this).data('convite-row-id');

	$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Sim",
			click: function () { aceitar_conexoes(cliente_id, convite_row_id); $("#dialog-alerta").dialog("close"); $("#dialog-convite-contador").dialog("close"); }
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Confirmar conexão com o contador?");

		$('#dialog-alerta').dialog('open');
		
	});
	
	//===== UI dialog - CONVERSA E MENSAGEM  =====//
	
	$( "#dialog-conversa" ).dialog({
		autoOpen: false,
		modal: true,
	});
	
	$('.opener-conversa').live("click",function(e){ 
		var id = $(this).attr("href");
			if(id == 0){ 
				$('.nomeP').html('Selecione os participantes');
				$('.msgAssunto').css('display','block');
				$('#funcao').val('addConversa');	
				$('.messagesOne').html('');
			}else{
				$('.nomeP').html('Participantes');
				$('.msgAssunto').css('display','none');	
				$('#funcao').val('addMensagem');
				$('#chat_id').val(id); 
					visualizar_mensagens(id);
				}
		$( "#dialog-conversa" ).dialog( "open" ); 		
		return false;
	});	
	
    //===== UI dialog - NOVA MENSAGEM  =====//

	$("#dialog-add-mensagem").dialog({
	    autoOpen: false,
	    modal: true,
	    position: { my: "top", at: "top+5%", of: window },
	   
	});

	$('#opener-add-mensagem').live("click", function (e) {
	    formLimpar('formAddMensagem');
	    $('.1').hide();
	    $('.2').hide();
	    $('.3').hide();
	    $('.4').hide();

        //Resetar inputs buscar funcionários
	    $("#formAddMensagem input[name='form_rcbt_funcionarios_id']").val("");
	    $('#formAddMensagem span.check-green').css('display', 'none');
	    $('#formAddMensagem .input-buscar').attr('disabled', false);
	    $('#form_rcbt_recisao_funcionario_id').val(0);
	    $('#form_rcbt_funcionarios_id').val(0);


	    $("#dialog-add-mensagem").dialog("open");
	    return false;
	});

    //===== UI dialog - VISUALIZAR MENSAGEM  =====//

	$("#dialog-visualizar-mensagem").dialog({
	    autoOpen: false,
	    modal: true,
	    position: { my: "top", at: "top+5%", of: window },

	});
/*
	$('.opener-visualizar-mensagem').live("click", function (e) {

	    $("#dialog-visualizar-mensagem").dialog("open");
	    $('.chat').scrollTop($('.chat')[0].scrollHeight); //move o scroll para a ultima mensagem
	    return false;
	});
  */  

});	

/*
================================================================================================
CONEXÃO CONTADOR E CLIENTE 
================================================================================================
*/

function convite_contador(){ 
	if($('#formConviteContador').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#formConviteContador').serialize();  
		ajax_jquery(params,"retorno_convite_contador()");
		$("#dialog-convite-contador").dialog("close");
		formLimpar('formConviteContador'); 
	}
}

function retorno_convite_contador(){ //alert(dados_global);
	var dados = eval("("+dados_global+")");
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
		$('#lista_conexoes').html(dados.lista_conexoes); /* Atualiza a lista de conexões */
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
		}
}


/*
================================================================================================
REENVIAR CONVITE
================================================================================================
*/

function reenviar_convite(id_list){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=reenviarConvite&id_list="+id_list; 
	ajax_jquery(params,"retorno_reenviar_convite()"); 
}

function retorno_reenviar_convite(){ 
	var dados = eval("("+dados_global+")");
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
		$('#lista_conexoes').html(dados.lista_conexoes); /* Atualiza a lista de conexões */
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
		}
}

/*
================================================================================================
CANCELAR CONEXÃO
================================================================================================
*/

function cancelar_conexoes(cliente_id, convite_row_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=cancelarConexoes&cliente_id="+cliente_id;
	ajax_jquery(params,"retorno_cancelar_conexoes(" + convite_row_id + ")");
}

function retorno_cancelar_conexoes(convite_row_id) { //alert(dados_global);
	var dados = eval("("+dados_global+")");
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
	    $('#convite-row-' + convite_row_id).remove();
	    $('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
	}
}


/*
================================================================================================
CANCELAR CONEXÃO ATIVAS
================================================================================================
*/

function cancelar_conexoes_ativas(cliente_id) {
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=cancelarConexoesAtivas&cliente_id="+cliente_id; //alert(params);
	ajax_jquery(params, "retorno_cancelar_conexoes_ativas()");
}

function retorno_cancelar_conexoes_ativas(){ //alert(dados_global);
	var dados = eval("("+dados_global+")");
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
		$('#div-contador-conectado').remove();
		$('#div-btn-convite').css('display', 'block');
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
	}
}

/*
================================================================================================
ACEITAR CONEXÃO
================================================================================================
*/

function aceitar_conexoes(cliente_id, convite_row_id) {
    $("span.aguarde, div.aguarde").css("display", "block");
	var params = "funcao=aceitarConexoes&cliente_id="+cliente_id;
	ajax_jquery(params, "retorno_aceitar_conexoes(" + convite_row_id + ")");
}

function retorno_aceitar_conexoes(convite_row_id) {
    //alert(dados_global);
	var dados = eval("("+dados_global+")"); 
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
		$('#div-btn-convite').css('display', 'none');
		$('#conexao_contador').append(dados.contador_info);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown(); 
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000); 
	}
	$('#convite-row-' + convite_row_id).remove();

}

/*
================================================================================================
ADD CONVERSA
================================================================================================
*/

//Adiciona o assunto da conversa no title do modal
$('.msgAssunto').keyup(function( event ){
	var assunto = $('.txtAssunto').val();
	$('.ui-dialog-title').html(assunto);
});

function add_conversa(){
	var params = $('#formAddConversa').serialize(); 
	ajax_jquery(params,"retorno_add_conversa()");
}

function retorno_add_conversa(){ 
		var dados = eval("("+dados_global+")"); 
	$('#new').css('display','block');
	$('#new').html(dados.atualizarConversa);  //Atualiza as mensagens	
	$('#new').after('<li id="new" class="by_me" style="display:none;"></li>');
	$('#new').attr('id','');
	$('.nomeP').html('Participantes');
	$('.msgAssunto').css('display','none');
	$('#chat_id').val(dados.chat_id);	
		var novo = $('#funcao').val();	
		if(novo == "addConversa"){ $('#funcao').val('addMensagem'); } 
		if(novo == "addMensagem"){  visualizar_mensagens($('#chat_id').val()); }
}

/*
================================================================================================
VISUALIZAR MENSAGENS
================================================================================================
*/

function visualizar_mensagens(chat_id){ //alert(chat_id);
	var params = "funcao=visualizarMensagens&chat_id="+chat_id; 
	ajax_jquery(params,"retorno_visualizar_mensagens()"); 
}

function retorno_visualizar_mensagens(){ //alert(dados_global);
		var dados = eval("("+dados_global+")"); 
	$('#new').css('display','block');
	$('.messagesOne').html(dados.atualizarConversa);  //Atualiza as mensagens	
	$('#new').after('<li id="new" class="by_me" style="display:none;"></li>');
	$('#new').attr('id','');
	$('.nomeP').html('Participantes');
	$('.msgAssunto').css('display','none');
	$('#chat_id').val(dados.chat_id);	
		var novo = $('#funcao').val();	
		if(novo == "addConversa"){ $('#funcao').val('addMensagem'); } 

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
	}else if(tp_lnct==='P'){
		var checkedStatus = $("#tpLnctCk01").attr("checked");
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
	}else{
		var checkedStatus = $("#tpLnctCk02").attr("checked");
		if(!checkedStatus){
			checkedStatus = false;
		}
		$('.lnctCheckbox div.checker span input:checkbox.E').each(function() {
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
ENVIAR REMESSA CONTÁBIL
===========================================================================================
*/

function RemessaValidarConta(){

	//contas financeiras
	var array_contas_id = new Array();
	$('#contas-financeiras div.checker span.checked input[type="checkbox"]').each(function() {
		array_contas_id.push($(this).val());
	});
	var flt_contas = array_contas_id.join(',');

	if( flt_contas!='' ){
		RemessaValidarData();
	}else{
		//Confirmar envio da remessa
		$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Fechar",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);
		$('#dialog-alerta').html("<br/> Nenhuma conta financeira foi selecionada");
		$('#dialog-alerta').dialog('open');
	}

}

function RemessaValidarData(){

	//Mês e ano atuais
	var data_atual = new Date();
	var mes_atual = parseInt(data_atual.getMonth())+1; //meses de 0 a 11
	var ano_atual = parseInt(data_atual.getFullYear());
	
	//Mês
	var data = $('#mes').val();
	data = data.split('/');
	var mes = parseInt(data[0]);
    var ano = parseInt(data[1]);

    //if (ano <= ano_atual && mes <= mes_atual){

    if (new Date(ano, mes - 1) <= new Date(ano_atual, mes_atual - 1)) {

        RemessaContabilConfirmar();

    } else {
		//Confirmar envio da remessa
		$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Fechar",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);
		$('#dialog-alerta').html("<br/> O período deve ser menor ou igual ao mês atual");
		$('#dialog-alerta').dialog('open');
	}

}

function RemessaContabilConfirmar(){
	//Confirmar envio da remessa
	$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
	{
		text: "Sim",
		click: function() { RemessaContabil();  $("#dialog-alerta").dialog("close");}
	},
	{
		text: "Não",
		click: function() { $("#dialog-alerta").dialog("close"); }
	}		
	]);
	$('#dialog-alerta').html("<br/> Confirmar envio da remessa?");
	$('#dialog-alerta').dialog('open');
}

function RemessaContabil(){

	$("span.aguarde, div.aguarde").css("display","block");
	
	//Mês
	var data = $('#mes').val();
	data = data.split('/');
	var mes = parseInt(data[0]);
	var ano = parseInt(data[1]);

	//contas financeiras
	var array_contas_id = new Array();
	$('#contas-financeiras div.checker span.checked input[type="checkbox"]').each(function() {
		array_contas_id.push($(this).val());
	});
	var flt_contas = array_contas_id.join(',');

	var params = 'funcao=RemessaContabil';
	params += '&mes='+parseInt(data[0]);
	params += '&ano='+parseInt(data[1]);
	params += '&cf='+flt_contas;
	//$("#dialog-rcbt").dialog("close");
	$.ajax({
		type: 'post',
		url: 'modulos/contador/php/funcoes.php',
		data: params,
		dataType: 'json',
		cache: true,
		success: function(data){
			if(data.situacao==1){
				$('#div-contas-financeiras').html(data.contas);
				//dTable();
				$("input:checkbox").uniform();
				$('.nSuccess p').html(data.notificacao);
				$('.nSuccess').slideDown();
				setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
			}else{
				$('.nWarning p').html(data.notificacao);
				$('.nWarning').slideDown();
				setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
			}
			$("span.aguarde, div.aguarde").css("display","none");
		},
		error: function(erro){
		}
	})

}

/*
===========================================================================================
PESQUISAR REMESSA CONTÁBIL
===========================================================================================
*/

function RemessaPesquisar(){
	$("span.aguarde, div.aguarde").css("display","block");
	var data = $('#mes').val();
	data = data.split('/');
	var params = 'funcao=RemessaPesquisar';
	params += '&mes='+parseInt(data[0]);
	params += '&ano='+data[1];
	//$("#dialog-rcbt").dialog("close");
	$.ajax({
		type: 'post',
		url: 'modulos/contador/php/funcoes.php',
		data: params,
		dataType: 'json',
		cache: true,
		success: function(data){
			$('#div-contas-financeiras').html(data.contas);
			$("input:checkbox").uniform();
			$("span.aguarde, div.aguarde").css("display","none");
		},
		error: function(erro){
		}
	})
}

/*
===========================================================================================
HISTÓRICO DA REMESSA CONTÁBIL
===========================================================================================
*/

function RemessaHistorico(){

	var data = $('#mes').val();
	data = data.split('/');
	var mes = parseInt(data[0]);
	var ano = data[1];

	$("#historicoMes").val(mes);
	$("#historicoAno").val(ano);

	$("#formHistorico").submit();

}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function formLimpar(form) {
    var validator = $('#' + form).validate();
    validator.resetForm();
}

/*
===========================================================================================
DIALOG ADD MENSAGENS - ALTERAR OPÇÕES
===========================================================================================
*/
$('.solicitacao').change(function() {
    var numero = $(this).val();

    if(numero == 1){
        $('.2').css('display','none');
        $('.3').css('display','none');
        $('.4').css('display','none');
    }else if(numero == 2){
        $('.1').css('display','none');
        $('.3').css('display','none');
        $('.4').css('display','none');
    }else if(numero == 3){
        $('.1').css('display','none');
        $('.2').css('display','none');
        $('.4').css('display','none');
    }else if (numero == 4){
        $('.1').css('display','none');
        $('.2').css('display','none');
        $('.3').css('display','none');
    } else {
        $('.1').css('display', 'none');
        $('.2').css('display', 'none');
        $('.3').css('display', 'none');
        $('.4').css('display', 'none');
    }

    if (numero != '') { $('.' + numero).fadeIn(); }
});

$('.recalculo').change(function () { 

    $('.tpGPS').val('');

    var nome = $(this).val(); 

    if (nome ==  5) { //GPS
        $('.GPS').fadeIn(); 
    } else {
        $('.GPS').css('display', 'none');
    }
});

/*
===========================================================================================
CHAT CLIENTE
===========================================================================================
*/
//Cria uma nova conversa
function mensagem_add() {    
    if ($('#formAddMensagem').valid()) {       
    $("span.aguarde, div.aguarde").css("display", "block");
    //Ajax
    var params = $('#formAddMensagem').serialize(); //alert(params);
        ajax_jquery(params, "retorno_mensagem_add()");
        $('#dialog-add-mensagem').dialog("close");
    }     
        //Evita o post.
        event.preventDefault();
}

function retorno_mensagem_add() {

    var dados = eval("(" + dados_global + ")"); //alert(dados.situacao);

    if(dados.situacao == 1){
        $('.nSuccess p').html(dados.notificacao);		
        $('.nSuccess').slideDown();
        setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
        dTableMsg.fnDraw();
    } else if (dados.situacao == 0) {
        $('.nWarning p').html(dados.notificacao);
        $('.nWarning').slideDown();
        setTimeout(function () { $('.nWarning').slideUp() }, 5000);
    }
    $("span.aguarde, div.aguarde").css("display", "none");

}

//Abri uma conversa já existente
function abrirMensagem(chat_categoria_id) {
        
    $("span.aguarde, div.aguarde").css("display", "block");
    $(".chat_categoria_id").val(chat_categoria_id);

        var params = "funcao=abrirMensagem&chat_categoria_id=" + chat_categoria_id; //alert(params);

        $.ajax({
            type: 'get',
            url: 'modulos/contador/php/funcoes.php',
            data: params,
            dataType: 'json',
            cache: true,
            success: function (dados) {
                //alert(dados.dt_conclusao);


                if (dados.situacaoChat == 1) {
                    $('.bt_textarea_msg').css('display', 'block');
                    $('.textoMsg').prop("disabled", false);
                    $('.ui-dialog-buttonpane').css('display', 'block');
                    $("#dialog-visualizar-mensagem").dialog("option", "title", dados.titulo);
                } else {
                    $('.bt_textarea_msg').css('display', 'none');
                    $('.textoMsg').prop("disabled", true);
                    $('.ui-dialog-buttonpane').css('display', 'none');
                    $("#dialog-visualizar-mensagem").dialog("option", "title", dados.titulo + "( Concluído em " + dados.dt_conclusao + " )");
                }

                //$("#dialog-visualizar-mensagem").dialog("option", "title", dados.titulo);
                $('ul#chatMsg').html(dados.msg);
                $('#detalhes-solicitacao').html(dados.detalhes_solicitacao);

                /*
                $("#dialog-visualizar-mensagem").dialog("option", "title",  dados.titulo );
                $('ul#chatMsg').html(dados.msg);
                $('#detalhes-solicitacao').html(dados.detalhes_solicitacao);
                */
                if (dados.abas == 1) {
                    $('#abas-dialog-mensagens').css('display', 'block');
                    $('.aba1').addClass('active');
                    $('.aba2').removeClass('active');
                } else {
                    $('#abas-dialog-mensagens').css('display', 'none');
                    $('.aba1').addClass('active');
                    $('.aba2').removeClass('active');
                }

                $('#dialog-visualizar-mensagem').dialog("open");
                $("span.aguarde, div.aguarde").css("display", "none");                

                $('#chatMsg').scrollTop($('#chatMsg')[0].scrollHeight); //move o scroll para a ultima mensagem 

                timerMensagem();

                dTableMsg.fnDraw();

            },
            error: function (erro) {
                alert(erro);
                $("span.aguarde, div.aguarde").css("display", "none");
        }
        })
}

//Atualiza as mensagens
function atualizarMensagem() {

    var chat_categoria_id = $('.chat_categoria_id').val();

    var params = 'chat_categoria_id=' + chat_categoria_id; //alert(chat_categoria_id);

    $.ajax({
        type: 'post',
        url: 'modulos/contador/php/funcoes.php?funcao=visualizarMensagem',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {            

            if (dados.novaMensagem === 1) {

                /* ========= Calucula a altura do scroll e compara com o tamanho do ul.#chatMsg ========= */
                var baseScroll = $('ul#chatMsg')[0].scrollHeight;
                var tamanhoScroll = baseScroll - 302; //302 é o tamanho da div no css que contém o SCROLL.

                var altruaScroll = $('ul#chatMsg').scrollTop(); //Altura do SCROLL
                /* ====== Fim Calucula a altura do scroll e compara com o tamanho do ul.#chatMsg ====== */

                //Inseri as mensagens na conversa
                $('ul#chatMsg').append(dados.msg);
                
                //alert(altruaScroll +' = '+tamanhoScroll);
                if (altruaScroll == tamanhoScroll) {

                    $('ul#chatMsg').scrollTop($('ul#chatMsg')[0].scrollHeight); //move o scroll para a ultima mensagem 

                } else {

                        $('.alertaNovaMsg').css('display', 'block');
                    $(".alertaNovaMsg").fadeOut(5000, function () {
                        $('.alertaNovaMsg').css('display', 'none');
                    });
                    
                }
                

                //console.log(JSON.stringify(dados));
            }

        },
        error: function (error) {
            //alert(error);
        }
    })

}

//Envia mensagem a uma conversa já iniciada
function enviarMensagem() {

    var texto = $('.textoMsg').val().length;

    if (texto == 0) {
        alert("Preencha a mensagem antes de enviar.");
    } else {

    var chat_categoria_id = $(".chat_categoria_id").val(); //alert(chat_categoria_id);

    var params = $('#formVisualizarMensagem').serialize(); 

    $.ajax({
        type: 'post',
        url: 'modulos/contador/php/funcoes.php?funcao=enviarMensagem',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (dados) {
            //alert(dados.msg);

            $('ul#chatMsg').append(dados.msg);
            $('.textoMsg').val('');               

            $('#chatMsg').scrollTop($('#chatMsg')[0].scrollHeight); //move o scroll para a ultima mensagem 

        },
        error: function (erro) {
            //alert(erro);
        }
    })

    }// else
    event.preventDefault();
    
}

//Temporizador que chama a função para verifica se existe mensagens novas
function timerMensagem() {

    timer = setInterval(function () { //setInterval
        atualizarMensagem(); //alert("Verificando..");
    }, 10000);
}


//Temporizador que chama a função para verifica se existe mensagens novas
function timerListaMensagem() {

    timer = setInterval(function () { //setInterval
        dTableMsg.fnDraw(); //alert("Verificando..");
    }, 180000);
}
/* =================== Botão e ESC janela finalizar temporizador de atualização ======================= */

// Ao pressionar o Esc ele para o timer do chat           
$(document).keyup(function (e) {
    if (e.keyCode == 27) {
        clearInterval(timer); 
    }
});

// Ao pressionar x do dialog para finalizar o timer do chat   
$('#dialog-visualizar-mensagem').on('dialogclose', function (event) {
    clearInterval(timer);    
});

//Botão no alerta que leva o scroll para visualizar mensagens novas
$('.alertaNovaMsg').click(function () {
    $('ul#chatMsg').scrollTop($('ul#chatMsg')[0].scrollHeight);
});
/*
//Scroll carrega mais conteúdo
$(document).ready(function () {

    $("#chatMsg").scroll(function () {
        if ($(this).scrollTop() == 0) {
            //requisição ajax
       
        }
    });
});
*/
/* =============================================== */
    
//===== Auto Complete - Funcionarios  =====//

//var cache = {};

$(".funcionarios_buscar").autocomplete({
    minLength: 0,
    source: function( request, response ) {
        //var term = request.term;
        //if ( term in cache ) {
        //response( cache[ term ] );
        //return;
        //}
        $.getJSON( "php/funcionarios_buscar.php", request, function( data, status, xhr ) {
            //cache[ term ] = data;
            response( data );
        });
    },
    search: function( event, ui ) {
        var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
        $('#'+campo_id+'_aguarde').css('display','block');
    },
    response: function( event, ui ) {
        var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
        $('#'+campo_id+'_aguarde').css('display','none');
        //if(ui.content.length==0){
        //alert('nenhum resultado encontrado');
        //}
        //alert('resposta');
    },
    select: function( event, ui ) {
        var campo_id = $(this).attr('name');
        $('#'+campo_id).val(ui.item.id);
        if(ui.item.id=="add")
            favorecidosIncluirAc(ui.item.value,campo_id);
        else
            if(ui.item.plc_id!=0 || ui.item.ctr_id!=0){
                var tp_lnct = $(this).data('tp-lnct');
                var form_id = $(this).data('form-id');
                if(tp_lnct=='R')
                    favorecidoCtrPlc(ui.item.cliente_ctr_id,ui.item.cliente_plc_id,form_id);
                else
                    favorecidoCtrPlc(ui.item.fornecedor_ctr_id,ui.item.fornecedor_plc_id,form_id);
            }
        $('#'+campo_id+'_cg').css('display','block');
        $(this).attr('disabled','disabled');
        fadeOut($(this).attr('id'));
    }
});
	
$(".funcionarios_buscar").click(function () {
    var campo_id = $(this).attr('id');
    $("#" + campo_id).autocomplete("search");
});
//===== End Auto Complete - Favorecidos  =====//