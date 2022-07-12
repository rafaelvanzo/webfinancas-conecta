<?php
// ====================== db ======================= 
require('Database.class.php');
require ('php-mailer/class.phpmailer.php');
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/servicos/mensagem/MensagemHelper.php";

$host = "mysql.web2business.com.br";
$usuario = "web2business";
$senha = "W2BSISTEMAS";
$db_usuario = "web2business";
$db_w2b = new Database($host,$usuario,$senha,$db_usuario);

$host = "mysql.webfinancas.com";
$usuario = "webfinancas";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinancas";
$db = new Database($host,$usuario,$senha,$db_usuario);

//REALIZA CADASTRO
//------------------------------------------------------------------------------------------------------------

//Email
$email = $_POST['email'];

$usuario_id = $db->fetch_assoc('select id from usuarios where email = "'.$email.'"');
	
if(!$usuario_id['id']){

	$cliente_id = $db_w2b->fetch_assoc('select id from clientes where email = "'.$email.'"');

	if(!$cliente_id['id']){
		//insere cliente no banco de dados da w2b
		$array_cadastro['nome'] = $_POST['nome'];
		$array_cadastro['email'] = $email;
		$array_cadastro['telefone'] = $_POST['tel'];
		$array_cadastro['tp_cadastro'] = 1;
		$array_cadastro['dt_cadastro'] = date('Y-m-d H:i:s');
		$db_w2b->query_insert('clientes',$array_cadastro);
		$cliente_id = mysql_insert_id($db_w2b->link_id);
	}else{
		$cliente_id = $cliente_id['id'];
	}
		
	//busca um banco de dados no web finanças para alocar os registros do cliente
	$cliente_db = $db->fetch_assoc('select min(id) id from clientes_db where situacao = 0');
	$db->query('update clientes_db set cliente_id = '.$cliente_id.', situacao = 1 where id = '.$cliente_db['id']);

	//insere usuário no banco de dados do web finanças
	$array_usuario['cliente_id'] = $cliente_id;
	$array_usuario['cliente_db_id'] = $cliente_db['id'];
	$array_usuario['email'] = $_POST['email'];
	$array_usuario['senha'] = md5($_POST['senha']);
	$array_usuario['situacao'] = 0;
	$array_usuario['dt_cadastro'] = date('Y-m-d H:i:s');
	$array_usuario['financeiro'] = 1; //registro que o usuário se cadastrou para o sistema financeiro
    if($_POST['tp_cadastro']=='contador')
        $array_usuario['contador'] = 1; //registro que o usuário se cadastrou como contador
    $array_usuario['grupo_id'] = 1; //grupo administrador para todos os usuário que são administradores em suas respectivas licenças
	$usuario_id = $db->query_insert('usuarios',$array_usuario);
		
	//registra situação de acesso do cliente no web finanças
	$array_situacao['cliente_id'] = $cliente_id;
	$array_situacao['situacao'] = 1;
	$db->query_insert('cli_acesso_situacao',$array_situacao);
		
	//Registra no contador + 1 cliente após o término do cadastro
	$qtd = $db->fetch_assoc('select qtd from contador_clientes where id = 1');
	$qtd = $qtd['qtd'] + 1;
	$db->query('update contador_clientes set qtd = "'.$qtd.'" where id = 1');

	//registra início do período de teste
	//$dt_cadastro = date('Y-m-d H:i:s');
	//$db->query('insert into clientes_trial(cliente_id,dt_cadastro) values('.$cliente_id.',"'.$dt_cadastro.'")');

	//registra sistema contratado pelo cliente
	$array_sistema = array("sistema_id"=>1,"cliente_id"=>$cliente_id);
	$db_w2b->query_insert('sistemas_clientes',$array_sistema);
	
	// ===== Conexão com o contador / cliente =====		
	if(isset($_POST['remetente_id']))
    {
		$remetente_id = $_POST['remetente_id'];
		$id_list = $_POST['id_list']; //id da lista de convites do remetente
		$token = $remetente_id.'.'.$id_list.'.'.$cliente_id;
	}

    $mensagem = array(
        "view"=>"_Cadastro.php",
        "usuarioId"=>$usuario_id,
        "token"=>$token,
        "email"=>$email);

    $mensagemHelper = new MensagemHelper();
    $mensagemHelper::EnviarEmail($email,"Web Finanças - Confirmação de cadastro",$mensagem);

    $arrResult = array ('situacao'=>1);
	echo json_encode($arrResult);
		
}else{
	$arrResult = array ('situacao'=>2);
	echo json_encode($arrResult);
} 
?>