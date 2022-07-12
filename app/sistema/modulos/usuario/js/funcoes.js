// JavaScript Document
var dados_global;

function ajax_jquery_login(params,funcao_retorno){

    $.ajax({
		   
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/usuario/php/funcoes.php', //url para onde será enviada as informações digitadas
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
      error: function(xhr, status, error){
		  alert(xhr.responseText);
      }
	  
    })

}

/*
===========================================================================================
LIMPAR FORMULÁRIO
===========================================================================================
*/

function formLimpar(form){

	var validator = $('#'+form).validate();
	validator.resetForm();
}

/*
================================================================================================
LOGIN
================================================================================================
*/

$(document).ready(function(){
	$('#formLogin input:text,#formLogin input:password').live("keypress", function(e) {
		if (e.keyCode == 13) {
			login();
		}
	});
})

function login(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = $('#formLogin').serialize();
	ajax_jquery_login(params,"login_retorno()");
	//alert(params);
}

function login_retorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	if(dados.situacao == 1){
		location.href='geral';
	}else{
		//alert(dados.notificacao);
		$("span.aguarde, div.aguarde").css("display","none");
		$( "#dialog-alerta" ).dialog( "option", "buttons", [{text: "OK",click: function() { $("#dialog-alerta").dialog("close"); }}]);
		$('#dialog-alerta').html("<br/> <img src='images/icons/notifications/error.png' style='float: left; padding-: 5px;'/> &nbsp;"+dados.notificacao);
		$('#dialog-alerta').dialog('open');
	}
}


/*
================================================================================================
LOGOFF
================================================================================================
*/

$(document).ready(function(){

	$('.sair').live("click",function(e){ 

		e.preventDefault(); 
			
		$( "#dialog-alerta" ).dialog( "option", "buttons", [ 
		{
			text: "Sim",
			click: function() { logoff(); $("#dialog-alerta").dialog("close");}
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close"); }
		}		
		]);

		$('#dialog-alerta').html("<br/> Deseja realmente sair do sistema?");

		$('#dialog-alerta').dialog('open');

	});
	
});


function logoff(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=logoff";
	ajax_jquery_login(params,"logoff_retorno()");
}

function logoff_retorno(){
    //$("span.aguarde, div.aguarde").css("display","none");
    var caminho = $('.sair').attr('data-sair'); 
    if (caminho != '') {
        location.href = caminho;
    } else {
	    location.href='https://www.webfinancas.com';
    }
}


/*
================================================================================================
SENHA
================================================================================================
*/

//Recuperar senha

function senhaRecuperar(){
	var params = "funcao=senhaRecuperar&email="+$('#email').val();
	ajax_jquery_login(params,"senhaRecuperarRetorno()");
	$("span.aguarde, div.aguarde").css("display","block");
}

function senhaRecuperarRetorno(){
	$("span.aguarde, div.aguarde").css("display","none");
	alert(dados_global);
	var dados = eval("("+dados_global+")");
	$( "#dialog-alerta" ).dialog( "option", "buttons", [{text: "OK",click: function() { $("#dialog-alerta").dialog("close"); }}]);
	$('#dialog-alerta').html(dados.notificacao);
	$('#dialog-alerta').dialog('open');
}

//Alterar senha
/*
$(document).ready(function(){

	$('#dialog_senha').dialog({
			autoOpen: false,
			width: 400,
	});
	
	//Senha
	$( "#dialog_senha" ).dialog( "option", "buttons", [
	{
		text: "Salvar",
		click: function() { 
			senha_alterar();
			//$("#dialog_senha").dialog("close"); //está dentro da função senha_alterar para não dar problema
		}
	},

	{
		text: "Cancelar",
		click: function() { 
			$("#dialog_senha").dialog("close");
		}
	}		
	
	]);
	
	$('.dialog_senha').live("click",function(e){
		e.preventDefault();
		//alert($(this).attr('href'));
		$('#dialog_senha').dialog('open');
	});	

})
/*
function senha_alterar(){
	if($('#form_senha').valid()){
		var params = $('#form_senha').serialize();
		ajax_jquery_login(params,"senha_alterar_retorno()");
		$("#dialog_senha").dialog("close");
	}
}

function senha_alterar_retorno(){
	var dados = eval("("+dados_global+")");
	if(dados.situacao == 1){
		$('.nFailure .msg_texto').html(dados.notificacao);
		$('.nFailure').slideDown();
		setTimeout(function(){ $('.nFailure').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nSuccess .msg_texto').html(dados.notificacao);		
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
		var validator = $('#form_senha').validate();
		validator.resetForm();
	}	
}
*/

