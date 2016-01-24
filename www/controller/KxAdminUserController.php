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
}