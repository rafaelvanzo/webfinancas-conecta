<?php
require("conexao_db.php");


$Registros = $db->fetch_all_array('SELECT id, tipo, descricao, favorecido_id, conta_id, valor, DATE_FORMAT(dt_emissao, "%d/%m/%Y") as dt_emissao, DATE_FORMAT(dt_vencimento, "%d/%m/%Y") as dt_vencimento, DATE_FORMAT(dt_compensacao, "%d/%m/%Y") as dt_compensacao, compensado FROM lancamentos');

foreach($Registros as $Registros){

	$PlanoContas = $db->fetch_assoc('SELECT plano_contas_id, centro_resp_id, valor FROM ctr_plc_lancamentos WHERE lancamento_id ='.$Registros['id']);

	$Resultado .= '<tr><td>'.$Registros['id'].'</td><td align="center">'.$Registros['tipo'].'</td><td>'.utf8_decode($Registros['descricao']).'</td><td align="center">'.$Registros['favorecido_id'].'</td><td align="center">'.$Registros['conta_id'].'</td><td align="right">'.number_format($Registros['valor'], 2, ',', '.').'</td><td>'.$Registros['dt_emissao'].'</td><td>'.$Registros['dt_vencimento'].'</td><td>'.$Registros['dt_compensacao'].'</td><td align="center">'.$Registros['compensado'].'</td><td align="center">'.$PlanoContas['plano_contas_id'].'</td><td align="center">'.$PlanoContas['centro_resp_id'].'</td><td align="center">'.$PlanoContas['valor'].'</td></tr>';

}

$Cabecalho = '<tr><td><b>Id</b></td><td width="150" align="center"><b>Tipo </b><br><font style="font-size:9px;">(R = Recebimento e P = Pagametno)</font></td><td><b>Descricao</b></td><td width="100"><b>favorecido id</b></td><td width="100"><b>Conta id</b></td><td><b>valor</b></td><td width="120"><b>Emissão</b></td><td width="120"><b>Vencimento</b></td><td width="120"><b>Data da compensação</b></td><td width="100"><b>Compensado </b><br><font style="font-size:9px;">(1 = Sim e 0 = Não)</font></td><td width="250"><b>Plano de contas id</b></td><td width="250"><b>Centro de custo id</b></td><td width="300"><b>Valor PL e CC</b><br><font style="font-size:9px;">(Plano de contas e Centro de custo)</font></td></tr>';
echo '<table width="100%">'.$Cabecalho.$Resultado.'</table>';

?>