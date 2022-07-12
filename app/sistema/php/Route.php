<?php
session_start();

header("Content-Type: text/html; charset=UTF-8");

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');

$phpSelf = $_SERVER['PHP_SELF'];
$arrayPhpSelf = explode('/',$phpSelf);

if(strtolower($arrayPhpSelf[1]) == 'desenvolvimento')
  define('ROOT_SISTEMA',$_SERVER['DOCUMENT_ROOT'].'/desenvolvimento/sistema/');
else
  define('ROOT_SISTEMA',$_SERVER['DOCUMENT_ROOT'].'/sistema/');

require_once(ROOT."sistema/php/db_conexao.php");
require_once(ROOT.'sistema/php/Util.php');

//$strParams = str_replace('\"', '"', file_get_contents('php://input'));
//$params = json_decode($strParams, true);

//Retorna o controller da URL
$controller = $_GET['Controller'];

//Verifica se é um cliente da Lexdata
if( ($_SESSION['parceiro_id']=='244' && $controller=='Honorario') || $controller=='HonorarioLexdata'){
    
    //Importa a classe controller e instancia um objeto
    $controllerClass = 'HonorarioLexdataController';
    require_once(ROOT.'lexdata/Controllers/HonorarioLexdataController.php');

}else{
    
    //Importa a classe controller e instancia um objeto
    $controllerClass = $controller.'Controller';
    require_once(ROOT_SISTEMA.'Controllers/'.$controllerClass.'.php');
}

$controllerObject = new $controllerClass($db);

//Chama a função dentro da classe controller
call_user_func(array($controllerObject, $_GET['Action']), $_REQUEST);
?>