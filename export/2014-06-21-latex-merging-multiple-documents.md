---
title: "Latex, merging multiple documents"
date: "2014-06-21"
categories: 
  - "languages"
  - "other"
tags: 
  - "latex"
---

How to integrate multiple latex files into one document
-------------------------------------------------------

### The structure

Personally, I do prefer having different directories for source and output files. The use of parts will increase the number of .ptc, etc. making it quite messy...
```bash
# Structure
tex/:
   part/:
      part1.tex
      part2.tex
      ...
   resources/:
      image1.png
      image2.pdf
      ...

   out/:

   main.tex
```
# pdflatex command

```bash
pdflatex -output-directory=./out -aux-directory=./out main.tex
```

### Including parts

We have the choice between `\include` and `\input`. The latter is easier and less messy for this purpose, since it just copy-paste the latex code into the main document before compiling. Thus, the parts should all be chapters, sections and text: no heading, no begindocument or class. Only the main template will have those.
```latex
    \input{part/part1} % note that the extension is not necessary
```
### TOC

To have a proper table of content, you need to use the minitoc package.
```latex
\usepackage{minitoc}
\usepackage{bookmark} % to fix hyperref's toc indentation
\newcommand{\partwithtoc}[1]{\part{#1} \parttoc{} }

%... and in the document
\partwithtoc{My part 1}
\input{part/part1}
\bookmarksetup{startatroot} % it marks the end of the part
                   % to have a correct document hierarchy
```

If you have trouble with the spacing between numbering and heading in TOC, you can use the package tocloft:
```latex
\usepackage{tocloft}
\addtolength{\cftchapnumwidth}{.5em}
\addtolength{\cftsecnumwidth}{.5em}
\addtolength{\cftsubsecnumwidth}{.5em}
```

### Reset chapter counter for each part

The problem when we want to start chapters numbering at 1 for each part is that hyperref will now have multiple 1. ..., which it does not handle. You will have your bookmarks totally messed up. To avoid that, the simplest thing to do is prefixing the chapter by the part number:

```latex
\usepackage{chngcntr}
\counterwithin{chapter}{part}

% and if we don't want roman numbering:
\renewcommand{\thepart}{\Alph{part}}
```