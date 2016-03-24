
import sys
import os
import json
from optparse import OptionParser
from tornado.template import Template
from build_template import *
from build_config import *

def get_allow_empty_fields(fields_config):
    allow_empty_fields = []
    for field in fields_config:
        if field["fieldMode"] == "primaryKey":
            continue
        if 'defaultValue' in field and field['defaultValue'] == "":
            allow_empty_fields.append(field['fieldName'])
    return allow_empty_fields

def get_like_fields(fields_config):
    like_fields = []
    for field in fields_config:
        if field["fieldMode"] == "primaryKey":
            continue
        if 'more' not in field:
            continue
        more = field['more']
        if 'search' in more and more['search'] == '2':
            like_fields.append(field['fieldName'])
    return like_fields

def build_model(config, base_model_name):
    model = config['model']
    module_name = config['module_name']

    fields_config = model['info']['FieldsConfig']
    d = init_dict()
    d['model_name'] = config['module_name']
    d['module_name'] = config['module_name']
    d['base_model_name'] = base_model_name

    d['table_name'] = config['table_name']
    d['primary_key'] = model['info']['PrimaryKey']
    d['fields_info'] = fields_config
    d['allow_empty_fields'] = get_allow_empty_fields(fields_config)
    d['like_fields'] = get_like_fields(fields_config)
    # print(d['fields_info'])
    del_support = model['info']['DeleteSupport']
    if 'support' in del_support and del_support['support'] == "Yes":
        d['field_for_delete'] = del_support['field']   # TODO:
        d['value_for_delete'] = del_support['value']
        d['support_delete'] = 'Yes'
    else:
        d['field_for_delete'] = ''
        d['value_for_delete'] = 0
        d['support_delete'] = 'No'

    filename = module_name[0].upper() + module_name[1:] + ".php"
    path = os.path.join(config['product']['path'], "www\\model", filename)
    model_filename = path

    create_file_from_template("model.tpl.php", d, model_filename)
    return model_filename


def build_model_by_config(config):
    fn = build_model(config, 'AbBaseModel')
    return fn

if __name__ == '__main__':
    config = load_config()

    build_model_by_config(config)

