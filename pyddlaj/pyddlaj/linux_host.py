'''
Created on 30 juin 2014

@author: rignier
'''
import os
from subprocess import call
import settings
from flufl.i18n import initialize
_ = initialize('pyddlaj_client')

class LinuxHost(object):
    '''
    Class to manipulates Linux hosts
    '''

    def __init__(self,dev_path):
        'Mount Linux partition'
        if not os.path.isdir(settings.FS_MOUNT):
            print _("Make directory ") + settings.FS_MOUNT
            os.mkdir(settings.FS_MOUNT)
        if not os.path.ismount(settings.FS_MOUNT):
            print _("Mount Linux root system partition")
            #mount NTFS partition with lower case only to avoid file names between windows version v
            call(['/bin/mount',"-o","rw",dev_path,settings.FS_MOUNT])
        
        self.hostname_file = "/etc/hostname"
    
    
    def rename(self,new_name):
        """
        rename Host with
        @param new_name: name of new host 
        
        Depends on distrib but write it in /etc/hostname shoud be enough
        """
        if "." in new_name:
            new_name = new_name.split('.')[0]
            
        fh = open(settings.FS_MOUNT + self.hostname_file,'w')
        fh.write(new_name)
        fh.close()
    
    def cleanUdev(self):
        """
        remove hosts's persistent rules, to have clean hdw detection'
        """
        
        dev_files = os.listdir(settings.FS_MOUNT + "/etc/udev/rules.d")
        for f in dev_files:
            os.remove(settings.FS_MOUNT + "/etc/udev/rules.d/"+f)
        