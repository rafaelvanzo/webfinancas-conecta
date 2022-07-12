var crud = false;


 /**
  * Data table custeio
  */
 var dTableCusteio = $('#dTable-custeio').dataTable({
    bProcessing: true,
    bServerSide: true,
    sAjaxSource: 'php/Route.php?Controller=Custeio&Action=DataTable',
    bJQueryUI: true,
    bAutoWidth: false,
    sPaginationType: "full_numbers",
    bFilter: false,
    //bLengthChange: false,
    bSort: false,
    aoColumns: [
        { "mData": "nome", "sClass": "updates newUpdate left" },
        { "mData": "tempo", "sClass": "updates newUpdate center" },
        { "mData": "tpCusto", "sClass": "updates newUpdate center", "width": "150px" },
        { "mData": "valorCusteio", "sClass": "updates newUpdate right",  },
        { "mData": "valorVariavel", "sClass": "updates newUpdate right",  },
        { "mData": "valorTotal", "sClass": "updates newUpdate right" },
        { "mData": "valorPraticado", "sClass": "updates newUpdate right" },
        { "mData": "opcoes", "sClass": "updates newUpdate center" }
    ],
    oLanguage: {
        "sLengthMenu": "<span>Mostrar:</span> _MENU_",
        "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
    },
    fnServerParams: function (aoData) {

        //var filtro = filtroParams();

        if (crud) {
            var oSettings = this.fnSettings();
            oSettings._iDisplayStart = iDisplayStart;
        } else {
            var oSettings = this.fnSettings();
            iDisplayStart = oSettings._iDisplayStart;
        }

        //start: Período do filtro
        var data = $('#mes').val();
        data = data.split('/');
        var mes = parseInt(data[0]);
        var ano = data[1];
        //end: Período do filtro

        aoData.push(/*{ "name": "filtro", "value": filtro },*/
                { "name": "iDisplayStart", "value": iDisplayStart },
                { "name": "mes", "value": mes },
                { "name": "ano", "value": ano }
            );

    },
    fnDrawCallback: function () {
        crud = false;
        //$('#btn-filtro').button('reset');
    }


});



