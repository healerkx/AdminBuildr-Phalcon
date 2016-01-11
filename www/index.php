<?php

use Phalcon\Loader,
    Phalcon\DI\FactoryDefault,
    Phalcon\Mvc\Application,
    Phalcon\Mvc\View,
    Phalcon\Mvc\View\Engine\Volt;

$loader = new Loader();

$loader->registerDirs(
    array(
        './controller',
        './model',
    )
)->register();


$di = new FactoryDefault();

$di->set('voltService', function ($view, $di) {
    $volt = new Volt($view, $di);
    $compiler = $volt->getCompiler();

    $filter = new Filter($compiler);
    $filter->init();

    $compiler->setOptions(
        array(
            "compiledPath"      => "./view/compiled-files/",
            "compiledExtension" => ".php",
            "compileAlways" => true
        )
    );

    return $volt;
});

// Registering the view component
$di->set('view', function() {
    $view = new View();
    $view->setViewsDir(__DIR__ . '/view');


    $view->registerEngines(
        array(
            ".phtml" => 'voltService'
        )
    );
    return $view;
});



$di->set('db', function() {
    $config = Config::getMySQLConnection();
    $config['options'] = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    );

    return new Phalcon\Db\Adapter\Pdo\Mysql($config);
});


$di->set('redis', function() {
    require_once("redisproxy.php");
    $redis = new RedisProxy();

    return $redis;
});


$di->set('modelsManager', function() {
    return new Phalcon\Mvc\Model\Manager();
});

try {

    ini_set('date.timezone','Asia/Shanghai');
    $t1 = microtime(true);
    $application = new Application($di);
    echo $application->handle()->getContent();

    $t2 = microtime(true);
    #echo ($t2 - $t1);

} catch (\Exception $e) {
    echo $e->getMessage();
}