<?php
class SSHttpRequest {

    public $protocol = 'ssl';
    public $hprotocol = 'https';
    public $host = 'onliner.io';
    public $api_path = 'test.php';
    public $port = 443;

    public function __construct(){
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
        $this->sendRequest($data, '/health/health');
    }

}
