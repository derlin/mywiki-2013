---
title: "Vim tricks"
date: "2014-05-17"
categories: 
  - "configuration-tricks"
  - "programs"
tags: 
  - "vim"
---

## Moving across windows

To facilitate the use of multiple windows, you can easily remap the wincmd to your favorite key combos. For me, it is space and then the arrow:

```
nmap :wincmd k
nmap :wincmd j
nmap :wincmd h
nmap :wincmd l
```

You can also use the built-in `w [arrow]` To open a new window: `sp [h|v]`, for horizontal or vertical.

## Misc

**Command**

**Action**

| Command         | Action                                                                      |
|------------------|-----------------------------------------------------------------------------|
| `.`              | repeat the last change made in normal mode                                  |
| `z=`             | open a list of suggestions when the cursor is on a mispelled word           |
| `:set nocindent` | stop indenting text after (,{, etc. Useful when you are editing simple text |
| `*`              | go to the next occurrence of the word under the cursor                      |
| `#`              | go to the previous occurrence of the word under the cursor                  |
| `yiw|viw`        | yank\|select the word under the cursor                                      |
| `shift+arrow`    | jump a whole word with the cursor                                           |

## Vim infos

**Command**

**Action**

| Command                    | Action                                               |
|------------------------------|------------------------------------------------------|
| `:set variablename?`         | show the value of a variable                         |
| `:verbose set variablename?` | show the value of a variable and where it is defined |

Vimrc
-----

### Backups

To avoid the .backups polluting your filesystem, you can group all the backup files (ending with ~) to one directory with those lines:

```
set backupdir=~/.vim/backup//,~/tmp//,~/
set backup
set directory=~/.vim/backup//,~/tmp//,~/
set noswapfile
```

This means that the backups will be written in your home directory. If the latter is not writeable, tmp will be used, etc. If it does not work, check where the backupdir variable is set using `:verbose set backupdir?`
