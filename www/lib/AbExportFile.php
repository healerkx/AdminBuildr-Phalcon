<?php

/**
 * Created by PhpStorm.
 * User: healer_kx@163.com
 * Date: 2016/4/15
 * Time: 9:05
 */
class AbExportFile
{
    public static function exportToCsvFile($fileName)
    {
        Header("Content-type: application/octet-stream;charset=UTF-8");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:-1");
        Header("Content-Disposition: attachment; filename=" . $fileName);
        if (true || $windows) {
            echo pack('H*','EFBBBF');   // 写入 BOM header for UTF8 files.
        }

    }

    public static function exportToExcelFile($fileName)
    {
        // TODO:
    }
}