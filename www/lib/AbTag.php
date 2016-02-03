<?php

use \Phalcon\Tag;

class AbTag extends Tag
{
    const TagType = 'tagType';

    const AbTag = 'abTag';

    private static function isAbFormField($p) {
        if (array_key_exists(self::TagType, $p) &&
            $p[self::TagType] == self::AbTag) {
            return true;
        }
        return false;
    }

    private static function emptyHolder(&$p, $a)
    {
        foreach ($a as $i) {
            if (array_key_exists($i, $p) && $p[$i]) {
            } else {
                $p[$i] = '';
            }
        }
    }

    static public function textField($parameters)
    {
        if (self::isAbFormField($parameters)) {
            return AbTag::formTextField($parameters);
        }
        return Tag::textField($parameters);
    }

    static public function select($parameters, $data = NULL)
    {
        if (self::isAbFormField($parameters)) {
            return AbTag::formSelect($parameters);
        }
        $data = $parameters['data'];
        return Tag::select($parameters, $data);
    }

    /**********************************************************************/


    private static function formTextField($p) {
        $html = <<<HD
<div class="Select">
    <label class="control-label">{{ label}}</label>

    <div class="controls">
        <input type="text" placeholder="{{placeholder}}" class="m-wrap small" value={{value}} />
        <span class="help-inline">{{ hint }}</span>
    </div>
</div>
HD;
        self::emptyHolder($p, ['label', 'placeholder', 'value', 'hint']);
        return Strings::format($html, $p);
    }

    private static function formSelect($p) {
        $html1 = <<<HD1
<div class="Text">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <select type="text" class="m-wrap small {{searchable}}">
HD1;

        $html2 = <<<HD2
        </select>
        <span class="help-inline">{{hint}}</span>
    </div>
</div>
HD2;

        $data = $p['data'];
        unset($p['data']);
        $options = '';
        foreach ($data as $i)
        {
            $options .= '<option>' . $i . '</option>';
        }


        $html = $html1 . $options . $html2;
        self::emptyHolder($p, ['label', 'placeholder', 'value', 'hint', 'searchable']);
        if ($p['searchable'] != '') {
            $p['searchable'] = 'chosen';
        }

        return Strings::format($html, $p);
    }
}