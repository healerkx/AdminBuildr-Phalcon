<?php

class AdminRoleController extends AbBaseController
{

    public function indexAction() {

        $allRoles = KxAdminRole::search($_GET);

        $items = array();
        if ($allRoles) {
            $items = $allRoles->toArray();
        }

        //parent::dump($allRoles->toArray());
        $data = array(
            'item_has_checkbox' => true,
            'item_has_operator' => true,
            'headers' => KxAdminRole::headers(),
            'items' => $items
        );
        parent::show('adminrole/index', $data);
    }

    public function createAction() {
        parent::result(array('a' => 2));
    }

    public function updateAction() {
        parent::result(array('a' => 3));
    }

    public function deleteAction() {
        parent::result(array('a' => 4));
    }

    public function itemOperator() {
        // array for operators
        return array(
            array('name' => '编辑', 'operator' => 'edit', 'action' => 'adminRole/update'),
            array('name' => '删除', 'operator' => 'delete', 'action' => 'adminRole/delete')
        );
    }
}