<?php

class KxAdminUserController extends AbBaseController
{
    /**
     * @param $roleId
     * @access Follow(kxAdminUser/index)
     * List all users of this Role id
     */
    public function listAction($roleId=0)
    {
        if ($roleId != 0)
        {
            $adminUsers = KxAdminUser::find("role_id=$roleId");
        }
        else
        {
            $adminUsers = KxAdminUser::find();
        }

        $data = array(
            'item_has_checkbox' => true,
            'item_has_operator' => true,
            'role_id' => $roleId,
            'count' => count($adminUsers),
            'users' => $adminUsers->toArray(),
            'target_field' => 'admin_uid'
        );

        $views = [
            ['name' => '管理员列表', "template" => "kxadminuser/list"],
        ];

        parent::showTabViews($views, '管理员角色管理', $data);
    }


    public function loginAction() {
        // TODO:
        parent::result(array('auth' => $this->request->getPost()));
    }

    public function logoutAction() {
        // TODO:
        parent::result(array('auth' => $this->request->getPost()));
    }

    /**
     * @param $roleId
     * Create a new Admin user
     */
    public function createAction($roleId=0)
    {
        $roles = KxAdminRole::find()->toArray();
        $init = KxAdminUser::getEmptyItem();
        $data = array(
            'i' => $init,
            'roles' => $roles
        );

        $views = [
            ['name' => '新增管理员', "template" => "kxadminuser/edit"],
        ];
        parent::showTabViews($views, '管理员管理', $data);
    }

    /**
     * @param $adminUid
     * Create a new Admin user
     */
    public function updateAction($adminUid)
    {
        $roles = KxAdminRole::find()->toArray();
        $item = KxAdminUser::getItemById($adminUid);
        $data = array(
            'i' => $item,
            'roles' => $roles
        );

        $views = [
            ['name' => '编辑管理员', "template" => "kxadminuser/edit"],
        ];
        parent::showTabViews($views, '管理员管理', $data);
    }

    public function editAction() {

        $now = date('Y-m-d H:i:s');
        $adminUid = $this->request->getPost('admin_uid');
        if ($adminUid) {
            $adminUser = KxAdminUser::findFirst($adminUid);
            $adminUser->update_time = $now;
        } else {
            $adminUser = new KxAdminUser();
            $adminUser->status = 1;
            $adminUser->create_time = $now;
            $adminUser->update_time = $now;
        }

        $adminUser->username = $this->request->getPost('username');
        $adminUser->nickname = $this->request->getPost('nickname');
        $adminUser->email = $this->request->getPost('email');
        $adminUser->phone = $this->request->getPost('phone');
        $adminUser->role_id = intval($this->request->getPost('role_id'));
        if ($adminUser->save())
        {
            parent::redirect('kxAdminUser/list');
        }
        else
        {
            var_dump($adminUser->getMessages());exit;
        }
    }

    public function removeAction() {
        // TODO:
    }

    public function changePasswordAction() {

    }

    /**
     * @return array
     */
    public function itemOperator() {
        // array for operators
        return array(
            array('name' => '编辑', 'operator' => 'edit', 'action' => 'kxAdminUser/update'),
            array('name' => '删除', 'operator' => 'delete', 'action' => 'kxAdminUser/delete')
        );
    }
}