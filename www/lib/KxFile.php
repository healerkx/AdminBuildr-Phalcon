<?php

/**
 * Class KxFile
 *
 * Nginx:
 * server{
 *     location /upload {
 *          root   'D:/Projects/ProductName/www';
 *      }
 * }
 */
class KxFile
{
    public static function getUploadFileName() {
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        return $fileName;
    }

    public static function upload($fileName, $pathName, $cleanupTargetDir = true)
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

        $targetDir = 'upload_tmp';
        $filePath = $targetDir . '/' . $fileName;

        $uploadPath = self::prepareUploadFileEnv($fileName, $pathName);
        // $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . '/' . $file;
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
        return array(
            'file' => $uploadPath
        );
    }

    private static function prepareUploadFileEnv($fileName, $pathName) {
        // header("HTTP/1.0 500 Internal Server Error");
        // exit;
        // 5 minutes execution time
        @set_time_limit(5 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);
        // Settings
        // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $targetDir = 'upload_tmp';
        $uploadDir = 'upload';

        $maxFileAge = 5 * 3600;     // Temp file age in seconds
        // Create target dir
        $subUploadDir = $uploadDir . '/' . $pathName;
        if (!file_exists($subUploadDir)) {
            @mkdir($subUploadDir, 0777, true);
        }

        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        return $subUploadDir . '/' . $fileName;
    }

    public static function convertFileName($fileName, $pattern)
    {
        if (empty($pattern)) {
            return $fileName;
        }

        $new = preg_replace_callback("({[\\w\\s\\[\\],-_]+})", function($p) use($fileName) {
            $a = $p[0];
            if ($a == '{md5}') {
                return md5($fileName);
            } else if ($a == '{ext}') {
                return substr(strrchr($fileName, '.'), 1);
            } else if ($a == '{origin}') {
                $ext = strrchr($fileName, '.');
                return substr($fileName, 0, strlen($fileName) - strlen($ext));
            } else if (Strings::startsWith($a, '{date')) {
                preg_match('(\\[.*\\])', $a, $p);
                $pattern = trim($p[0], "[]");
                return date($pattern, time());
            } else if ($a == '{unix_timestamp}') {
                return time();
            } else if (Strings::startsWith($a, '{random')) {
                preg_match('(\\[.*\\])', $a, $p);
                $pattern = trim($p[0], "[]");
                $r = explode(',', $pattern);
                return rand($r[0], $r[1]);
            }
            return $a;
        }, $pattern);
        return $new;
    }

}