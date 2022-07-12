/**
 * @Version: 1.0
 * @Author: Rafael Vanzo
 * @Layout theme: Porto Admin - Versão: 1.7.0
 * @Script
 */

/* ================================================================================= */
/* -------------------------------- CONEXÃO INTERNET ------------------------------- */ 
/* ================================================================================= */

// ===== Verifica se a conexão com a internet está ativa
function EstaConectado() {

    if(navigator.onLine == false){ 

        Notificacao('SemConexao', 'Verifique a sua conexão com a internet.');  
       
        return false;

    }else{ 
        
        //Se estiver conectado deixa o sistema prosseguir.
        return true;
    }
}

/**
* ================================================================================= 
* ------------------------------- DataTable MODELO --------------------------------  
* ================================================================================= 
*/

/*
// ===== Indique a ID da Table dentro da View para iniciar a tabela. 
var $UrlTabela = $('#datatable-ajax');                  //Ela deve vir antes das configurações por causa do sAjaxSource quando ele for utilizado dentro da váriavel padrão.

// ===== Configuração padrão do DataTable
var TabelaConfig = {
		    //searchDelay: '200',                      //Delay no localizar quando for utilizar o Servidor para buscar.
		    //Ajax: $table.data('url'),                 //Retorna apenas a lista do servidor, o resto é processado no navegador.
		    //lengthMenu: [[ 10, 25, 50, -1 ], [10, 25, 50, "Todos"]]          //Quantidade de registros a serem mostrados por página. Obs.:  O -1 mostra todos os registros, se utiliza-lo precisa preparar o servidor para que ele retorne.
		    //sAjaxSource: $UrlTabela.data('url'),	    //Url é inserida na página html	dentro do parametro data-url    
		    columns: [                                  //Defina quais são as ORDENS e os NOMES das COLUNAS que receberam os registros do db.
                { "data": "COD" },
                { "data": "Nome" },
                { "data": "Email" },
                { "data": "Tel01" },
                { "data": "Opcoes", className: "dt-opcoes" }       //A classe dt-opcoes deixa a coluna centralizada e com largura personalizada.
		    ],
            order: [[0, "desc"]],                     //Define qual coluna e como iniciará a ordenação. Se quiser.		    
            bProcessing: false,                         //Habilite para que apareça o icone PROCESSANDO.. quando o servidor estiver em processo.
            bServerSide: true,                          //Processar paginação, search e etc no servidor.
		    columnDefs: [{ "orderable": false , "targets": 4 }]    //Ativa apenas a 1º coluna das OPÇÕES de ordenar, utilize desta forma { "orderable": true , "targets": 0 } .  Se quiser inativar uma coluna utilize desta forma { "orderable": false , "targets": 0 } .
            }

// ===== Para adicionar configurações
TabelaConfig.sAjaxSource = $UrlTabela.data('url');                 //Adicione a variavel com as configurações padrões e depois coloque o option , lembrando que ele deve pré-existente na váriavel TabelaConfig. Da seguinte forma  TabelaConfig.sAjaxSource = 'Novo parametro'.

// ===== Executa o DataTable
var $table = $UrlTabela.dataTable(TabelaConfig);                    //Executa o dataTable inicial da página
*/

/* ================================================================================= */
/* ----------------------------------- NOTIFICAÇÃO --------------------------------- */ 
/* ================================================================================= */

        // ===== Utilizando o Pnotify
    function Notificacao(Type, Msg){ 
        var opcoes = {
            type: 'success',
            title: 'Sucesso!',
            text: 'Salvo com sucesso.',            
            icon: 'fa fa-check',
            delay: '2000',
            nonblock: {
                nonblock: true,
                nonblock_opacity: .2
            }
        }
        switch(Type){
            case 'Sucesso':
                opcoes.type = "success";
                opcoes.text = Msg;
                break
            case 'Erro':
                opcoes.type = "error";
                opcoes.icon = 'fa fa-close';
                opcoes.title = "Alerta!";            
                opcoes.text = Msg;            
                break;
            case 'Info':
                opcoes.type = "info";
                opcoes.icon = 'fa fa-info';
                opcoes.title = "Comunicado!";
                opcoes.text = Msg;
                break;
            case 'SemConexao':
                opcoes.type = "warning";
                opcoes.icon = 'fa fa-thumbs-down';
                opcoes.title = "Você está Off-line!";
                opcoes.text = Msg;
                //opcoes.delay = '3000'
                break;
            case 'Conectado':
                opcoes.type = "success";
                opcoes.icon = 'fa fa-thumbs-up';
                opcoes.title = "Online! ;)";
                opcoes.text = Msg;
                break;
        }
        new PNotify(opcoes);
    }