$(document).ready(function () {



    /**
     * Modal custeio
     */
    $("#modal-custeio").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                $('#form-custeio').submit();
            },
            Cancelar: function () {
                $(this).dialog("close");
                excluirInputsMaterial();
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });



    $("#open-modal-custeio").click(function (e) {
        e.preventDefault();
        $('#form-custeio').attr('action', 'Create');
        FormReset('form-custeio');
        $('#modal-custeio').dialog("option", "title", 'Novo Custeio');
        $("#modal-custeio").dialog("open");

        excluirInputsMaterial();
        
    })
    /**
     * =================================================================
     */


    /**
     * Botão Add material
     */
    $('.addMaterial').click(function(){ 
        
        addMaterial();

    });
     /**
     * =================================================================
     */

     /**
     * Botão Add Lancamentos
     */
    $('.addLancamentos').click(function(){ 
        
        addLancamentos();

    });
     /**
     * =================================================================
     */



    /**
    * Modal lançamentos
    */
       $("#modal-lancamentos").dialog({
        autoOpen: false,
        modal: true,
        position: { my: "top", at: "top+5%", of: window }, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        resizable: 'false',
        buttons: {
            Salvar: function () {
                editLancamentos();
                //$('#form-lancamentos').submit();
            },
            Cancelar: function () {
                $(this).dialog("close");
                excluirInputsLanc();
            }
        },
        //beforeClose: function( event, ui ) { resetAbasDialog( $( this ).attr('id') ); }  //resetar a posição das abas dentro do dialog
    });


    
    $("#open-modal-lancamentos").click(function (e) {
        e.preventDefault();
        //$('#form-lancamentos').attr('action', 'Edit');
        FormReset('form-lancamentos');
        //$('#modal-lancamentos').dialog("option", "title", 'Novo Custeio');
        var title = $("#mes").val();
        $("#modal-lancamentos").dialog('option', 'title', 'Lançamentos que compõe o custeio do mês ' + title);
        $("#modal-lancamentos").dialog("open");

        excluirInputsLanc();

        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Custeio&Action=DetailsLancamentos',
            dataType: 'json',
            data: {
                date: $('#mes').val()
            },
            success: function (data) { 


                $('#lancamentos').html(data.lancamentos);

                var total = data.lancamentoTotal; 
                var i = 1;

                if(total >= 1)
                { 
                    while(i <= total)
                    {  
                        addLancamentos();

                        i++;
                    }
                }

                $.each(data, function( key, value ) {                   

                   
                   if(key == 'nome'){
                        $('#funcao').prop('disabled', 'true');
                        $('#nome_cg').css('display', 'block');
                   }

                        //Add value dentro do input
                        $('#form-lancamentos [name='+ key +']').val(value);

                  });
               
                $("span.aguarde, div.aguarde").css("display", "none");

            },
        })
        
    })
     /**
     * =================================================================
     */

       
	/**
     * Auto Complete - Custeio 
     */
    $(".funcao_buscar").autocomplete({
        minLength: 0,
        source: function (request, response) {
            //var term = request.term;
            //if ( term in cache ) {
            //response( cache[ term ] );
            //return;
            //}
            $.getJSON('php/Route.php?Controller=Custeio&Action=AutoCompleteFuncao', request, function (data, status, xhr) {
                //cache[ term ] = data;
                response(data);
            });
        },
        search: function (event, ui) {
            var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
            $('#' + campo_id + '_aguarde').css('display', 'block');
        },
        response: function (event, ui) {
            var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
            $('#' + campo_id + '_aguarde').css('display', 'none');
            //if(ui.content.length==0){
            //alert('nenhum resultado encontrado');
            //}
            //alert('resposta');
        },
        select: function (event, ui) {
            var campo_id = $(this).attr('name');
            $('#' + campo_id).val(ui.item.id);
            if (ui.item.id == "add")
                IncluirCusteioAc(ui.item.value, campo_id);
            $('#' + campo_id + '_cg').css('display', 'block');
            $(this).attr('disabled', 'disabled');
            fadeOut($(this).attr('id'));
        }
    });
     /**
     * =================================================================
     */

    /**
     * Autocomplete função
     */
    var IncluirCusteioAc = function (nome, campo_id) {
        //$("span.aguarde, div.aguarde").css("display", "block");
        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Custeio&Action=IncluirCusteioAc',
            data: { nome: nome },
            dataType: 'json',
            success: function (data) {
                //data = JSON.parse(data);
                document.getElementById(campo_id).value = data.funcao_id;
                //$("span.aguarde, div.aguarde").css("display", "none");
            }
        });
    }
    /**
     * =================================================================
     */



    /**
     * Adicionar Custeio submit form
     */
    $('#form-custeio').on('submit', function (e) {

        e.preventDefault();

        if ($(this).valid()) {

            $("span.aguarde, div.aguarde").css("display", "block");
            
            var action = $('#form-custeio').attr('action');

            var data = $(this).serialize(); 

            $('#modal-custeio').dialog("close");

            $.ajax({
                type: 'post',
                url: 'php/Route.php?Controller=Custeio&Action=' + action,
                data: data,
                dataType: 'json',
                success: function (data) { console.log(data);
                    if (data.status == 1) {
                        crud = true;
                        dTableCusteio.fnDraw();
                        ShowCusto();
                        notificacao(1, data.msg);
                    } else {
                        notificacao(2, data.msg);
                        $("#modal-custeio").dialog("open");
                    }
                    $("span.aguarde, div.aguarde").css("display", "none");
                },
            })
        }
    });
    /**
     * =================================================================
     */



    /**
     * Filtrar honorários por mês
     */   
    
    $('#btn-pesquisar').on('click', function () {
        dTableCusteio.fnDraw();
        ShowCusto();
    });
    /**
     * =================================================================
     */



    /**
     * Inicialilza a função para visualizar os custos
     */
    ShowCusto();

});



 /**
  *  Add material
  */
 function addMaterial()
 {
     var qtd = $(".material:input").length + 1;

     var element = "'addMt-" + qtd + "'";

     var html = '<div class="formRow addMt addMt-'+ qtd +'"><span class="span4"><label>Despesa adicional:</label><input type="text" name="material' + qtd + '" class="required material" value="" /></span> <span class="span2"><label>Vencimento:</label><input type="text" name="dt_vencimento' + qtd + '" class=" maskDate " value=""/></span> <span class="span1"> <label>Qtd.:</label> <input type="text" name="qtdMateria' + qtd + '" class="qtdCalculo' + qtd + '" value="1" /> </span> <span class="span2"> <label>Custo unitário:</label> <input type="text" name="custoUnitario' + qtd + '" class="moeda custoCalculo' + qtd + '" value="0,00" /> </span><span class="span2"> <label>Valor:</label> <input type="text" name="valor' + qtd + '" class="moeda valorFinal' + qtd + '" value="0,00" readonly="readonly"/> </span> <span class="span1"><label style="color:#fff;"> .... </label> <a href="javascript://" title="Excluir" class="smallButton btTBwf redB excluir" onclick="excluirInput('+ element +');"><img src="images/icons/light/close.png" width="10"></a> </span></div> ';

     $('.listMaterial').before(html);

     /** instancia novamente o format da moeda */
     $('.moeda').priceFormat({
         prefix: '',
         centsSeparator: ',',
         thousandsSeparator: '',
     });

     $('.qtdCalculo'+ qtd).attr('onkeyup', 'calculoCusto(' + qtd + ')');
     $('.custoCalculo'+ qtd).attr('onkeyup', 'calculoCusto(' + qtd + ')');

     $(".maskDate").mask("99/99/9999");

 }
 /**
 * =================================================================
 */

 /**
  *  Add lancamento custo
  */
 function addLancamentos()
 {
     var qtd = $(".despesa:input").length + 1;

     var element = "'addLanc-" + qtd + "'";

     var html = '<div class="formRow addLanc addLanc-'+ qtd +'"><span class="span6"><label>Despesa adicional:</label><input type="text" name="descricao' + qtd + '" class="required despesa" value="" /></span> <span class="span2"><label title="Vencimento estiver 00/00/0000 ele permanecerá ativo até inserir uma data de vencimento">Vencimento</label><input type="text" name="dt_vencimento' + qtd + '" class="maskDate" value="" /></span> <span class="span3"> <label>Valor:</label> <input type="text" name="valor' + qtd + '" class="moeda" value="0,00" /> </span> <span class="span1"><label style="color:#fff;"> .... </label> <a href="javascript://" title="Excluir" class="smallButton btTBwf redB excluir" onclick="excluirInput('+ element +');"><img src="images/icons/light/close.png" width="10"></a> </span></div> ';

     $('.listLancamentos').before(html);

     /** instancia novamente o format da moeda */
     $('.moeda').priceFormat({
         prefix: '',
         centsSeparator: ',',
         thousandsSeparator: '',
     });

     $(".maskDate").mask("99/99/9999");

 }
 /**
 * =================================================================
 */


 /**
  *  Calculo unitário 
  */
 function calculoCusto(num)
{ 
   setTimeout(function(){

        var qtd = $('.qtdCalculo' + num).val();
        var custo = $('.custoCalculo' + num).val(); 

            custo = custo.replace(".", ""); 
            custo = custo.replace(",", ".");              

            custo = Number(custo); 
            qtd = Number(qtd);

            var valor = qtd * custo;

            valor = valor.toString().replace(".", ",");  

        $('.valorFinal' + num).val(valor);
        
    },100);

}
/**
 * =================================================================
 */

 /**
  * Excluir material/Lancamentos especifico
  */
 function excluirInput(param)
 {
    $('.' + param).remove();
 }

