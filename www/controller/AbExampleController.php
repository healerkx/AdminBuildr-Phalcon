<?php

/**
 * Created by PhpStorm.
 * User: healer_kx@163.com
 * Date: 2016/8/1
 * Time: 12:31
 */
class AbExampleController extends AbBaseController
{

    /**
     * @comment: 列出所有的示例
     * @page
     */
    public function indexAction() {

        $data = array();
        $this->preloadChinaProvince();
        parent::showPager(3, 23);
        $views = [
            ["name" =>'实例', "template" => "abexample/index"]
        ];
        parent::showTabViews($views, "Forms Creator - Example", $data);
    }
    /**
     *
     */
    public function searchAction() {

        $data = array();
        $this->preloadChinaProvince();
        parent::showPager(3, 23);
        $views = [
            ["name" =>'实例', "template" => "abforms/examples"]
        ];
        parent::showTabViews($views, "Forms Creator - Example", $data);
    }

    /**
     *
     */
    public function dialogAction() {

        $data = array();
        $this->preloadChinaProvince();
        parent::showPager(3, 23);
        $views = [
            ["name" =>'实例', "template" => "abforms/examples"]
        ];
        parent::showTabViews($views, "Forms Creator - Example", $data);
    }

    /**
     *
     */
    public function imageFileAction() {

        $data = array();
        $this->preloadChinaProvince();
        parent::showPager(3, 23);
        $views = [
            ["name" =>'实例', "template" => "abforms/examples"]
        ];
        parent::showTabViews($views, "Forms Creator - Example", $data);
    }

    /**
     *
     */
    public function fileAction() {

        $data = array();
        $this->preloadChinaProvince();
        parent::showPager(3, 23);
        $views = [
            ["name" =>'实例', "template" => "abforms/examples"]
        ];
        parent::showTabViews($views, "Forms Creator - Example", $data);
    }


    public function remoteValidateAction()
    {

    }


    private function showExampleViews($template, $data) {
        $views = [
            ["name" =>'示例', "template" => $template]
        ];
        parent::showTabViews($views, "示例 - $template", $data);
    }
}