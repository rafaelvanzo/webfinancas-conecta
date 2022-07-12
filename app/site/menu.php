			<header>
				<div class="container">
					<h1 class="logo">
						<a href="<?php echo $httpProtocol.$baseUrl;?>">
							<img alt="Web Finanças" src="site/img/logo_webfinancas_fundo_branco.png">
						</a>
					</h1>
          
					<nav>
						<ul class="nav nav-pills nav-top">
             
             <!-- <li class="phone"> <input type="button" class="btn btn-primary" value="Entrar"/> &nbsp;&nbsp; </li> -->
             
         <!--    <div class="social-icons">
							 <ul class="social-icons">              
               
								<li class="facebook"><a href="http://www.facebook.com/" target="_blank" title="Facebook">Facebook</a></li>
								<li class="googleplus"><a href="http://www.googleplus.com/" target="_blank" title="Google+">Google+</a></li>
								<li class="linkedin"><a href="http://www.linkedin.com/" target="_blank" title="Linkedin">Linkedin</a></li>
            
								</ul> 
							</div> -->

				<!--	<li class="phone">
								<span><i class="icon icon-phone"></i>(27) 9 9907 7885 &nbsp;&nbsp; <i class="icon icon-phone"></i>(27) 9 8811 7561</span>
							</li>   -->
                    
						</ul>
            
          <button class="btn btn-responsive-nav btn-inverse" data-toggle="collapse" data-target=".nav-main-collapse">
						<i class="icon icon-bars"></i>
					</button>
					</nav> 
          
				</div>
				<div class="navbar-collapse nav-main-collapse collapse">
					<div class="container">

						<nav class="nav-main" >
							<ul class="nav nav-pills nav-main" id="mainMenu">
								
                                <?php
                                if($page == 'selecionarSistema'){

                                    echo '
                                        <li>
                                            <a class="menu-toggle" href="javascript:Logoff();">
                                                SAIR
									        </a>
                                        </li>
                                    ';

                                }else{
                                ?>
                                
                                    <li <?php if($page == 'paginaInicial'){ echo 'class="active"'; }elseif(empty($page)){ echo 'class="active"'; } ?> >
									    <a class="menu-toggle" href="paginaInicial">
										    PÁGINA INICIAL
										    <!-- <i class="icon icon-angle-down"></i> -->
									    </a>
								    </li>
                                
                                    <li <?php if($page == 'quemSomos'){ echo 'class="active"'; } ?>>
									    <a class="menu-toggle" href="quemSomos">
										    QUEM SOMOS
									    </a>
                                    </li>
                
								    <li <?php if($page == 'planosPrecos'){ echo 'class="active"'; } ?>>
									    <a class="menu-toggle" href="planosPrecos">
										    PLANOS E PREÇOS
									    </a>
								    </li>
                
                                    <li <?php if($page == 'comoFunciona'){ echo 'class="active"'; } ?>>
									    <a class="menu-toggle" href="comoFunciona">
										    COMO FUNCIONA
									    </a>
								    </li>
                
								    <li <?php if($page == 'contato'){ echo 'class="active"'; } ?>>
									    <a class="menu-toggle" href="contato">
										    CONTATO
									    </a>
								    </li>
                
                                <?php
                                }
                                ?>

							</ul>
						</nav>
					</div>
				</div>
			</header>