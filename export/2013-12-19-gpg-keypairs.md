---
title: "GPG keypairs"
date: "2013-12-19"
categories: 
  - "configuration-tricks"
  - "programs"
tags: 
  - "gnu"
  - "linux"
  - "security"
---

## Create a master keypair and subkeys


### Create the master key

```text
> gpg --gen-key

Please select what kind of key you want:
    (1) RSA and RSA (default)
    (2) DSA and Elgamal
    (3) DSA (sign only)
    (4) RSA (sign only)
Your selection? 1
...
RSA keys may be between 1024 and 4096 bits long.
What keysize do you want? (2048) 4096
...
Please specify how long the key should be valid.
    0 = key does not expire
    = key expires in n days
    w = key expires in n weeks
    m = key expires in n months
    y = key expires in n years
Key is valid for? (0) 0
Key does not expire at all
Is this correct? (y/N) y
```
You need a user ID to identify your key; the software constructs the user ID
from the Real Name, Comment and E-mail Address in this form:
    "Heinrich Heine (Der Dichter)<heinrichh@duesseldorf.de>"

Real name: Lala Lulu
E-mail address: lala@truc.org
Comment:
You selected this USER-ID:
    "Lala Lulu <lala@truc.org>"

Change (N)ame, (C)omment, (E)-mail or (O)kay/(Q)uit? o
You need a Passphrase to protect your secret key.
<your passphrase, should be long>
```

### Set some options
```text
> gpg --edit-key lala@truc.org
gpg> setpref SHA512 SHA384 SHA256 SHA224 AES256 AES192 AES CAST5 ZLIB BZIP2 ZIP Uncompressed
gpg> save
```
**Note**: to avoid setting the preferences each time you create a key, you can set them directly in your `~/.gnupg/gpg.conf` file. See the [configuration file section](#conf-file) for more information.

### Create a set of subkeys

```text
> gpg --edit-key lala@truc.org
gpg> addkey
...
Please select what kind of key you want:
    (3) DSA (sign only)
    (4) RSA (sign only)
    (5) Elgamal (encrypt only)
    (6) RSA (encrypt only)
Your selection? 4
...
RSA keys may be between 1024 and 4096 bits long.
What keysize do you want? (2048) 4096
...
Key is valid for? (0) 0
Key does not expire at all
Is this correct? (y/N) y
Really create? (y/N) y
```

### Export the keys to servers

You can use either the master key or the subkey as argument to the export command, the result is the same. The easiest way is to modify your configuration file, `~/.gunpg/gpg.conf` and add the following (or copy-paste the lines in the [configuration file section](config-file)):
```text
use-agent
keyserver hkps://hkps.pool.sks-keyservers.net
keyserver-options ca-cert-file=/home/.../.gnupg/sks-keyservers.netCA.pem
keyserver-options no-honor-keyserver-url
keyserver-options auto-key-retrieve
```

In my case, I had to download the certificate `sks-keyservers.netCA.pem` of the keyserver and add it to my home, not `/etc/ssl/cert` (so I don't need to be root to run the command). Then, you just need to run the following command:

```bash
> gpg2 --send-keys 0xDD580A819CD4B746
```
where the keyid is found in the first line of the output of `gpg -K`:

```text
> gpg -K
/home/.../.gnupg/secring.gpg
-----------------------------
sec#  4096R/0x488BA441  # <----- this one, beginning with 0x
```
**Warning!** If you run into troubles with the command gpg --send-key (protocol error), this is because only **gpg2** supports ssl. So, launch the latter command with **gpg2** !!

### Export all the important stuff and clean your keyring

To avoid keeping sensible data on your laptop, it is recommended to export at least the revocation certificate to a safer place. A good practice is also to keep the master keypair somewhere safe, importing it only when we need to change it (or sign other keys). Here, we will export everything, clear our keyring and re-import the subkey only.

```text
> gpg --output \
     lala@truc.org.gpg-revocation-certificate \
     --gen-revoke lala@truc.org

