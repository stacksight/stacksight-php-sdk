<?php 

require(__DIR__.'/../bootstrap.php');

class DPStackSight extends StackSightBase implements iStacksight {

    public function initApp($name, $token) {
        if (!$name || !$token) return array('success' => false, 'message' => 'Empty name or token');

        $result = array();

        $response =  drupal_http_request(DPStackSight::INIT_APP_ENDPOINT_01, array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'authorization' => $token
            ),
            'method' => 'POST',
            'data' => http_build_query(array('name' => urlencode($name)))
        ));

        $this->error_log(print_r($response, true));

        if ($response->code != 200) {
            $result = array('success' => false, 'message' => $response->error);
        } else {
            $result = array('success' => true, 'message' => 'OK', 'data' => json_decode($response->data, true));
        }

        return $result;
    }

    public function publishEvent($data) {
        $result = array();

        $response =  drupal_http_request(DPStackSight::EVENTS_ENDPOINT_01, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'method' => 'POST',
            'data' => json_encode($data)
        ));

        $this->error_log(print_r($response, true));

        if ($response->code != 200) {
            $result = array('success' => false, 'message' => $response->error);
        } else {
            $result = array('success' => true, 'message' => 'OK');
        }

        return $result;
    }

}