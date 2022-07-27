---
title: "CUPS - printer not working"
date: "2014-08-31"
categories: 
  - "configuration-tricks"
  - "material"
---

## The issue

After upgrade, the printer (a Canon MX 360) is still listed, but nothing happens when is try to print a test page. The only hint is the status “stopped” in the print queue window…

The logs show nothing special, no error reported.

## Finding hints

To get more information, go to the web interface of cups: [http://127.0.0.1:631/](http://127.0.0.1:631/) (If you don’t know the port, try running `netstat -tulpn` as root and search for cups) under the administration panel. Clic on your printer and send a job to the print queue.

In my case, I got the error:

> The PPD version (5.2.9) is not compatible with Gutenprint 5.2.10.

Now that we got our hands on the problem, it is easy to find a solution!

## The solution

In this case, the easiest thing to do is to remove the printer and add it again. Cups will automatically search for (and find) the latest ppd. Problem solved.
