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

if (!isset($_SESSION['permissao'])) {

    $paginaSelecionada['pagina'] = 'modulos/usuario/paginas/login.php';

    //header('location:https://www.webfinancas.com/#login');
    //break;

} else {

    if ($_SESSION['cli_acesso_situacao'] == '2') {

        if ($_GET['p'] != "bloqueado") {
            header('location:' . $httpProtocol . $baseUrl . '/sistema/bloqueado');
            break;
        }
    } elseif ($_SESSION['cli_acesso_situacao'] == '3') {

        if ($_GET['p'] != "contratar") {
            header('location:' . $httpProtocol . $baseUrl . '/sistema/contratar');
            break;
        }
    }

    require("php/db_conexao.php");

    //Cálculo do saldo total disponível
    //A verificação da sessão foi comentada para que o total geral seja atualizado quando cliente estiver num grupo econômico
    //if(!isset($_SESSION['total_disponivel'])){
    $query_total_disponivel = mysql_query("select sum(vl_saldo) saldo_total, sum(vl_credito) credito_total from contas", $db->link_id);
    $total_disponivel = mysql_fetch_assoc($query_total_disponivel);
    $total_disponivel = $total_disponivel['saldo_total'] + $total_disponivel['credito_total'];
    $total_disponivel = number_format($total_disponivel, 2, ',', '.');
    $_SESSION['total_disponivel'] = 'R$ ' . $total_disponivel;
    //}

    /**
     * verifica se usuário tem permissão de acesso à página
     * caso tenha, retorna status = true
     * caso não, faz a busca da primeira página para a qual o usuário tem acesso dentro do array $arrayPermissaoLeitura
     * retorna status = false para que a função SelecionaPagina seja chamada recursivamente com a página permitida
     * */

    function VerificaPermissaoUsuario($permissao_id)
    {
        if (!in_array($permissao_id, $_SESSION['permissoes'])) {
            $arrayPermissaoLeitura = array(
                1 => 'geral',
                2 => 'lancamentos',
                6 => 'recorrencia',
                10 => 'orcamentosPlnj',
                14 => 'lancamentosPlnj',
                18 => 'provisao',
                22 => 'favorecidos',
                26 => 'centroCusto',
                30 => 'categorias',
                34 => 'contas',
                38 => 'relatorios',
                39 => 'contabilidade'
            );
            foreach ($arrayPermissaoLeitura as $id => $pagina) {
                if (in_array($id, $_SESSION['permissoes'])) {
                    $permissao = array('status' => false, 'pagina' => $pagina);
                    break;
                }
            }
            return $permissao;
        } else {
            return array('status' => true);
        }
    }

    /* === Selecionar página === */
    function SelecionaPagina($page)
    {

        switch ($page) {

                // === Lançamentos ===
            case "lancamentos";
                $verificaPermissao = VerificaPermissaoUsuario(2);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }

                return array(
                    'js' => "modulos/lancamento/js/funcoes.js.php",
                    'pagina' => "modulos/lancamento/paginas/lancamentos.php"
                );

                // === Lançamentos Teste ===
            case "lancamentosTeste";

                return array(
                    'js' => "modulos/lancamentoTeste/js/funcoes.js.php",
                    'pagina' => "modulos/lancamentoTeste/paginas/lancamentos.php"
                );

                // === Conciliação ===
            case "conciliacao";
                $verificaPermissao = VerificaPermissaoUsuario(2);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }

                return array(
                    'js' => "modulos/lancamento/js/funcoes_cnlc.js.php",
                    'pagina' => "modulos/lancamento/paginas/conciliacao.php"
                );

                // === Importar Lançamentos ===
            case "importarLancamentos";

                return array(
                    'js' => "modulos/lancamento/js/funcoes_lnct_import.js.php",
                    'pagina' => "modulos/lancamento/paginas/lnct_import.php"
                );

                // === Recorrência ===
            case "recorrencia";
                $verificaPermissao = VerificaPermissaoUsuario(6);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }

                return array(
                    'js' => "modulos/programacao/js/funcoes.js.php",
                    'pagina' => "modulos/programacao/paginas/recorrencia.php"
                );

                // === Recorrência Teste ===
            case "recorrenciaTeste";

                return array(
                    'js' => "modulos/programacaoTeste/js/funcoes.js.php",
                    'pagina' => "modulos/programacaoTeste/paginas/recorrencia.php"
                );

                // === Orçamentos planejados ===
            case "orcamentosPlnj";
                $verificaPermissao = VerificaPermissaoUsuario(10);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }

                return array(
                    'js' => "modulos/planejamento/js/funcoes_orct.js.php",
                    'pagina' => "modulos/planejamento/paginas/orcamentos.php"
                );

                // === Lançamentos planejados ===
            case "lancamentosPlnj";
                $verificaPermissao = VerificaPermissaoUsuario(14);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }

                return array(
                    'js' => "modulos/planejamento/js/funcoes_lnct.js.php",
                    'pagina' => "modulos/planejamento/paginas/lancamentos.php"
                );

                // === Provisão ===
            case "provisao";
                $verificaPermissao = VerificaPermissaoUsuario(18);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }

                return array(
                    'js' => "modulos/planejamento/js/funcoes_prov.js.php",
                    'pagina' => "modulos/planejamento/paginas/provisao.php"
                );

                // === Favorecidos ===
            case "favorecidos";
                $verificaPermissao = VerificaPermissaoUsuario(22);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/favorecido/js/funcoes.js.php",
                    'pagina' => "modulos/favorecido/paginas/favorecidos.php"
                );

                // === Favorecidos Teste ===
            case "favorecidosteste";
                return array(
                    'js' => "modulos/favorecidoteste/js/funcoes.js.php",
                    'pagina' => "modulos/favorecidoteste/paginas/favorecidos.php"
                );

                // === Contas ===
            case "contas";

                $verificaPermissao = VerificaPermissaoUsuario(34);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/conta/js/funcoes.js.php",
                    'pagina' => "modulos/conta/paginas/contas.php"
                );

                // === Remessa ===
            case "arquivosRemessa";
                $verificaPermissao = VerificaPermissaoUsuario(34);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/conta/js/funcoes.js.php",
                    'pagina' => "modulos/conta/paginas/contas_remessa.php"
                );

                // === Centro de Responsábilidade ===
            case "centroCusto";
                $verificaPermissao = VerificaPermissaoUsuario(26);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/centro_resp/js/funcoes.js.php",
                    'pagina' => "modulos/centro_resp/paginas/centro_resp.php"
                );

                // === Plano de Contas ===
            case "categorias";
                $verificaPermissao = VerificaPermissaoUsuario(30);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/planoContas/js/funcoes.js.php",
                    'pagina' => "modulos/planoContas/paginas/planoContas.php"
                );

                // === Relatórios ===
            case "relatorios";
                $verificaPermissao = VerificaPermissaoUsuario(38);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/relatorios/js/funcoes.js.php",
                    'pagina' => "modulos/relatorios/paginas/relatorios.php"
                );

                // === Relatórios ===
            case "relatoriosteste";
                $verificaPermissao = VerificaPermissaoUsuario(38);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/relatoriosteste/js/funcoes.js.php",
                    'pagina' => "modulos/relatoriosteste/paginas/relatorios.php"
                );

                // === Filial === 
            case "filial";
                $pagina = "filial.php";
                break;

                // === Usuários ===
            case "usuarios";
                $verificaPermissao = VerificaPermissaoUsuario(43);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/usuario/js/funcoes_usuarios.js.php",
                    'pagina' => "modulos/usuario/paginas/usuarios.php"
                );

                // === Empresa ===
            case "perfilCliente";
                return array(
                    'js' => "modulos/usuario/js/funcoes.js.php",
                    'pagina' => "modulos/usuario/paginas/perfil_usuario.php"
                );

                // === Contratação ===
            case "contratar";
                return array(
                    'js' => "modulos/usuario/js/funcoes.js.php",
                    'pagina' => "modulos/usuario/paginas/contratar.php"
                );

                // === Bloqueado ===
            case "bloqueado";
                return array(
                    'js' => "modulos/usuario/js/funcoes.js.php",
                    'pagina' => "modulos/usuario/paginas/bloqueado.php"
                );

                // === Faturas ===
            case "faturas";
                return array(
                    'js' => "modulos/usuario/js/funcoes.js.php",
                    'pagina' => "modulos/usuario/paginas/faturas.php"
                );

                // === Remessa Contábil ===
            case "remessaContabil";
                $verificaPermissao = VerificaPermissaoUsuario(39);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/contador/js/funcoes.js.php",
                    'pagina' => "modulos/contador/paginas/contador.php"
                );

                // === Contador ===
                //case "contadorRemessa";
                //$js = "modulos/contador/js/funcoes.js.php";
                //$pagina = "modulos/contador/paginas/contadorRemessa.php";
                //break;

                /* === Mensagens === */
            case "contadorMensagens";
                $verificaPermissao = VerificaPermissaoUsuario(39);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/contador/js/funcoes.js.php",
                    'pagina' => "modulos/contador/paginas/contadorMensagens.php"
                );

                // === Funcionários ===
            case 'funcionarios':
                $verificaPermissao = VerificaPermissaoUsuario(39);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "js/Funcionario.js.php",
                    'pagina' => "Views/Funcionario/Index.php"
                );

                // === Documentos ===
            case 'documentos':
                $verificaPermissao = VerificaPermissaoUsuario(39);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "js/Arquivo.js.php",
                    'pagina' => "Views/Arquivo/Index.php"
                );

                // === Honorários ===
            case 'honorarios':
                $verificaPermissao = VerificaPermissaoUsuario(39);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "js/Honorario.js.php",
                    'pagina' => "Views/Honorario/Index.php"
                );


                // === Custeio ===
            case 'custeio':
                $verificaPermissao = VerificaPermissaoUsuario(39);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "js/Custeio.js.php",
                    'pagina' => "Views/Custeio/Index.php"
                );


                /* === Geral === */
            default:
                $verificaPermissao = VerificaPermissaoUsuario(1);
                if (!$verificaPermissao['status']) {
                    SelecionaPagina($verificaPermissao['pagina']);
                    break;
                }
                return array(
                    'js' => "modulos/geral/js/funcoes.js.php",
                    'pagina' => "modulos/geral/paginas/geral.php"
                );
        }
    }
    /* === Fim selecionar página === */

    /* === Pega a referência via GET em qual página esta sendo exibida === */
    $page = $_GET['p'];
    $js = '';
    $pagina = '';
    $paginaSelecionada = SelecionaPagina($page);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php echo '<base href="' . $httpProtocol . $baseUrl . $_SERVER['PHP_SELF'] . '" />'; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>Web Finanças - Sistema de Gestão Financeira</title>

    <?php
    echo '
