<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/ContadorMensagens.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){
    
	case "DataTableAjax":
        $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
        $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");  
		$retorno = ContadorMensagens::DataTableAjax($db, $db_wf, $db_w2b, $_REQUEST);
		$db->close();
        $db_wf->close();
        $db_w2b->close();
		echo $retorno;
    break;
    
    case "addMensagem":
        try{
			$db_wf->query('start transaction');
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $contadorMensagens = new ContadorMensagens();
            $addMensagem = $contadorMensagens->addMensagem($db, $db_wf, $_REQUEST);
            $retorno = array("situacao"=>1,"notificacao"=>"Solicitação enviada com sucesso.");
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
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
		$abrirMsg = $contadorMensagens->abrirMensagem( $db_wf, $_REQUEST);
		$retorno = array('situacao'=>1, 'msg'=>$abrirMsg['msg'], 'detalhes_solicitacao'=>$abrirMsg['detalhes_solicitacao'], 'titulo'=>$abrirMsg['titulo'], 'abas'=>$abrirMsg['abas'], 'situacaoChat'=>$abrirMsg['situacaoChat'], 'dt_conclusao'=>$abrirMsg['dt_conclusao']);
            
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
    
    case "concluirSolicitacao":
        try{                       
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $contadorMensagens = new ContadorMensagens();
            $concluir = $contadorMensagens->concluirSolicitacao($db_wf, $_REQUEST);
            $retorno = array('situacao'=>1, 'notificacao'=>'Solicitação concluída com sucesso.');
            
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