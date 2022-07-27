---
title: "Bash tips and tricks"
date: "2013-10-30"
categories: 
  - "bash"
---

## Difference between $@ and $\*

Nothing is better than an example. Given the following code:

```bash
#!/bin/bash

echo -e "\n"'using $@'
for i in $@; do
    echo " -- $i"
done

echo -e "\n"'using $*'
for i in $*; do
    echo " -- $i"
done

echo -e "\n"'using "$@"'
for i in "$@"; do
    echo " -- $i"
done

echo -e "\n"'using "$*"'
for i in "$*"; do
    echo " -- $i"
done
```

If I call the script with the command:
```bash
./bash_test.sh normal_arg "arg with spaces"
```

I get the following output:
```text
using $@
 -- normal_arg
 -- arg
 -- with
 -- spaces

using $*
 -- normal_arg
 -- arg
 -- with
 -- spaces

using "$@"
 -- normal_arg
 -- arg with spaces

using "$*"
 -- normal_arg arg with spaces
```

Nice uh ?
