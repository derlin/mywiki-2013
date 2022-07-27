---
title: "What to do after installing ubuntu 13.10 gnome"
date: "2014-01-31"
categories: 
  - "configuration-tricks"
tags: 
  - "gnome"
  - "utilities"
  - "uuntu"
---

## Fix \*\*: no such file or directory

If you try to run some 32bits executables, you are familiar with this message. For me, it happened while trying to run adb from the terminal. It seems like many people have reported this issue on Ubuntu 12.04, 12.10 and 13.04 and there is a simpler solution than setting up a whole 32bits environment.

Open the terminal and install the following packages:
```bash
apt-get install libc6-i386 lib32stdc++6 lib32gcc1 lib32ncurses5 lib32z1
```
## Install a dark theme

The nicer I found is delorean-dark, which you can setup with:
```bash
sudo add-apt-repository ppa:noobslab/themes
sudo apt-get update
sudo apt-get install delorean-dark
```
Now, open `gnome-tweak-tools` and select your new theme from the dropdown.
