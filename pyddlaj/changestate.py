#!/usr/bin/env python
# -*- coding: utf-8 -*-
'''
Created on 28 avr. 2014

@author: rignier
'''
import pprint
import sys
import pyddlaj.db
import settings
import transfert
from flufl.i18n import initialize
import os
import languages


def action(jdb,dbhost,state,tid = 0):
   
    if state == "deploieidb":
        if tid > 0:
            jdb.setState(dbhost['nom_dns'], 'modifie')
            jdb.addTaskHost(tid, dbhost['nom_dns'])
            print _("Adding host to task ID " + str(tid))
    else:
        jdb.setState(dbhost['nom_dns'], state)
        
    #All action require remove of local boot from the tftp server        
    transfert.removeLocalBoot(dbhost['adresse_mac'])
    

     

if __name__ == '__main__':
    
    os.environ['LANG'] = settings.LANG;
    os.environ['LOCPATH'] = os.path.dirname(languages.__file__)
    _ = initialize('changestate')

    enstate = ["osdeploy","osbackup","rename","reboot","debug"]
    frstate = ["deploieidb","idb","renomme","reboot","depannage"]
    if len(sys.argv) !=3 or (sys.argv[2] not in enstate and sys.argv[2] not in frstate):
        print _("Usage : changestate hostname newstate")
        print _("newstate must be one of : osdeploy,osbackup,rename,reboot,debug")
        sys.exit(1)
    
    if (sys.argv[2] in enstate):
        idx = enstate.index(sys.argv[2])
        state = frstate[idx]
    else:
        state = sys.argv[2]
    
    pp= pprint.PrettyPrinter()            
    dns = sys.argv[1]
    
    jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST, settings.MYSQL_USER, settings.MYSQL_PASSWORD, settings.MYSQL_DB)
    
    fh = jdb.findHostByName(dns, method='like')
    
    hostnumber = len(fh)
    if hostnumber == 0:
        print _("Haven't found any host in database with the name : "), dns
        sys.exit(1)
    taskid=0
    if hostnumber > 0:
        print _("Records found corresponding to your search")
        while True:
            print _("Choose one from list :")
            num=1
            for row in fh:
                print "[%d] : %s" % (num, row['nom_dns'])
                num=num+1
            print _("[0] : end")
            val = raw_input(_("choice : "))
            #basic control of input
            if not val.isdigit():
                print _("Bad value, please try again")
            elif int(val) == 0:
                break
            elif int(val) < 1 or int(val) > len(fh):
                print _("Bad number, please try again")
            #if all OK we can launch updates on database
            else:
                val=int(val)
                print _("Selected host : "), fh[val-1]['nom_dns']
               
                if state == "deploieidb":
                    if taskid == 0:
                        taskid = jdb.createTask("deploieidb")
                        print _("New task : "), taskid
                    
                action(jdb,fh[val-1],state,taskid)
                print _("Database updated. Please reboot the host for changes to take effect")
                #if state != "deploieidb":
                #    break
                

                del fh[val-1]

                            
    print _("Thank you! See ya")
    jdb.close()
    
    pass