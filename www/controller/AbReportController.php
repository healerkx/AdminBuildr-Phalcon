<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2016/1/15
 * Time: 19:28
 */
class AbReportController extends AbBaseController
{
    public function createAction()
    {
        $tableNames = $this->tableNames();
        $data = array('table_names' => $tableNames);

        $views = [
            ["name" =>'新建报表', "template"=> "abreport/create"] ];

        parent::addDialog('添加列', 'abreport/dialog-new-row');
        parent::showTabViews($views, '创建报表', $data);
    }

    private function tableNames() {
        $a = $this->db->fetchAll("SHOW tables");
        $tableNames = array();
        foreach ($a as $table) {
            array_push($tableNames, $table['Tables_in_badmin']);
        }
        return $tableNames;
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

        $this->createReportConfigFile($path, $modelName, $p);

        $configPath = ApplicationConfig::getConfigPath('config.json');
        $cmdLine = "--prefix=$prefix --table=$tableName --config=\"$configPath\"";

        $c = Python3::run("build_report.py", $cmdLine);
        $targetHost = ApplicationConfig::getConfig('product')['host'];

        $testListUrl = "$targetHost/$modelName";
        parent::result(array(
            'model' => $modelName,
            'files' => json_decode($c),
            'cmd_line' => $cmdLine,
            'test_list_url' => $testListUrl,
            'build' => $c));
    }

    private function createReportConfigFile($path, $modelName, $data) {
        $workingModelFile = "$path\\model\\report\\{$modelName}.json";

        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($workingModelFile, $content);
        return true;
    }

}