/*
Name: 			View - Home
Written by: 	Okler Themes - (http://www.okler.net)
Version: 		2.0
*/

//var baseUrl = 'https://www.webfinancas.com/';
//var baseUrl = 'http://teste2.fundacao1demaio.org.br/webfinancas/';
var baseUrl = window.location.protocol + '//' + window.location.hostname + '/';

(function() {

    "use strict";

    var Home = {

        initialized: false,

        initialize: function() {

            if (this.initialized) return;
            this.initialized = true;

            this.build();
            this.events();
            this.validations();
            this.cadastroOk();
            //this.senhaRecuperar();
            this.senhaRecuperarOk();
            this.mascara();

        },

        build: function(options) {

            // Circle Slider
            if($("#fcSlideshow").get(0)) {
                $("#fcSlideshow").flipshow();

                setInterval( function() {
                    $("#fcSlideshow div.fc-right span:first").click();
                }, 3000);

            }

            // Revolution Slider Initialize
            $("#revolutionSlider").each(function() {

                var slider = $(this);

                var defaults = {
                    delay: 9000,
                    startheight: 495,
                    startwidth: 960,

                    hideThumbs: 10,

                    thumbWidth: 100,
                    thumbHeight: 50,
                    thumbAmount: 5,

                    navigationType: "both",
                    navigationArrows: "verticalcentered",
                    navigationStyle: "round",

                    touchenabled: "on",
                    onHoverStop: "on",

                    navOffsetHorizontal: 0,
                    navOffsetVertical: 20,

                    stopAtSlide: 0,
                    stopAfterLoops: -1,

                    shadow: 0,
                    fullWidth: "on",
                    videoJsPath: "site/vendor/rs-plugin/videojs/"
                }

                var config = $.extend({}, defaults, options, slider.data("plugin-options"));

                // Initialize Slider
                var sliderApi = slider.revolution(config).addClass("slider-init");

                // Set Play Button to Visible
                sliderApi.bind("revolution.slide.onloaded ",function (e,data) {
                    $(".home-player").addClass("visible");
                });

            });

            // Revolution Slider One Page
            if($("#revolutionSliderFullScreen").get(0)) {
                var rev = $("#revolutionSliderFullScreen").revolution({
                    delay: 9000,
                    startwidth: 1170,
                    startheight: 600,

                    hideThumbs: 200,

                    thumbWidth: 100,
                    thumbHeight: 50,
                    thumbAmount: 5,

                    navigationType: "both",
                    navigationArrows: "verticalcentered",
                    navigationStyle: "round",

                    touchenabled: "on",
                    onHoverStop: "on",

                    navOffsetHorizontal: 0,
                    navOffsetVertical: 20,

                    stopAtSlide: -1,
                    stopAfterLoops: -1,

                    shadow: 0,
                    fullWidth: "on",
                    fullScreen: "on",
                    fullScreenOffsetContainer: ".header",
                    videoJsPath: "site/vendor/rs-plugin/videojs/"
                });

            }

            // Nivo Slider
            if($("#nivoSlider").get(0)) {
                $("#nivoSlider").nivoSlider();
            }

        },

        events: function() {

            this.moveCloud();

        },

        moveCloud: function() {

            var $this = this;

            $(".cloud").animate( {"top": "+=20px"}, 3000, "linear", function() {
                $(".cloud").animate( {"top": "-=20px"}, 3000, "linear", function() {
                    $this.moveCloud();
                });
            });

        },

        senhaRecuperarOk: function(){
            $('#l1').click(function(){
                if( $('.form-login-element').hasClass('hidden') ){
                    $('.form-login-element').removeClass('hidden');
                    $('#senhaRecuperarOk').addClass('hidden');
                    $('#loginSuccess').addClass('hidden');
                }
                document.getElementById('loginForm').reset();
            })
        },
        /*
                senhaRecuperar: function(){
                    $('#btnSenhaRecuperar').click(function(e) {
        
                        $("#loginForm").validate({
                            submitHandler: function(form) {
                                
                                // Loading State
                                var submitButton = $("#entrar");
                                submitButton.button("loading");
                                
                                var form_data = "funcao=senhaRecuperar&email="+$('#loginForm input[name="email"]').val();
                                
                                // Ajax Submit
                                $.ajax({
                                    type: "POST",
                                    url: baseUrl + "sistema/modulos/usuario/php/funcoes.php",
                                    data: form_data,
                                    dataType: "json",
                                    beforeSend: function(){
                                        if($('#loginError').is(':visible')){
                                            $('#loginError').addClass('hidden');
                                        }
                                    },
                                    error: function(x, t, e){
                                        //alert(e);
                                    },
                                    success: function (data) {
                                        //alert(data.situacao);
                                        if(data.situacao == "0"){
                                        }else if(data.situacao == "1") {
                                            if($('#senhaRecuperarError').is(':visible')){
                                                $('#senhaRecuperarError').addClass('hidden');
                                            }
                                            //$('#loginForm').addClass('hidden');
                                            $('#entrar').addClass('hidden');
                                            $('#cancelarLogin').addClass('hidden');
                                            $('#senhaRecuperarOk').removeClass('hidden');
                                            $('#senhaRecuperarSuccess').removeClass('hidden');
                                        }else{
                                            $('#loginError .notificacao').html(data.notificacao);
                                            $('#loginError').removeClass('hidden');
                                        }
                                    },
                                    complete: function(){
                                        submitButton.button("reset");
                                    },
                                });
                                
                            },
                            rules: {
                                email: {
                                    required: true,
                                    email: true
                                },
                            },
            
                            highlight: function (element) {
                                $(element)
                                    .parent()
                                    .removeClass("has-success")
                                    .addClass("has-error");
                            },
                            success: function (element) {
                                $(element)
                                    .parent()
                                    .removeClass("has-error")
                                    .addClass("has-success")
                                    .find("label.error")
                                    .remove();
                            },
                            
                        });
                    });
                },
        */
        cadastroOk: function(){
            $('#c1').click(function(){
                if( $('.form-cadastro-element').hasClass('hidden') ){
                    $('.form-cadastro-element').removeClass('hidden');
                    $('#cadastrarOk').addClass('hidden');
                    $('#cadastroSuccess').addClass('hidden');
                }
                document.getElementById('cadastroForm').reset();
            })
        },
		
        validations: function() {

            $("#loginForm").validate({
                submitHandler: function(form) {
					
                    // Loading State
                    var submitButton = $("#entrar");
                    submitButton.button("loading");
					
                    var form_data = $('#loginForm').serialize();
                    // Ajax Submit
                    $.ajax({
                        type: "POST",
                        url: baseUrl + "sistema/modulos/usuario/php/funcoes.php",
                        data: form_data,
                        dataType: "json",
                        beforeSend: function(){
                            if($('#loginError').is(':visible')){
                                $('#loginError').addClass('hidden');
                            }
                        },
                        error: function(x, t, e){
                            //alert(e);
                        },
                        success: function (data) {
                            //alert(data.situacao);
                            if(data.situacao == "0"){
                            }else if(data.situacao == "1") {
                                if(data.financeiro == 1 && data.contador == 1){
                                    location.href = baseUrl + "selecionarSistema";
                                }else if(data.financeiro == 1){
                                    location.href = baseUrl + "sistema";
                                }else{
                                    location.href = baseUrl + "contador";
                                }
                            }else{
                                $('#loginError .notificacao').html(data.notificacao);
                                $('#loginError').removeClass('hidden');
                            }
                        },
                        complete: function(){
                            submitButton.button("reset");
                        },
                    });
					
                },
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    senha: {
                        required: true,
                        minlength: 6,
                        maxlength: 15
                    }
                },
                messages:{
                    senha:{
                        required: "Por favor preencha a senha.",
                        minlength: "A senha deve conter no mínimo 6 e no máximo 15 digitos.",
                        maxlength: "A senha deve conter no mínimo 6 e no máximo 15 digitos.",
                    }
                },

                highlight: function (element) {
                    $(element)
						.parent()
						.removeClass("has-success")
						.addClass("has-error");
                },
                success: function (element) {
                    $(element)
						.parent()
						.removeClass("has-error")
						.addClass("has-success")
						.find("label.error")
						.remove();
                },
            });

            $("#cadastroForm").validate({
                submitHandler: function(form) {

                    // Loading State
                    var submitButton = $("#cadastrar");
                    submitButton.button("loading");

                    var form_data = $('#cadastroForm').serialize();

                    // Ajax Submit
                    $.ajax({
                        type: "POST",
                        url: baseUrl + "site/php/cadastro-form.php",
                        data: form_data,
                        /*
                                                data: {
                                                    "name": $("#cadastroForm #name").val(),
                                                    "email": $("#cadastroForm #email").val(),
                                                    "tel": $("#cadastroForm #tel").val(),
                                                    "senha": $("#cadastroForm #senha").val(),
                                                    "funcao": $("#cadastroForm #senha").val(),
                                                },
                        */
                        dataType: "json",
                        beforeSend: function(){
                            if($('#cadastroError').is(':visible')){
                                $('#cadastroError').addClass('hidden');
                            }
                        },
                        error: function(x, t, e){
                            //alert(e);
                        },
                        success: function (data) {

                            if (data.situacao == "0") {
								
                            }else if(data.situacao == "1") {
                                if($('#cadastroError').is(':visible')){
                                    $('#cadastroError').addClass('hidden');
                                }
                                $('.form-cadastro-element').addClass('hidden');
                                $('#cadastrarOk').removeClass('hidden');
                                $('#cadastroSuccess').removeClass('hidden');
                                /*
								$("#orcamentoSuccess").removeClass("hidden");
								$("#orcamentoError").addClass("hidden");
								
								//Esconder a mensagem de sucesso
								setTimeout(function(){ $('#orcamentoSuccess').fadeOut();}, 5000); 
								
								// Reset Form
								$("#contactForm .form-control")
									.val("")
									.blur()
									.parent()
									.removeClass("has-success")
									.removeClass("has-error")
									.find("label.error")
									.remove();

								if(($("#orcamentoSuccess").position().top - 80) < $(window).scrollTop()){
									$("html, body").animate({
										 scrollTop: $("#contactSuccess").offset().top - 80
									}, 300);
								}
								*/
                                //$("#orcamentoSuccess").fadeOut('slow',function(){ $("#contactSuccess").addClass("hidden"); });
						
						
                            } else {
                                $('#cadastroError').removeClass('hidden');
								
                                /*
								$("#orcamentotError").removeClass("hidden").focus(); //comentado
								$("#orcamentoSuccess").addClass("hidden"); //comentado

								//comentado
								
								if(($("#orcamentoError").position().top - 80) < $(window).scrollTop()){
									$("html, body").animate({
										 scrollTop: $("#contactError").offset().top - 80
									}, 300);
								}
								*/
                            }
                        },
                        complete: function () {
                            submitButton.button("reset");
                        }
                    });
                },
                rules: {
                    nome: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    /*
					subject: {
						required: true
					},
					*/
                    senha: {
                        required: true,
                        minlength: 6,
                        maxlength: 8
                    },
                    termos_de_uso: {
                        required: true,
                    },
                },
                messages:{
                    senha:{
                        required: "Por favor preencha uma senha.",
                        minlength: "Crie uma senha de no mínimo 6 e no máximo 8 digitos.",
                        maxlength: "Crie uma senha de no máximo 8 digitos."
                    },
                    termos_de_uso:{
                        required: "É necessária a aceitação dos termos de uso.",
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "termos_de_uso")
                        error.insertAfter("#linkTermosUso");
                    else
                        error.insertAfter(element);
                },

                highlight: function (element) {
                    $(element)
						.parent()
						.removeClass("has-success")
						.addClass("has-error");
                },
                success: function (element) {
                    $(element)
						.parent()
						.removeClass("has-error")
						.addClass("has-success")
						.find("label.error")
						.remove();
                },
            });

        },
        //fim validations
		
        mascara: function(){
            $.mask.definitions['~'] = "[+-]";
            $(".maskPhone").mask("(99) 99999-999?9").focusout(function(e) {
                var phone = $(this).val();
                var _phone = phone.replace(/_/g, '');
                if(_phone.length > 14) {
                    $(this).mask("(99) 99999-999?9");
                } else {
                    $(this).mask("(99) 9999-9999?9");
                } 		
            });
        },

    };

    Home.initialize();

})();


