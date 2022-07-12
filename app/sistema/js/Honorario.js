var crud = false;

$(document).ready(function () {

    //Data table honorários
    var dTableHonorarios = $('#dTable-honorarios').dataTable({
        bProcessing: true,
        bServerSide: true,
        sAjaxSource: 'php/Route.php?Controller=Honorario&Action=DataTable',
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        bFilter: false,
        //bLengthChange: false,
        bSort: false,
        aoColumns: [
            { "mData": "dt_vencimento", "sClass": "updates newUpdate center" },
            { "mData": "valor", "sClass": "updates newUpdate right" },
            { "mData": "compensado", "sClass": "updates newUpdate center" },
            { "mData": "visualizado", "sClass": "updates newUpdate center" },
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

    //Download honorários
    $('#dTable-honorarios').on('click', '.download', function (e) {
        e.preventDefault();
        var link = $(this).data('link');
        var id = $(this).data('id');
        window.open('php/Route.php?Controller=Honorario&Action=DownloadHonorario&link=' + link + '&id=' + id, '_self');
        dTableHonorarios.fnDraw();
    });

    //Filtrar honorários por mês
    $('#btn-pesquisar').on('click', function () {
        dTableHonorarios.fnDraw();
    });
});