> gpg --export-secret-keys --armor \
    lala@truc.org.private.gpg-key > lala@truc.org

> gpg --export --armor \
    lala@truc.org.public.gpg-key > lala@truc.org

> gpg --export-secret-subkeys \
    lala@truc.org.subkeys > lala@truc.org

> mv lala@truc.org.* /somewhere/secure/

> gpg --delete-secret-key lala@truc.org

> gpg --import \
    /somewhere/secure/lala@truc.org.subkeys
```

To verify everything went fine, run the following command:
```text
> gpg -K
/home/.../.gnupg/secring.gpg
-----------------------------
sec#  4096R/0x488BA441 2013-03-13
uid                    Lala Lulu <lala@truc.org>
ssb   4096R/0x69B0EA85 2013-03-13
ssb   4096R/0xC24C2CDA 2013-03-13
```

Notice the #, which is the sign that we are now using a subkey.

## Configuration file


OpenPGP best pratices recommends us to use a set of preferences for all our keys. Instead of using the `setpref` command of gpg each time, it is better to copy-paste them once and for all. To set the default settings, open the file `~/.gnupg/gpg.conf` with your favorite editor and copy-paste the following (you can delete everything else, except the lines about the keyservers):
```bash
# when outputting certificates, view user IDs distinctly from keys:
fixed-list-mode
# short-keyids are trivially spoofed; it's easy to create a long-keyid collision; if you care about strong key identifiers, you always want to see the fingerprint:
keyid-format 0xlong
fingerprint
# when multiple digests are supported by all recipients, choose the strongest one:
personal-digest-preferences SHA512 SHA384 SHA256 SHA224
# preferences chosen for new keys should prioritize stronger algorithms:
default-preference-list SHA512 SHA384 SHA256 SHA224 AES256 AES192 AES CAST5 BZIP2 ZLIB ZIP Uncompressed
# If you use a graphical environment (and even if you don't) you should be using an agent:
# (similar arguments as  https://www.debian-administration.org/users/dkg/weblog/64)
use-agent
# You should always know at a glance which User IDs gpg thinks are legitimately bound to the keys in your keyring:
verify-options show-uid-validity
list-options show-uid-validity
# include an unambiguous indicator of which key made a signature:
# (see http://thread.gmane.org/gmane.mail.notmuch.general/3721/focus=7234)
sig-notation issuer-fpr@notations.openpgp.fifthhorseman.net=%g
# when making an OpenPGP certification, use a stronger digest than the default SHA1:
cert-digest-algo SHA256
```

Read more on best practices [here](https://we.riseup.net/riseuplabs+paow/openpgp-best-practices#update-your-gpg-defaults).

## Signatures and trust

### How PGP web of trust works

Unlike PKI insfrastructures, PGP is based on a _decentralised trust model_. As Phil Zimmermann, creator of PGP, pointed out:

As time goes on, you will accumulate keys from other people that you may want to designate as trusted introducers. Everyone else will each choose their own trusted introducers. And everyone will gradually accumulate and distribute with their key a collection of certifying signatures from other people, with the expectation that anyone receiving it will trust at least one or two of the signatures. This will cause the emergence of a decentralized fault-tolerant web of confidence for all public keys.

Since there is no centralised authority to tell which key we can trust, it is our responsibility to decide which key are trustworthy. But how ? There are basically two ways.  

The first set of keys we can trust are the ones we personally checked. They normally belong to friends or people we physically met.

Of course, if a physical encounter was the only way, PGP would be pretty limited. This is why PGP introduced the concepts of _signature_ (counter-signature actually) and _web of trust_. Here is how it works: for each people you physically checked, you add a signature to their public key. This allow the rest of the world know you trust it belongs to the right person and to add a node to your chain of trust.

People you signed may be of two types: **partially trusted** versus **fully trusted** introducers. When you download the key of someone you don't really know, you will use those introducers to decide wether or not you trust the key. The actual parameters may differ for each individual, but normally the **vote counting scheme** is as follow: a key is trusted when at least one fully trusted introducer and/or three partially trusted introducers have signed the key.

So in short, \[Open\]PGP is based on two principles: identity checks - verifying the link between a key and its owner - and trust - who you trust to check other's people identity. Those principles are gathered in the action of signing other people's keys. The more keys you sign, the greater your web of trust and the simpler it gets to trust a new key.

## GPG: signatures, trust signatures and certificates


With gpg, **signing** a key is usually rather straighforward: either you sign a key or you don't. By appending your signature to a key, you let other people know that you performed a rough identity check. Unfortunately, this does not really give any information about trust at all...

Actually, the OpenPGP standard also supports more advanced features, called **level certification** and **trust signatures**, which are \[unfortunately?\] disabled by default in GnuPG.

##### Certification level

Allows you to indicate how carefully you checked someone's identity. To enable it, use the option `--ask-cert-level` or add `ask-cert-level` in your default configuration file:

```bash
> gpg --sign-key --ask-cert-level <id or email>
```

GnuPG let you choose between four levels:

0.  no indication
1.  personal beliefs, but no real check. This may be useful for a "persona" verification, where you sign the key of a pseudonymous user.
2.  casual checking
3.  extensive check

##### Trust level

Allows you to indicate how much you trust someone and its ability to check other's identity (partially or fully trustet introducers).

```bash
# in "edit-key" mode, use tsign instread of sign
> gpg --edit-key <id or email>
gpg> tsign # instead of sign
```
When issuing a trust signature, gpg will ask you three questions:

1.  how far you trust this user to correctly verify other users' keys
2.  depth of the trust signature: specifies how far you allow him to make trust signatures on your behalf. As I understood, this allows building _extended chains of trust_ and _pyramidal certification systems_: a depth 0 corresponds to the classic web of trust as described earlier. Setting a depth of 1 will allow the other to issue level 0 signatures on your behalf; it is thus comparable to the certificate authority trust model ([example](http://www.gossamer-threads.com/lists/gnupg/users/44058)). Finally, a depth of 2 is similar to "_the trust assumption users must rely on whenever they use the default certificate authority list (like those included in web browsers); it allows the owner of the key to make other keys certificate authorities_"; this is like hierarchical webs of trust ([source](http://en.wikipedia.org/wiki/Pretty_Good_Privacy))
3.  enter a domain to restrict this signature: I could not grab the meaning or utility of this one so far...

Those features are interesting since they add valuable informations to one's signatures, but they also carry some security risks, since they tend to leak more information about personal relationships (which would explain why they are disabled by default).

[Further reading](http://tanguy.ortolo.eu/blog/article9/pgp-signature-infos)

## Signature/Certification process

**prequisite** be sure that you have your master key in your keyring (you can import it with `gpg --import /path/to/mymaster.private.gpg-keys`).

### Sign the key of someone else

Signing a key includes three basic steps: signing the key, exporting the key, sending the key to its owner.

To sign the key, use gpg (or gpg2, it does not matter):
```text
# fetch the key from the keyserver
> gpg2 --search-keys <id or email>
# or use the following (id only)
> gpg2 --recv-keys <short id>

