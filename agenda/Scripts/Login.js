function Logar(FormId) {
    //alert($('#' + FormId).serialize());

    if (EstaConectado()) {        
 
        if ($("#" + FormId).valid(ValidateOpt)){

            $.ajax({
                dataType: 'json',
                type: 'post',                                                               //Tipo de envio GET ou POST.
                url: 'Login/Logar/',                                                         //Caminho do arquivo no servidor que ira receber e retornar as informações.
                data: $('#' + FormId).serialize(),                                          //Envia as informações para o servidor.
                beforeSend: function () {
                    $('body').addClass('loading-overlay-showing');                          //Aparece o gif de loading... 
                },
                success: function (data) {                                                  //Retorno quando houver sucesso.
                    if (data.logado === 1) {
                        //Notificacao('Sucesso', 'Sucesso!'); 
                        window.location.href = data.url;                                    //Redireciona para a pasta raiz, após iniciar a sessão.
                    } else if (data.logado === 0) {
                        Notificacao('Erro', 'Login ou Senha inválida.');                    //Chama a função de Notificação, insira o tipo da notíficação no 1º parametro e a mensagem no 1º. 
                    }                                    
                },
                error: function (data) {
                    $('body').removeClass('loading-overlay-showing');
                    Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
                }
            }).done(function (data) {
                $('body').removeClass('loading-overlay-showing');                           //Desaparece o gif de loading...              
                //LimparForm(FormId);
            });

        }
    }

}


//Enviar nova senha
function RecuperarSenha(FormId) {
    //alert($('#' + FormId).serialize());

    if (EstaConectado()) {

        if ($("#" + FormId).valid(ValidateOpt)) {

            $.ajax({
                dataType: 'json',
                type: 'post',                                                               //Tipo de envio GET ou POST.
                url: 'Login/RecuperarSenha/',                                                         //Caminho do arquivo no servidor que ira receber e retornar as informações.
                data: $('#' + FormId).serialize(),                                          //Envia as informações para o servidor.
                beforeSend: function () {
                    $('body').addClass('loading-overlay-showing');                          //Aparece o gif de loading... 
                },
                success: function (data) {                                                  //Retorno quando houver sucesso.
                    if (data.situacao === 1) {
                        Notificacao('Sucesso', 'Uma nova senha foi enviada para o e-mail de cadastro.');
                    } else if (data.situacao === 0) {
                        Notificacao('Erro', 'Email não cadastrado.');                    //Chama a função de Notificação, insira o tipo da notíficação no 1º parametro e a mensagem no 1º. 
                    }
                },
                error: function (data) {
                    $('body').removeClass('loading-overlay-showing');
                    Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
                }
            }).done(function (data) {
                $('body').removeClass('loading-overlay-showing');                           //Desaparece o gif de loading...              
                //LimparForm(FormId);
            });

        }
    }

}




/* ==== Recuperar Senha - Exibir campos recuperar senha ===== */
$('.recuperarSenha').click(function () {
    
        //Esconde
        $('.Login').addClass('hidden');
        $('.Senha').addClass('hidden');
        $('.recuperarSenha').addClass('hidden');

        //Exibe
        $('.alert').removeClass('hidden');
        $('.resetarSenha').removeClass('hidden');
        $('.voltarLogin').removeClass('hidden');
        $('.title').html('Recuperar Senha');

});

/* ==== Recuperar Senha - Exibir campos login ===== */
$('.voltarLogin').click(function () {
    
        //Esconde
        $('.alert').addClass('hidden');
        $('.resetarSenha').addClass('hidden');
        $('.voltarLogin').addClass('hidden');

        //Exibe
        $('.Login').removeClass('hidden');
        $('.Senha').removeClass('hidden');
        $('.recuperarSenha').removeClass('hidden');
        $('.title').html('Login');

});

/* ==== Submit Form com o botão ENTER ===== */
$(document).bind('keypress', function (e) {
    if (e.keyCode == 13) {
        $('.Login').trigger('click');
    }
});