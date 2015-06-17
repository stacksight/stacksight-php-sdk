<?php 

require(__DIR__.'/../bootstrap.php');

class WPStackSight extends StackSightBase implements iStacksight {

    public function initApp($name, $token) {
        if (!$name || !$token) return array('success' => false, 'message' => 'Empty name or token');

        $result = array();

        $response = wp_remote_post(WPStackSight::INIT_APP_ENDPOINT_01, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 0,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'authorization' => $token
            ),
            'body' => array(
                'name' => urlencode($name)
            )
        ));

        if (is_wp_error($response)) {
           $error_message = $response->get_error_message();
           $result['message'] = "initApp: something went wrong: $error_message";
           $result['success'] = false;
        } else {
            if ($response['response']['code'] == 200) {
                $result['data'] = json_decode($response['body'], true);
                $result['message'] = 'OK';
                $result['success'] = true;
            } else {
                $result['message'] = "initApp: something went wrong: ".$response['body'];
                $result['success'] = false;
            }
        }

        return $result;
    }

    public function publishEvent($data) {
        $result = array();

        $response = wp_remote_post(WPStackSight::EVENTS_ENDPOINT_01, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 0,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data)
        ));

        if (is_wp_error($response)) {
           $error_message = $response->get_error_message();
           $result['message'] = "publishEvent: something went wrong: $error_message";
           $result['success'] = false;
        } else {
            if ($response['response']['code'] == 200) {
                $result['message'] = 'OK';
                $result['success'] = true;
            } else {
                $result['message'] = "publishEvent: something went wrong: ".$response['body'];
                $result['success'] = false;
            }
        }

        return $result;
    }

}