# check the fingerprint of the key (can also be done when editing a key,
# through the fpr command)
> gpg2 --fingerprint <id or email>

# sign the key (using your master)
> gpg2 --edit-key <id or email>
gpg> uid
# if more than one result, select the right one
gpg> <number>  # a \* will be put after the selection
gpg> sign # or tsign for trusted signatures
Really sign ? Y
<passphrase>

# don't forget to save the changes
gpg> save
gpg> quit
```

Note: the eight last groups of a fingerprint are actually the long id of a key.

Now, verify everything went well:
```text
> gpg --list-sigs <id or email>
pub   XXXXR/XxXXEFXXECCXEBXXXX XXXX-XX-XX [expires: XXXX-XX-XX]
      Key fingerprint = XXDX AEXX EXXX FDXX FAXX  XXDX XXEF XXEC CXEB XXXX
uid                 [  full  ] Other Guy <other.guy@truc.org>
sig          0x2E20FA2997D 2013-12-19  [User ID not found]
sig 3        0x95EF36ECC4E 2013-12-19  Other Guy <other.guy@truc.org>
...
sig          0xA534CC879DF 2013-12-21  Lala Lulu <lala.lulu@truc.org>
```
You can also use the option `--check-sigs`, which will also print informations about certification and trust levels: an exclamation mark indicates a good signature and the number at the right of the sig is the certification level:
```text
> gpg2 --check-sigs <id or email>
pub   XXXXR/XxXXEFXXECCXEBXXXX XXXX-XX-XX [expires: XXXX-XX-XX]
      Key fingerprint = XXDX AEXX EXXX FDXX FAXX  XXDX XXEF XXEC CXEB XXXX
