
import sys
import os
import json
import random
from tornado.template import Template
from build_template import *
from build_config import *
from build_view_fields import *


def add_indent(s, indents="\t\t"):
    lines = s.split("\n")
    return "\n".join(map(lambda x: "%s%s" % (indents, x), lines))

def get_field_cell_html(field_names, indent=3):
    spaces = "\n" + "\t" * indent
    html = spaces.join(map(lambda x: "<td>%s</td>" % x, field_names))
    return html

def get_field_groups_html(field_groups, indent):
    indents = "\t" * indent
    newline = "\n" + "\t" * (indent - 1)
    html = newline.join(map(lambda x: "<div class=\"control-group\">\n%s%s</div>" % (add_indent(x, indents), newline), field_groups))
    return html


def hide_in(view, field_config, more):
    if field_config['fieldMode'] == 'primaryKey':
        if view in ['Create', 'Update']:
            return True
    if more is not None:
        hide = 'hideIn' + view
        if hide in more and int(more[hide]) == 1:
            return True
    return False  

def get_value(field_config):
    if field_config['fieldMode'] == 'region':
        return get_html_for_show_view(field_config, 'List')
    elif field_config['fieldMode'] == 'file' or field_config['fieldMode'] == 'image':
        return get_html_for_show_view(field_config, 'List')
    elif field_config['fieldMode'] == 'enum':
        return get_html_for_show_view(field_config, 'List')
    elif field_config['fieldMode'] == 'fk':
        more = field_config['more']
        model_name = get_module_name('', more['table'])
        value = "{{ i.%s.%s }}" % (model_name, more['display'])
        return value

    value = "{{ i.%s }}" % field_config['fieldName']
    return value

# List view
def get_field_header_and_value(fields):
    searchs = []
    headers = []
    values = []
    one_field_group = []
    group_value = []
    last_group_id = None
    for field_config in fields:
        more = field_config['more']
        if hide_in('List', field_config, more):
            continue

        group_id = more['group_id'] if 'group_id' in more else None

        if group_id != last_group_id:
            if len(one_field_group) > 0:
                if one_field_group[0]['fieldMode'] == 'region':  # Should use fieldMode
                    headers.append(one_field_group[0]['more']['group_name'])
                    values.append(template_from_region_group(one_field_group, "List"))
                    one_field_group = []
                else:
                    """ TODO: Other type group handling """
                    pass

        if group_id is not None:
            one_field_group.append(field_config)
        elif field_config['fieldMode'] != '__virtual__':
            search = get_search_field(field_config)
            if search is not None:
                searchs.append(search) 
            headers.append(field_config['fieldText'])
            values.append(get_value(field_config))

        last_group_id = group_id

    return searchs, headers, values
#
def get_field_groups_from_fields(fields, view):
    field_groups = []
    one_field_group = []
    last_group_id = None
    # current_group_type = None
    for field_config in fields:
        more = field_config['more']
        if hide_in(view, field_config, more):
            continue

        group_id = more['group_id'] if 'group_id' in more else None

        if group_id != last_group_id:
            if len(one_field_group) > 0:
                if one_field_group[0]['fieldMode'] == 'region':  # Should use fieldMode
                    field_groups.append(template_from_region_group(one_field_group, view))
                    one_field_group = []
                else:
                    """ TODO: Other type group handling """
                    print("DDD")
                    pass
        if group_id is not None:
            # current_group_type = more['group_type']
            one_field_group.append(field_config)
        else:
            if field_config['fieldMode'] != '__virtual__':
                html = template_from_field_config(field_config, view)
                field_groups.append(html)

        last_group_id = group_id

    return field_groups

def build_view_index(config, d):
    fields = config['model']['info']['FieldsConfig']

    searchs, headers, values = get_field_header_and_value(fields)
    
    d['field_search'] = get_field_groups_html(searchs, 4)
    d['field_header'] = get_field_cell_html(headers)
    d['field_row'] = get_field_cell_html(values)
    return True

def build_view_create(config, d):
    fields = config['model']['info']['FieldsConfig']

    field_groups = get_field_groups_from_fields(fields, 'Create')
    d['field_groups'] = get_field_groups_html(field_groups, 2)
    return True

def build_view_update(config, d):
    fields = config['model']['info']['FieldsConfig']

    field_groups = get_field_groups_from_fields(fields, 'Update')
    d['field_groups'] = get_field_groups_html(field_groups, 2)
    return True

def build_view_detail(config, d):
    fields = config['model']['info']['FieldsConfig']

    field_groups = get_field_groups_from_fields(fields, 'Detail')
    d['field_groups'] = get_field_groups_html(field_groups, 2)
    return True
    

def build_view(config):

    model = config['model']
    module_name = config['module_name']
    primary_key = model['info']['PrimaryKey']
    table_name = config['table_name']
    path = config['product']['path']
    d = init_dict()
    d['title'] = module_name
    d['model_name'] = module_name
    d['model_path_name'] = module_name.lower()
    d['table_name'] = table_name
    d['primary_key'] = primary_key
    d['target_field'] = primary_key
    d['field_for_delete'] = 'status'    # TODO:
    d['value_for_delete'] = 0
    d['search_row'] = ''
 
    module_path = module_name.lower()
    full_path = os.path.join(path, 'www\\view', module_path)
    if not os.path.exists(full_path):
        os.mkdir(full_path)

    view_index_filename = os.path.join(full_path, "index.phtml")
    view_index_js_filename = os.path.join(full_path, "index.js.phtml")
    

    # List
    build_view_index(config, d)
    create_file_from_string_template("view_index.tpl.html", d, view_index_filename)
    create_file_from_string_template("view_index_js.tpl.html", d, view_index_js_filename)

    # create
    build_view_create(config, d)
    view_create_filename = os.path.join(full_path, "create.phtml")
    create_file_from_string_template("view_create.tpl.html", d, view_create_filename)
    
    # update
    build_view_update(config, d)
    view_update_filename = os.path.join(full_path, "update.phtml")
    create_file_from_string_template("view_update.tpl.html", d, view_update_filename)

    # module js
    build_view_update(config, d)
    view_module_filename = os.path.join(full_path, "%s.js.phtml" % module_name.lower())
    create_file_from_string_template("view_module_js.tpl.html", d, view_module_filename)


    # detail
    build_view_detail(config, d)
    view_detail_filename = os.path.join(full_path, "detail.phtml")
    create_file_from_string_template("view_detail.tpl.html", d, view_detail_filename)

    # confirm
    view_confirm_filename = os.path.join(full_path, "confirm.phtml")
    create_file_from_string_template("view_confirm.tpl.html", d, view_confirm_filename)

    return [
        view_index_filename,
        view_index_js_filename,
        view_create_filename,
        view_update_filename,
        view_detail_filename,
        view_confirm_filename]

def build_view_by_config(config):
    return build_view(config)

if __name__ == '__main__':
    config = load_config()

    build_view_by_config(config)

