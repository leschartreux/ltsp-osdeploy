<?php
# ################################ GPL STUFF ################################
#
# ********************************* ENGLISH *********************************
# 
# --- Copyright notice :
# 
# Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
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
# *********** TRADUCTION FRANÇAISE PERSONNELLE SANS VALEUR LÉGALE ***********
#
# --- Notice de Copyright :
# 
# Copyright 2003, 2004, 2005 Gérard Milhaud - Frédéric Bloise
# 
# 
# --- Déclaration de permission de copie
# 
# Ce fichier fait partie de JeDDLaJ.
# 
# JeDDLaJ est un logiciel libre : vous pouvez le redistribuer ou le modifier
# selon les termes de la Licence Publique Générale GNU telle qu'elle est
# publiée par la Free Software Foundation ; soit la version 2 de la Licence,
# soit (à votre choix) une quelconque version ultérieure.
# 
# JeDDLaJ est distribué dans l'espoir qu'il soit utile, mais SANS AUCUNE
# GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou 
# d'ADAPTATION DANS UN BUT PARTICULIER. Voir la Licence publique Générale GNU
# pour plus de détails.
# 
# Vous devriez avoir reçu une copie de la Licence Publique Générale GNU avec 
# JeDDLaJ ; si ça n'était pas le cas, écrivez à la Free Software Foundation,
# Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# ############################ END OF GPL STUFF #############################


include("../UtilsHTML.php");

# Main()
entete("Gérard Milhaud & Frédéric Bloise : La.Firme@esil.univ-mrs.fr", "../CSS/g.css", "JeDDLaJ : HWC accueil");

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

print("<H3>Attention ces menus sont à utiliser avec beaucoup de précautions</H3>\n");

# On attaque la tables Ordinateurs pour les infos générales

print("<H2><A HREF=\"reinstallation_logiciel.html\">Réinstallation/Désinstallation d'un logiciel</A></H2>\n");
print("<H2><A HREF=\"desinstallation_distribution.html\">Désinstallation d'une distribution</A></H2>\n");
print("<H2>Modifier la visibilité <A HREF=\"visibilite_logiciels.php\">des logiciels</A>/<A HREF=\"visibilite_distributions.php\">des distributions</A></H2>\n");
print("<H2><A HREF=\"priorite_logiciels.php\">Modifier la priorité d'un logiciel</A></H2>\n");
print("<H2><A HREF=\"depannage_mode_admin.php\">Mise en dépannage mode administrateur</A>/<A HREF=\"sortie_en_cours.php\">Sortie de l'état en cours vers état dépannage</A></H2>\n");
print("<H2><A HREF=\"ajout_composants.html\">Ajout de composants/<A HREF=\"update_composants.php\">Mise à jour de la base des composants</A>/<A HREF=\"modification_composant.php\">Modifier un composant dans la base</A></H2>\n");

PiedPage();
//FIN Main()

?>
