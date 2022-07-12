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

		$('#dialog-alerta').html("<br/> O sistema será enecerrado. Deseja continuar?");

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
	location.href='login';
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
	//alert(dados_global);
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
PERFIL DO USUÁRIO
================================================================================================
*/
$(document).ready(function(){

	//===== UI dialog - Alterar Senha =====//	
	$( "#dialog-editar-plano" ).dialog({
		autoOpen: false,
		modal: true,
		width: '500',
		buttons: {	
			Concluir: function() {
			//	senha_alterar();
			},
			Sair:	function() {
			$( this ).dialog( "close" );
			formLimpar('formAlterarSenha');
			}			
		}
	});
	
	$( "#opener-editar-plano" ).click(function() {
		$( "#dialog-editar-plano" ).dialog( "open" );
		return false;

	});	
});


/*
===========================================================================================
EDITAR DADOS DO USUÁRIO
===========================================================================================
*/

function usuariosEditar(){
	if($('#form_usuarios').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = $('#form_usuarios').serialize();
		ajax_jquery(params,"usuariosEditarRetorno()");
	}
}

function usuariosEditarRetorno(){
	var dados = eval("("+dados_global+")");
	$('.nSuccess p').html(dados.notificacao);
	$('.nSuccess').slideDown();
	setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
  $('#form_usuarios').html(dados.favorecidos);
	$("span.aguarde, div.aguarde").css("display","none");	
	
}


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
		Width: 'auto',
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

