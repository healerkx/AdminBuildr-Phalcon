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

            $results = array();
            if (class_exists($modelName)) {
                $pri = $modelName::primaryKeyName();
                $condition = '';
                if ($field) {
                    if ($search) {
                        $condition = "$field LIKE '%{$search}%'";
                        if (is_numeric($search)) {
                            $condition .= " or $pri=$search";
                        }

                        $results = $modelName::find(array(
                            'conditions' => $condition,
                            "limit" => 20));
                        $results = $results->toArray();
                    }
                }

                // TODO: Merge two parts
                parent::result(array('results' => $results, 'SQL' => $condition, 'key' => $pri));
            } else {
                parent::error(-2, "$modelName does not exists");
            }

        } catch (Exception $e) {
            parent::error(-3, "$e");
        }
        parent::error(-1, "$modelName ?");
    }


}