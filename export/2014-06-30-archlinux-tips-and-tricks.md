---
title: "Archlinux tips and tricks"
date: "2014-06-30"
categories: 
  - "archlinux"
  - "configuration-tricks"
  - "env"
tags: 
  - "archlinux"
  - "configuration"
  - "yaourt"
---

## Easier installs with Yaourt

Create or open the file ~/.yaourtrc and add/uncomment the following:
```bash
BUILD_NOCONFIRM=1 # No confirm for build 
EDITFILES=0 	  # No prompt for editing files
# to also suppress pacman Y|n install:
PU_NOCONFIRM=1
```