/* ================================================================================= */
/* ------------------------------- MASKEDINPT 9 DIGITO ----------------------------- */ 
/* ================================================================================= */

jQuery("input.telefone")
    .mask("(99) 9999-9999?9")
    .focusout(function (event) {  
        var target, phone, element;  
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        phone = target.value.replace(/\D/g, '');
        element = $(target);  
        element.unmask();  
        if(phone.length > 10) {  
            element.mask("(99) 99999-999?9");  
        } else {  
            element.mask("(99) 9999-9999?9");  
        }  
    });
 

/* ================================================================================= */
/* -------------------------------- MASKEDINPT MONEY ------------------------------- */ 
/* ================================================================================= */

$(".money").maskMoney({
    //prefix: "R$:",
    decimal: ",",
    thousands: ""
});
    
/* ================================================================================= */
/* --------------------------------- MASCARA MONEY --------------------------------- */ 
/* ================================================================================= */
/*
    $('.money').change(function(){
    
        var money = $(this).val();

        money = money.replace(",", ".");

        money = parseFloat(money).formatMoney(2, "", "", ","); 

        $(this).val(money);

    });

    Number.prototype.formatMoney = function(places, symbol, thousand, decimal) {
        places = !isNaN(places = Math.abs(places)) ? places : 2;
        symbol = symbol !== undefined ? symbol : "$";
        thousand = thousand || "";
        decimal = decimal || ".";
        var number = this, 
            negative = number < 0 ? "-" : "",
            i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + "") + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
    };
    
    // Aqui para testar
    var a = 3211000.354;
    
   // alert(a.formatMoney(2, "R$ ", ".", ","));
 */   
/* ================================================================================= */
/* ------------------------------- LIMPAR FORMÚLÁRIO ------------------------------- */ 
/* ================================================================================= */


// ===== Resetar o Formulário 
function LimparForm(FormId) {

        //Pega a Id do Form.
        //var FormId = $(this).closest("form").attr("id");

        //Resetar validação
        $('#' + FormId).validate().resetForm();
        
        //Unckeck todos os checkbox
        $('input:checkbox').removeAttr('checked');

        //Unckeck todos os select
        $('input:radio').removeAttr('checked');

        //Resetar o formulário
        $('#' + FormId).each(function () {
            this.reset();
            
            //--- Zerar o Slim Upload ---
            SlimModal.slim('remove');
            //---------------------------
        }); 
        
        $('.summernote').summernote('code', '');

        //Limpa mensagem de validação.
        $('#' + FormId + ' #erro-validation').html('');

        //Remove a class .has-error que o plugin validate insere.
        $('#' + FormId + ' .has-error').each(function () {
            $('#' + FormId + ' .has-error').toggleClass('has-error', 'Remove');
            
        });

        //Limpa formulário Select2 / selecteTwo
        $('.selectTwo').val(null).trigger('change');
       
}   


//Criada para fácilitar a forma de chamar a função LimparForm().
$('.LimparForm').on('click', function () {

    //Pega a Id do Form.
    var FormId = $(this).closest("form").attr("id");

    //Chama a Função de Limar
    LimparForm(FormId);

});


//Cancelar formulário exibido no palco
$(".Cancelar").click(function (){

    //Oculta o formulário
    $("#ModalCreate").hide();

    //Exibe o dataTabel
    $("#dataTable").show();

});

/* ================================================================================= */
/* ----------------------------------- Validação ----------------------------------- */ 
/* ================================================================================= */

