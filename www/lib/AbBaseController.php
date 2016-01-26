<?php

use \Phalcon\Mvc\Controller;
use \Phalcon\Debug\Dump;

/**
 * Class AbBaseController
 *
 */
class AbBaseController extends Controller
{
    /**
     * @param $view
     * @param array $data
     */
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

    /**
     * @param $views
     * @param $viewsTitle
     * @param array $data
     */
    public function showTabViews($views, $viewsTitle, $data=array()) {

        $data['content_phtml'] = 'common/tabview2';
        $data['content_javascript_phtml'] = self::getJavaScriptTemplateName($view);

        if (array_key_exists('item_has_operator', $data) && $data['item_has_operator']) {
            if (method_exists($this, 'itemOperator')) {
                $data['item_operators'] = $this->itemOperator();
            } else {
                $data['item_operators'] = array();
            }
        }

        foreach ($views as &$view) {
            if (!array_key_exists('id', $view)) {
                $rand1 = rand(10, 99);
                $rand2 = rand(10, 99);
                $view['id'] = "tab_id_{$rand1}{$rand2}";
            }
        }

        $data['tabview_title'] = $viewsTitle;
        $data['tabview_variables'] = $views;

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