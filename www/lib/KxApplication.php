<?php

use \Phalcon\Mvc\Application;

class KxApplication extends Application
{
    static $app = null;

    public function __construct($di) {
        parent::__construct($di);
        self::$app = $this;
    }

    public static function current() {
        return self::$app;
    }


}