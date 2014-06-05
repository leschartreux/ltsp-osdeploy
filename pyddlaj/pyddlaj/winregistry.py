# -*- coding: utf-8 -*-
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
Created on 25 avr. 2014

@author: rignier
'''

import os
from subprocess import call
import settings
import shutil
from string import Template

from flufl.i18n import initialize
_ = initialize('pyddlaj_client')

class WinRegistry(object):
    '''
    Manipulates the windows Registry via reged tool and ntfs3G mounting
    '''


    def __init__(self, dev_path):
        '''
        Mount windows partition
        @param dev_path: Path of Windows partition device (usually /dev/sda1 for XP or /dev/sda2 for Vista and above) 
        '''
        if not os.path.isdir(settings.NTFS_MOUNT):
            print _("Make directory ") + settings.NTFS_MOUNT
            os.mkdir(settings.NTFS_MOUNT)
        if not os.path.ismount(settings.NTFS_MOUNT):
            print _("Mount Windows system partition")
            #mount NTFS partition with lower case only to avoid file names between windows version v
            call(['/sbin/mount.lowntfs-3g',"-o","ignore_case",dev_path,settings.NTFS_MOUNT])
        
        self._configpath = settings.NTFS_MOUNT + "/" + "Windows/System32/Config/".lower()
        self._systemfile = self._configpath + "system"
        self._softwarefile = self._configpath + "software"
        self._reged = settings.BASE_DIR + "/tools/reged.static"
        self._joindir = settings.NTFS_MOUNT + "/joindom"

    def getReg(self,key,filename='/tmp/regfile.reg',filehive='system'):
        cmd = [self._reged,'-x']
        if filehive=='system':
            cmd += [self._systemfile,"HKEY_LOCAL_MACHINE\\SYSTEM", key  ]
        if filehive=='software':
            cmd += [self._softwarefile,"HKEY_LOCAL_MACHINE\\SOFTWARE", key  ]
        cmd += [filename]
        
        call(cmd)
        regfile = RegFile(filename)
        return regfile
    
    def UpdateReg(self,filename,filehive='system'):
        cmd = [self._reged,'-C','-I']
        if filehive=='system':
            cmd += [self._systemfile,"HKEY_LOCAL_MACHINE\\SYSTEM", filename  ]
        if filehive=='software':
            cmd += [self._softwarefile,"HKEY_LOCAL_MACHINE\\SOFTWARE", filename  ]
        cmd += [filename]
        
        call(cmd)
        regfile = RegFile(filename)
        return regfile
    
    def addRunOnceJoin(self,type_os,cmd):
        """This method will modify windows registry to run command once
        before login screen
        """
        
        launchfile = "/tmp/launch.cmd"
        f = open (launchfile,'w')
        f.write("C:\r\n")
        f.write("CD C:\\joindom\r\n")
        if "vbs" in cmd:
            join_bat="net start dhcp\r\n"
            join_bat+="ipconfig /renew\r\n"
            join_bat+="net start workstation\r\n"
            join_bat+="net start server\r\n"
            join_bat+="net start lmhosts\r\n"
            join_bat+="net start winmgmt\r\n"
            join_bat+="nbtstat -R\r\n"
            join_bat+="echo Modification du Registre...\r\n"
            join_bat+="reg add \"HKLM\\System\\Setup\" /v SystemSetupInProgress /t REG_DWORD /d 00000000 /f\r\n"
            join_bat+=("C:\\Windows\\System32\\cscript.exe C:\\joindom\\" + cmd + "\r\n")
#            join_bat+="echo Modification du Registre...\r\n"
#            join_bat+="reg add \"HKLM\\System\\Setup\" /v SystemSetupInProgress /t REG_DWORD /d 00000001 /f\r\n"
            f.write(join_bat)
        else:
            f.write("Call " + cmd + "\r\n")
        #f.write("Call C:\\Windows\\System32\\cmd.exe") # interactive cmd thi is for debug
        #f.write("pause\r\n")
        f.write("DEL " + cmd + "\r\n") #self suppress of file to hide join passwords
        f.write("suthdown -r\r\n")
        f.close()
        shutil.copyfile('/tmp/launch.cmd',self._joindir + "/launch.cmd")
        
        
        regfile =RegFile('/tmp/regfilesw.reg',overwrite=True)
        
        #We activate local powershell execution in registry first        
        if "ps1" in cmd:
            regfile.addKey("HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\PowerShell\\1\\ShellIds\\Microsoft.PowerShell")
            regfile.addValue("ExecutionPolicy","\"RemoteSigned\"")
     

        #disable UAC for Windows Vista and above. store old value 
        if self.isNT6System(type_os):
            reg2 = self.getReg("Microsoft\\Windows\\CurrentVersion\\Policies\\System", '/tmp/regpolsys.reg', 'software')
            lua = reg2.getValue("EnableLUA")
            #print "valeur LUA", lua.split('=')
            regfile.addKey("HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Policies\\System")
            regfile.addValue("EnableLUA_old", lua.split('=')[1])
            regfile.addValue("EnableLUA","dword:00000000")
        
        regfile.close()
        self.UpdateReg('/tmp/regfilesw.reg', 'software')
        
        #Next push cmd to launch in System Setup
        regfile = RegFile('/tmp/regfilesys.reg',overwrite=True)
        regfile.addKey("HKEY_LOCAL_MACHINE\\SYSTEM\\Setup")
        regfile.addValue("SetupType","dword:00000004")
        regfile.addValue("SystemSetupInProgress","dword:00000001")
        regfile.addValue("CmdLine","\"cmd.exe /c C:\\\\joindom\\\\launch.cmd\"")
        regfile.close()
        self.UpdateReg('/tmp/regfilesys.reg', 'system')
    
    def renameComputer(self,newname):
        """Modify Windows Registry for offline computer rename"""
        
        newname = newname.upper()
        print _("Rename host in registry "), newname
        
        regfile = RegFile('/tmp/regfileRename.reg',True)
        regfile.addKey('HKEY_LOCAL_MACHINE\\SYSTEM\\ControlSet001\\Control\\ComputerName\\ComputerName')
        regfile.addValue("Computername", '"%s"'%(newname))
        regfile.addKey('HKEY_LOCAL_MACHINE\\SYSTEM\\ControlSet001\\Control\\ComputerName\\ActiveComputerName')
        regfile.addValue("Computername", '"%s"'%(newname))
        
        regfile.addKey('HKEY_LOCAL_MACHINE\\SYSTEM\\ControlSet001\\Services\\Tcpip\\Parameters')
        regfile.addValue('Hostname','"%s"' % (newname))
        regfile.addValue('NV Hostname','"%s"' % (newname))
        regfile.close()
        self.UpdateReg('/tmp/regfileRename.reg', 'system')
        
        
        
        #modification in tcpip parameters : 
        
    
    def isPowershellVersion(self,type_os):
        powershell_versions = ['windowsvista',"windows7",'windows2008','windows8']

        ps= False
        for p in powershell_versions:
            if p in type_os.lower():
                ps = True
                break
        return ps
    
    def isNTSystem(self,type_os):
            NTsys = ['windowsnt','windows2000','windows2003','windows2003_x64','windowsvista','windowsvista_x64','windows2008','windows2008_x64','windows7','windows7_x64','window8','windows8_x64']
            return type_os.lower() in NTsys
        
    def isNT6System(self,type_os):
            NT6sys = ['windowsvista','windowsvista_x64','windows2008','windows2008_x64','windows7','windows7_x64','window8','windows8_x64']
            return type_os.lower() in NT6sys
        
    
    def RenameJoinScript(self,type_os,dns):
        """Generate Script to rename then join the computer in the domain
        There is netdom version for XP or older, and VBScript version for windows Vista and newer
        """ 
       
        ps = self.isNT6System(type_os)
        tdns = dns.split('.')
        netbios = tdns[0][:15]
            
        joindir = self._joindir
        if not os.path.isdir(joindir):
            os.mkdir(joindir)
        
        if ps:
            '''*********************************************
            this doesn't work
            
            fh = open('/tmp/joincom.ps1', 'w')
            pscmd = '$User = "leschartreux\\jeddlaj"\r\n'
            pscmd += '$PWord = ConvertTo-SecureString -String "jeddlaj" -AsPlainText -Force\r\n'
            pscmd += '$Credential = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $User, $PWord\r\n'
            
            pscmd +="Rename-Computer -DomainCredential $Credential -NewName \"" + netbios +"\" -Force\r\n"
            pscmd +="add-computer -Credential $Credential -DomainName leschartreux.com\r\n"
            #pscmd +="Restart-Computer\r\n"
            fh.write(pscmd)
            ***********************************************
            '''
            
            #Use vbscript to rename and join new computer
            #python template is nice to genrate script
            self.renameComputer(netbios);
            fr = open(settings.BASE_DIR + '/tools/jjoin.vbs','r')
            vbscript = fr.read()
            fr.close()
            #netbios from database needs to be ascii encoded before using substitute
            joinparms = dict(nom_affiliation=settings.AD_DOMAIN,new_netbios=netbios,ADMINUSER=settings.AD_JOINUSER,ADMINPASSWD=settings.AD_JOINPASSWORD,OU='NULL',OPTIONS="JOIN_DOMAIN + ACCT_CREATE + DOMAIN_JOIN_IF_JOINED")
           
            tscript = Template(vbscript)
            joinscript = tscript.safe_substitute(joinparms) 
            #print "le script vbs : " , joinscript
            fh = open('/tmp/jjoin.vbs','w')
            fh.write(joinscript)
            fh.close()
            shutil.copyfile('/tmp/jjoin.vbs',joindir + "/jjoin.vbs")
            self.addRunOnceJoin(type_os,'jjoin.vbs')
        
        else:
            self.renameComputer(netbios)
            fh = open('/tmp/joincom.bat', 'w')
            join_bat="@echo off\r\n"
            join_bat+="net start dhcp\r\n"
            join_bat+="ipconfig /renew\r\n"
            join_bat+="net start workstation\r\n"
            join_bat+="net start server\r\n"
            join_bat+="net start lmhosts\r\n"
            join_bat+="nbtstat -R\r\n"
            join_bat+="cd C:\\joindom\r\n"
            join_bat+="echo Modification Registre\r\n"
            join_bat+="reg add \"HKLM\\System\\Setup\" /v SystemSetupInProgress /t REG_DWORD /d 00000000 /f\r\n"
            fh.write(join_bat)
#            join_bat="netdom renamecomputer %%COMPUTERNAME%% /newName=%s\r\n" % (netbios)
            join_bat += "netdom.exe join %s /domain:%s /UserD:%s\\%s /PasswordD:%s\r\n" % (netbios,settings.AD_DOMAIN,settings.AD_DOMAIN,settings.AD_JOINUSER,settings.AD_JOINPASSWORD)           
            fh.write(join_bat)
            fh.close()
            shutil.copyfile("tools/netdomxp.exe",joindir + "/netdom.exe")
            shutil.copyfile('/tmp/joincom.bat',joindir + "/joincom.bat")
            self.addRunOnceJoin(type_os,'joincom.bat')
        

    def close(self):
        """unmount the ntfspartition"""
        call (['/bin/umount', settings.NTFS_MOUNT])
            
        
        
    

class RegFile(object):
    '''Represents Windows Reg File extracted or created to import'''
    '''writing methods are minimalistic just put keys between square brackets and name/values as "name"=value''' 
    
    def __init__(self,filename,overwrite=False):
        '''Create a new .reg File'''
        self._fh=None
        try:
            if os.path.isfile(filename) and not overwrite:
                fh= open(filename,'r+')
            else:
                fh = open(filename, 'w+')
                fh.write("Windows Registry Editor Version 5.00\r\n") #Header of reg file
            self._fh = fh
        except:
            print _('Error!')
            

    def addKey(self,key):
        self._fh.write("\r\n[" + key +"]\r\n") #empty line before each new key
    
    def addValue(self,name,value):
        self.toEnd()
        self._fh.write('"%s"=%s\r\n' % (name,value))
    
    def getValue(self,name):
        #read lines for corresponding value name
        self._fh.seek(0)
        lines = self._fh.read().split("\r\n") #put in memory
        for line in lines:
            if name in line: #found a matching line
                lval = line.split('=')[0]
                #print "lval trouve", lval, " ", name
                if '"' + name + '"' == lval : # fond matching value
                    return line
        return None
        
    
    def close(self):
        self._fh.close()
    
    def toEnd(self):
        """just read file till EOF"""
        self._fh.read()
    
    def __str__(self):
        self._fh.seek(0)
        return self._fh.read()
    