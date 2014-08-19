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
 


import mysql.connector
#import host

from flufl.i18n import initialize
import settings

#import languages

_= initialize('pyddlaj_client')


class MySQLCursorDict(mysql.connector.cursor.MySQLCursor):
    """special class to have cursors retunr dict"""
    def fetchone(self):
        row = self._fetch_row()
        if row:
            return dict(zip(self.column_names, self._row_to_python(row)))
        return None

class jeddlajdb:    
    """Jeddlaj Mysql Database"""
    def __init__(self, host, user, pwd, db, port=3306):
        self.host = host
        self.user = user
        self.pwd = pwd
        self.db = db
        self.port = port
        
        self._dbconnect = mysql.connector.Connect(user=user, host=host, password=pwd, db=db, port=port, autocommit=True)
        self._cursor = self._dbconnect.cursor()
        print _("Connection OK")
        
    def ShowTables(self):
        cursor = self._dbconnect.cursor()
        cursor.execute('SHOW TABLES;')
        for (name) in cursor:
            print (name)
        cursor.close()
    
    def findhost(self, strmac):
        """Find existing host in database. returns host object if found"""

        cursor = self._dbconnect.cursor(cursor_class=MySQLCursorDict)
        query = "Select * from ordinateurs where adresse_mac='" + strmac + "'"
        
        ''' cursor = self._dbconnect.cursor()
        cursor.execute(query)
        foundhost = None
        for c in cursor:
            foundhost = c
        cursor.close()
        return foundhost'''
        
        cursor.execute(query)
        #Row dictionary type is available for fetchone only
        #we store them all in a list
        fh = None
        while True:
            row = cursor.fetchone()
            if row:
                fh = row
            else:
                break
        
        cursor.close()
        return fh
    
    def findHostByName(self,strdns,method='exact'):
        '''Find computer(s) in database with dns name
        if method = exact, nom_dns from database must match strdns. return None when not found
        else it will return all rows containing strdns'''
        
        #it is better to get rows as dict : {field1:value1,field2:value2, ...}
        cursor = self._dbconnect.cursor(cursor_class=MySQLCursorDict)
        query = "Select * from ordinateurs where nom_dns"
        if method == "exact":
            query += "=" + self.valsql(strdns)
        else:
            query += " like '%" + strdns + "%'"
        
        cursor.execute(query)
        rows = []
        #Row dictionary type is available for fetchone only
        #we store them all in a list
        while True:
            row = cursor.fetchone()
            if row:
                rows.append(row)
            else:
                break
        
        cursor.close()
        if method == "exact":
            if len(rows)==0:
                return None
            return rows[0]
        else:
            return rows
    
    def newhost(self, host):
        """Insert newhost in database
        @param host: host object returned locally"""
        
        meminfo = host.meminfo()
    
        sql = "insert into ordinateurs(nom_dns,adresse_ip,adresse_mac,processeur,nombre_proc,marque,modele,numero_serie,ram,affiliation_widows,nom_affiliation) values("
        sql += self.valsql(host.dns) + "," + self.valsql(host.ip) + "," + self.valsql(host.mac) + "," + self.valsql(host.proc) + "," + str(host.nbcpu)
        sql += "," + self.valsql(host.manuf) + "," + self.valsql(host.model) +","+ self.valsql(host.serial)+ "," + meminfo['MemTotal'].replace(" kB","")
        sql += ",'domain'," + self.valsql(settings.AD_DOMAIN)
        sql += ")"
        #print "SQL req = ", sql
        cursor = self._dbconnect.cursor()
        cursor.execute(sql)
        sql = "INSERT into ord_appartient_a_gpe values ('%s','tous les ordinateurs')" % (host.dns)
        cursor.execute(sql)
        self._dbconnect.commit()
        cursor.close()
        
    def updateHost(self, host):
        """update host info in database
        @param host: host object returned locally"""
        
        meminfo = host.meminfo()
    
        sql = "update ordinateurs set "
        sql +="adresse_ip=" + self.valsql(host.ip) + ",adresse_mac=" + self.valsql(host.mac) + ",processeur=" + self.valsql(host.proc) + ",nombre_proc=" + str(host.nbcpu)
        sql += ",marque=" + self.valsql(host.manuf) + ",modele=" + self.valsql(host.model) +",numero_serie="+ self.valsql(host.serial)+ ",ram=" + meminfo['MemTotal'].replace(" kB","")
        sql += " WHERE nom_dns=" + self.valsql(host.dns)
        print "SQL req = ", sql
        cursor = self._dbconnect.cursor()
        cursor.execute(sql)
        self._dbconnect.commit()
        cursor.close()
    
    def getdisks(self,dns):
        
        sql = "Select * from stockages_de_masse where nom_dns=" + self.valsql(dns)
        cursor = self._dbconnect.cursor()
        cursor.execute(sql)
        disks = cursor.fetchall()
        
        return disks
    
    def diskPartitions(self,dns,tocreate=True):
        """Returns dict which represent disks and partitions to create"""
        """if tocreate=False return system partitions list"""
        disklist={}
        cursor = self._dbconnect.cursor()
        sql =  "SELECT s.linux_device,s.num_disque,capacite from stockages_de_masse s where "
   
        sql += "s.nom_dns=" + self.valsql(dns)
        if tocreate:
            sql += " and dd_a_partitionner='oui'"

