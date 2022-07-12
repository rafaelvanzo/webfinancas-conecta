<?php
header("Content-Type: text/html; charset=UTF-8");

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
define('ROOT_MODULOS',$_SERVER['DOCUMENT_ROOT'].'/sistema/modulos/');
//define('CHAVE_GUID', 'BA48C023-2173-4B9F-90CA-75B0A7D64B1E');

require_once(ROOT.'sistema/php/db_conexao_login.php');
require_once(ROOT.'lexdata/Util/Mascara.php');
require_once(ROOT_MODULOS.'favorecido/class/Favorecido.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Lancamento.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Recebimento.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Pagamento.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Transferencia.class.php');
require_once(ROOT.'lexdata/Controllers/RemessaLexdataController.php');
require_once(ROOT.'lexdata/Controllers/HonorarioLexdataController.php');
require_once(ROOT.'lexdata/Database/DbMSSql.php');

//HonorarioLexdataController::Create();

//HonorarioLexdataController::Edit();

/*
 * futuramente replicar o código dentro das classes do sistema para outras contabilidades utilizarem
 * */

?>