/**
 * =================================================================
 */

 /** 
  * Resetar Form
  */
function FormReset(form) { 

    var validator = $('#' + form).validate();
    validator.resetForm();
    $("#" + form + " input[name='banco_id']").val("");
    $('span.check-green').css('display', 'none');
    $('#funcao').prop('disabled', false);

    //resetar abas
    $('#abas-' + form + ' a:first').tab('show');

    //$('#' + form + ' div.MaisOpcoes').attr('class', 'title closed MaisOpcoes normal');
    //$('#' + form + ' div.body:eq(0)').css('display', 'none');
}

/**
 * =================================================================
 */

/**
 * Excluir input do modal custeio
 */
function excluirInputsMaterial()
{
    $(".addMt").remove();
}

/**
 * =================================================================
 */

 
/**
 * Excluir input do modal custeio
 */
function excluirInputsLanc()
{
    $(".addLanc").remove();
}

/**
 * =================================================================
 */


/**
 * Mensagem de notificação
 */
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

/**
 * =================================================================
 */


/**
 * Detalhes
 */
function details(id)
{
    $('#form-custeio').attr('action', 'Edit');
    FormReset('form-custeio');
    $('#modal-funcionario').dialog("option", "title", 'Editar Custeio');
    $("#modal-custeio").dialog("open");
    excluirInputsMaterial();

    $.ajax({
        type: 'post',
        url: 'php/Route.php?Controller=Custeio&Action=Details',
        data: { 
            id: id,
            date: $('#mes').val()
        },
        dataType: 'json',
        success: function (data) { 

            var total = data.materialTotal;
            var i = 1;

            if(total >= 1)
            { 
                while(i < total)
                {  
                    addMaterial();

                    i++;
                }
            }

            $.each(data, function( key, value ) {                   

                
                if(key == 'nome'){
                    $('#funcao').prop('disabled', 'true');
                    $('#nome_cg').css('display', 'block');
                }

                    //Add value dentro do input
                    $('#form-custeio [name='+ key +']').val(value);

                });
            
            $("span.aguarde, div.aguarde").css("display", "none");

        },
    })

}

