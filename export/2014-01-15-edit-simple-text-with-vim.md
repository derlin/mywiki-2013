---
title: "edit simple text with vim"
date: "2014-01-15"
---

## Wrapping, indenting

Here are some useful options:

```bash
# stop indenting {,( and such
:set nocindent

# enable spell-checker
:setlocal spell spelllang=en

# auto wrapping
# wrap lines that are too long with "visual" newlines
# rather than adding an actual newline character t
# The 'list' option must be off because it automatically
# disables the 'linebreak' option:
:set wrap nolist inebreak

# width of wrap to 60 chars
:set textwidth=60

# to test, not sure of the impact
:set formatoptions+=n # numbered-list
:set formatoptions+=a #
```

## Vim spell check

```bash
# enable spell-checker
:setlocal spell spelllang=en # or fr, ...
```

To use the french dictionary, download the following files and add them either in `/usr/share/vim/spell` or in your `~/.vim/spell` folder:

```bash
wget http://ftp.vim.org/vim/runtime/spell/fr.latin1.spl
wget http://ftp.vim.org/vim/runtime/spell/fr.latin1.sug
wget http://ftp.vim.org/vim/runtime/spell/fr.utf-8.spl
wget http://ftp.vim.org/vim/runtime/spell/fr.utf-8.sug
```

Note that you can also choose juste the utf8 or the latin1, not both, if you plan to use only one encoding.

To autocorrect a word, use `z=`. To mark a word as correct and update the dictionary, use `zg`; `zw` does the opposite, i.e. mark a word as mispelled.
