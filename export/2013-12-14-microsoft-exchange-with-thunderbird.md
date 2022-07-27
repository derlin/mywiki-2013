---
title: "Microsoft Exchange through Thunderbird"
date: "2013-12-14"
categories: 
  - "configuration-tricks"
  - "eia"
  - "programs"
---

Using thunderbird instead of the common webmail of the hefr is really convenient, except when we need to consult the address book or the calendar. Here, no other possibility that to open the web browser. Or is it ?

To be able to use the owa server exchange from Thunderbird, we need a middleware: DAVmail. Here is the setup procedure.

## Installing DAVmail

1. go to the [davmail sourceforge page](http://sourceforge.net/projects/davmail/files/davmail/) and download the last deb package
2. install and start it:
    ```bash
    sudo dpkg -i devmail.deb
    sudo apt-get install -f
    davmail &
    ```
    
3. in the window that appears, change only the URL OWA (Exchange) to `https://webmail.hefr.ch/owa`

To be sure it works, try `sudo netstat -tulpn`, you should see a java program listening on the 1143 port.

## Configuring thunderbird

1. create a new account: _Edit > Account Settings > Account Actions > Add new mail account_
    - In the first window, give your edut.hefr email address and password
    - In the second window, click on _manual config_ and change the following:
        ```text
        Incoming IMAP localhost 1143 none normalpassword
        Outgoing SMTP localhost 1025 none normalpassword
        ```
        
2. click on re-test. If the configuration seems incorrect, check that davmail is running.
3. if a security popup arise, don't panic: the credentials are transmitted unencrypted between davmail and thunderbird (local only), but the rest of the communications are encrypted via https

## Adding the address book

1. add a new LDAP directory: _Tools > Address Book > File... > New LDAP directory_
2. Fill the following fields:

    | Name              | Hostname  | Base DN   | Port number | Bind DN           |
    |-------------------|-----------|-----------|-------------|-------------------|
    | whatever you want | localhost | OU=people | 1389        | firstname.surname |
    
3. Test it: try to type a name in the search field

**Warning!** the contacts do not appear in the list, due to a bug of thunderbird, but normally, the names appear after entering a search string and the completion works perfectly if you thought of checking _Directory server_ in the _Edit > Preferences > Composition > Address autocompletion_ settings.

## Init script for DavMail

To make it work as a daemon, just copy the following file to the `/etc/init.d` directory, ensure that it has the right permissions (755) and run `sudo update-rc.d davmail defaults`.

```bash
#!/bin/sh
#
# davmail:  davmail exchange gateway daemon
#
# chkconfig:    345 98 02
# description:  DavMail gateway for Microsoft Exchange
# processname:  davmail
# config:   /etc/davmail.properties
# LSB init-info
### BEGIN INIT INFO
# Provides:          davmail
# Required-Start:    $network
# Required-Stop:     $network
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: DavMail gateway for Microsoft Exchange
### END INIT INFO

PROG_NAME=DavMail
# Source function library.
if [ -e /etc/init.d/functions ]; then
    . /etc/init.d/functions
fi
# LSB functions
. /lib/lsb/init-functions

# Check that networking is configured.
[ "${NETWORKING}" = "no" ] && exit 0
DAVMAIL_CONF=/home/lucy/.davmail.properties
test -f $DAVMAIL_CONF || exit 4

DAVMAIL_HOME=/usr/share/davmail #/var/lib/davmail
test -d $DAVMAIL_HOME || exit 5


LOGFILE=/home/lucy/davmail.log
PIDFILE=/var/run/davmail.pid
LOCKFILE=/var/lock/davmail

start() {
    echo "Starting $PROG_NAME gateway... "
    dostatus > /dev/null 2>&1
    if [ $RETVAL -eq 0 ]
    then
        log_failure_msg "$PROG_NAME gateway already running"
        RETVAL=1
        return
    fi
    #runuser - davmail -s /bin/sh -c "exec nohup $DAVMAIL_HOME/davmail $DAVMAIL_CONF >> $LOGFILE 2>&1 &"
    nohup /usr/bin/davmail $DAVMAIL_CONF >> $LOGFILE 2>&1 &
    RETVAL=$?

    if [ $RETVAL -eq 0 ]
    then
        sleep 1
        echo $(pgrep -f 'java.*davmail') > $PIDFILE
        touch $LOCKFILE
        log_success_msg "$PROG_NAME started."
    else
        log_failure_msg "Could not start $PROG_NAME."
    fi

    return $RETVAL
}

stop() {
    echo "Shutting down $PROG_NAME gateway..."
    kill $(cat $PIDFILE 2>/dev/null) > /dev/null 2>&1
    RETVAL=$?
    sleep 1
    if [ $RETVAL -eq 0 ]
    then
        rm -f $PIDFILE $LOCKFILE
        log_success_msg "$PROG_NAME stopped."
    else
        log_failure_msg "$PROG_NAME could not be stopped"
    fi
    return $RETVAL
}

restart() {
    if [ ! -f $PIDFILE ]; then
        log_failure_msg "$PROG_NAME not running"
        RETVAL=1
        return
    fi

    stop
    start
}

condrestart() {
    [ -f $LOCKFILE ] && restart || :
}

dostatus() {
    kill -0 $(cat $PIDFILE 2>/dev/null) > /dev/null 2>&1
    RETVAL=$?
    if [ $RETVAL -eq 0 ]
    then
        echo "$PROG_NAME gateway (pid $(cat $PIDFILE 2>/dev/null)) is running..."
    else
        if [ -f $PIDFILE ]
        then
            echo "$PROG_NAME gateway dead but pid file exists"
            RETVAL=1
            return
        fi
        if [ -f $LOCKFILE ]
        then
            echo "$PROG_NAME gateway dead but subsys locked"
            RETVAL=2
            return
        fi
        echo "$PROG_NAME gateway is stopped"
        RETVAL=3
    fi
}

# See how we were called.
case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  status)
    dostatus
    ;;
  restart|reload)
    restart
    ;;
  condrestart)
    condrestart
    ;;
  *)
    echo $"Usage: $0 {start|stop|status|restart|reload|condrestart}"
    exit 1
esac

exit $RETVAL
```
