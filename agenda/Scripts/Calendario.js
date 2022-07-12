/**
 * @Version: 1.0
 * @Author: Rafael Vanzo
 * @Layout theme: Porto Admin - Versão: 1.7.0
 * @Script
 */

/* ================================================================================= */
/* ----------------------------------- Validação ----------------------------------- */
/* ================================================================================= */

//**Instânciar fomulários que serão validados pela Id
var validacao = $('#FormCreate').validate(ValidateOpt);

//** Instânciar formulário de atualização de CPF */
var validacaoCPF = $('#FormCpf').validate(ValidateOpt);

/*
$('.cpf_cnpj').change(function(){

    var cpf = $(this).val();
    
    if(cpf.length == 14){
        VerifyCPF(cpf);
    }

});
*/
$('.cpf_cnpj').focusout(function(){

    var cpf = $(this).val();
    
    if(cpf.length == 14){
        var FormId = $(this).closest("form").attr("id");
        VerifyCPF(cpf, FormId);
    }

});

$('#SalvarForm').click(function(){
    
    var cpf = $(this).val();
    
    if(cpf.length == 14){
        var FormId = $(this).closest("form").attr("id");
        VerifyCPF(cpf, FormId);
    }

});



/* ================================================================================= */
/* ---------------------------------- CALENDARIO ----------------------------------- */
/* ================================================================================= */


$(document).ready(function(){        
   
    $('#calendar').fullCalendar({ 
            header: {
            left: 'agendaDay, agendaWeek, month, listWeek',
            center: 'title',
            right: 'prev, today, next'
        },
        defaultView: 'listWeek',
        editable: false, 
        
        navLinks: true,
        eventsLimit: true, 
        eventClick: function(calEvent)
        {

            var time = calEvent.start.toString(); 
                time = time.split(" ");


            $.when( modalOpen('Edit', 'ModalAddEvento', calEvent.id) ).done(function(x){ 

                setTimeout(function(){  
                    $('.horario').val(time);
                }, 1000);

            });

            $('.ExcluirAgendamento').attr('data-delete-id', calEvent.id);
            $('.ExcluirAgendamento').attr('data-delete-nome', calEvent.title);

        },
        selectable: true,
        selectHelper: true,
        select: function(start, end)
        {
            modalOpen('Create', 'ModalAddEvento');
            alert(start + '- '+ end)
        },
        events: $('#calendar').data('calendar-url') 

    });
   
});

// ==== Altera horário no campo tempo
$(".changeHorario").change(function(){

    var IdConsulta = $('#FormAddevento select[name="IdConsulta"]').val(); 
    var IdDoutor = $('#FormAddevento select[name="IdDoutor"]').val();  
    var Data = $('#FormAddevento input[name="Data"]').val(); 

    if(IdConsulta !== null && IdDoutor !== null && Data.length > 0)
    {

        $.ajax({
            type: 'post',                                                               
            url: 'Calendario/CalculoHorario/',
            data: { 
                idTipoConsulta : IdConsulta,
                idDoutor : IdDoutor,
                data : Data,
                idConsulta : $('#FormAddevento input[name="Id"]').val()
                },                                                                      
            beforeSend: function () {
                
                $('.horario').attr('disabled','disabled');                                   

            },
            success: function (data) {               
    
                $('.horario').html(data);
                $('.horario').removeAttr('disabled');

            }

        });

    }else{

        $('#FormAddevento select[name="Horario"]').attr('disabled', true);

    }

});

// ==== Passar detalhes para exclusão do modal.
$('.ExcluirAgendamento').click(function(){

    var id = $(this).data('delete-id');
    var nome =  $(this).data('delete-nome');

    modalOpen('Delete', 'ModalDelete', id, nome);

});

// ==== Esconder o modal.
$('.ExcluirConfirm').click(function(){ 

    $('#ModalAddEvento').modal('hide');

});


// ==== Add Pacientes
$('.AddPaciente').click(function(){

    $('#ModalCreate').modal('show');

});

