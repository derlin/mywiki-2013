---
title: "Logitech Marblemouse"
date: "2014-01-23"
categories: 
  - "configuration-tricks"
  - "material"
---

To configure it, simply add a file in your **xorg.conf.d** directory (in my case, it is `/usr/share/X11/xorg.conf.d`).

This file, named `50-marblemouse.conf` should look like this:

```
Section "InputClass"
        Identifier  "Marble Mouse"
        MatchProduct "Logitech USB Trackball"
        MatchIsPointer "on"
        MatchDevicePath "/dev/input/event*"
        Driver "evdev"
        Option "ButtonMapping" "1 2 3 4 5 6 7 8 9"
        Option "EmulateWheel" "true"
        Option "EmulateWheelButton" "9"
        Option "ZAxisMapping" "4 5"
        Option "XAxisMapping" "6 7"
        Option "Emulate3Buttons" "true"
EndSection
```

With this you'll have:

- normal left and right clicks
- two large buttons pressed together: middle click
- scroll: keep the small left button pressed and move the ball
- small right button click: back
- small left button: forth
- two large button

If the middle click does not work, ensure that it is supported/enabled by gnome:

gsettings set org.gnome.settings-daemon.peripherals.mouse middle-button-enabled true

For other configurations, see [this article](https://help.ubuntu.com/community/Logitech_Marblemouse_USB).