<!-- === CSS === -->
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-switch.css" rel="stylesheet" type="text/css" />
<link href="css/fullcalendar.css" rel="stylesheet" type="text/css" />
<link href="css/datatable.css" rel="stylesheet" type="text/css" />
<link href="css/ui_custom.css" rel="stylesheet" type="text/css" />
<link href="css/prettyPhoto.css" rel="stylesheet" type="text/css" />
<link href="css/elfinder.css" rel="stylesheet" type="text/css" />
<link href="css/main.css" rel="stylesheet" type="text/css" />
<!--<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />-->
<!--<link href="css/print.css" rel="stylesheet" type="text/css" media="print"/>-->
';

    ?>

</head>

<?php

if (isset($_SESSION['permissao']) && $_SESSION['permissao'] == 1) {

    if ($page == "contratar" || $page == "bloqueado") {
        echo '<body class="bodySemMenu">';
    } else {
        echo '<body>';

        /* === Menu Lateral Esquerdo === */
        //include("menu_lateral_esquerdo.php");

        /* === Menu Superior === */
        //include("menu_superior.php");

        /* === Menu === */
        require("menu.php");
    }
} else {

    echo '<body class="nobg loginPage">';
}

/* === Incluír página === */
require($paginaSelecionada['pagina']);
/* === Fim incluír página === */

