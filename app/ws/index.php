<?php
header("Access-Control-Allow-Origin: *");

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
define('ROOT_MODULOS',$_SERVER['DOCUMENT_ROOT'].'/sistema/modulos/');

require_once(ROOT.'sistema/php/db_conexao_login.php');
require_once(ROOT.'ws/Controllers/Controller.php');
require_once(ROOT.'sistema/php/DbConnectionW2b.php');

//Autentica sistema cliente no web service
if(isset($_POST['parametros'])){
    $strParams = str_replace('\"', '"', $_POST['parametros']);
    $params = json_decode($strParams, true);
}else{
    $strParams = str_replace('\"', '"', file_get_contents('php://input'));
    $arrayParams = json_decode($strParams, true);
    if(array_key_exists('parametros',$arrayParams))
        $params = $arrayParams['parametros'];
    else
        $params = $arrayParams;
}

$clienteWsCadastrado = $db->fetch_assoc('select Id, ClienteId from WS_Clientes where ChaveGuid = "'.$params['chave_guid'].'"');

//Validar se o cliente_id está relacionado com a chave_guid, sendo cliente direto do WF ou cliente de parceiro.
//A id do cliente deve estar relacionada com a id do parceiro na W2B.
//A verificação é feita se for passado o cliente_id na requisição à API.
$isClienteDoParceiro = true;
if($params['cliente_id'] && $params['cliente_id'] != $clienteWsCadastrado['ClienteId']){
    
    $cliente = $dbW2b->fetch_assoc('select parceiro_id from clientes where id = '.$params['cliente_id']);
    if($cliente['parceiro_id'] != $clienteWsCadastrado['ClienteId'])
        $isClienteDoParceiro = false;
}
$dbW2b->close();
//end: Validar se é um cliente de parceiro.


if($clienteWsCadastrado && $isClienteDoParceiro){

    //Id do sistema cliente na tabela WS_Clientes
    $params['ws_cliente_id'] = $clienteWsCadastrado['Id'];

    //Id do cliente na W2B, que é parceiro
    $params['parceiro_id'] = $clienteWsCadastrado['ClienteId'];

    $controller = $_GET['Controller'];

    //Importa a classe controller e instancia um objeto
    $controllerClass = $controller.'Controller';
    require_once('Controllers/'.$controllerClass.'.php');

    //Faz conexão com o banco de dados do cliente se não for registro de novo cliente e usuário
    if($controller != 'Registro'){
        
        //Se não for informado a id do cliente, conecta no banco de dados do parceiro
        if(!$params['cliente_id'])
            $params['cliente_id'] = $params['parceiro_id'];

        $clienteDadosDb = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$params['cliente_id']);
        $db->close();
        $db = new Database("mysql.webfinancas.com",$clienteDadosDb['db'],$clienteDadosDb['db_senha'],$clienteDadosDb['db']);
    }

    $controllerObject = new $controllerClass($db);

    //Chama a função dentro da classe controller
    call_user_func(array($controllerObject, $_GET['Action']), $params);

}else{

    echo Controller::array_to_json(array('status'=>0,'msg'=>'Integração não autorizada.'));

}

?>