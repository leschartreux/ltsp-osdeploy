07







0c                O0butil 0cS0bysteme 0cC0bomplet d'0cA0bssistance 0cR0beseau (c)07

09                      Liste des options de commande 0foscar07

0C*07 You can add the following kernel parameters after the image name :
0Fdocache07    Cache the runtime in RAM. This require a computer with 0C256MB07 of RAM,
   but allows you to unmount and eject the CD in order to mount a new one.
0Fsetkmap=xx07 Configure the keyboard with the 0Cxx07 keymap (xx='fr','de',...)
0Fnokeymap07   Disables the keymap selection used to select non-US keyboard layouts

0C*07 In case of boot trouble, you may try one or more of these options :
0Fscandelay07 Pause at startup to allow for devices that are slow to initialize
0Fnodetect07  Prevents generic auto-detection at boot time.
0Fnoxdetect07 Prevents automatic X configuration: use in case of freeze at X detect
0Fnoapic07    This option disables 0CAPIC07 support which may cause booting problems.
0Fide=nodma07 Forcefully disable 0CDMA07 for malfunctioning IDE devices.
0Fdoevms207   Enables support for IBM's 0CEVMS07 (Enterprise Volume Management System)
0Fdolvm207    Enables support for 0CLogical Volume Management07. Not safe with evms2.
0Fdoload=X07  This causes the initial ramdisk to 0Cload any module07 listed.
0Fnoload=X07  This causes the initial ramdisk to 0Cskip any module07 listed.
0Fdopcmcia07  Loads support for 0CPCMCIA07 and 0CCardbus07 hardware
0Fdoscsi07    Loads support for most 0CSCSI07 controllers and most 0CUSB07 devices.
0Fnogpm07     Disables the loading of the 0CGPM07 (Mouse in the console)
0Fedd=off07   Ne valide pas le test du disque dur au demarrage
0C07