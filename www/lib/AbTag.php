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

    static public function tagHtml($tagName, $parameters = NULL, $selfClose = NULL, $onlyStart = NULL, $useEol = NULL)
    {
        if ('province' == $tagName) {
            return self::province($parameters[0], $parameters[1]);
        } else if ('city' == $tagName) {
            return self::city($parameters[0], $parameters[1], $parameters[2]);
        } else if ('county' == $tagName) {
            return self::county($parameters[0], $parameters[1], $parameters[2]);
        }
        return Tag::tagHtml($tagName, $parameters, $selfClose, $onlyStart, $useEol);
    }

    /**********************************************************************/


    private static function formTextField($p, $itemViewMode) {
        $html = <<<HTML
<div class="Select">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <input type="text" placeholder="{{placeholder}}" class="m-wrap small" field="{{field}}" value="{{value}}" />
        <span class="help-inline">{{ hint }}</span>
    </div>
</div>
HTML;
        self::emptyHolder($p, ['label', 'placeholder', 'field', 'value', 'hint']);
        return Strings::format($html, $p);
    }

    private static function formSelect($p, $itemViewMode) {
        $html1 = <<<HTML
<div class="Text">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <select type="text" field='{{field}}' class="m-wrap small {{searchable}}">
HTML;

        $html2 = <<<HTML
        </select>
        <span class="help-inline">{{hint}}</span>
    </div>
</div>
HTML;

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

        self::emptyHolder($p, ['label', 'placeholder', 'field', 'value', 'hint', 'searchable']);
        if ($p['searchable'] != '') {
            $p['searchable'] = 'chosen';
        }

        return Strings::format($html, $p);
    }

    private static function province($widgetId, $field)
    {
        $html1 = <<<HTML
<div widget-class="RegionSelector" widget-id="{{widget_id}}" mode="province" class="pull-left margin-right-20" style="float: left">
    <select field='{$field}'>
        <option value="-1">请选择省</option>
HTML;
        $options = array();
        foreach (SysRegion::provinces() as $p) {
            $o = "<option value=\"{$p['sys_region_index']}\">{$p['sys_region_name']}</option>";
            array_push($options, $o);
        }
        $options = join('', $options);

        $html2 = <<<HTML
    </select>
</div>
HTML;
        return Strings::format($html1 . $options . $html2, array('widget_id' => $widgetId));

    }

    private static function city($widgetId, $field, $listenTo) {
        $html = <<<HTML
<div widget-class="RegionSelector" widget-id="{$widgetId}" mode="city" class="pull-left margin-right-20" listen-to="{$listenTo}">
    <select field='{$field}'>
    </select>
</div>
HTML;
    return $html;
    }

    private static function county($widgetId, $field, $listenTo) {
        $html = <<<HTML
<div widget-class="RegionSelector" widget-id="{$widgetId}" mode="county" class="pull-left margin-right-20" listen-to="{$listenTo}">
    <select field='{$field}'>
    </select>
</div>
HTML;
        return $html;
    }
}