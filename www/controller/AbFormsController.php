<?php

/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2016/1/31
 * Time: 21:05
 */
class AbFormsController extends AbBaseController
{

    public function createAction() {
        $views = [
            ["name" =>'新建', "template" => "abforms/create"],
            ["name" =>'预览', "template" => "abforms/preview"]];

        $data = array();
        parent::showTabViews($views, "Forms Creator", $data);
    }

    public function examplesAction() {
        $views = [
            ["name" =>'实例', "template" => "abforms/examples"]
            ];

        $data = array();
        $this->preloadChinaProvince();
        parent::showPager(3, 23);
        parent::showTabViews($views, "Forms Creator - Example", $data);
    }
}