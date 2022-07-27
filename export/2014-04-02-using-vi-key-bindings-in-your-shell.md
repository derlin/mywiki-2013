---
title: "Using vi key bindings in your shell"
date: "2014-04-02"
categories: 
  - "configuration-tricks"
  - "env"
tags: 
  - "bash-2"
  - "customization"
  - "linux"
  - "utilities"
  - "zsh"
---

By default, most shells use Emacs style key bindings for commandline, like ctrl+A to go at the beginning of a line or ctrl+k to clear words following the cursor.

But with a simple option, it is possible to switch to vi style:

bash:`set -o vi`

zsh:`bindkey -v`

The whole set of available commands can be found with `bindkey -a`.

To switch to Emacs mode, the option is `-e`.

to find out which keycode corresponds to what, simply type `cat` in your terminal. You can then press any key and its code will be displayed.

## Troubles with delete key on zsh

Simply add the following _at the end_ of your `~/.zshrc`:

```bash
# to correct vi mode annoying behavior...
bindkey "^[[1;6C" delete-word
bindkey "^[[3~"   delete-char
```

The following could also be useful, since we are at it:
```bash
# use ctrl+arrow to move between words,
#     ctrl+shift+arrow to delete words
bindkey "^[[1;5D" .backward-word
bindkey "^[[1;5C" .forward-word
bindkey "^[[1;6D" backward-delete-word
```