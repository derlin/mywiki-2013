---
title: "Perl and the f***ing arrows in debug mode"
date: "2014-02-11"
categories: 
  - "perl"
---

Everytime it is the same story: `perl -d` works when I use the perl in /usr/bin, but from the perlbrew one, I always get the `[[A` and so forth when using arrows.

So, here is the trick:
```bash
# for the whole system, but does not 
# install the dev files !
sudo apt-get install libterm-readline-gnu-perl libterm-readkey-perl

# for perlbrew ReadLine module compilation to work
sudo apt-get install libreadline-dev libncurses-dev

# finally, install the module
cpanm Term::ReadLine::Gnu
# note: if cpanm is not installed, run
# perlbrew install-cpanm
```