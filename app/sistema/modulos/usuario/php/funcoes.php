<?php
session_start();
require("../../../php/db_conexao_login.php");
require("../class/Usuario.class.php");
require("../class/Grupo.php");
require("../../../php/swiftMailer/lib/swift_required.php");

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'msg'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

switch($_REQUEST['funcao']){

    case 'CreateUsuario':
        $verificaPermissao = VerificaPermissaoUsuario(44);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $usuario = new Usuario($_REQUEST);
        echo $usuario->CreateUsuario($db);
        break;

    case 'EditUsuario':
        $verificaPermissao = VerificaPermissaoUsuario(45);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $usuario = new Usuario($_REQUEST);
        echo $usuario->EditUsuario($db);
        break;

    case 'DeleteUsuario':
        $verificaPermissao = VerificaPermissaoUsuario(46);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $usuario = new Usuario();
        echo $usuario->DeleteUsuario($db,$_REQUEST);
        break;

    case 'DetailsUsuario':
        $verificaPermissao = VerificaPermissaoUsuario(43);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $usuario = new Usuario();
        echo $usuario->DetailsUsuario($db,$_REQUEST);
        break;

    case 'DataTableUsuarios':
        $usuario = new Usuario();
        echo $usuario->DataTableUsuarios($db,$_REQUEST);
        break;

    case 'CreateGrupo':
        $verificaPermissao = VerificaPermissaoUsuario(44);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $grupo = new Grupo($_REQUEST);
        echo $grupo->CreateGrupo($db);
        break;

    case 'EditGrupo':
        $verificaPermissao = VerificaPermissaoUsuario(45);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $grupo = new Grupo($_REQUEST);
        echo $grupo->EditGrupo($db);
        break;

    case 'DeleteGrupo':
        $verificaPermissao = VerificaPermissaoUsuario(46);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $grupo = new Grupo();
        echo $grupo->DeleteGrupo($db,$_REQUEST);
        break;

    case 'DetailsGrupo':
        $verificaPermissao = VerificaPermissaoUsuario(43);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

        $grupo = new Grupo();
        echo $grupo->DetailsGrupo($db,$_REQUEST);
        break;

    case 'DataTableGrupos':
        $grupo = new Grupo();
        echo $grupo->DataTableGrupos($db,$_REQUEST);
        break;

    case 'GetModulos':
        $grupo = new Grupo();
        echo $grupo->GetModulos($db,$_REQUEST);
        break;

    case 'GetGrupos':
        $grupo = new Grupo();
        echo $grupo->GetGrupos($db);
        break;

    case "login":
		
        $usuario = new Usuario();
		
        $login = $usuario->login($db,$_REQUEST);
		
        if($login['situacao']==1){

            session_regenerate_id();
            
            
            $_SESSION['cli_acesso_situacao'] = $login['cli_acesso_situacao'];
			$_SESSION['permissao'] = $login['permissao'];
            $_SESSION['permissao_contador'] = $login['permissao_contador'];
			$_SESSION['usuario_id'] = $login['usuario_id'];
			$_SESSION['cliente_id'] = $login['cliente_id'];
			$_SESSION['email'] = $login['email'];
			$_SESSION['db_usuario'] = $login['cliente_db'];
			$_SESSION['db_senha'] = $login['cliente_db_senha'];
            $_SESSION['db_id'] = $login['cliente_db_id'];
            $_SESSION['financeiro_acesso'] = $login['financeiro'];
            $_SESSION['contador_acesso'] = $login['contador'];
            $_SESSION['carne_leao'] = $login['carne_leao'];
            $_SESSION['permissoes'] = $login['permissoes'];
            $_SESSION['logo_recibo'] = $login['logo_recibo'];
            
            $_SESSION['nome'] = $login['nome'];
            $_SESSION['cpf_cnpj'] = $login['cpf_cnpj'];

            $_SESSION['parceiro'] = $login['parceiro'];
            $_SESSION['parceiro_id'] = $login['parceiro_id'];
            $_SESSION['logo_parceiro'] = $login['logo_parceiro'];
            $_SESSION['logo_imagem'] = $login['logo_imagem'];
            
            $retorno = array("situacao"=>$login['situacao'],"notificacao"=>$login['notificacao'],"financeiro"=>$login['financeiro'],"contador"=>$login['contador']);

        }else{
            $retorno = array("situacao"=>$login['situacao'],"notificacao"=>$login['notificacao']);
        }
        //Caminho personalizado para sair
        $_SESSION['sair_caminho'] = $_SERVER['HTTP_REFERER'];
        
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

    case 'AlternarLicenca':
    
        try{
            
            $usuario = new Usuario();
            
            $usuario->AlternarLicenca($db,$_REQUEST['cliente_id']);

            echo json_encode(array('situacao'=>true,'notificacao'=>''));
                        
        }catch(Exception $e){
        
            echo json_encode(array('situacao'=>false,'notificacao'=>$e->getMessage()));

        }

        break;

	case "logoff":
		session_destroy();
	break;

	case "senhaAlterar":
		$usuario = new Usuario();
		$senhaAlterar = $usuario->senhaAlterar($db,$_REQUEST);
		$retorno = array("situacao"=>$senhaAlterar['situacao'],"notificacao"=>$senhaAlterar['notificacao']);
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "senhaRecuperar":
		$usuario = new Usuario();
		$senhaRecuperar = $usuario->senhaRecuperar($db,$_REQUEST);
		$db->close();
		$retorno = json_encode($senhaRecuperar);
		echo $retorno;
	break;
	
	case "usuariosEditar":
		$usuario = new Usuario();
		$usuariosEditar = $usuario->usuariosEditar($db,$_REQUEST);
		$db->close();
		$retorno = json_encode($usuariosEditar);
		echo $retorno;
	break;
	
	case "planoEditar":
		$usuario = new Usuario();
		$planoEditar = $usuario->planoEditar($db,$_REQUEST);
		$db->close();
		$retorno = json_encode($planoEditar);
		echo $retorno;
	break;
	
	case "planoContratar":
		$usuario = new Usuario();
		$chave = $usuario->planoContratar($_SESSION["cliente_id"],$_REQUEST); //id do cliente que contrata o sistema
		$retorno = array("situacao"=>1,"chave"=>$chave);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "faturaAtrasada":
		$usuario = new Usuario();
		$chave = $usuario->faturaAtrasada($_SESSION["cliente_id"]); //id do cliente que contrata o sistema
		$retorno = array("situacao"=>1,"chave"=>$chave);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "faturasGerar":
		$usuario = new Usuario();
		$usuario->faturasGerar();
	break;
	
	case "conviteContador":
		$usuario = new Usuario();
		$conviteContador = $usuario->conviteContador($db,$_REQUEST);
		$retorno = array("situacao"=>$conviteContador['situacao'],"notificacao"=>$conviteContador['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
    case "ContadorHabilitar":
        $usuario = new Usuario();
        $habilitar = $usuario->ContadorHabilitar($db,$_REQUEST);
        $retorno = array("situacao"=>$habilitar['situacao'],"notificacao"=>$habilitar['notificacao']);
        $retorno = json_encode($retorno);
        echo $retorno;
        break;
    
    case "CarneLeaoHabilitar":
        $usuario = new Usuario();
        $habilitar = $usuario->CarneLeaoHabilitar($db,$_REQUEST);
        $retorno = array("situacao"=>$habilitar['situacao'],"notificacao"=>$habilitar['notificacao']);
        $retorno = json_encode($retorno);
        echo $retorno;
        break;
    
    case "CancelarContratacao":
		$usuario = new Usuario();
        echo json_encode($usuario->CancelarContratacao());
        break;
        
    case "logoRecibo":
		$usuario = new Usuario();
        $logorecibo = $usuario->logoRecibo($_REQUEST);
        $filename = '../../../'.$_SESSION['logo_recibo'];
        if (file_exists($filename)) {
            unlink($filename);
        }
        $_SESSION['logo_recibo'] = $_REQUEST['arquivo'];
        break;

    //somente para teste
    case 'ConciliarFatura':
        Usuario::ConciliarFatura();
        break;
    
}

?>