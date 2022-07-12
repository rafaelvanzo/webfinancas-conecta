/*
MELHORIAS PARA FUNÇÕES JS
-retirar campo vl_anual da classe php
-criar função única para preencher valor anual
-criar função única para preencher valor mensal
-separar a inclusão da exibição
-separar os valores por ano dentro do objeto retornado da classe e contruído no js
-incluír somente valores que sejam diferente de zero
*/

// JavaScript Document

/*
========================================================================================================================
REQUISICAO AJAX
========================================================================================================================
*/

function ajax_jquery(params,funcao_retorno){

		/*
		params += "&bd_web_financas="+$('#bd_web_financas').val();
		params += "&id_usuario="+$('#id_usuario').val();
		params += "&id_dependente="+$('#id_dependente').val();
		*/
		
    $.ajax({
		  
      type: 'post', //Tipo do envio das informações GET ou POST
      url: 'modulos/planejamento/php/funcoes_orct.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      //beforeSend: function(){
      //},

      success: function(data){
				eval("("+funcao_retorno+")");
	  	},

      //error: function(erro){
      //}
	  
    })

}

/*
========================================================================================================================
JANELAS
========================================================================================================================
*/
/*
$(document).ready(function(e) {
 
	//===== UI dialog - Incluir conta =====//
  
	$( "#dialog-message-planoContas-incluir" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		buttons: {
			Salvar: function() {
				planoContasIncluir();
				//$( this ).dialog( "close" );
			},	
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-planoContas-incluir" ).click(function() {
		planoContasLimpar('form_planoContas');
		$( "#dialog-message-planoContas-incluir" ).dialog( "open" );
		return false;
	});		

	//===== UI dialog - Editar conta =====//
	
	$( "#dialog-message-planoContas-editar" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		buttons: {
			Salvar: function() {
				planoContasEditar();
				//$( this ).dialog( "close" );
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-planoContas-editar" ).click(function() {
		$( "#dialog-message-planoContas-editar" ).dialog( "open" );
		return false;
	});		

});
*/

/*
===========================================================================================
CONVERTER TEXTO PARA VALOR
===========================================================================================
*/

function txtToValor(valor){
	var txt = valor;
	txt = txt.replace(/\./g, '');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
}

/*
===========================================================================================
ALTERNAR OPERAÇÃO SALVAR
===========================================================================================
*/

function oprSalvar(){
	var ano = new Date();
	ano = ano.getFullYear();
	$("#ano").val(ano);
	$("label.error").hide();
	var salvar = $('input[name="radio_orcamento"]:checked').val();
	if(salvar=="incluir"){
		$('#orcamento_novo').attr('readonly',false);
		$('#orcamento_buscar').attr('disabled',true);
	}else{
		$('#orcamento_novo').attr('readonly',true);
		$('#orcamento_buscar').attr('disabled',false);
	}	
}

/*
===========================================================================================
SALVAR ORÇAMENTO
===========================================================================================
*/

function orcamentosSalvar(){
	plcValIncluir(); //inclui valores na conta selecioanda antes da inclusão ou edição, pois a atualização dos valores mensais só é feita com a troca de contas
	var salvar = $('input[name="radio_orcamento"]:checked').val();
	if(salvar=="incluir"){
		orcamentosIncluir();
	}else{
		orcamentosEditar();
	}
}

/*
===========================================================================================
INCLUÍR ORÇAMENTO
===========================================================================================
*/

$(document).ready(function(e) {
	$('#form_orcamento').validate({
		onkeyup: false,
		onfocusout: false
	});
});

function orcamentosIncluir(){
	if($('#orcamento_novo').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = "funcao=orcamentosIncluir&descricao="+$("#orcamento_novo").val()+"&valores="+$("#valores").val();
		ajax_jquery(params,"orcamentosIncluirRetorno(data)");
		//alert(params);
	}
}

function orcamentosIncluirRetorno(data){
	//alert(data);
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);	
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$("span.aguarde, div.aguarde").css("display","none");	
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 4000);
		$("span.aguarde, div.aguarde").css("display","none");
	}
}

/*
===========================================================================================
EDITAR ORCAMENTO
===========================================================================================
*/

function orcamentosEditar(){
	if($('#orcamento_buscar').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var params = "funcao=orcamentosEditar&orcamento_id="+$("#orct_id").val()+"&descricao="+$("#orcamento_buscar").val()+"&dscr_ini="+$("#dscr_ini").val()+"&valores="+$("#valores").val();
		//alert(params);
		ajax_jquery(params,"orcamentosEditarRetorno(data)");
	}
}

