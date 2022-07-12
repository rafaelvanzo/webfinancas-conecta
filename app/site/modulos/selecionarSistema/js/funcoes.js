var baseUrl = 'https://' + window.location.hostname + '/';

/*
===========================================================================================
LOGOFF
===========================================================================================
*/
/*
$(document).ready(function () {

    $('.sair').live("click", function (e) {

        e.preventDefault();

        $("#dialog-alerta").dialog("option", "buttons", [
		{
		    text: "Sim",
		    click: function () { Logoff(); $("#dialog-alerta").dialog("close"); }
		},
		{
		    text: "Não",
		    click: function () { $("#dialog-alerta").dialog("close"); }
		}
        ]);

        $('#dialog-alerta').html("<br/> O sistema será enecerrado. Deseja continuar?");

        $('#dialog-alerta').dialog('open');

    });

});
*/
function Logoff() {
    //$("span.aguarde, div.aguarde").css("display", "block");
    var params = 'funcao=logoff';
    $.ajax({
        type: 'post',
        url: 'sistema/modulos/usuario/php/funcoes.php',
        data: params,
        dataType: 'json',
        cache: true,
        success: function (data) {
            location.href = baseUrl;
        }
    })
}