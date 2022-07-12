<!-- 
=================================================================================== 
MENU LATERAL ESQUERDO
=================================================================================== 
-->

<!-- === Left side content === -->
<div id="leftSide" class="noPrint"> 
    <div class="logo"><a href="javascript://" style="cursor: default;" title="WebFinancas">
        <?php  if($_SESSION['logo_parceiro']==1){ $logo = $raiz.$_SESSION['logo_imagem']; }else{ $logo = $raiz."images/logo_webfinancas_fundo_pb.png"; } ?>
        <img src="<?php echo $logo; ?>" alt="" />

                      </a></div>
    


    <!-- Left navigation -->
    <div class="tour-block last" id="AjudaInicial4">
    <ul id="menu" class="nav">
        <li class="msgBalao"><a href="contadorMensagens" title="" <?php if($page == "contadorMensagens" || $page ==""){ echo 'class="active"'; } ?> ><span>Solicitações</span></a></li>  
        
        <?php 
        if($_SESSION['parceiro'] > 1){ ?>
          <li class="users"><a href="clientes" title="" <?php if($page == "clientes" ){ echo 'class="active"'; } ?> ><span>Clientes</span></a></li>
        <?php }else{ ?>
          <li class="mail"><a href="convites" title="" <?php if($page == "convites"){ echo 'class="active"'; } ?> ><span>Convites</span></a></li>  
        <?php } ?>
        
        <li class="files"><a href="arquivoContabil" title="" <?php if($page == "clientesDetalhes" || $page == "clientesDetalhesConfig" || $page == "arquivoContabil"){ echo 'class="active"'; } ?> ><span>Arquivo Contábil</span></a></li>
        <li class="docs"><a href="documentos" title="" <?php if($page == "documentos"){ echo 'class="active"'; } ?> ><span>Documentos</span></a></li>
        
		
		<li class="cloudDonwload"><a href="carneLeao" title="" <?php if($page == "carneLeao"){ echo 'class="active"'; } ?> ><span>Exportar carne leão</span></a></li>
        
		
		<li class="megaphone"><a href="informativo" title="" <?php if($page == "informativo"){ echo 'class="active"'; } ?> ><span>Informativo</span></a></li>
      
        <!--<li class="config"><a href="config" title="" <?php //if($page == "config"){ echo 'class="active"'; } ?> ><span>Configurações</span></a></li>  -->
    </ul>
    </div>
</div>

<!-- 
=================================================================================== 
MENU SUPERIOR DO PALCO
=================================================================================== 
-->
   <div class="alerta">

     <!-- Notifications -->
        <div class="nNote nWarning hideit" style="display:none; margin-top:40px; width:300px; margin-left:auto; margin-right:auto;">
            <p align="center"></p>
        </div>
        <div class="nNote nInformation hideit" style="display:none; margin-top:40px; width:300px; margin-left:auto; margin-right:auto;">
            <p align="center"></p>
        </div>   
        <div class="nNote nSuccess hideit" style="display:none; margin-top:40px; width:300px; margin-left:auto; margin-right:auto;">
          <p align="center"></p>
        </div>  
        <div class="nNote nFailure hideit" style="display:none; margin-top:40px; width:300px; margin-left:auto; margin-right:auto;">
            <p align="center"></p>
        </div>

		</div>
    
