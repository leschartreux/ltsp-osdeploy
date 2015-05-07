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
            
