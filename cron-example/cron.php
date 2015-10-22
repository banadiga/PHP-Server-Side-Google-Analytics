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
