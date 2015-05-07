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
