<!DOCTYPE html>
<html class="fixed  sidebar-left-collapsed">
	<head>

    <?php
	/* inicio: Instancia a classe de configurações em jSon e atribui as configurações. */
	$ConfigService = new ConfigService;
	$Config = $ConfigService->GetConfiguracoes();
	/* fim: Instancia a classe de configurações em jSon e atribui as configurações. */
	?>

		<!-- Basic -->
		<meta charset="UTF-8">

        <!-- URL BASE -->
        <base href="<?php echo $Config["Layout"]["Base"]["urlBase"];?>" />

        <!-- Metadados -->
        <meta name="description" content="<?php echo $Config["Layout"]["Metadados"]["description"];?>">
        <meta name="keywords" content="<?php echo $Config["Layout"]["Metadados"]["keywords"];?>">
        <meta name="author" content="<?php echo $Config["Layout"]["Metadados"]["author"];?>">
        <meta name="robots" content="<?php echo $Config["Layout"]["Metadados"]["robots"];?>">

        <!-- Open Graph protocol markup -->
        <meta property="og:locale" content="<?php echo $Config["Layout"]["OpenGraph"]["locale"];?>" />
        <meta property="og:type" content="<?php echo $Config["Layout"]["OpenGraph"]["type"];?>" />
        <meta property="og:title" content="<?php echo $Config["Layout"]["OpenGraph"]["title"];?>" />
        <meta property="og:description" content="<?php echo $Config["Layout"]["OpenGraph"]["description"];?>" />
        <meta property="og:url" content="<?php echo $Config["Layout"]["OpenGraph"]["url"];?>" />

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
        <link rel="stylesheet" href="Assets/vendor/bootstrap/css/bootstrap.css" />

        <link rel="stylesheet" href="Assets/vendor/font-awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="Assets/vendor/magnific-popup/magnific-popup.css" />
        <link rel="stylesheet" href="Assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

        <!-- Specific Page Vendor CSS -->
        <link rel="stylesheet" href="Assets/vendor/jquery-ui/jquery-ui.css" />
        <link rel="stylesheet" href="Assets/vendor/jquery-ui/jquery-ui.theme.css" />
        <link rel="stylesheet" href="Assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />
        <link rel="stylesheet" href="Assets/vendor/morris.js/morris.css" />
        <link rel="stylesheet" href="Assets/vendor/pnotify/pnotify.custom.css" />
        <link rel="stylesheet" href="Assets/vendor/dropzone/basic.css">
        <link rel="stylesheet" href="Assets/vendor/dropzone/dropzone.css">
        <link rel="stylesheet" href="Assets/SlimUpload/css/slim.css">
        <link rel="stylesheet" href="Assets/vendor/summernote/summernote.css" />
        <link rel="stylesheet" href="Assets/vendor/select2/css/select2.css" />
        <link rel="stylesheet" href="Assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
        
        <link rel="stylesheet" href="Assets/vendor/fullcalendar/fullcalendar.css" />
		<link rel="stylesheet" href="Assets/vendor/fullcalendar/fullcalendar.print.css" media="print" />

        <link rel="stylesheet" href="Assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css">

        <!-- Theme CSS -->
        <link rel="stylesheet" href="Assets/stylesheets/theme.css" />

        <!-- Skins CSS -->
        <link rel="stylesheet" href="Assets/stylesheets/skins/default.css" />

        <!-- Theme Custom CSS -->
        <link rel="stylesheet" href="Assets/stylesheets/theme-custom.css">

        <!-- Favicon -->
        <link rel="icon" href="<?php echo $Config["Layout"]["Base"]["Favicon"]['caminho']; ?>" type="<?php echo $Config["Layout"]["Base"]["Favicon"]['imgType']; ?>" />

        <!-- Head Libs -->
        <script src="Assets/vendor/modernizr/modernizr.js"></script>

	</head>

    <!-- Para remover o loading remova a classe e o data-loading-overlay do body -->
	<body class="loading-overlay-showing" data-loading-overlay >

        <!-- Loading... -->
        <div class="loading-overlay">
            <div class="bounce-loader">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>

        <section class="body">
                                   