function addPaciente()
{
    if (EstaConectado()) {

        var FormId = "FormCreate";
        var ModalId = "ModalCreate";
        var Msg = $('#' + FormId).data('msg-sucesso');

        if ($("#" + FormId).valid(ValidateOpt)) {
        
            $.ajax({
                type: 'post',                                                       //Tipo de envio GET ou POST.
                url: $('#' + FormId).attr('action'),                                //Caminho do arquivo no servidor que ira receber e retornar as informações.
                data: $('#' + FormId).serialize(),                                  //Envia as informações para o servidor.
                beforeSend: function () {
                    $('body').addClass('loading-overlay-showing');                  //Aparece o gif de loading...       

                    if ($('#' + ModalId).is(':visible')) {
                        $('#' + ModalId).modal('hide');                             //Esconde o modal.   
                    } else {
                        $('#' + ModalId).modal('show');                             //Mostrar o modal.  
                    }
                },
                success: function (data) {                                          //Retorno quando houver sucesso.                                    

                    var obj = JSON.parse(data); 
                
                        $('#' + ModalId).modal('hide');

                        Notificacao('Sucesso', Msg);                                        //Chama a função de Notificação, insira o tipo da notíficação no 1º parametro e a mensagem no 1º. 

                    // Preencher selectTwo ** Adicionar em utilities.js
                    $('#FormAddevento [name="IdFavorecido"]').val(obj.id).trigger('change');
                    var newOption = new Option(obj.nome, obj.id, true, true);
                    $('#FormAddevento [name=IdFavorecido]').append(newOption).trigger('change');                       

                },
                error: function (data) {
                    Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
                }
            }).done(function (data) {
                $('body').removeClass('loading-overlay-showing');                       //Desaparece o gif de loading...   

            });
            //* Obs.: console.log(data); para visualizar o resultado via console.
        }

    }
}


//Filtrar por Doutor
$('.CalendarioDoutor').change(function(){


        var idDoutor = $(this).val();
        
        var url = $('#calendar').data('calendar-url-base');
    
        $('#calendar').data('calendar-url', url + idDoutor + '/'); 
    

        $('#calendar').fullCalendar('destroy');



        $('#calendar').fullCalendar({

            header: {
            left: 'agendaDay, agendaWeek, month, listWeek',
            center: 'title',
            right: 'prev, today, next'
        },
        defaultView: 'listWeek',
        editable: false, 
        
        navLinks: true,
        eventsLimit: true, 
        eventClick: function(calEvent)
        {

            var time = calEvent.start.toString(); 
                time = time.split(" ");


            $.when( modalOpen('Edit', 'ModalAddEvento', calEvent.id) ).done(function(x){ 

                setTimeout(function(){  
                    $('.horario').val(time);
                }, 1000);

            });

            $('.ExcluirAgendamento').attr('data-delete-id', calEvent.id);
            $('.ExcluirAgendamento').attr('data-delete-nome', calEvent.title);

        },
        selectable: true,
        selectHelper: true,
        select: function(start, end)
        {
            modalOpen('Create', 'ModalAddEvento');
            alert(start + '- '+ end)
        },
        events: $('#calendar').data('calendar-url') 


        });


});

$('.situacao-change').change(function(){

    var valor = $(this).val(); 
    var consulta_id = $('.idConsulta').val();
    
        if(valor === '1')
            VerifyCadastroCPF(consulta_id); 
            
});

//Verifica se o select dos favorecidos foi alterado
$('.situacao-change-favorecido').on('select2:select', function (e) {
  
    var valor = $('.situacao-change').val(); 
    var consulta_id = $('.idConsulta').val();
    
        if(valor === '1')
            VerifyCadastroCPF(consulta_id); 
});

//Verifica se o select dos Responsaveis foi alterado
$('.situacao-change-responsavel').on('select2:select', function (e) {
  
    var valor = $('.situacao-change').val(); 
    var consulta_id = $('.idConsulta').val();
    
        if(valor === '1')
            VerifyCadastroCPF(consulta_id); 
});



