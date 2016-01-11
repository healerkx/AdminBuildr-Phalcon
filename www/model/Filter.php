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
        $this->compiler->addFilter('reverse', 'Filter::reverse');
    }

    public static function has($obj, $field) {
        return array_key_exists($field, $obj);
    }

    public static function reverse($array) {
        return array_reverse($array);
    }


}