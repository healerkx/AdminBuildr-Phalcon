<?php

use \Phalcon\Mvc\Controller;
use \Phalcon\Debug\Dump;

class AbBaseController extends Controller
{

    public function show($view, $data=array()) {

        $data['content_phtml'] = $view;
        $data['content_javascript_phtml'] = self::getJavaScriptTemplateName($view);

        $this->view->setVars($data);
        $this->view->pick('common/main');
    }

    public function result($data) {
        return $this->error(0, $data);
    }

    public function error($errorCode, $data) {
        exit(json_encode(array(
            'error' => $errorCode,
            'data' => $data
        )));
    }

    public function dump($var) {
        echo (new Dump)->variables($var);
        exit;
    }

    private static function getJavaScriptTemplateName($template) {
        return $template . '.js';
    }
}