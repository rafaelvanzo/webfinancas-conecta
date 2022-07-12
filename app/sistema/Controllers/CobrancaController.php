<?php
require_once "$_SERVER[DOCUMENT_ROOT]/sistema/servicos/configuracao/ConfiguracaoHelper.php";

/**
 * CobrancaController short summary.
 *
 * CobrancaController description.
 *
 * @version 1.0
 * @author Fabio
 */
class CobrancaController
{
    public $dbW2b;
    public $dbWf;
    public $dbVmSistemas;
    public $configuracaoHelper;
    
    /**
     * Summary of __construct
     */
    function __construct()
    {
        $this->configuracaoHelper = new ConfiguracaoHelper();
        //self::ConectarDbW2b();
        //self::ConectarDbWf();
        //self::ConectarDbVmSistemas();
    }

    /**
     * Summary of ConectarDbW2b
     * @return Database
     */
    function ConectarDbW2b()
    {
         return $this->dbW2b = new Database(
            $this->configuracaoHelper->configuracoes["DbConexao"]["W2b"]["host"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["W2b"]["usuario"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["W2b"]["senha"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["W2b"]["db"]);
    }

    /**
     * Summary of ConectarDbWf
     * @return Database
     */
    function ConectarDbWf()
    {
        return $this->dbWf = new Database(
            $this->configuracaoHelper->configuracoes["DbConexao"]["Wf"]["host"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["Wf"]["usuario"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["Wf"]["senha"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["Wf"]["db"]);
    }

    /**
     * Summary of ConectarDbVmSistemas
     * @return Database
     */
    function ConectarDbVmSistemas()
    {
        return $this->dbVmSistemas = new Database(
            $this->configuracaoHelper->configuracoes["DbConexao"]["WfVmSistemas"]["host"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["WfVmSistemas"]["usuario"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["WfVmSistemas"]["senha"],
            $this->configuracaoHelper->configuracoes["DbConexao"]["WfVmSistemas"]["db"]);
    }

    /**
     * Summary of GetContaFinanceiraVmSistemas
     * @return array
     */
    function GetContaFinanceiraVmSistemas()
    {
        self::ConectarDbVmSistemas();

        $contaFinanceiraId = $this->configuracaoHelper->configuracoes["ContaFinanceiraVmSistemas"]["Id"];

        $contaFinanceira = $this->dbVmSistemas->fetch_assoc("
            select banco_id, carteira, convenio, agencia, codigo banco_codigo
            from contas a
            join bancos b on a.banco_id = b.id
            where a.id = $contaFinanceiraId");

        return $contaFinanceira;
    }
}
