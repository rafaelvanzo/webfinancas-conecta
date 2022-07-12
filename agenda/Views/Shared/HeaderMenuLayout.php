			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="javascript://" class="logo">
						<img src="Assets/images/<?php echo $Config["Layout"]["Base"]["logo"];?>" width="auto" height="35" alt="Logo" />
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
				</div>
			
				<!-- start: search & user box -->
				<div class="header-right">

				<span class="separator"></span>
				

				<ul class="notifications">
						<li>
							<a href="#" class="notification-icon aniversarios" title="Aniversariantes da semana">
								<i class="fa fa-bell totalAniversarios"></i> 

									<?php 
									/*
									$W = date('W') - 1;	
									$num = $this->db->fetch_assoc('SELECT count(id) as num FROM favorecidos WHERE WEEK(dtNascimento) = "'.$W.'" AND YEAR(dtNascimento) = "'.date('Y').'"');
									echo ($num['num'] > 0) ? '<span class="badge qtdConvites" > '.$num['num'].'</span>' : '';
									*/
									?>
									
								
							</a>
						</li>
					</ul>
			
			<span class="separator"></span>

			<div id="userbox" class="userbox">
						<a href="#" data-toggle="dropdown">
							<figure class="profile-picture">
								<img src="Assets/images/default-user.png" alt="Usuário" class="img-circle" data-lock-picture="Assets/images/default-user.png" />
							</figure>
							<div class="profile-info" data-lock-name="<?php //echo $_SESSION['Nome']; ?>" data-lock-email="<?php //echo $_SESSION['Nome']; ?>">
								<span class="name"><?php //echo $_SESSION['Nome']; ?></span>
								<span class="role">administrador</span>
							</div>
			
							<i class="fa custom-caret"></i>
						</a>
			
						<div class="dropdown-menu">
							<ul class="list-unstyled">
								<li class="divider"></li>
								<!--<li>
									<a role="menuitem" tabindex="-1" href="pages-user-profile.html"><i class="fa fa-user"></i> My Profile</a>
								</li>-->
                                <li>
									<a role="menuitem" tabindex="-1" href="#" onclick="modalOpen('Create', 'ModalAlterarSenha')"><i class="fa fa-lock"></i> Alterar Senha</a>
								</li>
								<!--<li>
									<a role="menuitem" tabindex="-1" href="#" data-lock-screen="true"><i class="fa fa-lock"></i> Lock Screen</a>
								</li>-->
								<li>
									<a role="menuitem" tabindex="-1" href="#" class="OpenModalLogout"><i class="fa fa-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
					
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->


            <!-- ===== Menu ===== -->
            <div class="inner-wrapper">


			<!-- start: sidebar -->
				<aside id="sidebar-left" class="sidebar-left">
				
				    <div class="sidebar-header">
				        <div class="sidebar-title">
				            Menu
				        </div>
				        <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
				            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
				        </div>
				    </div>
				
				    <div class="nano">
				        <div class="nano-content">
				            <nav id="menu" class="nav-main" role="navigation">
				            
				                <ul class="nav nav-main">                 
								  
								  

								<?php 

								if($_SESSION['Tipo'] == 2){

									echo '<li class="nav-active" >
												<a href="Calendario">
													<i class="fa fa-calendar-o" aria-hidden="true"></i>
													<span>Agenda</span>
											</a>
										</li>';
								}else{


								$MenuView = '';
								$Total = count($Config["Menu"]);
								$i = 1;

									while($i <= $Total)
									{				
											$nome = $Config["Menu"]["Item".$i]["nome"];
											
											$icone = $Config["Menu"]["Item".$i]["icone"];											

												if(isset($Config["Menu"]["Item".$i]["subMenu"]))
												{
													$subMenu = subMenu($Config["Menu"]["Item".$i]["subMenu"]);
																										
													echo '<li class="nav-parent '.$subMenu['activeParent'].'" >
															<a href="javascript://">
																<i class="'.$icone.'" aria-hidden="true"></i>
																<span>'.$nome.'</span>
															</a>      
															'.$subMenu['sub'].'
														</li>';

														if(!empty($subMenu['MenuView']))
														{															
															$MenuGrupoView = $nome;
															$MenuView = $subMenu['MenuView'];
														}

												}else{
													
													$link = $Config["Menu"]["Item".$i]["link"];

													if($_GET["Controller"] == $Config["Menu"]["Item".$i]["link"])
													{ 
														$active =  'nav-active';
														$MenuView = $nome;
													}else{
														$active =  '';
													}
													
													echo '<li class="'.$active.'" >
															<a href="'.$link.'">
																<i class="'.$icone.'" aria-hidden="true"></i>
																<span>'.$nome.'</span>
															</a>      
														</li>';
												}
											$i += 1;
									}

		
									function subMenu($subMenu)
									{										
										
										$subTotal = count($subMenu);
										$c = 1;
										
										$retorno = ' <ul class="nav nav-children">';
										
											while($c <= $subTotal)
											{
												if($_GET["Controller"] == $subMenu["subItem".$c]["link"])
												{ 
													$activeSub = 'nav-active';
													$MenuView = $subMenu["subItem".$c]["nome"];
													$activeParent = 'nav-active';

												}else{
													$activeSub = '';
												}

												$retorno .= '<li class="'.$activeSub.'" ><a href="'.$subMenu["subItem".$c]["link"].'">'.$subMenu["subItem".$c]["nome"].'</a></li>';

												$c += 1;
											}							
											
										$retorno .= ' </ul>';

										return array('sub' => $retorno, 'activeParent' => $activeParent, 'MenuView' => $MenuView);
									}




								} //Fim else

								?>

				                </ul>
				            </nav>
				        </div>
				
				    </div>
				
				</aside>
				<!-- end: sidebar -->


				<title><?php echo $Config["Layout"]["Base"]["title"];?></title>


				<header class="page-header">
					
					<h2>
						<div class="left-wrapper text-left">
							<ol class="breadcrumbs">
								<li>
									<a href="Calendario">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<?php
								if(!empty($MenuGrupoView)){ echo "<li><span>".$MenuGrupoView."</span></li>"; }
								if(!empty($MenuView)){ echo "<li><span>".$MenuView."</span></li>"; }
								?>
							</ol>
						</div>
					</h2>

				</header>

         <!-- ===== Menu ===== -->