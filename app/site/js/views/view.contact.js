/*
Name: 			View - Contact
Written by: 	Crivos - (http://www.crivos.com)
Version: 		2.0
*/

(function() {

	"use strict";

	var Contact = {

		initialized: false,

		initialize: function() {

			if (this.initialized) return;
			this.initialized = true;

			this.build();
			this.events();

		},

		build: function() {

			this.validations();

		},

		events: function() {



		},

		validations: function() {

			$("#contactForm").validate({ 
				submitHandler: function(form) {

					// Loading State
					var submitButton = $(this.submitButton);
					submitButton.button("loading");

					// Ajax Submit
					$.ajax({
						type: "POST",
						url: "site/php/contact-form.php",
						data: {
							"name": $("#contactForm #name").val(),
							"email": $("#contactForm #email").val(),
							//"subject": $("#contactForm #subject").val(),
							"message": $("#contactForm #message").val()
						},
						dataType: "json",
						success: function (data) {
							if (data.response == "success") {
								
								$("#contactSuccess").removeClass("hidden");
								$("#contactError").addClass("hidden");
								
								//Esconder a mensagem de sucesso
								setTimeout(function(){ $('#contactSuccess').fadeOut();}, 5000); 
								
								// Reset Form
								$("#contactForm .form-control")
									.val("")
									.blur()
									.parent()
									.removeClass("has-success")
									.removeClass("has-error")
									.find("label.error")
									.remove();

								if(($("#contactSuccess").position().top - 80) < $(window).scrollTop()){
									$("html, body").animate({
										 scrollTop: $("#contactSuccess").offset().top - 80
									}, 300);
								}

							//	$("#contactSuccess").fadeOut('slow',function(){ $("#contactSuccess").addClass("hidden"); });
							
							


							} else {

								$("#contactError").removeClass("hidden");
								$("#contactSuccess").addClass("hidden");

								if(($("#contactError").position().top - 80) < $(window).scrollTop()){
									$("html, body").animate({
										 scrollTop: $("#contactError").offset().top - 80
									}, 300);
								}

							}
						},
						complete: function () {
							submitButton.button("reset");
						}
					});
				},
				rules: {
					name: {
						required: true
					},
					email: {
						required: true,
						email: true
					},
					subject: {
						required: true
					},
					message: {
						required: true
					}
				},
				highlight: function (element) {
					$(element)
						.parent()
						.removeClass("has-success")
						.addClass("has-error");
				},
				success: function (element) {
					$(element)
						.parent()
						.removeClass("has-error")
						.addClass("has-success")
						.find("label.error")
						.remove();
				}
			});

		}

	};

	Contact.initialize();

})();