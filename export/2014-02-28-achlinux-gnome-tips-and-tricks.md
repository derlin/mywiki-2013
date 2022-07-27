---
title: "Achlinux (gnome) tips and tricks"
date: "2014-02-28"
categories: 
  - "archlinux"
  - "configuration-tricks"
  - "env"
---

## Terminal does not remember working directory when opening a new tab

Normally, I was used to keep the current directory when opening a new tab, which I loved. Indeed, I often opened a new tab then detached it using `F12` so I did not have to cp to a (too) long directory... But since my reinstall, this behavior seemed to have vanished. The answer is simple:

1. Open a new terminal window
2. Clic on `Edit > Profile Preferences > Title and Command` and tick the _Run as a login shell_

Open a new windows and it is solved.

## close button on the left

First, open dconf-editor and change `gnome.shell.overrides button-layout` to "close,maximize,minimize:".

This works for applications, but there is still some troubles with the gnome tweak tools and nautilus...

### gnome 3.10 trouble

GNOME 3.10 introduced [Client Side Decoration](http://worldofgnome.org/csds-came-to-stay-in-gnome-3-10/) (CSD), allowing application to paint the window border and buttons. Quoting from the linked site above:

> A disadvantage of CSD is the inconsistency that brings between Apps that support them (mostly GNOME Apps) and Apps that don’t (3rd party Apps, like Firefox). However this is mostly in theory, because in practice, you won’t really be bothered from it.

A new widget called [GtkHeaderBar](https://developer.gnome.org/gtk3/3.10/GtkHeaderBar.html) was added in the process, and it was decided that the GtkHeaderBar will forcibly put the _close button_ in the right, and then [bug 706708](https://bugzilla.gnome.org/show_bug.cgi?id=706708) was filled.

A [fix](https://git.gnome.org/browse/gtk+/commit/?id=54773ba45ba6348cc8c94e7fbab10049fac02884) was commited a month after the bug was filled and it entered in GTK+ 3.10.3. Now I can set the placement of the _close button_ again, so Iet’s create a ~/.config/gtk-3.0/gtk.css with the following content:

GtkWindow {
  -GtkWindow-decoration-button-layout: "close:";
}

This will work for the tweak-tool, or at least they will try to honor the contract. The remaining problem is that they use two GtkHeaderBar, one for the left menu and one for the content. The result is thus kind of strange...

[source](http://mmoya.org/blog/2013/11/16/to-left-or-not-to-left-gnome/)

### Nautilus

Nautilus manually adds the close button at the end of the toolbar and I did not find the time to play with the source code...

coming soon !
