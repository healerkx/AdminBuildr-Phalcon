<?php


class {{ enum_name }}
{
{% for item in array %}
    // {{item['display']}}
    const {{item['name']}} = {{item['value']}};
{% end %}

    private static $array = array(
{% for item in array %}
        self::{{item['name']}} => array('desc' => '{{item['display']}}'),
{% end %}
    );

    public static function items()
    {
        return self::$array;
    }
}