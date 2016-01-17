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

            $tableNames = $this->tableNames();

            $data = array('table_names' => $tableNames);

            parent::show('module/create_curd_by_model', $data);
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

        $path = AdminBuilderConfig::getConfig('product')['path'] . '\\www';

        $this->createModelConfigFile($path, $modelName, $p);

        $cmdLine = "--prefix=$prefix --table=$tableName --config=\"$path\"";
        $c = Python3::run("build_mvc.py", $cmdLine);

        parent::result(array(
            'model' => $modelName,
            'files' => array('a', 'b'),
            'build' => $c));
    }

    public function infoAction() {
        $modelName = $this->request->get('model');
        $tableName = $this->request->get('table');

        $a = $this->db->fetchAll("SHOW FULL COLUMNS FROM $tableName");
        $data = array();
        $fields = array();
        foreach ($a as $i) {

            array_push($fields, $i);
        }

        $data['fields'] = $fields;

        parent::result($data);

    }

    private function tableNames() {
        $a = $this->db->fetchAll("SHOW tables");
        $tableNames = array();
        foreach ($a as $table) {
            array_push($tableNames, $table['Tables_in_badmin']);
        }
        return $tableNames;
    }

    private function getTableInfo($tableName) {
        $this->db->execute('select create table');
    }

    private function createModelConfigFile($path, $modelName, $data) {
        $workingModelFile = "$path\\model\\config\\{$modelName}.json";

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