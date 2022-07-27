---
title: "ARM Registers"
date: "2013-10-11"
categories: 
  - "languages"
  - "other"
tags: 
  - "arm"
  - "other"
  - "raspberry-pi"
---

In ARM, 15 registers are at disposal:

- **R0-R3** are used for passing arguments to subroutines; they are thus modified by each call.
- **R4-R9** are working registers : they should normally be preserved between calls. Thus, when writing a subroutine, never forget to back them up before anything else:
  ```arm
  push {R4-R9, LR} /* backup */
  ...
  pop {R4-R9, PC} /* restore and branch at the same time 
  (LR restored in PC) */
  ```
- **R7** is also used to store the address of a syscall to execute
- **R13** points to the stack, **R14** is the link register (holding the return address when calling a subroutine), and **R15** is the programm counter (holding the address of the next instruction).

## Registers complete table

| register | alt. name | function |
| --- | --- | --- |
| r0 | a1 | First function argument / Integer function result Scratch register |
| r1 | a2 | Second function argument Scratch register |
| r2 | a3 | Third function argument Scratch register |
| r3 | a4 | Fourth function argument Scratch register |
| r4 | v1 | Register variable |
| r5 | v2 | Register variable |
| r6 | v3 | Register variable |
| r7 | v4 | Register variable, also used to store the address of a syscall |
| r8 | v5 | Register variable |
| r9 | v6 | Register variable |
| r10 | sl | Stack limit |
| r11 | fp | Argument pointer |
| r12 | ip | Temporary workspace |
| r13 | sp | Stack pointer |
| r14 | lr | Link register Workspace |
| r15 | pc | Program counter |

[source](http://www.exploit-db.com/papers/14143/)
