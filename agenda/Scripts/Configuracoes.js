/**
 * @Version: 1.0
 * @Author: Rafael Vanzo
 * @Layout theme: Porto Admin - Versão: 1.7.0
 * @Script
 */

/* ========================================================================================== */
/* ----------------------------------- DataTable usuários ----------------------------------- */
/* ========================================================================================== */

    // ===== Indique a ID da Table dentro da View para iniciar a tabela. 
    var $UrlTabela1 = $('#datatable-ajax-usuarios');                  //Ela deve vir antes das configurações por causa do sAjaxSource quando ele for utilizado dentro da váriavel padrão.
    
    // ===== Configuração padrão do DataTable
    var TabelaConfig1 = {
        columns: [                                  //Defina quais são as ORDENS e os NOMES das COLUNAS que receberam os registros do db.
            { "data": "Id" },           
            { "data": "Nome" },
            { "data": "Email" },
            { "data": "Opcoes", className: "dt-opcoes" }       //A classe dt-opcoes deixa a coluna centralizada e com largura personalizada.
        ],
        order: [[0, "asc"]],                     //Define qual coluna e como iniciará a ordenação. Se quiser.		    
        bProcessing: true,                         //Habilite para que apareça o icone PROCESSANDO.. quando o servidor estiver em processo.
        bServerSide: true,                          //Processar paginação, search e etc no servidor.
        columnDefs: [{ "orderable": false, "width": "75px", "targets": 0 },
                     { "orderable": false, "width": "auto", "targets": 1 },                     
                     { "orderable": false, "width": "auto", "targets": 2 },
                     { "orderable": false, "width": "100px", "targets": 3 }]    //Ativa apenas a 1º coluna das OPÇÕES de ordenar, utilize desta forma { "orderable": true , "targets": 0 } .  Se quiser inativar uma coluna utilize desta forma { "orderable": false , "targets": 0 } .
    }
    
    // ===== Para adicionar configurações
    TabelaConfig1.sAjaxSource = $UrlTabela1.data('url');                 //Adicione a variavel com as configurações padrões e depois coloque o option , lembrando que ele deve pré-existente na váriavel TabelaConfig. Da seguinte forma  TabelaConfig.sAjaxSource = 'Novo parametro'.
    
    // ===== Executa o DataTable
    var $table1 = $UrlTabela1.dataTable(TabelaConfig1);                    //Executa o dataTable inicial da página

    // ===== Auto-Resize Table
    //$(window).resize(function () {
    $UrlTabela1.css('width', '100%');
    //});

/* ========================================================================================== */
/* ----------------------------------- DataTable usuários ----------------------------------- */
/* ========================================================================================== */

    // ===== Indique a ID da Table dentro da View para iniciar a tabela. 
    var $UrlTabela2 = $('#datatable-ajax-doutor');                  //Ela deve vir antes das configurações por causa do sAjaxSource quando ele for utilizado dentro da váriavel padrão.
    
    // ===== Configuração padrão do DataTable
    var TabelaConfig2 = {
        columns: [                                  //Defina quais são as ORDENS e os NOMES das COLUNAS que receberam os registros do db.
            { "data": "id" },
            { "data": "nome" },
            { "data": "email" },
            { "data": "Opcoes", className: "dt-opcoes" }       //A classe dt-opcoes deixa a coluna centralizada e com largura personalizada.
        ],
        order: [[0, "asc"]],                     //Define qual coluna e como iniciará a ordenação. Se quiser.		    
        bProcessing: true,                         //Habilite para que apareça o icone PROCESSANDO.. quando o servidor estiver em processo.
        bServerSide: true,                          //Processar paginação, search e etc no servidor.
        columnDefs: [{ "orderable": false, "width": "75px", "targets": 0 },
                     { "orderable": false, "width": "auto", "targets": 1 },                     
                     { "orderable": false, "width": "auto", "targets": 2 },
                     { "orderable": false, "width": "100px", "targets": 3 }]    //Ativa apenas a 1º coluna das OPÇÕES de ordenar, utilize desta forma { "orderable": true , "targets": 0 } .  Se quiser inativar uma coluna utilize desta forma { "orderable": false , "targets": 0 } .
    }
    
    // ===== Para adicionar configurações
    TabelaConfig2.sAjaxSource = $UrlTabela2.data('url');                 //Adicione a variavel com as configurações padrões e depois coloque o option , lembrando que ele deve pré-existente na váriavel TabelaConfig. Da seguinte forma  TabelaConfig.sAjaxSource = 'Novo parametro'.
    
    // ===== Executa o DataTable
    var $table2 = $UrlTabela2.dataTable(TabelaConfig2);                    //Executa o dataTable inicial da página

    // ===== Auto-Resize Table
    //$(window).resize(function () {
    $UrlTabela2.css('width', '100%');
    //});

