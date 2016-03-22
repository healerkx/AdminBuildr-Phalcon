import sys
import os
import json
from optparse import OptionParser
from tornado.template import Template as TT	
from string import Template as ST

class MyTemplate(ST):
    delimiter = "##"
    idpattern = "[_a-z][_a-z0-9]+"


def init_dict():
    d = {}
    d['lower'] = lambda x: x.lower()
    d['firstlower'] = lambda x: x[0].lower() + x[1:]
    return d

def create_file_from_template(template_filename, d, filename):
    #template_file = os.path.dirname(os.path.realpath(__file__)) + "\\templates\\" + template_filename
    template_file = os.path.join(os.path.dirname(__file__), "templates", template_filename)
    r = open(template_file, 'r', encoding='utf-8')
    content = r.read()
    r.close()
    
    t = TT(content)
    c = t.generate(**d)
    
    f = open(filename, 'w', encoding='utf-8')
    f.write(c.decode('utf-8'))
    f.close()   

def create_file_from_string_template(template_filename, d, filename):
    #template_file = os.path.dirname(os.path.realpath(__file__)) + "\\templates\\" + template_filename
    template_file = os.path.join(os.path.dirname(__file__), "templates", template_filename)
    r = open(template_file, 'r', encoding='utf-8')
    content = r.read()
    r.close()
    
    t = MyTemplate(content)
    c = t.substitute(d)

    f = open(filename, 'w', encoding='utf-8')
    f.write(c)
    f.close()      