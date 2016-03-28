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

    public static function enableDbProfiling($di, $connection) {
        $eventsManager = new \Phalcon\Events\Manager();

        //从di中获取共享的profiler实例
        $profiler = $di->getProfiler();

        //监听所有的db事件
        $eventsManager->attach('db', function($event, $connection) use ($profiler) {
            //一条语句查询之前事件，profiler开始记录sql语句
            if ($event->getType() == 'beforeQuery') {
                $profiler->startProfile($connection->getSQLStatement());
            }
            //一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
            if ($event->getType() == 'afterQuery') {
                $profiler->stopProfile();
            }
        });
        $connection->setEventsManager($eventsManager);
    }

}