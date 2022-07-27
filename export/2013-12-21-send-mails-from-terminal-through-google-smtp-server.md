---
title: "Send mails from terminal through google smtp server"
date: "2013-12-21"
---

###### Authentication

We will need to authenticate with a valid gmail username before proceeding. For that, we use the smtp command `auth` , as long as the server supports it.

To check the server capability, use `ehlo` instead of the classic `helo`, which will display the list of the server capabilities.

If the list contains the keyword **AUTH PLAIN**, it means that we can authenticate using a simple string base64-encoded.

The easiest way to generate this string is to use perl:

```bash
> perl -MMIME::Base64 -e 'print encode_base64("lucy.derlin\\@gmail.com\\0lucy.derlin\\@gmail.com\\0motdepasse")';
bHVjeS5kZXJsaW5AZ21haWwuY29tAGx1Y3kuZGVybGluQGdtYWlsLmNvbQBDQUNBcHJvdXQh
```
Notice the escape character before the @, as well as the null characters (\\0) used as fields separators. They are really important !

###### Actually send a mail

Since the server uses SSL, it is a good idea to use `openssl` directly.

```bash
# connect to gmail smtp server through openssl
> openssl s_client -crlf -connect smtp.gmail.com:465 
220 mx.google.com ESMTP y10sm59601148eev.3 - gsmtp

# send helo and list the capabilities of the server
ehlo 
250-mx.google.com at your service, \[77.56.233.245\]
250-SIZE 35882577
250-8BITMIME
250-AUTH LOGIN PLAIN XOAUTH XOAUTH2  # AUTH PLAIN present !
250 ENHANCEDSTATUSCODES

# send the base64-encoded credentials 
auth plain bHVjeS5kZXJsaW5AZ21haWwuY29tAGx1Y3kuZGVybGluQGdtYWlsLmNvbQBDQUNBcHJvdXQh
235 2.7.0 Accepted

# set the expeditor (from) -- don't forget the < >
mail from: 
250 2.1.0 OK y10sm59601148eev.3 - gsmtp

# set the recipient -- don't forget the < >
rcpt to: 
250 2.1.5 OK y10sm59601148eev.3 - gsmtp

# begin the message with data and end it with 
# a newline + a dot
data
354  Go ahead y10sm59601148eev.3 - gsmtp
# notice how we add meta info inside the data
Subject: test	
From: lucy linder 
To: me 
Date: 30 May 2013 03:40:31 -0700 (PDT)

Hello !	# the actual message

.       # newline + dot => end of data
250 2.0.0 OK 1369912614 y10sm59601148eev.3 - gsmtp
```
And we are done ! The message is sent.

The source code of the message we just sent is of this form:
```text
Return-Path: 
Received: from  (77-56-233-245.dclient.hispeed.ch. \[77.56.233.245\])
		by mx.google.com with ESMTPSA id y10sm59601148eev.3.2013.05.30.04.15.20
		for 
		(version=TLSv1.1 cipher=ECDHE-RSA-RC4-SHA bits=128/128);
		Thu, 30 May 2013 04:16:54 -0700 (PDT)
Message-ID: <51a73526.0a2c0f0a.2cee.ffffbc84@mx.google.com>
Subject: test
From: lucy linder 
To: me 
Date: Thu, 30 May 2013 04:16:54 -0700 (PDT)

Hello !
```

[further reading](http://qmail.jms1.net/test-auth.shtml)
