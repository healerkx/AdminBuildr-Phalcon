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
        parent::result(array('a' => 2));
    }

    private function getControllerNodes() {
        $nodes = KxAdminNode::find();
        $result = array();
        foreach ($nodes as $node)
        {
            $controller = $node->controller;
            $action = $node->action;
            $name = $node->name;

            $result[$controller][] = array('action' => $action, 'name' => $name);
        }
        return $result;
    }

    /**
     * @param $roleId
     * @access Follow(kxAdminRole/index)
     */
    public function editNodeAction($roleId) {
        $controllerNodes = $this->getControllerNodes();

        $data = array(
            'controllerNodes' => $controllerNodes,
        );
        $views = array('name' => '节点访问控制', 'template' => 'kxadminrole/edit_node');
        parent::showTabViews($views, '节点访问控制', $data);
    }

    /**
     * @param $roleId
     * @access Follow(kxAdminRole/index)
     */
    public function editMenuAction($roleId) {
        $controllerNodes = $this->getControllerNodes();

        $role = KxAdminRole::findFirst($roleId);
        if (!$role) {
            // TODO: 没有这个角色
        }
        $tabTitle = "角色菜单设置 > {$role->name}";
        $data = array(
            'controllerNodes' => $controllerNodes,
            'tab_title' => $tabTitle
        );
        $views = [
            ["name" => '导航菜单', "template" => "kxadminrole/edit_menu"],
            ["name" => '导航菜单分组', "template" => "kxadminrole/edit_menu_group"]
            ];
        parent::showTabViews($views, $tabTitle, $data);
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
            array('name' => '节点管理', 'operator' => 'editNode', 'action' => 'kxAdminRole/editNode'),
            array('name' => '菜单管理', 'operator' => 'editMenu', 'action' => 'kxAdminRole/editMenu'),
            array('name' => '用户列表', 'operator' => 'listAdminUser', 'action' => 'kxAdminRole/listAdminUser'),
            array('name' => '删除', 'operator' => 'delete', 'action' => 'kxAdminRole/delete')
        );
    }
}