/* === dialog_alerta.php === */
//include("dialog_alerta.php"); 

/* === alterar_senha.php === */
//include("modulos/usuario/paginas/alterar_senha.php");

/* === cancelar.php === */
//include("modulos/usuario/paginas/cancelar.php");

/* === Shadow box de carregamento === */
//include("aguarde.php"); 

/* === dialogs alerta, alterar senha e aguarde === */
require("extras.php");

?>

<br />

<?php
if ($page != "contratar" && $page != "bloqueado") {
?>

    <!-- Footer line -->
    <div id="footer">
        <div class="wrapper">Web Finanças © 2011-<?php echo date('Y'); ?>. Todos os direitos reservados.</div>
    </div>

<?php
}
?>

<!-- Repositório de dados do javascript -->
<div id="dados">
</div>

</body>

<?php
echo '
<!-- === JavaScript === -->

<!--<script type="text/javascript" src="js/jquery/1.8.3/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-1.9.1.js"></script>-->
<script src="js/jquery/jquery-migration/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery/jqueryui/1.9.2/jquery-ui.min.js"></script>
<!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.4.0.min.js"></script>-->
<script src="js/jquery/jquery-migration/jquery-migrate-1.4.0.min.js"></script>
<script type="text/javascript" src="js/plugins/tables/datatable.js"></script>
';

