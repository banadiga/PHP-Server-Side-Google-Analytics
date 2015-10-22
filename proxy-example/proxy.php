<?php

/**
 * Create Google analytics event for each file download.
 *
 * Return 404 error in case file does not exist.
 * Stream the file and create Google analytics event otherwise.
 */

// Include lib.
include_once("../google-analytics.lib.php");

// Configuration
define("ACCOUNT_ID", "");

// Get file name from GET params.
$fileName = $_GET["file"];

$eventBuilder = EventBuilder::inctase(ACCOUNT_ID)->withCategory("My file")->withAction($fileName);

// Check is file exist.
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
