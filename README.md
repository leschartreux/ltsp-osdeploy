ltsp-osdeploy
=============

Mix from various open source projects to have auto installation of oses via PXE.
This is first development release with minimal features.

###Main features :
- Mysql database for central management
- Web interface compatible with https://sourcesup.renater.fr/projects/jeddlaj/ (in french)
- Full disk backup and clone of Windows XP and above hosts
  

###OS Backup features
- full Disk image backup of host
- Small interactive text interface for first image creation task of host
- NTFS support
- Fast Compression. With 2 cores CPU and gigabit network card, disk partitions dumping rate is over 2GB/minute.
- Central storage on NFS server

###OS Clone features
- Full disk clone on same disk size or greater
- Totally unattended cloning process from Network Boot. No menu client client size.
- Fast image deploy with multicast. Tested 81Mbs on 12 100mbs network PC.
- Auto rename of computer and windows domain joining
 
 ###Technical features
- Client uses PXE PC's Network boot feature to the work (PXE)
- Boot image is a Linux 32 bits dedicated Debian 7 distribution, based on excellent LTSP project. http://www.ltsp.org
- All work is done with python scripts connected to central database
- Local boot on hard drive or LTSP boot is dynamically managed with web GUI and scripts
- Main difference with others cloning projects is that Windows Host customization is done with a registry edition tool from chntpasswd http://http://pogostick.net/~pnh/ntpasswd/. It offers
offline Registry modifications with .Reg files import/export. Windows batch scripts are dynamically generated after clone and before reboot.
No need to install complex scripts or agents on windows hosts before generating base image. 
- Dump of partitions is done with partclone http://partclone.org
- fast compression is done with pigz. Which is a multithread version of gzip (Now, most of PC's CPUs have multithread, multicore support)
- Multicast transfert uses powerfull udpcast project http://www.udpcast.linux.lu/
- A small python daemon is used to synchronize multicast image transfert. Only one multicast transfert at a time is implemented for now.
- The MySQL database and Web inerface intend to be compatible with the base project from Jeddlaj https://sourcesup.renater.fr/projects/jeddlaj/


Python scripts have international support with traditional gettext library
Web interface and database are still in french.