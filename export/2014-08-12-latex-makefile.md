---
title: "latex Makefile"
date: "2014-08-12"
categories: 
  - "languages"
  - "other"
---

A makefile is not always useful, but anyway, it is always easier to type `make` than `pdflatex ... truc.tex`.

Another thing is when you want to use custom templates and do not remember the exact options (and path) to pass to the pdflatex command.

So, here is a simple makefile that works pretty well:

```bash
TEX=pdflatex
SRC=$(wildcard *.tex) # change it if you have more than one
                      # .tex file in the current directory
OUT=$(SRC:.tex=.pdf)

OUT_DIR=./out  # if you do not want it, remove ARGS and compile deps
ARGS=-output-directory=$(OUT_DIR) -aux-directory=$(OUT_DIR)

# if your templates are not in your path (like in ~/texmf folder),
# set the TEXINPUTS path like this:
export TEXINPUTS=../TEMPLATES:

# =========================

compile: $(OUT_DIR)
        $(TEX) $(ARGS) $(SRC) &amp;&amp; mv out/$(OUT) .

all: compile clean show

show:
        if [ ! -e $(OUT) ]; then make compile; fi
        xdg-open $(OUT)

# ------------------------

$(OUT_DIR):
        mkdir $(OUT_DIR)

# ------------------------

clean_all: clean
        rm -rf $(OUT)

clean:
        rm -f $(OUT_DIR)

# ------------------------

.PHONY: all clean show
```

[Download the raw source file](../wordpress/wp-content/uploads/2014/08/latex_makefile.txt).

### Notes:

- clean does delete the whole out folder, so the toc will disappear as well. This is one of the reasons I love out directories: the root folder stays clean , so we don't really need to run a clean so often.
- the generated pdf is automatically moved to the root directory.
- the toc problem is one of the reasons I don't specify `$(OUT)` as a compile dependency (or as the target itself): if I do that, it is not possible to compile twice without removing the pdf...
- if all the latex templates are in the default latex path (usually `~/texmf/tex/latex`), setting the `TEXINPUTS` variable is useless. You can safely remove the line.
