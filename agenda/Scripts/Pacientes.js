/**
 * @Version: 1.0
 * @Author: Rafael Vanzo
 * @Layout theme: Porto Admin - Versão: 1.7.0
 * @Script
 */

/* ================================================================================= */
/* ----------------------------------- DataTable ----------------------------------- */
/* ================================================================================= */

    // ===== Indique a ID da Table dentro da View para iniciar a tabela. 
    var $UrlTabela = $('#datatable-ajax');                  //Ela deve vir antes das configurações por causa do sAjaxSource quando ele for utilizado dentro da váriavel padrão.
    
    // ===== Configuração padrão do DataTable
    var TabelaConfig = {
        columns: [                                  //Defina quais são as ORDENS e os NOMES das COLUNAS que receberam os registros do db.
            { "data": "id" },
            { "data": "nome"},
            { "data": "email" },
            { "data": "telefone" },
            { "data": "celular", className: "dt-opcoes" },
            { "data": "Opcoes", className: "dt-opcoes" }       //A classe dt-opcoes deixa a coluna centralizada e com largura personalizada.
        ],
        order: [[1, "asc"]],                     //Define qual coluna e como iniciará a ordenação. Se quiser.		    
        bProcessing: true,                         //Habilite para que apareça o icone PROCESSANDO.. quando o servidor estiver em processo.
        bServerSide: true,                          //Processar paginação, search e etc no servidor.
        columnDefs: [{ "orderable": false, "width": "75px", "targets": 0 },
                     { "orderable": true },
                     { "orderable": false, "width": "120px", "targets": 2 },
                     { "orderable": false,  "targets": 3 },
                     { "width": "75px", "targets": 4 },
                     { "orderable": false, "width": "100px", "targets": 5 }]    //Ativa apenas a 1º coluna das OPÇÕES de ordenar, utilize desta forma { "orderable": true , "targets": 0 } .  Se quiser inativar uma coluna utilize desta forma { "orderable": false , "targets": 0 } .
    }
    
    // ===== Para adicionar configurações
    TabelaConfig.sAjaxSource = $UrlTabela.data('url');                 //Adicione a variavel com as configurações padrões e depois coloque o option , lembrando que ele deve pré-existente na váriavel TabelaConfig. Da seguinte forma  TabelaConfig.sAjaxSource = 'Novo parametro'.
    
    // ===== Executa o DataTable
    var $table = $UrlTabela.dataTable(TabelaConfig);                    //Executa o dataTable inicial da página

    // ===== Auto-Resize Table
    //$(window).resize(function () {
    $UrlTabela.css('width', '100%');
    //});

/* ================================================================================= */
/* ----------------------------------- Validação ----------------------------------- */
/* ================================================================================= */

//**Instânciar fomulários que serão validados pela Id
var validacao = $('#FormCreate').validate(ValidateOpt);
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
        VerifyCPF(cpf);
    }

});

$('#SalvarForm').click(function(){
    
    var cpf = $(this).val();
    
    if(cpf.length == 14){
        VerifyCPF(cpf);
    }

});


/* ================================================================================= */
/* ----------------------------- SummerNote Instanciar ----------------------------- */
/* ================================================================================= */
/*
$('.summernote').summernote({
    height: 300,
    toolbar : [
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough', 'superscript', 'subscript']],
    ['fontsize', ['fontsize']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['height', ['height']],
    ['insert', ['link', 'picture', 'video']]
    ],
    callbacks: {
        onImageUpload: function (files) {
            sendFile(files[0]);
        }
    }

});
*/

/* ================================================================================= */
/* ------------------------------------- UPLOAD ------------------------------------ */
/* ================================================================================= */
/*
// Inabilita a discoberta automática do DropZone e aceita apenas pela Id ou Classe.
Dropzone.autoDiscover = false;

// ===== Id DropZone para a View.
var DropZoneId = '#Upload01';

// ===== Option do DropZone envio automático.
OptDropZone.dictDefaultMessage = 'Add imagem responsiva.'
OptDropZone.url = $(DropZoneId).attr('action');
OptDropZone.method = "POST";
OptDropZone.params = { pasta : $(DropZoneId).data('pasta-uploads'), larguraThumb: $(DropZoneId).data('largura-thumb') }
OptDropZone.acceptedFiles = 'image/*';
OptDropZone.maxFiles = 1;

var DropZone = new Dropzone(DropZoneId, OptDropZone);


// ===== Iniciar o Upload
$('.StartUpload').click(function () {
    DropZone.processQueue();
});
*/

/* ================================================================================= */
/* ------------------------------------ OPTION ------------------------------------- */
/* ================================================================================= */

/*

$("#Categoria").change(function () {
    var Id = $('#Categoria').val();
    if (Id != ''){
        $.ajax({
            type: 'get',
            url: 'Noticias/SubCategoriaListar/' + Id,
            success: function (data) {
                $('#SubCategoria').attr("readonly", false);
                $('#SubCategoria').html(data);
            },
        });
    } else {
        $('#SubCategoria').attr("readonly", true);
        $('#SubCategoria').html('');
    }
})


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
*/






function VerifyCPF(params)
{

    $.ajax({
        type: 'post',                                                             
        url: 'Pacientes/VerifyCPF',
        data: { 
            id: $('#FormCreate').attr('action'),
            cpf_cnpj : params
        },                                                                    
        beforeSend: function () {
            $('body').addClass('loading-overlay-showing');   
        },
        success: function (data) { 

            var obj = JSON.parse(data);

            if(obj.Situacao == 1)
            {
                Notificacao('Erro', 'Paciente já cadastrado.');

                validacao.showErrors({
                    "cpf_cnpj": "CPF " + $('.cpf_cnpj').val() + " já possui cadastrado."
                });

                //resolver o problema de cadastro de cpf.
                $('.cpf_cnpj').val('');
                
            }

            $('body').removeClass('loading-overlay-showing');
        },
        error: function (data) {
            Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
        }
    });

}



/* ====== Fim ====== */