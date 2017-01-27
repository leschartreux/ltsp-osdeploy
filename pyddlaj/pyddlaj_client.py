#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright 2014 R. RIGNIER
#This file is part of ltsp-osdeploy.
#
#    Foobar is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    ltsp-osdeploy is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with ltsp-osdeploy.  If not, see <http://www.gnu.org/licenses/>.
from pyddlaj import askYesNo
 
'''
Created on 8 avr. 2014

@author: rignier
'''

#import sys ;sys.path.append(r'/home/rignier/.eclipse/org.eclipse.platform_3.8_155965261/plugins/org.python.pydev_3.4.1.201403181715/pysrc')
#import sys ;sys.path.append(r'pydevd')
#import pydevd; pydevd.settrace(host='10.11.1.186')
 

import settings


import pyddlaj.host
import pyddlaj.db
import pyddlaj.nettask
import transfert.ssh
import pyddlaj.winregistry
#import transfert.tftp
import sys
import time

import os
from flufl.i18n import initialize
import languages
import pyddlaj.linux_host
#import subprocess
from subprocess import call #to launch shell cmds


def installed():
    """Action to do when existing host is in installed state"""
    if myhost.isBootable():
        print _('Localboot PXE file copy')
        transfert.ssh.scplocalboot(myhost.mac)
        exit_code = 0
    else:
        print "This computer doesn't seems Bootable, even if in installed state."
        exit_code = 1
    return exit_code

def modifiedpc():
    modified(clone_type='partclone')
    
