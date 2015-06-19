<?php

abstract class SSClientBase {

	private $token;
	private $app;
	private $platform;
	private $request;

	abstract protected function saveSettings($data);
	abstract protected function getSettings();

	public function __construct($token, $platform) {
		$this->token = $token;
		$this->platform = $platform;
		$this->request = new SSHttpRequest();
	}

	public function initApp($name) {
		$this->app = $this->getSettings();
		$check_name = array(
			$name,
			$name . ' - 1'
		);

		if (!$this->app ||
			!in_array($this->app['name'], $check_name) || 
			$this->token != $this->app['token']) {	// app is not created yet
			$response = $this->request->createApp($name, $this->token, $this->platform);

			if ($response['success']) {
				$this->app = $response['data'];
				$this->saveSettings($this->app + array('token' => $this->token));
			} else {
				$this->app = null;
				$this->saveSettings(null);
			}
		} else $response = array('success' => true, 'message' => 'OK', 'data' => $this->app); // app exists

		return $response;
	}

	public function publishEvent($data) {
		$data['index'] = 'events';
		$data['type'] = 'events';
		$data['token'] = $this->token;
		$data['appId'] = $this->app['_id'];
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();

		$response = $this->request->publishEvent($data);
		return $response;
	}

	public function sendLog($message, $level = 'log') {
		$data['index'] = 'logs';
		$data['type'] = 'console';
		$data['token'] = $this->token;
		$data['appId'] = $this->app['_id'];
		$data['method'] = $level;
		$data['content'] = $message;
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();

		$response = $this->request->sendLog($data);
		return $response;
	}
}