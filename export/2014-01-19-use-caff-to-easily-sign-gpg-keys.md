---
title: "Use CAFF to easily sign GPG keys"
date: "2014-01-19"
categories: 
  - "configuration-tricks"
---

`caff` is a rather easy-to-use perl program which automates the key signature process. It will fetch the key, sign it, send it by email and remove it from your keyring by a simple command.

But to be able to use it, you first need to configure your environment properly.

## Installing caff

The caff utility is part of the `signing-party` package:
```bash
sudo apt-get install signing-party
```
During the install, you will see some prompts: just let the defaults.

## Configuring caff

You will need a `~/.caffrc` file.  
You can either copy the sample configuration file from `/usr/share/doc/signing-party/caff/caffrc.sample` or just start anew.

Add the following to it:

```perl
# your name and email
$CONFIG{'owner'} = 'Firstname Lastname';
$CONFIG{'email'} = 'username@truc.com';

# the long id of your key
$CONFIG{'keyid'}       = [ qw{A534CC879DF02B77} ];
# home (default)
$CONFIG{'caffhome'}    = $ENV{'HOME'}.'/.caff';

# the body of the mail to send
$CONFIG{'mail-template'} = <<'EOM'
<span class="nocode">
Hi,

please find attached the user id{(scalar @uids >= 2 ? 's' : '')}
{foreach $uid (@uids) {
    $OUT .= "t".$uid."n";
};}of your key {$key} signed by me.

Note that I did not upload your key to any keyservers.
If you have multiple user ids, I sent the signature for each user id
separately to that user id's associated email address. You can import
the signatures by running each through `gpg --import`.

If you want this new signature to be available to others, please
upload it yourself. With GnuPG this can be done using
    gpg --keyserver pool.sks-keyservers.net --send-key {$key}

If you have any questions, don't hesitate to ask.

Regards,
{$owner}
EOM
</span>
```

For the caff part, that's it !

## Configuring the mail server (gmail)

In my case, I did not want to set up a full featured mail server just to send a bunch of emails.  
Fortunately, the package `sSMTP` provides an extremely simple SMTP server which allows your desktop to send emails. Here, we will configure it to use Gmail.

First, install the package via:
```bash
sudo apt-get install ssmtp
```
Then, edit the file `/etc/ssmtp/ssmtp.conf` and add the following:
```
root=username@gmail.com
mailhub=smtp.gmail.com:587
rewriteDomain=
hostname=username@gmail.com
UseSTARTTLS=YES
AuthUser=username
AuthPass=password
FromLineOverride=YES
```
This will tell smtp to use those settings when sending mails through username@gmail.com. The last thing to do is to tell smtp to bind your local username to this email address. For that, open `/etc/ssmtp/revaliases` and add one line for each local user:
```
root:username@gmail.com:smtp.gmail.com:587
localusername:username@gmail.com:smtp.gmail.com:587
```
To test your ssmtp configuration, open a terminal and type:
```bash
ssmtp recipient@truc.com
Type the body of the email and 
finish by adding a newline and pressing 
CTRL+D
```

## Sign a key with caff

Now that everything is configured, we can sign keys. In the terminal, just type:
```bash
caff <long-id of the key to sign>
```
Note that you can specify multiple keys at once.