function VerifyCadastroCPF(consulta_id)
{
    var consulta_id = (consulta_id == undefined || consulta_id == '')? 0 : consulta_id;

    var idFav = $('.situacao-change-favorecido').select2('data')[0];
    idFav = (idFav == undefined)? 0 : idFav.id; 

    var idResp = $('.situacao-change-responsavel').select2('data')[0];
    idResp = (idResp == undefined)? 0 : idResp.id; console.log(idResp);

        $.ajax({ 
            type: 'post',                                                       
            url: "Calendario/VerifyCadastroCPF",                               
            data: { 
                id: consulta_id,
                IdFavorecido: idFav,
                IdResponsavel: idResp

            },
            beforeSend: function () {
                $('body').addClass('loading-overlay-showing');                     
            },
            success: function (data) {                                                            
            
            var obj = JSON.parse(data); 

                if(obj.favorecido == true && obj.responsavel == true){

                    alert('AVISO: Ao selecionar a situação ATENDIDO e salvar, a consulta/procedimento será registrada no sistema financeiro.');
                    return true;

                }else{               

                    alert('AVISO: Para selecionar a opção "Atendido" é necessário adicionar um CPF válido no cadastro do paciente e/ou responsável.');

                    $('.situacao-change').val(obj.Situacao);

                    
                        if(obj.responsavel == false)
                        {
                            $('.nomeResp').html(obj.resp_nome);
                            $('.showResponsavel').css('display', 'block');
                            $('.resp_id').val(obj.resp_id);
                            $('.responsavel_cpf').val(obj.resp_cpf);

                        }else{
                            $('.showResponsavel').css('display', 'none');
                            $('.nomeResp').val('');
                        }


                        if(obj.favorecido == false)
                        {
                           
                            $('.nomeFav').html(obj.fav_nome);
                            $('.showFavorecido').css('display', 'block');
                            $('.favorecido_cpf').val(obj.fav_cpf);
                            $('.fav_id').val(obj.fav_id);

                        }else{
                            $('.showFavorecido').css('display', 'none');
                            $('.nomeFav').val('');
                        }


                    $('#ModalCPf').modal('show');

                    return false;
                }                
        
            },
            error: function (data) {            
                Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
                $('.situacao-change').val(obj.Situacao);
                return false;
            }
            }).done(function (data) {
                $('body').removeClass('loading-overlay-showing');                         

            });

}


/** Get id from IdConsulta */
$('.procedimento').change(function(){
   
    if($('#ModalAddEvento').is(':visible'))
    ProcedimentoValor($(this).val());
    
});

function ProcedimentoValor(id)
{   

    if (EstaConectado()) { 
        
            if(id != null || id != ''){


                $.ajax({
                    type: 'post',                                                       
                    url: "Configuracoes/ProcedimentoValor",                               
                    data: { 
                      /*
                        idTipoConsulta : $('#FormAddevento select[name="IdConsulta"]').val(),
                        idDoutor : $('#FormAddevento select[name="IdDoutor"]').val(),
                        data : $('#FormAddevento input[name="Data"]').val(),
                        idConsulta : $('#FormAddevento input[name="Id"]').val()
                        */
                       id: id
                    },                                 
                    beforeSend: function () {
                        $('body').addClass('loading-overlay-showing');                     
                    },
                    success: function (data) {                                                            

                        var obj = JSON.parse(data); 

                        $('#FormAddevento input[name="Valor"]').val(obj.Valor);
                    
                    },
                    error: function (data) {
                        Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
                    }
                }).done(function (data) {
                    $('body').removeClass('loading-overlay-showing');                         

                });
                
            }

    }else{

        Notificacao('Erro', 'Verifique sua conexão com a internet.');
    }

}

/**
 * Pegar a id do select favorecido para envio de whatsapp
 */
/*
$('select [name=IdFavorecido]').on('change', function() {
    //alert( 'teste' );
  });
*/

  
function VerifyCPF(cpf, FormId)
{

    $.ajax({
        type: 'post',                                                             
        url: 'Pacientes/VerifyCPF',
        data: { 
            id: $('#' + FormId).attr('action'),
            cpf_cnpj : cpf
        },                                                                    
        beforeSend: function () {
            $('body').addClass('loading-overlay-showing');   
        },
        success: function (data) { 


            var obj = JSON.parse(data);

            if(obj.Situacao == 1)
            {
                Notificacao('Erro', 'Paciente já cadastrado.');

                if(FormId == "FormCreate")
                {
                    validacao.showErrors({
                        "cpf_cnpj": "CPF " + $('#' + FormId + ' .cpf_cnpj').val() + " já possui cadastrado."
                    });

                }else{

                    validacaoCpf.showErrors({
                        "cpf_cnpj": "CPF " + $('#' + FormId + ' .cpf_cnpj').val() + " já possui cadastrado."
                    });


                }   
                //resolver o problema de cadastro de cpf.
                $('#' + FormId + ' .cpf_cnpj').val('');
                
            }

            $('body').removeClass('loading-overlay-showing');
        },
        error: function (data) {
            Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
        }
    });

}


/**
 * Zerar ID do agendamento quando for uma nova consulta
 */
$('.zeraForm').click(function(){
    $('.idConsulta').val('');
});

/**
 * Get id Select Two
 */
/*
$('.selectTwo').on('select2:select', function (e) {
    var data = e.params.data;
    //console.log(data);
});
console.log($('.selectTwo').find(':selected').data('value'));
//var teste = $('#mySelect2').val(data.id).trigger('change');
*/

//var teste = $('#mySelect2').trigger('change.select2');
//var idFav = $('.situacao-change-favorecido').select2('data').id; console.log(idFav);