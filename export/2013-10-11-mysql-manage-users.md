---
title: "MySql - manage users"
date: "2013-10-11"
categories: 
  - "web"
tags: 
  - "db"
  - "lampp"
  - "mysql"
  - "wordpress"
---

To connect to a mysql server, type: `> mysql -u root -p`

At the prompt, you can do the following: 
```mysql
use mysql;
create user 'user'@'%' identified by 'password';
grant all privileges on mywiki.* to 'mywiki';
```
```mysql
-- change root password
update user set password=PASSWORD("NEWPASSWORD") where User='root'; flush privileges;
quit
```

If you use lampp, don't forget that the mysql to use is located in lampp/bin/mysql.
