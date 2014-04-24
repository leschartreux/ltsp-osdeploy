import mysql.connector
import host

class MySQLCursorDict(mysql.connector.cursor.MySQLCursor):
    """special class to have cursors retunr dict"""
    def fetchone(self):
        row = self._fetch_row()
        if row:
            return dict(zip(self.column_names, self._row_to_python(row)))
        return None

class jeddlajdb:    
    """Jeddlaj Mysql Database"""
    def __init__(self, host, user, pwd, db, port):
        self.host = host
        self.user = user
        self.pwd = pwd
        self.db = db
        self.port = port
        
        self._dbconnect = mysql.connector.Connect(user=user, host=host, password=pwd, db=db, port=port)
        self._cursor = self._dbconnect.cursor()
        print ("Connecion OK")
        
    def ShowTables(self):
        cursor = self._dbconnect.cursor()
        cursor.execute('SHOW TABLES;')
        for (name) in cursor:
            print (name)
        cursor.close()
    
    def findhost(self, strmac):
        """Find existing host in database. returns host object if found"""
        
        query = "Select * from ordinateurs where adresse_mac='" + strmac + "'"
        cursor = self._dbconnect.cursor()
        cursor.execute(query)
        foundhost = None
        for c in cursor:
            foundhost = c
        cursor.close()
        return foundhost
    
    def newhost(self, host):
        """Insert newhost in database
        @param host: host object returned locally"""
        
        meminfo = host.meminfo()
    
        sql = "insert into ordinateurs(nom_dns,adresse_ip,adresse_mac,processeur,nombre_proc,marque,modele,numero_serie,ram) values("
        sql += self.valsql(host.dns) + "," + self.valsql(host.ip) + "," + self.valsql(host.mac) + "," + self.valsql(host.proc) + "," + str(host.nbcpu)
        sql += "," + self.valsql(host.manuf) + "," + self.valsql(host.model) +","+ self.valsql(host.serial)+ "," + meminfo['MemTotal'].replace(" kB","")
        sql += ")"
        print "SQL req = ", sql
        cursor = self._dbconnect.cursor()
        cursor.execute(sql)
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
            
            sql =  "SELECT linux_device,num_partition,taille_partition,type_partition,nom_partition from partitions p where "
            sql += " p.num_disque=%s and p.nom_dns = %s" % (num_disque,self.valsql(dns))
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
        
    def getIdbToInstall(self,dns):
        """Get list of partitions and associated base image to Install"""
        listidb=[]
        cursor = self._dbconnect.cursor()
        sql =  "Select idb.id_idb, id_os, repertoire"
        sql += ",idbs.num_disque,num_partition,etat_idb"
        sql += ",s.nom_dns, linux_device"
        sql += " FROM images_de_base idb,idb_est_installe_sur idbs,stockages_de_masse s"
        sql += " WHERE s.nom_dns=" + self.valsql(dns)
        sql += " AND idbs.nom_dns=" + self.valsql(dns)
        sql += " AND idbs.num_disque=s.num_disque"
        sql += " AND idbs.id_idb = idb.id_idb"
        print "Sql = ", sql
        cursor.execute(sql)
        res = cursor.fetchall()
        for (id_idb,id_os,repertoire,num_disque,num_partition,etat_idb,nom_dns,linux_device) in res:
            row = {}
            row['id_idb']=id_idb
            row['id_os']=id_os
            row['imgfile']=repertoire
            row['num_disk']=num_disque
            row['num_part']=num_partition
            row['state']=etat_idb
            row['dns']=nom_dns
            row['dev_path']=linux_device
            
            listidb.append(row)
        cursor.close()
        return listidb
    
    def getTask(self,dns,idonly=True):
        """Get task id of host
        if id=True returns id only, else return row data
        """
        cur = self._dbconnect.cursor(cursor_class=MySQLCursorDict)
        sql = "SELECT t.id_tache as tid,type_tache,dte_deb,dte_fin,nb_ok,nb_ko from tache t,tache_est_assignee_a ta WHERE ta.id_tache=t.id_tache AND nom_dns=" + self.valsql(dns)
        cur.execute(sql)
        row = cur.fetchone()
        print "row = " , row
        if idonly == True:
            task=row['tid']
        else:
            task=row
            
        return task
    
    def getClientNumber(self,tid):
        """return number of hosts assiciated to task id"""
        sql = "SELECT count(*) from tache_est_assignee_a where id_tache=" + str(tid)
        self._cursor.execute(sql)
        row = self._cursor.fetchone()
        for (number) in row:
            clientnumber=number
        return clientnumber
            
    
    
    def deldisks(self,dns):
        '''Remove all disk records in theb of the specified host'''
        cursor = self._dbconnect.cursor()
        sql = "delete from partitions where nom_dns=" + self.valsql(dns)
        cursor.execute(sql)
        sql = "delete from stockages_de_masse where nom_dns=" + self.valsql(dns)
        cursor.execute(sql)
        cursor.close()
        
    def setTaskDate(self,tid,start):
        """
        update the task start time or end time fi start is false
        """
        sql = "UPDATE tache set "
        if (tid):
            sql+= "dte_deb"
        else:
            sql += "dte_end"
        
        sql +="=now() Where id_tache=" + str(tid)
        
        self._cursor.execute(sql)
        
        
        
    
    def addpartitions(self,dns,diskinfo):
        '''insert int db disks and partitions information'''
        ''' following dictionary example '''
        '''{'/dev/sdb': 
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
            sql = "Insert into stockages_de_masse (nom_dns,num_disque,capacite,linux_device) values("
            sql += self.valsql(dns) + "," + str(num_disque) + "," + str(capacite) + "," + self.valsql(linux_device) +")"
            
            
            cursor.execute(sql)
            
            print "Insertion disque :" , sql
            print ("info disque : ", diskinfo)
            if 'PPartitions' in diskinfo[disk].keys(): #Partitions info could be empty 
                for p_partition in diskinfo[disk]['PPartitions']:
                    num_partition = p_partition['num']
                    taille_partition = p_partition['size']
                    type_partition = p_partition['fs_type']
                    nom_partition = p_partition['name']
                    plinux_device = linux_device + str(num_partition)
                    sql = "Insert into partitions (nom_dns,num_disque,num_partition,taille_partition,type_partition,nom_partition,linux_device) values("
                    sql += self.valsql(dns) + ","
                    sql += str(num_disque) + ","
                    sql += str(num_partition) + ","
                    sql += str(taille_partition) +","
                    sql += self.valsql(type_partition) + ","
                    sql += self.valsql(nom_partition) + ","
                    sql += self.valsql(plinux_device) + ")"
                    print "INSERTION PARTITION : ", sql
                    cursor.execute(sql)
        cursor.close()
    
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
        cursor = self._dbconnect.cursor()
        sql = "UPDATE ordinateurs set etat_install=%s where nom_dns=%s" % self.valsql(state) ,self.valsql(dns)
        cursor.execute(sql)            
        
        
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
        self._dbconnect.close()
        
    
    
