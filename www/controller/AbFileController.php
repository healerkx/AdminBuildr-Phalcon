<?php


class AbFileController extends AbBaseController
{
    private $cleanupTargetDir = true;

    private $filePattern = "";

    private $pathPattern = "a/b/c{date[Y-m]}";

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
        $path = $this->request->getPost('path');
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
        $cmdLine = "--name=$controller --path=$path --filename-pattern=$filenamePattern --subdir-pattern=$subdirPattern --config=\"$configPath\"";

        $c = Python3::run("build_upload_controller.py", $cmdLine);
        return parent::result($c);
    }

    public function addControllerAction()
    {
        $url = $this->request->getPost('url');
        $filenamePattern = $this->request->getPost('filename_pattern');
        $subdirPattern = $this->request->getPost('subdir_pattern');
        $cfg = new KxUploadConfig();
        $cfg->url = $url;
        $cfg->filename_pattern = $filenamePattern;
        $cfg->subdir_pattern = $subdirPattern;
        $cfg->status = 1;
        $time = date('Y-m-d h:i:s');
        $cfg->create_time = $time;
        $cfg->update_time = $time;

        $r = $cfg->save();
        if ($r) {
            return parent::result(array('result' => $r, 'post' => $this->request->getPost()));
        }
        // TODO:
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