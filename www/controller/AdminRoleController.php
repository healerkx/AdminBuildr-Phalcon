<?php

class AdminRoleController extends AbBaseController
{

    public function indexAction() {

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
        parent::show('adminrole/index', $data);
    }

    public function createAction() {
        parent::result(array('a' => 2));
    }

    public function updateAction($id) {
        $item = KxAdminRole::findFirst($id);
        parent::result(array('a' => $item->toArray()));
    }

    public function deleteAction($id) {
        $item = KxAdminRole::findFirst($id);
        $deleteField = 'deleted';
        $deleteValue = 1;
        $item->$deleteField = $deleteValue;
        //parent::dump($item);
        $deleted = $item->save();
        parent::result(array('id' => $id, 'deleted' => $deleted));
    }

    public function itemOperator() {
        // array for operators
        return array(
            array('name' => '编辑', 'operator' => 'edit', 'action' => 'adminRole/update'),
            array('name' => '删除', 'operator' => 'delete', 'action' => 'adminRole/delete')
        );
    }
}