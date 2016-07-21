<?php

/**
 * Class ModuleController
 * A abmodule means a bundle of CURD
 */

class AbModuleController extends AbBaseController
{
    /**
     * @param string $method
     * @page
     * @comment CURD入口
     */
    public function indexAction($method='curdFromModel') {
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
        parent::addDialog('时间选择设置', 'abmodule/dialog-datetime');
        parent::addDialog('关联ID设置', 'abmodule/dialog-fk');
        parent::addDialog('主键ID设置', 'abmodule/dialog-pk');
        parent::addDialog('关联ID设置', 'abmodule/dialog-extend');
        parent::addDialog('行政区设置', 'abmodule/dialog-region');
        parent::addDialog('文件上传设置', 'abmodule/dialog-file');
        parent::addDialog('图片上传设置', 'abmodule/dialog-img');

        $uploadConfigs = KxUploadConfig::find();
        $uploadUrls = array();
        foreach ($uploadConfigs->toArray() as $cfg) {
            $uploadUrls[] = $cfg['url'];
        }

        $enumDefines =

        $data = array(
            'table_names' => $tableNames,
            'enum_defines' => AbEnumController::getEnumClasses(),
            'upload_urls' => $uploadUrls);
        // $this->session->set('a', '323');
        parent::showTabViews($views, '创建CURD模块', $data);
    }

    /**
     * @comment 更新入口(Test)
     */
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

        $modelName = Strings::tableNameToModelName($tableName);

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
     * @param $path
     * @param $modelName
     * @param $data
     * @return bool
     * @PHP 5.4+ for JSON_PRETTY_PRINT
     */
    private function createModelConfigFile($path, $modelName, $data) {
        $workingModelFile = "$path\\model\\config\\{$modelName}.json";

        $json = self::convert($data);
        $content = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($workingModelFile, $content);
        return true;
    }

    private static function convert($data)
    {
        $json = $data;
        $info = &$json['info'];
        if (!array_key_exists('FieldsConfig', $info)) {
        }

        $fieldsConfig = &$info['FieldsConfig'];

        foreach ($fieldsConfig as &$fieldConfig)
        {
            // 针对enum select的处理
            if ($fieldConfig['fieldMode'] == 'enum')
            {
                if (array_key_exists('enum', $fieldConfig['more']))
                {
                    $enumDefine = $fieldConfig['more']['enum'];
                    $enums = AbEnumController::getEnum($enumDefine);
                    $fieldConfig['more']['values'] = $enums;
                }
            }
        }

        return $json;
    }



}