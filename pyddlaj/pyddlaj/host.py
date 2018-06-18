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
 
import netifaces
import platform

import reparted #this api is easier than parted and sufficient
import parted
#import settings
from collections import OrderedDict
import socket
from subprocess import call


import glob
import re
import sys
import os

from flufl.i18n import initialize
from lxml import _elementpath

#import languages

_= initialize('pyddlaj_client')

class host:
    """Represents current host data needed for os deployment"""
 
    # avaliable Device list should contains parted 

    
    def __init__(self):
        import dmidecode
        
        self.intf = netifaces.ifaddresses('eth0')
        #print ('test intf:', self.intf)
        self.mac = self.intf[netifaces.AF_LINK][0]['addr']
        self.ip = self.intf[netifaces.AF_INET][0]['addr']
        self.uname = platform.uname()
        self.system = platform.system()
        self.node = platform.node()
        self.release = platform.release()
        self.version = platform.version()
        self.machine = platform.machine()
        # self.proc=platform.processor()

        #system info exxtracted from BIOS via dmidecode
        self.system = dmidecode.system()
        for v in self.system.values():
            if type(v) == dict and v['dmi_type'] == 1:
                self.manuf = v['data']['Manufacturer'] 
                self.model = v['data']['Product Name']
                self.serial = v['data']['Serial Number']
                
        self.cpuinf = self.cpuinfo()
        self.proc = self.cpuinf['proc0']['model name']
        try:
            self.dns = socket.gethostbyaddr(self.ip)[0]
        except (socket.herror):
            self.dns = ''
        
        self._devices = []
        self.nbdev = 0
        self.efi = self.isEFI()
        self._cachepart = ''
        self._disks = {}
        self._diskinfo={} #Dictionary with local disks informations

        
    def cpuinfo(self):
        """"Return the information in /proc/cpuinfo
        as a dictionary in the following format:
        cpu_info['proc0']={...}
        cpu_info['proc1']={...}
    
        """
    
        cpuinfo = OrderedDict()
        procinfo = OrderedDict()
    
        nprocs = 0
        with open('/proc/cpuinfo') as f:
            for line in f:
                if not line.strip():
                    # end of one processor
                    cpuinfo['proc%s' % nprocs] = procinfo
                    nprocs = nprocs + 1
                    # Reset
                    procinfo = OrderedDict()
                else:
                    if len(line.split(':')) == 2:
                        procinfo[line.split(':')[0].strip()] = line.split(':')[1].strip()
                    else:
                        procinfo[line.split(':')[0].strip()] = ''
        self.nbcpu = nprocs
        return cpuinfo
    
    
    @property
    def cahePart(self):
        if self._cachepart == '':
            self._cachepart = self._getCacheDevPath(self._disks.keys()[0])
        return self._cachepart
    
    def cacheFormat(self):
        dev_path = self.cahePart
        print _("Formating cache partition : "), dev_path
        cmd = "/sbin/mkfs.ext4"
        call( [cmd,dev_path])
        
    
    def listdisks(self):
        # Add any other device pattern to read from
        dev_pattern = ['sd.*', 'mmcblk*', 'hd*']
        self._detect_devs(dev_pattern)

        for dev in self._devices:
            self._detect_partition(dev)
    
    def getdisks(self):
        """Return disks with partitions dictionary
        @return:  dictionnary of form :
            key= device path (/dev/sda) then tuplet ( Disk Object , [Primary partition list], extended partion object, [Logical partition list] )"""
        return self._disks
    
    def createDosTableIfEmpty(self,path):
        """Returns True if Partition table is not empty
        If no partiton table detected, then create one and return False
        """
        myDev =  reparted.Device(path)
        try:
            myDisk = reparted.Disk(myDev) #An error is raised if disk has empty partition table
            if myDisk.type_name is None:
                return True
            else:
                return False
            
        except reparted.exception.DiskError as de:
            print _("Disk Error : "), de
            if de.code == 600: #Error with empty disk partition
                print _("New partition table :"),
                #only way to create partition unattend is by calling parted command...
                pcmd=("/sbin/parted") 
