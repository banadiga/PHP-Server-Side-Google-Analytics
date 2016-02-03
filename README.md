PHP Server Side Google Analytics Client.
====================================================================================================
Server Side Google Analytics (SSGA) is a simple PHP lib, which allows to track server-side events and data within Google Analytics.

An implementation of a generic server-side Google Analytics client in PHP that implements nearly every parameter and tracking feature of the original GA Javascript client. 

**Quick introductions**

* This SSGA supported only `Event Tracking` in this moment.
* Functionality witch will create event implemented as builder named `EventBuilder`. We cau use all benefits of builder patterns.
* Event setup based on [Event Tracker Guide](http://code.google.com/apis/analytics/docs/tracking/eventTrackerGuide.html) 

Supported Features
----------------------------------------------------------------------------------------------------
* Event Tracking

List TODO
----------------------------------------------------------------------------------------------------
* Pageview Tracking
* Custom Variable Tracking
* Ecommerce Tracking
* Campaign Tracking
* Social Interaction Tracking
* Site Speed Tracking

Known problems
----------------------------------------------------------------------------------------------------
**Google Analytics'** geo location functionalities won't work Native geo location features like the worldmap view won't work anymore as they rely solely on the IP address of the GA client - which will always be the one of your server(s) when using this library.

Quick start
----------------------------------------------------------------------------------------------------
Create event with all event's attribute.

```php
<?php

// Include lib.
include_once("google-analytics.lib.php");

// Create event
EventBuilder::inctase("ACCOUNT_ID")->withCategory("Category anme")
    ->withAction("Action cane")
    ->withLabel("Label")
    ->withValue(100500)
    ->createEvent();
```

**NODE** Only Category is required. Action, Label and Value can be empty or not set.

Usage Example
----------------------------------------------------------------------------------------------------
* [ **Cron task finished** track is cron task finished.](https://github.com/banadiga/PHP-Server-Side-Google-Analytics/blob/master/cron-example/README.md)
* [ **Proxy example** track downloading mp3 files.](https://github.com/banadiga/PHP-Server-Side-Google-Analytics/blob/master/proxy-example/README.md)
