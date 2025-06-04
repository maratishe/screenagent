;NoEnv
;SendMode Input
;SetWorkingDir %A_ScriptDir%
SetTitleMatchMode, 2

; Activate the Telegram window
WinActivate, WINDOWNAME

; Wait a bit for the window to activate
Sleep, 800

; Send Alt+Enter to send the message
Send, !{Enter}

; Wait for message to be sent
Sleep, 1500