<?php

class KxAdminUser extends AbBaseModel
{
    public static function primaryKeyName() {
        return "admin_uid";
    }


    public static function headers() {
        return array(
            'admin_uid' => 'ID',
            'username' => '姓名',
            'nickname' => '昵称',
            'email' => '电子邮件',
            'phone' => '联系电话',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        );
    }
}