<?php


class AbFileController extends AbBaseController
{
    private $cleanupTargetDir = true;

    private $filePattern = "{md5}.{ext}";

    private $pathPattern = "example/D{date[Y-m]}";

    public function manageAction()
    {
        $views = [
            ["name" =>'上传管理', "template" => "abfile/manage"]];

        $cfgs = KxUploadConfig::find();
        $actions = $cfgs->toArray();

        $data = array(
            'actions' => $actions
        );
        parent::addDialog('Action属性', 'abfile/settings');
        parent::showTabViews($views, '文件上传管理', $data);
    }

    public function buildAction()
    {
        $overwrite = $this->request->getPost('overwrite');
        $controller = $this->request->getPost('controller');
        $filenamePattern = $this->request->getPost('filename_pattern');
        $subdirPattern = $this->request->getPost('subdir_pattern');

        //return parent::error(-1, $overwrite);
        if (!$controller) {
            return parent::error(-1, false);
        }

        $controllerFileName = '';
        if (file_exists($controllerFileName) && $overwrite == 'false') {
            return parent::error(-2, "$controllerFileName can NOT be overwrite");
        }

        $configPath = ApplicationConfig::getConfigPath('config.json');
        $cmdLine = "--name=$controller --filename-pattern=$filenamePattern --subdir-pattern=$subdirPattern --config=\"$configPath\"";

        $c = Python3::run("build_upload_controller.py", $cmdLine);

        $this->addFileUploaderModel($controller, $filenamePattern, $subdirPattern);
        return parent::result(array('post' => $this->request->getPost()));
    }

    public function addFileUploaderModel($url, $filenamePattern, $subdirPattern)
    {
        $data = array(
            'url' => $url,
            'filename_pattern' => $filenamePattern,
            'subdir_pattern' => $subdirPattern
        );

        $path = ApplicationConfig::getConfig('product')['path'] . '\\www\\model\\file-upload\\';

        return $this->createUploadPolicyFile($path, $url, $data);
    }

    private function createUploadPolicyFile($path, $url, $data)
    {
        $fileName = $path . "{$url}.json";
        file_put_contents($fileName, json_encode($data), JSON_PRETTY_PRINT);
        return true;
    }

    /**
     * Demo code.
     */
    public function uploadAction()
    {
        $uploadFileName = KxFile::getUploadFileName();
        $fileName = KxFile::convertFileName($uploadFileName, $this->filePattern);
        $pathName = KxFile::convertFileName('', $this->pathPattern);

        KxFile::upload($fileName, $pathName, $this->cleanupTargetDir);
    }

    /**
     * Test code for convertFileName from pattern
     */
    public function getAction()
    {
        $fileName = "a/123456.jpg";
        $b = KxFile::convertFileName($fileName, $this->filePattern);
        $pathName = KxFile::convertFileName('.', $this->pathPattern);
        var_dump(json_encode(array($b, $pathName)));
    }
}