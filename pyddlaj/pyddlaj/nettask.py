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
        self._sock.connect((settings.TFTP_SERVER,12345))
    
    
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
        
        