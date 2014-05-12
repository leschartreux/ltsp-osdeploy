#!/bin/bash

DELAY="2s"

while true ;do
	grep "$2" $3 &&  killall $1 
	sleep $DELAY 
done