// ===== Configurações Gerais   
var ValidateOpt = {
    ignore: ":hidden:not(.summernote), .note-editable.panel-body", //Validação do summernote

    highlight: function (label) {
        $(label).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    success: function (label) {
        $(label).closest('.form-group').removeClass('has-error');
        label.remove();
    },
    errorPlacement: function (error, element) {
        var placement = element.closest('.input-group');
        if (!placement.get(0)) {
            placement = element;
        }
        if (error.text() !== '') {
            placement.after(error);
        }
    },

}


// ===== Validate Alterar Senha
$("#FormAlterarSenha").validate({
    rules: {
        senha: {
            minlength: 6,
            maxlength: 20
        },
        senhasIgual: {
            equalTo: "#senha"
        }

    }
});


/*
//**Instânciar todos os fomulários que serão validados
$('#FormCreate').validate(ValidateOpt);
*/

/* ================================================================================= */
/* ------------------------------------- MODAL ------------------------------------- */ 
/* ================================================================================= */

// ===== Abrir Modal 

function modalOpen(TPform, ModalId, Id, NomeRegistro) {              //Deve ser passado o Tipo do Modal, ID do modal, a ID do registro para edição/exclusão e o Nome do registro se quiser que ele retorne.

    //Pega a Id do Formulário
    var FormId = $('#' + ModalId + ' form').attr('id');

    //Se existir o parametro NomeRegistro ele inclui no html do modal.
    if (!! NomeRegistro) {
        $('#'+ FormId + ' .NomeRegistro').html("' " + NomeRegistro + " '");
    }

    //Verifica se existe no form e limpa o DropZone
    if ($('#' + ModalId + ' .dropzone').length) { DropZone.removeAllFiles();    $('#' + ModalId + ' .dzArqNome').val(); }
        
        //Ele troca a action para os parametros do Create, Edit ou Delete. 
        if (TPform === 'Create') {

            //Limpa o Form
            LimparForm(FormId);

            //Troca a action para o caminho dentro de data-action-create.
            $('#' + FormId).attr('action', $('#' + FormId).data('action-create'));


            //Verifica se o modal indicado existe, se não mostra o formulário no palco da página.
            if ($('#' + ModalId).hasClass('modal')) {

                // Abrir o modal.
                $('#' + ModalId).modal('show');

            } else {

                // Abrir no palco da página.
                $('#' + ModalId).show();

                //Ocultar a tabela da páginas
                $('#dataTable').hide();

            }


        } else if (TPform === 'Edit') {

            //Unckeck todos os checkbox
            $('input:checkbox').removeAttr('checked');

            //Chama o ajax para mostrar os registros.
            var conectado = Details(FormId, Id);

            //Troca a action para o caminho dentro de data-action-edit, para atualizar os registros.
            $('#' + FormId).attr('action', $('#' + FormId).data('action-edit') + '/' + Id);

            //Se estiver offline ele esconde o modal.
            if (conectado != false) {


                //Verifica se o modal indicado existe, se não mostra o formulário no palco da página.
                if ($('#' + ModalId).hasClass('modal')) {

                    // Abrir o modal.
                    $('#' + ModalId).modal('show');
                    
                } else {

                    // Abrir no palco da página.
                    $('#' + ModalId).show();

                    //Ocultar a tabela da páginas
                    $('#dataTable').hide();

                }



            }
            
        } else {

            //Troca a action para o caminho dentro de data-action-delete.
            $('#' + FormId).attr('action', $('#' + FormId).data('action-delete') + '/' + Id);


            //Verifica se o modal indicado existe, se não mostra o formulário no palco da página.
            if ($('#' + ModalId).hasClass('modal')) {

                // Abrir o modal.
                $('#' + ModalId).modal('show');
                
            } else {

                // Abrir no palco da página.
                $('#' + ModalId).show();

                //Ocultar a tabela da páginas
                $('#dataTable').hide();

            }


        }
                
}


/* ================================================================================= */
/* --------------------------- VISUALIZAR REGISTROS MODAL -------------------------- */
/* ================================================================================= */

// ====== SELECT2: Separa a Id do nome do option
function splitSelect2(string)
{
    return string.split('|');
}

// ===== Visualizar os registros 
function Details(FormId, Id, ModalId) {


    if (EstaConectado()) {

        //Troca a action para o caminho dentro de data-action-create.
        var Action = $('#' + FormId).data('action-details') + '/' + Id;

        $.ajax({
            type: 'post',                                                               //Tipo de envio GET ou POST.
            url: Action,                                                                //Caminho do arquivo no servidor que ira receber e retornar as informações.            
            beforeSend: function () {
                $('body').addClass('loading-overlay-showing');                          //Aparece o gif de loading...                

            },
            success: function (data) {                                                  //Retorno quando houver sucesso.                             

                var obj = JSON.parse(data);                                             //Decodifica o retorno do PHP em JSON e trata como um array. $('#' + ModalId + ' [name=nome]').val(obj.Nome);
                //console.log(obj);

                var n = 0;

                $.each(obj, function (key, val) {                                       //Pega o array obj e separa o indice do valor utilizando as variaveis key e val dentro do $.each.


                    //Inputs do Formulario
                    $('#' + FormId + ' [name=' + key + ']').val(val);                   //Foi removido -> .toLowerCase()
                    $('#' + FormId + ' [name=' + key + ']').attr('checked', true);

                    //SlimCropImage
                    if (key === 'SlimImg') {

                        $.each(val, function (SlimNum, SlimImg) {

                            $('[name="slim[' + SlimNum + ']"]').parent().slim('load', SlimImg);

                        });
                    }

                    //SummerNote
                    if (key === 'Texto' && $('#' + FormId + ' [name=Texto]').hasClass('summernote')) {
                        $('.summernote').summernote('code', val);
                    }

                    //DropZone
                    for (var i = 0; i < key.length; i++) {

                        if ($('#' + FormId + ' .dropzone').length && key === 'DropZone' + i) {                         // Verifica se o existe o .dropzone no formulário

                            //Informa ao plugin os arquivos que serão exibidos
                            var mockFile = { name: val, size: 12345, accepted: true };
                            DropZone.options.addedfile.call(DropZone, mockFile);
                            DropZone.options.thumbnail.call(DropZone, mockFile, "Uploads/Thumbs/thumb-" + val);
                            DropZone.options.complete.call(DropZone, mockFile);
                            DropZone.files.push(mockFile);

                            //ACESSIBILIDADE: torna dinamico o nome do input da legenda para a foto.
                            $(DropZoneId).find('[data-dz-legenda-acessivel]:eq(' + i + ')').attr('name', val.substr(0, val.lastIndexOf('.')));                     // Insere o nome do arquivo no valor de um data-dz-name-legenda-acessivel


                            //remove a barra de upload
                            $('.dz-progress').css('display', 'none');

                            //Adiciona o nome ao input
                            var nomes = $(DropZoneId + ' .dzArqNome').val();
                            nomes = nomes + val + ' ';
                            $(DropZoneId + ' .dzArqNome').val(nomes);


                        }
                    }

                });

            },
            error: function (data) {
                Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
            }
        }).done(function (data) {
            $('body').removeClass('loading-overlay-showing');                       //Desaparece o gif de loading...  
            

            //Para preencher quando utilizar o Plugin Select2 ou SelectTwo
            var obj = JSON.parse(data);     

            $.each(obj, function (key, val) {    

                
                if( $('#' + FormId + ' [name=' + key + ']').hasClass('selectTwo') ){ 

                    if(val > '0'){                            
                        
                        var select2 = splitSelect2(val);                            

                            var newOption = new Option(select2[1], select2[0], true, true);

                            $('#' + FormId + ' [name=' + key + ']').append(newOption).trigger('change');  
                           // DetailsSelect2Params[0] = newOption;                         
                       
                    }
                }

            });

            // ===============================================================


        });
        //* Obs.: console.log(data); para visualizar o resultado via console.
    } else {
        return false;
    }
}


/* ================================================================================= */
/* ----------------------------------- SALVAR AJAX --------------------------------- */
/* ================================================================================= */


// ===== Salvar Formulário Ajax (Create, Edit e Delete)
function SalvarForm(FormId, ModalId, Msg) {

    if (EstaConectado()) {
        
        $.ajax({
            type: 'post',                                                           //Tipo de envio GET ou POST.
            url: $('#' + FormId).attr('action'),                                //Caminho do arquivo no servidor que ira receber e retornar as informações.
            data: $('#' + FormId).serialize(),                                    //Envia as informações para o servidor.
            beforeSend: function () {
                $('body').addClass('loading-overlay-showing');                  //Aparece o gif de loading...       

                if ($('#' + ModalId).is(':visible')) {
                    $('#' + ModalId).modal('hide');                             //Esconde o modal.   
                } else {
                    $('#' + ModalId).modal('show');                             //Mostrar o modal.  
                }
            },
            success: function (data) {                                          //Retorno quando houver sucesso.                                    
                
                var obj = (data != '')? JSON.parse(data) : '';

                if ($('#' + ModalId).hasClass('formPalco') == true) {
                    
                    // Abrir no palco da página.
                    $('#' + ModalId).hide();

                    //Ocultar a tabela da páginas
                    $('#dataTable').show();

                }else{
                    
                    $('#' + ModalId).modal('hide');

                }

                if (typeof $table !== "undefined") {
                    $table.fnDraw(false);                                           //Redesenha o DataTable.        
                }                                                   
                $('#calendar').fullCalendar( 'refetchEvents' );
                
                
                if(obj != "" && obj.situacao == 0){
                    Notificacao('Erro', obj.Msg);
                    $('#' + ModalId).modal('show');
                }else{
                    Notificacao('Sucesso', Msg);
                    LimparForm(FormId);
                }
                                                        //Chama a função de Notificação, insira o tipo da notíficação no 1º parametro e a mensagem no 1º. 


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


// ===== Salva e Valida o conteúdo do Form Create, Edit e Delete   
$(".SalvarForm").click(function () {

    //Pega a Id do Form.
    var FormId = $(this).closest("form").attr("id");

    //Pega o Modal.
    var ModalId = ($(this).closest('.formPalco').hasClass('formPalco'))? $(this).closest('.formPalco').attr('id') : $(this).closest('.modal').attr('id');

    //Pega a mensagem de sucesso.
    var Msg = $('#' + FormId).data('msg-sucesso');

    //Valida o Slim Upload
    var SlimExist = $('#' + FormId + ' .slim').data('state');

    //Valida o DropZone     
    if ($('#' + FormId + ' .dropzone').length) {                                                                  //Atribui o valor true porque tem aguardar a verificação do queuecomplete para deixar prosseguir.
        
        //Verifica se é preciso validar o DropZone, para validar adicione data-dz-valid = "1"
        if ($('#' + FormId + ' .dropzone').data('dz-valid') == 1 || DropZone.getUploadingFiles().length !== 0) {
            var DZValide = 0;

            if (DropZone.files.length === 0) {

                alert('Por favor, adicione fotos na galeria.');

            } else if (DropZone.getUploadingFiles().length !== 0) {

                alert('Por favor, aguarde finalizar o uploads das imagens da galeria.');

            } else if (DropZone.getUploadingFiles().length === 0) {

                DZValide = 1; 
            }
        }

    } else { var DZValide = 1; }
    

    //Valida o Form.
    if ($("#" + FormId).valid(ValidateOpt) && SlimExist != 'empty,error'  && DZValide != 0) {

        //Se estiver validado chama o Ajax para Create, Edit e Delete.
        SalvarForm(FormId, ModalId, Msg);
    }

});

/**
/* =================================================================================
/* ----------------------------------- GERAR EXCEL ---------------------------------
/* =================================================================================
*/
$(document).ready(function () {
    $(".btnExport").click(function () {
        var TabelaId = $(this).data('table-id');
        //Exclui a coluna de opções.
        $("#" + TabelaId).find('.dt-opcoes').css('display', 'none');
        //Gera o PDF.
        $("#" + TabelaId).btechco_excelexport({
            containerid: TabelaId
           , datatype: $datatype.Table
           , filename: 'Tabela_Excel'
        });
        //Exibila novamente a coluna de opções.
        $("#" + TabelaId).find('.dt-opcoes').css('display', '');
    });
});



/* ================================================================================= */
/* ------------------------------------- UPLOAD ------------------------------------ */
/* ================================================================================= */

/* ===== Option Padrão DropZone =====*/
Dropzone.autoDiscover = false;

var OptDropZone = {

    //acceptedFiles: 'image/*',                                           // Arquivos que serão aceitos. Ex.: 'image/*,application/pdf,.psd'

    //url: 'Home/Uploads/Uploads/',                                     // URL -> NOME DO CONTROLE / NOME DA ACTION / NOME DA PASTA / LARGURA DO THUMBNAILS (Não é necessário preencher a largura do Thumbnails).
    //url: $(DropZoneId).attr('action') + '/' + $(DropZoneId).data('pasta-uploads') + '/' + $(DropZoneId).data('largura-thumb'),
    
    dictDefaultMessage: 'Clique para adicionar os arquivos.',           // Mensgem inicial.
    autoDiscover: false,                                                // Descobrir automáticamente todos as Id/Classes que tenham a identificação da váravel DropZoneId.
    maxFilesize: 15,                                                     // Tamanho dos arquivos em MB.

    parallelUploads: 5,                                                 // Qtd de uploads paralelos.
    maxFiles: null,                                                     // Qtd máxima de arquivos. null = ilimitado.
    autoProcessQueue: true,                                            // Upload automáticamente.

    createImageThumbnails: true,                                        // Aparecer o thumbnails.
    thumbnailWidth: 70,
    thumbnailHeight: 70,
        
    addRemoveLinks: true,                                               // Adicionar o botão de excluir.
    dictRemoveFile: 'Remover',                                          // Palavra para aparecer no botão excluir.
    dictFileTooBig: 'Tamanho máximo permitido: {{maxFilesize}}MB.',     // Palavra para aparecer no botão excluir.

    init: function () {

        // ===== Remove os arquivos em Excesso.
        this.on("maxfilesexceeded", function (file) {
            
            this.removeFile(file);
            
            Notificacao('Erro', 'Não é possível anexar mais arquivos. Limite atingido.');

        });


        this.on("addedfile", function (file) {
            // ===== Define o thumb para arquivos que não forem imagens.
            ext = file.name.split('.').pop();
            if (ext === "pdf" ||
                ext === "doc" ||
                ext === "docx"||
                ext === "xls" ||
                ext === "xlsx"||
                ext === "ppt" ||
                ext === "pptx"||
                ext === "txt" ||
                ext === "csv" ||
                ext === "zip" ||
                ext === "rar" ||
                ext === "mp3" ||
                ext === "cdr" ||
                ext === "avi" )
            {
                this.emit('thumbnail', file, 'assets/stylesheets/Icones/' + ext + '.png');
            }       
            // ===== FIM =====
        });

        
        // ===== Retira o nome do arquivo quando ele é removido do painel para upload
        this.on("removedfile", function (file, accepted) {
            
            var ArqExcluir = $(file.previewElement).find('[data-dz-name]').text();

            var str = $(DropZoneId + ' .dzArqNome').val();
            var str_final = str.replace(ArqExcluir + ' ', '');
            $(DropZoneId + ' .dzArqNome').val(str_final);

            //removeImagem('uploads', ArqExcluir);
            // Remove o arquivo do servidor, se já for feito o upload.
        });
        
        
        // ===== Chama uma função ao finalizar cada upload - UTILIZE -> success: quando finalizar cada upload.
        this.on("success", function (file, serverFileName) {                                                                // serverFileName Retorna o nome de cada arquivo do servidor
            this.processQueue(); 

            var arquivosNomes = $(DropZoneId + ' .dzArqNome').val() + serverFileName +' ';                                  // Adiciona o nome no input hidden para adicionar no db.
            $(DropZoneId + ' .dzArqNome').val(arquivosNomes);
            $(file.previewElement).find('[data-dz-name]').text(serverFileName);                                             // Insere o nome do arquivo no valor de um data-dz-name-remove

        });  
    }
}

/*
// ===== Função para remover o arquivo do servidor.
function removeImagem(pasta, arquivo){
    $.ajax({
        type: 'get',                                                               
        url: 'Utilities/DeleteDocument/' + pasta + '/' + arquivo,                             
    });
}
*/
/* ------------------------------------------------------ */
/* ================== Modelo DropZone =================== */
/* ------------------------------------------------------ *//*
Dropzone.autoDiscover = false;                                          // Inabilita a discoberta automática do DropZone e aceita apenas pela Id ou Classe.

// ===== Option do DropZone envio automático.
var DropZoneId = '#Upload01';                                           //Pode colocar a id que quiser, como padrão coloquei o nome da classe do css para aplicar a todos os uploads.
var DropZone = new Dropzone(DropZoneId, {

    //acceptedFiles: 'image/*',                                           // Arquivos que serão aceitos. Ex.: 'image/*,application/pdf,.psd'

    //url: 'Home/Uploads/Uploads/',                                     // URL -> NOME DO CONTROLE / NOME DA ACTION / NOME DA PASTA / LARGURA DO THUMBNAILS (Não é necessário preencher a largura do Thumbnails).
    url: $(DropZoneId).attr('action') + '/' + $(DropZoneId).data('pasta-uploads') + '/' + $(DropZoneId).data('largura-thumb'),
    
    dictDefaultMessage: 'Clique para adicionar os arquivos.',           // Mensgem inicial.
    autoDiscover: false,                                                // Descobrir automáticamente todos as Id/Classes que tenham a identificação da váravel DropZoneId.
    maxFilesize: 15,                                                     // Tamanho dos arquivos em MB.

    parallelUploads: 5,                                                 // Qtd de uploads paralelos.
    maxFiles: null,                                                     // Qtd máxima de arquivos. null = ilimitado.
    autoProcessQueue: false,                                            // Upload automáticamente.

    createImageThumbnails: true,                                        // Aparecer o thumbnails.
    thumbnailWidth: 70,
    thumbnailHeight: 70,
        
    addRemoveLinks: true,                                               // Adicionar o botão de excluir.
    dictRemoveFile: 'Remover',                                          // Palavra para aparecer no botão excluir.
    dictFileTooBig: 'Tamanho máximo permitido: {{maxFilesize}}MB.',     // Palavra para aparecer no botão excluir.

    init: function () {
        this.on("addedfile", function (file) {
            // ===== Define o thumb para arquivos que não forem imagens.
            ext = file.name.split('.').pop();
            if (ext === "pdf" ||
                ext === "doc" ||
                ext === "docx"||
                ext === "xls" ||
                ext === "xlsx"||
                ext === "ppt" ||
                ext === "pptx"||
                ext === "txt" ||
                ext === "csv" ||
                ext === "zip" ||
                ext === "rar" ||
                ext === "mp3" ||
                ext === "cdr" ||
                ext === "avi" )
            {
                this.emit('thumbnail', file, 'assets/stylesheets/Icones/' + ext + '.png');
            }            
            // ===== FIM =====
        });

        
        // ===== Chama uma função ao finalizar cada upload - UTILIZE -> success: quando finalizar cada upload.
        this.on("success", function (file, serverFileName) {
            this.processQueue();                                                            // Inicia o envio dos arquivos que estão aguardando.  
            //alert(serverFileName);                                                        // Retorna o nome de cada arquivo do servidor
        });
        

        // ===== Remove todos os arquivos após a finalização de todos os uploads - UTILIZE -> queuecomplete: quando finalizar todos os uploads. 
        /*
        this.on("queuecomplete", function (file) {      
            $(DropZoneId + ' .dz-image-preview').fadeOut(2000, function () {
                this.removeAllFiles(file);                                                  // Remove todos os arquivos após a conclusão.
            });

            setTimeout(function () {                                        
                $(DropZoneId + ' .dropzone').removeClass('dz-started');                     // Reaparece o texto padrão para adicionar arquivos na Div.
            }, 2100);
        });
        */
/*
    }
});
*/
// ===== Modelo de botão para iniciar o Upload 
/* $('.StartUpload').click(function () {  DropZone01.processQueue(); }); */

/*
// #Upload01
$('.StartUpload').click(function () {                                                    
    DropZone.processQueue();                                                           // Botão para iniciar o upload.
});

$('.ClearUpload').click(function () {
    DropZone.removeAllFiles();                                                           // Botão para iniciar o upload.
});

*/
//****https://stackoverflow.com/questions/24859005/dropzone-js-how-to-change-file-name-before-uploading-to-folder

/* ------------------------------------------------------------ */
/* =========================== Fim ============================ */
/* ------------------------------------------------------------ */

/* ================================================================================= */
/* ----------------------------------- SLIM UPLOAD --------------------------------- */
/* ================================================================================= */

var SlimModal = $('.SlimModal').slim();
$('.slim').slim();


/* ==== Logout ===== */
$('.OpenModalLogout').click(function(){
    $('#ModalLogout').modal('show');
});
function Logout() {

    if (EstaConectado()) {

        $.ajax({
            dataType: 'json',
            type: 'post',                                                               //Tipo de envio GET ou POST.
            url: 'Login/Logout/',                                                       //Caminho do arquivo no servidor que ira receber e retornar as informações.
            beforeSend: function () {
                $('body').addClass('loading-overlay-showing');                          //Aparece o gif de loading... 
            },
            success: function (data) {                                                  //Retorno quando houver sucesso.
                window.location.href = data.url;                                        //Redireciona para a pasta raiz, após iniciar a sessão.                
            },
            error: function (data) {
                $('body').removeClass('loading-overlay-showing');
                Notificacao('Erro', 'Não foi possível concluir a operação.<br>Por favor, tente novamente.');
            }
        }).done(function (data) {
            $('body').removeClass('loading-overlay-showing');                           //Desaparece o gif de loading...        
        });

    }

}

/* ================================================================================= */
/* ----------------------------------- SUMMER NOTE --------------------------------- */
/* ================================================================================= */
 
OptSummnerNote = {
    lang: 'pt-BR',
    dialogsInBody: true,
    height: 300,
    toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen']],
        ['help', ['help']]
    ],
    callbacks: {
        onImageUpload: function (files) {
            sendFile(files[0]);
        },
        onMediaDelete : function (target) { 
            //console.log($target.attr('src'));   // get image url 
            //deleteFile(target.attr('src'));
        }
    }

}

/* ===== Enviar imagem para o servidor */
function sendFile(files) {
    var form_data = new FormData(); 
    form_data.append('file', files);
    $.ajax({
        data: form_data,
        type: "POST",
        url: 'Utilities/SummerNoteImg',
        cache: false,
    contentType: false,
    processData: false,
    beforeSend: function () {
        $('body').addClass('loading-overlay-showing');                          //Aparece o gif de loading...                

    },
    success: function(url) {
        $('.summernote').summernote('insertImage', url);
    }
    }).done(function (data) {
        $('body').removeClass('loading-overlay-showing');                       //Desaparece o gif de loading...  
    });
}

function deleteFile(target)
{

    $.ajax({
        data: { src: target },
        type: "POST",
        url: 'Utilities/SummerNoteImgDelete',
        cache: false,
    contentType: false,
    processData: false,
    });


}





/* ===== Instanciar summernote */
//$('.summernote').summernote(OptSummnerNote);


/* ================================================================================= */
/* -------------------------------- OPTIONS FRAMEWORK ------------------------------ */
/* ================================================================================= */

// ===== CHECKALL
    $('.CheckAll').click(function (e) {
        //Pega a Classe da lista do Checkbox
        var Classe = $(this).data('checkbox-class');

        if ($(this).is(':checked'))
            $('.' + Classe).prop('checked', true);
        else
            $('.' + Classe).prop('checked', false);
    });
/*
<-- CheckAll. -->
<div class="checkbox-custom checkbox-default" style="margin-left:12px;">
<input type="checkbox" id="checkboxStylo1" class="CheckAll" data-checkbox-class="Ck-Categoria" title="Marcar todos" style="margin-left:12px;"> 
<label for="checkboxStylo1">Marcar todos</label> </div> 

<-- Checkbox que será marcado. -->
<div class="checkbox-custom checkbox-default">
<input type="checkbox" id="checkboxStylo1"  class="Ck-Categoria"> 
<label for="checkboxStylo1">Tipo 01</label> </div>
*/
//===============

/* ================================================================================= */
/* ----------------------- VERIFICAÇÃO CPF JQUERY VALIDATE ------------------------- */
/* ================================================================================= */

jQuery.validator.addMethod("cpf_cnpj", function(value, element) {
    value = jQuery.trim(value);
 
     value = value.replace('.','');
     value = value.replace('.','');
     cpf = value.replace('-','');
     while(cpf.length < 11) cpf = "0"+ cpf;
     var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
     var a = [];
     var b = new Number;
     var c = 11;
     for (i=0; i<11; i++){
         a[i] = cpf.charAt(i);
         if (i < 9) b += (a[i] * --c);
     }
     if ((x = b % 11) < 2) { a[9] = 0 } else { a[9] = 11-x }
     b = 0;
     c = 11;
     for (y=0; y<10; y++) b += (a[y] * c--);
     if ((x = b % 11) < 2) { a[10] = 0; } else { a[10] = 11-x; }
 
     var retorno = true;
     if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) retorno = false;
 
     return this.optional(element) || retorno;
 
 }, "Informe um CPF válido");
