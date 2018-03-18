# Angry Log

[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/igun997/angry-log/issues)

Angry log is a mini tool for break unauthorized query_string like a SQL Injection method or like that and you not required into install additional database for logging

# New Features!

  - Basic detector for SQL Injection
  - Blacklist IP Attacker
  - Whitelist IP
  - Show all log access  


### Installation

Angry log required
  - PHP 5.6 >=
  - Webserver like Apache
  - SQLite3 (Just Enable from php.ini)


Install it with git .

```sh
$ git clone https://github.com/igun997/angry-log.git
```
### Usage
Initilize Log Class
```php
//Initialize your db sqlite , Columns and table you can see on logs/db/log.db
$log = new Log("logs/db/log.db");
```
Excute Logging Method
```php
//Excute Logging
$log->logging();
```
Recomended run cronjob to excute sqlinjection method, you can input your own custom query_string to detect sql injection or something autorize query_string with input like this format
*$param = ['%YOUR_CUSTOM_QUERY_STRING%','%AND_MORE%']*  and *%* is required
This function automated block attacker IP to .htaccess
```php
// SQL Injection Section Block Your Own searchstring or Use Standar searchstring
$log->sqlinjection($param = []);
```
Get blocked IP on Your .htaccess and result on Array
```php
// See Blocked IP On your htaccess
$log->list_blocked();
```
Get All access_log from database, result can be a table or array. set false to result on table and true to result on array
```php
// See access_log from database
$log->get_log($array=false);
```
This method to manually add blocked IP into .htaccess
```php
// Input list on Array
$log->blockip(["127.0.0.1","127.0.0.2"]);
```
This method to manually add whitelist IP into .htaccess
```php
// Input list on string
$log->whiteip("127.0.0.1");
```
This method to clean log on database
```php
// Input list on string
$log->clean_log();
```
[This Demo Result ](http://prntscr.com/istku3)
### Development

Want to contribute? Great!

If you think this tool cool, can you help me to develepment this tools for have fun :D
