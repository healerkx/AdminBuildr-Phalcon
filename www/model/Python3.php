<?php


class Python3
{
    /**
     * @param $pythonFile
     * @param $params
     * @return string
     */
    public static function run($pythonFile, $params=false) {
        $builder = Config::getConfig('builder');
        $builderPath = $builder['scripts-path'];

        return exec("python $builderPath\\$pythonFile $params");
    }
}