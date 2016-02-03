Track is cron task finished
====================================================================================================
Create GA event when some script is finished or stopped.

Solutions
----------------------------------------------------------------------------------------------------
Use `register_shutdown_function` to registers a callback to be executed after script execution finishes or `exit()` is called.

```php
<?php

/**
 * Create Google analytics event for each time when script is stopped or finished.
 */

// Include lib.
include_once("../google-analytics.lib.php");

// Configuration
define("ACCOUNT_ID", "");

function shutdown() {
    // Create event
    EventBuilder::inctase(ACCOUNT_ID)->withCategory("My script")->withAction("finished")->createEvent();
}

register_shutdown_function('shutdown');

// Do something...

```
[Get raw cron.php](https://raw.githubusercontent.com//banadiga/PHP-Server-Side-Google-Analytics/master/cron-example/cron.php)
