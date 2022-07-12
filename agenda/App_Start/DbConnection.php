<?php
/* inicio: Verifica a versão do PHP e atribui a classe Database para a versão compatível */
$PHPVersion = phpversion();
$DataBase = ($PHPVersion < 7)? "Database.class.5.php" : "Database.class.7.php";
require_once($DataBase);
/* fim: Verifica a versão do PHP e atribui a classe Database para a versão compatível */

/**
 * DbConnection short summary.
 *
 * DbConnection description.
 *
 * @version 1.0
 * @author Fabio
 */

class DbConnection
{
    public static function GetConnection()
    {
        $dadosConexao = ConfigService::GetConfiguracoes();
        $dadosConexao = $dadosConexao["DbConnection"]["DbProducao"];

        $host = $dadosConexao["Host"];
        $usuario = $dadosConexao["Usuario"];
        $senha = $dadosConexao["Senha"];
        $db_usuario = $dadosConexao["Db"];
                
        return new Database($host,$usuario,$senha,$db_usuario);
    }

    public static function GetDinamicConnection(){

        //session_start();

        $host = $_SESSION["Host"];
        $usuario = $_SESSION["dbUsuario"];
        $senha = 'W2BSISTEMAS';
        $db_usuario = $_SESSION["Db"];

        return new Database($host,$usuario,$senha,$db_usuario);

    }

    public static function GetDinamicConnectionW2B(){

        $dadosConexao = ConfigService::GetConfiguracoes();
        $dadosConexao = $dadosConexao["DbConnection"]["DbW2B"];

        $host = $dadosConexao["Host"];
        $usuario = $dadosConexao["Usuario"];
        $senha = $dadosConexao["Senha"];
        $db_usuario = $dadosConexao["Db"];

        return new Database($host,$usuario,$senha,$db_usuario);

    }

}

?>