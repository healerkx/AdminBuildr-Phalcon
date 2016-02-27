<?php


class AjaxController extends AbBaseController
{
    public function citiesAction($provinceId) {
        parent::result(SysRegion::cities($provinceId));
    }

    public function countiesAction($cityId) {
        parent::result(SysRegion::counties($cityId));
    }


    public function loginAction() {

        parent::result(array('auth' => $this->request->getPost()));
    }

}