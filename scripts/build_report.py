
from build_controller import *
from build_model import *
from build_view import *
from build_config import *
from tornado.template import Template
import json

def extract_time_fields(d, fields_config):
    d['create_time'] = False
    d['update_time'] = False
    for field in fields_config:
        if field['fieldMode'] != 'datetime':
            continue
        if 'more' in field:
            dt_type = field['more']['type']
            if dt_type == 'create_time':
                d['create_time'] = field['fieldName']
            elif dt_type == 'update_time':
                d['update_time'] = field['fieldName']

def build_report_controller(config, base_controller_name):
    model = config['model']
    info = model['info']
    module_name = config['module_name']
    controller_name = module_name + "Controller"
    d = init_dict()
    d['model_name'] = module_name
    d['controller_name'] = controller_name
    d['base_controller_name'] = base_controller_name
    d['tabview_title'] = model['info']['Title']

    d['item_has_checkbox'] = 'true'
    d['item_has_operator'] = 'true'
    d['support_delete'] = info['DeleteSupport']['support']

    fields_config = model['info']['FieldsConfig']
    extract_time_fields(d, fields_config)


    path = os.path.join(config['product']['path'], "www\\controller", controller_name + ".php")
    controller_filename = path
    create_file_from_template("controller.tpl.php", d, controller_filename)
    return controller_filename


def build_report_model_by_config(config):
    return []

def build_report_view_by_config(config):        
    return []

def build_report_controller_by_config(config):
    controller_file = build_report_controller(config, 'AbBaseController')
    return controller_file


if __name__ == '__main__':
    sys.argv = ['build_report.py', '--command=build_report', '--table=kx_company', '--prefix=', '--config=D:\\Projects\\AdminBuildr\\config\\config.json']
    config = load_config()

    print(config)

    model_file = build_report_model_by_config(config)
    view_files = build_report_view_by_config(config)
    controller_file = build_report_controller_by_config(config)

    d = [model_file, controller_file] + view_files

    print(json.dumps(d))

