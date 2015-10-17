<?php

class GA {

    private $beaconURL = "http://www.google-analytics.com/__utm.gif"; // Beacon
    private $utmwv = "4.3"; // Analytics version
    private $utmhn; // Host name (www.elements.at)
    private $utmcs; // Charset
    private $utmul; // Language
    private $utmhid; // Random number (unique for all session requests)
    private $utmp; // Pageview
    private $utmac; // Google Analytics account
    private $utmt; // Analytics type (event)
    private $utmcc; //Cookie related variables

    private $eventCategory; // Event category
    private $eventAction; // Event action
    private $eventLabel; // Event label
    private $eventValue; // Event value

    private $eventString; // Internal structure of the complete event string

    private $httpClient;

    public function __construct($hostName, $accountId, $charset = "UTF-8") {
        $this->setHostName($hostName);
        $this->setAccountId($accountId);
        $this->setCharset($charset);

        $this->setUtmhid();
        $this->setCharset();
        $this->setCookieVariables();

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lc = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $this->setLanguage($lc);
        }

        $this->httpClient = curl_init();
    }

    public function getHttpClient() {
        curl_setopt($this->httpClient, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($this->httpClient, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: " . $_SERVER["REMOTE_ADDR"],
                "HTTP_X_FORWARDED_FOR: " . $_SERVER["REMOTE_ADDR"],
                "X-Forwarded-For: " . $_SERVER["REMOTE_ADDR"]));

        return $this->httpClient;
    }

    private function setCookieVariables() {
        $cookie = rand(10000000, 99999999) . "00145214523";
        $random = rand(1000000000, 2147483647);
        $today = time();
        $this->utmcc =
                '__utma=1.' . $cookie . '.' . $random . '.' . $today . '.' . $today . '.15;+__utmz=1.'
                . $today . '.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none);';
    }

    private function getCookieVariables() {
        return $this->utmcc;
    }

    public function setEvent($category, $action, $label = "", $value = "") {
        $this->eventCategory = (string) $category;
        $this->eventAction = (string) $action;
        if ($label) {
            $this->eventLabel = (string) $label;
        }
        if ($value) {
            $this->eventValue = (int) intval($value);
        }

        $eventString = "5(" . $this->eventCategory . "*" . $this->eventAction;

        if ($label) {
            $eventString .= "*" . $this->eventLabel . ")";
        } else {
            $eventString .= ")";
        }

        if ($this->eventValue) {
            $eventString .= "(" . $this->eventValue . ")";
        }

        $this->eventString = $eventString;
    }

    private function getEventString() {
        return $this->eventString;
    }

    private function setAnalyticsType($type = "event") {
        $this->utmt = $type;
    }

    private function getAnalyticsType() {
        return $this->utmt;
    }

    public function setAccountId($accountId) {
        $this->utmac = $accountId;
    }

    private function getAccountId() {
        return $this->utmac;
    }

    public function setVersion($version = "") {
        if ($version) {
            $this->utmwv = $version;
        }
    }

    private function getVersion() {
        return $this->utmwv;
    }

    private function getGetUniqueId() {
        return $this->utmhid;
    }

    private function setUtmhid() {
        $this->utmhid = mt_rand(100000000, 999999999);
    }

    private function getRandomNumber() {
        return mt_rand(100000000, 999999999);
    }

    public function setCharset($charset = "UTF-8") {
        if ($charset) {
            $this->utmcs = $charset;
        } else {
            $this->utmcs = "UTF-8";
        }
    }

    private function getCharset() {
        return $this->utmcs;
    }

    public function setLanguage($language = "") {
        if ($language) {
            $this->utmul = $language;
        } else {
            $this->utmul = "en-us";
        }
    }

    private function getLanguage() {
        return $this->utmul;
    }

    public function setHostName($hostName = "") {
        $this->utmhn = $hostName;
    }

    private function getHostName() {
        return $this->utmhn;
    }

    public function createEvent() {
        $this->setAnalyticsType("event");
        $parameters = array(
                'utmwv' => $this->getVersion(),
                'utmn' => $this->getRandomNumber(),
                'utmhn' => $this->getHostName(),
                'utmt' => $this->getAnalyticsType(),
                'utme' => $this->getEventString(),
                'utmcs' => $this->getCharset(),
                "utmul" => $this->getLanguage(),
                "utmhid" => $this->getGetUniqueId(),
                "utmac" => $this->getAccountId(),
                "utmcc" => $this->getCookieVariables(),
                "utmip" => $_SERVER["REMOTE_ADDR"]
        );
        return $this->requestHttp($this->beaconURL, $parameters);
    }

    private function requestHttp($url, $getParams = array()) {
        $client = $this->getHttpClient();
        curl_setopt($client, CURLOPT_URL, $url . "?" . http_build_query($getParams));
        @curl_exec($client);
    }
}

class EventBuildet {

    private $_ga;
    private $_category;
    private $_action;
    private $_label = "";
    private $_value = "";

    public static function inctase($hostName, $accountId, $charset = "UTF-8") {
        $ga = new GA($hostName, $accountId, $charset);
        return new EventBuildet($ga);
    }

    private function __construct(GA $ga) {
        $this->_ga = $ga;
    }

    public function withCategory($category) {
        $this->_category = $category;
        return $this;
    }

    public function withAction($action) {
        $this->_action = $action;
        return $this;
    }

    public function withLabel($label = "") {
        $this->_label = $label;
        return $this;
    }

    public function withValue($value = "") {
        $this->_value = $value;
        return $this;
    }

    public function createEvent() {
        $this->_ga->setEvent($this->_category, $this->_action, $this->_label, $this->_value);
        $this->_ga->createEvent();
    }
}
