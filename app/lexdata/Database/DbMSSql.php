<?
/**
 * Summary of DbMSSql
 */
class DbMSSql{

    // Dados do banco
    private $dbhost;
    private $db;
    private $user;
    private $password;
    private $linkId;

    /**
     * Summary of __construct
     */
    function __construct(){
        $this->dbhost   = "www.lexdata.com.br";
        $this->db       = "App_boletos";
        $this->user     = "lex_boletos";
        $this->password = "lex_boletos";
    }
    
    /**
     * Summary of Open
     */
    function Open(){
        $this->linkId = mssql_connect($this->dbhost,$this->user,$this->password) or die("Não foi possível a conexão com o servidor!");
        mssql_select_db($this->db) or die("Não foi possível selecionar o banco de dados!");
    }

    /**
     * Summary of Close
     */
    function Close(){
        mssql_close($this->linkId);
    }

    function GetLinkId(){
        return $this->linkId;
    }

    /**
     * Summary of Teste
     */
    function Teste(){
    
        $instrucaoSQL = "SELECT top 1 * FROM Boletos where Numero like '%306876%' order by id desc";
        $consulta = mssql_query($instrucaoSQL, $this->linkId);
        $numRegistros = mssql_num_rows($consulta);
        
        echo "Esta tabela contém $numRegistros registros!\n<hr>\n";
        
        if ($numRegistros!=0) {
            while ($cadaLinha = mssql_fetch_assoc($consulta)) {
                foreach($cadaLinha as $key => $value){
                    echo "$key: $value <br><br>";
                }
                    
            }
        }
    }
}
?>