function orcamentosEditarRetorno(data){
	//alert(data);
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		$('.nSuccess p').html(dados.notificacao);	
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
		$("span.aguarde, div.aguarde").css("display","none");	
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 4000);
		$("span.aguarde, div.aguarde").css("display","none");
	}
}

/*
===========================================================================================
INCLUIR VALORES NO PLANO DE CONTAS
===========================================================================================
*/

function plcValIncluir(){

	var plano_contas_id = $('#conta_id').val();
	$("span.aguarde, div.aguarde").css("display","block");
	var str_valores = $('#valores').val();
	var valores = [];
	var vl_unico_check = $('#vl_unico_check').attr('checked');
	var vl_unico = $('#vl_unico').val();
	var vl_anual = 0;
	var vl_mes = 0;
	var ano = $('#ano').val();
	var ano_selecionado = $('#ano_selecionado').val(); 
	var achou_conta = false;
	
	if(str_valores!=''){
		valores = JSON.parse(str_valores);
		for(var i=0;i<valores.length;i++){
			if(valores[i].plano_contas_id==plano_contas_id && valores[i].ano==ano_selecionado){
				var conta_indice = i;
				i=valores.length;
				achou_conta = true;
			}
		}
	}

	if(vl_unico_check=='checked'){
		if(achou_conta){
			valores[conta_indice] = {'plano_contas_id':plano_contas_id,'vl_unico_check':1,'jan':vl_unico,'fev':vl_unico,'mar':vl_unico,'abr':vl_unico,'mai':vl_unico,'jun':vl_unico,'jul':vl_unico,'ago':vl_unico,'sete':vl_unico,'outu':vl_unico,'nov':vl_unico,'dez':vl_unico,'ano':ano_selecionado};
		}else{
			valores.push({'plano_contas_id':plano_contas_id,'vl_unico_check':1,'jan':vl_unico,'fev':vl_unico,'mar':vl_unico,'abr':vl_unico,'mai':vl_unico,'jun':vl_unico,'jul':vl_unico,'ago':vl_unico,'sete':vl_unico,'outu':vl_unico,'nov':vl_unico,'dez':vl_unico,'ano':ano_selecionado});
		}

	}else{
		if(achou_conta){
			valores[conta_indice] = {'plano_contas_id':plano_contas_id,'vl_unico_check':0,'jan':$('#jan').val(),'fev':$('#fev').val(),'mar':$('#mar').val(),'abr':$('#abr').val(),'mai':$('#mai').val(),'jun':$('#jun').val(),'jul':$('#jul').val(),'ago':$('#ago').val(),'sete':$('#sete').val(),'outu':$('#outu').val(),'nov':$('#nov').val(),'dez':$('#dez').val(),'ano':ano_selecionado};
		}else{
			valores.push({'plano_contas_id':plano_contas_id,'vl_unico_check':0,'jan':$('#jan').val(),'fev':$('#fev').val(),'mar':$('#mar').val(),'abr':$('#abr').val(),'mai':$('#mai').val(),'jun':$('#jun').val(),'jul':$('#jul').val(),'ago':$('#ago').val(),'sete':$('#sete').val(),'outu':$('#outu').val(),'nov':$('#nov').val(),'dez':$('#dez').val(),'ano':ano_selecionado});
		}

	}

	str_valores = JSON.stringify(valores);
	$('#valores').val(str_valores);
	$("span.aguarde, div.aguarde").css("display","none");

}

/*
===========================================================================================
EXIBIR VALORES NO PLANO DE CONTAS
===========================================================================================
*/

