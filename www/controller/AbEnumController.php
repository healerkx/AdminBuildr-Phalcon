<?php
/**
 * Created by PhpStorm.
 * User: healer_kx@163.com
 * Date: 2016/7/15
 * Time: 17:05
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

        $productPath = ApplicationConfig::getConfig('product')['path'];
        $classNamePath = $productPath . '/www/defines';

        $filePaths = scandir($classNamePath);

        $fileNames = [];
        foreach ($filePaths as $filePath) {
            if ($filePath == '.' || $filePath == '..') {
                continue;
            }
            $fileNames[] = ['className' => $filePath];
        }


        $data = array(
            'classNames' => $fileNames
        );
        parent::addDialog('Action属性', 'abenum/settings');
        parent::showTabViews($views, '常量定义管理', $data);
    }


    public function updateAction()
    {
        $name = $this->request->getPost('name');
        $enums = $this->request->getPost('enums');

        echo json_encode($enums);
        //var_dump($enums);

    }


}