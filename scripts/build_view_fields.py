
from build_template import *
from build_config import *
from build_view_field_templates import *

def get_region_widget_id(more, region_type):
    if 'widget_id' in more:
        return more['widget_id']
    group_id = 'u'
    if 'group_id' in more:
        group_id = more['group_id']
    
    return '%s_%s' % (region_type, group_id) 


def get_region_dict(group, view):
    province_field = None
    city_field = None
    county_field = None
    group_name = ''
    for f in group:
        more = f['more']
        if more is not None:
            if more['region_type'] == 'province':
                province_field = f['fieldName']
                if 'group_name' in more:group_name = more['group_name']
            elif more['region_type'] == 'city':
                city_field = f['fieldName']
            elif more['region_type'] == 'county':
                county_field = f['fieldName']
    group_id = 0
    if 'group_id' in more:
        group_id = more['group_id']

    province_wid = get_region_widget_id(more, 'province')
    city_wid = get_region_widget_id(more, 'city')
    county_wid = get_region_widget_id(more, 'county')

    m = {'group_name':group_name, 
        'province_id':province_wid, 
        'province_field':province_field, 
        'city_id':city_wid, 
        'city_field':city_field, 
        'county_id':county_wid, 
        'county_field':county_field,
        'province_val': 0, 'city_val': 0, 'county_val': 0 }

    if view == 'Update':
        m['province_val'] = "i['%s']" % province_field
        m['city_val'] = "i['%s']" % city_field
        m['county_val'] = "i['%s']" % county_field        
    return m

def validate_rules(m, more):
    text_type = ''
    if 'type' in more:
        text_type = more['type']

    validate = []
    if 'minlength' in more:
        validate.append("'minlength':%s" % more['minlength'])
    if 'maxlength' in more:
        validate.append("'maxlength':%s" % more['maxlength'])
    if 'min' in more and text_type in ['digits', 'number']:
        validate.append("'min':%s" % more['min'])
    if 'max' in more and text_type in ['digits', 'number']:
        validate.append("'max':%s" % more['max'])
    
    if text_type in ['email', 'url', 'mobile', 'creditcard']:
        validate.append("'%s':true" % text_type)
    
    m['validate'] = ', '.join(validate)

def get_region_html(group, view, template):
    m = get_region_dict(group, view)
    content = template.strip()
    t = MyTemplate(content)
    c = t.substitute(m)
    return c

def get_region_for_create_update(group, view):
    return get_region_html(group, view, region_field)

def get_region_for_detail(group):
    return get_region_html(group, "Detail", region_line)

def get_region_for_list(group):
    return get_region_html(group, "List", region_cell)

"""
Get a field HTML for Create and Update page
"""
def get_html_for_form_view(field_config, view):
    field_mode = field_config['fieldMode']
    more = field_config['more']
    m = dict()
    field_template = ''
    if field_mode == 'fk':
        field_template = search_field
        m['search_table'] = more['search_table']
        m['search_field'] = more['search_field']
    elif field_mode == 'text':
        validate_rules(m, more)
        field_template = text_field
    elif field_mode == 'primaryKey':
        print('primaryKey in FormView')
        field_template = text_field
    elif field_mode == 'enum':
        field_template = select_field
        more = field_config['more']
        m['data'] = more['values']
    elif field_mode == 'datetime':
        field_template = date_field
    elif field_mode == 'file':
        field_template = file_field
        m['upload_url'] = more['upload_url']
    elif field_mode == 'image':
        field_template = image_field
        m['upload_url'] = more['upload_url']
    elif field_mode == 'region':
        if more['region_type'] == 'province':
            m = get_region_dict([field_config], view)
            field_template = region_province_field
        elif more['region_type'] == 'city':
            m = get_region_dict([field_config], view)
            field_template = region_city_field
        elif more['region_type'] == 'county':
            m = get_region_dict([field_config], view)
            field_template = region_county_field

    else:
        field_template = unknown_field

    for c, v in field_config.items():
        if c == 'fieldMode':
            continue
        if c == 'fieldName':
            m['field_name'] = v
        else:
            m['label'] = field_config['fieldText']
    
    content = field_template.strip()
    t = MyTemplate(content)
    c = t.substitute(m)

    return c