//exibe valores para todo o plano de contas quando o ano é alterado
function vlPlcExibir(){

	var conta_id = $('#conta_id').val();
	contaSelecionar(conta_id,"");//chama função para incluír valores no plano de contas antes de trocar o ano
	$('#ano_selecionado').val($('#ano').val());

	var ano = $('#ano').val();
	var vl_unico;
	var vl_anual;
	var conta_id;
	var arr_valida = [];
	var obj_valores = [];
	var str_valores = $('#valores').val();
	var arr_valores = JSON.parse(str_valores);

	for(var i=0;i<arr_valores.length;i++){
		
		if(arr_valores[i].ano==ano){
			
			obj_valores = arr_valores[i];
			conta_id = obj_valores['plano_contas_id'];

			if(obj_valores['vl_unico_check']==1){

				vl_unico = obj_valores['jan'];
				vl_unico = txtToValor(vl_unico);
				vl_anual = 12*vl_unico;
				vl_anual = number_format(vl_anual,2,',','.');
				$('#vl_'+conta_id).html(vl_anual);

			}else{
				
				delete obj_valores.plano_contas_id;
				delete obj_valores.vl_unico_check;
				delete obj_valores.ano;
				if(obj_valores.vl_anual){ //campo retornado quando o orçamento é carregado(estudar viabilidade de retirar esse campo do retorno da classe)
					delete obj_valores.vl_anual;
				}
				
				var vl_anual= 0,vl_mes,j;

				for(j in obj_valores){
					vl_mes = obj_valores[j];
					vl_mes = txtToValor(vl_mes);
					vl_anual += vl_mes;
				}

				vl_anual = number_format(vl_anual,2,',','.');
				$('#vl_'+conta_id).html(vl_anual);
			}
			
			arr_valida.push(conta_id);
			
		}else{
			
			if(arr_valida.indexOf(conta_id)==-1){
				conta_id = arr_valores[i].plano_contas_id;
				$('#vl_'+conta_id).html('0,00');
			}

		}

	}

}

//altera valores exibidos de conta quando é selecionada
function contaSelecionar(conta_id,conta_nom){
	plcValIncluir(); //inclui valores na conta selecioanda antes de exibir valores de outra conta
	plcValExibir(conta_id,conta_nom);
}

//exibição dos valores de uma conta
function plcValExibir(conta_id,conta_nom){

	if(conta_nom!=""){
		$('#contaNome').html(conta_nom);
		$('#conta_id').val(conta_id);
	}

	var ano = $('#ano').val();
	var str_valores = $('#valores').val();//var str_valores = '{"1":[{"jan":"10","fev":"10","mar":"10","abr":"10","mai":"10","jun":"10","jul":"10","ago":"10","set":"10","out":"10","nov":"10","dez":"10"}]}';

	if(str_valores!=""){
		
		var arr_valores = JSON.parse(str_valores);
		//verifica se a conta selecionada está no array de valores
		var achou_conta = false;
		for(var i=0;i<arr_valores.length;i++){
			if(arr_valores[i].plano_contas_id==conta_id && arr_valores[i].ano==ano){
				var obj_valores = arr_valores[i];
				i=arr_valores.length;
				achou_conta = true;
			}
		}

		//se achar a conta exibe seus valores por mês
		if(achou_conta){

			if(obj_valores['vl_unico_check']==1){
				$('input[name="vl_unico_check"]').attr('checked',true);
				$('#divVlUnico div.checker span input:checkbox').closest('.checker > span').addClass('checked');
				var vl_unico = obj_valores['jan'];
				$('.vl-mes').each(function(index, element) {
					$(this).attr('readonly',true);
					$(this).val(vl_unico);
				});
				$('#vl_unico').attr('readonly',false);
				$('#vl_unico').val(vl_unico);

			}else{

				$('input[name="vl_unico_check"]').attr('checked',false);
				$('#divVlUnico div.checker span input:checkbox').closest('.checker > span').removeClass('checked');
				$('#vl_unico').attr('readonly',true);
				var pos = "";
				var valor = "";
				var vl_anual = 0;
				$('.vl-mes').each(function(index, element) {
					$(this).attr('readonly',false);
					pos = $(this).attr('name');
					valor = obj_valores[pos];
					$(this).val(valor);

				});

			}

		}else{

			$('input[name="vl_unico_check"]').attr('checked',false);
			$('#vl_unico_check').closest('.checker > span').removeClass('checked');
			$('#vl_unico').attr('readonly',true);
			$('.vl-mes').each(function(index, element) {
				$(this).attr('readonly',false);
				$(this).val('0,00');
			});
			$('#vl_'+conta_id).html('0,00');

		}

	}else{

		$('input[name="vl_unico_check"]').attr('checked',false);
		$('#vl_unico_check').closest('.checker > span').removeClass('checked');
		$('.vl-mes').each(function(index, element) {
			$(this).attr('readonly',false);
			$(this).val('0,00');
		});

	}
}

/*
===========================================================================================
ATRIBUÍR VALOR ÚNICO
===========================================================================================
*/

//habilita / desabilita campos de valores após clicar no checkbox de valor único
function valUnico(){
	var checked = $('#vl_unico_check').attr('checked');
	if( checked == 'checked' ){
		//var vl_unico = $('#vl_unico').val();
		$('#vl_unico').attr('readonly',false);
		$('.vl-mes').each(function(index, element) {
			$(this).attr('readonly',true);
			//$(this).val(vl_unico);
		});
	}else{
		$('#vl_unico').attr('readonly',true);
		$('.vl-mes').each(function(index, element) {
			$(this).attr('readonly',false);
		});
	}
}

