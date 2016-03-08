<?php

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2016/2/5
 * Time: 22:56
 */
class CommonController extends AbBaseController
{
    public function errorAction()
    {
        $views = [
            ['name' => '错误信息', "template" => "kxadminrole/item"],
        ];
        $data = array('itemViewMode' => 'update');
        parent::showTabViews($views, '错误信息', $data);
    }

    public function loginAction() {
        $data = array();
        parent::showPage('common/login', $data);
    }


}