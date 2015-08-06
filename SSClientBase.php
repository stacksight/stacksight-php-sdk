<?php

abstract class SSClientBase {

	private $app_id;
	private $token;
	private $app_saved;
	private $platform;
	private $request;

	abstract protected function saveSettings($data);
	abstract protected function getSettings();

	public function __construct($token, $platform) {
		$this->token = $token;
		$this->platform = $platform;
		$this->request = new SSHttpRequest();
	}

	public function initApp($app_id) {
		$this->app_id = $app_id;
	}

	/**
	 * Creates and saves an app,
	 * @param  string $name the app name (alphabet, numbers, low dash)
	 * @return array       an array with following structure: array(
	 *                        	'success' 	=> bool,
	 *                        	'message' 	=> string, (description of the operation)
	 *                        	'new' 		=> bool, (whether app new or not)
	 *                        	'data' 		=> array, (an app's data)
	 *                        )
	 */
	public function createApp($name) {
		$this->app_saved = $this->getSettings();
		$check_name = array(
			$name,
			$name . ' - 1'
		);

		if (!$this->app_saved ||
			!in_array($this->app_saved['name'], $check_name) || 
			$this->token != $this->app_saved['token']) {	// app is not created yet
			$response = $this->request->createApp($name, $this->token, $this->platform);

			if ($response['success']) {
				$this->app_saved = $response['data'];
				$this->saveSettings($this->app_saved + array('token' => $this->token));
				// TODO: a decision whether new or not should be based on the API response
				$response['new'] = true;
			} else {
				$this->app_saved = null;
				$this->saveSettings(null);
			}
		} else $response = array('success' => true, 'message' => 'OK', 'new' => false, 'data' => $this->app_saved); // app exists

		return $response;
	}

	public function publishEvent($data) {
		$data['index'] = 'events';
		$data['eType'] = 'event';
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();

		$response = $this->request->publishEvent($data);
		return $response;
	}

	public function sendLog($message, $level = 'log') {
		$data['index'] = 'logs';
		$data['type'] = 'console';
		$data['eType'] = 'log';
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;
		$data['method'] = $level;
		$data['content'] = $message;
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();

		$response = $this->request->sendLog($data);
		return $response;
	}
}