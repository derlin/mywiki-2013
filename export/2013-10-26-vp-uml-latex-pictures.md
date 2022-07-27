---
title: "VP uml - latex - pictures"
date: "2013-10-26"
tags: 
  - "latex"
  - "svg"
---

1. clic export active diagram as image SVG
2. install imagemagick
3. `convert -resize 4000x src.svg dest.png` . Le 4000x est en fait la nouvelle largeur (largeur x hauteur). On peut donc mettre 300x300 par ex.

Done !

A noter: ne pas mettre les extensions dans latex pour les includegraphics.
