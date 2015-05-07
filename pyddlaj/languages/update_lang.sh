#!/bin/bash
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
 
for l in fr en
do
	echo '' > messages.po # xgettext needs that file, and we need it empty
	find .. -type f -iname "pyddlajd.py" | xgettext -j -f -
	msgmerge -N $l/LC_MESSAGES/pyddlajd.po messages.po > new.po
	mv new.po $l/LC_MESSAGES/pyddlajd.po

	echo '' > messages.po
	find .. -type f -iname "pyddlaj_client.py" -o -iname db.py -o -iname host.py -o -iname nettask.py -o -iname winregistry.py | xgettext -j -f -
	msgmerge -N $l/LC_MESSAGES/pyddlaj_client.po messages.po > new.po
	mv new.po $l/LC_MESSAGES/pyddlaj_client.po

	echo '' > messages.po
	find .. -type f -iname "changestate.py" | xgettext -j -f -
	msgmerge -N $l/LC_MESSAGES/changestate.po messages.po > new.po
	mv new.po $l/LC_MESSAGES/changestate.po
done
rm messages.po
