######################################################
# Form Fields
text_field = """
{{ text_field("tagType":"abTag", 'label':'##label', 'field':'##field_name', 'value':value(i, ['##field_name'], ''), 'validate':[##validate]) }}
"""

select_field = """
{{ select("tagType":"abTag", 'label':'##label', 'data':##data, 'field':'##field_name', 'value':value(i, ['##field_name'], '')) }}
"""

date_field = """
{{ date_field("tagType":"abTag", 'label':'##label', 'field':"##field_name", "value":i['##field_name']) }}
"""

file_field = """
{{ tag_html('file_upload', ['label':'##label', 'upload_url':'##upload_url', 'field':'##field_name', 'value':value(i, ['##field_name'], '')]) }}
"""

image_field = """
{{ tag_html('img_upload', ['label':'##label', 'upload_url':'##upload_url', 'field':'##field_name', 'value':value(i, ['##field_name'], '')]) }}
"""

######################################################
# Cells and Details
text_line = """
<div class="TextLine">
    <label class="control-label">##label</label>

    <div class="controls">
        <span class="m-wrap small">{{i['##field_name']}}</span>
    </div>
</div>
"""

file_download = """
<div class="Link">
    <label class="control-label">##label</label>
    <div class="controls">
        <a href="{{i['##field_name']}}">点击下载</a>
    </div>
</div>
"""

file_cell = """<a href="{{i['##field_name']}}">下载</a>"""

image_view = """
<div class="Image">
    <label class="control-label">##label</label>
    <div class="controls">
        <img src="{{i['##field_name']}}" style="width:100px"/>
    </div>
</div>
"""

#----------------------------------------------------------
# Region about

# Cell
region_province_cell = "{{ tag_html('province_name', [i['##province_field']] ) }}"

region_city_cell = "{{ tag_html('city_name', [i['##city_field']] ) }}"

region_county_cell = "{{ tag_html('county_name', [i['##county_field']] ) }}"

region_cell = ' - '.join([region_province_cell, region_city_cell, region_county_cell])

# Detail Group Line
region_line = """
<label class="control-label">##group_name</label>
<div class="controls">
    {{ tag_html('province_name', [i['##province_field']] ) }}
    {{ tag_html('city_name', [i['##city_field']] ) }}
    {{ tag_html('county_name', [i['##county_field']] ) }}
</div>
"""
# Detail Single
region_province_item = """
<label class="control-label">##label</label>
<div class="controls">
    {{ tag_html('province_name', [i['##province_field']] ) }}
</div>
"""

region_city_item = """
<label class="control-label">##label</label>
<div class="controls">
    {{ tag_html('city_name', [i['##city_field']] ) }}
</div>
"""

region_county_item = """
<label class="control-label">##label</label>
<div class="controls">
    {{ tag_html('county_name', [i['##county_field']] ) }}
</div>
"""

region_field = """
<label class="control-label">##group_name</label>
<div class="controls">
    {{ tag_html('province', ['##province_id', '##province_field', ##province_val ] ) }}
    {{ tag_html('city', ['##city_id', '##city_field', '##province_id', ##province_val, ##city_val] ) }}
    {{ tag_html('county', ['##county_id', '##county_field', '##city_id', ##city_val, ##county_val] ) }}
</div>
"""

region_province_field = """
<label class="control-label">##label</label>
<div class="controls">
    {{ tag_html('province', ['##province_id', '##province_field', ##province_val ] ) }}
</div>
"""

region_city_field = """
<label class="control-label">##label</label>
<div class="controls">
    {{ tag_html('city', ['##city_id', '##city_field', '##province_id', ##province_val, ##city_val] ) }}
</div>
"""

region_county_field = """
<label class="control-label">##label</label>
<div class="controls">
    {{ tag_html('county', ['##county_id', '##county_field', '##city_id', ##city_val, ##county_val] ) }}
</div>
"""

#----------------------------------------------------------
# Advanced search
search_field = """
<div class="AdvancedSearch">
    <label class="control-label">##label</label>
    <div class="controls" widget-class='AdvancedSearch'>
        <select name='##field_name' search-table='##search_table' search-field='##search_field'
            class="span6 chosen" data-placeholder="选择一条记录" tabindex="1">
            <option value="0">请选择</option>
        </select>
    </div>
</div>
"""

#----------------------------------------------------------
# List search
# Policies:
# Text supports exactly search and like search
# Number supports exactly, like and range search
# Date only supports range search
# File and image do NOT support search 
text_search_range = """
<div class="Text">
    <label class="control-label">##label</label>
    <div class="controls">
        <input type="text" placeholder="FROM" class="m-wrap small" ##validate 
        name="##field_name[from]" value="{{value(i, ['##field_name', 'from'])}}" />
        <span>-</span>
        <input type="text" placeholder="TO" class="m-wrap small" ##validate 
        name="##field_name[to]" value="{{value(i, ['##field_name', 'to'])}}" />
    </div>
</div>
"""

date_search_range = """
<div class="DTP-range">
    <label class="control-label">##label</label>
    <div class="controls">
        <input class="m-wrap m-ctrl-medium date-picker" readonly
               size="16" type="text"
               name="##field_name[from]" value="{{value(i, ['##field_name', 'from'], '')}}"/>
        <span>-</span>
        <input class="m-wrap m-ctrl-medium date-picker" readonly
               size="16" type="text"
               name="##field_name[to]" value="{{value(i, ['##field_name', 'to'], '')}}"/>
    </div>
</div>
"""

unknown_field = """
<div class="unknown">
    <label class="control-label">未知类型</label>
    <div class="controls">
        <span>----</span>
    </div>
</div>
"""