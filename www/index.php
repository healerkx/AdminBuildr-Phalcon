<?php

use Phalcon\Loader,
    Phalcon\DI\FactoryDefault,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Mvc\Application,
    Phalcon\Mvc\View,
    Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Session\Adapter\Files as Session;

$loader = new Loader();

$loader->registerDirs(
    array(
        './controller',
        './model',
        './lib',
        './plugin'
    )
)->register();


$di = new FactoryDefault();

$di->set('dispatcher', function () {

    // Create an events manager
    $eventsManager = new EventsManager();

    // Listen for events produced in the dispatcher using the Security plugin
    $eventsManager->attach('dispatch:beforeExecuteRoute', new SecurityPlugin);

    // Handle exceptions and not-found exceptions using NotFoundPlugin
    // $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

    $dispatcher = new Dispatcher();

    // Assign the events manager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});


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


$di['tag'] = function() {
    return new AbTag();
};


$di->set('db', function() {
    $config = ApplicationConfig::getMySQLConnection();
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

$di->setShared('session', function () {
    $session = new Session();
    $session->start();
    return $session;
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