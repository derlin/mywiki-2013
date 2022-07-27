---
title: "Thinkpad w530, linux 13.10 and optimus"
date: "2014-02-03"
categories: 
  - "configuration-tricks"
  - "material"
tags: 
  - "linux"
  - "optimus"
  - "ubuntu"
---

## Prerequisites

- be sure that your BIOS is configured for NVIDIA optimus (BIOS > config > display)
- a working version of ubuntu 13.10
- the following programs installed: git, make, autotools
- you know what you do with a terminal...

## Latest nvidia drivers

Right know, the latest one is nvidia-311, but to be sure use `sudo apt-cache search nvidia-3` and use the highest number.
```bash
sudo add-apt-repository ppa:xorg-edgers/ppa 
sudo apt-get update 
sudo apt-get install nvidia-331
```
## Bumblebee

For those who don't know, "_Bumblebee daemon is a rewrite of the original Bumblebee service, providing an elegant and stable means of managing Optimus hybrid graphics chipsets._ " ([project main page](https://github.com/Bumblebee-Project/Bumblebee)).

### Installation

Note: for bumblebee to work, you need either **virtualGL** or **primus** installed. The former is best suited for ubuntu 13.04, the latter is the one I use on ubuntu 13.10. It is normally installed out of the box, but if you don't have it (`dpkg -i | grep primus`), install it as well.

sudo add-apt-repository ppa:bumblebee/stable
sudo apt-get install bumblebee bumblebee-nvidia bbswitch-dkms

### Configuration

Edit the file `/etc/bumblebee/bumblebee.conf` and change the following:
```bash
KeepUnusedXServer=true # default to false
Driver=nvidia # default to ""
...
[driver-nvidia]
KernelDriver=nvidia-331 # must match the one installed in step 1
PMMethod=none  # default to auto 
...
```

Now, it is always recommended to reboot...

### Test

Try to run bumblebeed with the command `sudo bumblebeed -vv`. The output should look like this:
```
[ 2313.699346] [DEBUG]Found card: 01:00.0 (discrete)
[ 2313.699397] [DEBUG]Found card: 00:02.0 (integrated)
[ 2313.699418] [DEBUG]Reading file: /etc/bumblebee/bumblebee.conf
[ 2313.699858] [INFO]Configured driver: nvidia
[ 2313.699893] [DEBUG]Skipping auto-detection, using configured driver 'nvidia'
[ 2313.700094] [DEBUG]Process /sbin/modprobe started, PID 3537.
[ 2313.700149] [DEBUG]Hiding stderr for execution of /sbin/modprobe
[ 2313.702308] [DEBUG]SIGCHILD received, but wait failed with No child processes
[ 2313.702473] [INFO]PM is disabled, not performing detection.
[ 2313.702528] [DEBUG]Active configuration:
[ 2313.702559] [DEBUG] bumblebeed config file: /etc/bumblebee/bumblebee.conf
[ 2313.702597] [DEBUG] X display: :8
[ 2313.702628] [DEBUG] LD_LIBRARY_PATH: /usr/lib/nvidia-current:/usr/lib32/nvidia-current
[ 2313.702666] [DEBUG] Socket path: /var/run/bumblebee.socket
[ 2313.702697] [DEBUG] pidfile: /var/run/bumblebeed.pid
[ 2313.702729] [DEBUG] xorg.conf file: /etc/bumblebee/xorg.conf.nvidia
[ 2313.702762] [DEBUG] xorg.conf.d dir: /etc/bumblebee/xorg.conf.d
[ 2313.702800] [DEBUG] ModulePath: /usr/lib/nvidia-current/xorg,/usr/lib/xorg/modules
[ 2313.702833] [DEBUG] GID name: bumblebee
[ 2313.702872] [DEBUG] Power method: none
[ 2313.702902] [DEBUG] Stop X on exit: 0
[ 2313.702936] [DEBUG] Driver: nvidia
[ 2313.702970] [DEBUG] Driver module: nvidia-331
[ 2313.703008] [DEBUG] Card shutdown state: 1
[ 2313.703186] [DEBUG]Process /sbin/modprobe started, PID 3538.
[ 2313.703263] [DEBUG]Hiding stderr for execution of /sbin/modprobe
[ 2313.705422] [DEBUG]SIGCHILD received, but wait failed with No child processes
[ 2313.705491] [DEBUG]Configuration test passed.
[ 2313.706229] [INFO]bumblebeed 3.2.1 started
[ 2313.706383] [INFO]Initialization completed - now handling client requests
```
## Intel-virtual-output

