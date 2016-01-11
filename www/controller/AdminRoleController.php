<?php

class AdminRoleController extends AbBaseController
{

    public function indexAction() {
        parent::result(array('a' => 1));
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
}