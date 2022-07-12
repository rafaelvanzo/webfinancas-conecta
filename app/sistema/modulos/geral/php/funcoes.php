<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Geral.class.php");

switch($_REQUEST['funcao']){
	
    case "visualizarInfo":
        try{
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $informativo = new geral();
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
	
}
?>
