
from build_model import *
from build_config import *
from tornado.template import Template
import json

def build_report_model_by_config(config):
    return []




if __name__ == '__main__':
    sys.argv = ['build_report.py', '--command=build_report', '--table=kx_user', '--prefix=', '--config=D:\\Projects\\AdminBuildr\\config\\config.json']
    config = load_config()

    print(config)
    model_file = build_report_model_by_config(config)
    print(model_file)

