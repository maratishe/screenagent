SetTitleMatchMode, 2
IfWinExist, WINDOWNAME
{
	WinActivate
	#Send !{F4}
    Sleep 800
}
return