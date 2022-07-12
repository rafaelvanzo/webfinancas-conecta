<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/Clientes.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");

switch($_REQUEST['funcao']){
    
	case "DataTableAjax":
        $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");  
		$retorno = Clientes::DataTableAjax($db_w2b,$_REQUEST);
		$db->close();
        $db_w2b->close();
		echo $retorno;
    break;
    
    case "add":
        try{
            $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");  
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_w2b->query('start transaction');

            $db_wf->query('start transaction');
            
            $clientes = new Clientes();
            $registro = $clientes->add($db, $db_wf, $db_w2b, $_REQUEST);
            $retorno = array("situacao"=>$registro['situacao'],"notificacao"=>$registro['notificacao']);
            
            $db_w2b->query('commit');

            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
            $db_w2b->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        $db_w2b->close();
        echo json_encode($retorno); 
    break;

    case "visualizar":
        try{
            
            $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");  
            
            $db_w2b->query('start transaction');
            
            $clientes = new Clientes();
            $registro = $clientes->visualizar($db_w2b, $_REQUEST);
            $retorno = array("situacao"=>1, "nome"=>$registro['nome'], "inscricao"=>$registro['inscricao'], "cpf_cnpj"=>$registro['cpf_cnpj'], "logradouro"=>$registro['logradouro'], "numero"=>$registro['numero'], "complemento"=>$registro['complemento'], "bairro"=>$registro['bairro'], "cidade"=>$registro['cidade'], "uf"=>$registro['uf'], "cep"=>$registro['cep'], "telefone"=>$registro['telefone'], "celular"=>$registro['celular'], "email"=>$registro['email'], "email_fin"=>$registro['email_fin'], "observacao"=>$registro['observacao'], "agenda"=>$registro['agenda']);
            
            $db_w2b->query('commit');
        }
        catch(Exception $e){
			$db_w2b->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_w2b->close();
        echo json_encode($retorno); 
    break;
    
    case "editar":
        try{
            $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business"); 
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $clientes = new Clientes();
            $registro = $clientes->editar($db_w2b, $db_wf, $_REQUEST);
            $retorno = array("situacao"=>$registro['situacao'], "notificacao"=>$registro['notificacao']);
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        $db_w2b->close();
        echo json_encode($retorno); 
    break;
    
    case "excluir":
        try{
            $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
            
            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $clientes = new Clientes();
            $registro = $clientes->excluir($db_w2b, $db_wf, $db, $_REQUEST);
            $retorno = array("situacao"=>1, "notificacao"=>"Cliente removido com sucesso.");
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        $db_wf->close();
        $db_w2b->close();
        echo json_encode($retorno); 
   break;

    case "alterarSenha":
        try{

            $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");

            $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
            
            $db_wf->query('start transaction');
            
            $clientes = new Clientes();
            $alterarSenha = $clientes->alterarSenha($db_w2b, $db_wf, $_REQUEST);
            $retorno = array("situacao"=>$alterarSenha['situacao'], "notificacao"=>$alterarSenha['notificacao']);
            
            $db_wf->query('commit');
        }
        catch(Exception $e){
			$db_wf->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
        
        $db_wf->close();
        $db_w2b->close();
        echo json_encode($retorno); 
        break;

    case 'getUsuarios':
        $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['usuario']);
        $usuarios = Clientes::getUsuarios($dbWf,$_REQUEST);
        $dbWf->close();
        echo json_encode($usuarios);
        break;

    case "login":
        $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
        $dbWf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
        $usuario = new Clientes();            
        $login = $usuario->loginCliente($db_w2b, $dbWf,$_REQUEST);    
        echo json_encode($login);
        break;   


}


?>