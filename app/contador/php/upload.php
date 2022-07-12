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

// Get parameters
$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

// Clean the fileName for security reasons
//$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

$fileName = UtilController::retirar_acento($fileName);

$fileName = preg_replace('/[^\w\.]+/', '_', $fileName);

$fileName = $_POST['cliente_id'] . '_' . $fileName;

// Make sure the fileName is unique but only if chunking is disabled
if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
	$ext = strrpos($fileName, '.');
	$fileName_a = substr($fileName, 0, $ext);
	$fileName_b = substr($fileName, $ext);

	$count = 1;
	while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
		$count++;

	$fileName = $fileName_a . '_' . $count . $fileName_b;

}

$nome_arquivo = $fileName;
$underline = strpos($fileName, '_')+1;
$nome_arquivo_org = substr($fileName, $underline);

$tp_documento_id = $_POST['tp_documento_id'];
$classificacao_id = $_POST['classificacao_id'];
$dt_competencia = $_POST['dt_competencia'];
$dt_visualizacao = $_POST['dt_visualizacao'];

// Create target dir
if (!file_exists($targetDir))
	@mkdir($targetDir);

// Remove old temp files
/* this doesn't really work by now
	
if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
	while (($file = readdir($dir)) !== false) {
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// Remove temp files if they are older than the max age
		if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
			@unlink($filePath);
	}

	closedir($dir);
} else
	die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
*/

// Look for the content type header
if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
	$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

if (isset($_SERVER["CONTENT_TYPE"]))
	$contentType = $_SERVER["CONTENT_TYPE"];

// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
if (strpos($contentType, "multipart") !== false) {
	if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
		// Open temp file
		$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen($_FILES['file']['tmp_name'], "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);

					if (($chunks - 1) == $chunk){
						$array_insert = array("tp_documento_id"=>$tp_documento_id,"classificacao_id"=>$classificacao_id,"nome_arquivo"=>$nome_arquivo,"nome_arquivo_org"=>$nome_arquivo_org,"dt_cadastro"=>date('Y-m-d'), "dt_competencia"=>$dbCliente->data_to_sql($dt_competencia), "dt_visualizacao"=>$dbCliente->data_to_sql($dt_visualizacao));
						$dbCliente->query_insert('lnct_anexos',$array_insert);
					}
					
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			fclose($in);
			fclose($out);
			@unlink($_FILES['file']['tmp_name']);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
} else {
	// Open temp file
	$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
	if ($out) {
		// Read binary input stream and append it to temp file
		$in = fopen("php://input", "rb");

		if ($in) {
			while ($buff = fread($in, 4096))
				fwrite($out, $buff);

				if (($chunks - 1) == $chunk){
					$array_insert = array("tp_documento_id"=>$tp_documento_id,"classificacao_id"=>$classificacao_id,"nome_arquivo"=>$nome_arquivo,"nome_arquivo_org"=>$nome_arquivo_org,"dt_cadastro"=>date('Y-m-d'), "dt_competencia"=>$dbCliente->data_to_sql($dt_competencia), "dt_visualizacao"=>$dbCliente->data_to_sql($dt_visualizacao));
					$dbCliente->query_insert('lnct_anexos',$array_insert);
				}

		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

		fclose($in);
		fclose($out);
	} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}


// Return JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

?>
<br>
