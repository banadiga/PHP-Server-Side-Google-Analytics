Track downloading mp3
====================================================================================================
We needed track each downloading mp3 file from some folder.

Sub tasks
----------------------------------------------------------------------------------------------------
* Track witch file was downloaded.
* Track count of downloading.
* Track trying to download no existing file. 

Solutions
----------------------------------------------------------------------------------------------------
* Configuration execution `PHP` script each time when some one try to get `mp3` files thru `.htaccess`

```
Options +FollowSymlinks
RewriteEngine on
RewriteRule ^(.*)\.mp3$ proxy.php?file=$1.mp3 [QSA]
```

[Get raw .htaccess](https://raw.githubusercontent.com/banadiga/PHP-Server-Side-Google-Analytics/master/proxy-example/.htaccess)


* Event should be created when file was streamed or in case file was not found/not exists.

```php
<?php

/**
 * Google analytics event should be created each time when file was downloaded.
 *
 * Return 404 error in case when file does not exist.
 * Stream the file and create Google analytics event otherwise.
 */

// Include lib.
include_once("../google-analytics.lib.php");

// Configuration
define("ACCOUNT_ID", "");

// Get file name from GET params.
$fileName = $_GET["file"];

$eventBuilder = EventBuilder::inctase(ACCOUNT_ID)->withCategory("My file")->withAction($fileName);

// Check that file exists.
if (!file_exists($fileName)) {
    // Create event
    $eventBuilder->withCategory("File not found")->createEvent();
    header("HTTP/1.0 404 Not Found");
    exit;
}

// Header
header("Content-Disposition: attachment; filename=" . basename($fileName) . ";");
header("Content-Type: audio/mpeg");
header('Content-Length: ' . filesize($fileName));

// Stream the file
fpassthru(fopen($fileName, 'rb'));

// Create event
$eventBuilder->createEvent();

```
[Get raw proxy.php](https://raw.githubusercontent.com//banadiga/PHP-Server-Side-Google-Analytics/master/proxy-example/proxy.php)