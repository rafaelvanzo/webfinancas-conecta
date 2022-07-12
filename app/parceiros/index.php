<?php 
$subdominio = $_SERVER['SERVER_NAME'];
$parceiro = explode('.', $subdominio);
/*if($parceiro[0] != 'www' || $parceiro[0] != 'app'){
//header('location:https://app.webfinancas.com/parceiros/'.$parceiro[0]);
header('location:https://app.webfinancas.com/parceiros/conecta');
}*/
require("../site/php/db_conexao.php");
$logoParceiro = $db->fetch_assoc('SELECT logo_imagem FROM parceiros WHERE nome_pagina = "'.$_GET[p].'"');
if($logoParceiro['logo_imagem']==true){ $logo = "../sistema/".$logoParceiro['logo_imagem']; }else{ $logo = "../sistema/images/logo_webfinancas_fundo_branco.png"; } 
/* === Selecionar página === */
	//switch($page){
		
        /* === Página Inicial === */
        //default:
            $js = "../site/modulos/paginaInicial/js/funcoes.js.php";
            //$pagina = "paginaInicial.php";
         //   break;
	//}
	
	/* === Fim selecionar página === */ 
?>
<!DOCTYPE html>
<!--[if IE 8]>			<html class="ie ie8"> <![endif]-->
<!--[if IE 9]>			<html class="ie ie9"> <![endif]-->
<!--[if gt IE 9]><!-->	<html> <!--<![endif]-->
	<head>

		<!-- Basic -->
  	    <base href="https://app.webfinancas.com/parceiros" />
		<meta charset="utf-8">
		<title>Web Finanças - Sistema de Gestão Financeira</title>
		<meta name="keywords" content="HTML5" />
		<meta name="description" content="Web Finanças">
		<meta name="author" content="web2business.com.br">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php 
echo $css = '	
		<!-- Web Fonts  -->
		 <link href="../site/css/fonts/google/fonts_google.css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css"> 

		<!-- Libs CSS -->
		<link rel="stylesheet" href="../site/css/bootstrap.css">
		<link rel="stylesheet" href="../site/css/fonts/font-awesome/css/font-awesome.css">
		<link rel="stylesheet" href="../site/vendor/owl-carousel/owl.carousel.css" media="screen">
		<link rel="stylesheet" href="../site/vendor/owl-carousel/owl.theme.css" media="screen">
		<link rel="stylesheet" href="../site/vendor/magnific-popup/magnific-popup.css" media="screen">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="../site/css/theme.css">
		<link rel="stylesheet" href="../site/css/theme-elements.css">
		<link rel="stylesheet" href="../site/css/theme-animate.css">

		<!-- Current Page Styles -->
		<link rel="stylesheet" href="../site/vendor/rs-plugin/css/settings.css" media="screen">
		<link rel="stylesheet" href="../site/vendor/circle-flip-slideshow/css/component.css" media="screen">

		<!-- Skin CSS -->
		<link rel="stylesheet" href="../site/css/skins/blue.css">

		<!-- Custom CSS -->
		<link rel="stylesheet" href="../site/css/custom.css">

		<!-- Responsive CSS -->
		<link rel="stylesheet" href="../site/css/theme-responsive.css" />

		<!--[if IE]>-->
			<link rel="stylesheet" href="../site/css/ie.css">
		<![endif]-->
        
        <style>
        body{
        background: url("https://app.webfinancas.com/parceiros/img/bg_parceiros.jpg");
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        }
        </style>
 ';
?>

	</head>
	<body>

		<div class="body">


	<?php 
	/* === Incluír página === */
	//require($pagina);
	/* === Fim incluír página === */ 
	?>

                  <!-- =========================== Modal Login ============================== -->
							<div class="modal fade in" id="myModalLogin" tabindex="-1" role="dialog" scrool=no aria-labelledby="myModalLabel" aria-hidden="true" style="display:block; overflow-y:auto;">
								<div class="modal-dialog" style="max-width: 380px;">
									<div class="modal-content">

                    <form id="loginForm">

                      <input type="hidden" name="funcao" value="login">
  
                      <div class="modal-header">
                        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                        <h4 class="modal-title" id="myModalLabel" align="center"><img src="<?php echo $logo; ?>" /></h4>
                      </div>
                      
                      <div class="modal-body">

                        <div class="hidden alert alert-success img-responsive appear-animation bounceIn appear-animation-visible" id="loginSuccess" align="center" data-appear-animation="bounceIn">
                          <i class="icon icon-check-circle-o" style="font-size:48px;"></i> <br />
                          <strong>Senha recuperada com sucesso!</strong> <br />
                          <span style="font-size:12px;">Uma nova senha foi enviada para o seu e-mail.</span> <br /> 
                        </div>
  
                        <div class="hidden alert alert-error img-responsive appear-animation shake appear-animation-visible" id="loginError" align="center" data-appear-animation="shake" style="padding-top:-70px; padding-bottom: 0px;">
                          <strong style="font-size: 32px;color:#900;">Oops!</strong> <br> 
                          <span style="font-size:13px;color:#900;" class="notificacao"></span>
                        </div>
          
                        <div class="row form-login-element">
                        
                          <div class="form-group">
                            
                            <div class="col-md-12">
                              <label>E-mail:</label>
                              <input type="email" data-msg-required="Por favor preencha um e-mail para contato." data-msg-email="Porfavor preencha um e-mail válido." maxlength="100" class="form-control form-login" name="email">
                            </div>
                            
                            <div class="col-md-12" style="padding-top: 7px;">
                              <label>Senha:</label>
                              <input type="password" class="form-control" name="senha" maxlength="15">
                            </div>
                            
                            <div class="col-md-12" align="right"  style="padding-top: 7px;">
                            	<label > <a href="javascript://void(0);" onClick="senha_recuperar();" id="senhaRecuperar">Esqueceu sua senha?</a> </label>
                            </div>
                            
                          </div>
                      
                        </div> 
                        
                      </div>
                      
                      <div class="modal-footer" style="margin-top: -20px;">
                      		<button type="button" class="btn btn-primary hidden" id="senhaRecuperarOk" data-dismiss="modal">OK</button>
                          <button type="submit" class="btn btn-primary form-login-element" id="entrar" data-loading-text="Verificando..." data-complete-text="Entrando...">Entrar</button>
                          <!--<button type="button" class="btn btn-default form-login-element" data-dismiss="modal">Cancelar</button>-->
                      </div>
                    
										</form>
                  </div>
                  
								</div>

                                <p align="center" style="font-size:11px; color:#d9d9d9; text-shadow: 0 1px 2px #000;">Web Finanças © 2011-<?php echo date('Y'); ?>. Todos os direitos reservados.</p>

							</div>
 
      <!-- =========================== Modal ============================== -->  


            

<div class="modal-backdrop fade in"></div>

			
		</div> <!-- Div Body -->

    <!-- Head Libs -->
    <script src="sistema/js/jquery/1.8.3/jquery.min.js"></script>
    <script src="site/vendor/modernizr.js"></script>

		<!--[if lte IE 8]>
			<script src="site/vendor/respond.js"></script>
		<![endif]-->

<?php 
	/* === js ===*/
	require($js); 
?>
        <script>$('data-dismiss="modal"').off();</script>
	</body>
</html>
