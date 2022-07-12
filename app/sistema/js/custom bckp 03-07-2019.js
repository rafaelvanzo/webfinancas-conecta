$(function() {

/* Form related plugins
================================================== */

	//===== Form elements styling =====//
	/* select,  input:radio, input:checkbox*/
	$("input:file").uniform();

    //===== Accordion =====//	//---> Abas que se movem dentro do dialog	
    /*
        var tela =	$(document).width(); //pega o tamanho da tela
        alert(tela);
        if(tela <= 300){
            var largura = tela; //calcula o tamanho da div do modal 
        }else if(tela <= 1024){
            var largura = 600; //calcula o tamanho da div do modal 
        }else{
            var largura = 700; //calcula o tamanho da div do modal 
        }*/
    //	$('div.menu_body:eq(0)').css("width",largura); alert(largura);
    //	var largura = $('div.menu_body:eq(0)').width('auto');alert(largura);
/*
	$('div.menu_body:eq(0)').show();
	$('.acc .title:eq(0)').show().css({ color: "#2B6893" });

	$(".MaisOpcoes").click(function () {
	    if ($(this).index() != 0) {
	        var form_id = $(this).parents('.dialog').attr('id');
	        if ($('#' + form_id).valid()) {
	            if (($(this).attr('id') !== 'boletoCnfg' && $(this).attr('id') !== 'boletoCnfgEdit') || $('#dados').data('emite_boleto') === '1') {
	                //$(this).css({color:"#2B6893"}).next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow"); <!-- original
	                $(this).css({ color: "#2B6893" }).next("div.menu_body").slideDown(300).siblings("div.menu_body").slideUp("slow");
	                $(this).siblings().css({ color: "#404040" });
	                //$(this).css("width",largura);
	            } else {
	                //exibe mensagem informando que o banco não emite boleto pelo Web Finanças
	                $("#dialog-alerta").dialog("option", "buttons", [
					{
					    text: "Fechar",
					    click: function () { $("#dialog-alerta").dialog("close"); }
					}
	                ]);
	                $('#dialog-alerta').html("<br/> O banco selecionado não está habilitado para emitir boletos. <br/> Favor entrar em contato com o suporte do Web Finanças.");
	                $('#dialog-alerta').dialog('open');
	            }
	        }
	    } else {
	        $(this).css({ color: "#2B6893" }).next("div.menu_body").slideDown(300).siblings("div.menu_body").slideUp("slow");
	        $(this).siblings().css({ color: "#404040" });
	    }
	});
*/
    /*
	$(".MaisOpcoes").click(function () {
	    if ( ($(this).attr('id') == 'boletoCnfg' || $(this).attr('id') == 'boletoCnfgEdit') && $('#dados').data('emite_boleto') == '0') {
	        //exibe mensagem informando que o banco não emite boleto pelo Web Finanças
	        $("#dialog-alerta").dialog("option", "buttons", [
            {
                text: "Fechar",
                click: function () { $("#dialog-alerta").dialog("close"); }
            }
	        ]);
	        $('#dialog-alerta').html("<br/> O banco selecionado não está habilitado para emitir boletos. <br/> Favor entrar em contato com o suporte do Web Finanças.");
	        $('#dialog-alerta').dialog('open');
	    }
	});
    */
	//===== Usual validation engine=====//

	$("#usualValidate").validate({
		rules: {
			firstname: "required",
			minChars: {
				required: true,
				minlength: 3
			},
			maxChars: {
				required: true,
				maxlength: 6
			},
			mini: {
				required: true,
				min: 3
			},
			maxi: {
				required: true,
				max: 6
			},
			range: {
				required: true,
				range: [6, 16]
			},
			emailField: {
				required: true,
				email: true
			},
			urlField: {
				required: true,
				url: true
			},
			dateField: {
				required: true,
				date: true
			},
			digitsOnly: {
				required: true,
				digits: true
			},
			enterPass: {
				required: true,
				minlength: 5
			},
			repeatPass: {
				required: true,
				minlength: 5,
				equalTo: "#enterPass"
			},
			number: {
				required: true,
				number: true,
			},
			customMessage: "required",
			
	
			topic: {
				required: "#newsletter:checked",
				minlength: 2
			},
			agree: "required"
		},
		messages: {
			customMessage: {
				required: "Bazinga! This message is editable",
			},
			customMessage: {
				required: "*Campo obrigatório.",
				number: "*Digite um número.",
			},
			agree: "Please accept our policy"
		}
	});



	//===== Input limiter =====//
	
	$('.lim').inputlimiter({
		limit: 100
		//boxClass: 'limBox',
		//boxAttach: false
	});


	//===== Multiple select with dropdown =====//
	
	$(".chzn-select").chosen(); 
	
	
	
	
	//===== ShowCode plugin for <pre> tag =====//
	
	$('.showCode').sourcerer('js html css php'); // Display all languages
	$('.showCodeJS').sourcerer('js'); // Display JS only
	$('.showCodeHTML').sourcerer('html'); // Display HTML only
	$('.showCodePHP').sourcerer('php'); // Display PHP only
	$('.showCodeCSS').sourcerer('css'); // Display CSS only
	
	
	//===== Autocomplete =====//
	
	var availableTags = [ "ActionScript", "AppleScript", "Asp", "BASIC", "C", "C++", "Clojure", "COBOL", "ColdFusion", "Erlang", "Fortran", "Groovy", "Haskell", "Java", "JavaScript", "Lisp", "Perl", "PHP", "Python", "Ruby", "Scala", "Scheme" ];
	$( "#ac" ).autocomplete({
	source: availableTags
	});	
	
	//===== Mascará para número =====//
	$(".maskNum").keyup(function(e){
		var num = $(this).val().replace(/\D/g,"");
		$(this).val(num);
	});

	//===== Masked input =====//
	$.mask.definitions['~'] = "[+-]";

	$(".maskDate").mask("99/99/9999");//, { completed: function () { alert("Callback when completed"); } };

    $(".maskPhone").mask("(99) 99999-999?9")
								 .live('focusout', function (event) {  /* === Inicio adaptação nono digito celular === */ 
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
								}); /* === Fim adaptação nono digito celular === */
	$(".maskPhoneExt").mask("(999) 999-9999? x99999");
	$(".maskIntPhone").mask("+33 999 999 999");
	$(".maskTin").mask("99-9999999");
	$(".maskSsn").mask("999-99-9999");
	$(".maskProd").mask("a*-999-a999", { placeholder: " " });
	$(".maskEye").mask("~9.99 ~9.99 999");
	$(".maskPo").mask("PO: aaa-999-***");
	$(".maskPct").mask("99,99%");
	$(".maskCep").mask("99999-999");
	$(".maskCpf").mask("999.999.999-99");
	$(".maskCnpj").mask("99.999.999/9999-99");

	//===== Dual select boxes =====//

	$.configureBoxes();
	
	
	//===== Wizards =====//
	
	$("#wizard1").formwizard({
		formPluginEnabled: true, 
		validationEnabled: false,
		focusFirstInput : false,
		disableUIStyles : true,
	
		formOptions :{
			success: function(data){$("#status1").fadeTo(500,1,function(){ $(this).html("<span>Form was submitted!</span>").fadeTo(5000, 0); })},
			beforeSubmit: function(data){$("#w1").html("<span>Form was submitted with ajax. Data sent to the server: " + $.param(data) + "</span>");},
			resetForm: true
		}
	});
	
	// Wizard da contratação
	$("#wizard2").formwizard({ 
		formPluginEnabled: true,
		validationEnabled: true,
		focusFirstInput : false,
		disableUIStyles : true,
	
		formOptions :{
			//success: function(data){$("#status2").fadeTo(500,1,function(){ $(this).html("<span>Form was submitted!</span>").fadeTo(5000, 0); })},
			//beforeSubmit: function(data){$("#w2").html("<span>Form was submitted with ajax. Data sent to the server: " + $.param(data) + "</span>");},
			beforeSubmit: function(data) { contratarWF(); },
			dataType: 'json',
			resetForm: true
		},
		validationOptions : {
			rules: {
				required: "required",
				email: { required: true, email: true }
			}
			/*messages: {
				bazinga: "Bazinga. This note is editable",
				email: { required: "Please specify your email", email: "Correct format is name@domain.com" }
			}*/
		}
	});
	
	$("#wizard3").formwizard({
		formPluginEnabled: false, 
		validationEnabled: false,
		focusFirstInput : false,
		disableUIStyles : true
	});
	
	
	//===== Validation engine =====//
	
	$("#validate").validationEngine();
	
	
	//===== WYSIWYG editor =====//
	
	$("#editor").cleditor({
		width:"100%", 
		height:"100%",
		bodyStyle: "margin: 10px; font: 12px Arial,Verdana; cursor:text"
	});
	
	
	//===== File uploader =====//
/*	
	$("#uploader").pluploadQueue({
		runtimes : 'html5,html4',
		url : 'php/upload.php',
		max_file_size : '1mb',
		unique_names : true,
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"}
			//{title : "Zip files", extensions : "zip"}
		]
	});
*/	
	
	//===== Tags =====//	
		
	$('#tags').tagsInput({width:'100%'});
		
		
	//===== Autogrowing textarea =====//
	
	$('.autoGrow').autosize();




/* General stuff
================================================== */


	//===== Left navigation styling =====//
	
	$('li.this').prev('li').css('border-bottom-color', '#2c3237');
	$('li.this').next('li').css('border-top-color', '#2c3237');
	

	
	//===== User nav dropdown =====//		
	
	$('.hp').click(function () {
		$('.helpDropdown').slideToggle(200);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("hp"))
		$(".helpDropdown").slideUp(200);
	});
	
	$('.dd').click(function () {
		$('.userDropdown').slideToggle(200);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("dd"))
		$(".userDropdown").slideUp(200);
	});
	  
	  
	  
	//===== Statistics row dropdowns =====//	
		
	$('.ticketsStats > h2 a').click(function () {
		$('#s1').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("ticketsStats"))
		$("#s1").slideUp(150);
	});
	
	
	$('.visitsStats > h2 a').click(function () {
		$('#s2').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("visitsStats"))
		$("#s2").slideUp(150);
	});
	
	
	$('.usersStats > h2 a').click(function () {
		$('#s3').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("usersStats"))
		$("#s3").slideUp(150);
	});
	
	
	$('.ordersStats > h2 a').click(function () {
		$('#s4').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("ordersStats"))
		$("#s4").slideUp(150);
	});

	//===== Botão Drop Down =====//	

	filtroCalcular = false;
	
	$('.btnDropDown').click(function () {
		
		if(!filtroCalcular){
			filtroCalcular = function(lista_class){
			
				if(lista_class){
			
					var selected = 0;
					var icon_list, count_list;
					
					if(lista_class=='listItens1'){
				
						icon_list = "iconList1";
						count_list = "countList1";
						$('#contasFinanceiras div.checker span input:checkbox').each(function() {
							var checkedStatus = this.checked
							if (checkedStatus == true) {
								selected ++;
							}
						})
				
					}else if(lista_class=='listItens2'){
				
						icon_list = "iconList2";
						count_list = "countList2";
						$('#tpLnct div.checker span input:checkbox').each(function() {
							var checkedStatus = this.checked
							if (checkedStatus == true) {
								selected ++;
							}
						})
				
					}else if(lista_class=='listItens3'){
				
						icon_list = "iconList3";
						count_list = "countList3";
						var ctr = document.getElementById("ct_resp_pesq").value;
						var plc = document.getElementById("pl_cnt_pesq").value;
						if(ctr!=0){
							selected += 1;
						}
						if(plc!=0){
							selected += 1;
						}
	
					}else if(lista_class=='listItens4'){
				
						icon_list = "iconList4";
						count_list = "countList4";
						$('#tpVenc div.checker span input:checkbox').each(function() {
							var checkedStatus = this.checked
							if (checkedStatus == true) {
								selected ++;
							}
						})
				
					}else if(lista_class=='listItens5'){
				
						icon_list = "iconList5";
						count_list = "countList5";
						var fav = document.getElementById("fav_pesq").value;
						var valor = document.getElementById("vl_pesq").value;
						var doc = document.getElementById("doc_pesq").value;
						var forma_pgto = document.getElementById("forma_pgto_pesq").value;
						var prcl = document.getElementById("prcl_pesq").checked;
						var nossoNumero = document.getElementById("nosso-numero-pesquisar").value;

						if(fav!=0){selected += 1;}
						if(valor!="0,00" && valor!=""){selected += 1;}
						if(doc!=""){selected += 1;}
						if(forma_pgto!=""){selected += 1;}
						if(prcl==true){selected += 1;}
						if (nossoNumero != "") { selected += 1; }
					}
				
					if(selected>0){
						document.getElementById(count_list).innerHTML = selected;
						document.getElementById(icon_list).style.display = 'none';
						document.getElementById(count_list).style.display = 'block';
					}else{
						document.getElementById(count_list).innerHTML = '0';
						document.getElementById(count_list).style.display = 'none';
						document.getElementById(icon_list).style.display = 'block';
					}		
				}
			}
		}
				
	});

	$('.btnDropDown').click(function () {
		var $id = $(this).attr('id');
		$('.'+$id).toggle(0,'show');
		$("#dados").data('listItemAtivo',$id);
	});

	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		var listItemAtivo = $("#dados").data('listItemAtivo');
		if (! $clicked.parents().hasClass("dropDownList1") ){
			//$("#s4").slideUp(150);
			if(filtroCalcular){
				filtroCalcular(listItemAtivo);
				$('.listItens1').hide();
			}
		}
	});

	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("dropDownList2") )
		//$("#s4").slideUp(150);
				$('.listItens2').hide();
	});	
	
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		var listItemAtivo = $("#dados").data('listItemAtivo');
		if (! $clicked.parents().hasClass("dropDownList3") && ! $clicked.parents().hasClass("ui-autocomplete")){
		//$("#s4").slideUp(150);
			if(filtroCalcular){
				filtroCalcular(listItemAtivo);
				$('.listItens3').hide();
			}
		}
	});	

	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		var listItemAtivo = $("#dados").data('listItemAtivo');
		if (! $clicked.parents().hasClass("dropDownList4")){
		//$("#s4").slideUp(150);
			if(filtroCalcular){
			filtroCalcular(listItemAtivo);
			$('.listItens4').hide();
			}
		}
	});	

	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		var listItemAtivo = $("#dados").data('listItemAtivo');
		if (! $clicked.parents().hasClass("dropDownList5") && ! $clicked.parents().hasClass("ui-autocomplete")){
		//$("#s4").slideUp(150);
			if(filtroCalcular){
				filtroCalcular(listItemAtivo);
				$('.listItens5').hide();
			}
		}
	});
	
	//===== Botão Drop Down - Check All =====//	

	$('.btnDropDownCk').live('click',function () {
		var id = $(this).attr('id');
		var dropDownMenuCk = document.getElementById(id).nextElementSibling;
		var listItemAtivoId = dropDownMenuCk.id;
		var visible = dropDownMenuCk.style.display;
		if(visible==='block'){
			dropDownMenuCk.style.display = 'none';
		}else{
			dropDownMenuCk.style.display = 'block';
		}
		//$('.'+id).toggle(0,'show');
		$("#dados").data({'btnDropDownCkAtivo':id,'listItemAtivoId':listItemAtivoId});
	});

	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		var btnDropDownCkAtivo = $("#dados").data('btnDropDownCkAtivo');
		var listItemAtivoId = $("#dados").data('listItemAtivoId');
		if( $clicked.attr('id') !== btnDropDownCkAtivo )
			$('#'+listItemAtivoId).hide();
	});

	//===== DropDown Cotação de Moedas como Dolar, Euro e Libra =====//	
		
	$('.moedaDetalhes').click(function () {
		$('#s1').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("ticketsStats"))
		$("#s1").slideUp(150);
	});
	
	
	$('.moedaDetalhes').click(function () {
		$('#s2').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("visitsStats"))
		$("#s2").slideUp(150);
	});
	
	
	$('.moedaDetalhes').click(function () {
		$('#s3').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("usersStats"))
		$("#s3").slideUp(150);
	});
	
	
	$('.moedaDetalhes').click(function () {
		$('#s4').slideToggle(150);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("ordersStats"))
		$("#s4").slideUp(150);
	});
	
	
	
	//===== Collapsible elements management =====//
	
	$('.exp').collapsible({
		defaultOpen: 'current',
		cookieName: 'navAct',
		cssOpen: 'active',
		cssClose: 'inactive',
		speed: 200
	});
	
	$('.opened').collapsible({
		defaultOpen: 'opened,toggleOpened',
		cssOpen: 'inactive',
		cssClose: 'normal',
		speed: 200
	});
	
	$('.closed').collapsible({
		defaultOpen: '',
		cssOpen: 'inactive',
		cssClose: 'normal',
		speed: 200
	});
	
	
	$('.goTo').collapsible({
		defaultOpen: 'openedDrop',
		cookieName: 'smallNavAct',
		cssOpen: 'active',
		cssClose: 'inactive',
		speed: 100
	});
	


	
	//===== Middle navigation dropdowns =====//
	
	$('.mUser').click(function () {
		$('.mSub1').slideToggle(100);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("mUser"))
		$(".mSub1").slideUp(100);
	});
	
	$('.mMessages').click(function () {
		$('.mSub2').slideToggle(100);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("mMessages"))
		$(".mSub2").slideUp(100);
	});
	
	$('.mFiles').click(function () {
		$('.mSub3').slideToggle(100);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("mFiles"))
		$(".mSub3").slideUp(100);
	});
	
	$('.mOrders').click(function () {
		$('.mSub4').slideToggle(100);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("mOrders"))
		$(".mSub4").slideUp(100);
	});



	//===== User nav dropdown =====//		
	
	$('.sidedd').click(function () {
		$('.sideDropdown').slideToggle(200);
	});
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("sidedd"))
		$(".sideDropdown").slideUp(200);
	});
	
	




