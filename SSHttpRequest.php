<?php
class SSHttpRequest {

    public $protocol = 'ssl';
    public $hprotocol = 'https';
    public $host = 'api.stacksight.io';
    public $api_path = 'v0.1/index';
    public $port = 443;

    public function __construct(){
        if(!defined(INDEX_ENDPOINT_01))
            define(INDEX_ENDPOINT_01, $this->hprotocol.'://'.$this->host.'/'.$this->api_path);
    }

    public function publishEvent($data) {
        $this->sendRequest($data);
    }

    public function sendLog($data) {
        $this->sendRequest($data);
    }

    public function sendUpdates($data) {
        $this->sendRequest($data, '/updates/update');
    }

    public function sendHealth($data) {
        $this->sendRequest($data, '/health/security');
    }

}
