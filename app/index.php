<?php 
session_start();

$urlSSL = 'https://app.webfinancas.com' . $_SERVER['REQUEST_URI'];

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $httpProtocol = 'https://';
    if ($_SERVER['HTTP_HOST'] == 'www.webfinancas.com')
        header("Location: $urlSSL");
} else {
    $httpProtocol = 'http://';
    header("Location: $urlSSL");
}

$baseUrl = $_SERVER['SERVER_NAME'];

$page = $_GET['p'];

//CentralAjuda
if($page== 'centralAjuda'){ 

		$js = "site/modulos/centralAjuda/js/funcoes.js.php";
		$pagina = "site/modulos/centralAjuda/paginas/centralAjuda.php";
		
}elseif( ($_SESSION['permissao']==1 && $page!='selecionarSistema') || ($_SESSION['permissao']==1 && $page=='selecionarSistema' && ($_SESSION['financeiro_acesso']!=1 || $_SESSION['contador_acesso']!=1) ) ){
    header('location:https://www.webfinancas.com/sistema');
    break;
}elseif($page!='selecionarSistema')
    require("site/php/db_conexao.php");

	/* === Selecionar página === */
	switch($page){
		
		/* === Quem Somos === */
		case "quemSomos";
		$js = "site/modulos/quemSomos/js/funcoes.js.php";
		$pagina = "site/modulos/quemSomos/paginas/quemSomos.php";
		break;

		/* === Selecionar Sistema === */
		case "selecionarSistema";
            if($_SESSION['permissao']==1){
		        $js = "site/modulos/selecionarSistema/js/funcoes.js.php";
		        $pagina = "site/modulos/selecionarSistema/paginas/selecionarSistema.php";
            }else{
                header('location:https://www.webfinancas.com');
            }
		break;

		/* === Planos === */
		case "planosPrecos";
		$js = "site/modulos/planos/js/funcoes.js.php";
		$pagina = "site/modulos/planos/paginas/planos.php";
		break;
		
		/* === Como Funciona === */
		case "comoFunciona";
		$js = "site/modulos/comoFunciona/js/funcoes.js.php";
		$pagina = "site/modulos/comoFunciona/paginas/comoFunciona.php";
		break;
		
		/* === Contato === */
		case "contato";
		$js = "site/modulos/contato/js/funcoes.js.php";
		$pagina = "site/modulos/contato/paginas/contato.php";
		break;
		
		/* === termos de Uso === */
		case "termosUso";
		$js = "site/modulos/termosUso/js/funcoes.js.php";
		$pagina = "site/modulos/termosUso/paginas/termosUso.php";
		break;
		
		/* === Central de Ajuda === */
		case "centralAjuda";
		$js = "site/modulos/centralAjuda/js/funcoes.js.php";
		$pagina = "site/modulos/centralAjuda/paginas/centralAjuda.php";
		break;
	
        /* === Página Inicial === */
        default:
            $js = "site/modulos/paginaInicial/js/funcoes.js.php";
            $pagina = "site/modulos/paginaInicial/paginas/paginaInicial.php";
            break;
	}
	
	/* === Fim selecionar página === */ 
?>
<!DOCTYPE html>
<!--[if IE 8]>			<html class="ie ie8"> <![endif]-->
<!--[if IE 9]>			<html class="ie ie9"> <![endif]-->
<!--[if gt IE 9]><!-->	<html> <!--<![endif]-->
	<head>

		<!-- Basic -->

        <?php 
        if($p == 'portfolio')
            echo '<base href="'.$httpProtocol.$baseUrl.'/portfolio" />';
        else
            echo '<base href="'.$httpProtocol.$baseUrl.'" />';
        ?>

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
		 <link href="site/css/fonts/google/fonts_google.css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css"> 

		<!-- Libs CSS -->
		<link rel="stylesheet" href="site/css/bootstrap.css">
		<link rel="stylesheet" href="site/css/fonts/font-awesome/css/font-awesome.css">
		<link rel="stylesheet" href="site/vendor/owl-carousel/owl.carousel.css" media="screen">
		<link rel="stylesheet" href="site/vendor/owl-carousel/owl.theme.css" media="screen">
		<link rel="stylesheet" href="site/vendor/magnific-popup/magnific-popup.css" media="screen">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="site/css/theme.css">
		<link rel="stylesheet" href="site/css/theme-elements.css">
		<link rel="stylesheet" href="site/css/theme-animate.css">

		<!-- Current Page Styles -->
		<link rel="stylesheet" href="site/vendor/rs-plugin/css/settings.css" media="screen">
		<link rel="stylesheet" href="site/vendor/circle-flip-slideshow/css/component.css" media="screen">

		<!-- Skin CSS -->
		<link rel="stylesheet" href="site/css/skins/blue.css">

		<!-- Custom CSS -->
		<link rel="stylesheet" href="site/css/custom.css">

		<!-- Responsive CSS -->
		<link rel="stylesheet" href="site/css/theme-responsive.css" />

		<!--[if IE]>-->
			<link rel="stylesheet" href="site/css/ie.css">
		<![endif]-->

 ';
?>

	</head>
	<body>

		<div class="body">

<?php require("site/menu.php"); ?>

	<?php 
	/* === Incluír página === */
	require($pagina);
	/* === Fim incluír página === */ 
	?>

<?php require("site/rodape.php"); ?>
			
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

	</body>
</html>
