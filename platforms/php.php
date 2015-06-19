<?php 

require(__DIR__.'/../bootstrap.php');
define('APP_SETTINGS_FILE', __DIR__.'/../../app_settings.json');

class PHPStackSight extends StackSightBase implements iStacksight {

	public $token;
	private $app;

	public function __construct($app_name, $token) {
		$this->token = $token;
	}

	/**
	 * Creates new or loads existing application. 
	 * If name of token are different than current app - creates new
	 * @param  [type] $name  [description]
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
    public function initApp($name, $token) {
        $result = false;
        $app_data = $this->readSettings();

        if (!$name) {
        	$this->error_log('initApp: Empty name or token', 'error');
        	return $result;
        }

        $postdata = http_build_query(
        	array('name' => urlencode($name))
        );
        $opts = array(
          'http' => array(
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
            			 "authorization: " . $this->token . "\r\n",
            'content' => $postdata
          )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents(PHPStackSight::INIT_APP_ENDPOINT_01, false, $context);

        if ($response !== false) {
        	$result = json_decode($response, true);
        	$result['token'] = $this->token;
        	$this->saveSettings($result);
        } else {
        	$error = error_get_last();
        	$this->error_log('initApp: '.$error['message']), 'error');
        }

        $this->error_log(print_r($result, true));

        return $result;
    }

    public function publishEvent($data) {
        $result = array();

        $opts = array(
          'http' => array(
            'method'  => 'POST',
            'header'  => "Content-type: application/json",
            'content' => json_encode($data)
          )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents(PHPStackSight::EVENTS_ENDPOINT_01, false, $context);

        if ($response !== false) {
        	$result = array('success' => true, 'message' => 'OK');
        } else {
        	$error = error_get_last();
        	$result = array('success' => false, 'message' => $error['message']);
        }

        return $result;
    }

    public function saveSettings($data) {
    	return file_put_contents(APP_SETTINGS_FILE, json_encode($data));
    }

    public function readSettings() {
    	if (file_exists(APP_SETTINGS_FILE)) return json_decode(file_get_contents(APP_SETTINGS_FILE), true);
    }

}