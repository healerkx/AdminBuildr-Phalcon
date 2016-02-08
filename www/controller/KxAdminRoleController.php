<?php

class KxAdminRoleController extends AbBaseController
{
    /**
     *
     */
    public function listAction() {
        $result = KxAdminRole::search($_GET);

        $items = array();
        if ($result['items']) {
            $items = $result['items']->toArray();
        }

        //parent::dump($allRoles->toArray());
        $data = array(
            'item_has_checkbox' => true,
            'item_has_operator' => true,
            'headers' => KxAdminRole::headers(),
            'count' => $result['count'],
            'items' => $items,
            'target_field' => 'role_id'
        );

        $views = [
            ['name' => '管理员角色列表', "template" => "kxadminrole/list_admin_role"],
            ];

        parent::showTabViews($views, '管理员角色管理', $data);
    }

    public function createAction() {
        $views = [
            ['name' => '新建角色', "template" => "kxadminrole/item"],
        ];
        $init = KxAdminRole::getEmptyItem();
        //parent::dump($init);
        $data = array('itemViewMode' => 'create', 'i' => $init);
        parent::showTabViews($views, '新建角色', $data);
    }

    public function updateAction($id) {
        if (!isset($id)) {
            parent::redirect('common/error', "This action need parameter \$id");
        }
        $views = [
            ['name' => '更新角色信息', "template" => "kxadminrole/item"],
        ];
        $item = KxAdminRole::getItemById($id);
        $data = array('itemViewMode' => 'update', 'i' => $item);
        parent::showTabViews($views, '更新角色信息', $data);
    }

    public function viewAction($id) {
        if (!isset($id)) {
            parent::redirect('common/error', "This action need parameter \$id");
        }
        $views = [
            ['name' => '查看角色信息', "template" => "kxadminrole/item"],
        ];
        $item = KxAdminRole::getItemById($id);
        $data = array('itemViewMode' => 'view', 'i' => $item);
        parent::showTabViews($views, '查看角色信息', $data);
    }

    private function getControllerNodes() {
        $nodes = KxAdminNode::find();
        $result = array();
        foreach ($nodes as $node)
        {
            $nodeId = $node->node_id;
            $controller = $node->controller;
            $action = $node->action;
            $name = $node->name;

            $result[$controller][] = array('node_id' => $nodeId, 'action' => $action, 'name' => $name);
        }
        return $result;
    }

    /**
     * @param $roleId
     * @access Follow(kxAdminRole/index)
     */
    public function editAction($roleId) {
        if (!isset($roleId)) {
            // TODO: 没有参数
            // TODO: Redirect to ...
        }
        $role = KxAdminRole::findFirst($roleId);
        if (!$role) {
            // TODO: 没有这个角色
            // TODO: Redirect to ...
        }

        $access = KxAdminAccess::getAccess($role->role_id);
        $controllerNodes = $this->getControllerNodes();


        foreach ($controllerNodes as $controller => &$nodes) {
            foreach ($nodes as &$node) {
                $node['access'] = false;
                $node['menu_group_id'] = 0;

                //
                $nodeId = $node['node_id'];
                foreach ($access as $a) {
                    $accessNodeId = $a['node_id'];

                    if ($nodeId == $accessNodeId) {
                        $node['access'] = true;
                        $node['menu_group_id'] = $a['menu_group_id'];
                        break;
                    }

                }
            }
            unset($node);
        }
        unset($nodes);


        $menuGroups = array(array('id'=>1, 'name'=>"ddd"), array('id'=>1, 'name'=>'Name'));

        $data = array(
            'controllerNodes' => $controllerNodes,
            'edit_menu_groups' => $menuGroups,
            'role_name' => $role->name
        );
        $views = [
            ["name" => '访问控制', "template" => "kxadminrole/edit"],
            ["name" => '导航菜单分组', "template" => "kxadminrole/edit_menu_group"]
        ];
        parent::showTabViews($views, '角色访问控制', $data);
    }

    public function updateMenuGroupsAction($roleId) {
        $controllerNodes = $this->getControllerNodes();

        $data = array(
            'controllerNodes' => $controllerNodes,
        );
        return parent::result($data);
    }

    public function deleteAction($id) {
        $item = KxAdminRole::findFirst($id);
        $deleteField = 'deleted';
        $deleteValue = 1;
        $item->$deleteField = $deleteValue;

        $deleted = $item->save();
        parent::result(array('id' => $id, 'deleted' => $deleted));
    }

    /**
     * @param $id
     * @access Follow(kxAdminRole/index)
     */
    public function listAdminUserAction($id)
    {
        $roles = KxAdminRole::find();
        $rs = KxAdminUserRole::find("role_id=$id");
        $users = array();
        foreach ($rs as $r) {
            $user = $r->KxAdminUser->toArray();
            array_push($users, $user[0]);
        }

        $data = array(
            'current' => $id,
            'roles' => $roles->toArray(),
            'users' => $users
        );

        $views = [
            ['name' => '管理员角色列表', "template" => "kxadminrole/list_admin_user"],
        ];

        parent::showTabViews($views, '管理员角色管理', $data);
    }

    /**
     * @return array
     */
    public function itemOperator() {
        // array for operators
        return array(
            array('name' => '编辑', 'operator' => 'edit', 'action' => 'kxAdminRole/edit'),
            array('name' => '用户列表', 'operator' => 'listAdminUser', 'action' => 'kxAdminRole/listAdminUser'),
            array('name' => '删除', 'operator' => 'delete', 'action' => 'kxAdminRole/delete')
        );
    }
}