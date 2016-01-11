<?php

class AjaxController extends AbBaseController
{

    public function result($data) {
        return $this->error(0, $data);
    }

    public function error($errorCode, $data) {
        exit(json_decode(array(
            'error' => $errorCode,
            'data' => $data
        )));
    }


}