;NoEnv
;SendMode Input
;SetWorkingDir %A_ScriptDir%
SetTitleMatchMode, 2

; Activate the Chrome window
WinActivate, Saved

; Wait a bit for the window to activate
Sleep, 800

; Send Ctrl+R to refresh
;Send, ^r

; Wait for page to begin loading
Sleep, 3000