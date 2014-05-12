<?php
# ################################ GPL STUFF ################################
#
# ********************************* ENGLISH *********************************
# 
# --- Copyright notice :
# 
# Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
# 
# 
# --- Statement of copying permission
# 
# This file is part of JeDDLaJ.
# 
# JeDDLaJ is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# JeDDLaJ is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with JeDDLaJ; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# 
# *********** TRADUCTION FRAN�AISE PERSONNELLE SANS VALEUR L�GALE ***********
#
# --- Notice de Copyright :
# 
# Copyright 2003, 2004, 2005 G�rard Milhaud - Fr�d�ric Bloise
# 
# 
# --- D�claration de permission de copie
# 
# Ce fichier fait partie de JeDDLaJ.
# 
# JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
# selon les termes de la Licence Publique G�n�rale GNU telle qu'elle est
# publi�e par la Free Software Foundation ; soit la version 2 de la Licence,
# soit (� votre choix) une quelconque version ult�rieure.
# 
# JeDDLaJ est distribu� dans l'espoir qu'il soit utile, mais SANS AUCUNE
# GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou 
# d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique G�n�rale GNU
# pour plus de d�tails.
# 
# Vous devriez avoir re�u une copie de la Licence Publique G�n�rale GNU avec 
# JeDDLaJ ; si �a n'�tait pas le cas, �crivez � la Free Software Foundation,
# Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# ############################ END OF GPL STUFF #############################


include("../UtilsHTML.php");

# Main()
entete("G�rard Milhaud & Fr�d�ric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : HWC accueil");

print("<CENTER>");
print("<TABLE BORDER=0 WIDTH=100%>");
print("<TR ALIGN=CENTER>");
print("<TD ALIGN=LEFT><IMG ALIGN=CENTER BORDER=0 SRC=\"../LOGOS/hwc.jpg\" ALT=\"hwc.jpg\"></TD>");
print("<TD ALIGN=CENTER><A HREF=\"../index.php\"><IMG ALIGN=CENTER SRC=\"../LOGOS/JeDDLaJ_current.png\" BORDER=0 ALT=\"JeDDLaJ's Logo\"></A></TD>");
print("<TD ALIGN=RIGHT><IMG ALIGN=CENTER BORDER=0 SRC=\"../LOGOS/hwc.jpg\" ALT=\"hwc.jpg\"></TD>");
print("</TR>");
print("</TABLE>");
print("</CENTER>");
print("<CENTER><H1>HWC Accueil</H1></CENTER>\n");

#info

print("<H3>Attention ces menus sont � utiliser avec beaucoup de pr�cautions</H3>\n");

# On attaque la tables Ordinateurs pour les infos g�n�rales

print("<H2><A HREF=\"reinstallation_logiciel.html\">R�installation/D�sinstallation d'un logiciel</A></H2>\n");
print("<H2><A HREF=\"desinstallation_distribution.html\">D�sinstallation d'une distribution</A></H2>\n");
print("<H2>Modifier la visibilit� <A HREF=\"visibilite_logiciels.php\">des logiciels</A>/<A HREF=\"visibilite_distributions.php\">des distributions</A></H2>\n");
print("<H2><A HREF=\"priorite_logiciels.php\">Modifier la priorit� d'un logiciel</A></H2>\n");
print("<H2><A HREF=\"depannage_mode_admin.php\">Mise en d�pannage mode administrateur</A>/<A HREF=\"sortie_en_cours.php\">Sortie de l'�tat en cours vers �tat d�pannage</A></H2>\n");
print("<H2><A HREF=\"ajout_composants.html\">Ajout de composants/<A HREF=\"update_composants.php\">Mise � jour de la base des composants</A>/<A HREF=\"modification_composant.php\">Modifier un composant dans la base</A></H2>\n");

PiedPage();
//FIN Main()

?>
