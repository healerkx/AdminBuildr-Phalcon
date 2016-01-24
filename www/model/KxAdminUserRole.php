<?php

/**
 * Created by PhpStorm.
 * User: heale
 * Date: 2016/1/24
 * Time: 18:40
 */
class KxAdminUserRole extends AbBaseModel
{
    public function initialize()
    {
        $this->hasMany('id', 'KxAdminUser', 'admin_uid');
    }
}