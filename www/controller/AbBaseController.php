<?php

use \Phalcon\Mvc\Controller;
use \Phalcon\Debug\Dump;

class AbBaseController extends Controller
{

    public function show($view, $data=array()) {

        $data['content_phtml'] = $view;
        $data['content_javascript_phtml'] = self::getJavaScriptTemplateName($view);

        if (array_key_exists('item_has_operator', $data) && $data['item_has_operator']) {
            if (method_exists($this, 'itemOperator')) {
                $data['item_operators'] = $this->itemOperator();
            } else {
                $data['item_operators'] = array();
            }
        }

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