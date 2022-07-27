---
title: "Migrate rootfs to LVM"
date: "2014-08-25"
categories: 
  - "configuration-tricks"
  - "material"
---

## Preliminary notes

This procedure was tested on a 500GB sdd containing one ext4 partition for the system. The /home and /opt directories are on other disks and no swap partition is used. The linux system to migrate is archlinux 3.16.1, located on /dev/sda.

## Backing up the system

Since the root filesystem is only 16GB out of a 500GB partition, it is recommended to use tar.

1. boot on a live usb
2. mount the root filesystem: `sudo mkdir /mnt/rootfs && sudo mount -t ext4 /dev/sda /mnt/rootfs`
3. do the backup:
    ```bash
    cd /mnt/rootfs
    tar cvzpf rootfs.tar.gz .
    ```
    Note the \-p option: it _keeps the original permissions_, something not to forget!

## Installing lvm and recreating the root partition

1. Open gparted, select /dev/sda and click on device > create partition table... to create a gpt partition table. It will also **format the entire disk**.
2. We will use `gdisk` to create two partitions:
    
    - a _BIOS boot partition_, used by GRUB to embed its core.img in the absence of post-MBR gap in GPT partitioned systems
    - the actual lvm partition
    
    Open a terminal and type:
    ```
    gdisk
    
    Command (? for help): p
    Number  Start (sector)    End (sector)  Size       Code  Name
    
    Command (? for help): n
    Partition number (1-128, default 1): 
    First asector (34-15974366, default = 36) or {+-}size{KMGTP}: 
    Last sector (36-15974366, default = 15974366) or {+-}size{KMGTP}: +1007k
    Current type is 'Linux filesystem'
    Hex code or GUID (L to show codes, Enter = 8300): ef02
    Changed type of partition to 'BIOS boot partition'
    
    Command (? for help): n
    Partition number (1-128, default 1): 
    First sector (34-15974366, default = 36) or {+-}size{KMGTP}: 
    Last sector (36-15974366, default = 15974366) or {+-}size{KMGTP}: +1007k
    Current type is 'Linux filesystem'
    Hex code or GUID (L to show codes, Enter = 8300): 8e00
    Changed type of partition to 'Linux LVM'
    
    Command (? for help): p
    Number  Start (sector)    End (sector)  Size       Code  Name
       1              36            2049   1007.0 KiB  EF02  BIOS boot partition
       2            2052        15974366   7.6 GiB     8E00  Linux LVM
    
    Command (? for help): w
    
    Command (? for help): q
    ```
    
3. create the root partition on lvm
    ```bash
    sudo su
    pvcreate /dev/sda2
    vgcreate vol-name /dev/sda2
    lgcreate -L 30G -n lroot vol-name 
    
    pvs
    lvs
    ```
    
4. format the root partition and copy back the system
    ```bash
    mkfs.ext4 /dev/mapper/vol-name-lroot
    mount /dev/mapper/vol-name-lroot /mnt
    cd /mnt
    tar xpvzf rootfs.tar .
    ```

## Repairing the system

Mount your partition (if not already done).

### Update fstab

```
sudo blkid /dev/mapper/volname-lroot
/dev/sda2: LABEL="rootfs" UUID="102adbe7-87c1-4b5a-80e2-97013851d790" TYPE="ext4" PARTUUID="c349be14-9052-407f-b1ce-c419bd20704f"
```

Then, edit `/mnt/etc/fstab` and replace the UUID of “/” by the one above.

### Recreate the boot image

First, chroot into your system. With an archlinux Live USB, use `arch-chroot`.

* * *

###### chroot using an Ubuntu Live USB

Before chroot, you need to manually mount your virtual filesystem, thing that the `arch-chroot` command does automatically for you:

```bash
# Mount root partition:
sudo mount /dev/sdXY /mnt # /dev/sdXY is your root partition, e.g. /dev/sda1

# If you have a separate boot partition you'll need to mount it also:
sudo mount /dev/sdYY /mnt/boot

# Mount your virtual filesystems:
for i in /dev /dev/pts /proc /sys /run; do sudo mount -B $i /mnt$i; done

# Chroot
sudo chroot /mnt
```

Once chrooted:

1. Add lvm hook to mkinitcpio.conf. Edit `/etc/mkinitcpio.conf` and make sure the udev and lvm2 mkinitcpio hooks are enabled:
    ```bash
    HOOKS="base udev ... block lvm2 filesystems"
    ```
2. (_I am not sure this step is useful, but anyway_) edit `/etc/rc.conf` and add:
    ```bash
    USELVM="yes"
    ```
3. recreate the linux.img and linux-fallback.img:
    ```bash
    cd /boot
    mkinitcpio -p linux
    ```
    The -p switch specifies a preset to utilize; most kernel packages provide a related mkinitcpio preset file, found in /etc/mkinitcpio.d (e.g. /etc/mkinitcpio.d/linux.preset for linux). A preset is a predefined definition of how to create an initramfs image instead of specifying the configuration file and output file every time.

### Repair GRUB

```bash
grub-install --recheck /dev/sda
grub-mkconfig -o /boot/grub/grub.cfg
```

**Note**: if you get errors like
```
WARNING: Failed to connect to lvmetad: No such file or directory. Falling back to internal scanning.
/run/lvm/lvmetad.socket: connect failed: No such file or directory
...
```

don’t freak out, this won’t prevent your system to boot normally.

### Test it

Now, reboot. Everything should run smoothly. If so, don’t forget to take a screenshot of your root filesystem using:
```bash
lvcreate -s -n sys-snap -L 3g vol-name/lroot
```