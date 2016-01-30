<?php

/**
 * Created by PhpStorm.
 * User: heale
 * Date: 2016/1/30
 * Time: 23:01
 */
class KxAdminAccess extends AbBaseModel
{

    public static function getAccess($roleId) {
        $access = KxAdminAccess::find("role_id=$roleId");
        return $access->toArray();
    }
}