/*
================================================================================================
ALTERAR SENHA
================================================================================================
*/
// Validação
$(document).ready(function(){
	
	//===== UI dialog - Alterar Senha =====//	
	$( "#dialog-alterar-senha" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		buttons: {	
			Salvar: function() {
				senha_alterar();
			},
			Cancelar:	function() {
			$( this ).dialog( "close" );
			formLimpar('formAlterarSenha');
			}
		}
	});
	
	$( "#opener-alterar-senha" ).click(function() {
		$( "#dialog-alterar-senha" ).dialog( "open" );
		return false;
	});	
	
$('#formAlterarSenha').validate({
		rules: {
    senha: {
			required: true,
			minlength: 6,
			maxlength: 12
		},
    repetir_senha: {
      equalTo: "#senha"
    	}
		},
		messages: {
			senha: {
				minlength: "*A senha deve conter no mínimo 6 digitos.",
				maxlength: "*A senha deve conter no maxímo 12 digitos.",
			},
			repetir_senha: {
				equalTo: "*Por favor confirme a nova senha.",
			}
		}
	});	
});

function senha_alterar(){ 
	if($('#formAlterarSenha').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#formAlterarSenha').serialize();
		ajax_jquery_login(params,"senha_alterar_retorno()");
		$("#dialog-alterar-senha").dialog("close");
		formLimpar('formAlterarSenha');
	}
}

function senha_alterar_retorno(){
	var dados = eval("("+dados_global+")");
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
}


/*
================================================================================================
CANCELAR SISTEMA
================================================================================================
*/
// Validação
$(document).ready(function(){
	
	//===== UI dialog - Alterar Senha =====//	
	$( "#dialog-cancelar" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		buttons: {	
			Confirmar: function() {
				senha_alterar();
			},
			Cancelar:	function() {
			$( this ).dialog( "close" );
			formLimpar('formCancelar');
			}
		}
	});
	
	$( "#opener-cancelar" ).click(function() {
		$( "#dialog-cancelar" ).dialog( "open" );
		return false;
	});	

});

/*
===========================================================================================
CONVERTER TEXTO PARA VALOR
===========================================================================================
*/

