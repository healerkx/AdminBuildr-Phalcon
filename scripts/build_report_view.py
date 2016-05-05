
from build_controller import *
from build_model import *
from build_view import *
from build_config import *
from tornado.template import Template
import json

def build_report_view(config):

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
    full_path = os.path.join(path, 'www\\view', module_path + "-report")
    if not os.path.exists(full_path):
        os.mkdir(full_path)

    view_index_filename = os.path.join(full_path, "index.phtml")
    view_index_js_filename = os.path.join(full_path, "index.js.phtml")
    

    # List
    build_view_index(config, d)
    create_file_from_string_template("report_view_index.tpl.html", d, view_index_filename)
    create_file_from_string_template("report_view_index_js.tpl.html", d, view_index_js_filename)

    return [view_index_filename, view_index_js_filename]



def build_report_view_by_config(config):        
    return build_report_view(config)



if __name__ == '__main__':
    sys.argv = ['build_report.py', '--command=build_report', '--table=kx_user', '--prefix=', '--config=D:\\Projects\\AdminBuildr\\config\\config.json']
    config = load_config()

    print(config)

    view_files = build_report_view_by_config(config)

    print(view_files)

