
from build_controller import *
from build_model import *
from build_view import *
from build_config import *
from tornado.template import Template
import json

if __name__ == '__main__':
    sys.argv = ['a.py', '--table=kx_user', '--prefix=ab', '--config=D:\\Projects\\AdminBuildr\\config\\config.json']
    config = load_config()

    model_file = build_model_by_config(config)
    view_files = build_view_by_config(config)
    controller_file = build_controller_by_config(config)

    d = [model_file, controller_file] + view_files

    print(json.dumps(d))
