<?php


class Filter
{
    private $compiler;

    public function __construct($compiler)
    {
        $this->compiler = $compiler;
    }

    public function init()
    {
        $this->compiler->addFunction('has', 'Filter::has');
        $this->compiler->addFunction('value', 'Filter::value');
        $this->compiler->addFunction('extract', 'Filter::extract');
        $this->compiler->addFilter('reverse', 'Filter::reverse');
        $this->compiler->addFilter('invoke', 'Filter::invoke');
    }

    public static function has($obj, $field) {
        return array_key_exists($field, $obj);
    }

    public static function value($obj, $dim, $default = '') {
        if (empty($dim)) {
            return $default;
        }
        $v = $obj;
        foreach ($dim as $i) {
            if (array_key_exists($i, $v)) {
                $v = $v[$i];
            } else {
                return $v;
            }
        }
        return $v;
    }

    public static function extract($obj, $field, $defVal) {
        if (array_key_exists($field, $obj) && strlen($obj[$field]) > 0) {
            return $obj[$field];
        }
        return $defVal;
    }

    public static function reverse($array) {
        return array_reverse($array);
    }

    public static function invoke($obj) {


    }


}