/* ========================================================================================== */
/* ----------------------------------- DataTable usuários ----------------------------------- */
/* ========================================================================================== */

    // ===== Indique a ID da Table dentro da View para iniciar a tabela. 
    var $UrlTabelaConsultas = $('#datatable-ajax-consultas');                  //Ela deve vir antes das configurações por causa do sAjaxSource quando ele for utilizado dentro da váriavel padrão.
    
    // ===== Configuração padrão do DataTable
    var TabelaConfigConsultas = {
        columns: [                                  //Defina quais são as ORDENS e os NOMES das COLUNAS que receberam os registros do db.
            { "data": "Id" },
            { "data": "Tipo" },
            { "data": "Descricao" },
            { "data": "Opcoes", className: "dt-opcoes" }       //A classe dt-opcoes deixa a coluna centralizada e com largura personalizada.
        ],
        order: [[0, "asc"]],                     //Define qual coluna e como iniciará a ordenação. Se quiser.		    
        bProcessing: true,                         //Habilite para que apareça o icone PROCESSANDO.. quando o servidor estiver em processo.
        bServerSide: true,                          //Processar paginação, search e etc no servidor.
        columnDefs: [{ "orderable": false, "width": "75px", "targets": 0 },
                     { "orderable": false, "width": "auto", "targets": 1 },                     
                     { "orderable": false, "width": "auto", "targets": 2 },
                     { "orderable": false, "width": "100px", "targets": 3 }]    //Ativa apenas a 1º coluna das OPÇÕES de ordenar, utilize desta forma { "orderable": true , "targets": 0 } .  Se quiser inativar uma coluna utilize desta forma { "orderable": false , "targets": 0 } .
    }
    
    // ===== Para adicionar configurações
    TabelaConfigConsultas.sAjaxSource = $UrlTabelaConsultas.data('url');                 //Adicione a variavel com as configurações padrões e depois coloque o option , lembrando que ele deve pré-existente na váriavel TabelaConfig. Da seguinte forma  TabelaConfig.sAjaxSource = 'Novo parametro'.
    
    // ===== Executa o DataTable
    var $table = $UrlTabelaConsultas.dataTable(TabelaConfigConsultas);                    //Executa o dataTable inicial da página

    // ===== Auto-Resize Table
    //$(window).resize(function () {
    $UrlTabelaConsultas.css('width', '100%');
    //});

/* ================================================================================= */
/* ----------------------------------- Validação ----------------------------------- */
/* ================================================================================= */

//**Instânciar fomulários que serão validados pela Id
$('#FormCreate').validate(ValidateOpt);



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
/* --------------------------- ATUALIZAR TABELA ------------------------------------ */
/* ================================================================================= */
/*
$('#ModalUsuarios, #ModalDeleteUsuarios').on('hide.bs.modal', function (event) {
    $table1.fnDraw(false);   
});
*/

$('#ModalDoutor, #ModalDeleteUsuarios').on('hide.bs.modal', function (event) {
    $table2.fnDraw(false);  
});




		