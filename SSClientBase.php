<?php

abstract class SSClientBase {

	private $app_id;
	private $token;
	private $app_saved;
	private $platform;
	private $request_curl;
	private $request_socket;
	private $request_thread;

	public function __construct($token, $platform) {
		$this->token = $token;
		$this->platform = $platform;
		$this->request_curl = new SSHttpRequestCurl();
		$this->request_socket = new SSHttpRequestSockets();
		$this->request_thread = new SSHttpRequestThread();
	}

	public function initApp($app_id) {
		$this->app_id = $app_id;
	}

	public function publishEvent($data) {
		$data['index'] = 'events';
		$data['eType'] = 'event';
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();

		$response = $this->request_socket->publishEvent($data);
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

		$response = $this->request_socket->publishEvent($data);
		return $response;
	}

	public function sendUpdates($data) {
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;

		$response = $this->request_socket->sendUpdates($data);
		return $response;
	}

	public function sendHealth($data) {
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;

		$response = $this->request_curl->sendHealth($data);
		return $response;
	}

}
