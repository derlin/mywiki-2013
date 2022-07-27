---
title: "gparted: segfault while creating a gpt table"
date: "2014-07-25"
categories: 
  - "configuration-tricks"
  - "material"
---

## The problem

While willing to create a simple gpt table on a brand new disk, I got the following error:
```bash
[~] sudo gparted
======================
libparted : 3.1
======================
/usr/bin/gparted: line 179:   775 Segmentation fault      $BASE_CMD
```

## The solution

A simple way to fix it is to fill the first sectors of the disk with zeroes (or anything, really), in order to force gparted to consider this disk as "raw":

```bash
[~] sudo dd if=/dev/zero of=/dev/sde
^C # use ctrl+c after ~ 20 secs should do the trick
961529+0 records in
961529+0 records out
492302848 bytes (492 MB) copied, 19.2061 s, 25.6 MB/s
```
Relaunches gparted and normally it should run smoothly.
