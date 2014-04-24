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

import sys ;sys.path.append(r'/home/rignier/.eclipse/org.eclipse.platform_3.8_155965261/plugins/org.python.pydev_3.4.1.201403181715/pysrc')
#import pydevd; pydevd.settrace(host='10.11.1.186')

jdb = pyddlaj.db.jeddlajdb(settings.MYSQL_HOST,settings.MYSQL_USER,settings.MYSQL_PASSWORD,settings.MYSQL_DB,3306)


def handle_client(sock):
    """ dedicated thread for incoming connection
    read task id and dns, find into database variables needed to launch udpcast"""
    print 'Connected by'
    data = sock.recv(1024)
    print "les data !", data
    tdata = data.partition(';') #split data with ;
    sock.close() #socket is no more needed
    
    #get task info whole record from database
    task = jdb.getTask(tdata[2], idonly=False)
    print "la tâche :", task
    if task['dte_deb'] is None:
        if task['type_tache'] == "deploieidb":
            #get needed info
            clinumber = jdb.getClientNumber(tdata[0])
            lidb = jdb.getIdbToInstall(tdata[2])
            print "nombre client : ", clinumber
            print "les images a deployer", lidb
            for img in lidb:
                cmd = ["/usr/bin/udp-sender"]
                cmd+= ["--file","/opt/pyddlaj/images/" +  img['imgfile']]
                cmd+= ["--ttl",str(int(tdata[0])+5)]
                cmd+=["--min-receivers",str(clinumber)]
                cmd+=["--nokbd"]
                cmd+=["--max-wait","900"]
                
                jdb.setTaskDate(task['tid'], True)
                print ("cmd",cmd)
                result = call(cmd);
                if result == 0:
                    jdb.setTaskDate(task['tid'], False)
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



HOST = ''                 # Symbolic name meaning all available interfaces
PORT = 12345              # Arbitrary non-privileged port
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.bind((HOST, PORT))
s.listen(5)
while 1:
    conn, addr = s.accept()
    thread = threading.Thread(target=handle_client, args=[conn])
    thread.daemon = True
    thread.start()

conn.close()