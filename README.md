# MyWiki

This wordpress site is a remnant of a wiki tentative back in **2013 - early 2014**...
(I just discovered computers, hardly one year in).

There are are bunch of articles on perl, archlinux, latex, bumblebee (making Optimus work on Linux), etc.

The posts are also available in markdown, see the [export/](export) folder.

**IMPORTANT**: do NOT try to update wordpress, as the code blocks will be completely messed up !!!

## How to

1. run `docker-compose up`
2. go to http://localhost
3. login using `chief`:`chief`

## Preview

![home](screenshots/home.png)
![menu open: configuration-tricks](screenshots/menu-configuration-tricks.png)
![post: c strings](screenshots/post-c-strings.png)
![post: perl-makefile](screenshots/post-perl-makefile.png)
![footer](screenshots/footer.png)

## Export to markdown

```bash
cd /tmp
mkdir lala
cd lala
# rom WordPress, Tools > Export > export posts => save to /tmp/lala/export.xml

git clone git@github.com:lonekorean/wordpress-export-to-markdown.git
docker run --rm -it --name lala -v $PWD:/app node:15 bash

# inside the container
cd /app/wordpress-export-to-markdown
npm install
node index.js --input ../export.xml
```

Then, most code blocks need to be fixed manually...