/* Tables
================================================== */


	//===== Check all checbboxes =====//
	
	$(".titleIcon input:checkbox").click(function() {
		var checkedStatus = this.checked;
		$("#checkAll tbody tr td:first-child input:checkbox").each(function() {
			this.checked = checkedStatus;
				if (checkedStatus == this.checked) {
					$(this).closest('.checker > span').removeClass('checked');
				}
				if (this.checked) {
					$(this).closest('.checker > span').addClass('checked');
				}
		});
	});	
	
	$('#checkAll tbody tr td:first-child').next('td').css('border-left-color', '#CBCBCB');
	
	
	
	//===== Resizable columns =====//
	
	$("#res, #res1").colResizable({
		liveDrag:true,
		draggingClass:"dragging" 
	});
	  
	  
	  
	//===== Sortable columns =====//
	
	$("table").tablesorter();
	
	
	
	//===== Dynamic data table =====//
	
	oTable = $('.dTable').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"itemsPerPage"fl>t<"F"ip>',
		//"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"oLanguage": {
			"sLengthMenu": "<span>Mostrar:</span> _MENU_",
			"sSearch": "<span>Localizar:</span> _INPUT_ <i class='srch'></i>"
		}
	});

	
/* # Pickers
================================================== */


	//===== Color picker =====//
	/*
	$('#cPicker').ColorPicker({
		color: '#e62e90',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#cPicker div').css('backgroundColor', '#' + hex);
		}
	});
	
	$('#flatPicker').ColorPicker({flat: true});
	*/
	
	
	//===== Time picker =====//
	
	$('.timepicker').timeEntry({
		show24Hours: true, // 24 hours format
		showSeconds: true, // Show seconds?
		spinnerImage: 'images/forms/spinnerUpDown.png', // Arrows image
		spinnerSize: [19, 30, 0], // Image size
		spinnerIncDecOnly: true // Only up and down arrows
	});
	
	
	//===== Datepickers Month =====//
	$('.monthpicker').monthpicker({
	    autoSize: true,
		//pattern: 'mm/yyyy',
        //monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNames: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior',
	});
	
	// bt_monthpicker
	$('.bt_monthpicker').bind('click', function () {
			$('.monthpicker').monthpicker('show');
	});

	//===== Datepickers=====//
	$( ".datepicker, .datepickerFullWidth" ).datepicker({ 
	//	defaultDate: +7,
		autoSize: true,
	//	appendText: '(dd-mm-yyyy)',
		dateFormat: 'dd/mm/yy',
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior',
		changeMonth: true,
		changeYear: true,
//- See more at: http://www.botecodigital.info/web/jquery-ui-datepicker-em-portugues/#sthash.A8ROIVBS.dpuf
	});
	
	$('.datepicker').on('blur', function () {
	    var data = $(this).val();
	    var validacao = true;
	    if (data != '')
	        validacao = dataValidar(data);
	    if (!validacao) {
	        alert('Data inválida!');
	        $(this).val('');
	    }
	})

	//===== Monthpicker Report =====//
	$( ".monthpickerReport" ).datepicker({ 
	//	defaultDate: +7,
		autoSize: true,
	//	appendText: '(dd-mm-yyyy)',
		dateFormat: 'mm/yy',
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    nextText: 'Próximo',
    prevText: 'Anterior',
		changeMonth: true,
		changeYear: true,
		beforeShow : function(input, inst) {
       if ((selDate = $(this).val()).length > 0)
       {
          year = selDate.substring(selDate.length - 4, selDate.length);
          month = selDate.substring(0, 2);
					$(this).datepicker('option', 'defaultDate', new Date(year, month-1, 1));
          $(this).datepicker('setDate', new Date(year, month-1, 1));
       }
		}
//- See more at: http://www.botecodigital.info/web/jquery-ui-datepicker-em-portugues/#sthash.A8ROIVBS.dpuf
	});	

