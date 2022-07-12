<?php
session_start();
/**
 * upload.php
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// HTTP headers for no cache etc
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Settings
//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
$targetDir = $_SERVER['DOCUMENT_ROOT'].'/sistema/uploads/cliente_'.$_POST['cliente_id'];
//require('db_conexao.php');
require_once('Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/contador/Controllers/UtilController.php');

//Conexão no banco da Web 2 Business
$db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
$clienteDb = $db_wf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$_POST['cliente_id']);
$db_wf->close();

//Fazer conexão dinamicamente com o banco de dados do cliente
$dbCliente = new Database('mysql.webfinancas.com',$clienteDb['db'],$clienteDb['db_senha'],$clienteDb['db']);

//$cleanupTargetDir = false; // Remove old files
//$maxFileAge = 60 * 60; // Temp file age in seconds

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);


// Clean the fileName for security reasons
//$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

$fileName = UtilController::retirar_acento($fileName);

$fileName = preg_replace('/[^\w\.]+/', '_', $fileName);

$fileName = $_POST['cliente_id'] . '_' . $fileName;


$nome_arquivo = $fileName;
$underline = strpos($fileName, '_')+1;
$nome_arquivo_org = substr($fileName, $underline);

$tp_documento_id = $_POST['tp_documento_id'];
$classificacao_id = $_POST['classificacao_id'];
$dt_competencia = $_POST['dt_competencia'];
$dt_visualizacao = $_POST['dt_visualizacao'];

						$array_insert = array("tp_documento_id"=>$tp_documento_id,"classificacao_id"=>$classificacao_id,"nome_arquivo"=>$nome_arquivo,"nome_arquivo_org"=>$nome_arquivo_org,"dt_cadastro"=>date('Y-m-d'), "dt_competencia"=>$dbCliente->data_to_sql($dt_competencia), "dt_visualizacao"=>$dbCliente->data_to_sql($dt_visualizacao));
						$dbCliente->query_insert('lnct_anexos',$array_insert);


?>
<br>
