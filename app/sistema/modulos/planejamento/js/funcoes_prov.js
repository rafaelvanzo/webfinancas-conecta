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
      url: 'modulos/planejamento/php/funcoes_prov.php', //url para onde será enviada as informações digitadas
      data: params, /*parâmetros que serão carregados para a url selecionada (via POST). o form serialize passa de uma só vez todas as informações que estão dentro do formulário. Facilita, mas pode atrapalhar quando não for aplicado adequadamente a sua   aplicação*/
	  	cache: true,

      beforeSend: function(){
      },

      success: function(data){
				funcaoRetorno(data);
	  	},

      error: function(erro){
      }
	  
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
FORMATAR NÚMERO
===========================================================================================
*/

function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}	

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
SALVAR ORÇAMENTO
===========================================================================================
*/

function provisaoSalvar(tipo){
	plcValIncluir(tipo); //inclui valores na conta selecioanda antes da inclusão ou edição, pois a atualização dos valores mensais só é feita com a troca de contas
	if(tipo=="dpre"){
		dpreIncluir();
	}else if(tipo=="amrt"){
		amrtIncluir();
	}else if(tipo=="trbt"){
		trbtIncluir();
	}
	//var salvar = $('input[name="radio_orcamento"]:checked').val();
	//if(salvar=="incluir"){
		//provisaoIncluir();
	//}else{
		//provisaoEditar();
	//}
}

/*
===========================================================================================
INCLUÍR DEPRECIAÇÃO
===========================================================================================
*/

