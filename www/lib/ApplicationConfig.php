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



        file_put_contents('ss.txt', json_encode($session), FILE_APPEND);
        $menuArr = array(
            array(
                'name' => '系统管理',
                'active' => false,
                'sub_menus' => array(array('name' => '创建模块', 'url' => 'kxAdminRole/edit'), array('name' => "444"))
            ),
            array(
                'name' => '系统管理2',
                'active' => false,
                'sub_menus' => array(array('name'=>'www'), array('name' => '创建模块', 'url' => 'module/create'), array('name' => "555"))
            )
        );
        return $menuArr;
    }
}