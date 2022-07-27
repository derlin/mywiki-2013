---
title: "C misc notes"
date: "2014-12-17"
categories: 
  - "c"
  - "languages"
---

## Opening files with a system call

```c
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

int main(){
    int i = 0;
    for(; i < 2; i++)
    {
        int f = open("f", O_WRONLY|O_CREAT, 0666);
        write(f, "aaa", 3);
        close(f);
        int f2 = open("f", O_WRONLY|O_APPEND, 0666);
        write(f2, "bb",2);
        close(f2);
    }
}
```

At the end of this script, the result of `cat` will be aaabbbb and note aaabb! Why ? simply because the flag O\_CREATE will create the file if not exists and place the cursor to the beginning. O\_APPEND will place the cursor at the end. The content of the file is not erased !

To really create or overwrite a file (like the shell behavior of >), use the flags `O_CREAT|O_TRUNC`.

## The strange behavior of recursive main

The code:
```c
int main() { 
    main(); 
    return 0;
}
```

should result in a stack overflow error. But the system will output a segfault... Why ? No idea!

An interesting thing is that the version:
```c
int main() { 
    return main(); 
}
```

Will be a simple infinite loop. The reason is that it is a tail recursion, so the compiler will optimize it with a loop.
