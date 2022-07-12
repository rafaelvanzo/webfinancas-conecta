<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Informativo.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){
    
	case "DataTableAjax":
        $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");  
		$retorno = Informativo::DataTableAjax($db_wf,$_REQUEST);
		$db->close();
        $db_wf->close();
		echo $retorno;
    break;
    
    case "addInfo":
        try{
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $informativo = new Informativo();
            $registro = $informativo->addInfo($db_wf, $_REQUEST);
            $retorno = array("situacao"=>1,"notificacao"=>"Informativo salvo com sucesso.");
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        echo json_encode($retorno); 
    break;

    case "visualizarInfo":
        try{
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $informativo = new Informativo();
            $registro = $informativo->visualizarInfo($db_wf, $_REQUEST);
            $retorno = array("situacao"=>1, "titulo"=>$registro['titulo'], "dt_inicio"=>$registro['dt_inicio'], "dt_final"=>$registro['dt_final'], "situacao"=>$registro['situacao'], "descricao"=>$registro['descricao']);
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        echo json_encode($retorno); 
    break;
    
    case "editarInfo":
        try{
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $informativo = new Informativo();
            $registro = $informativo->editarInfo($db_wf, $_REQUEST);
            $retorno = array("situacao"=>1, "notificacao"=>"Informativo salvo com sucesso.");
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        echo json_encode($retorno); 
    break;
    
    case "excluirInfo":
        try{
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $informativo = new Informativo();
            $registro = $informativo->excluirInfo($db_wf, $_REQUEST);
            $retorno = array("situacao"=>1, "notificacao"=>"Registro excluído com sucesso.");
            
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