---
title: "Install archlinux"
date: "2014-02-28"
categories: 
  - "archlinux"
---

Notes on partitioning
---------------------

If you start from scratch (new disk), then here are some useful information. From now on, let's say we want the root filesystem on /dev/sda and we have bios not uefi.

#### GPT vs MBR

Before digging into it, I always stumbled accross the same problem when I wanted to install grub into a partitioned disk:

```bash
# grub-install /dev/sda
/usr/sbin/grub-setup: warn: This GPT partition label has no BIOS Boot Partition; embedding won't be possible!.
/usr/sbin/grub-setup: warn: Embedding is not possible.  GRUB can only be installed in this setup by using blocklists.  However, 
blocklists are UNRELIABLE and their use is discouraged..
/usr/sbin/grub-setup: error: will not proceed with blocklists.
```
To avoid this, I usually cleared my disk and created a mbr partition... Not really a solution. Here is the trick:

*   Install gdisk: `pacman -S gdisk`
*   Delete everything from your disk
*   Using gdisk, create a 1007K partition of type **BIOS boot partition**at the beginning of the disk:
    ```bash
    # gdisk /dev/sda
    Command (? for help): n
    Partition number (1-128, default 1): First sector (0-15654878, default = 0): Size in sectors or {KMGTP} (default = 15654839): 1007K
    Hex code or GUID (L to show codes, Enter = 8300): ef02
    Command (? for help): w
    ```
        
    
*   Partition the rest of the disk as you wish.
    ```
    Linux partition Hex Code: 8300  
    Swap Hex Code: 8200  
    Show partition table: p  
    Write table on disk: w  
    ```
    
*   Don't forget to create a filesystem on your root partition: `mkfs.ext4 /dev/sda2`
*   Check the result with `parted /dev/sda print`

**Note**

The BIOS boot partition doesn't get mounted and doesn't need formatting or a file system. It is just somewhere grub uses when it installs to disk and it finds it automatically.

If you want a regular boot partition, you need to create a separate partition for that with a regular file system type and of a reasonable size. ext4 is a perfectly good choice for /boot (I use it for my /boot) but you will need something bigger than 1MB!

## Install

create a usb stick and boot on it. In the menu, select "boot archlinux \[your architecture\]". You will get a prompt (zsh :)), where you are root. First, language and keyboard, internet connection:
```bash
loadkeys fr_CH-latin1 # change keyboard layout
vi /etc/locale.gen
## uncomment the langugage that suits you
locale-gen
export LANG=fr_CH.utf8 # or the one you chose
systemctl start dhcpcd.service # enable internet service
ping www.google.com # check your connection
```
If you don't have any partition formatted in ext4, I let you find the proper documentation on how to use `fdisk` from the commandline. If, like me, you are not really sure of yourself, just use a live usb of ubuntu or something that gives you gparted ^^. If you already have a partition and just need to clear it, type `mkfs.ext4 /dev/sd[...]` Now that you have a partition in ext4 to install your new system to:
```bash
mount /dev/sd[..] /mnt # mount it to mnt
lsblk /dev/sd[..]       # make sure it is mounted
#! if you have a home on another partition or drive
mkdir /mnt/home
mount /dev/sd[...] /mnt/home
```
Select a mirror:
```bash
vi /etc/pacman.d/mirrorlist
## choose your preferred mirror list and
## set it first (for me, a swiss one does the trick)
```
Install the base system:
```bash
pacstrap -i /mnt base   # this will install the core system
                        # you can leave the default "all"
genfstab -U -p /mnt >> /mnt/etc/fstab  # create fstab
less /mnt/etc/fstab                    # verify it was properly generated

Chroot and configure the base system:

arch-root /mnt /bin/bash # unfortunately, zsh is not present on the new system
vi /etc/locale.gen
## uncomment the language you want
locale-gen
echo LANG=en_GB.UTF-8 > /etc/locale.conf
export LANG=en_GB.UTF-8

loadkeys fr_CH-latin1
setfont Lat2-Terminus16 # really nice terminal font !

vi /etc/vconsole.conf
## put the following two lines:
## KEYMAP=fr\_CH-latin1
## FONT=Lat2-Terminus16

ln -s /usr/share/zoneinfo/Europe/Zurich /etc/localtime # configure the clock
hwclock --systohc --utc

echo my-hostname > /etc/hostname # set hostname

passwd # set a password for root


pacman -S grub os-prober    # os-prober is used to detect other os 
grub-install --recheck /dev/sd[...]
grub-mkconfig -o /boot/grub/grub.cfg
## now, check /boot/grub/grub.cfg to ensure the other os were properly detected
## each of them should have a menuentry of their own
```
Finally:
```bash
exit
umount /mnt
reboot
```
### For those having GPT

