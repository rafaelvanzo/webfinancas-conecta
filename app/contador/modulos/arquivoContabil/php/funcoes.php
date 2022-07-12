<?php
session_start();
require("../../../php/db_conexao.php");
require("../class/PlanoContas.class.php");
require("../class/ArquivoContabil.class.php");
require("../../../php/swiftMailer/lib/swift_required.php");
require("../../../../sistema/modulos/contador/class/Contador.class.php");

switch($_REQUEST['funcao']){

	
	case "salvarPlConfig":
		$arquivoContabil = new ArquivoContabil();
		$salvarPlConfig = $arquivoContabil-> salvarPlConfig($db,$_REQUEST);
		$retorno = array("situacao"=>$salvarPlConfig['situacao'],"notificacao"=>$salvarPlConfig['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "salvarPlConfigContas":
		$arquivoContabil = new ArquivoContabil();
		$salvarPlConfig = $arquivoContabil-> salvarPlConfigContas($db,$_REQUEST);
		$retorno = array("situacao"=>$salvarPlConfig['situacao'],"notificacao"=>$salvarPlConfig['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "salvarPlConfigPlano":
		$arquivoContabil = new ArquivoContabil();
		$salvarPlConfig = $arquivoContabil-> salvarPlConfigPlano($db,$_REQUEST);
		$retorno = array("situacao"=>$salvarPlConfig['situacao'],"notificacao"=>$salvarPlConfig['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	case "salvarPlConfigFavorecido":
		$arquivoContabil = new ArquivoContabil();
		$salvarPlConfig = $arquivoContabil-> salvarPlConfigFavorecido($db,$_REQUEST);
		$retorno = array("situacao"=>$salvarPlConfig['situacao'],"notificacao"=>$salvarPlConfig['notificacao']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "visualizarLancamentos":
		$arquivoContabil = new ArquivoContabil();
		$visualizarLancamentos = $arquivoContabil-> visualizarLancamentos($db,$_REQUEST);
		$retorno = array("situacao"=>$visualizarLancamentos['situacao'],"palco"=>$visualizarLancamentos['palco']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "gerarArquivoContabil":
		$arquivoContabil = new ArquivoContabil();
		$gerarArquivoContabil = $arquivoContabil-> gerarArquivoContabil($db,$_REQUEST);
		$retorno = array("situacao"=>$gerarArquivoContabil['situacao'],"notificacao"=>$gerarArquivoContabil['notificacao'],"download"=>$gerarArquivoContabil['download']);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	case "excluirPastasZip":
		$arquivoContabil = new ArquivoContabil();
		$excluirPastasZip = $arquivoContabil-> excluirPastasZip();
	break;
	
	case 'DocumentosDownload':
		$arquivoContabil = new ArquivoContabil();
		$documentos = $arquivoContabil->DocumentosDownload($_REQUEST);
		$retorno = json_encode(array('situacao'=>1,'notificacao'=>'','documentos'=>$documentos));
		echo $retorno;
	break;
	
	case "RemessaHistorico":
		$contador = new ArquivoContabil();
		$contador->RemessaHistorico($_REQUEST);
	break;

    case "CarregarPlanoContas":
		$contador = new ArquivoContabil();
		$contador->CarregarPlanoContas($_REQUEST,$dadosDbWf);
        $planoContas = $contador->ListarPlanoContas($_REQUEST,$db,$dadosDbWf);
        echo json_encode(array('situacao'=>1,'notificacao'=>'Plano de contas carregado','planoContas'=>$planoContas['planoContas'],'totalCategorias'=>$planoContas['totalCategorias']));
        break;

    case "planoContasIncluir":
        //Conex�o no banco do Web Finan�as
	    $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
        //Conex�o com o db do cliente
        $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_REQUEST['clienteId']);
        $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
        $dbWf->close();

		$planoContas = new planoContas($_REQUEST);
		$incluir = $planoContas->planoContasIncluir($dbCliente);
        $dbCliente->close();
		if($incluir[situacao]==1){
			$arquivoContabil = new ArquivoContabil();
            $planoContas_listar = $arquivoContabil->ListarPlanoContas($_REQUEST,$db,$dadosDbWf);
			$retorno = array("planoContas" => $planoContas_listar,"situacao"=>$incluir[situacao],"notificacao"=>$incluir[notificacao]);
		}else{
			$retorno = array("situacao"=>$incluir[situacao],"notificacao"=>$incluir[notificacao]);
		}
		$retorno = json_encode($retorno);
		echo $retorno;
        break;

	case "planoContasEditar":
        //Conex�o no banco do Web Finan�as
	    $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
        //Conex�o com o db do cliente
        $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_REQUEST['clienteId']);
        $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
        $dbWf->close();

		$dbCliente->query('start transaction');
		$planoContas = new planoContas($_REQUEST);
		$editar = $planoContas->planoContasEditar($dbCliente,$_REQUEST);
		$dbCliente->query('commit');
        $dbCliente->close();
		if($editar[situacao]==1){
			$arquivoContabil = new ArquivoContabil();
            $planoContas_listar = $arquivoContabil->ListarPlanoContas($_REQUEST,$db,$dadosDbWf);
			$retorno = array("planoContas" => $planoContas_listar,"situacao"=>$editar[situacao],"notificacao"=>$editar[notificacao]);
		}else{
			$retorno = array("situacao"=>$editar[situacao],"notificacao"=>$editar[notificacao]);
		}
		$retorno = json_encode($retorno);
		echo $retorno;
        break;

	case "planoContasExcluir":
        //Conex�o no banco do Web Finan�as
	    $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
        //Conex�o com o db do cliente
        $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_REQUEST['clienteId']);
        $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
        $dbWf->close();

		$dbCliente->query('start transaction');
		$planoContas = new planoContas();
		$excluir = $planoContas->planoContasExcluir($dbCliente,$_REQUEST[planoContas_id]);
		$dbCliente->query('commit');
        $dbCliente->close();
        if($excluir['situacao']==1){
            $arquivoContabil = new ArquivoContabil();
            $planoContas_listar = $arquivoContabil->ListarPlanoContas($_REQUEST,$db,$dadosDbWf);
            $retorno = array("planoContas" => $planoContas_listar,"notificacao"=>$excluir[notificacao],"situacao"=>$excluir[situacao]);
        }else{
            $retorno = array("notificacao"=>$excluir[notificacao],"situacao"=>$excluir[situacao]); 
        }
		
		$retorno = json_encode($retorno);
		echo $retorno;
        break;
	
	case "planoContasExibir":
        //Conex�o no banco do Web Finan�as
	    $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
        //Conex�o com o db do cliente
        $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_REQUEST['clienteId']);
        $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
        $dbWf->close();

		$planoContas = new planoContas();
		$retorno = $planoContas->planoContasExibir($dbCliente,$_REQUEST[planoContas_id]);
		$retorno = json_encode($retorno);
		echo $retorno;
        break;

	case "BloquearLiberarLancamentos":
	
		 //Conex�o no banco do Web Finan�as
		 $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
		 //Conex�o com o db do cliente
		 $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_REQUEST['clienteId']);
		 $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
		 $dbWf->close();
 
		 $dbCliente->query('start transaction');
		 $contador = new ArquivoContabil();
		 $retorno = $contador->BloquearLiberarLancamentos($dbCliente, $_REQUEST);
		 $dbCliente->query('commit');
		 $dbCliente->close();

		 $retorno = json_encode($retorno);
		 echo $retorno;
		 break;

	case "BlqLiberarLancExibir":
	
		 //Conex�o no banco do Web Finan�as
		 $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);
		 //Conex�o com o db do cliente
		 $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_REQUEST['clienteId']);
		 $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
		 $dbWf->close();
 
		 $dbCliente->query('start transaction');
		 $contador = new ArquivoContabil();
		 $retorno = $contador->BlqLiberarLancExibir($dbCliente, $_REQUEST);
		 $dbCliente->query('commit');
		 $dbCliente->close();

		 $retorno = json_encode($retorno);
		 echo $retorno;
		 break;

	}

?>