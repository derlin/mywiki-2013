---
title: "Latex tips and tricks"
date: "2014-07-03"
categories: 
  - "configuration-tricks"
  - "languages"
  - "other"
  - "programs"
---

## Force the placement of a graphic

```latex
\begin{figure*}[h]
    \begin{center}
        \includegraphics[width=.9\textwidth]{my-image}
        \caption{this is a caption}
    \end{center}
\end{figure*}
```

## Expand margins


Normally, margins are set in the directive:

```latex
\usepackage[tmargin=2.2cm,bmargin=2.2cm,lmargin=1.5cm,rmargin=1.5cm]{geometry}
```

Unfortunately, this is often done in the document template, thus not modifiable as we want. Here is an alternative. Add those lines in the preamble:
```latex
\addtolength{\oddsidemargin}{1cm}
\addtolength{\evensidemargin}{1cm}
\addtolength{\textwidth}{-2cm}
```

## Specify the graphics path

Add this line at the top of the preamble:

```latex
\graphicspath{{resources/}}
```

where resources is the name of the folder. Notice the **double brackets**: without it, nothing works.

## Inputenc error with lstlistings


Just pass the following option to `lst[input]listing`:

```bash
inputenc=latin1
```
