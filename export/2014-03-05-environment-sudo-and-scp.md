---
title: "environment, sudo and scp"
date: "2014-03-05"
categories: 
  - "archlinux"
  - "bash"
  - "configuration-tricks"
  - "env"
---

## Keep your environment with sudo

I have always been pissed off when I launch a program with sudo and I get an error saying the environment variable is not defined... Here are some tricks to avoid that. Open your sudoers file in write mode through `sudo visudo` and:

- in order to keep your home (~) uncomment or add the line `Defaults env_keep += "HOME"`
- you can do the same for any other variable you want to be exported, like `Defaults env_keep = "http_proxy"` for example
- since we are at it, try uncomment the `Defaults insults`. This will replace the "wrong password" message by a clever insult ^^

## The strange "scp: command not found" error

When you launch scp, it will first connect to the other machine with ssh, and then copy the file(s).

If you did not know the sudo tricks described above, you maybe did the same mistake as me: modify the `/etc/environment` file and add a line like `PATH="...$PATH"`. I don't know exactly why, but this messes up with scp, since the PATH that is exported does not contain the usual `/usr/bin` anymore; thus scp is not found on the remote machine.

Another common mistake is to comment out the `export PATH=...` in your .bashrc or .zshrc. This result in the same problem.