...
# good signature with extensive check
sig!3        0x95EF36ECC4E 2013-12-19  Other Guy <other.guy@truc.org>
```

Export the newly signed key in a text file:
```bash
# export the signed key (-a for text file, ASCII mode)
> gpg2 --export -a <id or email> > some_file.txt
```
Send an email with the text file as attachment. Don't forget to sign and encrypt the _whole_ email, checking the **PGP/MIME option** (so the attachment is also crypted).

**Warning!** the object of an email is NEVER encrypted.

Finally, clean your keyring by deleting the key:
```bash
# remove the signed key from the trust
> gpg2 --delete-keys <id or email>
```
### Add a new signature to your key

When receiving an email with a new signature, save the attachment somewhere and do the following:
```bash
# import the key
> gpg --import /path/to/attachment.txt

# check that there is a new signature
> gpg --list-sigs <id or email>

# send it to the keyservers
> gpg2 --send-keys <long id>
```

## Miscellaneous

### Set the trust level of a key

For your own keys, it is a good idea to set them to ultimate trust. For the other keys, the only purpose is to avoid the "this key is not fully trusted. Are you sure you want to ... ?" messages. To change the level of trust of a key, either use the commandline or use thunderbird.
```text
 > gpg --edit-key <keyid or email address>
 gpg> trust
 gpg> save
```
By convention, it is good practice to set **ultimate** to your own keys and **full** for the other ones.

### Generate good pseudo-random numbers

Install `haveged`, which generates entropy source using the HAVEGE algorithm.

### Modify the master key

When you modify your master, you don't need to reimport the subkeys. The only modification which will require a new export/import is a password modification. After a change, don't forget to re-export your master in a secure location.

### Keep your keyring up to date

Don't forget to update your keyring, in case some of the keys are revocated or ... Use the command :
```bash
gpg --refresh-keys
```

or put it in a cronjob. One problem is that the server will always receive the full list of your contacts, making it easy for a bad guy to fetch the list of your contacts. If you are a little paranoiak, you can use `parcimonie.sh`, shich will use Tor and update only one key at a time. On debian, a package also called `parcimonie` available (a huge perl script).

Further reading
---------------

*   [OpenPGP best practices](https://we.riseup.net/riseuplabs+paow/openpgp-best-practices)
*   [Keysigning with the GNU/Linux Terminal](http://www.phillylinux.org/keys/terminal.html)
*   [PGP signatures with trust and verification level](http://tanguy.ortolo.eu/blog/article9/pgp-signature-infos)
*   [Using the GNU Privacy Guard](http://www.gnupg.org/documentation/manuals/gnupg-devel/index.html#Top)
*   [Les réseaux de confiance ("web of trust")](http://matrix.samizdat.net/crypto/gpg_intro/gpg-intro-5.html)
