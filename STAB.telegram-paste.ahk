;NoEnv
;SendMode Input
;SetWorkingDir %A_ScriptDir%
SetTitleMatchMode, 2

; Activate the Telegram window
WinActivate, WINDOWNAME

; Wait a bit for the window to activate
Sleep, 800

; Send Ctrl+V to paste clipboard content
Send, ^v

; Wait for paste to complete
Sleep, 3000