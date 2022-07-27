---
title: "C development tricks"
date: "2014-01-03"
categories: 
  - "c"
  - "languages"
tags: 
  - "c"
  - "linux"
  - "utilities"
---

## Debug and stuff

- run only the preprocessor: `gcc -std=c99 -E source.c`

## GDB

- print the type of a variable:  `ptype var` or `whatis var`
- change a variable's value:  `set var=value`
- use an internal variable, for example to iterate throught a linked list:
    ```bash
    # with an array
    (gdb) set $p = the_array
    (gdb) p *($p++)
    (gdb) # enter to repeat the last command
    # with a linked list
    (gdb) define do_iter
    > p $p->value
    > set $p = $p->next
    > end
    (gdb) set $p = list->head
    (gdb) do_iter
    (gdb) # press enter until the last elt
    ```
