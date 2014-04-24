'''
Created on 10 avr. 2014

@author: rignier
'''

"""global Conf variables"""

MYSQL_HOST = '10.11.1.29'
MYSQL_DB = 'jeddlaj10'
MYSQL_USER = 'jeddlajadmin'
MYSQL_PASSWORD = 'jeddlajadmin'

TFTP_SERVER = '10.11.1.186'
TFTP_PORT = 69
TFTP_ROOT = '/ltsp/i386-osdeploy'

IMG_SERVER = '10.11.1.186'
IMG_NFS_SHARE = "/opt/pyddlaj/images"
IMG_NFS_MOUNT = "/nfsimg"

SSH_ROOT = '/srv/tftp' + TFTP_ROOT
SSH_PORT = 22

CACHE_MOUNT = '/localimg'

