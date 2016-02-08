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
            $view = KxApplication::current()->view;
            $itemViewMode = $view->getVar('itemViewMode');
            return AbTag::formTextField($parameters, $itemViewMode);
        }
        return Tag::textField($parameters);
    }

    static public function select($parameters, $data = NULL)
    {
        if (self::isAbFormField($parameters)) {
            $view = KxApplication::current()->view;
            $itemViewMode = $view->getVar('itemViewMode');
            return AbTag::formSelect($parameters, $itemViewMode);
        }
        $data = $parameters['data'];
        return Tag::select($parameters, $data);
    }

    /**********************************************************************/


    private static function formTextField($p, $itemViewMode) {
        $html = <<<HD
<div class="Select">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <input type="text" placeholder="{{placeholder}}" class="m-wrap small" value="{{value}}" />
        <span class="help-inline">{{ hint }}</span>
    </div>
</div>
HD;
        self::emptyHolder($p, ['label', 'placeholder', 'value', 'hint']);
        return Strings::format($html, $p);
    }

    private static function formSelect($p, $itemViewMode) {
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
        $options = '<option value="0">请选择</option>';
        foreach ($data as $i)
        {
            $v = $i;
            if (!is_array($i)) {
                $v = array(
                    'name' => $i, 'value' => $i
                );
            }

            $option = '<option value="{{value}}">{{name}}</option>';
            $options .= Strings::format($option, $v);
        }

        $html = $html1 . $options . $html2;

        self::emptyHolder($p, ['label', 'placeholder', 'value', 'hint', 'searchable']);
        if ($p['searchable'] != '') {
            $p['searchable'] = 'chosen';
        }

        return Strings::format($html, $p);
    }
}