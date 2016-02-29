<?php

class KxAdminUserController extends AbBaseController
{
    public function listAction()
    {
        $data = array(
            'users' => array("Mike", "Lily")
        );
        parent::show('user/list', $data);
    }


    public function loginAction() {
        // TODO:
        parent::result(array('auth' => $this->request->getPost()));
    }

    public function logoutAction() {
        // TODO:
        parent::result(array('auth' => $this->request->getPost()));
    }

    public function addAction() {
        // TODO:
    }

    public function disableAction() {
        // TODO:
    }

    public function removeAction() {
        // TODO:
    }

    public function changePasswordAction() {

    }
}