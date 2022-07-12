<?php
session_start();

require_once('db_conexao.php');

$ctrId = $_POST['ctr_id'];
$plcId = $_POST['plc_id'];

$ctr = $db->fetch_assoc('select nome from centro_resp where id = '.$ctrId);
$plc = $db->fetch_assoc('select nome from plano_contas where id = '.$plcId);

echo json_encode(array('ctr'=>$ctr['nome'],'plc'=>$plc['nome']));

?>
