<?php

use \Phalcon\Tag;

class AbTag extends Tag
{
    const TagType = 'tagType';

    const AbTag = 'abTag';

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

    static public function dateField($parameters)
    {
        if (self::isAbFormField($parameters)) {
            $view = KxApplication::current()->view;
            $itemViewMode = $view->getVar('itemViewMode');
            return AbTag::formDateField($parameters, $itemViewMode);
        }

        return Tag::dateField($parameters);
    }

    static public function tagHtml($tagName, $parameters = NULL, $selfClose = NULL, $onlyStart = NULL, $useEol = NULL)
    {
        if ('province' == $tagName) {
            return self::province($parameters[0], $parameters[1], $parameters[2]);
        } else if ('city' == $tagName) {
            return self::city($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]);
        } else if ('county' == $tagName) {
            return self::county($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]);
        } else if ('img_upload' == $tagName) {
            return self::imageUpload($parameters);
        } else if ('file_upload' == $tagName) {
            return self::fileUpload($parameters);
        } else if ('file_download' == $tagName) {
            return self::fileDownload($parameters);
        } else if ('province_name' == $tagName) {
            return self::provinceName($parameters);
        } else if ('city_name' == $tagName) {
            return self::cityName($parameters);
        } else if ('county_name' == $tagName) {
            return self::countyName($parameters);
        }
        return Tag::tagHtml($tagName, $parameters, $selfClose, $onlyStart, $useEol);
    }

    /**********************************************************************/
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

    private static function formTextField($p, $itemViewMode) {
        $html = <<<HTML
<div class="Text">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <input type="text" placeholder="{{placeholder}}" class="m-wrap small" {{validate}} name="{{field}}" value="{{value}}" />
        <span class="help-inline"></span>
    </div>
</div>
HTML;
        self::emptyHolder($p, ['label', 'placeholder', 'field', 'value', 'validate']);
        $p['validate'] = self::dataRules($p['validate']);

        return Strings::format($html, $p);
    }

    private static function formSelect($p, $itemViewMode) {
        $html1 = <<<HTML
<div class="Select">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <select type="text" name='{{field}}' class="m-wrap small {{searchable}}">
HTML;

        $html2 = <<<HTML
        </select>
        <span class="help-inline"></span>
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

        self::emptyHolder($p, ['label', 'placeholder', 'field', 'value', 'searchable']);
        if ($p['searchable'] != '') {
            $p['searchable'] = 'chosen';
        }

        return Strings::format($html, $p);
    }

    private static function formDateField($params)
    {
        $html = <<<HTML
<div class="DTP">
    <label class="control-label">{{label}}</label>
    <div class="controls">
        <input class="m-wrap m-ctrl-medium date-picker" readonly
               placeholder="{{placeholder}}" size="16" type="text"
               name="{{field}}" value="{{value}}"/>
    </div>
</div>
HTML;
        self::emptyHolder($params, ['label', 'placeholder', 'field', 'value']);
        return Strings::format($html, $params);
    }

    private static function province($widgetId, $field, $initValue)
    {
        $html1 = <<<HTML
<div widget-class="RegionSelector" widget-id="{{widget_id}}" mode="province" class="pull-left margin-right-20" style="float: left">
    <select name='{$field}'>
        <option value="-1">请选择省</option>
HTML;
        $options = array();
        foreach (SysRegion::provinces() as $p) {
            $selected = '';
            if ($initValue == $p['sys_region_index']) {
                $selected = 'selected';
            }
            $o = "<option value=\"{$p['sys_region_index']}\" $selected>{$p['sys_region_name']}</option>";
            array_push($options, $o);
        }
        $options = join('', $options);

        $html2 = <<<HTML
    </select>
</div>
HTML;
        return Strings::format($html1 . $options . $html2, array('widget_id' => $widgetId));

    }

    private static function city($widgetId, $field, $listenTo, $provinceVal, $cityVal) {
        $html = <<<HTML
<div widget-class="RegionSelector" widget-id="{$widgetId}" mode="city" class="pull-left margin-right-20" listen-to="{$listenTo}">
    <select name='{$field}'>
        <option value="-1">请选择市</option>
        <__OPTIONS__/>
    </select>
</div>
HTML;
        $optionsHtml = '';
        if ($provinceVal && $provinceVal > 0) {
            $options = array();
            foreach (SysRegion::cities($provinceVal) as $p) {
                $selected = '';
                if ($cityVal == $p['sys_region_index']) {
                    $selected = 'selected';
                }
                $o = "<option value=\"{$p['sys_region_index']}\" $selected>{$p['sys_region_name']}</option>";
                array_push($options, $o);
            }
            $optionsHtml = join('', $options);
        }

        $html = str_replace('<__OPTIONS__/>', $optionsHtml, $html);

        return $html;
    }

    private static function county($widgetId, $field, $listenTo, $cityVal, $countyVal) {
        $html = <<<HTML
<div widget-class="RegionSelector" widget-id="{$widgetId}" mode="county" class="pull-left margin-right-20" listen-to="{$listenTo}">
    <select name='{$field}'>
        <option value="-1">请选择区</option>
        <__OPTIONS__/>
    </select>
</div>
HTML;
        $optionsHtml = '';
        if ($cityVal && $cityVal > 0) {
            $options = array();
            foreach (SysRegion::counties($cityVal) as $p) {
                $selected = '';
                if ($countyVal == $p['sys_region_index']) {
                    $selected = 'selected';
                }
                $o = "<option value=\"{$p['sys_region_index']}\" $selected>{$p['sys_region_name']}</option>";
                array_push($options, $o);
            }
            $optionsHtml = join('', $options);
        }
        $html = str_replace('<__OPTIONS__/>', $optionsHtml, $html);
        return $html;
    }

    private static function imageUpload($parameters) {
        $html = <<<HTML
<label class="control-label">{{label}}</label>
<div id="uploader3" class="controls" widget-class="ImageUploader">
    <div class="thumbnail" style="width: 120px; height: 120px;margin-bottom: 10px">
    </div>
    <div>
        <div class="picker" upload-url="{{upload_url}}">选择文件</div><a class="btn btn-default upload" style="display: none">开始上传</a>
    </div>
    <input type="hidden" class="file-path" name="{{field}}"/>
</div>
HTML;
        self::emptyHolder($parameters, ['label', 'field', 'upload_url']);
        return Strings::format($html, $parameters);
    }

    private static function fileUpload($parameters) {
        $html = <<<HTML
<label class="control-label">{{label}}</label>
<div class="controls" widget-class="FileUploader">
    <div class="fileupload fileupload-new" data-provides="fileupload">
        <div class="uneditable-input pull-left">
            <i class="icon-file fileupload-exists"></i>
            <span class="fileupload-preview" style="background-color: transparent!important;"></span>
            <DIV class="progress" style="margin-top: -20px;display: none">
                <div class="bar" style="width: 1%"></div>
            </DIV>
        </div>

        <div class="pull-left">
            <div class="picker" upload-url="{{upload_url}}">选择文件</div>
            <a class="btn btn-default upload" style="display: none">开始上传</a>
        </div>
    </div>
    <input type="hidden" class="file-path" name="{{field}}"/>
</div>
HTML;
        self::emptyHolder($parameters, ['label', 'field', 'upload_url']);
        return Strings::format($html, $parameters);
    }

    /**
     * @param $parameters
     * @return mixed
     * Deprecated
     */
    private static function fileDownload($parameters) {
        $html = <<<HTML
<div class="Link">
    <label class="control-label">{{label}}</label>

    <div class="controls">
        <a href="{{link}}">{{name}}</a>
    </div>
</div>
HTML;

        self::emptyHolder($parameters, ['label', 'link']);
        if (!$parameters['name']) {
            $parameters['name'] = $parameters['link'];
        }
        return Strings::format($html, $parameters);
    }

    private static function provinceName($parameters)
    {
        return self::regionName($parameters[0]);
    }

    private static function cityName($parameters)
    {
        return self::regionName($parameters[0]);
    }
    private static function countyName($parameters)
    {
        return self::regionName($parameters[0]);
    }

    private static function regionName($regionIndex) {
        $html = "<span class='m-wrap'>{{name}}</span>";
        $region = SysRegion::findFirst("sys_region_index={$regionIndex}");
        $regionName = '';
        if ($region)
            $regionName = $region->sys_region_name;
        return Strings::format($html, array('name' => $regionName));
    }

    private static function dataRules($array)
    {
        $array = $array ?: array();
        $rules = array();
        foreach ($array as $key => $value) {
            //$value = 1? "'true'";
            if (is_numeric($key) && in_array($value, array("required", "email"))) {
                array_push($rules, "data-rule-$value='true'");
                continue;
            }
            array_push($rules, "data-rule-$key='$value'");
        }
        return implode(' ', $rules);
    }
}