/* === Incluír javascript da página carregada === */
if (isset($_SESSION['permissao']) && $_SESSION['permissao'] == 1) {
    include($paginaSelecionada['js']);
}
/* === Fim incluír javascript === */

echo '
<!-- === JavaScript === -->
<script type="text/javascript" src="modulos/usuario/js/funcoes.js"></script>

<script type="text/javascript" src="js/plugins/spinner/jquery.mousewheel.js"></script>

<script type="text/javascript" src="js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.pie.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.resize.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.sparkline.min.js"></script>

<script type="text/javascript" src="js/plugins/forms/uniform.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.cleditor.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.validationEngine.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.autosize.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="js/plugins/forms/jquery.inputlimiter.min.js"></script>
<script type="text/javascript" src="js/plugins/forms/chosen.jquery.min.js"></script>

<script type="text/javascript" src="js/plugins/wizard/jquery.form.js"></script>
<script type="text/javascript" src="js/plugins/wizard/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/plugins/wizard/jquery.validate.cpf.cnpj.js"></script>
<script type="text/javascript" src="js/plugins/wizard/jquery.form.wizard.js"></script>

<!--
<script type="text/javascript" src="js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="js/plugins/uploader/jquery.plupload.queue.js"></script>
-->
<script type="text/javascript" src="js/plugins/plupload.v2/plupload.full.min.js"></script>
<script type="text/javascript" src="js/plugins/plupload.v2/pt_BR.js"></script>

<script type="text/javascript" src="js/plugins/tables/tablesort.min.js"></script>
<script type="text/javascript" src="js/plugins/tables/resizable.min.js"></script>
<script type="text/javascript" src="js/plugins/tables/FixedColumns.js"></script>

<script type="text/javascript" src="js/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.collapsible.min.js"></script>
<!--<script type="text/javascript" src="js/plugins/ui/jquery.prettyPhoto.js"></script>-->
<script type="text/javascript" src="js/plugins/ui/jquery.progress.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.timeentry.min.js"></script>
<!--<script type="text/javascript" src="js/plugins/ui/jquery.colorpicker.js"></script>-->
<script type="text/javascript" src="js/plugins/ui/jquery.jgrowl.js"></script>
<!--<script type="text/javascript" src="js/plugins/ui/jquery.breadcrumbs.js"></script>-->
<script type="text/javascript" src="js/plugins/ui/jquery.sourcerer.js"></script>
<script type="text/javascript" src="js/plugins/ui/jquery.mtz.monthpicker.js"></script>

<script type="text/javascript" src="js/plugins/jquery.fullcalendar.js"></script>
<script type="text/javascript" src="js/plugins/jquery.elfinder.js"></script>
<script type="text/javascript" src="js/plugins/jquery-moeda.js"></script>

<script type="text/javascript" src="js/plugins/bootstrap.min.js"></script>

<script type="text/javascript" src="js/plugins/checkbox/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/custom.js"></script>

<!--<script type="text/javascript" src="js/charts/bar.js"></script>-->
<!--<script type="text/javascript" src="js/charts/pie.js"></script>-->
<!--<script type="text/javascript" src="js/charts/chart.js"></script>-->
<script type="text/javascript" src="js/charts/hBar.js"></script>
<script type="text/javascript" src="js/charts/updating.js"></script>

<!--<script type="text/javascript" src="js/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>-->
';
?>

</html>