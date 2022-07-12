<?php 
/**
 * @version 1.1
 * @author Rafael Vanzo
 * @RouterConfig
 */
header('Access-Control-Allow-Origin: *'); 

//date_default_timezone_set('America/Brasilia');      // Utilize este parametro somente em algumas hospedagens para Ativa a data padrão do local.
session_start();

if(!isset($_SESSION['logado']) && ($_GET['Controller'] != 'Login' && $_GET['Action'] != 'LoginExterno') ){ header('location:/Login'); } //|| !isset($_SESSION['logado']) && $_GET['Action'] != 'LoginExterno' |   

require_once "Services/ConfigService.php"; /* Configuracões */
require_once "App_Start/DbConnection.php"; /* *Conexão com o db */
require_once "App_Start/RouteConfig.php";  /* *Configuração do Router */
$Router = new Router();       /* *Instância o Router e passa o parametro da conexão com o db */

?>