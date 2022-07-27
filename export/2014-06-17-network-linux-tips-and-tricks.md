---
title: "Network Linux tips and tricks"
date: "2014-06-17"
categories: 
  - "configuration-tricks"
  - "env"
---

## NetworkManager config

edit the corresponding file in /etc/NetworkManager/system-connections/, and add/modify the following:
```bash
[802-1x]
...
password-flags=0      # change it to 0 instead of 1
system-ca-certs=false # instead of true to avoid no cert error
password=YourPass     # careful, it is in clear text !!
```