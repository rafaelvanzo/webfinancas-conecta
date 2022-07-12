/**
 * @Version: 1.0
 * @Author: Rafael Vanzo
 * @Layout theme: Porto Admin - Versão: 1.7.0
 * @Script
 */

//START PAGE: Iniciar relatório
$(document).ready(function () {

    $('.gerarRelatorio').trigger('click');

});

/* ================================================================================= */
/* ----------------------------------- DataTable ----------------------------------- */
/* ================================================================================= */

  $('.gerarRelatorio').click(function(){

        if (EstaConectado()) {
        
        
            $.ajax({
                type: 'post',                                                       //Tipo de envio GET ou POST.
                url: $('#FormRelatorios').attr('action'),                                //Caminho do arquivo no servidor que ira receber e retornar as informações.
                data: $('#FormRelatorios').serialize(),                                  //Envia as informações para o servidor.
              
                beforeSend: function () {

                    $('body').addClass('loading-overlay-showing');                  //Aparece o gif de loading...       

                },
                success: function (data) {                                          //Retorno quando houver sucesso.                                    

                    //var obj = JSON.parse(data);   
                    
                    $('.tableRelatorio').html(data);

                },
                error: function (data) {
                    Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
                }
            }).done(function (data) {

                $('body').removeClass('loading-overlay-showing');                       //Desaparece o gif de loading...   

            });
        
        
        
        }

    return false;

  });




  //Exibir consultas reagendadas
 function ModalList(id)
 {


    $.ajax({
        type: 'post',                                                       //Tipo de envio GET ou POST.
        url: 'Relatorios/ListReagendadas/' + id ,                                //Caminho do arquivo no servidor que ira receber e retornar as informações.
            
        beforeSend: function () {

            $('body').addClass('loading-overlay-showing');                  //Aparece o gif de loading...       

        },
        success: function (data) {                                          //Retorno quando houver sucesso.                                    

            $('#ModalReagendadas').modal('show');
            
            $('.modalReagendadas').html(data);

        },
        error: function (data) {
            Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
        }
    }).done(function (data) {

        $('body').removeClass('loading-overlay-showing');                       //Desaparece o gif de loading...   

    });


  }

/* ================================================================================= */
/* ----------------------------------- Validação ----------------------------------- */
/* ================================================================================= */

//**Instânciar fomulários que serão validados pela Id
$('#FormCreate').validate(ValidateOpt);




/* ================================================================================= */
/* ------------------------------------ OPTION ------------------------------------- */
/* ================================================================================= */

$(".data").datepicker({
    dateFormat: 'dd/mm/yy',
    dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
    dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
    monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
    nextText: 'Próximo',
    prevText: 'Anterior'
});



/* ================================================================================= */
/* --------------------------------- GERAR EXCEL ----------------------------------- */
/* ================================================================================= */
function GerarExcel()
{
    
    var nomePlanilha = 'Relatorio_agenda';

    var htmlPlanilha = $('#excel').html();
    htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>' + nomePlanilha + '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>' + htmlPlanilha + '</body></html>';
    var htmlBase64 = btoa(htmlPlanilha);
    var link = "data:application/vnd.ms-excel;base64," + htmlBase64;

    var hyperlink = document.createElement("a");
    hyperlink.download = nomePlanilha;
    hyperlink.href = link;
    hyperlink.style.display = 'none';
 
    document.body.appendChild(hyperlink);
    hyperlink.click();
    document.body.removeChild(hyperlink);
}

/* ====== Fim ====== */