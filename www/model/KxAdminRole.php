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
            'remark' => '说明',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        );
    }


    public static function getItemById($id) {
        if ($id == 0) {

        }
        $item = KxAdminRole::findFirst($id);
        return $item->toArray();
    }

}