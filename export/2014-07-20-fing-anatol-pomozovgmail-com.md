---
title: "f***ing anatol.pomozov@gmail.com"
date: "2014-07-20"
categories: 
  - "archlinux"
  - "configuration-tricks"
---

Every time I use pacman on a new machine, this f\*\*\*ing key error occurs:
```
downloading required keys...
:: Import PGP key 4096R/, "Anatol Pomozov ", created: 2014-02-03? \[Y/n\]
error: key "Anatol Pomozov ;" could not be imported
error: required key missing from keyring
error: failed to commit transaction (unexpected error)
Errors occurred, no packages were upgraded.
```

To solve it, I am not quite sure... I normally try one of the two things below, hoping that one works:

1. On a new install:
    ```bash
    pacman-key --init && pacman-key --populate archlinux
    ```
    
2. On a running system:
    ```bash
    pacman -S archlinux-keyring; pacman -Su
    ```
    
    (although, that will put his key in your keyring and let him break whatever he wants... just like any other developer)
    
    source [https://bbs.archlinux.org/viewtopic.php?id=178185](https://bbs.archlinux.org/viewtopic.php?id=178185)
