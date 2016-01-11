<?php

class AdminAppController extends AbBaseController
{
    /**
     * Add new admin-application, for example: Badmin
     */
    public function createAction() {
        parent::show('app/create', false);
    }
}