//atribui o valor único a cada mês
function valUnicoAttr(){
	var checked = $('#vl_unico_check').attr('checked');
	var vl_unico = $('#vl_unico').val();
	$('.vl-mes').each(function(index, element) {
		$(this).val(vl_unico);
	});
}

/*
===========================================================================================
ATUALIZAR VALOR ANUAL DA CONTA
===========================================================================================
*/

//atualiza valor anual sem incluir, é apenas para mostrar ao usuário que o valor foi alterado
function vlAnualAtualizar(){

	var conta_id = $("#conta_id").val();
	var vl_mes=0,
			vl_anual=0;
			
	$('.vl-mes').each(function() {
		vl_mes = $(this).val();
		vl_mes = txtToValor(vl_mes);
		vl_anual += vl_mes;
	});

	vl_anual = number_format(vl_anual,2,',','.');
	$('#vl_'+conta_id).html(vl_anual);

}

/*
===========================================================================================
EXIBIR ORÇAMENTO
===========================================================================================
*/

function orcamentosExibir(orcamento_id){
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=orcamentosExibir";
	params += "&orcamento_id="+orcamento_id;
	ajax_jquery(params,"orcamentosExibirRetorno(data)");
}

function orcamentosExibirRetorno(data){
	//alert(data);
	var _data = JSON.parse(data);
	var d = new Date();
	var ano_atual = d.getFullYear();
	//alert(_data.valores);
	$('#valores').val(_data.valores);
	var valores = JSON.parse(_data.valores);
	var plano_contas_id = '';
	var vl_anual = 0;
	//exibe valores anuais para todas as contas
	for(var i=0;i<valores.length;i++){
		if(valores[i].ano==ano_atual){
			vl_anual = valores[i].vl_anual;
			plano_contas_id = valores[i].plano_contas_id;
			$('#vl_'+plano_contas_id).html(vl_anual);
		}
	}
	//exibe valores mensais para a primeira conta do array se for diferente de zero
	if($('.vl_conta_anual').html()!="0,00"){ //verifica se o o valor anual da primeira conta na tabela é igual à zero
		plano_contas_id = valores[0].plano_contas_id;
		var conta_nome = $("#contaNome").html();
		plcValExibir(plano_contas_id,conta_nome);
	}
	$("#dscr_ini").val($("#orcamento_buscar").val());
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
EXCLUÍR
===========================================================================================
*/

function orcamentosExcluir(){
	var checked = $('input[name="radio_orcamento"]:checked').val();
	var orcamento_id = $("#orct_id").val();
	if(checked=="incluir" || orcamento_id==""){
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "OK",
			click: function() { $("#dialog-alerta").dialog("close");}
		}	
		]);
		$('#dialog-alerta').html("<br/> Selecione um orçamento existente.");
	}else{
		$( "#dialog-alerta" ).dialog( "option", "buttons", [
		{
			text: "Sim",
			click: function() { 
				var params = "funcao=orcamentosExcluir&orcamento_id="+orcamento_id;
				ajax_jquery(params,"orcamentosExcluirRetorno(data)");
				$("#dialog-alerta").dialog("close");
				$("span.aguarde, div.aguarde").css("display","block");
			}
		},
		{
			text: "Não",
			click: function() { $("#dialog-alerta").dialog("close");}
		}	
		]);
		$('#dialog-alerta').html("<br/> Deseja realmente excluír o orçamento selecionado?");
	}
	$('#dialog-alerta').dialog('open');		
}

function orcamentosExcluirRetorno(data){
	var dados = JSON.parse(data);
	if(dados.situacao==1){
		orcamentosLimpar();
		$('.nSuccess p').html(dados.notificacao);
		$('.nSuccess').slideDown();
		setTimeout(function(){ $('.nSuccess').slideUp() }, 3000);
	}else{
		$('.nWarning p').html(dados.notificacao);
		$('.nWarning').slideDown();
		setTimeout(function(){ $('.nWarning').slideUp() }, 5000);
	}
	$("span.aguarde, div.aguarde").css("display","none");
}

/*
===========================================================================================
LIMPAR ORÇAMENTO
===========================================================================================
*/