"""
Get a field HTML for Detail and List page
"""
def get_html_for_show_view(field_config, view):
    m = dict()
    field_mode = field_config['fieldMode']
    more = field_config['more']

    if field_mode == 'fk':
        field_template = text_line
        m['search_table'] = more['search_table']
        m['search_field'] = more['search_field']        
    elif field_mode == 'primaryKey':
        field_template = text_line
    elif field_mode == 'enum':
        field_template = text_line
        m['data'] = more['values']
    elif field_mode == 'datetime':
        field_template = text_line
    # File
    elif field_mode == 'file':
        if view == 'Detail':
            field_template = file_download
        elif view == 'List':
            field_template = file_cell
    # Image        
    elif field_mode == 'image':
        if view == 'Detail':
            field_template = image_view
        elif view == 'List':
            field_template = file_cell            
    elif field_mode == 'text':
        field_template = text_line
    elif field_mode == 'region':
        if more['region_type'] == 'province':
            m = get_region_dict([field_config], view)
            if view == 'Detail':
                field_template = region_province_item
            elif view == 'List':
                field_template = region_province_cell
        elif more['region_type'] == 'city':
            m = get_region_dict([field_config], view)
            if view == 'Detail':
                field_template = region_city_item
            elif view == 'List':
                field_template = region_city_cell
        elif more['region_type'] == 'county':
            m = get_region_dict([field_config], view)
            if view == 'Detail':
                field_template = region_county_item
            elif view == 'List':
                field_template = region_county_cell            
    else:
        field_template = unknown_field

    for c, v in field_config.items():
        if c == 'fieldMode':
            continue
        if c == 'fieldName':
            m['field_name'] = v
        else:
            m['label'] = field_config['fieldText']
            # m['value'] = "A"

    content = field_template.strip()
    t = MyTemplate(content)
    c = t.substitute(m)

    return c


def template_from_region_group(group, view):    
    if view == "Create" or view == "Update":
        return get_region_for_create_update(group, view)
    elif view == "Detail":
        return get_region_for_detail(group)
    else:
        return get_region_for_list(group)

def template_from_field_config(field_config, view):
    if view == "Create" or view == "Update":
        return get_html_for_form_view(field_config, view)
    elif view == 'Detail' or view == "List":
        return get_html_for_show_view(field_config, view)

# Get search field by field-config
def get_search_field(field_config):
    field_mode = field_config['fieldMode']
    if field_mode in ['image', 'file']:
        return None

    more = field_config['more']    
    if 'search' not in more or more['search'] == '0':
        return None

    m = dict()
    m['label'] = field_config['fieldText']
    m['field_name'] = field_config['fieldName']
    m['value'] = ""
    field_template = ''
    search = int(more['search'])

    if field_mode == 'datetime':    # Only support range
        # assert(search == 3)
        field_template = date_search_range

    elif field_mode == 'enum':
        field_template = select_field
        m['data'] = more['values']

    elif field_mode == 'text':
        if search == 1 or search == 2: # Exactly and Like
            return get_html_for_form_view(field_config, 'Create')
        elif search == 3: # Range
            m['validate'] = ''
            field_template = text_search_range

    """
    if search == 0: # Do not search
        return None
    elif search == 1: # Exactly
        return get_html_for_form_view(field_config, 'Create')
    elif search == 2: # Like
        return ""
    elif search == 3: # Range
        return ""
    elif search == 4: # Show enum
        return ""
    """

    content = field_template.strip()
    t = MyTemplate(content)
    c = t.substitute(m)
    return c
