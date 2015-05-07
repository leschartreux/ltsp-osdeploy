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
 
'''
Created on 18 avr. 2014

@author: rignier
'''

import socket
import settings

class NetTask(object):
    '''
    send Task to listening server
    goal is to sync udpcast transfert in case of multiple host
    '''


    def __init__(self):
        '''
        Constructor
        '''
        self._sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self._sock.connect((settings.TFTP_SERVER,settings.PYDDLAD_PORT))
    
    
    def send(self,id_task,dns):
        '''
        send to the server the task id and host name
        if host is the first member of the task, the server launch corresponding udpcast transfert
        transfert will launch as soon as all clients are connected or timeout
        '''
        data = str(id_task)+";"+dns #id_task and dns are simply separated with ;
        #print "j'envoie data : ", data
        self._sock.send(data)
        
    
    def close(self):
        self._sock.close()
        
        