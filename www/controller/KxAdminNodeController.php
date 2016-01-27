<?php

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2016/1/22
 * Time: 19:59
 */
class KxAdminNodeController extends AbBaseController
{
    /**
     *
     */
    public function indexAction()
    {
        //parent::dump($this->getAdminNodes());
        $nodes = $this->sync($this->getActionAccessLists(), $this->getAdminNodes());

        $views = array('name' => 'Action管理', 'template' => 'kxadminnode/actions');
        $data = array(
            'controllers' => $nodes
        );

        parent::showTabViews($views, '系统节点管理', $data);
    }

    private static function hasControllerActionInNodes($controller, $action, &$nodes)
    {
        foreach ($nodes as &$node)
        {
            if ($node['controller'] == $controller && $node['action'] == $action)
            {
                $node['exists'] = true;
                return $node;
            }
        }
        return false;
    }

    private function sync($controllerActions, $nodes)
    {
        $result = array();
        foreach ($controllerActions as $controllerName => $actions)
        {
            $a = array();
            foreach ($actions as $action)
            {
                $actionName = $action['action'];
                $accessName = $action['access'];

                $w['action'] = $actionName;
                $w['access'] = $accessName;

                $node = self::hasControllerActionInNodes($controllerName, $actionName, $nodes);
                if ($node)
                {
                    $w['node_id'] = $node['node_id'];
                    $w['name'] = $node['name'];
                }
                else
                {
                    $w['node_id'] = 0;
                    $w['name'] = '';
                }

                array_push($a, $w);
            }
            $result[$controllerName] = $a;
        }

        foreach ($nodes as $node)
        {
            if (array_key_exists('exists', $node))
            {
                continue;
            }

            $controller = $node['controller'];
            $action = $node['action'];

            $a = array('action' => $action, 'node_id' => $node['node_id'], 'name' => $node['name']);
            array_push($result[$controller], $a);
        }
        return $result;
    }

    private function getAdminNodes()
    {
        $nodes = KxAdminNode::find()->toArray();
        return $nodes;
    }

    private function getActionAccessLists() {
        $controllerNames = ['ModuleController', 'ReportController'];
        $result = array();
        foreach ($controllerNames as $controllerName) {
            $actions = $this->getActionAccessList($controllerName);
            $controller = lcfirst(substr($controllerName, 0, -10));
            $result[$controller] = $actions;
        }
        return $result;
    }

    private function getActionAccessList($className)
    {
        // $className = 'ModuleController';
        $clz = new ReflectionClass($className);
        $methods = $clz->getMethods(ReflectionMethod::IS_PUBLIC);

        $actions = array();
        foreach ($methods as $method) {
            if ($method->class != $className) {
                continue;
            }

            $methodName = $method->getName();
            if (Strings::endsWith($methodName, 'Action')) {
                array_push($actions, $this->getActionAccess($method));
            }
        }
        return $actions;
    }

    private function getActionAccess($method) {
        $methodName = $method->getName();
        $actionName = substr($methodName, 0, -6);
        $comments = $method->getDocComment();
        $comments = trim($comments);
        $commentLines = explode("\n", $comments);
        foreach ($commentLines as $commentLine) {
            $d = strstr($commentLine, '@access');
            $access = trim(substr($d, 7));
            if ($access) {
                return array('action' => $actionName, 'access' => $access);
            }
        }

        return array('action' => $actionName, 'access' => '');
    }
}