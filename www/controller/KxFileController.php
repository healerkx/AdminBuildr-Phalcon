<?php


class KxFileController extends AbBaseController
{
    private $cleanupTargetDir = true;

    private $filePattern = "";   // TODO:

    private $pathPattern = "";              // TODO:

    public function manageAction()
    {
        $views = [
            ["name" =>'上传管理', "template" => "kxfile/manage"]];

        $data = array(
            'actions' => array(array('action' => 'kxFile/upload', 'path' => 'abc'))
        );
        parent::addDialog('Action属性', 'kxfile/settings');
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
        $controller = $this->request->getPost('controller');
        $path = ApplicationConfig::getConfigPath('upload-file.json');

        $c = array();
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $c = json_decode($content, true);
        }

        $c[] = $controller;
        $content = json_encode($c);
        file_put_contents($path, $content);

        return parent::result($controller);
    }

    public function uploadAction()
    {
        $uploadFileName = KxFile::getUploadFileName();
        $fileName = KxFile::convertFileName($uploadFileName, $this->filePattern);
        $pathName = KxFile::convertFileName('', $this->pathPattern);
        KxFile::upload($fileName, $pathName);
    }

    public function getAction()
    {
        $fileName = "123456.jpg";
        $b = KxFile::convertFileName($fileName, $this->filePattern);
        $pathName = KxFile::convertFileName('', $this->pathPattern);
        var_dump(json_encode(array($b, $pathName)));
    }

    public function uploadDemoAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        if (!empty($_REQUEST['debug'])) {
            $random = rand(0, intval($_REQUEST['debug']));
            if ($random === 0) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        self::prepareUploadFileEnv();

        // Get a file name
        $debug = "";
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
            $debug = "1($fileName)";
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
            $debug = "2($fileName)";
        } else {
            $fileName = uniqid("file_");
            $debug = "3($fileName)";
        }

        file_put_contents("d:\\a.txt", $debug, FILE_APPEND);

        $targetDir = 'upload_tmp';
        $uploadDir = 'upload';
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // Remove old temp files
        if ($this->cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                $maxFileAge = 5 * 3600;
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $done = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (!file_exists("{$filePath}_{$index}.part")) {
                $done = false;
                break;
            }
        }
        if ($done) {
            if (!$out = @fopen($uploadPath, "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
            if (flock($out, LOCK_EX)) {
                for ($index = 0; $index < $chunks; $index++) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);
        }

        // Return Success JSON-RPC response
        parent::result(array(
            'file' => $uploadPath
        ));

    }


}