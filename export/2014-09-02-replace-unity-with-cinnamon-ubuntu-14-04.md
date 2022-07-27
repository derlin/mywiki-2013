---
title: "Replace Unity with Cinnamon - Ubuntu 14.04"
date: "2014-09-02"
categories: 
  - "configuration-tricks"
  - "env"
---

## Backup

If you use lvm, do a snapshot of the system:
```bash
sudo lvcreate -s -n snap -L 10g sys/lroot
sudo lvcreate -s -n snap -L 10g sys/lhome
sudo lvdisplay
```

This will create two snapshots, one for the root partition and one for the home partition. If anything goes wrong, simply do:
```bash
sudo lvconvert --merge sys/snap
sudo lvconvert --merge sys/snapHome
sudo reboot
```
and you are back to unity.

If you do not use lvm, well, it is time to start !

## Install Cinnamon

For Ubuntu 14.04, there is currently no cinnamon packages in the Universe repository (read [this](http://www.omgubuntu.co.uk/2014/05/ubuntu-cinnamon-desktop-ppa-retired) for more info.

Fortunately, there are several PPAs which seem to be stable. Note that I am not responsible for any dammages to your machine.

### Stable PPA (14.04 only !)

```bash
sudo add-apt-repository ppa:lestcape/cinnamon
sudo apt-get update
sudo apt-get install cinnamon
```

### Unstable - Nightly build (14.04 only !)

```bash
sudo add-apt-repository ppa:gwendal-lebihan-dev/cinnamon-nightly
sudo apt-get update
sudo apt-get install cinnamon
```

Then, logout and login with cinnamon to ensure everything went well.

## Purge Unity from your system

The fun part ;).

Remove the unecessary packages:

```bash
sudo apt-get autoremove --purge unity unity-common unity-services unity-lens-* unity-scope-* unity-webapps-* gnome-control-center-unity hud libunity-core-6* libunity-misc4 libunity-webapps* appmenu-gtk appmenu-gtk3 appmenu-qt* overlay-scrollbar* activity-log-manager-control-center firefox-globalmenu thunderbird-globalmenu libufe-xidgetter0 xul-ext-unity xul-ext-webaccounts webaccounts-extension-common xul-ext-websites-integration gnome-control-center gnome-session
```

Since cinnamon uses Muffin as a Window Manager, compiz is useless:

```bash
sudo apt-get autoremove --purge compiz compiz-gnome compiz-plugins-default libcompizconfig0
```

Cinnamon is shipped with Nemo, which is a good alternative to Nautilus:
```bash
sudo apt-get autoremove --purge nautilus nautilus-sendto nautilus-sendto-empathy nautilus-share
```

Optionally, remove Zeitgeist:
```bash
zeitgeist-daemon --quit
sudo apt-get autoremove --purge activity-log-manager-common python-zeitgeist rhythmbox-plugin-zeitgeist zeitgeist zeitgeist-core zeitgeist-datahub
```

Finally, remove the config files polluting your home, namely:
```bash
~/.local/share/unity-webapps
~/.compiz
~/.config/compiz-1
~/.config/nautilus
~/.local/share/nautilus
~/.local/share/zeitgeist
```

You can also install `bleachbit`, a nice utility to clean temp file (a sort of CCLeaner for Linux).

Reboot, and normally you have Cinnamon !