<!-- Right side -->
<div id="rightSide">

    <!-- === Top fixed navigation === -->
    <div class="topNav noPrint">
        <div class="wrapper">
            <div class="welcome"><a href="javascript://void(0);" title="" style="cursor: default;"><img src="<?php echo $raiz;?>images/userPic.png" alt="" /></a><span><?php echo $_SESSION['email'];?></span></div>
             <div class="userNav">
                <ul>
          			<?php if($_SESSION['financeiro_acesso']==1){ ?> <li><a href="<?php echo $httpProtocol.$baseUrl; ?>/sistema" title=""><img src="<?php echo $raiz;?>images/icons/light/refresh2.png" alt=""><span>Empresa</span></a></li> <?php } ?>
                    <li class="hp tour-block" id="AjudaInicial2"><a href="../centralAjuda" target="_blank"><img src="<?php echo $raiz;?>images/icons/topnav/help2.png" alt="" /><span>Ajuda</span><!--<span class="numberTop">8</span>--></a></li>
                    <!--<li><a href="javascript://void(0);" title=""><img src="<?php echo $raiz;?>images/icons/topnav/messages.png" alt=""/><span>Atendimento</span><span class="numberTop">1</span></a></li>   -->
                    <li class="dd tour-block" id="AjudaInicial1"><a href="javascript://void(0);" title=""><img src="<?php echo $raiz;?>images/icons/topnav/profile.png" alt="" /><span>Minha Conta</span> <!-- <span class="numberTop">1</span> --> </a>
                        <ul class="userDropdown">
                            <li><a href="../sistema/perfilUsuario" class="sUser">Perfil do usuário</a></li>
                            <!-- <li><a href="faturas" class="sFaturas">Faturas <span class="numberTop" style="float: right; margin-top: 2px;">1</span> </a></li> -->
                            <li><a href="javascript://void(0);" class="sLocked" id="opener-alterar-senha">Alterar a senha</a></li>
                            <!-- <li><a href="javascript://void(0);" class="sSys">Configuração do sistema</a></li> -->
                        </ul> 
                    </li> 
                   <!-- <li><a href="javascript://void(0);" title="" id="opener-alterar-senha"><img src="<?php echo $raiz;?>images/icons/topnav/locked2.png" alt="" /><span>Alterar a senha</span></a></li> -->
                    <li><a href="javascript://void(0);" title="" class="sair" data-sair="<?php echo $_SESSION['sair_caminho']; ?>"><img src="<?php echo $raiz;?>images/icons/topnav/logout.png" alt="" /><span>Sair</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Botão para chamar ajuda inteligente -->
			<input type="hidden" class="bt_ajudaInteligente2" data-tour="ajuda">
    
    <!-- Responsive header -->
    <div class="resp noPrint">
        <div class="respHead">        
            <?php if($_SESSION['logo_parceiro']!=1){ $logo = $raiz."images/logo_webfinancas_fundo_branco.png"; } ?>
            <a href="javascript://" style="cursor: default;" title="WebFinancas"><img src="<?php echo $logo; ?>" alt="" /></a>
        </div>
        
        <div class="cLine"></div>
        <div class="smalldd">
 
        
            <span class="goTo">
						<?php
                        if ($page == 'contadorMensagens'|| $page ==""){ echo '<img src="'.$raiz.'images/icons/light/speech.png" alt="" />Solicitações'; }
                        else if( $page == 'clientes' ){ echo '<img src="'.$raiz.'images/icons/light/users.png" alt="" />Clientes'; }
                        else if( $page == 'arquivoContabil' || $page =="clientesDetalhes" || $page =="clientesDetalhesConfig" || $page =="clientesDetalhesDocumentos" || $page =="clientesDetalhesGerarArquivo"){ echo '<img src="'.$raiz.'images/icons/light/mail.png" alt="" />Convites'; }
                        else if( $page == 'documentos' ){ echo '<img src="'.$raiz.'images/icons/light/docs.png" alt="" />Documentos'; }
                        else if( $page == 'carneLeao'){ echo '<img src="'.$raiz.'images/icons/light/cloudDownload.png" alt="" />Exportar carne leão'; }
						else if( $page == 'informativo' ){ echo '<img src="'.$raiz.'images/icons/light/megaphone.png" alt="" />Informativo'; }
                        else if( $page == 'convites' ){ echo '<img src="'.$raiz.'images/icons/light/mail.png" alt="" />Convites'; }

						?></span>
            
            <ul class="smallDropdown">
                <li <?php if($page == "contadorMensagens" || $page ==""){ echo 'class="active"'; } ?> ><a href="contadorMensagens" title="" ><img src="<?php echo $raiz;?>images/icons/light/speech.png" alt="" />Solicitações</a></li>
               
                <?php if($_SESSION['parceiro'] > 1){ ?>
                 <li><a href="clientes" title=""><img src="<?php echo $raiz;?>images/icons/light/users.png" alt="" />Clientes</a></li>
                <?php  }else{ ?>
                 <li><a href="convites" title=""><img src="<?php echo $raiz;?>images/icons/light/mail.png" alt="" />Convites</a></li>
                <?php } ?>
                
                <li><a href="arquivoContabil" title=""><img src="<?php echo $raiz;?>images/icons/light/files.png" alt="" />Arquivo Contábil</a></li>
                <li><a href="documentos" title="" <?php if($page == "contadorMensagens" || $page ==""){ echo 'class="active"'; } ?>><img src="<?php echo $raiz;?>images/icons/light/docs.png" alt="" />Documentos</a></li>
                
				<li><a href="carneLeao" title="" <?php if($page == "carneLeao" || $page ==""){ echo 'class="active"'; } ?>><img src="<?php echo $raiz;?>images/icons/light/cloudDownload.png" alt="" />Exportar carne leão</a></li>
                
				<li><a href="informativo" title=""><img src="<?php echo $raiz;?>images/icons/light/megaphone.png" alt="" />Informativo</a></li>
                
                
            </ul>
        </div>
        <div class="cLine"></div>
    </div>