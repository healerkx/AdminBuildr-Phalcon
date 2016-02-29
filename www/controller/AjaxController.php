<?php


class AjaxController extends AbBaseController
{
    public function citiesAction($provinceId) {
        parent::result(SysRegion::cities($provinceId));
    }

    public function countiesAction($cityId) {
        parent::result(SysRegion::counties($cityId));
    }

    public function searchAction() {

        try {
            $tableName = $this->request->getPost('table');
            $field = $this->request->getPost('field');
            $search = $this->request->getPost('search');
            $modelName = Strings::tableNameToModelName($tableName);

            if (class_exists($modelName)) {
                if ($field) {
                    $results = $modelName::find("$field like '$search%'");
                } else {
                    $results = $modelName::findFirst($search);
                }

                parent::result(array('results' => $results));
            } else {
                parent::error(-2, "$modelName does not exists");
            }

        } catch (Exception $e) {
            parent::error(-3, "$e");
        }
        parent::error(-1, "$modelName");
    }



}