def modified(clone_type="fsa"):
    """Action to take when host is in modified states"""
    
    #
    #file archiver version
    #THIS Doesn't work for noow
    #
    if clone_type == "fsa":
        #First we check if Hard drive should be partitioned
        disklist = jdb.diskPartitions(myhost.dns)
        if len(disklist) > 0:
            #new partition will be made for the listed disks
            ret = myhost.makePartitions(disklist)
            #Format Cache Filesystem partition
            myhost.cacheFormat()
            if ret:
                #disable hdd partitioning
                jdb.updateHddToPart(myhost.dns,False)
        
        lbaseimg = jdb.getIdbToInstall(myhost.dns)
        if not os.path.isdir(settings.CACHE_MOUNT):
            #print "Création du répertoire " + settings.CACHE_MOUNT
            os.mkdir(settings.CACHE_MOUNT)
        if not os.path.ismount(settings.CACHE_MOUNT):
            print _("Mounting cache partition")
            #mount cache file system
            call(['/bin/mount',myhost.cahePart,settings.CACHE_MOUNT])
        task_id = jdb.getTask(myhost.dns)
        #print "Les images de bases : ", lbaseimg
        print _("Task ID"), task_id
        if task_id > 0:
            Nt = pyddlaj.nettask.NetTask()
            Nt.send(task_id, myhost.dns)
        for img in lbaseimg:
            cmd = ["/usr/bin/udp-receiver","--file" , settings.CACHE_MOUNT + "/" +os.path.basename(img['imgfile']),
                   #"--mcast-rdv-address" , settings.TFTP_SERVER, "--nokbd", "--ttl" , str(task_id+5)]
                   "--mcast-rdv-address" , settings.TFTP_SERVER, "--nokbd", "--ttl" , str(task_id+5)]
            print _("Wait 5 secondes pour le sender")
            time.sleep(5)
            result = call(cmd)
        
        #Next we copy fsa file to corresponding partition
        for img in lbaseimg:
            print "********************************************"
            print _("Partition dump of ") + img['dev_path'] + str(img['num_part'])
            print "********************************************"
            
            srcfile = settings.CACHE_MOUNT + "/" +os.path.basename(img['imgfile'])
            dstpart = img['dev_path'] + str(img['num_part'])
            cmd = ["/usr/sbin/fsarchiver", "-v", "restfs", srcfile,"id=0,dest="+dstpart]
            #print "commande : ", cmd 
        
            ret = call(cmd)
    
    #
    #partclone version. Partition table and MBR is restored
    #Full disk restore
    #                
    if clone_type == "partclone":
        disklist = jdb.diskPartitions(myhost.dns)
        if len(disklist) > 0:
            lbaseimg = jdb.getIdbToInstall(myhost.dns)
            
            curTask = jdb.getTask(myhost.dns,False)
            if curTask == None:
                print _("No task for this host, switching to installed state")
                return installed()
            
            task_id = curTask['tid']
            use_nfs = curTask['utilise_nfs']
            #print "Les images de bases : ", lbaseimg
            print _("Task ID "), task_id
            okTask = True
                
            if task_id > 0 and use_nfs == 0:
                Nt = pyddlaj.nettask.NetTask()
                Nt.send(task_id, myhost.dns)
                
            if not os.path.isdir(settings.IMG_NFS_MOUNT):
                #print "Création du répertoire " + settings.IMG_NFS_MOUNT
                os.mkdir(settings.IMG_NFS_MOUNT)
            if not os.path.ismount(settings.IMG_NFS_MOUNT):
                print _("Mounting NFS master Images directory")
                #mount Images directory via NFS
                call(['/bin/mount',settings.IMG_SERVER+':'+settings.IMG_NFS_SHARE,settings.IMG_NFS_MOUNT])
            
            current_device=""
            for img in lbaseimg:
                #destination parition is generated here (/dev/sdax)
                dstpart = img['dev_path'] + str(img['num_part'])
                
                if 'swap' in img['fs_type']:
                    print _("Swap partition type. Initiating...")
                    cmd = "mkswap " + dstpart
                    result = call(cmd,shell=True)
                    if result != 0:
                        okTask = False
                    
                    continue
                
                src_dir= os.path.dirname(settings.IMG_NFS_MOUNT + '/' + img['imgfile'])
                
                if current_device != img['dev_path']: 
                    print _("Disk partitionning : "), 
                    cmd = "/sbin/sfdisk  %s < %s" % (img['dev_path'],src_dir + "/" + os.path.basename(img['dev_path'])  + ".dup")
                    call ( cmd,shell=True)
                    newdi = myhost.getdiskinfos()
                    jdb.updatePartitions(myhost.dns, newdi, img['num_disk'])
                    
                
                
                print "img : " , img
                fstype = img['fs_type'].lower()
                #Use dd for unsuported fs type
                if "unused" in fstype:
                    fstype = "dd"
                
                if use_nfs == 1:
                    print _("Restoring partition using NFS")
                    speed = curTask['speed']/10
                    cmd = "pv -L%s %s | /usr/bin/pigz -d  | /usr/sbin/partclone.%s -r -o %s" % (str(speed)+'m', settings.IMG_NFS_MOUNT+"/" +  img['imgfile'] + ".gz", fstype,dstpart)
                else:
                    #cmd = "/usr/bin/udp-receiver --mcast-rdv-address %s --start-timeout 900 --nokbd --ttl 32 --exit-wait 2000 | /usr/bin/pigz -d -c | /usr/sbin/partclone.%s --ncurses -r -o %s" % (settings.TFTP_SERVER,img['fs_type'],dstpart)
                    cmd = "/usr/bin/udp-receiver --mcast-rdv-address %s --start-timeout 900 --ttl 32 --exit-wait 2000 | /usr/bin/pigz -d -c | /usr/sbin/partclone.%s -r -o %s" % (settings.TFTP_SERVER,fstype,dstpart)
                    '''cmd = ["/usr/bin/udp-receiver","--pipe" , settings.CACHE_MOUNT + "/" +os.path.basename(img['imgfile']),
                    "--mcast-rdv-address" , settings.TFTP_SERVER, "--nokbd", "--ttl" , str(task_id+5)]'''
                    #print "cmd = ",cmd       
                    print _("Wait 5s then launch udp-reciever")
                    time.sleep(5)
                
                result=0
                result = call(cmd,shell=True)
                if result == 0: #on success we dump MBR
                    if current_device != img['dev_path']:
                        print _("MBR Backup")
                        cmd = "dd of=%s if=%s bs=446 count=1" % (img['dev_path'],src_dir + "/" + os.path.basename(img['dev_path']) + ".mbr")
                        call ( cmd,shell=True)
                        current_device = img['dev_path']
                else:
                    print _("Error: can't connect to sender.")
                    okTask = False
                
            if okTask: # all went ok we next rename/join the host
                jdb.addTaskOK(task_id)
                jdb.setState(myhost.dns, 'renomme')
                #get from task if Computer needs to be joined to windows domain
                full_task = jdb.getTask(myhost.dns,idonly=False)
                print "infos tache : ",full_task
    
                return rename( full_task['faire_jointure'])
            else:
                jdb.addTaskKO(task_id)
                return 1
        else:
            print _("No Disk in partitionning state")
            return 1
    #get
    
    return 0