//=========================== Abrir Login apÃ³s cadastro ==============================

/* Abrir o formulÃ¡rio de login */
var hlg = window.location.hash;
if(hlg == '#login'){ $("#l1").trigger('click'); }else if(hlg == '#cadastro'){ $("#c1").trigger('click'); }

//=========================== Recuperar senha ==============================

function senha_recuperar(){

    // Loading State
    var submitButton = $("#entrar");
    submitButton.button("loading");
    $('#senhaRecuperar').attr('onclick','');

    var form_data = "funcao=senhaRecuperar&email="+$('#loginForm input[name="email"]').val();
	
    // Ajax Submit
    $.ajax({
        type: "POST",
        url: baseUrl + "sistema/modulos/usuario/php/funcoes.php",
        data: form_data,
        dataType: "json",
        beforeSend: function(){
            if($('#loginError').is(':visible')){
                $('#loginError').addClass('hidden');
            }
        },
        error: function(x, t, e){
            //alert(e);
            $('#senhaRecuperar').attr('onclick','senha_recuperar();');
            submitButton.button("reset");
        },
        success: function (data) {
            //alert(data.situacao);
            $('#senhaRecuperar').attr('onclick','senha_recuperar();');
            submitButton.button("reset");
            if(data.situacao == "0"){
            }else if(data.situacao == "1") {
                if($('#loginError').is(':visible')){
                    $('#loginError').addClass('hidden');
                }
                $('.form-login-element').addClass('hidden');
                $('#senhaRecuperarOk').removeClass('hidden');
                $('#loginSuccess').removeClass('hidden');
            }else{
                $('#loginError .notificacao').html(data.notificacao);
                $('#loginError').removeClass('hidden');
            }
        },
        complete: function(){
            //submitButton.button("reset");
        },
    });

}

