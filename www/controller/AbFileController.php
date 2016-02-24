<?php

/**
 * Created by PhpStorm.
 * User: heale
 * Date: 2016/2/24
 * Time: 10:30
 */
class AbFileController extends AbBaseController
{

    public function uploadAction()
    {
        $uploadPath = '';   // TODO: Config
        $uploadFile = self::makeFileName();



    }

    private static function makeFileName($fileName) {
        return $fileName;
    }
}