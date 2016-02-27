<?php


class MainBoardController extends AbBaseController {

    public function indexAction() {
        $data = array();
        parent::show('index/a', $data);
    }

}