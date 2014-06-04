Const JOIN_WORKGROUP          = 0
Const JOIN_DOMAIN             = 1
Const ACCT_CREATE             = 2
Const ACCT_DELETE             = 4
Const WIN9X_UPGRADE           = 16
Const DOMAIN_JOIN_IF_JOINED   = 32
Const JOIN_UNSECURE           = 64
Const MACHINE_PASSWORD_PASSED = 128
Const DEFERRED_SPN_SET        = 256
Const INSTALL_INVOCATION      = 262144
Const PAUSE                   = 30
Const NETSETUP_JOIN_WITH_NEW_NAME = 1024

function launchJoin
	wscript.echo "Recuperation objet WMI"
	Set objWMISvc = GetObject( "winmgmts:\\.\root\cimv2" )
	Set colItems = objWMISvc.ExecQuery( "Select * from Win32_ComputerSystem", , 48 )
	wscript.echo "Recuperation objet ordi"
	For Each objComputer in colItems
		wscript.echo "OK"
    	strComputerName = objComputer.Name
    	if UCase(strComputerName) <> UCase($new_netbios) then
    		wscript.echo "renommage de " & strComputername & " en $new_netbios"
    		objComputer.Rename("$new_netbios")
    		opt = $OPTIONS + NETSETUP_JOIN_WITH_NEW_NAME
    	else
    		wscript.echo "Les noms sont identiques. Tentative de jointure"
    		opt = $OPTIONS
    	end if
    	wscript.echo "jointure..."
    	ret = objComputer.JoinDomainOrWorkGroup( "$nom_affiliation", "$ADMINPASSWD", "$nom_affiliation\$ADMINUSER", $OU , opt)
    	Wscript.echo "Code retour : " & ret
    	launchjoin=ret
	Next
	
end function

function restoreLUA
    Dim WSHShell, value

    On Error Resume Next
    Set WSHShell = CreateObject("WScript.Shell")
    value = WSHShell.RegRead( "HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System\EnableLUA_old" )

    if err.number <> 0 then
        WSHShell.regWrite( "HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System\EnableLUA",value,"REG_DWORD")
        restoreLUA = err.number
    else
        restoreLUA = err.number
    end if

    set WSHShell = nothing
end function

Wscript.echo "Entree dans le {$affiliation_windows$} {$nom_affiliation$}..."

i=1
Do While i<6 And launchJoin>0
	Wscript.echo "Tentative " & i & " a echoue. Nouvel essai dans " & PAUSE & " secondes..." 
	WScript.Sleep PAUSE*1000
	i=i+1
LOOP
 	
If i < 6 Then
	Wscript.echo "Entree reussie."
	ret = restoreLUA
	Wscript.echo "Recuperation LUA : " & ret
	
End if


