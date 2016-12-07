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

# Common functions in use with pyddlaj



import readline
from __builtin__ import False
from flufl.i18n import initialize
 
_= initialize('pyddlaj_client')
 
 
def rlinput(prompt, prefill=''):
    readline.set_startup_hook(lambda: readline.insert_text(prefill))
    try:
        return raw_input(prompt)
    finally:
        readline.set_startup_hook()
        

def askYesNo(prompt):
    answer = False
    
    while 1:
        print _(prompt)
        val = raw_input("Choice (Y/N) : ")
        if val not in ['Y','N']:
            print _("Bad Value")
        else:
            break
    if val == "Y":
        answer = True
    
    return answer