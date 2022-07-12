<?php
/**
 * ConfigService short summary.
 *
 * ConfigService description.
 *
 * @version 1.0
 * @author Fabio
 */
 class ConfigService
{
    public static function GetConfiguracoes()
    {
        $config = json_decode(trim(file_get_contents("Config/config.json"),"\xEF\xBB\xBF"),true);
        return $config;
    }
}
