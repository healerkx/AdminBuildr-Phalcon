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
        $nodes = $this->merge($this->getActionAccessLists(), $this->getAdminNodes());

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

    private function merge($controllerActions, $nodes)
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
                    $w['brand_new'] = false;
                }
                else
                {
                    $w['node_id'] = 0;
                    $w['name'] = '';
                    $w['brand_new'] = true;
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

    /**
     * @return mixed
     * Node record in DB
     */
    private function getAdminNodes()
    {
        $nodes = KxAdminNode::find()->toArray();
        return $nodes;
    }

    /**
     * @return array
     * Get all given controllers' actions
     */
    private function getActionAccessLists() {
        $controllerNames = ['AbModuleController', 'AbReportController'];
        $result = array();
        foreach ($controllerNames as $controllerName) {
            $actions = $this->getActionAccessList($controllerName);
            $controller = lcfirst(substr($controllerName, 0, -10));
            $result[$controller] = $actions;
        }
        return $result;
    }

    /**
     * @param $className
     * @return array
     * Get a controller's actions
     */
    private function getActionAccessList($className)
    {
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

    /**
     * @param $method
     * @return array
     */
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

    public function syncAction()
    {
        $entries = $_POST['entries'];
        $now = date('Y-m-d H:i:s');
        foreach ($entries as $entry)
        {
            $nodeId = $entry['node_id'];
            $name = trim($entry['name']);
            if ($nodeId == 0) {
                if (!empty($name)) {
                    $node = new KxAdminNode();
                    $node->controller = trim($entry['controller']);
                    $node->action = trim($entry['action']);
                    $node->name = $name;
                    $node->create_time = $now;
                    $node->update_time = $now;
                    $node->save();
                }

            } else {
                $node = KxAdminNode::findFirst($nodeId);
                if ($node->name != $name) {
                    $node->name = $name;
                    $node->update_time = $now;
                    $node->save();
                }
            }
        }
        parent::result(array('post' => $entries));
    }
}