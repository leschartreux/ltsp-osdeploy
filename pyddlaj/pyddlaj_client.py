#!/usr/bin/env python
# -*- coding: utf-8 -*-
'''
Created on 8 avr. 2014

@author: rignier
'''

#import sys ;sys.path.append(r'/home/rignier/.eclipse/org.eclipse.platform_3.8_155965261/plugins/org.python.pydev_3.4.1.201403181715/pysrc')
import sys ;sys.path.append(r'pydevd')
#import pydevd; pydevd.settrace(host='10.11.1.186')
 

import settings


import pyddlaj.host
import pyddlaj.db
import pyddlaj.nettask
import transfert.ssh
#import transfert.tftp
#import sys
import time
import os

from subprocess import call #to launch shell cmds

if __name__ == '__main__':
    
    
    exit_code = 0
    jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST, settings.MYSQL_USER, settings.MYSQL_PASSWORD, settings.MYSQL_DB, 3306)
    
    myhost = pyddlaj.host.host()
    
    print '************************************************'
    print 'Détection du système'
    print 'mac' , myhost.mac
    print 'uname:', myhost.uname
    print 'ip:', myhost.ip
    print 'proc:', myhost.proc
    print 'machine : ' , myhost.machine
    print 'dns :', myhost.dns
    print '**********************************************'
    print 'Mémoire : '
    print '**********************************************'
    print 'CPU : ', myhost.cpuinf['proc0']['model name']
    print 'nbcpu:', myhost.nbcpu
    print '**********************************************'
    meminfo = myhost.meminfo()
    print('Totale : {0}'.format(meminfo['MemTotal']))
    print('Libre: {0}'.format(meminfo['MemFree']))
    
    #Get Local Disks infos
    myhost.listdisks()
    mydiskinfo = myhost.getdiskinfos()
    
    # get host from database if exists
    fh = jdb.findhost(myhost.mac)
    if fh == None:
        """Host is not found. Then the database should be updated
        etat_install is set to installed by default so it could boot on its hdd"""
        #Can't go on if non DNS record (host's primary key)
        if len(myhost.dns) == 0:
            print "No DNS record correspond to your IP"
            
            if myhost.isBootable():
                print "Un système existe je copy pxe local boot"
                transfert.ssh.scplocalboot(myhost.mac)
                sys.exit(0)
            else:
                print "Enregistrez cet hôte dans le dns et recommencez"
                sys.exit(1)
        
        print "Ordi non trouvé, j'ajoute dans la base"
        jdb.newhost(myhost)
        if myhost.isBootable():
            print "copie du localboot"
            transfert.ssh.scplocalboot(myhost.mac)
            
    else:
        print "Ordi trouvé dans la base : "
    
    #jdb.deldisks(myhost.dns)
    dbdisk = []
    dbdisk = jdb.getdisks(myhost.dns)     
    print "Les disques dans la base : ", dbdisk

    if len(dbdisk) == 0:
        print "Je n'ai pas d'info disques. Je les ajoutes"
        jdb.addpartitions(myhost.dns,mydiskinfo)

        
    print "LES disques : "
    print '**********************************************'    
    print 'Les partitions', mydiskinfo
    print '**********************************************'

    state = jdb.getState(myhost.dns)
    
    print '***********************************************'
    print 'Etat du poste dans la base : ', state
    print '***********************************************'
    
    
    
    def installed():
        """Action to do when existing host is in installed state"""
        if myhost.isBootable():
            print 'Je recopie le pxe localboot'
            transfert.ssh.scplocalboot(myhost.mac)
            exit_code = 0
        else:
            print "Ce post ne semble pas bootable, bien que en état installé."
            exit_code = 1
        return exit_code
    
    def modified():
        """Action to take when host is in modified states"""
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
            print "Création du répertoire " + settings.CACHE_MOUNT
            os.mkdir(settings.CACHE_MOUNT)
        if not os.path.ismount(settings.CACHE_MOUNT):
            print "Montage du répertoire cache"
            #mount cache file system
            call(['/bin/mount',myhost.cahePart,settings.CACHE_MOUNT])
        
        task_id = jdb.getTask(myhost.dns)
        print "Les images de bases : ", lbaseimg
        print "id tache", task_id
        if task_id > 0:
            Nt = pyddlaj.nettask.NetTask()
            Nt.send(task_id, myhost.dns)
        for img in lbaseimg:
            cmd = ["/usr/bin/udp-receiver","--file" , settings.CACHE_MOUNT + "/" +os.path.basename(img['imgfile']),
                   "--mcast-rdv-address" , settings.TFTP_SERVER, "--nokbd", "--ttl" , str(task_id+5)]
            print "attente 5 secondes pour le sender"
            time.sleep(5)
            result = call(cmd)
        
        #Next we copu fsa file to cerrospondign partition
        for img in lbaseimg:
            print "********************************************"
            print "REcopie de la partition " + img['dev_path'] + str(img['num_part'])
            print "********************************************"
            
            srcfile = settings.CACHE_MOUNT + "/" +os.path.basename(img['imgfile'])
            dstpart = img['dev_path'] + str(img['num_part'])
            cmd = ["/usr/sbin/fsarchiver", "-v", "restfs", srcfile,"id=0,dest="+dstpart]
            print "commande : ", cmd 
        
            ret = call(cmd)
        #get
        
        return 1
    
    def create_idb():
        """save host's system partitions"""
        """Uses info stored in database"""
        disklist = jdb.diskPartitions(myhost.dns,tocreate=False)
        if len(disklist) > 0:
            print "Les disques : ", disklist
            #store partition table
           
        
        lbaseimg = jdb.getIdbToInstall(myhost.dns)
        if not os.path.isdir(settings.IMG_NFS_MOUNT):
            print "Création du répertoire " + settings.IMG_NFS_MOUNT
            os.mkdir(settings.IMG_NFS_MOUNT)
        if not os.path.ismount(settings.IMG_NFS_MOUNT):
            print "Montage du répertoire Images"
            #mount Images directory via NFS
            call(['/bin/mount',settings.IMG_SERVER+':'+settings.IMG_NFS_SHARE,settings.IMG_NFS_MOUNT])
        
        current_device=""
        for img in lbaseimg:
            #prepare source dev and destination directories
            dst_file = os.path.basename(img['imgfile'])
            src_dev =  img['dev_path'] + str(img['num_part'])
            dst_dir= os.path.dirname(settings.IMG_NFS_MOUNT + '/' + img['imgfile'])
            #create dest dir if not exist
            if not os.path.isdir(dst_dir):
                os.makedirs(dst_dir)
            
            #with each new device we store partition table and MBR
            if current_device != img['dev_path']: 
                print "Sauvegarde table des partition"
                cmd = "/sbin/sfdisk -d %s > %s" % (img['dev_path'],dst_dir + "/" + dst_file + ".dup")
                call ( cmd,shell=True)
                print "Sauvegarde MBR"
                cmd = "dd if=%s of=%s bs=446 count=1" % (img['dev_path'],dst_dir + "/" + dst_file + ".mbr")
                call ( cmd,shell=True)
                current_device = img['dev_path']

            print 'sauvegarde de la partition '
            cmd = "/usr/sbin/partclone.ntfs --ncurses -c -s " + src_dev + " | /usr/bin/pigz -c --fast > "+ dst_dir +"/" +dst_file + ".gz"
            print "commande : ", cmd
            
            ret = call(cmd,shell=True)
            #On clone succes, we update db and restore localboot of the host
            if ret == 0:
                jdb.setState(myhost.dns, "installe")
                transfert.ssh.scplocalboot(myhost.mac)
                return 0
            else:
                print "Something went wrong ! no reboot"
                return 1


    
    states = { 'installe':installed , 'idb':create_idb,'modifie':modified }
    #launch corresponding code with state
    exit_code=states[state]()
    
    print("yo man");
    time.sleep(10)
    sys.exit(exit_code)
