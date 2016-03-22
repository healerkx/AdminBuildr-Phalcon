
from optparse import OptionParser
import json
import re
import os


def get_module_name(prefix, table_name):
    if prefix and len(prefix) > 0:
        table_name = prefix + "_" + table_name

    m = re.sub(r"_([a-z])", lambda x: x.group(1).capitalize(), table_name)
    return m[0].upper() + m[1:]

"""
"""
def load_config(config_file=None):
    options, args = parse_args()
    config_file = config_file if config_file else options.config_file
    config = {}
    product_path = None
    if config_file is not None:
        with open(config_file, 'r') as f:
            content = f.read()
            config = json.loads(content)
        product_path = config['product']['path']

    config['prefix'] = options.prefix
    config['table_name'] = options.table_name
    model_name = get_module_name(options.prefix, options.table_name)
    config['module_name'] = model_name
    
    model = None
    p = os.path.join(product_path, 'www\\model\\config', model_name + ".json")
    if os.path.exists(p):
        with open(p, 'r', encoding='utf-8') as f:
            content = f.read()
            model = json.loads(content)
            
            virtual = {
                'fieldText': '', 'fieldName': '',
                'fieldMode': '__virtual__', 'more': ''
            }
            model['info']['FieldsConfig'].append(virtual)
    config['model'] = model
    return config

"""
"""
def parse_args():
    parser = OptionParser()

    parser.add_option("-c", "--config", action="store",
                  dest="config_file", help="Provide controller name")

    parser.add_option("-p", "--prefix", action="store", default='',
                  dest="prefix", help="Provide the module prefix")

    parser.add_option("-t", "--table", action="store",
                  dest="table_name", help="Provide the database table name")

    options, args = parser.parse_args()
    return options, args

if __name__ == '__main__':
    # print(get_module_name('kx', 'admin_user'))
    
    c = load_config(config_file='')
    print(c)