/*	
	$( ".datepickerInline" ).datepicker({ 
	//	defaultDate: +7,
		autoSize: true,
	//	appendText: '(dd-mm-yyyy)',
		dateFormat: 'dd/mm/yy',
		numberOfMonths: 1,
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    nextText: 'Próximo',
    prevText: 'Anterior'
	});	
*/
	
			
//===== Progress bars =====//
	
	// default mode
	$('#progress1').anim_progressbar();
	
	// from second #5 till 15
	var iNow = new Date().setTime(new Date().getTime() + 5 * 1000); // now plus 5 secs
	var iEnd = new Date().setTime(new Date().getTime() + 15 * 1000); // now plus 15 secs
	$('#progress2').anim_progressbar({start: iNow, finish: iEnd, interval: 1});
	
	// jQuery UI progress bar
	$( "#progress" ).progressbar({
			value: 80
	});
	
	
	
	//===== Animated progress bars =====//
	
	var percent = $('.progressG').attr('title');
	$('.progressG').animate({width: percent},1000);
	
	var percent = $('.progressO').attr('title');
	$('.progressO').animate({width: percent},1000);
	
	var percent = $('.progressB').attr('title');
	$('.progressB').animate({width: percent},1000);
	
	var percent = $('.progressR').attr('title');
	$('.progressR').animate({width: percent},1000);
	
	
	
	
	var percent = $('#bar1').attr('title');
	$('#bar1').animate({width: percent},1000);
	
	var percent = $('#bar2').attr('title');
	$('#bar2').animate({width: percent},1000);
	
	var percent = $('#bar3').attr('title');
	$('#bar3').animate({width: percent},1000);
	
	var percent = $('#bar4').attr('title');
	$('#bar4').animate({width: percent},1000);
	
	var percent = $('#bar5').attr('title');
	$('#bar5').animate({width: percent},1000);

	var percent = $('#bar6').attr('title');
	$('#bar6').animate({width: percent},1000);

	var percent = $('#bar7').attr('title');
	$('#bar7').animate({width: percent},1000);

	var percent = $('#bar8').attr('title');
	$('#bar8').animate({width: percent},1000);

	var percent = $('#bar9').attr('title');
	$('#bar9').animate({width: percent},1000);




/* Other plugins
================================================== */


	//===== File manager =====//
	
	var elf = $('#fm').elfinder({
		url : 'php/connector.php',  // connector URL (REQUIRED)
		uiOptions : {
			// toolbar configuration
			toolbar : [
				['back', 'forward'],
				['info'],
				['quicklook'],
				['search']
			]
		},
		contextmenu : {
		  // Commands that can be executed for current directory
		  cwd : ['reload', 'delim', 'info'], 
		  // Commands for only one selected file
		  files : ['select', 'open']
		}
	}).elfinder('instance');	

	
	//===== Calendar =====//
	
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
	$('.calendar').fullCalendar({
		header: {
			left: 'prev,next',
			center: 'title',
			right: 'month,basicWeek,basicDay'
		},
		editable: true,
		events: [
			{
				title: 'All day event',
				start: new Date(y, m, 1)
			},
			{
				title: 'Long event',
				start: new Date(y, m, 5),
				end: new Date(y, m, 8)
			},
			{
				id: 999,
				title: 'Repeating event',
				start: new Date(y, m, 2, 16, 0),
				end: new Date(y, m, 3, 18, 0),
				allDay: false
			},
			{
				id: 999,
				title: 'Repeating event',
				start: new Date(y, m, 9, 16, 0),
				end: new Date(y, m, 10, 18, 0),
				allDay: false
			},
			{
				title: 'Background color could be changed',
				start: new Date(y, m, 30, 10, 30),
				end: new Date(y, m, d+1, 14, 0),
				allDay: false,
				color: '#5c90b5'
			},
			{
				title: 'Lunch',
				start: new Date(y, m, 14, 12, 0),
				end: new Date(y, m, 15, 14, 0),
				allDay: false
			},
			{
				title: 'Birthday PARTY',
				start: new Date(y, m, 18),
				end: new Date(y, m, 20),
				allDay: false
			},
			{
				title: 'Clackable',
				start: new Date(y, m, 27),
				end: new Date(y, m, 29),
				url: 'http://themeforest.net/user/Kopyov'
			}
		]
	});
	
	
	
	
/* UI stuff
================================================== */


	//===== Sparklines =====//
	
	$('.negBar').sparkline('html', {type: 'bar', barColor: '#db6464'} );
	$('.posBar').sparkline('html', {type: 'bar', barColor: '#6daa24'} );
	$('.zeroBar').sparkline('html', {type: 'bar', barColor: '#4e8fc6'} ); 
	
	
	
	//===== Tooltips =====//

	$('.tipN').tipsy({gravity: 'n',fade: true});
	$('.tipS').tipsy({gravity: 's',fade: true});
	$('.tipW').tipsy({gravity: 'w',fade: true});
	$('.tipE').tipsy({gravity: 'e',fade: true});

