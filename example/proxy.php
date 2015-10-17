<?php

include_once("../GA.php");

/**
 *
 */

$fileName = $_GET["file"];

header("Content-Disposition: attachment; filename=" . basename($fileName) . ";");
header("Content-Type: audio/mpeg");
header('Content-Length: ' . filesize($fileName));

// stream the file
$fp = fopen($fileName, 'rb');
fpassthru($fp);

EventBuildet::inctase("", "")->withCategory("")->withAction($path)->createEvent();
exit;