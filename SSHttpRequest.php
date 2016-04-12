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
    
    public function __construct(){
        if(!defined('INDEX_ENDPOINT_01'))
            define('INDEX_ENDPOINT_01', $this->hprotocol.'://'.$this->host.'/'.$this->api_path);
    }

    public function publishEvent($data) {
        if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true){
            $_SESSION['stacksight_debug']['events'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['events']['data'][] = $data;
        }
        $this->sendRequest($data, false, 'events');
    }

    public function sendLog($data) {
        if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true){
            $_SESSION['stacksight_debug']['logs'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['logs']['data'][] = $data;
        }
        $this->sendRequest($data, 'logs');
    }

    public function sendUpdates($data) {
        if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true){
            $_SESSION['stacksight_debug']['updates'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['updates']['data'][] = $data;
        }
        $this->sendRequest($data, self::UPDATE_URL, 'updates');
    }

    public function sendHealth($data) {
        if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true){
            $_SESSION['stacksight_debug']['health'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['health']['data'][] = $data;
        }
//        print_r($_SESSION['stacksight_debug']['health']);
//        die();
        $this->sendRequest($data, self::HEALTH_URL, 'health');
    }

    public function sendInventory($data){
        if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true){
            $_SESSION['stacksight_debug']['inventory'] = array();
            $data = array(
                'type' =>  $this->type,
                'data' => $data
            );
            $_SESSION['stacksight_debug']['inventory']['data'][] = $data;
        }
        $this->sendRequest($data, self::INVENTORY_URL, 'inventory');
    }

    public function sendSlackNotify($data) {
        $incoming_url = parse_url(STACKSIGHT_INCOMING_SLACK_URL);
        $this->host = $incoming_url['host'];
        $this->api_path = ltrim($incoming_url['path'], '/');
        $this->createSocket(true);
        $this->sendRequest($data, false);
    }
}
