<?php

echo '
<script type="text/javascript" src="modulos/geral/js/funcoes.js"></script>

<script type="text/javascript">

document.getElementById("saldoTotal").innerHTML = "'.$_SESSION['total_disponivel'].'";

/*
===========================================================================================
GRÁFICO DE BARRAS
===========================================================================================
*/

$(function () {
    var previousPoint;
';

$dt_ini = date('Y-m').'-01';
$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
$dt_fim = date('Y-m-d',$dt_fim);

/*
$query_receitas = "
	select month(dt_compensacao) mes, sum(valor) valor
	from lancamentos 
	where tipo = 'R' 
		and compensado = 0
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
	group by month(dt_compensacao)
";
$array_receitas = $db->fetch_all_array($query_receitas);

$query_despesas = "
	select month(dt_compensacao) mes, sum(valor) valor
	from lancamentos 
	where tipo = 'P' 
		and compensado = 0
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
	group by month(dt_compensacao)
";
$array_despesas = $db->fetch_all_array($query_despesas);
*/

$query_receitas_previstas = "
select sum(valor) valor 
from(
	(select sum(valor) valor
	from lancamentos
	where tipo = 'R' 
		and compensado = 0
		and dt_vencimento >= '".$dt_ini."'
		and dt_vencimento <= '".$dt_fim."')
	
	union

	(select sum(valor) valor
	from lancamentos_recorrentes_temp
	where tipo = 'R')
) receitas_previstas
";
$receitas_previstas = $db->fetch_assoc($query_receitas_previstas);

$query_receitas_realizadas = "
	select sum(valor) valor
	from lancamentos
	where tipo = 'R' 
		and compensado = 1
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
";
$receitas_realizadas = $db->fetch_assoc($query_receitas_realizadas);

$query_despesas_previstas = "
select sum(valor) valor 
from(
	(select sum(valor) valor
	from lancamentos
	where tipo = 'P' 
		and compensado = 0
		and dt_vencimento >= '".$dt_ini."'
		and dt_vencimento <= '".$dt_fim."')
	
	union

	(select sum(valor) valor
	from lancamentos_recorrentes_temp
	where tipo = 'P')
) despesas_previstas
";
$despesas_previstas = $db->fetch_assoc($query_despesas_previstas);

$query_despesas_realizadas = "
	select sum(valor) valor
	from lancamentos
	where tipo = 'P' 
		and compensado = 1
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
";
$despesas_realizadas = $db->fetch_assoc($query_despesas_realizadas);

if($receitas_previstas['valor']==''){
	$receitas_previstas = 0;
}else{
	$receitas_previstas = $receitas_previstas['valor'];
}

if($receitas_realizadas['valor']==''){
	$receitas_realizadas = 0;
}else{
	$receitas_realizadas = $receitas_realizadas['valor'];
}

if($despesas_previstas['valor']==''){
	$despesas_previstas = 0;
}else{
	$despesas_previstas = $despesas_previstas['valor'];
}

if($despesas_realizadas['valor']==''){
	$despesas_realizadas = 0;
}else{
	$despesas_realizadas = $despesas_realizadas['valor'];
}


$receitas_total = $receitas_previstas + $receitas_realizadas;
$despesas_total = $despesas_previstas + $despesas_realizadas;
$saldo_total = $receitas_total - $despesas_total;

echo '
    var d1 = [[1,'.$receitas_realizadas.']];
    var d2 = [[1,'.$receitas_previstas.']];
    var d3 = [[1,'.$receitas_total.']];

    var d4 = [[1,'.$despesas_realizadas.']];
    var d5 = [[1,'.$despesas_previstas.']];
    var d6 = [[1,'.$despesas_total.']];

    var d7 = [[1,'.($receitas_realizadas-$despesas_realizadas).']];
    var d8 = [[1,'.$saldo_total.']];
';

if($receitas_realizadas>=$despesas_realizadas){
	$saldo_realizado_cor = "#096AC0";
}else{
	$saldo_realizado_cor = "#A73939";
}
#6495
if($saldo_total>=0){
	$saldo_total_cor = "#2b6893";
}else{
	$saldo_total_cor = "#AF0000";
	
}

/*
echo '
    var d1 = [[1,10],[2,20],[3,30]];
    var d2 = [[1,5],[2,10],[3,90]];
    var d3 = [[1,50],[2,70]];

';
*/

echo '
    var ds1 = new Array();
		var ds2 = new Array();
		var ds3 = new Array();

    ds1.push({
        data:d1,
        bars: {
          order: 1
        }
    });
    ds1.push({
        data:d2,
        bars: {
            order: 2
        }
    });
    ds1.push({
        data:d3,
        bars: {
            order: 3
        }
    });

    ds2.push({
        data:d4,
        bars: {
            order: 1
        }	
    });
    ds2.push({
        data:d5,
        bars: {
            order: 2
        }
    });
    ds2.push({
        data:d6,
        bars: {
            order: 3
        }
    });
		
    ds3.push({
        data:d7,
        bars: {
            order: 1
        }	
    });
    ds3.push({
        data:d8,
        bars: {
            order: 2
        }
    });

    //tooltip function
    function showTooltip(x, y, contents, areAbsoluteXY) {
        var rootElt = \'body\';

        $(\'<div id="tooltip2" class="tooltip">\' + contents + \'</div>\').css( {
            position: \'absolute\',
            display: \'none\',
            top: y - 35,
            left: x - 5,
            border: \'1px solid #000\',
            padding: \'1px 5px\',
			\'z-index\': \'9999\',
            \'background-color\': \'#202020\',
			\'color\': \'#fff\',
			\'font-size\': \'11px\',
            opacity: 0.8
        }).prependTo(rootElt).show();
    }

    //Display graph
    $.plot($(".placeholder1"), ds1, {
				series: {
					bars: {
							show: true,
							barWidth: 0.1,
							/*align: "left",*/
					},
				},
				colors: ["#acd163", "#8ece0f", "#6ca103"],
				grid:{
            hoverable:true
        },
        xaxis:{
            show: true,
						ticks: [[1, "Entradas"]]
        },
			 legend: true,
    });

    //Display graph
    $.plot($(".placeholder2"), ds2, {
				series: {
					bars: {
							show: true,
							barWidth: 0.1,
							/*align: "left",*/
					},
				},
				colors: ["#d18a63", "#d35c1a", "#bb4708"],
				bars: {
						show: true,
						barWidth: 0.1,
						/*align: "left",*/
				},
        grid:{
            hoverable:true
        },
        xaxis:{
            show: true,
						ticks: [[1, "Saídas"]]
        },
			 legend: true,
			 
    });

    $.plot($(".placeholder3"), ds3, {
				series: {
					bars: {
							show: true,
							barWidth: 0.1,
							/*align: "left",*/
					},
				},
				colors: ["'.$saldo_realizado_cor.'", "'.$saldo_total_cor.'"],
				grid:{
            hoverable:true
        },
        xaxis:{
            show: true,
						ticks: [[1, "Saldo"]]
        },
			 legend: true,

    });

//add tooltip event
$(".placeholder1, .placeholder2, .placeholder3").bind("plothover", function (event, pos, item) {
    if (item) {
        if (previousPoint != item.datapoint) {
            previousPoint = item.datapoint;
 
            //delete de prГ©cГ©dente tooltip
            $(\'.tooltip\').remove();
 
            var x = item.datapoint[0];
 
            //All the bars concerning a same x value must display a tooltip with this value and not the shifted value
            if(item.series.bars.order){
                for(var i=0; i < item.series.data.length; i++){
                    if(item.series.data[i][3] == item.datapoint[0])
                        x = item.series.data[i][0];
                }
            }
 
            var y = item.datapoint[1];
						
						y = number_format(y, 2, \',\', \'.\');
						
						var tool_tip_txt = "", serie_indice = item.seriesIndex, barra_id = $(this).attr("id");
						
						if(serie_indice==0){
							tool_tip_txt = "Realizado: R$ ";
						}else if(serie_indice==1){
							if(barra_id=="barraEntrada" || barra_id=="barraSaida"){
								tool_tip_txt = "À Realizar: R$ ";
							}else{
								tool_tip_txt = "Total: R$ ";
							}
						}else{
							tool_tip_txt = "Total: R$ ";
						}
						
						tool_tip_txt += y;
 
            /*showTooltip(item.pageX+5, item.pageY+5,x + " = " + y);*/
						showTooltip(item.pageX+5, item.pageY+5,tool_tip_txt);
        }
    }
    else {
        $(\'.tooltip\').remove();
        previousPoint = null;
    }
 
});
});

';

echo '

/*
===========================================================================================
GRÁFICO DE PIZZA
===========================================================================================
*/


	$(function () {
		var data = [];
		
';

$dt_ini = date('Y-m').'-01';
$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
$dt_fim = date('Y-m-d',$dt_fim);

$query_despesas = "
	select month(dt_compensacao) mes, sum(valor) valor
	from lancamentos 
	where tipo = 'P' 
		and compensado = 1
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
	group by month(dt_compensacao)
";
$array_despesas = $db->fetch_assoc($query_despesas);
if(empty($array_despesas)){
	$despesas = 0;
}else{
	$despesas = $array_despesas['valor'];
}
	

$query_receitas = "
	select month(dt_compensacao) mes, sum(valor) valor
	from lancamentos 
	where tipo = 'R' 
		and compensado = 1
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
	group by month(dt_compensacao)
";
$array_receitas = $db->fetch_assoc($query_receitas);
if(empty($array_receitas)){
	$receitas = 0;
}else{
	$receitas = $array_receitas['valor'];
}

echo '

	data[0] = { label: "Entradas", data: '.$receitas.'/*, color: "#069"*/ };
	data[1] = { label: "Saídas", data: '.$despesas.'/*, color: "#CC3333"*/ };
		
	$.plot($("#donut"), data,
	{
			series: {
				pie: { 
					show: true,
					innerRadius: 0.5,
					radius: 1,
					label: {
						show: true,
						radius: 2/3,
						formatter: function(label, series, data){
							/*return \'<div style="font-size:11px;text-align:center;padding:4px;color:black;">\'+label+\'<br/>\'+Math.round(series.percent)+\'%</div>\';*/
							return \'<div style="font-size:14px;font-weight:bold;text-align:center;padding:4px;color:white;">\'+Math.round(series.percent)+\'%</div>\';
						},
					/*background: {
							opacity: 0.5,
							color: \'#000\',
						},*/
						threshold: 0.1
					}
				}
			},
			legend: {
				show: true,
				noColumns: 1, // number of colums in legend table
				labelFormatter: null, // fn: string -> string
				labelBoxBorderColor: "#000", // border color for the little label boxes
				container: null, // container (as jQuery object) to put legend in, null means default on top of graph
				position: "ne", // position of default legend container within plot
				margin: [5, 10], // distance from grid edge to default legend container within plot
				backgroundColor: "#efefef", // null means auto-detect
				backgroundOpacity: 1 // set to 0 to avoid background
			},
			grid: {
				hoverable: true,
				clickable: true
			},
	});
	$("#interactive").bind("plothover", pieHover);
	$("#interactive").bind("plotclick", pieClick);
	
	});
	
	function pieHover(event, pos, obj) 
	{
		if (!obj)
					return;
		percent = parseFloat(obj.series.percent).toFixed(2);
		$("#hover").html(\'<span style="font-weight: bold; color: \'+obj.series.color+\'">\'+obj.series.label+\' (\'+percent+\'%)</span>\');
	}
	function pieClick(event, pos, obj) 
	{
		if (!obj)
					return;
		percent = parseFloat(obj.series.percent).toFixed(2);
		alert(\'\'+obj.series.label+\': \'+percent+\'%\');
	}

';

echo '

/*
===========================================================================================
GRÁFICO DE LINHAS
===========================================================================================
*/


	$(function () {
		
    var receitas = [], despesas = [];
';

$dt_ini = date('Y').'-01-01';
$dt_fim = date('Y').'-12-31';

$query_despesas = "
	select month(dt_compensacao) mes, sum(valor) valor
	from lancamentos 
	where tipo = 'P' 
		and compensado = 1
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
	group by month(dt_compensacao)
";
$array_despesas = $db->fetch_all_array($query_despesas);

$query_receitas = "
	select month(dt_compensacao) mes, sum(valor) valor
	from lancamentos 
	where tipo = 'R' 
		and compensado = 1
		and dt_compensacao >= '".$dt_ini."'
		and dt_compensacao <= '".$dt_fim."'
	group by month(dt_compensacao)
";
$array_receitas = $db->fetch_all_array($query_receitas);
/*
echo ' 
    var receitas = [[1,1250],[2,3560],[3,5489],[4,1250],[5,3560],[6,5489],[7,1250],[8,3560],[9,5489],[10,1250],[11,3560],[12,5489]];
    var despesas = [[1,450],[2,630],[3,740],[4,256],[5,2465],[6,1952],[7,4256],[8,1256],[9,3256],[10,5000],[11,2000],[12,3247]];
';
*/

echo ' 
    var receitas = [[0,0],[1,0],[2,0],[3,0],[4,0],[5,0],[6,0],[7,0],[8,0],[9,0],[10,0],[11,0],[12,0]];
    var despesas = [[0,0],[1,0],[2,0],[3,0],[4,0],[5,0],[6,0],[7,0],[8,0],[9,0],[10,0],[11,0],[12,0]];
';

if(count($array_receitas) == 0){ echo "receitas[1] = [1, parseFloat(0.00)];"; }		
if(count($array_despesas) == 0){ echo "despesas[1] = [1, parseFloat(0.00)];"; }
	
	
foreach($array_receitas as $receita){
	echo '
		receitas['.$receita[mes].'] = ['.$receita[mes].', parseFloat('.$receita[valor].')];
	';
}

foreach($array_despesas as $despesa){
	echo '
		despesas['.$despesa[mes].'] = ['.$despesa[mes].', parseFloat('.$despesa[valor].')];
	';
}


echo '		
    var plot = $.plot($(".chart"),
           [ { data: receitas, label: "Entradas"}, { data: despesas, label: "Saídas" } ], {
               series: {
                   lines: { show: true },
                   points: { show: true }
               },
               grid: { hoverable: true, clickable: true },
               yaxis: { min: 0},
			   			 xaxis: { min:1, show: true,	ticks: [[1, "Jan"], [2, "Fev"], [3, "Mar"], [4, "Abr"], [5, "Mai"], [6, "Jun"], [7, "Jul"], [8, "Ago"], [9, "Set"], [10, "Out"], [11, "Nov"], [12, "Dez"]]}
             });
						 
						  //Display graph
    function showTooltip(x, y, contents) {
        $(\'<div id="tooltip" class="tooltip">\' + contents + \'</div>\').css( {
            position: \'absolute\',
            display: \'none\',
            top: y + 5,
            left: x + 5,
            border: \'1px solid #000\',
            padding: \'2px\',
			\'z-index\': \'9999\',
            \'background-color\': \'#202020\',
			\'color\': \'#fff\',
			\'font-size\': \'11px\',
            opacity: 0.8
        }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    $(".chart").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if ($(".chart").length > 0) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);
												
												
			            var y = item.datapoint[1];
									y = number_format(y, 2, \',\', \'.\');
                  
									showTooltip(item.pageX+5, item.pageY+5, item.series.label + " R$ " + y);  
                  /*  showTooltip(item.pageX, item.pageY,
                                item.series.label + "R$ " + receitas); */
									
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
        }
    });

    $(".chart").bind("plotclick", function (event, pos, item) {
        if (item) {
            $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});

/* ======= Ajuda Primeiro Acesso ========= */
var steps = [{
                content: "<p>No menu <b>Minha Conta</b> você poderá atualizar seus dados cadastrais, efetuar novas contratações, visualizar faturas e alterar a senha de acesso.</p>",
			  highlightTarget: true,
        nextButton: true,
        target: $("#AjudaInicial1"),
        my: "top center",
        at: "bottom center"
      }, {
        content: "<p>Através do menu <b>Ajuda</b> você terrá acesso a: <br>- <b>Ajuda Inteligente:</b> Um guia para ensinar a utilizar o sistema. <br>- <b>Central de Ajuda:</b> Uma documentação completa do sistema.</p>",
        highlightTarget: true,
        nextButton: true,
        target: $("#AjudaInicial2"),
        my: "top center",
        at: "bottom center"
      }, {
        content: "<p>Visualize o <b>Total Disponível</b> em todas as suas contas financeiras.</p>",
        highlightTarget: true,
        nextButton: true,
        target: $("#AjudaInicial3"),
        my: "left center",
        at: "right center"
      }, {
        content: "<p>Utilize o <b>Menu Vertical</b> para navegar pelo sistema.</p>",
        highlightTarget: true,
        nextButton: true,
        target: $("#AjudaInicial4"),
        my: "left center",
        at: "right center"
      }]
</script>
';

if($_SESSION['primeiro_acesso'] == 0){
	 $_SESSION['primeiro_acesso'] = 1;
echo ' <script>
$(window).load(function(){

      var tour = new Tourist.Tour({
        steps: steps,
        tipClass: "Bootstrap",
        /* tipOptions:{ showEffect: "slidein" } */
      });
      tour.start();
});

</script>
';
}

/*
======================================================================
MOSTRAR E TROCAR INFORMATIVO DO CONTADOR - GERAL
======================================================================
*/
if($ic > 1){

echo '<script>
$(window).load(function(){
var c1 = 1;
var c2 = c1 + 1;
var totalC = '.$ic.';

        setInterval(function () { //setInterval     
            //alert(c1); alert(c2);
            alterarInfo(c1, c2); 
        
            if(c1 == totalC){
                 c1 = 1;
            }else{
                c1 = c1 +1;
            }                   
        
            if(c2 < totalC){ 
                c2 = c2 +1;
            }else if(c2 == totalC && c1 == totalC){ 
                c2 = 1;                
            }
   
        }, 10000);
});

function alterarInfo(c1, c2){
     $(".info" + c2).fadeIn( 400 ).css("display", "block");
     $(".info" + c1).fadeOut( 800 ).css("display","none"); 
  }
  </script>';
  
}  
  /*
===========================================================================================
DIALOG INFORMATIVO DO CONTADOR
===========================================================================================
*/
//===== UI dialog -  INFORAMTIVO  =====//
  
if($ic > 0){
echo '<script>
$(document).ready(function(){
    
	
    $( "#dialog-informativo" ).dialog({
        autoOpen: false,
        modal: true, 
        fluid: true,
        position: { my: "top", at: "top+5%", of: window }, //  https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
        buttons: {
            Fehcar: function () {
                $(this).dialog("close");
            } 
        },
    });
});


function visualizarInfo(id) { 
    
$("span.aguarde, div.aguarde").css("display", "block"); 

    $.ajax({
        type: "get",
        url: "modulos/geral/php/funcoes.php?funcao=visualizarInfo&id="+id,
       // data: params,
        dataType: "json",
        cache: true,
        success: function (dados) {
            
            $(".titulo").html(dados.titulo);
            $(".descricao").html(dados.descricao);
            
            $("#dialog-informativo").dialog("option", "title", "Informativo ( " + dados.dt_inicio + " )");
            
            //Abre o dialog
            $("#dialog-informativo").dialog("open");

            $("span.aguarde, div.aguarde").css("display", "none");    
        },
        error: function (erro) {
            alert(erro);
            $("span.aguarde, div.aguarde").css("display", "none");
        }
    })
    
}  
 </script>


';
}  

?>