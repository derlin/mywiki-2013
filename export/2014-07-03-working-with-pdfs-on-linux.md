---
title: "Working with pdfs on Linux"
date: "2014-07-03"
categories: 
  - "configuration-tricks"
  - "programs"
---

## cutePDF Editor

To manipulate pdfs less than 5MB, [cutpdf-editor](https://www.cutepdf-editor.com/edit.asp) is really a nive tool.

Everything is made online; you can crop, resize, merge, remove pages, etc. and then save the result on your machine.

## pdftk: a powerful commandline tool

[pdftk (man page)](http://www.pdflabs.com/docs/pdftk-man-page/) is a really powerful tool, which allow us to do pretty everything we want fast and without any restriction in size.

The syntax is always the same :
```bash
pdftk [pdf in] [operation] [page range] output [pdf out]
```

Here is a sample of what it can do:

- remove some pages from in.pdf. cat = concatenate
    ```
    pdftk in.pdf cat 1-5 7 9-21 23 25-33 output out2.pdf
    ```
- rotate odd pages by 90 degrees right (possibilities = north, south, east, west, left, right, or down), keeping even pages:
    ```
    pdftk in.pdf rotate 1-endoddnorth output out.pdf
    ```
- rotate odd pages by 180 degrees left and remove even pages:
    ```
    pdftk in.pdf cat 1-endoddleft output out.pdf
    ```
- collate documents A and B, merging them one page after another (1A-1B-2A-2B...):
    ```
    pdftk A=A.pdf B=B.pdf shuffle A B output out.pdf
    ```
- collate documents A and B, but keep only even pages from B and rotate pages from A:
    ```
    pdftk A=A.pdf B=B.pdf shuffle \
         A1-endright B1-endeven output out.pdf
    ```
- compress a document:
    ```
    pdftk A.pdf output out.pdf compress
    ```
- extract each page of A into pg0001.pdf, pg0002.pdf, etc:
    ```
    pdftk A.pdf burst
    ```