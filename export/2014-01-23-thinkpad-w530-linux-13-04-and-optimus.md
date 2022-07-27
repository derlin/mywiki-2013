---
title: "Thinkpad w530, linux 13.04 and optimus"
date: "2014-01-23"
categories: 
  - "configuration-tricks"
  - "material"
tags: 
  - "customization"
  - "linux"
---

## Nvidia drivers

Which nvidia driver to use ? Actually, I don't really know the answer. I tried like 4 different ones... I think the one that really worked for me was the one from the x-swat repo. Here is how to get it:
```bash
sudo add-apt-repository ppa:ubuntu-x-swat/x-updates 
sudo apt-get update
# if you already have a version of the nvidia-current
# driver installed,  use update instead of install !
sudo apt-get install nvidia-current
```

## Bumblebee

First, you need to install the bumblebee daemon. For those who have never heard of it, the [Bumblebee Project](https://github.com/Bumblebee-Project/Bumblebee/wiki) is a set of tools developed by people aiming to provide Optimus support under Linux (sic).

The installation is quite straight-forward:
```bash
sudo add-apt-repository ppa:bumblebee/stable
sudo apt-get update
sudo apt-get install bumblebee bumblebee-nvidia bbswitch-dkms
# those packages are needed, if not already installed:
sudo apt-get install virtualgl linux-headers-generic
sudo reboot
```

We also need to tweak the config a bit. Open `/etc/bumblebee/bumblebee.conf` as root and modify the following:
```bash
Driver=nvidia          # instead of Driver=
KeepUnusedXServer=true # instead of false
...
[driver-nvidia]
PMMethod=none          # instead of auto
KernelDriver=nvidia    # see notes below
```

Reboot and try to run `optirun true` and/or `sudo bumblebeed restart`. Ensure that there is not output in stderr.

Test that it runs properly with the command `optirun glxgears`. A windows with psychedelic spheres should appear.

### Error: module 'nvidia' not found

The problem is that the `KernelDriver` option in bumblebee.conf does not match the actual module loaded into the kernel. To avoid this, replace nvidia by the output of the following command:
```bash
# note: ignore nvidiafb.ko
find /lib/modules/$(uname -r) -name 'nvidia*.ko*'
```

In my case, the correct option was nvidia\_304.

If the error persists, add an entry in the LibraryPath list under \[driver-nvidia\] with the path to the file nvidia\_drv.so (`find / -name "nvidia_drv.so"`). In my case:
```bash
# comma-separated path of the directory containing nvidia_drv.so and the
# default Xorg modules path
XorgModulePath=/usr/lib/nvidia-304/xorg/,/usr/lib/nvidia-current/xorg,/usr/lib/xorg/modules
```
### Error: secondary GPU not found

First and foremost, ensure that you have an external monitor connected to your computer.

If this is the case, open `/etc/bumblebee/xorg.conf.nvidia` and ensure that the `BusID` under the Device section is correct. To do that, use the following:
```bash
# the bus id is the first field of the second line, i.e. the NVIDIA VGA controller
> lspci | grep -i vga
00:02.0 VGA compatible controller: Intel Corporation 3rd Gen Core processor Graphics Controller (rev 09)
01:00.0 VGA compatible controller: NVIDIA Corporation GK107GLM [Quadro K2000M] (rev a1)
```
In my case, I had to set `BusID "PCI:01:00:0"`. Note that the final dot is replaced by a **colon**.

Third, ... TODO

## Patch the intel-video driver

Under ubuntu 12.04, you needed to recompile the patched package by hand (and in my case, no patch worked, even if I tried more than three different ones).

With ubuntu 13.04, a fully functional package is already available. To install it, add the following ppa and use apt-get:
```bash
sudo add-apt-repository ppa:krlmlr/ppa
sudo apt-get update
sudo apt-get install xserver-xorg-video-intel
```

## Screenclone

This will be the main program used for rendering using the integrated intel card. See [hybrid-screenclone on git](https://github.com/liskin/hybrid-screenclone) for more infos.

Note: if you don't have git installed, now its the time to get it using `sudo apt-get install git`.
```bash
git clone git://github.com/liskin/hybrid-screenclone.git
cd hybrid-screenclone
sudo make
sudo cp screenclone /usr/bin/
sudo chmod +x /usr/bin/screenclone
sudo cp xorg.conf.nvidia /etc/bumblebee/xorg.conf.nvidia
sudo rm /etc/X11/xorg.conf
```
## Configuring xorg

This is **required for ubuntu 13.04**. Open `/etc/X11/xorg.conf` (create it if necessary), and copy-paste the following:
```
Section "Device"
    Identifier "intel"
    Driver "intel"
    Option "AccelMethod" "uxa"
    Option "Virtuals" "2"      # create two virtual displays
EndSection
```

Reboot for the changes to take effect.

## Did you do it right ?

Here are the outputs of some commands on my machine.

The following packages should be present:
```bash
> dpkg -l | grep nvidia                                        
ii  bbswitch-dkms                             0.8-1~raringppa1                           all          Interface for toggling the power on NVIDIA Optimus video cards
ii  bumblebee                                 3.2.1-1~raringppa5                         amd64        NVIDIA Optimus support
ii  bumblebee-nvidia                          3.2.1-1~raringppa5                         amd64        NVIDIA Optimus support using the proprietary NVIDIA driver
ii  libkwinnvidiahack4                        4:4.10.5-0ubuntu0.2                        amd64        library used by nvidia cards for the KDE window manager
ii  nvidia-304                                304.116-0ubuntu1~xedgers~raring1           amd64        NVIDIA binary Xorg driver, kernel module and VDPAU library
ii  nvidia-current                            304.116-0ubuntu1~xedgers~raring1           amd64        Transitional package for nvidia-current
ii  nvidia-settings-304                       304.116-0ubuntu1~xedgers~raring1           amd64        Tool for configuring the NVIDIA graphics driver
```
You should see at least one Virtual entry running `xrandr`:
```bash
> xrandr
Screen 0: minimum 320 x 200, current 1920 x 1080, maximum 8192 x 8192
LVDS1 connected 1920x1080+0+0 (normal left inverted right x axis y axis) 344mm x 193mm
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
VIRTUAL1 unknown connection (normal left inverted right x axis y axis)
   1920x1200      60.0  
   1920x1080      59.9  
   1600x1200      60.0  
   1680x1050      60.0     59.9  
   1400x1050      60.0  
   1440x900       59.9  
   1280x960       60.0  
   1360x768       59.8     60.0  
   1152x864       60.0  
   800x600        56.2  
   640x480        59.9  
VIRTUAL2 unknown connection (normal left inverted right x axis y axis)
   1920x1200      60.0  
   1920x1080      59.9  
   1600x1200      60.0  
   1680x1050      60.0     59.9  
   1400x1050      60.0  
   1440x900       59.9  
   1280x960       60.0  
   1360x768       59.8     60.0  
   1152x864       60.0  
   800x600        56.2  
   640x480        59.9 
```
The command `optirun true` should be totally silent.

When starting the bumblebeed client, nothing suspicious should be outputed:
```bash
> sudo stop bumblebeed # if already running, stop it
> sudo bumblebeed -vv      
[ 5263.200085] [DEBUG]Found card: 01:00.0 (discrete)
[ 5263.200137] [DEBUG]Found card: 00:02.0 (integrated)
[ 5263.200161] [DEBUG]Reading file: /etc/bumblebee/bumblebee.conf
[ 5263.200622] [INFO]Configured driver: nvidia
[ 5263.200661] [DEBUG]Skipping auto-detection, using configured driver 'nvidia'
[ 5263.200839] [DEBUG]Process /sbin/modprobe started, PID 6347.
[ 5263.200885] [DEBUG]Hiding stderr for execution of /sbin/modprobe
[ 5263.203065] [DEBUG]SIGCHILD received, but wait failed with No child processes
[ 5263.203165] [INFO]PM is disabled, not performing detection.
[ 5263.203207] [DEBUG]Active configuration:
[ 5263.203236] [DEBUG] bumblebeed config file: /etc/bumblebee/bumblebee.conf
[ 5263.203266] [DEBUG] X display: :8
[ 5263.203285] [DEBUG] LD_LIBRARY_PATH: /usr/lib/nvidia-current:/usr/lib32/nvidia-current
[ 5263.203310] [DEBUG] Socket path: /var/run/bumblebee.socket
[ 5263.203336] [DEBUG] pidfile: /var/run/bumblebeed.pid
[ 5263.203366] [DEBUG] xorg.conf file: /etc/bumblebee/xorg.conf.nvidia
[ 5263.203394] [DEBUG] xorg.conf.d dir: /etc/bumblebee/xorg.conf.d
[ 5263.203422] [DEBUG] ModulePath: /usr/lib/nvidia-304/xorg/,/usr/lib/nvidia-current/xorg,/usr/lib/xorg/modules
[ 5263.203444] [DEBUG] GID name: bumblebee
[ 5263.203466] [DEBUG] Power method: none
[ 5263.203490] [DEBUG] Stop X on exit: 0
[ 5263.203519] [DEBUG] Driver: nvidia
[ 5263.203540] [DEBUG] Driver module: nvidia_304
[ 5263.203567] [DEBUG] Card shutdown state: 1
[ 5263.203717] [DEBUG]Process /sbin/modprobe started, PID 6348.
[ 5263.203790] [DEBUG]Hiding stderr for execution of /sbin/modprobe
[ 5263.205938] [DEBUG]SIGCHILD received, but wait failed with No child processes
[ 5263.206015] [DEBUG]Configuration test passed.
[ 5263.206710] [INFO]bumblebeed 3.2.1 started
[ 5263.206826] [INFO]Initialization completed - now handling client requests
^C[ 5385.447337] [WARN]Received Interrupt signal.
[ 5385.447388] [DEBUG]Socket closed.
[ 5385.447497] [DEBUG]Killing all remaining processes.
```

## Finally, use your second monitor

Note that it works the same for both VGA and DisplayPort!
```bash
# be sure to turn on optirun only when an external
# device is wired to you laptop
> optirun true

# == now, some possibilities of screenclone

# just to use a mirror screen, nothing fancy
> screenclone -d :8

# dual monitors, the external one on the right
> randr --output LVDS1 --output VIRTUAL1 --mode 1920x1200 \
   --right-of LVDS1

# the option -d :8 is required only if you use bumblebee
# -x 1 is VIRTUAL 1, -x 2 is VIRTUAL 2
> screenclone -d :8 -x 1 &

# == when you are finished
# get screenclone from background
# and turn it off using CTRL+C
> fg
^C
# turn off the virtual screen
> xrandr --output VIRTUAL1 --off
```
## External links and resources

- [Optimal Ubuntu Graphics Setup for Thinkpads](http://sagark.org/optimal-ubuntu-graphics-setup-for-thinkpads/)
- [A Solution for External Monitors on a Thinkpad W520 running Linux](http://zachstechnotes.blogspot.ch/2012/04/post-title.html)
- [The Lenovo W530 with Optimus Technology and Linux](http://cfusting.wordpress.com/2013/09/01/the-lenovo-w530-with-optimus-technology-and-linux/)
- [Ubuntu: Bumblebee, Optimus and Multi-Monitor Support](http://blog.linuxacademy.com/linux/ubuntu-bumblebee-optimus-and-multi-monitor-support/)
