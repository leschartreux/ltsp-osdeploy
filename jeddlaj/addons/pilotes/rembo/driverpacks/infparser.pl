#!/usr/bin/perl

my @wnt5_x86= qw /Windows2000 WindowsXP Windows2003/;
my @wnt6_x86= qw /WindowsVista Windows7/;
my @wnt6_x64= qw /WindowsVista_x64 Windows7_x64/;

$filename=$ARGV[0];
$arch=$ARGV[1];
if ($arch eq "wnt5_x86") { @wnt=@wnt5_x86; } 
elsif ($arch eq "wnt6_x86") { @wnt=@wnt6_x86; } 
elsif ($arch eq "wnt6_x64") { @wnt=@wnt6_x64; } 

open(FILE,"<$filename") || die "Erreur de lecture $filename, Erreur: $!";
$etat=0;
$manufacturer="";
while (<FILE>) {
	chomp($_);
	$_=uc $_;
	if ($etat==3) {
		if (/^\s*\[/) { $etat=2; }	
		elsif (!(/\s*;/) && (/(PCI|HDAUDIO.FUNC_01).VEN_....&DEV_..../)) {
			$device=$_;
			$device=~s/.*(PCI|HDAUDIO.FUNC_01).VEN_(....)&DEV_(....).*/\2:\3/;
			if (/SUBSYS/) {
				$subsys=$_;
				$subsys=~s/.*SUBSYS_([A-Z0-9]{4})([A-Z0-9]{4}).*/\2:\1/;
			}
			else { $subsys="0000:0000"; }
			foreach $os (@wnt) {
				$filename=~s/^(.*)\/(.*).[iI][nN][fF]/\1,\2/;
				($inf_path,$inf_file)=split(/,/,$filename);
				print "INSERT IGNORE INTO pilotes(id_composant,subsys,inf_path,inf_file,nom_os,source) VALUES(\"$device\",\"$subsys\",\"$inf_path\",\"$inf_file\",\"$os\",\"$ARGV[2]\");\n";
			}
		}
	}
	if (($etat==0) && (/^\s*\[MANUFACTURER\]/)) {
			$etat=1;
	}
	if ($etat==1) {
		if (/^\s*%/) {
			($lvalue,$rvalue)=split(/=/,$_);
			($first,$rest)=split(/,/,$rvalue);
			$first=~s/\s+//g;
			if ($manufacturer ne "") { $manufacturer.="|"; }
			$manufacturer.=$first;
		} elsif ((/^\s*\[/) && !(/^\s*\[MANUFACTURER\]/)) {
				$etat=2;
			}
	}
	if ($etat==2) {
		if (/^\s*\[($manufacturer).*\]/) {
			$etat=3;
			if (/^\s*\[($manufacturer)\./) {
				$suf=$_;
				$suf=~s/^\s*\[($manufacturer)\.([^\]]*)\].*/\2/;
			}
			else { $suf=""; }
		}
	}
}
close(FILE);
