<?php

class AdminBuilderConfig extends CConfig
{
    public static function getConfigPath($fileName='')
    {
        return "..\\config\\" . $fileName;
    }

    public static function getConfig($key=false)
    {
        $configFile = self::getConfigPath('config.json');
        $config = json_decode(file_get_contents($configFile), true);
        if ($key)
        {
            return $config[$key];
        }
        return $config;
    }

    public static function getMySQLConnection() {
        $c = self::getConfig('product');
        file_put_contents("aaa.txt", "d", FILE_APPEND);
        return $c['mysql'];
    }
}