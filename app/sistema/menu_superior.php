<script> function janelaAtendimento(){ document.getElementById("atendimento").querySelector("a").click(); } </script>

<!-- Right side -->
<div id="rightSide">

    <!-- === Top fixed navigation === -->
    <div class="topNav">
        <div class="wrapper">
            <div class="welcome"><a href="javascript://void(0);" title="" style="cursor: default;"><img src="images/userPic.png" alt="" /></a><span><?php echo $_SESSION['email'];?></span></div>
             <div class="userNav">
                <ul>
              <!--  <li><a title=""><img src="images/icons/topnav/messages.png" alt="" /><span>Contratar</span><span class="numberTop">15</span></a></li>     --> 
                   <li><a href="javascript://void(0);" class="tipN" original-title="Atendimento Online" onClick="javascript: janelaAtendimento();"><img src="images/icons/topnav/messages.png" alt=""/><span>Atendimento</span> <!-- <span class="numberTop">1</span> --></a>
                   <div id="atendimento"style="display:none;"><script language="JavaScript" src="https://web417.uni5.net/~web2business/atendimento/js/status_image.php?base_url=https://web417.uni5.net/~web2business/atendimento&l=w2b&x=1&deptid=0&text=."></script> </div>

                   </li> 
                  
                   
                    <li class="dd"><a href="javascript://void(0);" title=""><img src="images/icons/topnav/profile.png" alt="" /><span>Minha Conta</span> <!-- <span class="numberTop">1</span> --> </a>
                        <ul class="userDropdown">
                          	<li><a href="perfilUsuario" class="sUser">Perfil do usuário</a></li> 
                            <!-- <li><a href="faturas" class="sFaturas">Faturas <span class="numberTop" style="float: right; margin-top: 2px;">1</span> </a></li> -->
                            <li><a href="javascript://void(0);" class="sLocked" id="opener-alterar-senha">Alterar a senha</a></li>
                            <!--<li><a href="javascript://void(0);" class="sCancelar" id="opener-cancelar">Cancelar conta</a></li>-->
                        </ul> 
                    </li> 
                   <!-- <li><a href="javascript://void(0);" title="" id="opener-alterar-senha"><img src="images/icons/topnav/locked2.png" alt="" /><span>Alterar a senha</span></a></li> -->
                    <li><a href="javascript://void(0);" title="" class="sair"><img src="images/icons/topnav/logout.png" alt="" /><span>Sair</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    
     <!-- Responsive header -->
    <div class="resp">
        <div class="respHead">        
            <a href="javascript://void(0);" style="cursor: default;" title="WebFinancas"><img src="images/logo_webfinancas_fundo_branco.png" alt="" /></a>
        </div>
        
        <div class="cLine"></div>
        <div class="smalldd" >   
        

          
        
        
            <span class="goTo">
						<?php
					 if ($page == 'geral'){ echo '<img src="images/icons/light/home.png" alt="" />Geral'; }
									else if( $page == 'lancamentos' ){ echo '<img src="images/icons/light/money2.png" alt="" />Lançamentos'; }
								//else if( $page == 'programacao' ){ echo '<img src="images/icons/light/clock.png" alt="" />Programação'; }
										else if( $page == 'recebimentosProg' ){ echo '<img src="images/icons/light/clock.png" alt="" />Programação > Recebimentos'; }
										else if( $page == 'pagamentosProg' ){ echo '<img src="images/icons/light/clock.png" alt="" />Programação > Pagamentos'; }
										else if( $page == 'transferenciasProg' ){ echo '<img src="images/icons/light/clock.png" alt="" />Programação > Transferências'; }
										else if( $page == 'recebimentosRcr' ){ echo '<img src="images/icons/light/clock.png" alt="" />Programação > Recebimentos Recorrentes'; }
										else if( $page == 'pagamentosRcr' ){ echo '<img src="images/icons/light/clock.png" alt="" />Programação > Pagamentos Recorrentes'; }
									else if( $page == 'favorecidos' ){ echo '<img src="images/icons/light/user.png" alt="" />Favorecidos'; }
									else if( $page == 'centroResponsabilidade' ){ echo '<img src="images/icons/light/files.png" alt="" />Centro de Responsabilidade'; }	
									else if( $page == 'planoContas' ){ echo '<img src="images/icons/light/frames.png" alt="" />Categorias'; }
									else if( $page == 'contas' ){ echo '<img src="images/icons/light/money.png" alt="" />Contas Financeiras'; }
									else if( $page == 'relatorios'){ echo '<img src="images/icons/light/stats.png" alt="" />Relatórios'; }
								//else if( $page == 'filial' ){ echo '<img src="images/icons/light/building.png" alt="" />Filial'; }
								  else{ echo '<img src="images/icons/light/signPost.png" alt="" />Menu'; }
						?></span>
            
            <ul class="smallDropdown">
                <li><a href="geral" title="" ><img src="images/icons/light/home.png" alt="" />Geral</a></li>
                <li><a href="lancamentos" title=""><img src="images/icons/light/money2.png" alt="" />Lançamentos</a></li>
                <li><a href="programacao" title="" <?php if( $page == 'recebimentosProg' || $page == 'pagamentosProg' || $page == 'transferenciasProg' || $page == 'recebimentosRcr' || $page == 'pagamentosRcr'  ){ echo 'class="exp active" id="current"'; }else{ echo 'class="exp"'; }?> ><img src="images/icons/light/clock.png" alt="" />Programação <strong>5</strong></a>
                		<ul>
                        <li><a href="recebimentosProg" title="">Recebimentos</a></li>
                        <li><a href="pagamentosProg" title="">Pagamentos</a></li>
                        <li><a href="transferenciasProg" title="">Transferências</a></li>
                        <li><a href="recebimentosRcr" title="">Recebimentos Recorrentes</a></li>
                        <li class="last"><a href="pagamentosRcr" title="">Pagamentos Recorrentes</a></li>
                    </ul>
                </li>
                <li><a href="favorecidos" title=""><img src="images/icons/light/user.png" alt="" />Favorecidos</a></li>
                <li><a href="centroResponsabilidade" title=""><img src="images/icons/light/files.png" alt="" />Centro de Responsabilidade</a></li>
                <li><a href="planoContas" title="" ><img src="images/icons/light/frames.png" alt="" />Plano de Contas</a></li>
                <li><a href="contas" title=""><img src="images/icons/light/money.png" alt="" />Contas Financeiras</a></li>
                <li><a href="relatorios" title=""><img src="images/icons/light/stats.png" alt="" />Relatórios</a></li>                
<!--                <li><a href="filial" title=""><img src="images/icons/light/building.png" alt="" />Filial</a></li>  -->
            </ul>
        </div>
        <div class="cLine"></div>
    </div>    