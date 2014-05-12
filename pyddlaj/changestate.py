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


def action(jdb,dbhost,state,tid = 0):
   
    if state == "deploieidb":
        if tid > 0:
            jdb.setState(dbhost['nom_dns'], 'modifie')
            jdb.addTaskHost(tid, dbhost['nom_dns'])
            print "j'ajoute l'hôte à la tâche " + str(tid)
    else:
        jdb.setState(dbhost['nom_dns'], state)
        
    #All action require remove of local boot from the tftp server        
    transfert.removeLocalBoot(dbhost['adresse_mac'])
    

     

if __name__ == '__main__':
    if len(sys.argv) !=3:
        print "Usage : changestate hostname state"
        sys.exit(1)

    pp= pprint.PrettyPrinter()            
    dns = sys.argv[1]
    state = sys.argv[2]
    
    jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST, settings.MYSQL_USER, settings.MYSQL_PASSWORD, settings.MYSQL_DB)
    
    fh = jdb.findHostByName(dns, method='like')
    
    hostnumber = len(fh)
    if hostnumber == 0:
        print "Je n'ai pas trouvé d'enrgistrement correspondant à cet hôte : ", dns
        sys.exit(1)
    taskid=0
    if hostnumber > 0:
        print "Des enregistrements correspondent à votre recherche"
        while True:
            print "Sélectionnez celui qui vous intéresse :"
            num=1
            for row in fh:
                print "[%d] : %s" % (num, row['nom_dns'])
                num=num+1
            print "[0] : finir"
            val = raw_input("choix : ")
            #basic control of input
            if not val.isdigit():
                print "Mauvais nombre, recommencez"
            elif int(val) == 0:
                break
            elif int(val) < 1 or int(val) > len(fh):
                print "Mauvais nombre, recommencez"
            #if all OK we can launch updates on database
            else:
                val=int(val)
                print "Hôte sélectionné : ", fh[val-1]['nom_dns']
               
                if state == "deploieidb":
                    if taskid == 0:
                        taskid = jdb.createTask("deploieidb")
                        print "Nouvell tâche : ", taskid
                    
                action(jdb,fh[val-1],state,taskid)
                print "Mise à jour effectuée dans la base. Redémarrez le poste pour le traitement"
                if state != "deploieidb":
                    break
                

                del fh[val-1]

                            
    print "MERCI ! A Bientôt"
    jdb.close()
    
    pass