function orcamentosLimpar(){

	//zera o valor único
	$('#vl_unico').val('0,00');
	
	//zera os valores mensais e habilita os campos
	$('.vl-mes').each(function() {
		$(this).val('0,00');
		$(this).attr('readonly',false);
	});
	
	//limpa o campo novo orçamento
	$('#orcamento_novo').val('');
	
	//limpa o campo orçamento existente
	$('#orcamento_buscar').val('');
	
	//limpa valores armazenados no html
	$('#valores').val('');
	
	//zera os valores da tabela
	$('.vl_conta_anual').each(function() {
		$(this).html('0,00');
	});

	//desmarca o valor único
	$('input[name="vl_unico_check"]').attr('checked',false);
	$('#divVlUnico div.checker span input:checkbox').closest('.checker > span').removeClass('checked');
	
	//desabilita o valor único
	$('#vl_unico').attr('readonly',true);
	
	//remove seleção de orçamento existente
	$('span.check-green').css('display','none');
	$('#orct_id').val('');
	$('#dscr_ini').val('');

	//var validator = $('#'+form).validate();
	//validator.resetForm();
	//$('span.check-green').css('display','none');

}

$(document).ready(function(){

    //DATA TABLE
    //========================================================================================================================

    oTablePlanoContas = $('.tblplanoContas').dataTable({
        bJQueryUI: true,
        bAutoWidth: false,
        sPaginationType: "full_numbers",
        iDisplayLength: -1, //Mostra todas os registros sem páginar
        bLengthChange: false, //Oculta o select que exibe a quantidade de reistros que podem ser visualizados
        bSort: false, //desabilita ordenação das colunas
        //"sDom": '<"itemsPerPage"fl>t<"F"ip>',
        //"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
        "aaSorting": [[0, "asc"]], //inicializa a tabela ordenada pela coluna especificada
        //'aoColumnDefs': [
			//{ "bVisible": false, "aTargets": [0] } //torna uma coluna invisivel
        //],
        oLanguage: {
            "sLengthMenu": "<span>Mostrar:</span> _MENU_",
            "sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
        }
    });

    //$('.tblplanoContas > thead').remove();


/*
========================================================================================================================
AUTO COMPLETAR
========================================================================================================================
*/

	//======== COMPLETAR - PLANO DE CONTAS - CONDIGO PAI ID ===================
	//var cache = {};
	
	$( ".orcamentos_buscar" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			lastXhr = $.getJSON( "modulos/planejamento/paginas/orcamentos_buscar.php", request, function( data, status, xhr ) {
				//cache[ term ] = data;
				response( data );
			});
		},
		search: function( event, ui ) {
			var campo_id = $(this).attr('name');
			$('#'+campo_id+'_aguarde').css('display','block');
		},
		response: function( event, ui ) {
			//alert(ui);
			var campo_id = $(this).attr('name');
			$('#'+campo_id+'_aguarde').css('display','none');
			if(ui.content.length==0){
				//alert('nenhum resultado encontrado');
			}
			//alert('resposta');
		},
		select: function( event, ui ) {
			var ano = new Date();
			ano = ano.getFullYear();
			$("#ano").val(ano);

			var campo_id = $(this).attr('name');
			$('#'+campo_id).val(ui.item.id);
	 		$('#'+campo_id+'_cg').css('display','block');
			orcamentosExibir(ui.item.id);
			//$(this).attr('disabled','disabled');
			fadeOut($(this).attr('id'));
	  }
	});

	$( ".orcamentos_buscar" ).click(function(){
		var nome = this.getAttribute('name');
		var selected = document.getElementById(nome).value;
		if(!selected){
			var campo_id = $(this).attr('id');
			$( "#"+campo_id ).autocomplete( "search" );
		}
	})
	//======== FIM COMPLETAR PLANO DE CONTAS - CONDIGO PAI ID =============
	
/*
========================================================================================================================
LIMPAR ORÇAMENTO - LISTENER
========================================================================================================================
*/

	$('input[name="radio_orcamento"]').change(function (e) {
	    if (this.value == 'editar')
	        orcamentosLimpar();
	});



/*
========================================================================================================================
INICIALIZA ORÇAMENTO
========================================================================================================================
*/

	var ano = new Date().getFullYear();
	document.getElementById("ano").value = ano;
	var contadorCategoria = $('#cntRadio1').data('contador-categoria');
	$('#cntRadio1').attr('checked', true);
	$('#conta_id').val($('#cntRadio1').val());
	$('#contaNome').text($('#codConta' + contadorCategoria).text() + $('#cntNome' + contadorCategoria).text());
});

