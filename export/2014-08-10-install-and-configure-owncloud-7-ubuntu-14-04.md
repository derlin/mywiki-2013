---
title: "Install and configure OwnCloud 7 (Ubuntu 14.04)"
date: "2014-08-10"
categories: 
  - "configuration-tricks"
  - "programs"
---

## Download and install

1. Add the repo:
    ```bash
    cd /tmp
    wget http://download.opensuse.org/repositories/isv:ownCloud:community/xUbuntu_14.04/Release.key
    sudo apt-key add - < Release.key sudo sh -c "echo 'deb http://download.opensuse.org/repositories/isv:/ownCloud:/community/xUbuntu_14.04/ /' >> /etc/apt/sources.list.d/owncloud.list"
    ```
2. Update and install OwnCloud:
    ```bash
    sudo apt-get update
    sudo apt-get install owncloud
    ```
3. Install `maria-db` (MySQL fork) and use it instead of the regular `mysql`:
    ```bash
    sudo apt-get install mariadb-server
    ```
4. Create the database for OwnCloud:
    ```sql
    sudo mysql -u root -p
    CREATE DATABASE owncloud;
    GRANT ALL ON owncloud.\* to 'owncloud'@'localhost' IDENTIFIED BY 'database\_password';
    exit
    ```
    
5. Connect and setup

Connect to localhost/owncloud (ensure that apache2 is actually running), you should get a page with a login prompt.

Click on _Storage and database_, select MariaDB and fill the form with what you did on the previous step (db name and so forth). Then, click _Finish setup_.

Everything should work out of the box !

## Secure OwnCloud

### Automatically install security updates on Ubuntu

Type the following in a terminal:
```bash
sudo dpkg-reconfigure -plow unattended-upgrades
```
See [this link](http://www.rojtberg.net/711/secure-owncloud-server/) for more information.

### Secure Apache - error pages

### Setup ssl (https) for OwnCloud

First, create a self-signed certificate:
```bash
openssl req -new -sha256 -x509 -nodes -days 365 -out your.website.net.pem -keyout your.website.net.key
```

(I put mine in `/etc/apache2/self-certs`, with an ownership of `root:www-data`).

Then, reference the two files in the apache config by editing `/etc/apache2/sites-available/default-ssl.conf`:

```
SSLCertificateFile    /path/to/your.website.net.pem
SSLCertificateKeyFile /path/to/your.website.net.key
```

You can also add the following lines to strengthen the security of SSL:
```
SSLProtocol all -SSLv2 -SSLv3
SSLCompression off
SSLHonorCipherOrder On
SSLCipherSuite EECDH+AESGCM:EECDH+AES:EDH+AES
```

> The rationale behind this suggestion is
> 
> - Allow TLS 1.0 for compability with mobile apps
> - Disable SSL compression to mitigate the CRIME attack
> - always use Diffie Hellman(DH) key exchange(Kx) for forward secrecy
> - prefer Elliptic Curve Diffie Hellman (ECDH) for performance
> - always use AES for symmetric encryption
> - prefer AES GCM mode for security and performance
> 
> [Source](http://www.rojtberg.net/687/secure-owncloud-setup/).

Enable the ssl module of Apache:

```bash
sudo a2enmod ssl
sudo a2ensite default-ssl
sudo service apache2 reload
```

[Source](http://doc.owncloud.org/server/6.0/admin_manual/installation/installation_source.html)

### Force Apache to use HTTPS

Edit `/etc/apache2/sites-available/00-default.conf` and change the virtualhost:\*80. It should look like this:

```bash
ServerAdmin webmaster@localhost
DocumentRoot /var/www/owncloud # default page
RewriteEngine on
ReWriteCond %{SERVER_PORT} !^443$
    RewriteRule ^/(.*) https://%{HTTP_HOST}/$1 [NC,R,L]

ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

#Include conf-available/serve-cgi-bin.conf
```

Don’t forget to restart the server with `sudo service apache2 restart`.

If you use **port forwarding**, don't forget to set the redirected port to 443 instead of 80.
