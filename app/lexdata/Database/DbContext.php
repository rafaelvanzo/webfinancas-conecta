<?
// Dados do banco
$dbhost   = "www.lexdata.com.br";   #Nome do host
$db       = "lex_boletos";   #Nome do banco de dados
$user     = "lex_boletos"; #Nome do usu�rio
$password = "lex_boletos";   #Senha do usu�rio

@mssql_connect($dbhost,$user,$password) or die("N�o foi poss�vel a conex�o com o servidor!");
@mssql_select_db("$db") or die("N�o foi poss�vel selecionar o banco de dados!");
 
$instrucaoSQL = "SELECT * FROM Boletos";
$consulta = mssql_query($instrucaoSQL);
$numRegistros = mssql_num_rows($consulta);
 
echo "Esta tabela cont�m $numRegistros registros!\n<hr>\n";
 
if ($numRegistros!=0) {
	while ($cadaLinha = mssql_fetch_array($consulta)) {
		echo $cadaLinha['Numero'];
	}
}
?>