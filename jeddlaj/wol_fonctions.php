<?php

function nor($a,$b) {
	return ((~(int)$b+256)|(int)$a)%256;
}

function wake_on_lan($ip,$netmask,$mac) {
	$macdigits=split(":",$mac);
	$macdec="";
	for ($i=0;$i<6;$i++) $macdec.= chr(hexdec($macdigits[$i]));
	$magic=str_repeat(chr(255),6);
	for ($i=0;$i<16;$i++) $magic.=$macdec;
	$ipdigits=split("\.",$ip);
	$maskdigits=split("\.",$netmask);
	$broadcast=sprintf("%d.%d.%d.%d",nor($ipdigits[0],$maskdigits[0]),nor($ipdigits[1],$maskdigits[1]),nor($ipdigits[2],$maskdigits[2]),nor($ipdigits[3],$maskdigits[3]));
	$sock=socket_create(AF_INET,SOCK_DGRAM,getprotobyname('udp')); 
	$sock_data=socket_set_option($sock,SOL_SOCKET,SO_BROADCAST,1);
	$sock_data=socket_sendto($sock,$magic,strlen($magic),0,$broadcast,9);
	socket_close($sock);
}

?>