#                print "Lancement commande : ", pcmd," ",parg
                retcode = call([pcmd,'-s',path,'mklabel','msdos'])
                if retcode != 0:
                    print _("Error, can't create partition. Exiting")
                    sys.exit(1)
                print _("...OK!")
                pass
            else:
                return False
            
        """    def clonePartitions(self, diskinfo):
        """"""Create partition table indicated in diskinfo dict""""""
        print ("Disque à partitionner : ", diskinfo)
        for disk_to_erase in diskinfo:
            dev_path = disk_to_erase.encode('ascii')
            print "dev_path: ", dev_path
            
            print "********************"
            lDevice = reparted.Device(dev_path)
            print "********************"
            
            #disk is not empty. This will destroy all data on disk !
            if not self.createDosTableIfEmpty(dev_path):
                lDisk = reparted.Disk(lDevice)
                print '**********************************************************************************'
                print '* Le Disque n''est pas vide ! J''EFFACE TOUTES LES DONNEES malgré tout           *'
                print '**********************************************************************************'
                lDisk.delete_all()
                lDisk.commit()   
        
            cmd = "sfdisk %s < %s" %(dev_path,)
        """
        
    def makePartitions(self, diskinfo):
        """Create partition table indicated in diskinfo dict"""
        print (_("Disk to be partitionned : "), diskinfo)
        for disk_to_erase in diskinfo:
            dev_path = disk_to_erase.encode('ascii')
            print "dev_path: ", dev_path
            
            print "********************"
            lDevice = reparted.Device(dev_path)
            print "********************"
            
            
            #disk is not empty. This will destroy all data on disk !
            if not self.createDosTableIfEmpty(dev_path):
                lDisk = reparted.Disk(lDevice)
                print '**********************************************************************************'
                print _('* Disk is not empty ! ALL DATA ON DISK WILL BE DESTROYED WITHOUT PROMPT      *')
                print '**********************************************************************************'
                lDisk.delete_all()
                lDisk.commit()
            '''else:
                try:
                    lDisk = reparted.Disk(lDevice)
                except reparted.exception.DiskError as de:
                    if de.code == 600:
                        print 'Erreur 600 : examen Disk :', lDisk
                        pass

            print "Nouvelle table des partitions",
            lDisk.set_label('msdos') #new traditional partition table. It should be safe to use GPT instead but don't know windows mbr boot code compatibility 
            lDisk.commit()
            print "...OK!"'''
            
            #then wee scan all defined partitions in diskinfo dict
            
            #get number of partitions to create
            nbpart = len(diskinfo[disk_to_erase]) - 2 #last 2 elements of dict are not partitions 
            print ('**********************************************************************************')
            print _('New Disk partitioning. Number to create : %d') % nbpart
            print ('**********************************************************************************')
            
            for i in range(1,nbpart+1):
                partition = diskinfo[disk_to_erase][i]
                print _("partition to make : "), partition
                print _('Partition %d : nom=%s type=%s taille=%d MB' % (i, partition['name'], partition['type'], partition['size'])),
                newSize = reparted.Size(partition['size'], 'MB')
                if partition['type'] == "NTFS":
                    newPartition = reparted.Partition( disk=lDisk, size=newSize, fs='ntfs')
                if partition['type'] == "EXT3":
                    newPartition = reparted.Partition( disk=lDisk, size=newSize, fs='ext4')
                #set first partition as boot
                if i == 1:
                    newPartition.set_flag('BOOT', True)
                    
                lDisk.add_partition(newPartition)
                lDisk.commit()
                print _('....OK!')
            
            #Free space on disk is used as cache partition (local storage for disk img)
            print _('Add last partition for cache : ')
            freeSize =  lDisk.usable_free_space
            #print "freesize = ", freeSize
            newPartition = reparted.Partition( disk=lDisk, size=freeSize, fs='ext4')
            lDisk.add_partition(newPartition)
            lDisk.commit()
            print ('....OK!')
            self._cachepart = lDevice.path + str(nbpart+1)
            return True
    
    def _getCacheDevPath(self,path):
        """Get path device of the Cache Partition for further action (mount or format)
        get the last partition of ext4 type
        """
        dev = reparted.Device(path)
        disk = reparted.Disk(dev)
        num=0
        self._cachedev = ''
        for p in disk.partitions():
            if p.fs_type == 'ext4' and p.num() > num:
                num = p.num()
                self._cachedev = p.path
            
 
    def getdiskinfos(self):
        """return lits of disks with folowing elements
        {disk0 num,disq0 size, disq0 type [ {part0 num, par0 size, part0 name, part0 fstype, part0 fssize}, ...]}"""
        if len(self._diskinfo) != 0:
            return ( self._diskinfo) #no need to rescan disk info if already stored
        
        listdisks = {}
        localdisk = ''
        for localdisk in self._disks:
            #som usefull disk info
            diskdesc = {}
            ld= self._disks[localdisk]
            print ("retour ld = ", ld)
            for ll in ld[0].partitions:
                print "*****ll",ll
            #    print ll
            #    if ll is parted.Disk:
            #        print "************l",ll.partitions
                
            #we coud consider last letter in path of disk (/dev/sd? is the disk number
            diskdesc['num'] = ord( localdisk[-1]) - 97 #fast convert letter to integer using (utf-8) code
            diskdesc['size'] = self._size( '/sys/block/' + os.path.basename(localdisk) )
            diskdesc['type'] = ld[0].type
            #pick som usefull  partition info
            if not ld[1][0] is None:
                l_localpart=[]
                for p_partition in ld[1][0]: #element 1,0 of list is primarypartition list
                    #for p_partition in p_partitions:
                    d_part = {}
                    d_part['num']=p_partition.number
                    d_part['size']=int(round(p_partition.getSize('MB')))
                    d_part['name']=p_partition.name
                    if p_partition.fileSystem is None: #This could be free partition
                        d_part['fs_type'] = 'Unused'
                    else:
                        d_part['fs_type']=p_partition.fileSystem.type
                    l_localpart.append(d_part)
                diskdesc['PPartitions'] = l_localpart
            
            if not ld[1][1] is None:
                l_localpart=[]
                for e_partition in ld[1][2]:
                    d_part = {}
                    d_part['num']=e_partition.number
                    d_part['size']=int(round(e_partition.getSize('MB')))
                    d_part['name']=e_partition.name
                    if e_partition.fileSystem is None: #This could be free partition
                        d_part['fs_type'] = 'Unused'
                    else:
                        d_part['fs_type']=e_partition.fileSystem.type
                    l_localpart.append(d_part)
                diskdesc['EPartitions'] = l_localpart

            """TODO
            Add code for extended and Logical Partition
            """

            listdisks[localdisk] = diskdesc
        
        self._diskinfo = listdisks
        print 'List disks : ', listdisks
        #sys.exit()    
        return listdisks
    
    def isBootable(self):
        '''Return true if host is Bootable (defined partition with boot flag'''
        bootflag = False
        
        listdisks = self._disks
        for disk in listdisks:
            ld = listdisks[disk]
            if not ld[1][0] is None:
                for partition in ld[1][0]:
                    if partition.getFlag(parted.PARTITION_BOOT):
                        bootflag = True
                        break
        
        return bootflag
    
    
    
    """
    test if host is EFI
    @return: int 
    """   
    def isEFI(self):
        if os.path.exists("/sys/firmware/efi"):
            return 1
        return 0
    
    """test if disk is GPT
    @return: int (1-0)
    """
    def isGPT(self,path):
        disktype = self._disks[path][0].type
        if (disktype == 'gpt'):
            return 1
        return 0
        

    def _size(self, device):
        nr_sectors = open(device + '/size').read().rstrip('\n')
        sect_size = open(device + '/queue/hw_sector_size').read().rstrip('\n')
    
        # The sect_size is in bytes, so we convert it to GiB and then send it back
        return (float(nr_sectors) * float(sect_size)) / (1024.0 * 1024.0 * 1024.0)
        
    def _detect_devs(self, dev_pattern):
        """Detect block devices from /sys/block/ filesystem
        @param dev_patern: list of file dev paterns to look at (usually ['sd.*', 'mmcblk*', 'hd*'])  
        Add device path in _devices list member
        O size disks that may be detetected are ignored. This usually correspond to embeded multi-card readers
        """
        
        for device in glob.glob('/sys/block/*'):
            for pattern in dev_pattern:
                if re.compile(pattern).match(os.path.basename(device)):
                    print('Device:: {0}, Size:: {1} GiB'.format(device, self._size(device)))
                    if (self._size(device) == 0):
                        print _("***ignoring 0 size disk***")
                    else:
                        self._devices.append(parted.Device('/dev/%s' % os.path.basename(device)))
                        self.nbdev += self.nbdev
    
    
    def _detect_partition(self, device):
        """Try to dectec partictions and File systems on the specified device"""
        localdisk = None
        Ppartitions = None
        Epartition = None
        Lpartitions = None

        try:
            localdisk = parted.Disk(device)
            print ("localdisk = ", localdisk)
            Ppartitions = localdisk.getPrimaryPartitions()
            print "partitions = ", localdisk.getPrimaryPartitions()
            Epartition = localdisk.getExtendedPartition()
            
        except: #could occur if no partition table
            print _('No partition for this disk')
            
        if Epartition != None:
            Lpartitions = localdisk.getLogicalPartitions()
        
        localpartitions = (localdisk, [ Ppartitions, Epartition, Lpartitions])
        
        #un gros dictionnaire qui contient les disques et leurs partitions 
        strpath = device.path
        self._disks[strpath] = localpartitions
        
    def meminfo(self):
        ''' Return the information in /proc/meminfo
        as a dictionary '''
        meminfo=OrderedDict()
    
        with open('/proc/meminfo') as f:
            for line in f:
                meminfo[line.split(':')[0]] = line.split(':')[1].strip()
        return meminfo
        
        
