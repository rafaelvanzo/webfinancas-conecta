// JavaScript Document
var dados_global;

function ajax_jquery_login(params,funcao_retorno){

    $.ajax({
		   
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/cambio/php/funcoes.php', //url para onde será enviada as informações digitadas
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
CAMBIO
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
	$("span.aguarde, div.aguarde").css("display","none");
	var dados = eval("("+dados_global+")");
	if(dados.situacao == 1){
		location.href='geral';
	}else{
		alert(dados.notificacao);
	}
}

