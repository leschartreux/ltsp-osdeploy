'''
Created on 30 juin 2014

@author: rignier
'''
import os
import subprocess
from subprocess import call
import fstab
import shutil
import StringIO
import settings


from flufl.i18n import initialize
from fstab import Fstab


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
        self.dev_path=dev_path
    
    
    def rename(self,new_name,domain='leschartreux.com'):
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
        
        #update /etc/hosts
        shutil.copy_file(settings.FSS_MOUNT + '/etc/hosts',settings.FSS_MOUNT + '/etc/hosts.old')
        fh = open(settings.FSS_MOUNT + '/etc/hosts.old','r')
        fhn = open(settings.FSS_MOUNT + '/etc/hosts','w')
        for l in fh:
            if str(l).startswith('127.0.1.1'):
                fhn.write("127.0.1.1\t%s\t%s\n" % (new_name, newname +"." . domain))
            else:
                fhn.write(l)
        
        fh.close()
        fhn.close()
        
    
    def cleanUdev(self):
        """
        remove hosts's persistent rules, to have clean hdw detection'
        """
        
        dev_files = os.listdir(settings.FS_MOUNT + "/etc/udev/rules.d")
        for f in dev_files:
            os.remove(settings.FS_MOUNT + "/etc/udev/rules.d/"+f)
        
    def installGrub(self):
        """
        Reinstall grub on disk mbr
        """
        
        print "\n#####################################\n"
        print "prepare Boot OS\n"
        
        #extract UUID string
        output = subprocess.Popen("/sbin/blkid | grep swap",shell=True,stdout=subprocess.PIPE)
        swaps = output.stdout.read()
        uuid = swaps.split(' ')[1].replace('\"','')
        
        
        fst = fstab.Fstab
        fst.read(settings.FS_MOUNT+'/etc/fstab','w')
        for l in fstab.lines:
            if l.fstype == 'swap':
                l.device=uuid
                
        
        fst.write(settings.FS_MOUNT+'/etc/fstab','w')
                
        
        if os.path.exists(settings.FS_MOUNT+"/usr/sbin/burg-install"):
            print _("\nBurg detected.")
            cmd = "chroot %s /usr/sbin/burg-installl %s" % (settings.FS_MOUNT, self.dev_path[:-1])
            call(cmd,shell=True)
        else:
            print _("\nReinstall grub")               
            call(['/usr/sbin/grub-install','--boot-directory='+settings.FS_MOUNT+'/boot','--dir='+settings.FS_MOUNT+'/usr/lib/grub/i386-pc', self.dev_path[:-1]]);
