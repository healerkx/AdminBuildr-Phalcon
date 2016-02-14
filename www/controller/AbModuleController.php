<?php

/**
 * Class ModuleController
 * A abmodule means a bundle of CURD
 */

class AbModuleController extends AbBaseController
{
    public function indexAction()
    {
        parent::show('abmodule/index', false);
    }

    /**
     * @param string $method
     */
    public function createAction($method='curdFromModel') {
        if ($method == 'curdFromModel') {
            $this->createCurdFromModel();
        } else {

        }
    }

    private function createCurdFromModel() {
        $tableNames = $this->tableNames();

        $views = [
            ["name" =>'新建模块', "template"=> "abmodule/new_curd"] ,
            ["name" =>'预览', 'id' => 'preview', "template" => "abmodule/new_curd_preview"]];

        parent::addDialog('下拉框设置', 'abmodule/dialog-select');
        parent::addDialog('文本框设置', 'abmodule/dialog-text');
        parent::addDialog('关联ID设置', 'abmodule/dialog-fk');
        parent::addDialog('行政区设置', 'abmodule/dialog-region');

        $data = array('table_names' => $tableNames);
        $this->session->set('a', '323');
        parent::showTabViews($views, '创建CURD模块', $data);
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

        $path = ApplicationConfig::getConfig('product')['path'] . '\\www';

        $this->createModelConfigFile($path, $modelName, $p);

        $configPath = ApplicationConfig::getConfigPath('config.json');
        $cmdLine = "--prefix=$prefix --table=$tableName --config=\"$configPath\"";

        $c = Python3::run("build_mvc.py", $cmdLine);

        $targetHost = ApplicationConfig::getConfig('product')['host'];

        $testListUrl = "$targetHost/$modelName";
        parent::result(array(
            'model' => $modelName,
            'files' => json_decode($c),
            'cmd_line' => $cmdLine,
            'test_list_url' => $testListUrl,
            'build' => $c));
    }

    /**
     * @access Guest
     */
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

        $content = json_encode($data, JSON_UNESCAPED_UNICODE);
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