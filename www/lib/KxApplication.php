<?php

use \Phalcon\Mvc\Application;

class KxApplication extends Application
{
    static $app = null;

    private $config = null;

    public function __construct($di) {
        parent::__construct($di);
        $this->config = ApplicationConfig::getConfig();
        self::$app = $this;
    }

    public static function current() {
        return self::$app;
    }

    public function getConfig() {
        return array(
            'appName' => 'AdminBuildr'
        );
        // return $this->config;
    }


}