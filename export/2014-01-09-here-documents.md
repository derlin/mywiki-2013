---
title: "Here documents"
date: "2014-01-09"
categories: 
  - "bash"
  - "languages"
---

## What is a here document

A here document uses I/O redirection tricks to feed a command expecting a file without actually creating one. The purpose is thus to avoid temp files and still use `cat, ftp,` etc.

## Usage

### A simple example

A simple Here document:
```bash
# use cat (file), not echo (chars) !!
cat <<UNIQ_SEQUENCE  
    Hi, I am a here document
a second line
            a third
UNIQ_SEQUENCE  # end of doc
```
So, we use the `<<` symbol directly followed by a **unique string** (not used anywhere in the here doc). The same unique string **at the beginning of the line** marks the end of the document.

### Operator variations

```bash
aVar=value
 cat <<LALA
    with a tab $aVar
LALA
# will print "   with a tab value"

# using the operator <<- instead of <<,
# leading tabs (not spaces) are wiped off
cat <<-LALA
    with a tab
LALA
# will print "with a tab value"

# prevent variable substitution by enclosing
# the string symbol by single or double quotes
cat <<'LALA'
    $aVar not substituted
LALA
# will print "$aVar not substituted"

# with multiple redirections:
f=/path/to/file; cat > $f <<<LALA
new content of file
LALA
```

### Usages

Use here documents to create simple self-extracted archives:

```bash
#!/bin/bash
filename=sh$$.arch

if [ -z "$1" ]; then
    echo "Usage $0 [file, <files..>]"
    exit 1
fi

if [ "$1" == "-n" ]; then
    [ -z "$3" ] && echo "-n requires an argument" && exit 1
    filename="$2"
    shift 2
fi

echo '#!/bin/bash' >> "$filename"

while [ -n "$1" ]; do

    f="${1##.*/}" # get the filename

    # ensure that the file does not already exist before extracting it
    echo "if [ -e \"$f\" ]; then echo 'The file $f already exists. Skipping'; else " >> "$filename"
    # add a char to the beginnig of each line to escape the
    # here string
    echo "sed 's/^X//' > \"$f\" <<'miamlecacacestdelicieux'" >> "$filename"
    # don't forget to remove the escape char during the extraction
    cat "$1" | sed 's/^/X/' >> "$filename"
    echo "miamlecacacestdelicieux" >> "$filename"
    echo "fi" >> "$filename"
    shift
done
```

Comment out easily a block of codes by using `: <<...`. The ":" are mandatory !
Or do a self-explanatory shell script:

```bash
if [ "$1" = "-h" ]     # Request help.
then
  echo; echo "Usage: $0 [directory-name]"; echo
  sed --silent -e '/DOCUMENTATIONXX$/,/^DOCUMENTATIONXX$/p' "$0" |
  sed -e '/DOCUMENTATIONXX$/d'; exit $DOC_REQUEST; fi

: <<DOCUMENTATIONXX
List the statistics of a specified directory in tabular format.
---------------------------------------------------------------
The command line parameter gives the directory to be listed.
If no directory specified or directory specified cannot be read,
then list the current working directory.

DOCUMENTATIONXX
```

## Here strings

Here strings are quite similar to here documents and allow a short string to be treated as a file by the interactive program. For example, `tr` only accepts files:

```bash
tr a-z A-Z <<< string # yields STRING

tr a-z A-Z <<< 'string with
spaces and such'
# is equivalent to :
echo 'string with
spaces and such' | tr a-z A-Z

# with double quotes, variable substitution occurs:
tr a-z A-Z <<< "your path is:
$PATH"
```