In ubuntu 13.04, I needed to patch the nvidia driver manually and then used **screenclone** to create virtual displays. With ubuntu 13.10, the xf86-video-intel driver already has this capability. The xf86-video-intel driver also contains a nice tool, **intel-virtual-output**, which is used instead of screenclone. Sadly, ubuntu does not bundle it in its package; we have to install it manually.
```bash
sudo apt-get install xorg-dev git
git clone git://anongit.freedesktop.org/xorg/driver/xf86-video-intel 
cd xf86-video-intel 
./autogen.sh 
cd tools
make 
sudo cp intel-virtual-output /usr/bin/ 
sudo chmod +x /usr/bin/intel-virtual-output
```
## Xorg configuration

The configuration for xorg is found in `/etc/bumblebee/xorg.conf.nvidia`.

In many forums and blogs, It is said that the only modification that is required is to add the propre PCI id in the "Device" section. So, your file should look like:
```
Section "ServerLayout"
    Identifier  "Layout0"
    Option      "AutoAddDevices" "false"
    Option      "AutoAddGPU" "false"
EndSection

Section "Device"
    Identifier  "DiscreteNvidia"
    Driver      "nvidia"
    VendorName  "NVIDIA Corporation"
    BusID       "PCI:01:00:0" # <== here is the added line
```
Note that the busid can be found with `lspci | grep -i vga`. The id is the first field in the line containing the word "nvidia". Just be sure to change the dot by a colon.

