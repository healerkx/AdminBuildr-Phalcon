
from build_template import *
from optparse import OptionParser
import json
import os

def generate_enum_classfile(json_file, model):
    filename = os.path.basename(json_file)
    p = os.path.dirname(os.path.dirname(json_file))
    enum_name = filename[:-5]
    enum_file = os.path.join(p, enum_name + '.php')
    print("$")
    d = {
        'enum_name': enum_name,
        'array': model
    }
    create_file_from_template('enum.tpl.php', d, enum_file)


"""
"""
if __name__ == '__main__':
    # sys.argv = ['build_enum.py', '--json=D:\Projects\Badmin\www\defines\const\A.json']
    parser = OptionParser()

    parser.add_option("-j", "--json", action="store",
                  dest="json_file", help="Provide JSON file name")

    options, args = parser.parse_args()
    json_file = options.json_file

    with open(json_file, 'r', encoding='utf-8') as f:
        content = f.read()
        model = json.loads(content)

        r = generate_enum_classfile(json_file, model)

    print(json.dumps(r))
