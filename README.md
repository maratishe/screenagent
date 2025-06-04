# screenagent

An agent that watches the screen, analyzes screenshots and uses desktop automation to move data around. 

## running environment setup (windows but mostly the same on macox)

you need:
 - windows and some shell, I recommend [cygwin](https://cygwin.com/)
 - `php` and `node`, easy install instructions for both are below
 - `AutoHotkey`
 - `nircmd`  -- search online, download -- it is a simple .exe file.  We use it for screenshots.
 - **OpenAI API key**, a nodejs script uses it to analyze screenshots
 - Chrome as browser and some chat app for posting the results
   - the structure is so that you need to specify two windows, one to use for screenshots and another to post analysis results into
   - both can be browsers if you can distinguish between the two using **windows titles**
   - my agents posts to my Telegram's **Saved Messages** which shows up as a windows with the title `Saved Messages`
   - **why window titles?** because we use AutoHotkey to automate the desktop, and it works with **window titles**, not with process names or anything else. Use alt-tab or just hover over an app window in windows to check its title. **Note:** partial matches in titles will work, I used "saved" and it works for me. For Chrome I use "chrome". 


### 1. OS, shell, and cli tools

Any Windows.  As shell I use [cygwin](https://cygwin.com/) -- see below how to set populate it with all the packages.  `cmd` or even `PowerShell` can be used as well. There is not much linux-like operation, you only need to run one script which itself will neet to run another script for image analysis using OpenAI. 

For **cygwin** 
 - download and install it from [cygwin.com](https://cygwin.com/), make sure you keep its `setup-x86_64.exe` somewhere where you can easily find it within cygwin shell (I just use Downloads)
 - install. use a good path like `C:\cygwin64`.  I use `C:\APPS\cygwin` as most of my apps are in `C:\APPS`
 - when installed, **run once** -- it needs to initialize some files, like `.bashrc` and `.bash_profile`.  Close. 
 - **run as administrator** -- we need this to automatically install lots of packages
 - use `cygwin-add-packages.sh` you find in this repo to install. Put the script next to the `setup-x86_64.exe` file you downloaded -- in the script it assumes that it is in the same directory. Then just `sh cygwin-add-packages.sh` and it will install all the packages you need. You can see that it installs lots of tools. Sadly, not `nodejs` but `php` is installed out of the box.  
   - in cygwin, **Downloads** is in `/cygdrive/c/Users/<your-user-name>/Downloads` or similar, so you can use `cd /cygdrive/c/Users/<your-user-name>/Downloads` to get there.

If **not cygwin**
 - use `cmd` directly
 - you will need to install `php` as a windows executable. It should be added to your `PATH` so you can run it from any `cmd` window.
 - Same for `nodejs` -- search online and install the lastest `Node JS` version. 

For `Node JS` specifically:
 - I include `nodejs.zip` with repo.
 - unpack it into `C:\APPS\nodejs` or similar
 - include it into `PATH`  -- if in cygwin, it is the same as in linux (see below)

#### cygwin packages -- same as in `cygwin-add-packages.sh`

```
# install packages on command line   --upgrade-also (upgrade)
./setup-x86_64.exe -q -P unzip,procps,cygrunsrv,nfs-server,unfs3  --upgrade-also
./setup-x86_64.exe -q -P cygwin32-binutils,cygwin32-gcc-g++,cygwin32-w32api-headers,cygwin32-w32api-runtime,cygwin32-gcc-objc,cygwin32-zlib,gcc-core,libgcc1
./setup-x86_64.exe -q -P wget,gcc-g++,make,diffutils,libmpfr-devel,libgmp-devel,libmpc-devel
./setup-x86_64.exe -q -P php,php-cli,php-curl,php-Archive_Tar,php-bz2,php-jsonc,php-mbstring,php-mcrypt,php-pdo_sqlite,php-shmop,php-sockets,php-sqlite3,php-iconv,php-intl,php-ctype
./setup-x86_64.exe -q -P libopenssl100,librsync2,rsh,rsync,ssh,autossh,libssh-common,openssh,openssl
./setup-x86_64.exe -q -P ImageMagick,graphviz,xpdf,xdelta,xdelta3,git,dos2unix,curl
./setup-x86_64.exe -q -P zlib,zlib-devel,jpeg,libjpeg-devel 
./setup-x86_64.exe -q -P python3,python3-pip,python3-devel,python3-openssl,openssl-devel --upgrade-also
./setup-x86_64.exe -q -P liblapack,liblapack-devel
./setup-x86_64.exe -q -P poppler*
./setup-x86_64.exe -q -P sox,lynx
./setup-x86_64.exe -q -P ruby,ruby-json,ruby-devel
./setup-x86_64.exe -q -P openssl openssl-devel libopenssl100 mingw64-i686-openssl mingw64-x86_64-openssl 
./setup-x86_64.exe -q -P lame lame-mp3x libmp3lame-devel libmp3lame0 mingw64-i686-lame
./setup-x86_64.exe -q -P tree
./setup-x86_64.exe -q -P mysql mysql-common
```

#### PATH registration assuming you unpack nodejs into `C:\APPS\nodejs`

```
PATH=/cygdrive/c/APPS/nodejs:$PATH
```

In PHP's case, it comes as part of the cygwin packages above and is already in the PATH.


#### on PHP specifically

You need to have `php` installed and available in your `PATH`.  If under cygwin, it will work flawlessly. If you install it natively on windows, I am not sure (have not actually tried it). But it should work. Note that I have the wierd `requireme.php` file in repo. And the `agent.php` is a bit complicated.  This is my 3-way script implementation where the same script can be used (1) as CLI, (2) as web API, and (3) imported and called by another script. This is an old idea that I keep using for 10+ years now.  `requireme.php` is a simple way for me to share all my self-developed PHP libraries.  I have lots of them. `requireme.php` is a packaged form which can be included in standalone repos, just like this one.  `agent.php` checks and if it finds `requireme.php` in the same directory, it includes it. If not, it will assume that it is running on my own machine and will try to use things in other folders.  Having `requireme.php` in the same directory as `agent.php` you do not have to worry about it. 

**The short version:** if on `cygwin`, with added packages (above) and using the source from current repo, it will run smoothly. 


#### On AutoHotkey

Install into a good place like `C:\APPS\AutoHotkey`.  Again, register in PATH

```
PATH=/cygdrive/c/APPS/AutoHotkey:$PATH
```

#### On nircmd

Download it from [nircmd](https://www.nirsoft.net/utils/nircmd.html) and unpack into `C:\APPS\nircmd` or similar.  Then register in PATH:

```
PATH=/cygdrive/c/APPS/nircmd:$PATH
```



#### Check if environment is ready

```
$ php -v
PHP 7.3.7 (cli) (built: Jul 21 2020 18:15:38) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.3.7, Copyright (c) 1998-2018 Zend Technologies

$ node -v
v20.18.0

AutoHotkey
.... a popup window will appear, just close it, ... it means it is working

nircmd -v 
... another popup window with version info, again, just close it...

```

#### install node packages

We need some packages to run OpenAI requests. Inside repo, run:
```
npm install fs openai
```

If it is successfull, it will output something like this:
```
added 1 package, and audited 2 packages in 1s
found 0 vulnerabilities
```


**And you are all set!**


## 2. a brief overview of operation

Below is how to run with current setup. If you want to change the target, read further alone on customization.

prepare:
 - **One major thing to configure before running:** open `analyze_image.js` and set your OpenAI API key at the head of the script.
 - open Chrome and point 2 first tabs to:
   - AirBnB page in map search mode, forcus on whatever area you want (see `$tabs` in `agent.php` for the current setup -- this is where you can customize as well) 
   - a Google Maps page in hotel search mode.
   - ... why the two tabs? The current `$tabs` in `agent.php` has 2 items which means that script will try to refresh and read 2 tabs.  You can customize it as you wish. Not limited to hotel search -- I do all kinds of stuff with it. 

This is happening:
 - you open **Chrome** and your chat app (Telegram, WhatsApp, etc) with the title `Saved Messages` or similar
   - make sure `chrome` is in the title
   - make sure `saved` is in the titlte of your chat app
   - use alt-tab to switch between the two windows, you should see the titles
 - run `php agent.php run` . this will run in a 1-minute cycles doing this:
   - focus on Chrome
   - depending on setup (current setup has 2 tabs), will peek each in turn
     - refresh the tab (I am monitoring AirBnB and Google Maps with hotel search)
     - take a screenshot using `nircmd` and save it to a file
     - will run `node analyze_image.js` which will use OpenAI to analyze the screenshot -- this results in some textual output
     - if running continusly, it checks for changes and will only post to your chat app if there is a change in the analysis results
     - if there are results, focus on "saved" app
     - post the results to the chat app -- this posting assumes you are in Telegram's "Saved Messages" or similar app and your cursor is on the input field (lots of assumptions, I know)
   - ...
   - ... sleep for 1 minute
   - ... repeat from the beginning
 - ...


## 3. running the agent

just run and see what is happening
```
php agent.php run
```

Wait for 20-30 seconds. It should focus on one app and then the other. If that does not happen, then your `AutoHotkey` is not installed or not working properly.  If you see errors in command line there are problems with `php, node, nircmd` and you will need to debug.  If there are no problems, the cli will have very little output. 

## 4. customization

This is what you can customize:
 - the logic assumes that you have 2 apps, the number 2 cannot change but titles can:
   - open `agent.php` and change **chrome** and **saved** some whatever two apps you want to analyze and post into
   - specifics with **chrome**: it uses `ctrl-{number}` to switch between tabs -- see `STAB.chrome-refresh.ahk`. if your app does not get that shortcut you will need to change the STAB. Note that STABS are not used directly (hence the name `STAB`) but are templates for AutoHotkey scripts created on the fly
   - specifics with **saved**: I post to Telegram.  If you post somewhere else, make sure the `STAB.telegram-paste.ahk` and `STAB.telegram-send.ahk` will work.  With minor adjustments they will do your custom posting easily. 
 - whatever the number of items you have on your **chrome** side, its configuration is in `agent.php` in `$tabs` variable.  The variable itself it used to adjust the `prompt` text depending on what OpenAI is seeing in the screenshot.  If your tabs are different from mine, you will have to adjust the prompts as well. My prompts work well for AirBnB and Google Maps -- tested and verified (in fact, used in practice for the aformentioned purposes). 


With this customization, you will have no trouble running your agent for any purpose you want.

Happy automating!

## 5. Demo

I will use this code in a seminar.  After, I will share the video of the running setup here.  So, expect an update.










