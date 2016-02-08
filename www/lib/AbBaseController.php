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
     * @deprecated now
     * @param $view
     * @param array $data
     */
    public function show($view, $data=array()) {

        $data['content_phtml'] = $view;
        $data['js_tpl_files'] = array(self::getJavaScriptTemplateName($view));

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

        if (array_key_exists('item_has_operator', $data) && $data['item_has_operator']) {
            if (method_exists($this, 'itemOperator')) {
                $data['item_operators'] = $this->itemOperator();
            } else {
                $data['item_operators'] = array();
            }
        }

        $data['menu_groups'] = $this->getMenuArray();

        $data['tabview_title'] = $viewsTitle;
        $data['content_phtml'] = 'common/tabview2';

        $tabViews = self::convertTabViewArray($views);

        $data['tabview_variables'] = $tabViews;
        $tabViewsJsTpl = self::getTabViewJavaScriptTemplateName($tabViews);
        $data['js_tpl_files'] = $this->filterTemplateFiles($tabViewsJsTpl);

        $this->view->setVars($data);
        $this->view->pick('common/main');
    }

    public function result($data) {
        return $this->error(0, $data);
    }

    public function error($errorCode, $data) {
        return exit(json_encode(array(
            'error' => $errorCode,
            'data' => $data
        )));
    }

    public function forward($controllerAction) {
        list($controller, $action) = explode('/', $controllerAction);
        $this->dispatcher->forward(
            array("controller" => $controller, "action" => $action));
    }

    public function redirect($controllerAction) {
        $this->response->redirect($controllerAction)->sendHeaders();
    }

    public function dump($var) {
        echo (new Dump)->variables($var);
        exit;
    }

    private function getMenuGroup() {

    }

    private function getCurrentAction() {
        $dispatcher = $this->getDI()->getShared('dispatcher');

        return array($dispatcher->getControllerName(), $dispatcher->getActionName());
    }

    private function getMenuArray() {
        $current = $this->getCurrentAction();
        $currentUrl = implode('/', $current);

        // $s = $this->session->get('a');
        $menuArr = ApplicationConfig::getMenu($this->session);

        // Compare current Url to active the menu group
        $activeSet = false;
        foreach ($menuArr as &$i) {
            $subMenu = $i['sub_menus'];
            foreach ($subMenu as $s) {
                $url = $s['url'];
                if ($url == $currentUrl) {
                    $i['active'] = true;
                    $activeSet = true;
                    break;
                }
            }
            if ($activeSet) { break; }
        }

        if (!$activeSet) {

        }
        return $menuArr;
    }

    /**
     * Only existing tpl file can be included.
     * @param $files
     * @return array
     */
    private function filterTemplateFiles($files) {
        //return $files;
        $result = array();
        $dir = $this->view->getViewsDir();
        foreach ($files as $file) {
            $tpl = $dir . "/{$file}.phtml";
            if (file_exists($tpl)) {
                array_push($result, $file);
            }
        }
        return $result;
    }

    private static function getJavaScriptTemplateName($template) {
        return $template . '.js';
    }

    /**
     * Render abmodule/a and abmodule/b in a tab view,
     * view will try to include abmodule/a.js.phtml, abmodule/b.js.phtml and abmodule/abmodule.js.phtml as common
     * @param $tabViews
     * @return array
     */
    private static function getTabViewJavaScriptTemplateName($tabViews) {
        $result = array();
        foreach ($tabViews as $tabView) {
            $template = $tabView['template'];

            // each abmodule has a common js tpl file.
            $parts = explode('/', $template);
            $module = $parts[0];
            array_push($result, "{$module}/{$module}.js");

            // each tab has its own js tpl file.
            array_push($result, "{$template}.js");
        }

        $result = array_unique($result);
        return $result;
    }

    private static function convertTabViewArray($views) {
        $result = array();
        if (is_array($views)) {
            if (array_key_exists('name', $views) && array_key_exists('template', $views)) {
                // Has a single tab only, set active.
                $views['active'] = true;
                $result[] = $views;
            } else {
                $result = $views;
            }
        }

        $activeSet = false;
        foreach ($result as &$view) {
            // If id not given, generate id for each tab.
            if (!array_key_exists('id', $view)) {
                $rand1 = rand(10, 99);
                $rand2 = rand(10, 99);
                $view['id'] = "tab_id_{$rand1}{$rand2}";
            }

            if (array_key_exists('active', $view) && $view['active']) {
                $activeSet = true;
            }
        }

        if (!$activeSet) {
            // set first tab as active if 'active' not set.
            $result[0]['active'] = true;
        }
        return $result;
    }
}