/**
 * =================================================================
 */


/**
 * Excluir custeio
 */
function excluir()
{
    if(confirm("Deseja realmente excluir o custeio?"))
    {
        $.ajax({
            type: 'post',
            url: 'php/Route.php?Controller=Custeio&Action=Excluir',
            data: { 
                id: $('.excluir').data('excluir-id')
            },
            dataType: 'json',
            success: function (data) { 
                if (data.status == 1) {
                    crud = true;
                    dTableCusteio.fnDraw();
                    ShowCusto();
                    notificacao(1, "Custeio excluido com sucesso.");
                } else {
                    notificacao(2, "Erro! Por favor tente novamente.");
                }
                $("span.aguarde, div.aguarde").css("display", "none");
            },
        })
    }

}

/**
 * =================================================================
 */

 /**
  * Atualizar lançamentos e configuração do modal composição custeio
  */
 function editLancamentos()
 {
    $.ajax({
        type: 'post',
        url: 'php/Route.php?Controller=Custeio&Action=EditLancamentos',
        data: $('#form-lancamentos').serialize(), 
        dataType: 'json',
        beforeSend: function(){
            $("#modal-lancamentos").dialog("close");
            $("span.aguarde, div.aguarde").css("display", "block"); 
        },
        success: function (data) {           
            $("span.aguarde, div.aguarde").css("display", "none");
            notificacao( 1, data.msg);
            dTableCusteio.fnDraw(false); 
            ShowCusto();
        },
    })
 
    
 }

 

function ShowCusto()
{
    $.ajax({
        type: 'post',
        url: 'php/Route.php?Controller=Custeio&Action=ShowCusto',
        data: { 
            mes: $('#mes').val() 
        }, 
        dataType: 'json',
        beforeSend: function(){
            $("#modal-lancamentos").dialog("close");
            $("span.aguarde, div.aguarde").css("display", "block"); 
        },
        success: function (data) {           
            $("span.aguarde, div.aguarde").css("display", "none");

            $('.total').html(data.custoTotal);
            $('.dias').html(data.custoDia); 
            $('.horas').html(data.custoHora);
            $('.minutos').html(data.custoMinuto);
        },
    })
}


 

 /**
  * ALTERAR A DATA ELE DISPARAR A FUNÇÃO DE ATUALIZAR A LISTA
  */

  $('#mes').change(function(){
    $('#btn-pesquisar').click();
  });


  /**
   * Validar Nome do custeio
   */
  $('.input-buscar').focusout(function(){
    
    var id = $('.input-buscar-hidden').val();

    if(id.length == 0)
    $('.input-buscar').val('');
  });