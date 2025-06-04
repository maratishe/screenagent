;NoEnv
;SendMode Input
;SetWorkingDir %A_ScriptDir%
SetTitleMatchMode, 2

; Activate the Chrome window
WinActivate, WINDOWNAME

; Wait a bit for the window to activate
Sleep, 800

; Switch to specific tab using Ctrl+TABNO
Send, ^TABNO

; Wait a moment for tab to activate
Sleep, 800

; Send Ctrl+R to refresh
Send, ^r

; Wait for page to begin loading
Sleep, 10000