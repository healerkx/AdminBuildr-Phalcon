
import os, string, sys, time, re, shutil
from build_template import *

def create_dir_if_not_exists(*path):
    path = os.path.join(*path)
    if not os.path.exists(path):
        os.mkdir(path)

"""
"""
def copytree(src, dst, symlinks=False):
    names = os.listdir(src)
    if not os.path.isdir(dst):
        os.makedirs(dst)
          
    errors = []
    for name in names:
        srcname = os.path.join(src, name)
        dstname = os.path.join(dst, name)
        try:
            if symlinks and os.path.islink(srcname):
                linkto = os.readlink(srcname)
                os.symlink(linkto, dstname)
            elif os.path.isdir(srcname):
                copytree(srcname, dstname, symlinks)
            else:
                if os.path.isdir(dstname):
                    os.rmdir(dstname)
                elif os.path.isfile(dstname):
                    os.remove(dstname)
                shutil.copy2(srcname, dstname)
            # XXX What about devices, sockets etc.?
        except (IOError, os.error) as why:
            errors.append((srcname, dstname, str(why)))
        # catch the Error from the recursive copytree so that we can
        # continue with other files
        except OSError as err:
            errors.extend(err.args[0])
    try:
        shutil.copystat(src, dst)
    except WindowsError:
        # can't copy file access times on Windows
        pass
    except OSError as why:
        errors.extend((src, dst, str(why)))
    if errors:
        raise Error(errors)


def copy_files(srcpath, destpath):
    copytree(srcpath, destpath, True)

def create_init_dirs(destpath):
    create_dir_if_not_exists(destpath)
    create_dir_if_not_exists(destpath, "config")
    create_dir_if_not_exists(destpath, "www")
    create_dir_if_not_exists(destpath, "www", "controller")
    create_dir_if_not_exists(destpath, "www", "model")
    create_dir_if_not_exists(destpath, "www", "model", "config")
    create_dir_if_not_exists(destpath, "www", "view")
    create_dir_if_not_exists(destpath, "www", "view", "compiled-files")
    create_dir_if_not_exists(destpath, "bank")
    create_dir_if_not_exists(destpath, "bank", "controller")
    create_dir_if_not_exists(destpath, "bank", "model")
    create_dir_if_not_exists(destpath, "bank", "view")

def copy_init_files(srcpath, destpath, workpath):
    srcpath = os.path.join(srcpath, workpath)
    destpath = os.path.join(destpath, workpath)
    copy_files(srcpath, destpath)


def rename_files(path, oldname, newname):
    oldname = os.path.join(path, oldname) 
    newname = os.path.join(path, newname) 
    os.rename(oldname, newname)

def create_index_file(path):
    d = {}
    index_php = os.path.join(path, "www", "index.php")
    create_file_from_string_template("index.tpl.php", d, index_php)

def create_config_file():
    pass

if __name__ == '__main__':
    srcpath = 'D:\\Projects\\AdminBuildr'
    destpath = 'D:\\Projects\\Badmin'
    create_init_dirs(destpath)
    copy_init_files(srcpath, destpath, "www\\lib")
    copy_init_files(srcpath, destpath, "www\\view\\active-web")
    copy_init_files(srcpath, destpath, "www\\view\\common")
    #copy_init_files(srcpath, destpath, "www\\view\\media")

    #
    create_index_file(destpath)
    create_config_file()