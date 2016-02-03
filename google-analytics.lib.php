<?php

class GA {

    const BASE_URL = "http://www.google-analytics.com/__utm.gif";
    const ANALYTICS_VERSION = "4.3";

    private $charset; // Charset
    private $accountId; // Google Analytics account
    private $language; // Language
    private $hostName; // Host name (www.elements.at)

    public function __construct($accountId, $charset = "UTF-8") {
        $this->setAccountId($accountId);
        $this->setCharset($charset);

        $this->setHostName($_SERVER['SERVER_NAME']);

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->setLanguage(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        }
    }

    public function getAccountId() {
        return $this->accountId;
    }

    public function setAccountId($accountId) {
        $this->accountId = $accountId;
    }

    public function getHostName() {
        return $this->hostName;
    }

    public function setHostName($hostName = "") {
        $this->hostName = $hostName;
    }

    public function getCharset() {
        return $this->charset;
    }

    public function setCharset($charset = "UTF-8") {
        $this->charset = $charset;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language = "en-us") {
        $this->language = $language;
    }

    public function createEvent($category, $action, $label = "", $value = "") {
        $parameters = array(
                'utmwv' => self::ANALYTICS_VERSION,
                'utmn' => $this->getGetUniqueId(),
                'utmhn' => $this->getHostName(),
                'utmt' => "event",
                'utme' => $this->getEventString($category, $action, $label, $value),
                'utmcs' => $this->getCharset(),
                "utmul" => $this->getLanguage(),
                "utmhid" => $this->getGetUniqueId(),
                "utmac" => $this->getAccountId(),
                "utmcc" => $this->getCookieVariables(),
                "utmip" => $_SERVER["REMOTE_ADDR"]
        );
        return $this->requestHttp(self::BASE_URL, $parameters);
    }

    private function getGetUniqueId() {
        return mt_rand(100000000, 999999999);
    }

    public function getEventString($category, $action, $label = "", $value = "") {
        $value = (int) intval($value);

        $eventString = "5(" . $category . "*" . $action;

        if ($label) {
            $eventString .= "*" . $label . ")";
        } else {
            $eventString .= ")";
        }

        if ($value) {
            $eventString .= "(" . $value . ")";
        }

        return $eventString;
    }

    private function getCookieVariables() {
        $cookie = $this->getGetUniqueId() . "00145214523";
        $random = $this->getGetUniqueId();
        $today = time();
        return '__utma=1.' . $cookie . '.' . $random . '.' . $today . '.' . $today . '.15;+__utmz=1.' . $today . '.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none);';
    }

    private function requestHttp($url, $getParams = array()) {
        $client = curl_init();
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($client, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($client, CURLOPT_HTTPHEADER, array("X-Forwarded-For: " . $_SERVER["REMOTE_ADDR"]));
        curl_setopt($client, CURLOPT_URL, $url . "?" . http_build_query($getParams));
        curl_exec($client);
        curl_close($client);
    }
}

class EventBuilder {

    private $ga;

    private $category;
    private $action;
    private $label = "";
    private $value = "";

    private function __construct(GA $ga) {
        $this->ga = $ga;
    }

    public static function inctase($accountId, $charset = "UTF-8") {
        return new EventBuilder(new GA($accountId, $charset));
    }

    public function withCategory($category) {
        $this->category = $category;
        return $this;
    }

    public function withAction($action) {
        $this->action = $action;
        return $this;
    }

    public function withLabel($label = "") {
        $this->label = $label;
        return $this;
    }

    public function withValue($value = "") {
        $this->value = $value;
        return $this;
    }

    public function createEvent() {
        $this->ga->createEvent($this->category, $this->action, $this->label, $this->value);
    }
}
