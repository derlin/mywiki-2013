---
title: "java setup"
date: "2013-12-18"
---

## using a java installer

### install the oracle 8 installer

Run the following commands:

```bash
sudo add-apt-repository ppa:webupd8team/java
sudo apt-get update
sudo apt-get install oracle-java8-installer
```

Note that there is a similar package for java 7.

### switch between versions

To alternate between versions, use one of the following:

```bash
sudo update-java-alternatives -s java-7-oracle
sudo update-java-alternatives -s java-8-oracle
```

### update your environment

Run the following commands:
```bash
sudo apt-get install oracle-java8-set-default
```
[source](http://www.webupd8.org/2012/09/install-oracle-java-8-in-ubuntu-via-ppa.html)

## Update java and javac

I don't know why, but I ran into troubles in one of my computers because of a mismatch between java and javac.

If you have the same issue, first try those commands:
```bash
sudo update-alternatives --config java
sudo update-alternatives --config javac
```
If one of the versions that is listed in the java is not in the javac (probably an openjdk), ensure that you have both the jre AND the jdk installed. If not, try `sudo apt-get install openjdk-7-jdk` or a similar package.

If by any chance update-alternatives tells you that no java(c) is found, you will need to add them manually:
```bash
# add the path to javac executable and a number from 1 to ..
# the number will be the index in the list of alternatives
sudo update-alternatives --install "/usr/bin/javac" "javac" \
   /opt/java/jdk1.7.0_40/bin/javac 1

# idem for java. If two versions, simply change the number from 1 to 2
sudo update-alternatives --install "/usr/bin/java" "java" \
   /opt/java/jdk1.7.0_40/bin/java 1

# if you made a mistake, remove an entry like this:
sudo update-alternatives --remove "java" /opt/java/jdk1.7.0_40/bin/java
```