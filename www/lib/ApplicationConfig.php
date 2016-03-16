<?php


class ApplicationConfig extends CConfig
{
    public static function getConfigPath($fileName='')
    {
        return dirname(dirname(dirname(__FILE__))) . "\\config\\" . $fileName;
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

        return $c['mysql'];
    }

    public static function getMenu($session) {
        $user = $session->get('user');

        $menuArr = null;
        $menuFile = self::getConfigPath('menu.php');
        if (file_exists($menuFile)) {
            $menuArr = include($menuFile);
            return $menuArr;
        }


        // TODO: Load menu according to Role.

        return $menuArr;
    }

    public static function getDefaultPageSize() {
        return 20;
    }
}