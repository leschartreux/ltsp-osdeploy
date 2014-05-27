#!/usr/bin/env python
# -*- coding: utf-8 -*-
'''
Created on 18 avr. 2014

@author: rignier
'''
import socket
import threading
import daemon
import pyddlaj.db
import settings
from subprocess import call
from time import sleep

#Code for internationalisation
from flufl.i18n import initialize
import os
import languages

#import sys ;sys.path.append(r'/home/rignier/.eclipse/org.eclipse.platform_3.8_155965261/plugins/org.python.pydev_3.4.1.201403181715/pysrc')
#import pydevd; pydevd.settrace(host='10.11.1.186')


def handle_client(sock):
    """ dedicated thread for incoming connection
    read task_id and dns, find into database variables needed to launch udpcast"""
    print _('Connected by')
    data = sock.recv(1024)
    #print "les data !", data
    
    tdata = data.partition(';') #split data with ;
    sock.close() #socket is no more needed
    
    #get task info whole record from database
    jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST,settings.MYSQL_USER,settings.MYSQL_PASSWORD,settings.MYSQL_DB,3306)
    task = jdb.getTask(tdata[2], idonly=False)
    #jdb.close()
    print _("Task info:"), task
    if task['dte_deb'] is None:
        if task['type_tache'] == "deploieidb":
            #get needed info
            #jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST,settings.MYSQL_USER,settings.MYSQL_PASSWORD,settings.MYSQL_DB,3306)
            speed = task['speed'] - task['speed'] / 10 #10% less bandwith to avoid flood
            clinumber = jdb.getClientNumber(tdata[0])
            lidb = jdb.getIdbToInstall(tdata[2])
            print _("Clients number : "), clinumber
            print _("Master image to deploy"), lidb
            for img in lidb:
                cmd = ["/usr/bin/udp-sender"]
                cmd+= ["--file",settings.IMG_NFS_SHARE+"/" +  img['imgfile'] + ".gz"]
                cmd+= ["--ttl","32"]
                cmd+= ["--mcast-data-address" ,"239.11.1.186"]
                cmd+=["--min-receivers",str(clinumber)]
                cmd+=["--nokbd"]
                cmd+=["--max-wait","900"]
                cmd+=["--full-duplex"]
                cmd+=["--max-bitrate", str(speed) + "m"]
                
                jdb.setTaskDate(task['tid'], True)
                #print ("cmd",cmd)
                result = call(cmd);
                if result == 0:
         
                    jdb.close()
                    print _("Wait 15s for update Database from clients")
                    sleep(15)
                    #reconnect to get updates from client
                    jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST,settings.MYSQL_USER,settings.MYSQL_PASSWORD,settings.MYSQL_DB,3306)
                    jdb.setTaskDate(task['tid'], False)
    else:
        print _("Task ID %d already started. No need to launch UDP sender") % (task['tid'])
    jdb.close()
    pass # end of client handler
                    
                

"""   with sock.makefile() as f:
        sock.close()
        for line in f:
            f.writeline(line)"""

"""def serve_forever():
    print ("start server")
    server = socket.socket()
#    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server.bind(('10.11.1.186', 12345))
    server.listen(5)
    while True:
        conn, address = server.accept()
        thread = threading.Thread(target=handle_client, args=[conn])
        thread.daemon = True
        thread.start()

#with daemon.DaemonContext():
print "yoyo"
serve_forever()"""

# Echo server program
#import socket
os.environ['LANG'] = settings.LANG;
os.environ['LOCPATH'] = os.path.dirname(languages.__file__)
_ = initialize('pyddlajd')

HOST = ''                 # Symbolic name meaning all available interfaces
PORT = settings.PYDDLAD_PORT     # Arbitrary non-privileged port
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.bind((HOST, PORT))
s.listen(5)
while 1:
    conn, addr = s.accept()
    thread = threading.Thread(target=handle_client, args=[conn])
    thread.daemon = True
    thread.start()

conn.close()