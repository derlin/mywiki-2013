---
title: "Separate boot partition after install"
date: "2014-09-14"
categories: 
  - "configuration-tricks"
  - "material"
---

Assuming you already created and formatted a boot partition (if you use LVM, you can safely use gparted from an ubuntu live cd, it works great ^^), here are the steps to follow.

Note: I assume you use an Ubuntu Live CD.

1. Mount your root partition and your boot partition, for example in /mnt/root and /mnt/boot.
2. Copy all the files from root/boot to boot.
3. Remove all the files from the root/boot folder.
4. Umount boot and remount it under /mnt/root/boot.
5. Mount the different volatile directories, which will be necessary to update grub:
    ```bash
    for i in /dev /dev/pts /proc /sys /run; do sudo mount -B $i /mnt/root$i; done
    ```
6. Chroot into your system: `chroot /mnt/root`.
7. Update your fstab (/etc/fstab):
    ```bash
    blkid /dev/sda1 # get the UUID of the boot partition
    vim /etc/fstab
    ## add the following line:
    UUID= ext3   /boot  defaults  0 0 
    ```
8. Update grub:
    ```bash
    grub-install --recheck /dev/sda
    grub-mkconfig -o /boot/grub/grub.cfg
    ```
9. Exit the chroot environment (exit) and umount everything:
    ```bash
    for i in /dev/pts /dev /proc /sys /run; do sudo mount -B $i /mnt/root$i; done
    umount /mnt/root/boot
    umount /mnt/root
    ```
10. Reboot and enjoy