def create_idb():
    """
      save host's system partitions
      Uses info stored in database
      IF no data found for this host, interactive ask for master image creation
    """
    disklist = jdb.diskPartitions(myhost.dns,tocreate=False)
    if len(disklist) > 0:
        print _("Disk list : "), disklist
        #store partition table
       
    lbaseimg = jdb.getIdbToInstall(myhost.dns)
    print "*************************************"
    print _('Associated master images : '),lbaseimg
    print "*************************************"
    
    #No base image is associated, we prompt for already exist or new one
    #Edit this should be always true to ask for things to do
    diskinfo = myhost.getdiskinfos()
    while True:
        print _("What do you want to do for this host ? ")
        if len(lbaseimg) > 0:
            print _("[0]: Delete existing distrib associations")         
        print _("[1]: Associate existing distrib")
        print _("[2]: Create new distrib")
        print _("[3]: Clone affected distrib(s)")
        print _("[4]: Exit create state and reboot")
        val = raw_input(_("choix : "))
        if not val.isdigit():
            print _("Bad number, please try again")
            continue
        val = int(val)
        if val < 0 or val > 4:
            print _("Bad input, please try again")
            continue
        if (val == 0):
            jdb.delDists(myhost.dns)
            lbaseimg=[]
            continue
        else:
            break

    if (val == 2):
        jdb.newDists(myhost.dns, diskinfo)
    elif (val ==1):
        jdb.addDists(myhost.dns, diskinfo)
    elif (val == 3):
        pass
    else:
        jdb.setState(myhost.dns, "reboot")
        reboot()
    #after prompt, Update list of base image 
    lbaseimg = jdb.getIdbToInstall(myhost.dns)
       
    
    if not os.path.isdir(settings.IMG_NFS_MOUNT):
        #print "Création du répertoire " + settings.IMG_NFS_MOUNT
        os.mkdir(settings.IMG_NFS_MOUNT)
    if not os.path.ismount(settings.IMG_NFS_MOUNT):
        print _("Mounting NFS master images directory")
        #mount Images directory via NFS
        call(['/bin/mount',settings.IMG_SERVER+':'+settings.IMG_NFS_SHARE,settings.IMG_NFS_MOUNT])

    bunattended = False
    current_device=""
    for img in lbaseimg:
        print ("*****************************************************")
        print _("Current Image : "),img
        #prepare source dev and destination directories
        if "swap" in img['fs_type']:
            print "Ignoring Swap type partition"
            continue

        dst_file = os.path.basename(img['imgfile'])
        src_dev =  img['dev_path'] + str(img['num_part'])
        dst_dir= os.path.dirname(settings.IMG_NFS_MOUNT + '/' + img['imgfile'])
        #create dest dir if not exist
        if not os.path.isdir(dst_dir):
            os.makedirs(dst_dir)
        
        #with each new device we store partition table and MBR
        if current_device != img['dev_path']: 
            print _("Backup partition table")
            dupfile = dst_dir + "/" + os.path.basename(img['dev_path'])  + ".dup"
            boverwrite=True
            if os.path.exists(dupfile):
                boverwrite = askYesNo(_("The MBR backup files already exists. Do you want to overwrite ?"))
           
            if boverwrite or bunattended:    
                cmd = "/sbin/sfdisk -d %s > %s" % (img['dev_path'],dst_dir + "/" + os.path.basename(img['dev_path'])  + ".dup")
                call ( cmd,shell=True)
                print _("Backup MBR")
                cmd = "dd if=%s of=%s bs=446 count=1" % (img['dev_path'],dst_dir + "/" + os.path.basename(img['dev_path']) + ".mbr")
                call ( cmd,shell=True)
            
            current_device = img['dev_path']

        print _('Backup partition in progress')
        if 'unused' in img['fs_type'].lower():
            print _('FS is unknowned type. (MSR?) We will use dd')
            fs = "dd"
        else:
            fs = img['fs_type'].lower()
        
        
        boverwrite =True
        partfile = dst_dir +"/" +dst_file + ".gz"
        if os.path.exists(partfile) and not bunattended:
            boverwrite = askYesNo("The partition backup already exists. Do you want to overwrite it ?")
            bunattended = askYesNo("Would you like to clone all remaining partitions without ask ?")
        if boverwrite or bunattended:
            cmd = "/usr/sbin/partclone." +fs +" -c -s " + src_dev + " | /usr/bin/pigz -c --fast > "+ dst_dir +"/" +dst_file + ".gz"
            #print "commande : ", cmd
            ret = call(cmd,shell=True)
            #ret = 0
            #On clone succes, we update db and restore localboot of the host
            if ret == 0:
                jdb.setState(myhost.dns, "renomme")
                rename()
            else:
                print _("Something went wrong ! can't reboot")
                return 1

        print ("*****************************************************")
        

def reboot():
    transfert.ssh.scplocalboot(myhost.mac)
    jdb.setState(myhost.dns, "installe")
    return 0

def debug():
    '''Host is in debug state. Does nothing'''
    print "*********************************"
    print _("host in debug mode. Please connect to console and manualy launch pyddlaj")
    print "*********************************"
    return 2

