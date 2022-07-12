<?php
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/php/Database.class.php";
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/servicos/mensagem/MensagemHelper.php";
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/php/Util.php";

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

function Verificar_Que_Usuario_Existe($db)
{
    $email = "fabio@web2business.com.br";

    $usuario = $db->fetch_assoc("select id from usuarios where email = '$email'");

    if($usuario)
        return true;
    else
        return false;
}

function Verificar_Que_Usuario_Nao_Existe($db)
{
    $email = "";

    $usuario = $db->fetch_assoc("select id from usuarios where email = '$email'");

    if($usuario)
        return false;
    else
        return true;
}


function cadastro($db,$db_w2b){

	$email = "fabiu.lm@gmail.com";
    
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
        //if($tp_cadastro['tp_cadastro']=='contador')
          //  $array_usuario['contador'] = 1; //registro que o usuário se cadastrou como contador
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
		if(isset($_POST['remetente_id'])){
            
            $remetente_id = $_POST['remetente_id'];
            $id_list = $_POST['id_list']; //id da lista de convites do remetente
            
            $token = $remetente_id.'.'.$id_list.'.'.$cliente_id;

		}
        
        $mensagemHelper = new MensagemHelper();
        $mensagemHelper::EnviarEmail($email,"Web Finanças - Confirmação de cadastro",$message);
        
        $arrResult = array ('situacao'=>1);
		echo json_encode($arrResult);
		
	}else{
		$arrResult = array ('situacao'=>2);
		echo json_encode($arrResult);
	} 

}

function Inserir_Usuario($db)
{
    $array_usuario['cliente_id'] = 1;
    $array_usuario['cliente_db_id'] = 0;
    $array_usuario['email'] = 'usuario_de_teste@webfinancas.com';
    $array_usuario['senha'] = md5("123456");
    $array_usuario['situacao'] = 0;
    $array_usuario['dt_cadastro'] = date('Y-m-d H:i:s');
    $array_usuario['financeiro'] = 1; //registro que o usuário se cadastrou para o sistema financeiro
    //if($tp_cadastro['tp_cadastro']=='contador')
    //  $array_usuario['contador'] = 1; //registro que o usuário se cadastrou como contador
    $array_usuario['grupo_id'] = 1;
    $usuario_id = $db->query_insert('usuarios',$array_usuario);

    if($usuario_id)
        return true;
    else
        return false;
}

function Enviar_Email_Para_Confirmar_Cadastro()
{
    $conteudo = array(
        "view"=>"_Cobranca.php",
        "usuarioId"=>1,
        "token"=>"ahf98724h",
        "email"=>"fabiu.lm@gmail.com");
        
    $act = MensagemHelper::EnviarEmail(
        "fabiu.lm@gmail.com",
        "Web Finanças - Confirmação de cadastro",
        $conteudo);

    if($act["status"])
        return true;
    else
        return false;
}

function ExecutarTeste($funcao,$db=null)
{
    $executar = call_user_func($funcao,$db);

    if(!$executar)
    {
        echo "$funcao: ERRO";
        break;
    }
    
    echo "$funcao: OK <br>";
}

//ExecutarTeste("Enviar_Email_Para_Confirmar_Cadastro");

//echo Enviar_Email();
//cadastro($db,$db_w2b);
//ExecutarTeste("Inserir_Usuario",$db);
//ExecutarTeste("Verificar_Que_Usuario_Existe",$db);
//ExecutarTeste("Verificar_Que_Usuario_Nao_Existe",$db);
?>