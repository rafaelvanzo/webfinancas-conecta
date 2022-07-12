var crud = false;



$(document).ready(function () {

       
    //Data table arquivos
    var dTableArquivos = $('#dTableArquivos').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=CarneLeao&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        //bFilter: false,
        //bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "id", "sClass": "updates newUpdate dt-center" },
            { "mData": "nome", "sClass": "updates newUpdate" },
            { "mData": "email", "sClass": "updates newUpdate" },
            { "mData": "opcoes", "sClass": "updates newUpdate dt-center" }
        ],
        oLanguage: {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        },
        fnDrawCallback: function () {
            crud = false;
            //$('#btn-filtro').button('reset');
        }
    }).fnSetFilteringDelay();

    //Modal arquivo
    $("#modal-arquivo").dialog({
        autoOpen: false,
        modal: true,
        width: '750px',
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        /*buttons: {
            Salvar: function () {
                $('#form-arquivo').submit();
            },
            Cancelar: function () {
                $(this).dialog("close");
            }
        },*/
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });
})

//Mensagem de notificação
function notificacao(situacao, mensagem) {
    if (situacao == 1) {
        $('.nSuccess p').html(mensagem);
        $('.nSuccess').slideDown();
        setTimeout(function () { $('.nSuccess').slideUp() }, 4000);
    } else {
        $('.nWarning p').html(mensagem);
        $('.nWarning').slideDown();
        setTimeout(function () { $('.nWarning').slideUp() }, 4000);
    }
}

//Abrir o modal
function OpenModal(Id, Nome, CPF) {

    $('.Nome').html(Nome);
    $('.CPF').html(CPF);
    $('.cliente_id').val(Id);

    $('.profissional_CPF').val(CPF);

    $('.Erro').html('');

    /* ===== Verifica se o cliente esta cadastrado como Pessoa Física no sistema ===== */
    if (CPF.length > 14) {

        notificacao(0, "Cliente cadastrado como <b>pessoa Jurídica</b>, para continuar altere o cadastro para <b>pessoa física</b>.");

    } else {

        $("#modal-arquivo").dialog("open");

    }
}

//Gerar arquivo exportação para IRPF (Carne Leão)
function ExportarArquivo() {

    var cliente_id = $('.cliente_id').val();
    var Profissional_CPF = $('.profissional_CPF').val();
    var AnoDeclaracao = $('.AnoDeclaracao').val();

    $("span.aguarde, div.aguarde").css("display", "block");

    $.ajax({
        type: 'post',
        url: 'php/Route.php?Controller=CarneLeao&Action=ExportarArquivo',
        data: {
            cliente_id: cliente_id,
            Profissional_CPF: Profissional_CPF,
            AnoDeclaracao: AnoDeclaracao
        },
        //dataType: 'json',
        success: function (data) {            
            var dados = $.parseJSON(data);

            if (dados.status === 0) {

                $('.Erro').html(dados.Retorno);

            } else {

                window.open("https://app.webfinancas.com/contador/ArquivosCarneLeao/download.php?download=" + dados.Retorno, '_parent');

                $("#modal-arquivo").dialog("close");

                notificacao(dados.status, "Arquivo gerado com sucesso.");
            }

            
            $("span.aguarde, div.aguarde").css("display", "none");
        },
    })

}


//Gerar arquivo de impressão do Carne Leão
function ImprimirMovimento() {

    var cliente_id = $('.cliente_id').val();
    var Profissional_CPF = $('.profissional_CPF').val();
    var AnoDeclaracao = $('.AnoDeclaracao').val();

    $("span.aguarde, div.aguarde").css("display", "block");

    $.ajax({
        type: 'post',
        url: 'php/Route.php?Controller=CarneLeao&Action=ImprimirMovimento',
        data: {
            cliente_id: cliente_id,
            Profissional_CPF: Profissional_CPF,
            AnoDeclaracao: AnoDeclaracao
        },
        //dataType: 'json',
        success: function (data) {
            var dados = $.parseJSON(data);

            if (dados.status === 0) {

                $('.Erro').html(dados.Retorno);

            } else {

                window.open("https://www.webfinancas.com/contador/ArquivosCarneLeao/download.php?download=" + dados.Retorno, '_blank');

                $("#modal-arquivo").dialog("close");

                notificacao(dados.status, "Arquivo gerado com sucesso.");
            }


            $("span.aguarde, div.aguarde").css("display", "none");
        },
    })

}
