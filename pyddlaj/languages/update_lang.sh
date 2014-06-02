#!/bin/bash

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
