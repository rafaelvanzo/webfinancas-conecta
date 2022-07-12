/* Add here all your JS customizations */
/*
 * Retorna lista dos aniversariantes
 */


$('.aniversarios').click(function(){ 

    $.ajax({
    type: 'post',                                                               
    url: 'Calendario/Aniversario',                                                                    
        beforeSend: function () {
            $('body').addClass('loading-overlay-showing');  
        },
        success: function (data) {  
            
            obj = JSON.parse(data);

            $('.listaAniversarios').html(obj.lista);

            $('#ModalAniversarios').modal('show'); 

        },
        error: function (data) {
            Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
        }

    }).done(function (data) {    
        $('body').removeClass('loading-overlay-showing'); 
    });
     

});

/**
 * Total de aniversariantes
 */
function TotalAniversaos()
{
    $.ajax({
    type: 'post',                                                               
    url: 'Calendario/TotalAniversario',                                                                    
        beforeSend: function () {
            $('body').addClass('loading-overlay-showing');  
        },
        success: function (data) {  
            
            obj = JSON.parse(data);
            
                $('.totalAniversarios').after(obj.html);

        },
        error: function (data) {
            Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
        }

    }).done(function (data) {    
        $('body').removeClass('loading-overlay-showing'); 
    });
     
}

TotalAniversaos()