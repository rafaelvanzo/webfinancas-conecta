<?php 
$protocolo = isset($_SERVER['HTTPS'])? "https://" : "http://";
$baseUrl = $_SERVER['HTTP_HOST'];
?>

<!-- 
MENU LATERAL ESQUERDO
=================================================================================== 
-->

<!-- === Left side content === -->
<div id="leftSide" class="noPrint">
    
    <!-- start: select de licença -->
    <?php
    if(isset($_SESSION['licencas'])){
        $licencas = $_SESSION['licencas'];
        echo '<select class="select-licenca" id="select-licenca">';
        foreach($licencas as $licensa){
                        
            if($licensa['cliente_id'] == $_SESSION['cliente_id'])
                $selected = 'selected';
            else
                $selected = '';
                        
            echo '<option value="'.$licensa['cliente_id'].'" '.$selected.'>'.$licensa['nome'].'</option>';
        }
        echo '</select>';
    }
    ?>
    <!-- end: select de licença -->

    <div class="logo">
        
        <a href="javascript://" style="cursor: default;" title="WebFinancas">
            <?php if($_SESSION['logo_parceiro']==1){ $logo = $_SESSION['logo_imagem']; }else{ $logo = "images/logo_webfinancas_fundo_pb.png"; } ?>
            <img src="<?php echo $logo; ?>" alt="" />
        </a>

    </div>

    <!-- General balance widget -->
    <?php 
    if(in_array(47,$_SESSION['permissoes'])){
    ?>
    
        <div class="genBalance tour-block" id="AjudaInicial3">
            <a  href="javascript://" style="cursor: default;width:86% " title="" class="amount">
                <span>Total Disponível:</span>
                <span class="balanceAmount saldoTotal" id="saldoTotal"><?php echo $_SESSION['total_disponivel']; //sessão atribuída na página funcoes.js.php do modulo geral ?></span>
             	
            </a>
            <!--
            <a  href="javascript://" style="cursor: default;" title="" class="amChanges">
                <strong class="sPositive">+0.6%</strong>
            </a>
            -->
        </div>

    <?php
    }
    ?>

    <!-- Left navigation -->
    <div class="tour-block last" id="AjudaInicial4">
    <ul id="menu" class="nav">
        
        <?php if(in_array(1,$_SESSION['permissoes'])){ ?>
            <li class="dash"><a href="geral" title="" <?php if($page == "geral"){ echo 'class="active"'; } ?> ><span>Geral</span></a></li>
        <?php } ?>

        <?php if(in_array(2,$_SESSION['permissoes'])){ ?>
        <li class="money2"><a href="lancamentos" title="" <?php if($page == "lancamentos"){ echo 'class="active"'; }?> ><span>Lançamentos</span></a></li>
        <?php } ?>

        <?php if(in_array(6,$_SESSION['permissoes'])){ ?>
        <li class="clock"><a href="recorrencia" title="" <?php if($page == "recorrencia"){ echo 'class="active"'; }?> ><span>Recorrência</span></a></li>
        <?php } ?>

        <?php if(in_array(10,$_SESSION['permissoes']) || in_array(14,$_SESSION['permissoes']) || in_array(18,$_SESSION['permissoes']) ){ ?>
        <li class="typo"><a href="javascript://" title="" class="exp" <?php if($page == "orcamentosPlnj" || $page == "lancamentosPlnj" || $page == "provisao"){ $menu_plnj_display = 'style="display:block"'; echo 'id="current"'; }else{$menu_plnj_display = 'style="display:none"';}?> ><span>Planejamento</span><strong>3</strong></a>
          <ul class="sub" <?php echo $menu_plnj_display;?>>

            <?php if(in_array(10,$_SESSION['permissoes'])){ ?>
            <li <?php if($page == "orcamentosPlnj"){ echo 'class="this"'; } ?> ><a href="orcamentosPlnj" title="" >Orçamentos Financeiros</a></li>
            <?php } ?>

            <?php if(in_array(14,$_SESSION['permissoes'])){ ?>
            <li class="last <?php if($page == "lancamentosPlnj"){ echo 'this"'; } ?>"><a href="lancamentosPlnj" title="" >Empenho</a></li>
            <?php } ?>

            <?php if(in_array(18,$_SESSION['permissoes'])){ ?>
            <li class="last <?php if($page == "provisao"){ echo 'this"'; } ?>"><a href="provisao" title="" >Provisão</a></li>
            <?php } ?>

          </ul>
        </li>
        <?php } ?>

        <?php if(in_array(22,$_SESSION['permissoes'])){ ?>
        <li class="ui"><a href="favorecidos" title="" <?php if($page == "favorecidos"){ echo 'class="active"'; } ?> ><span>Favorecidos</span></a></li>
        <?php } ?>

        <?php if(in_array(26,$_SESSION['permissoes'])){ ?>
        <li class="files"><a href="centroCusto" title="" <?php if($page == "centroCusto"){ echo 'class="active"'; } ?> ><span>Centro de Custo</span></a></li>
        <?php } ?>

        <?php if(in_array(30,$_SESSION['permissoes'])){ ?>
        <li class="tables" ><a href="categorias" title="" <?php if($page == "categorias"){ echo 'class="active"'; }?> ><span>Categorias</span></a></li>
        <?php } ?>

        <?php if(in_array(34,$_SESSION['permissoes'])){ ?>
        <li class="money"><a href="contas" title="" <?php if($page == "contas" || $page == "conciliacao" || $page == "importarLancamentos"){ echo 'class="active"'; } ?> ><span>Contas Financeiras</span></a></li>  
        <?php } ?>

        <?php if(in_array(38,$_SESSION['permissoes'])){ ?>
        <li class="charts"><a href="relatorios" title="" <?php if($page == "relatorios"){ echo 'class="active"'; } ?> ><span>Relatórios</span></a></li>
        <?php } ?>

        <!--<li class="building"><a href="filial" title="" <?php //if($page == "filial"){ echo 'class="active"'; } ?> ><span>Filial</span></a></li>  -->
        
        <?php
        //$contabilidadeAtiva = $db->fetch_assoc('select contador_id from conexao where conectado = 1');
        //if($contabilidadeAtiva && $contabilidadeAtiva['contador_id'] != 0){
        ?>
            <li class="calc"><a href="contabilidade" title="" class="exp" <?php if($page == "remessaContabil" || $page == "funcionarios" || $page == "documentos" || $page == "contadorMensagens" || $page == "honorarios"){ $menu_cont_display = 'style="display:block"'; echo 'id="current"'; }else{$menu_cont_display = 'style="display:none"';}?> ><span>Contabilidade</span><strong>5</strong></a>
                <ul class="sub" <?php echo $menu_cont_display;?> >
                    <li <?php if($page == "remessaContabil"){ echo 'class="this"'; } ?> ><a href="remessaContabil" title="" >Remessa Contábil</a></li>
                    <li <?php if($page == "funcionarios"){ echo 'class="this"'; } ?> ><a href="funcionarios" title="" >Funcionários</a></li>
                    <li <?php if($page == "documentos"){ echo 'class="this"'; } ?> ><a href="documentos" title="" >Documentos</a></li>
                    <li <?php if($page == "contadorMensagens"){ echo 'class="this"'; } ?> ><a href="contadorMensagens" title="" >Solicitações</a></li>
                    <li <?php if($page == "honorarios"){ echo 'class="this"'; } ?> >  <a href="honorarios" title="" >Honorários</a></li>  
                </ul>
            </li>
        <?php
        //}
        ?>
        
    </ul>
    </div>