$(document).on("click",function(){
	$('.tipN').tipsy({gravity: 'n',fade: true});
	$('.tipS').tipsy({gravity: 's',fade: true});
	$('.tipW').tipsy({gravity: 'w',fade: true});
	$('.tipE').tipsy({gravity: 'e',fade: true});
});
		

	//===== Notification boxes =====//
	
	$(".hideit").click(function() { $(this).slideUp();
		/*$(this).fadeTo(200, 0.00, function(){ //fade
			$(this).slideUp(300, function() { //slide up 
			//	$(this).remove(); //then remove from the DOM
			});
		});*/
	});	
	
	
	
	//===== Lightbox =====//
	
	//$("a[rel^='lightbox']").prettyPhoto();
	
	
	
	//===== Image gallery control buttons =====//
	
	$(".gallery ul li").hover(
		function() { $(this).children(".actions").show("fade", 200); },
		function() { $(this).children(".actions").hide("fade", 200); }
	);
	
	
	//===== Spinner options =====//
	
	$( "#spinner-default" ).spinner();
	
	$( "#spinner-decimal" ).spinner({
		step: 0.01,
		numberFormat: "n"
	});
	
	$( "#culture" ).change(function() {
		var current = $( "#spinner-decimal" ).spinner( "value" );
		Globalize.culture( $(this).val() );
		$( "#spinner-decimal" ).spinner( "value", current );
	});
	
	$( "#currency" ).change(function() {
		$( "#spinner-currency" ).spinner( "option", "culture", $( this ).val() );
	});
	
	$( "#spinner-currency" ).spinner({
		min: 5,
		max: 2500,
		step: 25,
		start: 1000,
		numberFormat: "C"
	});
		
	$( "#spinner-overflow" ).spinner({
		spin: function( event, ui ) {
			if ( ui.value > 10 ) {
				$( this ).spinner( "value", -10 );
				return false;
			} else if ( ui.value < -10 ) {
				$( this ).spinner( "value", 10 );
				return false;
			}
		}
	});
	
	$.widget( "ui.timespinner", $.ui.spinner, {
		options: {
			// seconds
			step: 60 * 1000,
			// hours
			page: 60
		},

		_parse: function( value ) {
			if ( typeof value === "string" ) {
				// already a timestamp
				if ( Number( value ) == value ) {
					return Number( value );
				}
				return +Globalize.parseDate( value );
			}
			return value;
		},

		_format: function( value ) {
			return Globalize.format( new Date(value), "t" );
		}
	});

	$( "#spinner-time" ).timespinner();
	$( "#culture-time" ).change(function() {
		var current = $( "#spinner-time" ).timespinner( "value" );
		Globalize.culture( $(this).val() );
		$( "#spinner-time" ).timespinner( "value", current );
	});
	
	
	
	//===== UI dialog =====//
	
	$( "#dialog-message" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener" ).click(function() {
		$( "#dialog-message" ).dialog( "open" );
		return false;
	});	

	//===== UI dialog - Contas a Receber =====//
	
	
	$( "#dialog-message-conta-receber" ).dialog({
		autoOpen: false,
		modal: true,
		width: '800',
		buttons: {	
			Cancelar:	function() {
				$( this ).dialog( "close" );
			},
			Agendar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-conta-receber" ).click(function() {
		$( "#dialog-message-conta-receber" ).dialog( "open" );
		return false;
	});	
	


	//===== UI dialog - Contas a Pagar =====//
	
	$( "#dialog-message-conta-pagar" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		buttons: {
			Cancelar: function() {
				$( this ).dialog( "close" );
			},
			Agendar: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$( "#opener-conta-pagar" ).click(function() {
		$( "#dialog-message-conta-pagar" ).dialog( "open" );
		return false;
	});	
	
	//===== UI dialog - Alerta =====//
	
	$( "#dialog-alerta" ).dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		position: {my: "top", at: "top+5%", of: window}, //https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
		resizable: 'false',
		buttons: {	
			Não:	function() {
				$( this ).dialog( "close" );
			},
			Sim: function() {
				$( this ).dialog( "close" );
			}
		}
	});	

	//===== UI dialog - Data de compensação =====//
	
	$( "#dialog-dt-compensacao" ).dialog({
		autoOpen: false,
		modal: true,
		width: '250',
		buttons: {	
			Confirmar:	function() {
				funcao();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});	

	//===== UI dialog - Data de compensação para lançamento recorrente =====//
	
	$( "#dialog-dt-compensacao-rcr" ).dialog({
		autoOpen: false,
		modal: true,
		width: '250',
		buttons: {	
			Quitar:	function() {
				compensarLancamentoRcr();
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		}
	});


    //===== UI dialog - Favorecido Auto Completar =====//

	$("#dialog-favorecido-ac").dialog({
	    autoOpen: false,
	    modal: true,
	    width: '300',
	    closeOnEscape: false,
        dialogClass: 'ui-dialog-no-close-x',
	    buttons: {
	        Salvar: function () {
	            if ($('#formfavAC').valid()) {
	                var nome = $('#dialog-favorecido-ac .favNome').val();
	                var campo_id = $('#dialog-favorecido-ac .formOrigem').val();
	                var inscricao = $('#dialog-favorecido-ac .favInsc').val();
	                var cpf_cnpj = $('#dialog-favorecido-ac .favCPF_CNPJ').val();

	                favorecidosIncluirAc(nome, campo_id, inscricao, cpf_cnpj);
	                $(this).dialog("close");
	            }
	        },
	        Cancelar: function () {
	            $('.favorecido_buscar').val('');
	            $(this).dialog("close");
	        }
	    }
	});

	$("#opener").click(function () {
	    $("#dialog-favorecido-ac").dialog("open");
	    return false;
	});
	
	//===== UI dialog - Convite Contador =====//
/*	
	$( "#dialog-convite-contador" ).dialog({
		autoOpen: false,
		modal: true,
		Width: 'auto',
		buttons: {	
			Cancelar:	function() {
				$( this ).dialog( "close" );
			},
			Salvar: function() {
				senha_alterar();
			}
		}
	});
	
	$( "#opener-convite-contador" ).click(function() {
		$( "#dialog-convite-contador" ).dialog( "open" );
		return false;
	});	
*/	
	//===== Breadcrumbs =====//
    
		//$('#breadcrumbs').xBreadcrumbs({
			//showAction: "click"
			//collapsible: true,
			//showEffect: "slide"
		//});
	
		
	//===== jQuery UI sliders =====//	
	
	$( ".uiSlider" ).slider(); /* Usual slider */
	
	
	$( ".uiSliderInc" ).slider({ /* Increments slider */
		value:100,
		min: 0,
		max: 500,
		step: 50,
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.value );
		}
	});
	$( "#amount" ).val( "$" + $( ".uiSliderInc" ).slider( "value" ) );
		
		
	$( ".uiRangeSlider" ).slider({ /* Range slider */
		range: true,
		min: 0,
		max: 500,
		values: [ 75, 300 ],
		slide: function( event, ui ) {
			$( "#rangeAmount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
		}
	});
	$( "#rangeAmount" ).val( "$" + $( ".uiRangeSlider" ).slider( "values", 0 ) +" - $" + $( ".uiRangeSlider" ).slider( "values", 1 ));
			
			
	$( ".uiMinRange" ).slider({ /* Slider with minimum */
		range: "min",
		value: 37,
		min: 1,
		max: 700,
		slide: function( event, ui ) {
			$( "#minRangeAmount" ).val( "$" + ui.value );
		}
	});
	$( "#minRangeAmount" ).val( "$" + $( ".uiMinRange" ).slider( "value" ) );
	
	
	$( ".uiMaxRange" ).slider({ /* Slider with maximum */
		range: "max",
		min: 1,
		max: 100,
		value: 20,
		slide: function( event, ui ) {
			$( "#maxRangeAmount" ).val( ui.value );
		}
	});
	$( "#maxRangeAmount" ).val( $( ".uiMaxRange" ).slider( "value" ) );	

	//===== Moeda =====//

	$('.moeda').priceFormat({
			prefix: '',
			centsSeparator: ',',
			thousandsSeparator: '.',
	});

	//===== Auto Complete fields Check and Close  =====//
	$('<span class="check-autocomplete-container" style="display:none"><img src="images/loaders/loader9.gif" width="14" alt="" class=" tipS" title="Aguarde"/></span>').insertAfter(".input-buscar");
  $('<span class="close-autocomplete-container close-red"><img src="images/icons/color/close_red.png" alt="" class=" tipS" title="Limpar seleção"/></span>').insertAfter(".input-buscar");
	$('<span class="check-autocomplete-container check-green" style="display:none"><img src="images/icons/color/check_green.png" alt="" class=" tipS" title="Opção selecionada"/></span>').insertAfter(".input-buscar");
	$('<span class="check-autocomplete-container check-dark"><img src="images/icons/dark/check.png" alt="" class="tipS check-dark" title="Selecione uma opção"/></span>').insertAfter(".input-buscar");
	
	$('.input-buscar').each(function(){
		var input_buscar_nome = $(this).attr('name'); //$(this).attr('id');
		var check_dark_id = input_buscar_nome+'_cd';
		$(this).next().attr('id',check_dark_id);
		var check_green_id = input_buscar_nome+'_cg';
		$(this).next().next().attr('id',check_green_id);
		var colse_red_id = input_buscar_nome+'_cr';
		$(this).next().next().next().attr('id',colse_red_id);
		var aguarde_id = input_buscar_nome+'_aguarde';
		$(this).next().next().next().next().attr('id',aguarde_id);
	})

	$('.close-red').click(function(){
		var close_red_id = $(this).attr('id');
		var input_buscar_nome = close_red_id.substr(0,close_red_id.length-3);//close_red_id.substr(0,close_red_id.length-6);
		var check_green_id = input_buscar_nome+'_cg';
		$('input[name="'+input_buscar_nome+'"]').val('');
		$('input[name="'+input_buscar_nome+'"]').attr('disabled',false);
		$('#'+input_buscar_nome).val(0);
		$('#'+check_green_id).css('display','none');
		//esconde o upload do extrato em conciliação
		//if(close_red_id=='conta_id_import_cr'){
			//document.getElementById("lnct_uploader").style.display = 'none';
		//}
		if(close_red_id=='bancos_buscar_cr' || close_red_id=='bancos_buscar_editar_cr'){
			$('#dados').data('emite_boleto','0');
			//document.getElementById("emite_boleto").value = 0;
		}
	})
	//===== End Auto Complete fields Check and Close  =====//

	//===== Auto Complete - Check search trigger  =====//

	$('.check-dark').click(function(){
		if( $(this).prop('tagName') == 'IMG' ){
			var ck_id = $(this).parent().attr('id');
		}else{
			var ck_id = $(this).attr('id');
		}
		var ck_id_len = ck_id.length;
		var input_name = ck_id.substring(0,ck_id_len-3); //var input_search_id = ck_id.substring(0,ck_id_len-3);
		//var input_search_id = $('input[name="'+input_name+'"]').attr('id');
		//alert(ck_id);
		//alert(input_name);
		//alert(input_search_id);
		if($("#dados").data('search-active-name')!=""){
			var search_active_name = $("#dados").data('search-active-name');
			$( "input[name='"+search_active_name+"']").autocomplete('close');
		}
		$("#dados").data('search-active-name',input_name);
		$( "input[name='"+input_name+"']").autocomplete( "search" );//$( "#"+input_search_id ).autocomplete( "search" );
	})

    //Fecha o autocompletar ao clicar em qualquer local do DOM quando a busca é acionada pelo clique no check dark
	$(document).bind('click', function(e) {
	    var search_active_name = $("#dados").data('search-active-name');
	    //console.log(search_active_name);
	    if (search_active_name != ''){
		    var $clicked = $(e.target);
		    if ( (!$clicked.hasClass("check-dark"))  && ($clicked.attr('name')!=search_active_name) ){
			    $( "input[name='"+search_active_name+"']").autocomplete('close');//$('#'+search_active_id).autocomplete('close');
		    }
	    }
	});

	//===== Auto Complete - Favorecidos  =====//

	//var cache = {};

	$( ".favorecido_buscar" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache ) {
				//response( cache[ term ] );
				//return;
			//}
			$.getJSON( "php/favorecidos_buscar.php", request, function( data, status, xhr ) {
				//cache[ term ] = data;
				response( data );
			});
		},
		search: function( event, ui ) {
			var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
			$('#'+campo_id+'_aguarde').css('display','block');
		},
		response: function( event, ui ) {
			var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
			$('#'+campo_id+'_aguarde').css('display','none');
			//if(ui.content.length==0){
				//alert('nenhum resultado encontrado');
			//}
			//alert('resposta');
		},
		select: function( event, ui ) {
			var campo_id = $(this).attr('name');
			$('#'+campo_id).val(ui.item.id);
			if(ui.item.id=="add")
			    favorecidoPreencherAC(ui.item.value, campo_id);
			    //favorecidosIncluirAc(ui.item.value,campo_id);
			else
			if(ui.item.plc_id!=0 || ui.item.ctr_id!=0){
				var tp_lnct = $(this).data('tp-lnct');
				var form_id = $(this).data('form-id');
				if(tp_lnct=='R')
					favorecidoCtrPlc(ui.item.cliente_ctr_id,ui.item.cliente_plc_id,form_id);
				else
				    favorecidoCtrPlc(ui.item.fornecedor_ctr_id, ui.item.fornecedor_plc_id, form_id);
			$('#'+campo_id+'_cg').css('display','block');
			$(this).attr('disabled','disabled');
			fadeOut($(this).attr('id'));
			}

		    /*$('#'+campo_id+'_cg').css('display','block');
			$(this).attr('disabled','disabled');
			fadeOut($(this).attr('id'));*/
	  }
	});
	
	$( ".favorecido_buscar" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//===== End Auto Complete - Favorecidos  =====//
	
	//===== Auto Complete - Conta Financeira  =====//
	
	//var cache2 = {};
	$( ".conta_buscar" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			//var term = request.term;
			//if ( term in cache2 ) {
				//response( cache2[ term ] );
				//return;
			//}
			$.getJSON( "php/contas_buscar.php", request, function( data, status, xhr ) {
				//cache2[ term ] = data;
				response( data );
			});
		},
		search: function( event, ui ) {
			var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
			$('#'+campo_id+'_aguarde').css('display','block');
		},
		response: function( event, ui ) {
			//alert(ui);
			var campo_id = $(this).attr('name'); //var campo_id = $(this).attr('id');
			$('#'+campo_id+'_aguarde').css('display','none');
			//if(ui.content.length==0){
				//alert('nenhum resultado encontrado');
			//}
			//alert('resposta');
		},
		select: function( event, ui ) {
			var campo_id = $(this).attr('name');
			$('#'+campo_id).val(ui.item.id).trigger('change');
			if(ui.item.id=="add"){
				contasIncluirAc(ui.item.value,campo_id);
			}
	 		$('#'+campo_id+'_cg').css('display','block');
			//exibe caixa de upload para extrato em conciliação
			//if(campo_id=='conta_id_import'){
				//document.getElementById('lnct_uploader').style.display = 'block';
			//}else if(campo_id=='conta_arq_ret_import'){
				//document.getElementById('arq-ret-uploader').style.display = 'block';
			//}
			$(this).attr('disabled','disabled');
			fadeOut($(this).attr('id'));
	  }
	});
	
	$( ".conta_buscar" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//===== End Auto Complete - Conta Financeira  =====//

	//======== CENTRO DE RESPONSABILIDADE ===================
	//var cache3 = {};

	var ctr_search = function (event, ui) {
	    var campo_id = $(this).attr('name');
	    $('#' + campo_id + '_aguarde').css('display', 'block');
	}

	var ctr_response = function (event, ui) {
	    var campo_id = $(this).attr('name');
	    $('#' + campo_id + '_aguarde').css('display', 'none');
	    if (ui.content.length == 0) {
	        //alert('nenhum resultado encontrado');
	    }
	    //alert('resposta');
	}

	var ctr_select = function (event, ui) {
	    var campo_id = $(this).attr('name');
	    $('#' + campo_id).val(ui.item.id);
	    $('#' + campo_id + '_cg').css('display', 'block');
	    $(this).attr('disabled', 'disabled');
	    fadeOut($(this).attr('id'));
	}

	var ctr_source_url = "php/centro_resp_buscar.php";
	var ctr_source = function (request, response) {
	    $.getJSON(ctr_source_url, request, function (data, status, xhr) {
	        response(data);
	    });
	}

	var ctr_render_item = function (ul, item) {
	    //Add the ui-state-disabled class and don't wrap in <a> if value is empty
	    if (item.tp_ctr_plc == 1) {
	        return $("<li>").append("<a>" + item.label + "</a>").appendTo(ul);
	    } else {
	        return $("<li class='ui-state-disabled'>").append("<span>" + item.label + "</span>").appendTo(ul);
	    }
	    console.log(item.tp_ctr_plc);
	}

	if (typeof ($("#form_rcbt_ct_resp_buscar").val()) !== 'undefined') {
	    $("#form_rcbt_ct_resp_buscar").autocomplete({
	        minLength: 0,
	        source: ctr_source,
	        search: ctr_search,
	        response: ctr_response,
	        select: ctr_select
	    }).data("ui-autocomplete")._renderItem = ctr_render_item;
	}
	
	if (typeof ($("#form_pgto_ct_resp_buscar").val()) !== 'undefined') {
	    $("#form_pgto_ct_resp_buscar").autocomplete({
	        minLength: 0,
	        source: ctr_source,
	        search: ctr_search,
	        response: ctr_response,
	        select: ctr_select
	    }).data("ui-autocomplete")._renderItem = ctr_render_item;
	}

    if (typeof ($("#form-pesq-ctr-buscar").val()) !== 'undefined') {
        $("#form-pesq-ctr-buscar").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

    if (typeof ($("#form_fav_ct_resp_buscar_01").val()) !== 'undefined') {
        $("#form_fav_ct_resp_buscar_01").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

    if (typeof ($("#form_fav_ct_resp_buscar_02").val()) !== 'undefined') {
        $("#form_fav_ct_resp_buscar_02").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

    if (typeof ($("#form_fav_editar_ct_resp_buscar_01").val()) !== 'undefined') {
        $("#form_fav_editar_ct_resp_buscar_01").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

    if (typeof ($("#form_fav_editar_ct_resp_buscar_02").val()) !== 'undefined') {
        $("#form_fav_editar_ct_resp_buscar_02").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

    if (typeof ($("#form_rcbt_editar_ct_resp_buscar").val()) !== 'undefined') {
        $("#form_rcbt_editar_ct_resp_buscar").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

    if (typeof ($("#form_pgto_editar_ct_resp_buscar").val()) !== 'undefined') {
        $("#form_pgto_editar_ct_resp_buscar").autocomplete({
            minLength: 0,
            source: ctr_source,
            search: ctr_search,
            response: ctr_response,
            select: ctr_select
        }).data("ui-autocomplete")._renderItem = ctr_render_item;
    }

	$( ".centro_resp_buscar" ).click(function(){
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})

    //======== FIM COMPLETAR CENTRO DE RESPONSABILIDADE =============

	//======== PLANO DE CONTAS ===================
	//var cache4 = {};

	var plc_search = function (event, ui) {
	    var campo_id = $(this).attr('name');
	    $('#' + campo_id + '_aguarde').css('display', 'block');
	}

	var plc_response = function (event, ui) {
	    var campo_id = $(this).attr('name');
	    $('#' + campo_id + '_aguarde').css('display', 'none');
	    if (ui.content.length == 0) {
	        //alert('nenhum resultado encontrado');
	    }
	    //alert('resposta');
	}

	var plc_select = function (event, ui) {
	    var campo_id = $(this).attr('name');
	    $('#' + campo_id).val(ui.item.id);
	    $('#' + campo_id + '_cg').css('display', 'block');
	    $(this).attr('disabled', 'disabled');
	    fadeOut($(this).attr('id'));
	}

	var plc_source_url = "php/plano_contas_buscar.php";
	var plc_source = function (request, response) {
	    $.getJSON(plc_source_url, request, function (data, status, xhr) {
	        response(data);
	    });
	}

	var plc_render_item = function (ul, item) {
	    //Add the ui-state-disabled class and don't wrap in <a> if value is empty
	    if (item.tp_ctr_plc == 1) {
            if(item.dedutivel==1)
                return $("<li>").append("<a style='color:green;'>" + item.label + "</a>").appendTo(ul);
            else
                return $("<li>").append("<a>" + item.label + "</a>").appendTo(ul);
	    } else {
	        return $("<li class='ui-state-disabled'>").append("<span>" + item.label + "</span>").appendTo(ul);
	    }
	}

	if (typeof ($("#form_rcbt_pl_conta_buscar").val()) !== 'undefined') {
	    $("#form_rcbt_pl_conta_buscar").autocomplete({
	        minLength: 0,
	        source: plc_source,
	        search: plc_search,
	        response: plc_response,
	        select: plc_select
	    }).data("ui-autocomplete")._renderItem = plc_render_item;
	}

	if (typeof ($("#form_pgto_pl_conta_buscar").val()) !== 'undefined') {
	    $("#form_pgto_pl_conta_buscar").autocomplete({
	        minLength: 0,
	        source: plc_source,
	        search: plc_search,
	        response: plc_response,
	        select: plc_select
	    }).data("ui-autocomplete")._renderItem = plc_render_item;
	}

    if (typeof ($("#form-pesq-plc-buscar").val()) !== 'undefined') {
        $("#form-pesq-plc-buscar").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    if (typeof ($("#form_fav_pl_conta_buscar_01").val()) !== 'undefined') {
        $("#form_fav_pl_conta_buscar_01").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    if (typeof ($("#form_fav_pl_conta_buscar_02").val()) !== 'undefined') {
        $("#form_fav_pl_conta_buscar_02").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    if (typeof ($("#form_fav_editar_pl_conta_buscar_01").val()) !== 'undefined') {
        $("#form_fav_editar_pl_conta_buscar_01").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    if (typeof ($("#form_fav_editar_pl_conta_buscar_02").val()) !== 'undefined') {
        $("#form_fav_editar_pl_conta_buscar_02").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    if (typeof ($("#form_rcbt_editar_pl_conta_buscar").val()) !== 'undefined') {
        $("#form_rcbt_editar_pl_conta_buscar").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    if (typeof ($("#form_pgto_editar_pl_conta_buscar").val()) !== 'undefined') {
        $("#form_pgto_editar_pl_conta_buscar").autocomplete({
            minLength: 0,
            source: plc_source,
            search: plc_search,
            response: plc_response,
            select: plc_select
        }).data("ui-autocomplete")._renderItem = plc_render_item;
    }

    $(".plano_contas_buscar").click(function () {
		var campo_id = $(this).attr('id');
		$( "#"+campo_id ).autocomplete( "search" );
	})
	//======== FIM COMPLETAR PLANO DE CONTAS =============

	//======== listener para limpar input onblur e colocar cor de fundo vermelha onclick ========
	input_buscar = document.getElementsByClassName('input-buscar');
	for(i=0;i<input_buscar.length;i++){
		input_buscar[i].addEventListener('blur',function(){
			input_name = this.getAttribute('name');
			item_selected = document.getElementById(input_name).value;
			if(item_selected=='' || item_selected==0){
				this.value = '';
			}
		});
		input_buscar[i].addEventListener('click',function(){
			input_name = this.getAttribute('name');
			item_selected = document.getElementById(input_name).value;
			if(item_selected=='' || item_selected==0){ //orçamentos financeiros
				this.style.backgroundColor = 'rgba(255, 0, 0, 0.06)';
			}
		});
	}

    //===== Tabs =====//

	//$(".tabs").tabs();
    /*
	var tabs = $( ".tabs-sortable" ).tabs();
    tabs.find( ".ui-tabs-nav" ).sortable({
        axis: "x",
        stop: function() {
        tabs.tabs( "refresh" );
        }
    });
	*/
	
});

//==========================================================================================================================================================================================
//==========================================================================================================================================================================================

//===== Adicionar favorecido pelo autocompletar  =====//

function favorecidoPreencherAC(nome, campo_id) {
    $('#formfavAC .favNome').val(nome);
    $('#formfavAC .formOrigem').val(campo_id);
    $('.favCPF_CNPJ').val('');
    $('.favInsc').val('cpf').trigger('change');
    $("#dialog-favorecido-ac").dialog("open");
}

function favorecidosIncluirAc(nome, campo_id, inscricao, cpf_cnpj) {
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=favorecidosIncluirAc";
	params += "&nome=" + nome + "&inscricao=" + inscricao + "&cpf_cnpj=" + cpf_cnpj;
	$.ajax({
		type: 'post',
		url: 'modulos/favorecido/php/funcoes.php',
		data: params,
		cache: true,
		success: function(data){
			var dados = JSON.parse(data);
			document.getElementById(campo_id).value = dados.favorecido_id;
			$("span.aguarde, div.aguarde").css("display", "none");

			$('#' + campo_id + '_cg').css('display', 'block');

			var campo_text = campo_id.split('_id', 1); alert(campo_id);
			$('#' + campo_text).val(nome);
			$('#' + campo_text).attr('disabled', 'disabled');
			fadeOut($('#' + campo_text).attr('id'));
		},
	});
}

//===== Adicionar conta financeira pelo autocompletar  =====//

function contasIncluirAc(descricao,campo_id){
	$("span.aguarde, div.aguarde").css("display","block");
	var params = "funcao=contasIncluirAc";
	params += "&descricao="+descricao;
	$.ajax({
		type: 'post',
		url: 'modulos/conta/php/funcoes.php',
		data: params,
		cache: true,
		success: function(data){
			var dados = JSON.parse(data);
			document.getElementById(campo_id).value = dados.conta_id;
			$("span.aguarde, div.aguarde").css("display","none");
		},
	});
}

//===== Converter texto para valor  =====//

function txtToValor(valor){
	var txt = valor;
	txt = txt.replace(/\./g, '');
	txt =	txt.replace(',','.');
	txt =	parseFloat(txt);
	return txt;
}

//===== Formatação de valores  =====//

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

//===== Efeito de fade  =====//

function fadeOut(element_id){
	element = document.getElementById(element_id);
	opc = 20;
	var increment = -0.3;
	intervalo = setInterval(function(){
		if(opc<=0){
			element.style.backgroundColor='rgb(255,255,255)';
			clearInterval(intervalo);
		}else{
			element.style.backgroundColor='rgba(0, 128, 0, '+opc/100+')';
			opc += increment;
		}
	},1);
}

//===== Recibo  =====//

function recibo(form_id,tipo){
	var params = 'funcao=recibo';
	params += '&favorecido_id='+$('#'+form_id+'_favorecido_id').val();
	params += '&descricao='+$('#'+form_id+'_dscr').val();
	params += '&valor='+$('#'+form_id+'_valor').val();
	params += '&dt_vencimento='+$('#'+form_id+'_dt_vencimento').val();
	params += '&dt_compensacao='+$('#'+form_id+'_dt_compensacao').val();
	params += '&tipo='+tipo;
	var win = window.open("modulos/lancamento/php/funcoes.php?"+params, '_parent');
	win.focus();
}

//===== Buscar Categoria e Centro de Custo ao selecionar favorecido  =====//

function favorecidoCtrPlc(ctr_id,plc_id,form_id){
	var params = 'funcao=favorecidoCtrPlc';
	params += '&ctr_id='+ctr_id;
	params += '&plc_id='+plc_id;
	$.ajax({
		type: 'post',
		url: 'php/favorecido_cat_ctc.php',
		data: params,
		cache: true,
		dataType: 'json',
		success: function(data){
			if(ctr_id!=0){
				$('#'+form_id+'_ct_resp_id').val(ctr_id);
				$('#'+form_id+'_ct_resp_buscar').val(data.ctr);
				$('#'+form_id+' .centro_resp_buscar').attr('disabled',true);
				$('#'+form_id+' span.check-green').eq(3).css('display','block');
			}
			if(plc_id!=0){
				$('#'+form_id+'_pl_conta_id').val(plc_id);
				$('#'+form_id+'_pl_conta_buscar').val(data.plc);
				$('#'+form_id+' .plano_contas_buscar').attr('disabled',true);
				$('#'+form_id+' span.check-green').eq(2).css('display','block');
			}

			if (ctr_id != 0 || plc_id != 0) {
			    //Atribui valor do lançamento e 100% à aba de categoria e centro de custo
			    var valor_lnct = $('#' + form_id + '_valor').val();
			    $('#' + form_id + '_ct_resp_valor').val(valor_lnct);
			    $('#' + form_id + '_ctr_plc_prct').val('100');
			    //Chama função para incluir valor do lançamento na categoria e centro de custo
			    centroRespLnctIncluir(form_id);
			}
		},
	});
}

//===== Validar data =====//

function dataValidar(value) {
    var check = false;
    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    if (re.test(value)) {
        var adata = value.split('/');
        var dd = parseInt(adata[0], 10); // was gg (giorno / day)
        var mm = parseInt(adata[1], 10); // was mm (mese / month)
        var yyyy = parseInt(adata[2], 10); // was aaaa (anno / year)
        var xdata = new Date(yyyy, mm - 1, dd);
        if ((xdata.getFullYear() == yyyy) && (xdata.getMonth() == mm - 1) && (xdata.getDate() == dd))
            check = true;
        else
            check = false;
    } else
        check = false;
    return check;//return this.optional(element) || check;
}

//===== Tourist - Ajuda (tour pelo sistema)  =====//
/*
 TOURS = {

    ajuda: function (){
      var steps = [{
        content: '<p>Primeiro preencha o nome do lançamento. <br>Primeiro preencha o nome do lançamento.</p>',
        highlightTarget: true,
        nextButton: true,
        target: $('#thing1'),
        my: 'bottom center',
        at: 'top center'
      }, {
        content: '<p>Depois clique em salvar</p>',
        highlightTarget: true,
        nextButton: true,
        target: $('#thing2'),
        my: 'bottom center',
        at: 'top center'
      }]

      var tour = new Tourist.Tour({
        steps: steps,
        tipClass: 'Bootstrap',
        tipOptions:{ showEffect: 'slidein' }
      });
      tour.start();
      return tour;
    }	//, // end tour1
  }

   $(function(){

    $('.btn-run-example').click(function(e){
      var btn = $(e.target);
      var tour = TOURS[btn.attr('data-tour')]();
			//alert("teste");

     btn.prop('disabled', true)
      tour.bind('stop', function(){
        btn.prop('disabled', false)
      }); 

      return false;
    });
 });
 

var hlg = window.location.hash;
if(hlg == '#Ajuda'){ $(".btn-run-example").trigger('click'); }else if(hlg == '#cadastro'){ $("#c1").trigger('click'); }
$(window).load(function(){ $('.btn-run-example').click();}); 
*/

/*
===========================================================================================
ATUALIZAR VALOR DO PLANO DE CONTAS E CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function plcCtrValorAtualizar(form) {

    //atualiza valor no campo da aba pc x cr
    var valor_total = 0;

    //var ctr_plc_prct = txtToValor($('#' + form + "_ctr_plc_prct").val());
    var ctr_plc_prct = $('#' + form + "_ctr_plc_prct").val();

    if (ctr_plc_prct > 0) {
        valor_total = txtToValor($("#" + form + "_valor").val());
        var pc_cr_valor_novo = (valor_total * ctr_plc_prct) / 100;
        pc_cr_valor_novo = number_format(pc_cr_valor_novo, 2, ',', '.');
        $('#' + form + '_ct_resp_valor').val(pc_cr_valor_novo);
    }

    //atualiza valores incluídos na aba pc x cr
    var lancamentos = $("#" + form + "_ctr_plc_lnct").val();

    if (lancamentos != "") {
        //alert(lancamentos);
        lancamentos = JSON.parse(lancamentos);
        valor_total = txtToValor($("#" + form + "_valor").val());
        var qtd_lancamentos = lancamentos.length;
        var valor_novo = 0, porcentagem = 0, conta = 0, plano_contas_id = 0, centro = 0, centro_resp_id = 0;
        $('.ct_resp_lancamento').remove();
        for (var i = 0; i < qtd_lancamentos; i++) {
            if (lancamentos[i].operacao != 3) {

                //porcentagem = txtToValor(lancamentos[i].porcentagem);
                porcentagem = lancamentos[i].porcentagem;

                valor_novo = (valor_total * porcentagem) / 100;
                valor_novo = number_format(valor_novo, 2, ',', '.');
                //porcentagem = number_format(porcentagem, 2, ',', '');
                lancamentos[i].valor = valor_novo;
                conta = lancamentos[i].conta;
                plano_contas_id = lancamentos[i].plano_contas_id;
                centro = lancamentos[i].centro;
                centro_resp_id = lancamentos[i].centro_resp_id;
                lancamentos[i] = { 'ctr_plc_lancamento_id': lancamentos[i].ctr_plc_lancamento_id, 'plano_contas_id': plano_contas_id, 'conta': conta, 'centro_resp_id': centro_resp_id, 'centro': centro, 'valor': valor_novo, 'porcentagem': porcentagem, 'operacao': lancamentos[i].operacao };
                $("#" + form + "_ct_resp_exibir").append('<li class="ct_resp_lancamento" id="ct_resp_lancamento' + i + '"><div class="floatL"><img src="images/icons/updateDone.png" alt="" style="padding-top: 2px; padding-right: -3px;"> <br /><img src="images/icons/updateDone.png" alt="" style="padding-top: 4px; padding-right: -3px;"></div><div class="pInfo" align="left"><strong>CT: </strong>' + conta + '<br /><strong>CC: </strong>' + centro + '</div><div class="floatR"><span style="display: inline-table; vertical-align: top;">R$ ' + valor_novo + ' <br />' + porcentagem + '%</span><span style="display: inline-table;"><a href="javascript://void(0);" title="Excluír" class="smallButton" style="margin-top: 5px; margin-left: 5px; width: 14px;" onClick="centroRespLnctExcluir(\'' + form + '\',\'' + i + '\')"><img src="images/icons/dark/close.png" alt=""></a></span></div></li>');
            }
        }
        lancamentos = JSON.stringify(lancamentos);
        $("#" + form + "_ctr_plc_lnct").val(lancamentos);
    }
}

/*
===========================================================================================
VALIDAR INCLUSÃO NO CENTRO DE RESPONSABILIDADE
===========================================================================================
*/

function ctRespValidar(form) {

    //valida conta e centro
    var centro = $("#" + form + "_ct_resp_id").val();
    var conta = $("#" + form + "_pl_conta_id").val();

    //valida valor
    var lancamentos = $("#" + form + "_ctr_plc_lnct").val();
    if (lancamentos != "") {
        lancamentos = JSON.parse(lancamentos);
        var vl_total = txtToValor($('#' + form + "_valor").val());
        var vl_acumulado = 0;
        var vl_lancamento = 0;
        var prct_acumulada = 0;
        var porcentagem = 0;
        var qtd_lancamentos = lancamentos.length;
        for (var i = 0; i < qtd_lancamentos; i++) {
            if (lancamentos[i].operacao != 3) {
                vl_lancamento = txtToValor(lancamentos[i].valor);
                vl_acumulado += vl_lancamento;
                porcentagem = txtToValor(lancamentos[i].porcentagem);
                prct_acumulada += porcentagem;
            }
        }
        var ct_resp_valor = txtToValor($("#" + form + "_ct_resp_valor").val());
        var vl_maximo = vl_total - vl_acumulado;
        //alert(valor_acumulado);
    }

    if (centro == "0" && conta == "0") {
        $("#dialog-alerta").dialog("option", "buttons", [
			{
			    text: "OK",
			    click: function () { $("#dialog-alerta").dialog("close"); }
			}
        ]);

        $('#dialog-alerta').html("<br/> Selecione uma categoria ou centro de custo para inserir o valor.");

        $('#dialog-alerta').dialog('open');
        return false;

    } else if (ct_resp_valor > vl_maximo) {
        $("#dialog-alerta").dialog("option", "buttons", [
			{
			    text: "OK",
			    click: function () { $("#dialog-alerta").dialog("close"); }
			}
        ]);

        $('#dialog-alerta').html("<br/> A soma dos valores nos centros de custo e categorias excede o valor total do lançamento.");

        $('#dialog-alerta').dialog('open');

        return false;

    } else if ($('#' + form + '_ct_resp_valor').val() == '0,00') {

        $("#dialog-alerta").dialog("option", "buttons", [
           {
               text: "OK",
               click: function () { $("#dialog-alerta").dialog("close"); }
           }
        ]);

        $('#dialog-alerta').html("<br/> Preencha um valor maior do que R$ 0,00 para a Categoria/Centro de Custo.");

        $('#dialog-alerta').dialog('open');

        return false;

    } else {

        return true;
    }
}

/*
===========================================================================================
CALCULAR VALOR E PORCENTAGEM NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

//formata o valor
function moeda(id) {

    $("#" + id).priceFormat({
        prefix: "",
        centsSeparator: ",",
        thousandsSeparator: "."
    });
}

//formata a porcentagem
$(document).ready(function () {

    $(".porcentagem").priceFormat({
        prefix: "",
        centsSeparator: ".",
        thousandsSeparator: ""
    });

});

function FormatarPorcentagem(form) {

    setTimeout(function () {

        var num, centena;

        num = $('#' + form + '_ctr_plc_prct').val();

        num = num.replace(/\./g, '');

        centena = num.substr(0, 4);

        if (parseInt(centena) == 1000) {

            num = 100;

        } else {
            
            num = num.substr(0, 2) + '.' + num.substr(2); 

        }

        $('#' + form + '_ctr_plc_prct').val(num);

    }, 50);

}

//converte valor para porcentagem
function valorPorcentagem(form) {

    var total = txtToValor($('#' + form + '_valor').val() == '' ? '0,00' : $('#' + form + '_valor').val());

    var valor = txtToValor($('#' + form + '_ct_resp_valor').val());

    if (valor > total) {

        $('#' + form + '_ct_resp_valor').val(number_format(total, 2, ',', '.'));

        $('#' + form + '_ctr_plc_prct').val('100');

    } else {

        var porcentagem = valor * 100 / total;

        if (isNaN(porcentagem) == true || porcentagem == 0) {
            $('#' + form + '_ctr_plc_prct').val('00.0');
        } else {
            //Não pode colocar toFixed(2) na porcentagem porque perde a precisão
            $('#' + form + '_ctr_plc_prct').val(porcentagem);
        }


    }

    /*
    var valor_total = $('#' + form + '_valor').val();
    var valor_PC_CR = $('#' + form + "_ct_resp_valor").val();
    var porcentagem = 0;

    valor_total = txtToValor(valor_total);
    valor_PC_CR = txtToValor(valor_PC_CR);

    if (valor_PC_CR > valor_total) {

        $('#' + form + '_ct_resp_valor').val($('#' + form + '_valor').val());

        $('#' + form + '_ctr_plc_prct').val('100,00');

    } else {

        porcentagem = (valor_PC_CR * 100) / valor_total;

        $('#' + form + '_ctr_plc_prct').val(porcentagem.toFixed(2));

        porcentagemFormat(form + '_ctr_plc_prct');

    }
    */
}

//converte porcentagem para valor
function porcentagemValor(form) {

    var valor = $('#' + form + '_valor').val();
    valor = txtToValor(valor);

    var porcentagem = $('#' + form + '_ctr_plc_prct').val();
    porcentagem = parseFloat(porcentagem);

    if (porcentagem > 100) {
        porcentagem = 100;
        $('#' + form + '_ctr_plc_prct').val('100');
    }

    var valorFinal = (porcentagem / 100) * valor;

    $('#' + form + '_ct_resp_valor').val(valorFinal.toFixed(2));

    moeda(form + '_ct_resp_valor');

    /*
    var valor = $('#' + form + '_valor').val();
    var valor = txtToValor(valor);

    var valor_PC_CR = $('#' + form + '_ctr_plc_prct').val();
    valor_PC_CR = txtToValor(valor_PC_CR);

    if (valor_PC_CR > 100) { 
        
        valor_PC_CR = 100; 
        
        $('#' + form + '_ctr_plc_prct').val('100');
        
    } //validação

    var calculo = parseFloat(valor_PC_CR) / 100;
    var calculoFinal = parseFloat(valor) * calculo;

    $('#' + form + '_ct_resp_valor').val(calculoFinal.toFixed(2));
    moeda(form + '_ct_resp_valor');
    */
}

/*
===========================================================================================
INCLUÍR LANÇAMENTO NO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

/*
function centroRespLnctIncluir(form) {
	var lancamentos = [];

	var plano_contas_id = $('#'+form+'_pl_conta_id').val();
	var centro_resp_id = $('#'+form+'_ct_resp_id').val();
	var valor = $('#'+form+'_valor').val();
	var porcentagem = 100;

	//if(plano_contas_id!=0 || centro_resp_id!=0){
		lancamentos.push({'ctr_plc_lancamento_id':0,'plano_contas_id':plano_contas_id,'centro_resp_id':centro_resp_id,'valor':valor,'porcentagem':porcentagem,'operacao':1});
		lancamentos = JSON.stringify(lancamentos);
		$('#'+form+'_ctr_plc_lnct').val(lancamentos);
	//}
}
*/

function centroRespLnctIncluir(form) {
    var validacao = ctRespValidar(form);
    if (validacao) {

        var lancamentos = $("#" + form + "_ctr_plc_lnct").val();

        if (lancamentos == "") {
            lancamentos = [];
        } else {
            lancamentos = JSON.parse(lancamentos);
        }

        var qtd_lancamentos = lancamentos.length;
        var conta = $("#" + form + "_pl_conta_buscar").val();
        var plano_contas_id = $("#" + form + "_pl_conta_id").val();
        var centro = $("#" + form + "_ct_resp_buscar").val();
        var centro_resp_id = $("#" + form + "_ct_resp_id").val();
        var valor = $("#" + form + "_ct_resp_valor").val();
        var porcentagem = $("#" + form + "_ctr_plc_prct").val();
        lancamentos.push({ 'ctr_plc_lancamento_id': 0, 'plano_contas_id': plano_contas_id, 'conta': conta, 'centro_resp_id': centro_resp_id, 'centro': centro, 'valor': valor, 'porcentagem': porcentagem, 'operacao': 1 });
        lancamentos = JSON.stringify(lancamentos);
        $("#" + form + "_ctr_plc_lnct").val(lancamentos);
        var qtd_registros = $("#" + form + "_ct_resp_exibir li").length;
        if (qtd_registros == 0) {
            $("#" + form + " div.ctr_plc_container").append('<div class="boxScroll"><ul class="partners" id="' + form + '_ct_resp_exibir"></ul></div>');
        }
        $("#" + form + "_ct_resp_exibir").append('<li class="ct_resp_lancamento" id="ct_resp_lancamento' + qtd_lancamentos + '"><div class="floatL"><img src="images/icons/updateDone.png" alt="" style="padding-top: 2px; padding-right: -3px;"> <br /><img src="images/icons/updateDone.png" alt="" style="padding-top: 4px; padding-right: -3px;"></div><div class="pInfo" align="left"><strong>CT: </strong>' + conta + '<br /><strong>CC: </strong>' + centro + '</div><div class="floatR"><span style="display: inline-table; vertical-align: top;">R$ ' + valor + ' <br />' + porcentagem + '%</span><span style="display: inline-table;"><a href="javascript://void(0);" title="Excluír" class="smallButton" style="margin-top: 5px; margin-left: 5px; width: 14px;" onClick="centroRespLnctExcluir(\'' + form + '\',\'' + qtd_lancamentos + '\')"><img src="images/icons/dark/close.png" alt=""></a></span></div></li>');
        $('#' + form + '_pl_conta_id_cr').trigger('click');
        $('#' + form + '_ct_resp_id_cr').trigger('click');
        //limpa os campos do centro de responsabilidade e plano de contas após incluir um valor
        $("#" + form + "_pl_conta_buscar").val("");
        $("#" + form + "_pl_conta_id").val("0");
        $("#" + form + "_ct_resp_buscar").val("");
        $("#" + form + "_ct_resp_id").val("0");
        $("#" + form + "_ct_resp_valor").val("0,00");
        $("#" + form + "_ctr_plc_prct").val("0,00");
    }

    return validacao;
}

/*
===========================================================================================
EXCLUIR LANÇAMENTO DO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function centroRespLnctExcluir(form, lancamento_id) {
    var lancamentos = $("#" + form + "_ctr_plc_lnct").val();
    lancamentos = JSON.parse(lancamentos);
    lancamentos[lancamento_id].operacao = 3;
    lancamentos = JSON.stringify(lancamentos);
    $("#" + form + "_ctr_plc_lnct").val(lancamentos);
    $("#" + form + " li#ct_resp_lancamento" + lancamento_id).remove();
    var qtd_registros = $("#" + form + "_ct_resp_exibir li").length;
    if (qtd_registros == 0) {
        $("#" + form + " div.boxScroll").remove();
    }
    //alert(lancamentos);
}

/*
===========================================================================================
EXIBIR LANÇAMENTOS DO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/
/*
function ctrPlcLancamentosExibir(form,ctr_plc_lancamentos){
	var lancamentos = JSON.parse(ctr_plc_lancamentos);
	if(lancamentos.length>0){
		if(lancamentos[0].plano_contas_id>0){
		    $('#' + form + '_pl_conta_buscar').val(lancamentos[0].conta);
		    $('#' + form + '_pl_conta_id').val(lancamentos[0].plano_contas_id);
		    $("#" + form + ' span.check-green').eq(2).css('display', 'block');
			$("#"+form+' .plano_contas_buscar').attr('disabled',true);
		}
		if(lancamentos[0].centro_resp_id>0){
		    $('#' + form + '_ct_resp_buscar').val(lancamentos[0].centro);
		    $('#' + form + '_ct_resp_id').val(lancamentos[0].centro_resp_id);
		    $("#" + form + ' span.check-green').eq(3).css('display', 'block');
			$("#"+form+' .centro_resp_buscar').attr('disabled',true);
		}
	}
}
*/
/*
===========================================================================================
EXIBIR LANÇAMENTOS DO CENTRO DE RESPONSABILIDADE E PLANO DE CONTAS
===========================================================================================
*/

function ctrPlcLancamentosExibir(form, ctr_plc_lancamentos) {
    //alert(ctr_plc_lancamentos);
    var lancamentos = JSON.parse(ctr_plc_lancamentos);
    var jsonObj = [];
    var total_lancamentos = lancamentos.length;
    if (total_lancamentos > 0) {
        $("#" + form + " div.ctr_plc_container").append('<div class="boxScroll"><ul class="partners" id="' + form + '_ct_resp_exibir"></ul></div>');
    }
    for (var i = 0; i < total_lancamentos; i++) {
        var ctr_plc_lancamento_id = lancamentos[i].ctr_plc_lancamento_id;
        var conta = lancamentos[i].conta;
        var plano_contas_id = lancamentos[i].plano_contas_id;
        var centro = lancamentos[i].centro;
        var centro_resp_id = lancamentos[i].centro_resp_id;
        var valor = lancamentos[i].valor;
        var porcentagem = lancamentos[i].porcentagem;
        $("#" + form + "_ct_resp_exibir").append('<li class="ct_resp_lancamento" id="ct_resp_lancamento' + i + '"><div class="floatL"><img src="images/icons/updateDone.png" alt="" style="padding-top: 2px; padding-right: -3px;"> <br /><img src="images/icons/updateDone.png" alt="" style="padding-top: 4px; padding-right: -3px;"></div><div class="pInfo" align="left"><strong>CT: </strong>' + conta + '<br /><strong>CC: </strong>' + centro + '</div><div class="floatR"><span style="display: inline-table; vertical-align: top;">R$ ' + valor + ' <br />' + porcentagem + '%</span><span style="display: inline-table;"><a href="javascript://void(0);" title="Excluír" class="smallButton" style="margin-top: 5px; margin-left: 5px; width: 14px;" onClick="centroRespLnctExcluir(\'' + form + '\',\'' + i + '\')"><img src="images/icons/dark/close.png" alt=""></a></span></div></li>');
        jsonObj.push({ 'ctr_plc_lancamento_id': ctr_plc_lancamento_id, 'plano_contas_id': plano_contas_id, 'conta': conta, 'centro_resp_id': centro_resp_id, 'centro': centro, 'valor': valor, 'porcentagem': porcentagem, 'operacao': 2 });
    }
    jsonTxt = JSON.stringify(jsonObj);
    $("#" + form + "_ctr_plc_lnct").val(jsonTxt);
}



/* ====== Dialog posição automática ======= */
$(window).resize(function () {
    $(".ui-dialog-content").dialog("option", "position", { my: "top", at: "top+5%", of: window } ); // { my: "top", at: "top+5%", of: window } https://api.jqueryui.com/dialog/#option-position e https://api.jqueryui.com/position/
});


/*
===========================================================================================
ALTERNAR ENTRE MASCARA CPF E CNPJ
===========================================================================================
*/

function cpfCnpjChangeMask(tipo) {

    var id = $("#" + tipo).val();

    if (id == "cnpj") {
        var add_cpf_cnpj = "maskCnpj";
        var remove_cpf_cnpj = "maskCpf";
    } else {
        var add_cpf_cnpj = "maskCpf";
        var remove_cpf_cnpj = "maskCnpj";
    }

    $('.cpf_cnpj').addClass(add_cpf_cnpj);
    $('.cpf_cnpj').removeClass(remove_cpf_cnpj);

    //Ativa as mascaras abaixo		
    $(".maskCpf").mask("999.999.999-99");
    $(".maskCnpj").mask("99.999.999/9999-99");

}

function checkCpfCnpj() {

    var verif = $("#cpf_cnpj").val();

    if (verif == "") {

        var id = $("#inscEditar").val();

        if (id == "cnpj") {

            var add_cpf_cnpj = "maskCnpj";
            var remove_cpf_cnpj = "maskCpf";
        } else {
            var add_cpf_cnpj = "maskCpf";
            var remove_cpf_cnpj = "maskCnpj";
        }

        $('.cpf_cnpj').addClass(add_cpf_cnpj);
        $('.cpf_cnpj').removeClass(remove_cpf_cnpj);

        //Ativa as mascaras abaixo		
        $(".maskCpf").mask("999.999.999-99");
        $(".maskCnpj").mask("99.999.999/9999-99");
    }
}


/*
========================================================================================================================
JQUERY VALIDATE CPF/CNPJ (VERIFICAÇÃO)
========================================================================================================================
*/
//Aplica o Plugin de verificação do CPF / CNPJ

$(function () {
    var $cnpj = $(".cpf_cnpj").attr("name");
    var $params = { debug: false, rules: {}, messages: {} };
    $params['rules'][$cnpj] = "cpf_cnpj";
    $params['messages'][$cnpj] = "*Número inválido.";

    /* ===== FORMS =====*/
    $("#formfavAC").validate($params);
});

/*
===========================================================================================
VERIFICAR SE É VERDADEIRO CPF / CNPJ
===========================================================================================
*/
/*
//Verifica se CPF é válido
function validarCPF(cpf) {

    // Elimina CNPJs invalidos conhecidos
    // if (cpf.length != 14)
    //     return false;

    cpf = cpf.replace(/\./g, '');
    cpf = cpf.replace(/\-/g, '');
    var Soma;
    var Resto;
    Soma = 0;
    //cpf  = RetiraCaracteresInvalidos(cpf,11);
    if (cpf == "" || cpf == "00000000000" || cpf == "11111111111" || cpf == "22222222222" || cpf == "33333333333" || cpf == "44444444444" || cpf == "55555555555" || cpf == "66666666666" || cpf == "77777777777" || cpf == "88888888888" || cpf == "99999999999")
        return false;
    for (i = 1; i <= 9; i++)
        Soma = Soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11))
        Resto = 0;
    if (Resto != parseInt(cpf.substring(9, 10)))
        return false;
    Soma = 0;
    for (i = 1; i <= 10; i++)
        Soma = Soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;
    if ((Resto == 10) || (Resto == 11))
        Resto = 0;
    if (Resto != parseInt(cpf.substring(10, 11)))
        return false;
    return true;
}

function validarCNPJ(cnpj) {

    cnpj = cnpj.replace(/[^\d]+/g, '');
    //alert(cnpj);
    if (cnpj == '' || cnpj == '00000000000000' || cnpj == '11111111111111' || cnpj == '22222222222222' || cnpj == '33333333333333' || cnpj == '44444444444444' || cnpj == '55555555555555' || cnpj == '66666666666666' || cnpj == '77777777777777' || cnpj == '88888888888888' || cnpj == '99999999999999') return false;

    // Elimina CNPJs invalidos conhecidos
    //if (cnpj.length != 18)
    //    return false;

    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
        return false;

    return true;

}

//Seleciona e aciona as funções
jQuery(function ($) {
    $('.cpfCnpjValid').on('keyup', function () {

        var cpfCnpj1 = $('.cpfCnpjValid').val();
        var cpfCnpj = cpfCnpj1.replace(/[^\d]+/g, '');
        //alert(cpfCnpj1.charAt(10));
        if (cpfCnpj.length === 11 && cpfCnpj1.charAt(3) === '.') {
            var cpf = validarCPF(cpfCnpj);
            if (cpf == false) { alert('CPF Inválido.'); }
        } else if (cpfCnpj.length === 14 && cpfCnpj1.charAt(10) === '/') {
            var cnpj = validarCNPJ(cpfCnpj);
            if (cnpj == false) { alert('CNPJ Inválido.'); }
        }
    });
});
*/

//INCLUI CATEGORIA E CENTRO DE CUSTO AUTOMATICAMENTE AO SALVAR LANÇAMENTO
//===========================================================================================

function AutoIncluirCategoriaECentroCusto(form_id) {

    if (($('#' + form_id + '_pl_conta_id').val() != 0 || $('#' + form_id + '_ct_resp_id').val() != 0) && form_id != 'form_trsf')
        return centroRespLnctIncluir(form_id);
    else
        return true;
}
