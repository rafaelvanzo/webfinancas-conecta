<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Contador.class.php");
require("../class/ContadorMensagens.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){

	case "conviteContador":
		$contador = new Contador();
		$conviteContador = $contador-> conviteContador($db,$_REQUEST);
		$retorno = array("situacao"=>$conviteContador['situacao'],"notificacao"=>$conviteContador['notificacao'],"lista_conexoes"=>$conviteContador['lista_conexoes']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "reenviarConvite":
		$contador = new Contador();
		$reenviarConvite = $contador-> reenviarConvite($db,$_REQUEST);
		$retorno = array("situacao"=>$reenviarConvite['situacao'],"notificacao"=>$reenviarConvite['notificacao'],"lista_conexoes"=>$reenviarConvite['lista_conexoes']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "cancelarConexoes":
		$contador = new Contador();
		$cancelarConexoes = $contador-> cancelarConexoes($db,$_REQUEST);
		$retorno = array("situacao"=>$cancelarConexoes['situacao'],"notificacao"=>$cancelarConexoes['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "cancelarConexoesAtivas":
		$contador = new Contador();
		$cancelarConexoesAtivas = $contador-> cancelarConexoesAtivas($db,$_REQUEST);
		$retorno = array("situacao"=>$cancelarConexoesAtivas['situacao'],"notificacao"=>$cancelarConexoesAtivas['notificacao'],"conexao_contador"=>$cancelarConexoesAtivas['conexao_contador']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "aceitarConexoes":
		$contador = new Contador();
		$aceitarConexoes = $contador-> aceitarConexoes($db,$_REQUEST);
		$retorno = array("situacao"=>$aceitarConexoes['situacao'],"notificacao"=>$aceitarConexoes['notificacao'],"lista_conexoes"=>$aceitarConexoes['lista_conexoes'],"contador_info"=>$aceitarConexoes['contador_info']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
/*	
	case "addConversa":
		$contador = new Contador();
		$addConversa = $contador-> addConversa($db,$_REQUEST);
		$retorno = array("atualizarConversa"=>$addConversa['atualizarConversa'],"chat_id"=>$addConversa['chat_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "addMensagem":
		$contador = new Contador();
		$addMensagem = $contador-> addMensagem($db,$_REQUEST);
		$retorno = array("atualizarConversa"=>$addMensagem['atualizarConversa'],"chat_id"=>$addMensagem['chat_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "visualizarMensagens":
		$contador = new Contador();
		$visualizarMensagens = $contador-> visualizarMensagens($db,$_REQUEST);
		$retorno = array("atualizarConversa"=>$visualizarMensagens['atualizarConversa'],"chat_id"=>$visualizarMensagens['chat_id']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
*/
	case "cfListar":
		$contador = new Contador();
		$cf = $contador->cfListar($db,$mes,$ano);
		echo $cf;
	break;

	case "RemessaContabil":
		$contador = new Contador();
		$contador->RemessaContabil($db,$_REQUEST);
		$cf_listar = $contador->cfListar($db,$_REQUEST['mes'],$_REQUEST['ano']);
		$retorno = array('situacao'=>1,'notificacao'=>'Remessa contábil enviada com sucesso','contas'=>$cf_listar);
		echo json_encode($retorno);
	break;
	
	case "RemessaPesquisar":
		$contador = new Contador();
		$cf_listar = $contador->cfListar($db,$_REQUEST['mes'],$_REQUEST['ano']);
		echo json_encode(array('contas'=>$cf_listar));
	break;
	
	case "RemessaHistorico":
		$contador = new Contador();
		$contador->RemessaHistorico($db,$_REQUEST);
	break;
   
	case "DataTableAjax":
        $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");  
		$retorno = ContadorMensagens::DataTableAjax($db,$db_wf,$_REQUEST);
		$db->close();
        $db_wf->close();
		echo $retorno;
    break;
    
    case "addMensagem":
        try{
			$db->query('start transaction');
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $contadorMensagens = new ContadorMensagens();
            $addMensagem = $contadorMensagens->addMensagem($db, $db_wf, $_REQUEST);
            $retorno = array("situacao"=>1,"notificacao"=>"Solicitação enviada com sucesso.");
        }
        catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        echo json_encode($retorno); 
    break;

	case "abrirMensagem":
         try{                       
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
        $contadorMensagens = new ContadorMensagens();
		$abrirMsg = $contadorMensagens->abrirMensagem($db, $db_wf, $_REQUEST);
		$retorno = array('situacao'=>1, 'msg'=>$abrirMsg['msg'], 'detalhes_solicitacao'=>$abrirMsg['detalhes_solicitacao'], 'titulo'=>$abrirMsg['titulo'], 'abas'=>$abrirMsg['abas'], 'situacaoChat'=>$abrirMsg['situacaoChat'], 'dt_conclusao'=>$abrirMsg['dt_conclusao'], );
            
            $db_wf->query('commit');
         }
         catch(Exception $e){
             $db_wf->query('rollback');
             $retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
         }
         $db_wf->close();
        echo json_encode($retorno);
   break;
   
    case "visualizarMensagem":
        try{                       
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $contadorMensagens = new ContadorMensagens();
            $vMsg = $contadorMensagens->visualizarMensagem($db_wf, $_REQUEST, '1');
            $retorno = array('situacao'=>1, 'msg'=>$vMsg['msg'], 'novaMensagem'=>$vMsg['novaMensagem']);
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
            $db_wf->query('rollback');
            $retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
        }
        $db_wf->close();
        echo json_encode($retorno);        
    break;
   
    case "enviarMensagem":
        try{                       
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $contadorMensagens = new ContadorMensagens();
            $enviarMsg = $contadorMensagens->enviarMensagem($db_wf, $_REQUEST);
            $retorno = array('situacao'=>1, 'msg'=>$enviarMsg['msg']);
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
            $db_wf->query('rollback');
            $retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
        }
        $db_wf->close();
        echo json_encode($retorno);       
    break;

}


?>