function txtToValor(valor){
	var txt = valor;
	txt = txt.replace(/\./g, '');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
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


/*
================================================================================================
RESETAR DIV PARA O ESTADO ORIGININAL
================================================================================================
*/

//Grava o html ao carregar a página
function guardarReset(){

	var modulos = [];
	var checked = '';
	var modulo_id = '';
	var tp_plano = $('#tp_plano').val();
	var vencimento = $('#vencimento').val();

	$('div.updates input[type="checkbox"]').each(function(index, element) {
		checked = $(this).attr('checked');
		modulo_id = $(this).attr('id');
		modulos.push({'modulo_id':modulo_id,'checked':checked});
	});
	
	modulos = JSON.stringify(modulos);

	//alert(modulos);

	$('#dialog-editar-plano').data('modulos-old-state', modulos);
	$('#dialog-editar-plano').data('plano-old-state', tp_plano);
	$('#dialog-editar-plano').data('vencimento-old-state', vencimento);

}

function resetarDiv(){
	//Reseta o estado do html
	//alert($('#resetDiv').data('old-state'));
	var modulo_id = '';
	var checked = '';
	var modulos = JSON.parse($('#dialog-editar-plano').data('modulos-old-state'));
	var total_modulos = modulos.length;

	$('#tp_plano').val($('#dialog-editar-plano').data('plano-old-state'));
	$('#vencimento').val($('#dialog-editar-plano').data('vencimento-old-state'));

	for(var i=0;i<total_modulos;i++){
		modulo_id = modulos[i].modulo_id;
		checked = modulos[i].checked;
		if(checked=='checked'){
			$('#'+modulo_id).attr('checked',true);
			$('#'+modulo_id).closest('.checker > span').attr('class','checked');
		}else{
			$('#'+modulo_id).attr('checked',false);
			$('#'+modulo_id).closest('.checker > span').removeClass('checked');
		}
	}
}

/*
================================================================================================
PERFIL DO USUÁRIO
================================================================================================
*/
$(document).ready(function(){
	
	guardarReset();
	
	//===== UI dialog - Editar Usuário =====//	
	$( "#dialog-editar-plano" ).dialog({
		autoOpen: false,
		modal: true,
		width: 320,
		buttons: {	
			'Confirmar alteração': function() {
				$( this ).dialog( "close" );
				guardarReset();
				plano_editar();
			},
			Cancelar:	function() { 
				$( this ).dialog( "close" );
				resetarDiv();
			}			
		}
	});
	
	$( "#opener-editar-plano" ).click(function() { 
		resetarDiv(); // Guarda a váriavel que se houver necessidade de resetar o formulário
		trocar_periodo();
		$( "#dialog-editar-plano" ).dialog( "open" );
		return false;
	});	
});

/*
================================================================================================
USUÁRIO EDITAR
================================================================================================
*/
function usuario_editar(){ 
	if($('#form_usuario_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_usuario_editar').serialize(); 
		ajax_jquery_login(params,"usuario_editar_retorno()");
	}
}

function usuario_editar_retorno(){ 
	var dados = eval("("+dados_global+")"); 
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
}

/*
===========================================================================================
ALTERNAR ENTRE MASCARA CPF E CNPJ
===========================================================================================
*/

function cpfCnpj(tipo){

	var id = $("#"+tipo).val();

	if(id == "CNPJ"){
		var add_cpf_cnpj = "maskCnpj";
		var remove_cpf_cnpj = "maskCpf";
	}else{
		var add_cpf_cnpj = "maskCpf";
		var remove_cpf_cnpj = "maskCnpj";
	}
	
	$('.cpf_cnpj').removeClass(remove_cpf_cnpj);
	$('.cpf_cnpj').addClass(add_cpf_cnpj);	

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
===========================================================================================
PLANO EDITAR - TROCAR VISUALIZAÇÃO DO PERÍODO/VALORES
===========================================================================================
*/

function trocar_periodo(){
	var tp_plano_atual = $('#tp_plano_atual').val();
	var tp_plano = $('#tp_plano').val();
		
	$('.'+tp_plano_atual).css('display','none');
	$('.'+tp_plano).css('display','block');
	
	$('#tp_plano_atual').val(tp_plano);
	
		// Pega valor do plano básico
		var valorPlano = $('#plvalor'+tp_plano).val();	
				valorPlano = valorPlano * 1; 
	
		// buscar valores módulos
		var i = 1;
		var numTotal = $('#numTotal').val();
		var soma = 0;
		var vl_modulo = 0;
		var m = "";
	//alert($('div.updates input[type="checkbox"]:checked').val());

	$('div.updates input[type="checkbox"]:checked').each(function(index, element) {
		m = $(this).val();
		vl_modulo = $('#'+m+tp_plano).val()*1;
    soma += vl_modulo;
  });
/*
	while(i <= numTotal){
		
		var m = $('#m'+i+tp_plano).val();
		m = m * 1;
		soma += m;
    
		i++; // Increment i
	}
*/	
	// Soma o valor do Plano 
	soma += valorPlano;
	var somaTotal = number_format(soma,2,',','.');
	
	// Período do plano
	if(tp_plano == '1'){ tp_plano = " / mês"; }else if(tp_plano == '2'){ tp_plano = " / trimestre"; }else if(tp_plano == '3'){ tp_plano = " / semestre"; }else{ tp_plano = " / ano"; }
	
	somaTotal = 'R$ '+somaTotal+tp_plano;
	$('.valorTotal').html(somaTotal);
}

/*
================================================================================================
PLANO EDITAR 
================================================================================================
*/

function plano_editar(){ 
	if($('#form_plano_editar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");

		//agrupa todos os módulos
		var modulos = [];
		var operacao = 0;
		var modulo_id = 0;
		var tp_plano = $('#tp_plano').val();
		var m = '';
		
		$('div.updates input[type="checkbox"]').each(function(index, element) {
			if($(this).is(':checked')){
				operacao = 1;
			}else{
				operacao = 0;
					//Esconde o módulo que não é mais comercializado
					var plInativoID = $(this).attr('id');
					$('.plInativo'+plInativoID).hide();
			}
			modulo_id = $(this).attr('id');
			m = $(this).val();
			vl_modulo = $('#'+m+tp_plano).val();
			modulos.push({'modulo_id':modulo_id,'vl_modulo':vl_modulo,'operacao':operacao});
		});

		modulos = JSON.stringify(modulos);
		$('#modulos').val(modulos);
		//alert(modulos);
		//fim agrupamento módulos

		var params = $('#form_plano_editar').serialize();
		ajax_jquery_login(params,"plano_editar_retorno()"); 
		//alert(params);
	}
}

function plano_editar_retorno(){  
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	$("span.aguarde, div.aguarde").css("display","none");
	if(dados.situacao == 1){
		$('.nSuccess p').html(dados.notificacao);		
		$('.nSuccess').slideDown();
			
			$('#tpPlano').html(dados.tpPlano);
			$('#diaVencimento').html(dados.diaVencimento);
			$('#produtos').html(dados.produtos);
			$('#vlTotal').html(dados.vlTotal);			
	
		setTimeout(function(){ $('.nSuccess').slideUp() }, 5000);
	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
}

/*
================================================================================================
CONTRATAR WEB FINANÇAS
================================================================================================
*/
// Passo 1
//Contratação dados
function contratarWF(){ 
	if($('#w2first').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#w2first').serialize();
		ajax_jquery_login(params,"contratarWF_retorno()");
	}
}

function contratarWF_retorno(){ 
	//var dados = eval("("+dados_global+")"); 
	$("span.aguarde, div.aguarde").css("display","none");
	//if(dados.situacao == 1){		
		contratarWFplano();
		
/*	}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
		}*/
}

// Passo 2
// Contratação Plano
function contratarWFplano(){ 
	if($('#w2confirmation').valid()){
		$("span.aguarde, div.aguarde").css("display","block");

		//agrupa todos os módulos
		var modulos = [];
		var operacao = 0;
		var modulo_id = 0;
		var tp_plano = $('#tp_plano').val();
		var m = '';
		
		$('div.updates input[type="checkbox"]').each(function(index, element) {
			if($(this).is(':checked')){
				operacao = 1;
			}else{
				operacao = 0;
					//Esconde o módulo que não é mais comercializado
					var plInativoID = $(this).attr('id');
					$('.plInativo'+plInativoID).hide();
			}
			modulo_id = $(this).attr('id');
			m = $(this).val();
			vl_modulo = $('#'+m+tp_plano).val();
			modulos.push({'modulo_id':modulo_id,'vl_modulo':vl_modulo,'operacao':operacao});
		});

		modulos = JSON.stringify(modulos);
		$('#modulos').val(modulos);
		//alert(modulos);
		//fim agrupamento módulos

		var params = $('#w2confirmation').serialize();
		ajax_jquery_login(params,"contratarWFplano_retorno()");
		//alert(params);
	}
}

function contratarWFplano_retorno(){  
	//alert(dados_global);
	//var dados = eval("("+dados_global+")");
	$("span.aguarde, div.aguarde").css("display","none");
	//if(dados.situacao == 1){ 
		$( "#dialog-contratar" ).dialog( "open" );
		//Remover o "x" do dialog
		$( ".ui-dialog-titlebar-close").css('display','none');
		$( ".ui-icon-closethick").css('display','none');
		
	/*}else if(dados.situacao == 2){
		$('.nWarning p').html(dados.notificacao);		
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}*/
}

//gerar boleto para faturar contratação
function boleto(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=planoContratar";
	params += "&tp_plano="+$("#tp_plano").val();
	params += "&vencimento="+$("#vencimento").val();
	ajax_jquery_login(params,"boletoRetorno()");
}

//gerar boleto para fatura em atraso
function faturaAtrasada(){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=faturaAtrasada";
	ajax_jquery_login(params,"boletoRetorno()");
}

function boletoRetorno(){
	//alert(dados_global);
	var dados = eval("("+dados_global+")");
	var boleto_link = window.open("https://app.webfinancas.com/sistema/modulos/boleto/boletos_imprimir.php?k="+dados.chave,'_self');
	$("span.aguarde, div.aguarde").css("display","none");
  boleto_link.focus();
}

//===== UI dialog - Finalizar Contratação =====//	
$(document).ready(function(){
	
	$( "#dialog-contratar" ).dialog({
		autoOpen: false,
		modal: true,
		width: 320,
		closeOnEscape: false,
		/*buttons: {	
			'Retornar para o sistema': function() {
				$( this ).dialog( "close" );
				location.href='perfilUsuario';
			}		
		}*/
	});	
	
	function abrirDialog() { 
		$( "#dialog-contratar" ).dialog( "open" );
		return false;
	}

});

/*
================================================================================================
BAIXAR FATURA NO PERFIL DO CLIENTE
================================================================================================
*/

function boletoPerfilUsuario(url){
	var boleto_link = window.open(url,'_self');
  boleto_link.focus();
}

//CANCELAR CONTRATAÇÃO
//========================================================================================================================

function CancelarContratacao() {

    $.ajax({

        type: 'post', //Tipo do envio das informações GET ou POST
        url: 'modulos/usuario/php/funcoes.php', //url para onde será enviada as informações digitadas
        data: {
            funcao: 'CancelarContratacao'
        }, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
        dataType: 'json',
        beforeSend: function () {
            //Ação que será executada após o envio, no caso, chamei um gif loading para dar a impressão de garregamento na página
        },

        //function(data) vide item 4 em $.get $.post
        success: function (data) {
            location.href = data.urlCancelar;
        },

        // Se acontecer algum erro é executada essa função
        error: function (xhr, status, error) {
            alert(xhr.responseText);
        }

    })

}



/*
========================================================================================================================
PLUPLOAD LOGO RECIBO
========================================================================================================================
*/
function logoRecibo(arquivo, cliente_id) {	console.log('Recibo');

    $.ajax({

        type: 'post', //Tipo do envio das informações GET ou POST
        url: '/sistema/modulos/usuario/php/funcoes.php?funcao=logoRecibo&arquivo=' + arquivo + '&cliente_id=' + cliente_id, //url para onde será enviada as informações digitadas
        dataType: 'json',
        beforeSend: function () {
            //Ação que será executada após o envio, no caso, chamei um gif loading para dar a impressão de garregamento na página
        },
        //function(data) vide item 4 em $.get $.post
        success: function (data) {
            $('.img').attr('src', arquivo);
            $('.nSuccess p').html('Imagem salva com sucesso');
            $('.nSuccess').slideDown();
            setTimeout(function () { $('.nSuccess').slideUp() }, 5000);
        },

        // Se acontecer algum erro é executada essa função
        error: function (xhr, status, error) {
            alert(xhr.responseText);
        }

    })

}

$(document).ready(function () {

    //DATA TABLE
    //========================================================================================================================

    oTable = $('.dTableFaturas').dataTable({
        "bJQueryUI": true,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "sDom": '<"itemsPerPage"fl>t<"F"ip>',
        //"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
        "aaSorting": [[0, "desc"]], //inicializa a tabela ordenada pela coluna especificada
        'aoColumnDefs': [
			{ "bVisible": false, "aTargets": [0] } //torna uma coluna invisivel
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

    //CHECKBOX BOOTSTRAP CONTADOR
    //========================================================================================================================

    $(".ckb-contador").bootstrapSwitch({
        //'state': false,
        'size': 'mini',
        'onText': 'Sim',
        'offText': 'Não',
        'inverse': true,
        'onColor': 'success',
        'offColor': 'warning',
        'labelWidth': 1,
        'onSwitchChange': function (event, state) {
            $("span.aguarde, div.aguarde").css("display", "block");
            var params = 'funcao=ContadorHabilitar';
            var habilitar = 0;
            if ($('.ckb-contador').is(':checked'))
                habilitar = 1;
            params += '&habilitar=' + habilitar;
            $.ajax({
                type: 'post',
                url: 'modulos/usuario/php/funcoes.php',
                data: params,
                //cache: true,
                //beforeSend: function () {
                //},
                success: function (data) {
                    $("span.aguarde, div.aguarde").css("display", "none");
                    if (habilitar == 1)
                        $('#li-area-contador').css('display', 'block');
                    else
                        $('#li-area-contador').css('display', 'none');
                    var _data = JSON.parse(data);
                    $('.nSuccess p').html(_data.notificacao);
                    $('.nSuccess').slideDown();
                    setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            })
        }
    });

    //CHECKBOX BOOTSTRAP CARNÊ LEÃO
    //========================================================================================================================

    $(".ckb-carne-leao").bootstrapSwitch({
        //'state': false,
        'size': 'mini',
        'onText': 'Sim',
        'offText': 'Não',
        'inverse': true,
        'onColor': 'success',
        'offColor': 'warning',
        'labelWidth': 1,
        'onSwitchChange': function (event, state) {
            $("span.aguarde, div.aguarde").css("display", "block");
            var params = 'funcao=CarneLeaoHabilitar';
            var habilitar = 0;
            if ($('.ckb-carne-leao').is(':checked'))
                habilitar = 1;
            params += '&habilitar=' + habilitar;
            $.ajax({
                type: 'post',
                url: 'modulos/usuario/php/funcoes.php',
                data: params,
                //cache: true,
                //beforeSend: function () {
                //},
                success: function (data) {
                    $("span.aguarde, div.aguarde").css("display", "none");

                    //if (habilitar == 1)
                    //  $('#li-area-contador').css('display', 'block');
                    //else
                    //  $('#li-area-contador').css('display', 'none');

                    var _data = JSON.parse(data);
                    $('.nSuccess p').html(_data.notificacao);
                    $('.nSuccess').slideDown();
                    setTimeout(function () { $('.nSuccess').slideUp() }, 3000);
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            })
        }
    });

    //CHECKBOX BOOTSTRAP PARA LAYOUT
    //========================================================================================================================

    $(".ckb-bootstrap").bootstrapSwitch({
        //'state': false,
        'size': 'mini',
        'onText': 'Sim',
        'offText': 'Não',
        'inverse': true,
        'onColor': 'success',
        'offColor': 'warning',
        'labelWidth': 1
    });


    //JQUERY VALIDATE CPF/CNPJ (VERIFICAÇÃO)
    //========================================================================================================================

    //Aplica o Plugin de verificação do CPF / CNPJ

    var $cnpj = $(".cpf_cnpj").attr("name");
    var $params = { debug: false, rules: {}, messages: {} };
    $params['rules'][$cnpj] = "cpf_cnpj";
    $params['messages'][$cnpj] = "Número inválido";

    /* ===== FORMS =====*/
    $("#form_usuario_editar").validate($params);

    //CHECKBOX CHECKALL
    //========================================================================================================================

    $('.checkAll').click(function () {
        var classCheckBox = $(this).val();
        $('.' + classCheckBox).prop('checked', this.checked);
    });

    //ALTERNAR LICENÇA
    //========================================================================================================================

    $('#select-licenca, #select-licenca-responsivo').on('change', function () {

        var clienteId = $(this).val();

        $.ajax({

            type: 'post', //Tipo do envio das informações GET ou POST
            url: 'modulos/usuario/php/funcoes.php',
            data:{
                funcao: 'AlternarLicenca',
                cliente_id: clienteId
            },
            dataType: 'json',
            //beforeSend: function () {
            //},
            success: function (data) {
                location.href = window.location.href;
            },

            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }

        });
    });

});