---
title: "Eclipse customisation"
date: "2014-05-28"
categories: 
  - "configuration-tricks"
  - "programs"
---

Eclipse offers a lot of settings for the colors and stuff. But it is really disappointing when the preview is right, but the real interface is not. The best thing to do to have a clean, nice eclipse is to remove all its css files:

```bash
# eclipse is often in /usr/share or /opt
cd eclipse/plugins/org.eclipse.platform_[your version]
rm *.css
rm -rf css/
```
Et voila.
