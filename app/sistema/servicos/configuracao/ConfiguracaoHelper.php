<?php

/**
 * ConfiguracaoHelper short summary.
 *
 * ConfiguracaoHelper description.
 *
 * @version 1.0
 * @author Fabio
 */
class ConfiguracaoHelper
{
    public $configuracoes;

    function __construct()
    {
        $this->configuracoes = self::GetConfiguracoes();
    }

    function GetConfiguracoes()
    {
        $config = json_decode(trim(file_get_contents("$_SERVER[DOCUMENT_ROOT]/sistema/config.json"),"\xEF\xBB\xBF"),true);
        return $config;
    }
}
