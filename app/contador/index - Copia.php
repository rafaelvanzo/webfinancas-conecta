<?php
session_start();

if( !isset($_SESSION['permissao_contador']) || $_SESSION['permissao_contador']!= 1 ){
	
	header('location:https://www.webfinancas.com/#login');
    break;

}else{

    $raiz = "https://www.webfinancas.com/sistema/";

	require("php/db_conexao.php");

	/* === Pega a referência via GET em qual página esta sendo exibida === */
	$page = (isset($_GET['p'])) ? $_GET['p'] : '';

	/* === Selecionar página === */
	switch($page){ 
        
        /* === Mensagens === */
        default: "contadorMensagens";
        $js = "modulos/mensagem/js/funcoes.js.php";
        $pagina = "modulos/mensagem/paginas/contadorMensagens.php";
        break;
        
        /* === Clientes === */
        case "convites";
        $js = "modulos/convites/js/funcoes.js.php";
        $pagina = "modulos/convites/paginas/convites.php";
        break;
        
	    /* === Clientes === */
        case "clientes";
	    $js = "modulos/clientes/js/funcoes.js.php";
	    $pagina = "modulos/clientes/paginas/clientes.php";
	    break;

        /* === Arquivos === */
	    case "documentos";
        $js = "Scripts/Arquivo.js.php";
        $pagina = "Views/Arquivo/Index.php";
        break;
            
        /* === Informativo === */
	    case "informativo";
        $js = "modulos/informativo/js/funcoes.js.php";
        $pagina = "modulos/informativo/paginas/informativo.php";
        break;
        
        /* === Arquivo Contabil === */
        case "arquivoContabil";
        $js = "modulos/arquivoContabil/js/funcoes.js.php";
        $pagina = "modulos/arquivoContabil/paginas/arquivoContabil.php";
        break;

        /* === Arquivo Contabil -> Clientes detalhes === */
        case "clientesDetalhes";
        $js = "modulos/arquivoContabil/js/funcoes.js.php";
        $pagina = "modulos/arquivoContabil/paginas/clientes_detalhes.php";
        break;
        
        /* === Arquivo Contabil -> Clientes Configuração === */
        case "clientesDetalhesConfig";
            $js = "modulos/arquivoContabil/js/funcoes.js.php";
            $pagina = "modulos/arquivoContabil/paginas/clientes_detalhes_config.php";
            break;

        /* === Arquivo Contabil -> Clientes Configuração Teste === */
        case "clientesDetalhesConfigTeste";
        $js = "modulos/arquivoContabilTeste/js/funcoes.js.php";
        $pagina = "modulos/arquivoContabilTeste/paginas/clientes_detalhes_config.php";
        break;
        
        /* === Arquivo Contabil -> Clientes === */
        case "clientesDetalhesDocumentos";
        $js = "modulos/arquivoContabil/js/funcoes.js.php";
        $pagina = "modulos/arquivoContabil/paginas/clientes_detalhes_documentos.php";
        break;
        
        /* === Arquivo Contabil -> Clientes Gerar arquivo contábil === */
        case "clientesDetalhesGerarArquivo";
        $js = "modulos/arquivoContabil/js/funcoes.js.php";
        $pagina = "modulos/arquivoContabil/paginas/clientes_detalhes_gerar_arq_contabil.php";
        break;

		/* === Carne Leão === */
	    case "carneLeao";
        $js = "Scripts/CarneLeao.js.php";
        $pagina = "Views/CarneLeao/Index.php";
        break;
	}
	/* === Fim selecionar página === */ 

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo '<base href="https://www.webfinancas.com'.$_SERVER['PHP_SELF'].'" />'; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>Web Finanças - Sistema de Contador</title>

<?php
echo '
<!-- === CSS === -->
<link href="'.$raiz.'css/reset.css" rel="stylesheet" type="text/css" />
<link href="'.$raiz.'css/fullcalendar.css" rel="stylesheet" type="text/css" />
<link href="'.$raiz.'css/datatable.css" rel="stylesheet" type="text/css" />
<link href="'.$raiz.'css/ui_custom.css" rel="stylesheet" type="text/css" />
<link href="'.$raiz.'css/prettyPhoto.css" rel="stylesheet" type="text/css" />
<link href="'.$raiz.'css/elfinder.css" rel="stylesheet" type="text/css" />
<link href="'.$raiz.'css/main.css" rel="stylesheet" type="text/css" />
<!--<link href="'.$raiz.'css/print.css" rel="stylesheet" type="text/css" media="print"/>-->
<!-- Tourist -->
<link rel="stylesheet" href="'.$raiz.'js/plugins/tourist/tourist.css" type="text/css" media="screen">
';
?>

</head>

<body>	
	
  <?php 
	
//	if(isset($_SESSION['permissao']) && $_SESSION['permissao'] == 1){
	
		echo '<body>';
		/* === Menu === */
		require("menu.php");
		
//	}else{ 

//		echo '<body class="nobg loginPage">';
			
//  }
	
	/* === Incluír página === */
	require($pagina);
	/* === Fim incluír página === */ 
  
	
	/* === dialogs alerta, instruções iniciais, alterar senha e aguarde === */
	require("extras.php");
	?>  
    
    <br />

    <!-- Footer line -->
    <div id="footer" class="noPrint">
        <div class="wrapper">Web Finanças © 2011-<?php echo date('Y'); ?>. Todos os direitos reservados.</div>
    </div>

    <!-- Repositório de dados do javascript -->
    <div id="dados">
    </div>

</body>

<?php

echo '
<!-- === JavaScript === -->
<!--<script type="text/javascript" src="'.$raiz.'js/jquery/1.8.3/jquery.min.js"></script>-->
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="'.$raiz.'js/jquery/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.4.0.min.js"></script>
';

/* === Incluír javascript da página carregada === */
//if(isset($_SESSION['permissao']) && $_SESSION['permissao'] == 1){
	require($js); 
//}
/* === Fim incluír javascript === */

echo '
<!-- === JavaScript === -->
<script type="text/javascript" src="'.$raiz.'js/plugins/tables/datatable.js"></script>
<script type="text/javascript" src="modulos/usuario/js/funcoes.js"></script>
<!--<script type="text/javascript" src="'.$raiz.'modulos/ajuda/js/funcoes.js"></script>--> <!-- Ajuda -->	

<script type="text/javascript" src="'.$raiz.'js/plugins/spinner/jquery.mousewheel.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/charts/jquery.flot.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/charts/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/charts/jquery.flot.pie.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/charts/jquery.flot.resize.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/charts/jquery.sparkline.min.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/forms/uniform.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.validationEngine.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.cleditor.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.autosize.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/chosen.jquery.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/forms/jquery.inputlimiter.min.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/wizard/jquery.form.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/wizard/jquery.validate.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/wizard/jquery.form.wizard.js"></script>

<!--
<script type="text/javascript" src="'.$raiz.'js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/uploader/jquery.plupload.queue.js"></script>
-->

<script type="text/javascript" src="'.$raiz.'js/plugins/plupload.v2/plupload.full.min.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/tables/tablesort.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/tables/resizable.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/tables/FixedColumns.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.tipsy.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.progress.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.colorpicker.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.jgrowl.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.breadcrumbs.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.collapsible.min.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.sourcerer.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/ui/jquery.mtz.monthpicker.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/jquery.fullcalendar.js"></script>

<script type="text/javascript" src="'.$raiz.'js/plugins/jquery.elfinder.js"></script>
<script type="text/javascript" src="'.$raiz.'js/plugins/jquery-moeda.js"></script>

	<!-- JS Tourist -->
  <script src="'.$raiz.'js/plugins/tourist/javascripts/underscore-1.4.4.js"></script>
  <script src="'.$raiz.'js/plugins/tourist/javascripts/backbone-1.0.0.js"></script>
  <script src="'.$raiz.'js/plugins/tourist/tourist.js"></script>  
  
  <script type="text/javascript" src="'.$raiz.'js/plugins/bootstrap.min.js"></script>

<script type="text/javascript" src="Scripts/custom.js"></script>
<!--
<script type="text/javascript" src="'.$raiz.'js/charts/bar.js"></script>
<script type="text/javascript" src="'.$raiz.'js/charts/pie.js"></script>
<script type="text/javascript" src="'.$raiz.'js/charts/chart.js"></script>
<script type="text/javascript" src="'.$raiz.'js/charts/hBar.js"></script>
<script type="text/javascript" src="'.$raiz.'js/charts/updating.js"></script>
-->
'; 
?>

</html>