function dpreIncluir(){ 
	//if($('#orcamento_novo').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var vl_unico_check = $('#vl_unico_check_dpre').attr('checked');
		var vl_unico = 0;
		if(vl_unico_check=='checked')
			vl_unico = 1;
		var params = "funcao=dpreIncluir&ano="+$("#ano_dpre").val()+"&vl_unico="+vl_unico+"&valores="+$("#vl_dpre").val();

		funcaoRetorno = function(data){
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

		ajax_jquery(params);
	//}
}

/*
===========================================================================================
INCLUÍR AMORTIZAÇÃO
===========================================================================================
*/

function amrtIncluir(){ 
	//if($('#orcamento_novo').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var vl_unico_check = $('#vl_unico_check_amrt').attr('checked');
		var vl_unico = 0;
		if(vl_unico_check=='checked')
			vl_unico = 1;
		var params = "funcao=amrtIncluir&ano="+$("#ano_amrt").val()+"&vl_unico="+vl_unico+"&valores="+$("#vl_amrt").val();

		funcaoRetorno = function(data){
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

		ajax_jquery(params);
	//}
}

/*
===========================================================================================
INCLUÍR PROVISÕES TRABALHISTAS
===========================================================================================
*/

function trbtIncluir(){ 
	//if($('#orcamento_novo').valid()){
		$("span.aguarde, div.aguarde").css("display","block");
		var vl_unico_check = $('#vl_unico_check_trbt').attr('checked');
		var vl_unico = 0;
		if(vl_unico_check=='checked')
			vl_unico = 1;
		var params = "funcao=trbtIncluir&ano="+$("#ano_trbt").val()+"&vl_unico="+vl_unico+"&valores="+$("#vl_trbt").val();

		funcaoRetorno = function(data){
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

		ajax_jquery(params);
	//}
}

/*
===========================================================================================
EXIBIR PROVISÃO
===========================================================================================
*/

function provisaoExibir(tipo_num,ano,tipo_nome){
	$("span.aguarde, div.aguarde").css("display","block");
  var params = "funcao=provisaoExibir";
	params += "&tipo="+tipo_num;
	params += "&ano="+ano;

	funcaoRetorno = function(data){
		var _data = JSON.parse(data);
		$("span.aguarde, div.aguarde").css("display","none");
		//bloqueia ou desbloqueia os campos conforme seja valor único ou não
		if(_data.vl_unico_check==1){
			$('#vl_unico_check_'+tipo_nome).attr('checked',true);
			$('#divVlUnico_'+tipo_nome+' div.checker span input:checkbox').closest('.checker > span').addClass('checked');
			$('.vl-mes-'+tipo_nome).each(function(index, element) {
				$(this).attr('readonly',true);
			});
			$('#vl_unico_'+tipo_nome).attr('readonly',false);
			$('#vl_unico_'+tipo_nome).val(_data.jan);
		}else{
			$('#vl_unico_check_'+tipo_nome).attr('checked',false);
			$('#divVlUnico_'+tipo_nome+' div.checker span input:checkbox').closest('.checker > span').removeClass('checked');
			$('.vl-mes-'+tipo_nome).each(function(index, element) {
				$(this).attr('readonly',false);
			});
			$('#vl_unico_'+tipo_nome).attr('readonly',true); 
			$('#vl_unico_'+tipo_nome).val('0,00');
		}	
		//coloca valores num array
		var arr_val = new Array();
		for(val in _data){
			arr_val[val] = _data[val];
		}
		//atribui os valores aos campos
		var pos = "";
		var valor = "";
		$('.vl-mes-'+tipo_nome).each(function(index, element) {
			pos = $(this).attr('name');
			valor = arr_val[pos];
			$(this).val(valor);
		});
	}
	
	ajax_jquery(params);
}

/*
===========================================================================================
ATRIBUÍR VALOR ÚNICO
===========================================================================================
*/

//habilita / desabilita campos de valores
function valUnico(tipo){
	var checked = $('#vl_unico_check_'+tipo).attr('checked');
	if( checked == 'checked' ){
		//var vl_unico = $('#vl_unico').val();
		$('#vl_unico_'+tipo).attr('readonly',false);
		$('.vl-mes-'+tipo).each(function(index, element) {
			$(this).attr('readonly',true);
			//$(this).val(vl_unico);
		});
	}else{
		$('#vl_unico_'+tipo).attr('readonly',true);
		$('.vl-mes-'+tipo).each(function(index, element) {
			$(this).attr('readonly',false);
		});
	}
}

//atribui o valor único a cada mês
function valUnicoAttr(tipo){
	var checked = $('#vl_unico_check_'+tipo).attr('checked');
	var vl_unico = $('#vl_unico_'+tipo).val();
	$('.vl-mes-'+tipo).each(function(index, element) {
		$(this).val(vl_unico);
	});
}

/*
===========================================================================================
LIMPAR ORÇAMENTO
===========================================================================================
*/

$(document).ready(function(e) {
  $('input[name="radio_orcamento"]').change(function(e) {
		orcamentosLimpar();
  });
});


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

/*
===========================================================================================
INCLUIR VALORES MENSAIS NA PROVISÃO
===========================================================================================
*/

function plcValIncluir(tipo){
	$("span.aguarde, div.aguarde").css("display","block");
	var str_valores = "";
	var vl_anual = 0;
	var vl_mes = 0;
	var ano = document.getElementById("ano_"+tipo).value;
	var vl_unico_check = $('#vl_unico_check_'+tipo).attr('checked');
	var vl_unico = $('#vl_unico_'+tipo).val();
	if(vl_unico_check=='checked'){
		str_valores = {'jan':vl_unico,'fev':vl_unico,'mar':vl_unico,'abr':vl_unico,'mai':vl_unico,'jun':vl_unico,'jul':vl_unico,'ago':vl_unico,'sete':vl_unico,'outu':vl_unico,'nov':vl_unico,'dez':vl_unico};
		//vl_unico = txtToValor(vl_unico);
		//vl_anual = 12 * vl_unico;
		//vl_anual = number_format(vl_anual,2,',','.');
		//$('#vl_'+plano_contas_id).html(vl_anual);
	}else{
		str_valores = {'jan':$('#jan_'+tipo).val(),'fev':$('#fev_'+tipo).val(),'mar':$('#mar_'+tipo).val(),'abr':$('#abr_'+tipo).val(),'mai':$('#mai_'+tipo).val(),'jun':$('#jun_'+tipo).val(),'jul':$('#jul_'+tipo).val(),'ago':$('#ago_'+tipo).val(),'sete':$('#sete_'+tipo).val(),'outu':$('#outu_'+tipo).val(),'nov':$('#nov_'+tipo).val(),'dez':$('#dez_'+tipo).val()};
		//$('.vl-mes').each(function() {
			//vl_mes = $(this).val();
			//vl_mes = txtToValor(vl_mes);
			//vl_anual += vl_mes;
		//});
		//vl_anual = number_format(vl_anual,2,',','.');
		//$('#vl_'+plano_contas_id).html(vl_anual);
	}
	str_valores = JSON.stringify(str_valores);
	$('#vl_'+tipo).val(str_valores);
	//alert($('#valores').val());
	$("span.aguarde, div.aguarde").css("display","none");
}


