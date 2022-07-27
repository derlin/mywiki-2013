---
title: "Archlinux, bumblebee, nvidia"
date: "2014-04-30"
categories: 
  - "configuration-tricks"
  - "material"
tags: 
  - "bumblebee-archlinux"
---

## Installation

Install:

- [xf86-video-nouveau](https://www.archlinux.org/packages/?name=xf86-video-nouveau) - experimental 3D acceleration driver.
- [nouveau-dri](https://www.archlinux.org/packages/?name=nouveau-dri) - Mesa classic DRI + Gallium3D drivers.
- [mesa](https://www.archlinux.org/packages/?name=mesa) - Mesa 3D graphics libraries.
- bbswitch - used to switch card on and off
- [bumblebee](https://www.archlinux.org/packages/?name=bumblebee) - The main package providing the daemon and client programs.

Note that you don't need nvidia or nvidia-utils. Open the file /etc/bumblebee/bumblebee.conf and modify the line `Driver=` by `Driver=nouveau`.

## Launch

For testing, do the following, in this order:
```bash
sudo modprobe bbswitch # load kernel module
sudo bumblebeed -vv # verbose mode

# in another terminal
intel-virtual-output
# now check in display if a new screen appears, or type optirun true
```
If it works, simply set bumblebeed as a service at startup and you are done! To use an external monitor, type `intel-virtual-output` and enjoy.

## Modification from the previous posts

After weeks of trying, I discovered that using driver=nvidia in bumblebee, which was required in ubuntu (at least when I tried). It seems that something changed, since now switching back to nouveau resolved everything !! YOUPIEE