<?php 
    /* =============================================================================================== */

    /** Verifica se o link está chamando alguma View específica, se não carrega a View Index.php */
    (!empty($_GET["Controller"])) ? $PastaView = $_GET["Controller"] : $PastaView = $Config["Layout"]["Base"]["controller"];

    /** Verifica se o link está chamando alguma View específica, se não carrega a View Index.php */
    (!empty($_GET["Action"])) ? $View = $_GET["Action"] : $View = $Config["Layout"]["Base"]["action"];


            /** ====--- Header e Menu ---==== */
     ($_GET['Controller'] != 'Login')? require_once 'Views/Shared/HeaderMenuLayout.php' : '';


    /** Verifica se o arquivo da View existe */
    if(file_exists('Views/'.$PastaView.'/'.$View.'.php')){

            /** Carrega a View específica */
            require_once 'Views/'.$PastaView.'/'.$View.'.php';

            /** Carrega a Utilities View */
            require_once 'Views/Shared/Utilities.php';

    }else{ echo '**** Não existe a view <b>'.$View.'</b> no caminho <b>'.$PastaView.'</b>. ****'; }
	
    /* =============================================================================================== */
?>

        </section>

		<!-- Vendor -->
    <script src="Assets/vendor/jquery/jquery.js"></script>
    <script src="Assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>

	<!-- Style Switcher 
	<script src="Assets/vendor/jquery-cookie/jquery-cookie.js"></script>
	<script src="Assets/vendor/style-switcher/style.switcher.js"></script>
	 Fim Style Switcher -->

    <script src="Assets/vendor/jquery-cookie/jquery-cookie.js"></script>
    <script src="Assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script src="Assets/vendor/nanoscroller/nanoscroller.js"></script>
    <script src="Assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="Assets/vendor/magnific-popup/jquery.magnific-popup.js"></script>
    <script src="Assets/vendor/jquery-placeholder/jquery-placeholder.js"></script>     

        <!-- Plugin para datatable --> 
		<script src="Assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
		<script src="Assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>
        
        <!-- Plugin para o Modal -->
		<script src="Assets/vendor/pnotify/pnotify.custom.js"></script>
                
        <!-- Plugin Validate -->
        <script src="Assets/vendor/jquery-validation/jquery.validate.js"></script>

        <!-- Notíficação Pnotify -->
        <script src="Assets/vendor/pnotify/pnotify.custom.js"></script>
        
        <!-- Plugin Masked Input -->
        <script src="Assets/vendor/jquery-maskedinput/jquery.maskedinput.js"></script>

         <!-- Plugin Masked Input Money -->
         <script src="Assets/vendor/jquery-maskedinput/jquery.maskMoney.min.js"></script>

        <!-- Plugin Upload DropZone -->
        <script src="Assets/vendor/dropzone/dropzone.js"></script>

		<!-- Plugin Multiplus Select -->
		<script src="Assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>

        <!-- Plugin Upload Slim -->
        <script src="Assets/SlimUpload/js/slim.jquery.js"></script> <!-- data-main="Assets/SlimUpload/js/slim.kickstart.js" -->

        <!-- Plugin SummerNote -->       
		<script src="Assets/vendor/summernote/summernote.js"></script>
        <script src="Assets/vendor/summernote/lang/summernote-pt-BR.js"></script>

        <!-- Plugin Gerar Excel -->
        <script src="Assets/javascripts/excel/jquery.btechco.excelexport.js"></script>
        <script src="Assets/javascripts/excel/jquery.base64.js"></script>

        <!-- Plugin MultiSelector Bootstrap -->
        <script src="Assets/vendor/select2/js/select2.js"></script>
        <script src="Assets/vendor/select2/js/i18n/pt-BR.js"></script>

        <!-- Plugin Full Calendar -->
        <script src="Assets/vendor/moment/moment.js"></script>
        <script src="Assets/vendor/fullcalendar/fullcalendar.js"></script>
        <script src="Assets/vendor/fullcalendar/locale/pt-br.js"></script>

        <!-- Plugin GoogleMaps Geolocalização --> 
        <!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> -->
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIXCCB20u7ysaN7rpB8Xe91p6OcjuNQcQ" type="text/javascript"></script>
        <script type="text/javascript" src="Assets/GoogleMapsW2B/map.js"></script>


    <!-- Theme Base, Components and Settings -->
    <script src="Assets/javascripts/theme.js"></script>

    <!-- Theme Custom -->
    <script src="Assets/javascripts/theme.custom.js"></script>

    <!-- Theme Initialization Files -->
    <script src="Assets/javascripts/theme.init.js"></script>

    <!-- Configurações padrões do sistema -->
    <script type="text/javascript" src="Scripts/Utilities.js"></script>
    
    <!-- Especific Scripts Page -->
    <script src="<?php echo 'Scripts/'.$PastaView.'.js';?>"></script>

	</body>
</html>
