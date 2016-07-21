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
                    $w['comment'] = $action['comment'];
                    $w['brand_new'] = false;
                }
                else
                {
                    $w['node_id'] = 0;
                    $w['name'] = '';
                    $w['comment'] = $action['comment'];
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

            $a = array(
                'action' => $action,
                'node_id' => $node['node_id'],
                'name' => $node['name'],
                'comment' => $node['comment']);
            array_push($result[$controller], $a);
        }
        return $result;
    }

    /**
     * @return mixed
     * Node records in DB
     */
    private function getAdminNodes()
    {
        $nodes = KxAdminNode::find()->toArray();
        return $nodes;
    }

    /**
     * @return array
     * Get all given controllers' actions
     * 通过反射得到我们需要的Controller(s)里面的每一个Action(s)
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
     * Get a controller's actions by reflection
     * 通过反射得到我们需要的Controller Class里面的每一个Action(s)
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
        $page = false;
        $access = '';
        $comment = '';
        foreach ($commentLines as $commentLine) {

            $p = strstr($commentLine, '@page');
            if ($p) {
                $page = true;
                continue;
            }

            $a = strstr($commentLine, '@access');
            if ($a) {
                $access = trim(substr($a, 7));
            }

            $c = strstr($commentLine, '@comment');
            if ($c) {
                $comment = trim(substr($c, 8));
            }

        }

        return array(
            'action' => $actionName,
            'access' => $access,
            'page' => $page,
            'comment' => $comment);
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
                    $node->status = 1;
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