</div>

<!--
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
            <div class="welcome">
                
                <a href="javascript://void(0);" title="" style="cursor: default;"><img src="images/userPic.png" alt="" /></a>
                
                <span><?php echo $_SESSION['email'];?></span>
                
            </div>
            <div class="userNav">
                <ul>
                    <?php if($_SESSION['parceiro_id']==342){ ?>  <li ><a href="http://agenda.webfinancas.com/Login/LoginExterno/<?php echo $_SESSION['cliente_id'];?>" title="Acesse a agenda" target="_blank"><img src="images/icons/light/refresh2.png" alt=""/><span>Agenda</span><!--<span class="numberTop">1</span>--></a></li> <?php } ?>
                   
                   <?php if($_SESSION['contador_acesso']==1){ ?>  <li id="li-area-contador" ><a href="<?php echo $protocolo.$baseUrl; ?>/contador/" title=""><img src="images/icons/light/refresh2.png" alt=""/><span>Contabilidade</span><!--<span class="numberTop">1</span>--></a></li> <?php } ?>
                    <li class="hp tour-block" id="AjudaInicial2"><a href="<?php echo $protocolo.$baseUrl; ?>/centralAjuda" target="_blank"><img src="images/icons/topnav/help2.png" alt="" /><span>Ajuda</span><!--<span class="numberTop">8</span>--></a>    
                    	<!--<ul class="helpDropdown">
                            <li><a href="javascript://void(0);" class="bt_ajudaInteligente1 sContactAdm" id="opener-dialog-ajudaListar">Ajuda Inteligente</a></li>
                            <li><a href="<?php echo $protocolo.$baseUrl; ?>/centralAjuda" class="sBook" target="_blank">Central de Ajuda</a></li>
                        </ul>-->
                    </li>
                    <!--<li><a href="javascript://void(0);" title=""><img src="images/icons/topnav/messages.png" alt=""/><span>Atendimento</span><span class="numberTop">1</span></a></li>-->
                    <li class="dd tour-block" id="AjudaInicial1"><a href="javascript://void(0);" title=""><img src="images/icons/topnav/profile.png" alt="" /><span>Minha Conta</span> <!-- <span class="numberTop">1</span> --> </a>
                        <ul class="userDropdown">
                            <li><a href="perfilCliente" class="sUser">Perfil do Cliente</a></li>
                            <?php if(in_array(43,$_SESSION['permissoes'])) echo '<li><a href="usuarios" class="sUser">Usuarios</a></li>'; ?>
                            <li><a href="javascript://void(0);" class="sLocked" id="opener-alterar-senha">Alterar a senha</a></li>
                        </ul> 
                    </li> 
                   <!-- <li><a href="javascript://void(0);" title="" id="opener-alterar-senha"><img src="images/icons/topnav/locked2.png" alt="" /><span>Alterar a senha</span></a></li> -->
                    <li><a href="javascript://void(0);" title="" class="sair" data-sair="<?php echo $_SESSION['sair_caminho']; ?>"><img src="images/icons/topnav/logout.png" alt="" /><span>Sair</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Botão para chamar ajuda inteligente -->
	<input type="hidden" class="bt_ajudaInteligente2" data-tour="ajuda">
    
    <!--
    MENU RESPONSIVO
    =================================================================================== 
    -->

    <div class="resp noPrint">
        
            <!-- start: select de licença -->
            <?php
            if(isset($_SESSION['licencas'])){
                $licencas = $_SESSION['licencas'];
                echo '
                <div style="text-align:center">
                    <select class="select-licenca" id="select-licenca-responsivo">
                ';
                foreach($licencas as $licensa){
                        
                    if($licensa['cliente_id'] == $_SESSION['cliente_id'])
                        $selected = 'selected';
                    else
                        $selected = '';
                        
                    echo '<option value="'.$licensa['cliente_id'].'" '.$selected.'>'.$licensa['nome'].'</option>';
                }
                echo '
                    </select>
                </div>
                ';
            }
            ?>
            <!-- end: select de licença -->    
        
        

        <div class="respHead">
            <?php if($_SESSION['logo_parceiro']!=1){ $logo = "images/logo_webfinancas_fundo_branco.png"; } ?>
            <a href="javascript://" style="cursor: default;" title="WebFinancas"><img src="<?php echo $logo; ?>" alt="" /></a>
        </div>
        
        <div class="cLine"></div>
        
        <div class="smalldd">
 
        
            <span class="goTo">
						<?php
                        if ($page == 'geral'|| $page ==""){ echo '<img src="images/icons/light/home.png" alt="" />Geral'; }
                        else if( $page == 'lancamentos' ){ echo '<img src="images/icons/light/money2.png" alt="" />Lançamentos'; }
                        else if( $page == 'recorrencia' ){ echo '<img src="images/icons/light/clock.png" alt="" />Recorrência'; }
                        else if( $page == 'programacao' ){ echo '<img src="images/icons/light/create.png" alt="" />Planejamento'; }
                        else if( $page == 'orcamentosPlnj' ){ echo '<img src="images/icons/light/create.png" alt="" />Planejamento > Orçamentos Financeiros'; }
                        else if( $page == 'lancamentosPlnj' ){ echo '<img src="images/icons/light/create.png" alt="" />Planejamento > Empenho'; }
                        else if( $page == 'provisao' ){ echo '<img src="images/icons/light/create.png" alt="" />Planejamento > Provisão'; }
                        else if( $page == 'favorecidos' ){ echo '<img src="images/icons/light/user.png" alt="" />Favorecidos'; }
                        else if( $page == 'centroCusto' ){ echo '<img src="images/icons/light/files.png" alt="" />Centro de Custo'; }
                        else if( $page == 'categorias' ){ echo '<img src="images/icons/light/frames.png" alt="" />Categorias'; }
                        else if( $page == 'contas' ){ echo '<img src="images/icons/light/money.png" alt="" />Contas Financeiras'; }
                        else if( $page == 'relatorios'){ echo '<img src="images/icons/light/stats.png" alt="" />Relatórios'; }
                        //else if( $page == 'filial' ){ echo '<img src="images/icons/light/building.png" alt="" />Filial'; }
                        elseif( $page == 'remessaContabil'){ echo '<img src="images/icons/light/calc.png" alt="" />Contabilidade > Remessa Contábil'; }
                        elseif( $page == 'funcionarios'){ echo '<img src="images/icons/light/calc.png" alt="" />Contabilidade > Funcionários'; }
                        elseif( $page == 'documentos'){ echo '<img src="images/icons/light/calc.png" alt="" />Contabilidade > Documentos'; }
                        elseif( $page == 'contadorMensagens'){ echo '<img src="images/icons/light/calc.png" alt="" />Contabilidade > Solicitações'; }
                        elseif( $page == 'honorarios'){ echo '<img src="images/icons/light/calc.png" alt="" />Contabilidade > Honorários'; }
                        ?>
                        </span>
            
            <ul class="smallDropdown">
                <?php if( in_array(1,$_SESSION['permissoes'])) echo '<li><a href="geral" title="" ><img src="images/icons/light/home.png" alt="" />Geral</a></li>'; ?>
                <?php if( in_array(2,$_SESSION['permissoes'])) echo '<li><a href="lancamentos" title=""><img src="images/icons/light/money2.png" alt="" />Lançamentos</a></li>'; ?>
                <?php if( in_array(6,$_SESSION['permissoes'])) echo '<li><a href="recorrencia" title=""><img src="images/icons/light/clock.png" alt="" />Recorrência</a></li>'; ?>
                
                <li><a href="programacao" title="" <?php if( $page == 'orcamentosPlnj' || $page == 'lancamentosPlnj' || $page == 'provisao' ){ echo 'class="exp active" id="current"'; }else{ echo 'class="exp"'; }?> ><img src="images/icons/light/create.png" alt="" /> Planejamento <strong>3</strong></a>
                		<ul>
                        <li><a href="orcamentosPlnj" title="">Orçamentos Financeiros</a></li>
                        <li><a href="lancamentosPlnj" title="">Empenho</a></li>
                        <li><a href="provisao" title="">Provisão</a></li>
                    </ul>
                </li>

                <?php if( in_array(22,$_SESSION['permissoes'])) echo '<li><a href="favorecidos" title=""><img src="images/icons/light/user.png" alt="" />Favorecidos</a></li>'; ?>
                <?php if( in_array(26,$_SESSION['permissoes'])) echo '<li><a href="centroCusto" title=""><img src="images/icons/light/files.png" alt="" />Centro de Custo</a></li>'; ?>
                <?php if( in_array(30,$_SESSION['permissoes'])) echo '<li><a href="categorias" title="" ><img src="images/icons/light/frames.png" alt="" />Categorias</a></li>'; ?>
                <?php if( in_array(34,$_SESSION['permissoes'])) echo '<li><a href="contas" title=""><img src="images/icons/light/money.png" alt="" />Contas Financeiras</a></li>'; ?>
                <?php if( in_array(38,$_SESSION['permissoes'])) echo '<li><a href="relatorios" title=""><img src="images/icons/light/stats.png" alt="" />Relatórios</a></li>'; ?>
                <?php //if( in_array(39,$_SESSION['permissoes'])) echo '<li><a href="contabilidade" title=""><img src="images/icons/light/calc.png" alt="" />Contabilidade</a></li>'; ?>

                <li>
                    <a href="contabilidade" title="" <?php if( $page == 'remessaContabil' || $page == 'funcionarios' || $page == 'documentos' || $page == 'contadorMensagens' ){ echo 'class="exp active" id="current"'; }else{ echo 'class="exp"'; }?> ><img src="images/icons/light/calc.png" alt="" /> Contabilidade <strong>5</strong></a>
                	<ul>
                        <li><a href="remessaContabil" title="">Remessa Contábil</a></li>
                        <li><a href="funcionarios" title="">Funcionários</a></li>
                        <li><a href="documentos" title="">Documentos</a></li>
                        <li><a href="contadorMensagens" title="">Solicitações</a></li>
                        <li><a href="honorarios" title="">Honorários</a></li>
                    </ul>
                </li>
                
                <!--<li><a href="filial" title=""><img src="images/icons/light/building.png" alt="" />Filial</a></li>-->
            </ul>
        </div>
        <div class="cLine"></div>
    </div>