The grub makes an error and says it cannot install on partitionless or blocklists devices. No worry. A simple way is to convert your HDD from GPT partition table to MBR partition table (or MSDOS in Linux) and after that install your Linux OS. For that, boot on a live cd, launch gparted, clear your entire HDD drive, go to `device > create partition table...` and select _msdos_. You can then install grub normally. Note: you must choose the boot option **Legacy first** in your BIOS for this to work.

## Next steps

Before you install a desktop environment, you will need internet access. Use the command `systemctl start dhcpcd.service` (but do not enable it, since it will later conflict with NetworkManager!) to connect.
```bash
systemctl start dhcpcd.service
pacman -Syu # update your packages
pacman -S base-devel 
```

Create a new user:
```bash
useradd -m [name]  # -m to create a home directory
passwd [name]
```

Install the video drivers and such:
```bash
pacman -S xorg-server xorg-server-utils xorg-xinit
pacman -S xf86-video-ati # for my part. For you, try to find the best video driver available with:
## lspci | grep -i vga # will display your gpu model
## pacman -Ss xf86-video | less  # will display a full list of drivers available
pacman -S xf86-input-synaptics
pacman -S ttf-dejavu
```

## Install Gnome and graphical stuffs

### Gnome and Slim

```bash
# Desktop Environment
pacman -S gnome gnome-extra
cp /etc/skel/.xinitrc /home/youruser
vim /home/youruser/.xinitrc
## add this line at the end of the file:
## exec gnome-session

# Session Manager
pacman -S slim
systemctl enable slim.service

reboot
```

### NetworkManager

```bash
pacman -S networkmanager dhclient dnsmasq
systemctl enable NetworkManager.service
systemctl start NetworkManager.service # just this time, to avoid reboot
```

### Avahi

```bash
pacman -S avahi nss-mdns
systemctl enable avahi-daemon.service
systemctl start avahi-daemon.service # just this time
vim /etc/nsswitch.conf
## in arch, .local querying is disabled by default. So replace the line:
##      hosts: files myhostname dns
## by :
##      hosts: files myhostname mdns\_minimal \[NOTFOUND=return\] dns
avahi-browse -alr # test it
```

### sudoers

The easiest way is to type `visudo` as root to access the sudoers file in write mode and to uncomment the line `%wheel ALL=(ALL) ALL`. This will allow the users in the wheel group to use sudo.

### ssh

```bash
pacman -S openssh
sysemctl enable sshd
```

### printers

```bash
pacman -S libcups cups
# and for gnome
pacman -S system-config-printer
sysemctl enable cupsd.service
```

### yaourt

From repo:

```bash
vim /etc/pacman.conf
## add the following:
\[archlinuxfr\]
SigLevel = Never
Server = http://repo.archlinux.fr/$arch
##
pacman -Sy yaourt
```

Manually:

```bash
curl -O https://aur.archlinux.org/packages/pa/package-query/package-query.tar.gz
tar zxvf package-query.tar.gz
cd package-query
makepkg -si
cd ..
curl -O https://aur.archlinux.org/packages/ya/yaourt/yaourt.tar.gz
tar zxvf yaourt.tar.gz
cd yaourt
makepkg -si
cd ..
```

### Gnome specifics

```bash
## to get back the old search behavior 
## press a letter to navigate quickly in nautilus
yaourt nautilus-typeahead

## gnome tweak tool
pacman -S gnome-tweak-tool

## black theme
yaourt gtk-theme-bleufear-git 

## set the close buttons on the left of the windows
dconf-editor
# navigate to org > gnome > shell > overrides and set the button-layout property to:
# close,maximize,minimize: 

## set ctrl+alt+T for opening a terminal
# open keyboard > shortcuts > custom shortcuts. Add a command "gnome-terminal".
```

### change uid and gid of an existing user

```bash
usermod -u groupmod -g find / -user -exec chown -h {} \;
find / -group -exec chgrp -h {} \;
usermod -g
```