<?php

/**
 * Class ModuleController
 * A module means a bundle of CURD
 */


class ModuleController extends AbBaseController
{

    public function indexAction()
    {
        parent::show('module/index', false);
    }

    public function createAction($method='curdByModel') {
        if ($method == 'curdByModel') {

            parent::show('module/create_curd_by_model', false);
        } else {

        }
    }

    public function updateAction() {
        parent::result(array('a' => 3));
    }

    public function deleteAction() {
        parent::result(array('a' => 4));
    }

    public function previewAction() {
        $p = $this->request->getPost();
        $prefix = $p['prefix'];
        $tableName = $p['table_name'];
        if ($prefix) {
            $tableName = "{$prefix}_{$tableName}";
        }

        $modelName = self::tableNameToModelName($tableName);

        $controllerName = $modelName . "Controller";

        $workbenchPath = Config::getConfig('product')['path'] . '\\workbench';

        // Create model config file to workbench
        $this->createModelConfigFile($workbenchPath, $modelName, $p);

        // generate temp files to preview
        $c = Python3::run("build_controller.py", "--module=$modelName --workbench=\"$workbenchPath\"");
        $m = Python3::run("build_model.py", "--module=$modelName --workbench=\"$workbenchPath\"");

        parent::result(array(
            'model' => $modelName,
            'controller' => $controllerName,
            'build_controller' => $c));
    }

    public function infoAction() {
        $modelName = $this->request->get('model');
        $tableName = $this->request->get('table');

        $a = $this->db->fetchAll("describe $tableName");
        $data = array();
        $fields = array();
        foreach ($a as $i) {

            array_push($fields, $i);
        }

        $data['fields'] = $fields;

        parent::result($data);

    }

    private function getTableInfo($tableName) {
        $this->db->execute('select create table');
    }

    private function createModelConfigFile($workbenchPath, $modelName, $data) {
        $workingModelFile = "$workbenchPath\\model\\config\\{$modelName}.json";

        $content = json_encode($data);
        file_put_contents($workingModelFile, $content);
        return true;
    }

    private static function tableNameToModelName($tableName) {
        $modelName = preg_replace_callback("/_([a-z])/", function($a) {
            return strtoupper($a[1]);
        }, $tableName);
        $modelName = ucfirst($modelName);
        return $modelName;
    }
}