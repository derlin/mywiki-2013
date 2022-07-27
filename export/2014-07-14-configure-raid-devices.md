---
title: "RAID"
date: "2014-07-14"
categories: 
  - "configuration-tricks"
  - "material"
---

## Types of RAID

RAID stands for **Redondant Array of Independant Devices**. There are basically five types of RAID commonly used these days:

- RAID 0: the data are distributed amongst two disks, without redundancy (stripping). It improves performances, but does not protect data against devices failures
- RAID 1: the disks are exact mirrors of each other. This is a pure redundancy without striping or parity: its protects data, but does not improve performances.
- RAID 1+0: stripping + mirroring. Requires at least 4 disks.
- RAID 5: blocks of data are distributed amongst at least 3 disks (striping), as well as the parity (versus one dedicated parity disk, as in RAID 4). This protects data against one disk failure. Because of the parity, the space actually available is thus <nbr of disks> - 1.
- RAID 6: same as RAID 5, but there are two blocks of parity for each <nbr of disks -2> blocks of data.

## Configuring a RAID 5 (software) in linux

1. Totally erase the content of your three (or more) disks
2. On each of them, create a gpt table. The easiest way is to use GParted: select the device, clic Device > create partition table
3. Use fdisk to create a Linux RAID partition on each disk, spreading upon on the space available:
    ```text
    # sudo fdisk /dev/sd[X]
    Command (m for help): n
    Partition number (1-128, default 1):  
    First sector (34-5860533134, default 2048): 
    Last sector, +sectors or +size{K,M,G,T,P} (2048-5860533134, default 5860533134): 
    
    Created a new partition 1 of type 'Linux filesystem' and of size 2.7 TiB.
    
    Command (m for help): t
    
    Selected partition 1
    Partition type (type L to list all types): 14
    Changed type of partition 'Linux filesystem' to 'Linux RAID'.
    
    Command (m for help): w
    The partition table has been altered.
    Calling ioctl() to re-read partition table.
    Syncing disks.
    ```
    
4. Install mdadm
5. Configure RAID with the following:
    ```bash
    sudo mdadm --create --verbose --level=raid5 \
     --raid-devices=3  /dev/md0 /dev/sd[acd]1
    ```

6. Wait for the process to terminate. You can see the state of the RAID by displaying the content of the file `/proc/mdstat`. To view the changes "live", use `watch -n 1 cat /proc/mdstat`

If everything went right, you should now have a new device (sudo fdisk -l) called /dev/md0. Partition and use it like a regular disk.

## Deleting the array (once and for all)

```bash
sudo mdadm --stop /dev/md0
sudo mdadm --zero-superblock /dev/sd[cde] # to be done for each disk of the array
sudo mdadm --remove /dev/md0
```