'''
 Common static functions for transfert use
 '''
 
import settings
import pyddlaj.db
import pyddlaj.host
import os

def removeLocalBoot(strmac):

    ret=False
    dstpath = settings.TFTPD_ROOT + settings.TFTP_ROOT + '/pxelinux.cfg'
    pxemacfile = '01-' + strmac.replace(':','-')
    dstfile = dstpath + "/" + pxemacfile
    print "effacement du fichier ", dstfile
    if os.path.isfile(dstfile):
        os.remove(dstfile)
        ret = True
    return ret
