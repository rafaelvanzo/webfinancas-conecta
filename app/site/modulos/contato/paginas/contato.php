			<div role="main" class="main">

				<section class="page-top">
				<div class="container">
            <div class="row">
							<div class="col-md-12">
								<h2 style="color: #0088cc;">Contato</h2>
							</div>
						</div> 
					</div>
				</section> 

				<!-- Google Maps -->
				<div id="googlemaps" class="google-map hidden-xs"></div>
        
				<div class="container">

					<div class="row">
						<div class="col-md-6">

							<div class="alert alert-success hidden" id="contactSuccess">
								<strong>Successo!</strong> Sua mensagem foi enviada, em breve entraremos em contato.
							</div>

							<div class="alert alert-error hidden" id="contactError">
								<strong>Oops!</strong> Não foi possível enviar o seu contato, por favor tente mais tarde.
							</div>

							<!-- <h2 class="short"><strong>Fale</strong> Conosco</h2> -->
							<form action="php/contact-form.php" id="contactForm" type="post">
								<div class="row">
									<div class="form-group">
										<div class="col-md-6">
											<label>Nome *</label>
											<input type="text" value="" data-msg-required="Por favor preencha o seu nome." maxlength="100" class="form-control" name="name" id="name">
										</div>
										<div class="col-md-6">
											<label>E-mail *</label>
											<input type="email" value="" data-msg-required="Por favor preencha um e-mail para contato." data-msg-email="Porfavor preencha um e-mail válido." maxlength="100" class="form-control" name="email" id="email">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-md-12">
											<label>Messagem *</label>
											<textarea maxlength="5000" data-msg-required="Por favor digite uma mensagem." rows="10" class="form-control" name="message" id="message"></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<input type="submit" value="Enviar" class="btn btn-primary" data-loading-text="Enviando...">
									</div>
								</div>
							</form>
						</div>
						<div class="col-md-6">

							<h4>Escritório <strong>Web Finanças</strong></h4>
							<ul class="list-unstyled">
								<li><i class="icon icon-map-marker"></i> <strong>Endereço:</strong> Av. Francisco Generoso da Fonseca, 374, Jardim da Penha, Vitória / ES - Brasil</li>
							<!--	<li><i class="icon icon-phone"></i> <strong>Telefone:</strong> (27) 9 9907 7885</li>
                <li><i class="icon icon-phone"></i> <strong>Telefone:</strong> (27) 9 8811 7561</li> -->
								<li><i class="icon icon-envelope"></i> <strong>E-mail:</strong> contato@webfinancas.com</li>
							</ul>

							<hr />

							<h4>Horário de <strong>Funcionamento</strong></h4>
							<ul class="list-unstyled">
								<li><i class="icon icon-time"></i> Segunda - Sexta de 9:00 às 12:00 e de 14:00 às 18:00</li>
							</ul>

							<hr />
              
            <!--  <div class="get-started" align="center">  
              <h4> Converse com um <strong>Atendente</strong> </h4>                        
								<a href="javascript://" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Atendimento Online</a>
							</div> -->

						</div>

					</div>

				</div>
