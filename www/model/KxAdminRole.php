<?php


class KxAdminRole extends AbBaseModel
{
    public function getSource() {
        return "kx_admin_role";
    }

    public static function headers() {
        return array(
            'role_id' => 'ID',
            'name' => '名称',
            'create_time' => '创建时间',
        );
    }

}