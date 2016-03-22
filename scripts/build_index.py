
import os, shutil

def copy_file(srcpath, destpath, filename):
    srcpath = os.path.join(srcpath, filename)
    destpath = os.path.join(destpath, filename)
    shutil.copyfile(srcpath, destpath)

if __name__ == '__main__':
    srcpath = 'D:\\Projects\\AdminBuildr'
    destpath = 'D:\\Projects\\Badmin'
    
    copy_file(srcpath, destpath, "www\\index.php")