If this does not work (which was my case), take a look at the section [debug](#errorsdebug) ^^.

## Use your external monitors

### Turn it on

After a reboot (just to be sure) and with a monitor connected either with VGA or DisplayPort, try the following:
```bash
sudo modprobe bbswitch # load the module in the kernel
sudo start bumblebeed  # only if not already running
optirun true
intel-virtual-output
```
Now, your monitor should wake up. To change its configuration, simply use the displays tools in the settings. It should work out of the box.

### Turn it off
```
# kill smoothly the second xorg server
> ps -ef | grep -i xorg
...
root  3865  3851  2 18:42 ?  00:00:10 Xorg :8 -config /etc/bumblebee/xorg.conf.nvidia ...
> sudo kill -15 3865

# stop bumblebeed
> sudo stop bumblebeed

# then, turn off your nvidia card
> sudo rmmod nvidia
> sudo tee /proc/acpi/bbswitch <<<OFF
```

## Errors, debug

	- check that you monitor is actually connected. If not, the output of optirun will be "no screen found"
	- check that the bbswithc is actually on with
        ```bash
        cat /proc/acpi/bbswitch 
        0000:01:00.0 ON  # should be ON!
        ```
    
	- check your logs (`dmesg` or `tail /var/log/syslog`) for any suspicious lines
	- try to launch intel-virtual-output like this: `optirun intel-virtual-output` instead of `optirun true; intel-virtual-ouptut` (which, in my case, does not work!)

If none of those work, there is still a possibility (see below).

### Errors "NVIDIA(0): Unable to get display device for DPI computation"

In my case, nothing happened when I ran intel-virtual-ouptput, but the logs where clear and optirun did function properly. 
After parsing the logs carefully, I detected some strange lines:
```
Feb  3 18:38:25 Cymbalta bumblebeed[3815]: [XORG] (WW) "glamoregl" will not be loaded unless you've specified it to be loaded elsewhere.
Feb  3 18:38:25 Cymbalta bumblebeed[3815]: [XORG] (WW) "xmir" is not to be loaded by default. Skipping.
Feb  3 18:38:25 Cymbalta bumblebeed[3815]: [XORG] (WW) Unresolved symbol: fbGetGCPrivateKey
Feb  3 18:38:25 Cymbalta bumblebeed[3815]: [XORG] (WW) NVIDIA(0): Unable to get display device for DPI computation.
Feb  3 18:38:25 Cymbalta bumblebeed[3815]: [XORG] (WW) NVIDIA(0): UBB is incompatible with the Composite extension.  Disabling
```

I finally made it work by changing totally the **/etc/bumblebee/xorg.conf.nvidia** file. 
Here is now how it looks like:
```
Section "ServerLayout"
    Identifier     "Layout0"
    Screen         "Screen0"
    Option         "AutoAddDevices" "false"
EndSection

Section "Device"
    Identifier     "Device0"
    Driver         "nvidia"
    VendorName     "NVIDIA Corporation"
    BusID          "PCI:1:0:0"
    #Option         "ConnectedMonitor" "DFP"
    Option         "DPI" "96 x 96" 
EndSection

Section "Screen"
    Identifier     "Screen0"
    Device         "Device0"
    DefaultDepth    24
    SubSection     "Display"
    Depth          24
End
```
And tadaaahhhhh, my monitors worked perfectly. 

If I run `xrandr`, I now have a large number of virtual outputs, and I can connect with both DisplayPort or VGA!
```
Screen 0: minimum 320 x 200, current 1920 x 1080, maximum 32767 x 32767
LVDS1 connected primary 1920x1080+0+0 (normal left inverted right x axis y axis) 344mm x 193mm
   1920x1080      60.0\*+   59.9     50.0  
   1680x1050      60.0     59.9  
   1600x1024      60.2  
   1400x1050      60.0  
   1280x1024      60.0  
   1440x900       59.9  
   1280x960       60.0  
   1360x768       59.8     60.0  
   1152x864       60.0  
   1024x768       60.0  
   800x600        60.3     56.2  
   640x480        59.9  
VGA1 disconnected (normal left inverted right x axis y axis)
VIRTUAL1 connected 1920x1080+0+0 0mm x 0mm
   VIRTUAL1.735-1920x1200   60.0  
   VIRTUAL1.736-1920x1080   60.0\* 
   1680x1050      60.0  
   VIRTUAL1.738-1600x1200   60.0  
   1280x1024      60.0  
   1280x960       60.0  
   1024x768       60.0  
   800x600        60.3  
   640x480        59.9  
VIRTUAL2 disconnected
VIRTUAL3 disconnected
VIRTUAL4 disconnected
VIRTUAL5 disconnected
VIRTUAL6 disconnected
VIRTUAL7 disconnected
VIRTUAL8 disconnected
```

## Automate the process

**Warning**: this script was written a midnight and can be buggy. It will not break your system (at least, a reboot will always do its job), but would need a lot of improvements. Take it as a good starting point, nothing else...

```bash
#!/bin/sh
set -x

case "$1" in
    "start")
        # enable bbswitch
        [ $( lsmod | grep bbswitch) -eq 1 ] && sudo modprobe bbswitch
        echo "ON" | sudo tee /proc/acpi/bbswitch
        # start the bumblebee daemon
        pgrep bumblebeed
        [ $? -eq 1 ] && sudo start bumblebeed
        # launch the virtual output monitor
        sleep 1
        optirun intel-virtual-output
        if [ $? -eq 0 ]; then
            echo "done"
            exit 0
        else
            echo "Oops, an error occurred"
            tail -20 /var/log/syslog
            exit 1
        fi
        ;;

    "stop")
        # find the second xorg server process id
        pid=$( ps -ef | grep "Xorg.*bumblebee" | grep -v grep | awk '{ print $2 }' )
        echo "pid is $pid"

        # stop the xorg server
        if [ -n "$pid" ]; then
            sudo kill -15 $pid
            ret=$?
            echo "second xorg server killed"
        else
            echo "error: second xorg server not running"
            ret=0
        fi

        # stop bumblebeed
        sudo stop bumblebeed
        # turn off nvidia card
        sudo rmmod nvidia
        echo "OFF" | sudo tee /proc/acpi/bbswitch

        exit $ret
        ;;

    *)  echo "usage $0 start | stop";
        exit 1
        ;;
esac
```
