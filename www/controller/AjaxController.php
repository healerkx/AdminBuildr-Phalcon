<?php


class AjaxController extends AbBaseController
{
    public function citiesAction($provinceId) {
        parent::result(SysRegion::cities($provinceId));
    }

    public function countiesAction($cityId) {
        parent::result(SysRegion::counties($cityId));
    }

    public function sAction() {
        $a = KxAdminUser::find(array('conditions' => "username like 'a%'", "limit" => 20));
        //var_dump($a);
        echo count($a);
        $a = $a->toArray();
        parent::error(-2, $a);
    }

    public function searchAction() {

        try {
            $tableName = $this->request->getPost('table');
            $field = $this->request->getPost('field');
            $search = $this->request->getPost('search');
            $modelName = Strings::tableNameToModelName($tableName);

            $results = array();
            if (class_exists($modelName)) {

                if (is_numeric($search) && $search > 0) {
                    $results = $modelName::findFirst($search);
                    $p1 = $results->toArray();
                }

                if ($field) {
                    if ($search) {
                        $results = $modelName::find(array('conditions' => "$field LIKE '%$search%'", "limit" => 20));
                        $p2 = $results->toArray();
                    }
                }

                // TODO: Merge two parts
                parent::result(array('results' => $p2, 'SQL' => "$modelName.($field) like '%$search%'", 'p' => $this->request->getPost()));
            } else {
                parent::error(-2, "$modelName does not exists");
            }

        } catch (Exception $e) {
            parent::error(-3, "$e");
        }
        parent::error(-1, "$modelName");
    }


}