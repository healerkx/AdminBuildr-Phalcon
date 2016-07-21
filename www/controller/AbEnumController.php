<?php
/**
 * Created by PhpStorm.
 * User: healer_kx@163.com
 * Date: 2016/07/15
 * 枚举类型支持，用于Form中select的options支持。
 */
class AbEnumController extends AbBaseController
{
    /**
     * List all Enum Classes
     */
    public function indexAction()
    {
        $views = [
            ["name" =>'常量定义', "template" => "abenum/list"]];

        $classNames = self::getEnumClasses();

        $data = array(
            'classNames' => $classNames
        );
        parent::addDialog('Action属性', 'abenum/settings');
        parent::showTabViews($views, '常量定义管理', $data);
    }


    public function updateAction()
    {
        $name = $this->request->getPost('name');
        $enums = $this->request->getPost('enums');

        if (!$name) {
            parent::error(1, array('msg' => 'None name error'));
            return;
        }

        $classNamePath = self::getEnumPath();

        $jsonName = "{$classNamePath}/const/{$name}.json";

        file_put_contents($jsonName, json_encode($enums));

        $cmdLine = "--json=$jsonName";
        $results = Python3::run('build_enum.py', $cmdLine);

        parent::result(array(
            'file' => $jsonName, 'results' => $results));
    }

    public static function getEnumPath()
    {
        $productPath = ApplicationConfig::getConfig('product')['path'];
        $classNamePath = $productPath . '/www/defines';
        return $classNamePath;
    }

    public static function getEnumClasses()
    {
        $classNamePath = self::getEnumPath();

        $filePaths = scandir($classNamePath);

        $fileNames = [];
        foreach ($filePaths as $filePath) {
            if ($filePath == '.' || $filePath == '..' || $filePath == 'const') {
                continue;
            }

            $className = substr($filePath, 0, -4);
            $fileNames[] = ['className' => $className];
        }

        return $fileNames;
    }

    public static function getEnum($name)
    {
        $classNamePath = self::getEnumPath();

        $jsonName = "{$classNamePath}/const/{$name}.json";

        $contents = file_get_contents($jsonName);

        return json_decode($contents);
    }


}