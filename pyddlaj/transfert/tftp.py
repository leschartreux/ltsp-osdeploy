'''
Created on 10 avr. 2014

@author: rignier
'''
import settings
#import tftpy

'''
def tftplocalboot(mac):
    """Copy via tftp the local boot pxe config
    for specified mac addres
    @param: mac Hosts's Mac adrress
    """
    client = tftpy.TftpClient(settings.TFTP_SERVER, settings.TFTP_PORT)
    source = 'conf/pxebootlocal'
    dest = ('%s/pxelinux.cfg/01-%s' % (settings.TFTP_ROOT, mac.replace(':', '-')))
    print "TFTP file from %s to %s" % (source, dest)
    client.upload(source, dest)
    '''
            