#        print ( "requete sql =" , sql)
        cursor.execute(sql)
        result = cursor.fetchall()
#        print "resultat : ", result
        for (diskpath,num_disque,capacite) in result:
            disklist[diskpath]={}
            disklist[diskpath]['size']=capacite
            disklist[diskpath]['num']=num_disque
            
            sql =  "SELECT linux_device,idbs.num_partition,taille_partition,type_partition,nom_partition"
            sql += " FROM partitions p, idb_est_installe_sur idbs, images_de_base idb"
            sql += " WHERE p.num_disque=%s and p.nom_dns = %s" % (num_disque,self.valsql(dns))
            
#            print ("sql", sql)
            cursor2 = self._dbconnect.cursor()
            cursor2.execute(sql)
            result2 = cursor2.fetchall()
            for (linux_device,num_partition,taille_partition,type_partition,nom_partition) in result2:
                partition={}
                partition['path']=linux_device
                partition['num'] = num_partition
                partition['size'] = taille_partition
                partition['type'] = type_partition
                partition['name'] = nom_partition
                disklist[diskpath][num_partition] = partition
        
        return disklist
    
    def updateHddToPart(self,dns,act):
        """Update Action on hosts hard drive
        @param dns: host id
        @param act: value to update true or false 
        """
        
        cursor=self._dbconnect.cursor()
        if (act):
            action = 'oui'
        else:
            action = 'non'
            
        sql = "UPDATE stockages_de_masse set dd_a_partitionner=" + self.valsql(action) + " where nom_dns=" + self.valsql(dns)
        cursor.execute(sql)
        self._dbconnect.commit()
    

    def addDists(self,dns,diskinfo):
        """Ask about distribution to associate with the host"""
        sql = "Select DISTINCT id_logiciel,nom_logiciel from images_de_base idb,logiciels where idb.id_os=id_logiciel"
        cursor = self._dbconnect.cursor(cursor_class=MySQLCursorDict)
        cursor.execute(sql)
        row=cursor.fetchone()
        idbs = []
        while row:
            idbs.append((row['id_logiciel'],row['nom_logiciel']))
            row=cursor.fetchone()

        print _("Please choose the distrib : ")
        while True:
            num=1
            for idb in idbs:
                print "[%d]: %s" % (num,idb[1])
                num+=1
                
            val = raw_input("Choice : ")
            if not val.isdigit():
                print _("Bad Number")
                continue
            val = int(val)
            if val < 1 or val > num:
                print _("Number not in range")
                continue
            break
        print "idbs : " , idbs     
        id_os = idbs[val-1][0]
        sql = "Select * from images_de_base where id_os= " + str(id_os)            
        cursor.execute(sql)
        row = cursor.fetchone()
        while row:
            for disk in diskinfo:
                disk_num = diskinfo[disk]['num']
                print "***********************"
                print _("Disk num ") + str(disk_num)
                print "***********************"
                while True:
                    print _("List of available partitions")
                    num=1
                    for part in diskinfo[disk]['PPartitions']:
                        print "[%d] : Number:%d, size: %s" % (num,part['num'],part['size'])
                        num+=1
                    val = raw_input("Choice for image " + row['nom_idb'] + " of size " + row['taille'] + " : ")
                    if not val.isdigit():
                        print _("Bad number")
                        continue
                    val = int(val)
                    if val < 1 or val > num:
                        print _("Number not in range")
                        continue
                    break
                
                sql = "Insert into idb_est_installe_sur (id_idb,nom_dns,num_disque,num_partition,etat_idb,idb_active) VALUES("
                sql += str(row['id_idb']) + ","
                sql += self.valsql(dns) + ","
                sql += str(disk_num) + "," + str(diskinfo[disk]['PPartitions'][val-1]['num']) + ","
                sql +="'installe','oui')"
                
                #print "verif sql" , sql
                self._cursor.execute(sql)
                del  diskinfo[disk]['PPartitions'][val-1]
                #next possible base image for the distrib
                row = cursor.fetchone()
        print _("OK, Insert finished. I will now save the images.")
                    
            
       
    
    def newDists(self,dns,diskinfo):
        '''Ask questions about new Distributions  and set of base Image'''
        oses = self.getOsList()
        while True: #For multiboot oses, more than one distrib could be added

            print "***********************************"
            print _("*     Add distribution      *")
            print "***********************************"
            print _("Which os is installed on this host ?")
            num=1
            for os in oses:
                print "[%d]: %s" % (num,os[1])
                num+=1
            val = raw_input(_("Choice : "))
            if not val.isdigit():
                print _("Bad number")
                continue
            val = int(val)
            if val < 1 or val > num:
                print _("Number not in range")
                continue
            #print 'oses',oses
            nom_os = oses[val-1][1]
            id_os = oses[val-1][0]
            
            #get default logo for distrib
            if "XP" in nom_os:
                logo = "windowsxpsp3.jpg"
            elif "7" in nom_os:
                logo = "win7.jpg"
            elif "2008" in nom_os:
                logo = "windows.png"
            elif "8" in nom_os:
                logo = "win8.jpg"
            elif "ubuntu" in nom_os:
                logo = "linux.jpg"
            elif "debian" in nom_os:
                logo = "debian.png"
            else:
                logo = "windows.jpg" 
            
            val= raw_input(_("Enter name for this distribution\n Adding computer model is a good idea for easier reference (ex : Fujitsu Esprimo P2520 winxp SP3 2014): "))
            nom_logiciel = val
            
            val = raw_input(_("Enter distribution version (SP1, R2, Lenny, ...) : "))
            version = val
            
            sql =  "INSERT INTO logiciels (nom_logiciel,icone,version,nom_os,idos) VALUES("
            sql += self.valsql(nom_logiciel) + ","
            sql += self.valsql(logo) + ","
            sql += self.valsql(version) + ","
            sql += self.valsql(nom_os) + ","
            sql += str(id_os) + ")";
            self._cursor.execute(sql)
           # print "verif requete : ", sql
            self._cursor.execute("select LAST_INSERT_ID()")
            result = self._cursor.fetchall()
            for r in result:
                num_logiciel = r[0]
            
            print _("Created dist number : "),num_logiciel
           
            
            for disk in diskinfo: #all disks are scanned
                print "disk", disk
                disk_num = diskinfo[disk]['num']
                print "***********************"
                print _("Disk num ") + str(disk_num)
                print "***********************"
                num_part = 1
                #add partitions loop
                while True:
                    print "*******************"
                    print _("Partition num ") + str(num_part)
                    print "*******************"
                    num = 1
                    if len( diskinfo[disk]['PPartitions']) == 0:
                        print _("No more partition available")
                        break
                    
                    print _("Choose one parition ")
                    for partition in diskinfo[disk]['PPartitions']:
                        print _("[%d]: %s type fs: %s size : %dMB" % (num,partition['num'],partition['fs_type'],partition['size']))
                        num+=1
                    np = raw_input(_("choice : "))
                    if not np.isdigit():
                        print _("Bad number")
                        continue
                    np = int(np)
                    if (np <0 or np >num):
                        print _('Number not in range')
                        continue
                    #extract partitions info
                    part =  diskinfo[disk]['PPartitions'][np-1]
                    size = part['size']
                    fs_type = part['fs_type']
                    
                    print _("Filename for partition backup. example : osname/manufacturer/model_[version]/sdax.pc")
                    repertoire= raw_input(_("Enter partition's backup filename : "))
                    
                    print _("Partition name")
                    print _(" -For windows Vista and above you must type keyword 'boot' for the boot partition (ex : win81_boot)")
                    print _(" -For linux distributions you must type  the mount point of the partition")
                    nom_idb = raw_input(_("partition name : "))
                    
                    val = raw_input(_("Confirm for this partition (Y/N) ? "))
                    if  not (val == 'Y' or val == 'y'):
                        continue
                    
                    #add idb and idb_est_installe_sur records
                    sql = "INSERT INTO images_de_base (id_os,nom_idb,repertoire,taille,num_part,fs_type) VALUES ("
                    sql += str(num_logiciel) + ","
                    sql += self.valsql(nom_idb) + ","
                    sql += self.valsql(repertoire) + ","
                    sql += self.valsql(str(size) + " MB") + ","
                    sql += str(num_part) + "," 
                    sql += self.valsql(fs_type) + ")"
                    #print "verif requete : ", sql
                    self._cursor.execute(sql)
                    self._cursor.execute("select LAST_INSERT_ID()")
                    result = self._cursor.fetchall()
                    for r in result:
                        id_idb = r[0]
                    sql = "INSERT INTO idb_est_installe_sur (id_idb,nom_dns,num_disque,num_partition,etat_idb,idb_active) VALUES("
                    sql += str(id_idb) + ","
                    sql += self.valsql(dns) + ","
                    sql += str(disk_num)+ "," + str(num_part) + ","
                    sql += "'installe','oui')"
                    self._cursor.execute(sql)
                    #print "verif requete : ", sql
                    #remove partition entry as it is already allocated
                    #it won't be listed in choice
                    del  diskinfo[disk]['PPartitions'][np-1]
                    
                    val = raw_input("Other partition to add (Y/N) ? ")
                    if  val == 'Y' or val == 'y':
                        num_part+=1
                    else:
                        break
                self._dbconnect.commit()
            #Ask for other distribution present in the host    
            val = raw_input(_("Another distrib on this computer you want to add (Y/N) ? "))
            if  val == 'Y' or val == 'y':
                continue #back to Distrib Prompt
            else:
                break #get out of loop, return to main program
                 
        
    def getIdbToInstall(self,dns):
        """Get list of partitions and associated base image to Install"""
        listidb=[]
        cursor = self._dbconnect.cursor()
        sql =  "Select idb.id_idb, id_os, repertoire"
        sql += ",idbs.num_disque,num_partition,etat_idb"
        sql += ",s.nom_dns, s.linux_device, fs_type"
        sql += " FROM images_de_base idb,idb_est_installe_sur idbs,stockages_de_masse s"
        sql += " WHERE s.nom_dns=" + self.valsql(dns)
        sql += " AND idbs.nom_dns=" + self.valsql(dns)
        sql += " AND idbs.num_disque=s.num_disque"
        sql += " AND idbs.id_idb = idb.id_idb"
        #print "Sql = ", sql
        cursor.execute(sql)
        res = cursor.fetchall()
        for (id_idb,id_os,repertoire,num_disque,num_partition,etat_idb,nom_dns,linux_device,fs_type) in res:
            row = {}
            row['id_idb']=id_idb
            row['id_os']=id_os
            row['imgfile']=repertoire
            row['num_disk']=num_disque
            row['num_part']=num_partition
            row['state']=etat_idb
            row['dns']=nom_dns
            row['dev_path']=linux_device
            row['fs_type']=fs_type
            
            listidb.append(row)
        cursor.close()
        return listidb
    
    def getOs(self,dns):
        """Get dict of Oses and partitions installed on the host """
        
        cursor = self._dbconnect.cursor(cursor_class=MySQLCursorDict)
        listOs=[]
        
        sql = "Select os.nom_os,idb.nom_idb,concat(s.linux_device,cast(idbs.num_partition as char)) as dev_path"
        sql +=" FROM os,logiciels,idb_est_installe_sur idbs,images_de_base idb,stockages_de_masse s"
        sql += " WHERE logiciels.id_logiciel=idb.id_os"
        sql += " AND os.idos=logiciels.idos"
        sql += " AND idbs.id_idb=idb.id_idb"
        sql += " AND idbs.num_disque=s.num_disque"
        sql += " AND s.nom_dns=" + self.valsql(dns)
        sql += " AND idbs.nom_dns=" + self.valsql(dns)
        
        #print "sql : ",sql
        
        cursor.execute(sql)
        while True:
            row = cursor.fetchone()
            if row:
                listOs.append(row)
                print "row = ", row
            else:
                break
            
        cursor.close()
        return listOs
    
    def getOsList(self):
        """return list of oses in database"""
        sql = "SELECT idos,nom_os from os"
        self._cursor.execute(sql)
        rows = self._cursor.fetchall()
        oses = []
        for row in rows:
            oses.append( (row[0],row[1]) )
        return oses
    

    
    def getClientNumber(self,tid):
        """return number of hosts assiciated to task id"""
        query = "SELECT count(*) as nb from tache_est_assignee_a where id_tache=%s" % ( str(tid) )
        #print "requete en cours : ", query
        cursor = self._dbconnect.cursor()
        cursor.execute(query)
        row =cursor.fetchall()
        print "row : " ,row
        for (r) in row:
            clientnumber=r[0]
        cursor.close()
        return clientnumber
            
    
    def deldisks(self,dns):
        '''Remove all disk records in theb of the specified host'''
        cursor = self._dbconnect.cursor()
        sql = "delete from partitions where nom_dns=" + self.valsql(dns)
        cursor.execute(sql)
        sql = "delete from stockages_de_masse where nom_dns=" + self.valsql(dns)
        cursor.execute(sql)
        cursor.close()
        
    def getTask(self,dns,idonly=True):
        """Get task id of host. Look into not terminated tasks only
        if id=True returns id only, else return row data
        """
        cur = self._dbconnect.cursor(cursor_class=MySQLCursorDict)
        #
        sql = "SELECT t.id_tache as tid,type_tache,dte_deb,dte_fin,nb_ok,nb_ko,speed,faire_jointure from tache t,tache_est_assignee_a ta WHERE ta.id_tache=t.id_tache AND dte_fin is null AND nom_dns=" + self.valsql(dns)
        cur.execute(sql)
        row = cur.fetchone()
        #print "row = " , row
        
        
        #There is no found task for specified host.
        if not row:
            return None
        
        if idonly == True: #Only first row is taken
            task=row['tid']
        else:
            task=row
        
        while True: #Clean others rows
            row = cur.fetchone()
            if not row:
                break
        cur.close()
            
        return task
    
    def createTask(self,task_type):
        """Create a new Task and return its id"""
        sql = "INSERT INTO tache (type_tache) values(" + self.valsql(task_type) + ")"
        self._cursor.execute(sql)
        sql= "SELECT LAST_INSERT_ID()"
        self._cursor.execute(sql)
        row = self._cursor.fetchall()
        for r in row:
            newid=r[0]
        
        return newid
    
    def addTaskHost(self,tid,dns):
        """Add host to a created task
        @param tid: Task ID
        @param dns: hostname"""
        
        sql = "INSERT into tache_est_assignee_a values (" + str(tid) + "," + self.valsql(dns) + ")"
        self._cursor.execute(sql)
        pass
  
        

    def setTaskDate(self,tid,start):
        """
        update the task start time or end time if start is false
        """
        sql = "UPDATE tache set "
        if (start):
            sql+= "dte_deb"
        else:
            sql += "dte_fin"
        

        sql +="=now() Where id_tache=" + str(tid)

        #we must check if all transferts are terminateted before updating table        
        if not start:
            testsql = "Select nb_ok + nb_ko as nbs from tache where id_tache=" + str(tid)
            self._cursor.execute(testsql)
            row = self._cursor.fetchall()
            for r in row:
                nb = r[0]
            print _("Record : "), r , _(" tache :"), row
            testsql = "Select count(*) from tache_est_assignee_a where id_tache=" + str(tid)
            self._cursor.execute(testsql)
            row = self._cursor.fetchall()
            for r in row:
                tot = r[0]
            print "nb finished:",nb," tot:",tot
            if nb >= tot:        
                self._cursor.execute(sql)
                self._dbconnect.commit()
            else:
                print _("There are still not finished transfers for this task. No update")
        else:
            self._cursor.execute(sql)
            self._dbconnect.commit()        
        
    
    def addTaskOK(self,tid):
        """
        Increment Successfull count of task
        """
        sql = "UPDATE tache set nb_ok=nb_ok+1 where id_tache=" + str(tid)
        self._cursor.execute(sql)
        self._dbconnect.commit()
    
    def addTaskKO(self,tid):
        """
        Increment unsuccessfull count of task
        """
        sql = "UPDATE tache set nb_ko=nb_ko+1 where id_tache=" + str(tid)
        self._cursor.execute(sql)
        self._dbconnect.commit()
        
    def updatePartitions(self,dns,diskinfo,disk_number):
        """This should be called just after repartitionning from a dump file"""
        
        sql = "delete from partitions where nom_dns=" + self.valsql(dns)
        self._cursor.execute(sql)
        self._dbconnect.commit()
        self.addpartitions(dns, diskinfo,disk_num_only=disk_number)

        
    def addpartitions(self,dns,diskinfo,disk_num_only=-1):
        '''insert int db disks and partitions found
        if disk_num_only >=0, insert only partition of the disk number
        diskinfo is dict with following example
        {'/dev/sdb': 
              {'num': 1, 'PPartitions': 
                         [{'num': 1, 'fs_type': 'ext4', 'name': None, 'size': 28},
                          {'num': 2, 'fs_type': 'ext4', 'name': None, 'size': 122}],
               'size': 150.0},
           '/dev/sdc': 
              {'num': 2, 'PPartitions': 
                         [{'num': 1, 'fs_type': 'linux-swap(v1)', 'name': None, 'size': 4}],
              'size': 4.0},
           '/dev/sda': 
              {'num': 0, 'PPartitions': 
                         [{'num': 1, 'fs_type': 'ext3', 'name': None, 'size': 8}],
              'size': 8.0}
           }'''
        
        cursor = self._dbconnect.cursor()
        for disk in diskinfo:
            linux_device = disk
            capacite = diskinfo[disk]['size']
            num_disque =  diskinfo[disk]['num']
            if (disk_num_only == -1 ):
                sql = "Insert into stockages_de_masse (nom_dns,num_disque,capacite,linux_device) values("
                sql += self.valsql(dns) + "," + str(num_disque) + "," + str(capacite) + "," + self.valsql(linux_device) +")"
                cursor.execute(sql)
                #print "Insertion disque :" , sql
            else:
                if num_disque != disk_num_only:
                    continue
            
            print ("Disk informations : ", diskinfo)
            if 'PPartitions' in diskinfo[disk].keys(): #Partitions info could be empty 
                for p_partition in diskinfo[disk]['PPartitions']:
                    num_partition = p_partition['num']
                    taille_partition = p_partition['size']
                    type_partition = p_partition['fs_type']
                    nom_partition = p_partition['name']
                    plinux_device = linux_device + str(num_partition)
                    sql = "Insert into partitions (nom_dns,num_disque,num_partition,taille_partition,type_partition,nom_partition,systeme,linux_device) values("
                    sql += self.valsql(dns) + ","
                    sql += str(num_disque) + ","
                    sql += str(num_partition) + ","
                    sql += str(taille_partition) +","
                    sql += self.valsql(type_partition) + ","
                    sql += self.valsql(nom_partition) + ","
                    sql += "'oui',"
                    sql += self.valsql(plinux_device) + ")"
                    #print "INSERTION PARTITION : ", sql
                    cursor.execute(sql)
        cursor.close()
        self._dbconnect.commit()
        
    
    def getState(self,dns):
        """Get status of host in Database
        @param dns: host's dns name
        """
        state = None
        cursor = self._dbconnect.cursor()
        sql = "SELECT etat_install from ordinateurs where nom_dns=" + self.valsql(dns)
        cursor.execute(sql)
        for c in cursor.fetchall():
            state = c[0]
        return state
    
    def setState(self,dns,state):
        """Set status of host in Database
        @param dns: host's dns name
        """
        
        sql = "UPDATE ordinateurs set etat_install=%s where nom_dns=%s" % (self.valsql(state) ,self.valsql(dns))
        #print "sql = ",sql
        self._cursor.execute(sql)
        self._dbconnect.commit()            
        
        
    def valsql(self, value):
        """convert sql value from val
        @param val: String value to convert
        @return: NULL for empty string quoted value else
        """
        #print "La valeur : ", value
        if value == None or len(value) == 0:
            return 'NULL'
        else:
            return ("'%s'" % value.replace("'", "''"))
    
    def close(self):
        self._dbconnect.commit()
        self._cursor.close()
        self._dbconnect.close()
        
    
    
