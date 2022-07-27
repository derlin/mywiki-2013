---
title: "Wordpress general tricks"
date: "2013-12-27"
categories: 
  - "web"
tags: 
  - "jquery"
  - "wordpress"
---

## Use the dollar sign instead of 'jQuery'

Two solutions.

- Either you have a function inside a `jQuery('document').ready` block, or
- you need the $ sign somewhere else, like a named function

Inside an anonymous function, you can simply pass the $ directly to the function:
```js
// pass the $ as an argument of the an. function
jQuery( document ).ready( function( $ ){
		$('my-selector').doStuff();
	});
});
```

Otherwise, you can declare the $ variable and assign jQuery to it:
```js
function marvellousFunction(){
    var $ = jQuery;
// more code
}
```js

## Enqueue scripts and styles from a child theme

Simply add the following in your `functions.php`:
```php
<?php
function nameofchildtheme_enqueue() {
    // enqueue a stylesheet 
    wp_enqueue_style( 'bootstrap.css', 
        CHILD_DIR . '/css/bootstrap.css' );
    // script loaded AFTER jquery >= 1.0.0
    wp_enqueue_script( 'bootstrap.min.js', 
        CHILD_DIR . '/js/bootstrap.js', 
        array('jquery'), 
        '1.0.0', true );
    // script depending on both jquery and bootstrap 
    wp_enqueue_script('myutils', 
        CHILD_DIR. '/js/myutils.js', 
        array( 'jquery', 'bootstrap.min.js' ) );
}

// register the hook
add_action( 'wp_enqueue_scripts', 'nameofchildtheme_enqueue' );
?>
```

## add a login/logout menu item

For that, we can use the filter hook `wp_nav_menu_items`.

Open your theme `function.php` and copy-paste the following:
```php
add_filter( 'wp_nav_menu_items', 'wpsites_loginout_menu_link' );

function wpsites_loginout_menu_link( $menu ) { 
    $loginout = wp_loginout($_SERVER['REQUEST_URI'], false );
    $menu .= '<li>' . $loginout . '</li>';
    return $menu;
}
```

Note that based on your theme, you need to customize the li classes. For twenty-thirteen for example, we would have:
```php
$loginout = '<li class="nav-menu">' . wp_loginout($_SERVER['REQUEST_URI'], false ) . '</li>';
```

## Use ssh/sftp to update your wordpress (apache2)

1. Ensure that apache2 has the ssh extension installed and enabled. If not:
    ```bash
    apt-get install libssh2-php
    echo extension=ssh2.so | sudo tee -a /etc/php5/apache2/php.ini
    service apache2 restart
    ```
    
2. Create an ssh key for your wordpress user
    ```bash
    sudo mkdir /home/wp-user/.ssh
    sudo chown wp-user:wp-user /home/wp-user/.ssh/
    
    su wp-user
    ssh-keygen -t rsa -b 4096
    ```
    
3. Add the correct permissions
    ```bash
    sudo chmod 0700 /home/wp-user/.ssh/
    sudo chown wp-user:www-data /home/wp-user/id_rsa*
    sudo chmod 0640 /home/wp-user/id_rsa*
    ```
    
4. add your user to the authorized keys
    ```bash
    cat /home/wp-user/.ssh/wp_rsa.pub >> /home/wp-user/.ssh/authorized_keys
    ```
    

Now, if you try to update wordpress, you will get the possibility to use ssh2. Note that I could only achieve the update from localhost (`host=localhost, user=wp-user, password=, paths=/home/wp-user/.ssh/id_rda*`).

### Automatize the process

```php
define('FTP_PUBKEY','/home/wp-user/.ssh/id_rsa.pub');
define('FTP_PRIKEY','/home/wp-user/.ssh/id_rsa');
define('FTP_USER','wp-user');
define('FTP_PASS','');
define('FTP_HOST','localhost');
```

### A word about permissions

Normally, the wordpress files should be owned by the ftp/ssh user. In our case, we should have created a new user (wp-user), and chowned all the site content to him. Note that there is a security breach if you use sudo while logged-in as your user !!

### some sources

SSH2:

- [http://www.jonathan.vc/2009/02/09/wordpress-install-upgrade-ssh/](http://www.jonathan.vc/2009/02/09/wordpress-install-upgrade-ssh/)
- [http://www.htpcbeginner.com/enable-wordpress-ssh-access/](http://www.htpcbeginner.com/enable-wordpress-ssh-access/)
- [http://code.tutsplus.com/articles/quick-tip-upgrade-your-wordpress-site-via-ssh–wp-27691](http://code.tutsplus.com/articles/quick-tip-upgrade-your-wordpress-site-via-ssh--wp-27691)
- [https://www.digitalocean.com/community/tutorials/how-to-configure-secure-updates-and-installations-in-wordpress-on-ubuntu](https://www.digitalocean.com/community/tutorials/how-to-configure-secure-updates-and-installations-in-wordpress-on-ubuntu)

permissions:

- [http://codex.wordpress.org/Changing_File_Permissions](http://codex.wordpress.org/Changing_File_Permissions)
- [http://codex.wordpress.org/Hardening_WordPress#File_Permissions](http://codex.wordpress.org/Hardening_WordPress#File_Permissions)

## Backup/restore database

```bash
# backup db
mysqldump -u -p > dbname.sql 

# restore db
# if the database does not exist, create it before running
# the following:
mysql -u -p < dbname.sql
```