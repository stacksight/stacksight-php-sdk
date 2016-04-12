<?php
class SSHttpRequest {

    public $protocol = 'ssl';
    public $hprotocol = 'https';
    public $host = 'api.stacksight.io';
    public $api_path = 'v0.1/index';
    public $port = 443;

    const UPDATE_URL = '/updates/update';
    const HEALTH_URL = '/health/health';
    const INVENTORY_URL = '/inventory/inventory';

    private $debug_mode = false;

    public function __construct(){
        if(!defined('INDEX_ENDPOINT_01'))
            define('INDEX_ENDPOINT_01', $this->hprotocol.'://'.$this->host.'/'.$this->api_path);

        if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true){
            $this->debug_mode = true;
        }
    }

    public function publishEvent($data) {
        if($this->debug_mode === true){
            $_SESSION['stacksight_debug']['events'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['events'][] = $data;
        }
        $this->sendRequest($data);
    }

    public function sendLog($data) {
        if($this->debug_mode === true) {
            $_SESSION['stacksight_debug']['logs'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['logs'][] = $data;
        }
        $this->sendRequest($data);
    }

    public function sendUpdates($data) {
        if($this->debug_mode === true) {
            $_SESSION['stacksight_debug']['updates'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['updates'][] = $data;
        }
        $this->sendRequest($data, self::UPDATE_URL);
    }

    public function sendHealth($data) {
        if($this->debug_mode === true) {
            $_SESSION['stacksight_debug']['health'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['health'][] = $data;
        }
        $this->sendRequest($data, self::HEALTH_URL);
    }

    public function sendInventory($data){
        if($this->debug_mode === true) {
            $_SESSION['stacksight_debug']['inventory'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['inventory'][] = $data;
        }
        $this->sendRequest($data, self::INVENTORY_URL);
    }

    public function sendSlackNotify($data) {
        $incoming_url = parse_url(STACKSIGHT_INCOMING_SLACK_URL);
        $this->host = $incoming_url['host'];
        $this->api_path = ltrim($incoming_url['path'], '/');
        $this->createSocket(true);
        $this->sendRequest($data, false);
    }
}