def rename(joindom=1):
    '''for windows Oses uses registry and netdom cmds to Rename host'''
    print "*********************************"
    print _("Renaming computer")
    print "*********************************"
    
    
    lOS = jdb.getOs(myhost.dns)
    print _("Detected OS(es) : "), lOS
    for os in lOS:
        print "os name", os['nom_os'].lower()
        
        if  'win' in os['nom_os'].lower(): #OS partition is Windows
            if 'boot' in os['nom_idb'].lower():
                continue #Ignore windows boot partition. must be part of idb name
            
            winreg = pyddlaj.winregistry.WinRegistry(os['dev_path'])
            winreg.RenameJoinScript(os['nom_os'], myhost.dns,joindom)
            winreg.close()
#                transfert.ssh.scplocalboot(myhost.mac)
        if 'lin' in os['nom_os'].lower():
            if os['nom_idb'] == '/':
                l = pyddlaj.linux_host.LinuxHost(os['dev_path'])
                l.rename(myhost.dns)
                l.cleanUdev()
                l.installGrub()
                
    return reboot()


if __name__ == '__main__':
    
    os.environ['LANG'] = settings.LANG;
    os.environ['LOCPATH'] = os.path.dirname(languages.__file__)
    _ = initialize('pyddlaj_client')
    
    exit_code = 0
    jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST, settings.MYSQL_USER, settings.MYSQL_PASSWORD, settings.MYSQL_DB, 3306)
    
    myhost = pyddlaj.host.host()
    
    print '************************************************'
    print _('system detection')
    print _('mac:') , myhost.mac
#    print _('uname:'), myhost.uname
    print _('ip:'), myhost.ip
#    print _('proc:'), myhost.proc
    print _('machine : ') , myhost.machine
    print _('dns :'), myhost.dns
    print '**********************************************'
    meminfo = myhost.meminfo()
    print _('Memory : ')
    print _('Total : {0}'.format(meminfo['MemTotal']))
    print _('Free: {0}'.format(meminfo['MemFree']))
    print '**********************************************'
    print _('CPU : '), myhost.cpuinf['proc0']['model name']
    print _('nbcpu:'), myhost.nbcpu
    print '**********************************************'
    
    #Get Local Disks infos
    myhost.listdisks()
    mydiskinfo = myhost.getdiskinfos()
    
    # get host from database if exists
    fh = jdb.findhost(myhost.mac)
    print _("Finding in DB by MAC Address : "), fh
    if fh == None:
        """Host is not found. Then the database should be updated
        etat_install is set to installed by default so it could boot on its hdd"""
        #Can't go on if non DNS record (host's primary key)
        if len(myhost.dns) == 0:
            print "!! No DNS record correspond to your IP !!"

            print _("Inserting Host with mac-adress as hostname. This should be change with Web GUI")
            myhost.dns = myhost.mac.replace(':','-') + '.' + settings.AD_DOMAIN
            
            jdb.newhost(myhost)
            if myhost.isBootable():
                print "The system seems bootable I Copy local boot PXE File"
                transfert.ssh.scplocalboot(myhost.mac)
                #sys.exit(0)
            else:
                print "Update your DNS PTR Records and restart"
                #sys.exit(1)
            
        #host not found on mac address.
        #search now with DNS
        fh = jdb.findHostByName(myhost.dns)
        print 
        if fh == None:
            print "***************************************"
            print _("Computer not in database. Adding this one")
            jdb.newhost(myhost)
            if myhost.isBootable():
                print _("Localboot PXE File copy")
                transfert.ssh.scplocalboot(myhost.mac)
        else:
            print "*************************************"
            print _("Computer found. updating info"), fh
            print "*************************************"
            jdb.updateHost(myhost)
            
            
    else:
        #force dns host name to be the same as database 
        myhost.dns = fh['nom_dns']
        print _("Computer Found in DB ")
    
    print '**********************************************'
    print _(' Disks and Partitions from database')
    print '**********************************************'
    #jdb.deldisks(myhost.dns)
    dbdisk = []
    dbdisk = jdb.getdisks(myhost.dns)     
    #print "Les disques dans la base : ", dbdisk

    if len(dbdisk) == 0:
        print _("Can't find Disks informations. Adding it")
        jdb.addpartitions(myhost.dns,mydiskinfo)

    print '**********************************************'    
    print _('Local Partitions list '), mydiskinfo
    print '**********************************************'

    state = jdb.getState(myhost.dns)
    
    print '***********************************************'
    print _('Computer state in DB : '), state
    print '***********************************************'
    
    states = { 'installe':installed , 'idb':create_idb,'modifie':modifiedpc, 'reboot':reboot, 'depannage':debug, 'renomme':rename }
    if ( state == 'renomme'):
        if (fh['affiliation_windows'] == 'workgroup'):
            exit_code = rename(0)
        else:
            exit_code= rename(1)
    #launch corresponding code with state
    else:
        exit_code=states[state]()
    
    jdb.close()
    
    print(_("Database closed"));
    print _("Wait for 10s")
    time.sleep(10)
    sys.exit(exit_code)
