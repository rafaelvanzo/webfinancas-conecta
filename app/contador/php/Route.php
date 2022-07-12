<?php
session_start();

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');

$phpSelf = $_SERVER['PHP_SELF'];
$arrayPhpSelf = explode('/',$phpSelf);

if(strtolower($arrayPhpSelf[1]) == 'desenvolvimento')
    define('ROOT_SISTEMA',$_SERVER['DOCUMENT_ROOT'].'/desenvolvimento/contador/');
else
    define('ROOT_SISTEMA',$_SERVER['DOCUMENT_ROOT'].'/contador/');

require_once(ROOT."contador/php/db_conexao.php");
require_once(ROOT.'contador/Controllers/UtilController.php');

//$strParams = str_replace('\"', '"', file_get_contents('php://input'));
//$params = json_decode($strParams, true);

$controller = $_GET['Controller'];

//Importa a classe controller e instancia um objeto
$controllerClass = $controller.'Controller';
require_once(ROOT_SISTEMA.'Controllers/'.$controllerClass.'.php');

$controllerObject = new $controllerClass($db);

//Chama a função dentro da classe controller
call_user_func(array($controllerObject, $_GET['Action']), $_REQUEST);

?>