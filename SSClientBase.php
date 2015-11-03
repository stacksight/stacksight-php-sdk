<?php

abstract class SSClientBase {

	private $app_id;
	private $token;
	private $app_saved;
	private $platform;
	private $request;

	const RT_SOCKET = 'socket';
	const RT_CURL = 'curl';
	const RT_THREAD = 'thread';

	public function __construct($token, $platform, $request_type = self::RT_SOCKET) {
		$this->token = $token;
		$this->platform = $platform;
		switch($request_type){
			case self::RT_SOCKET:
				$this->request = new SSHttpRequestSockets();
				break;
			case self::RT_CURL:
				$this->request = new SSHttpRequestCurl();
				break;
			case self::RT_THREAD:
				$this->request = new SSHttpRequestThread();
				break;
		}
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

		$response = $this->request->publishEvent($data);
		return $response;
	}

	public function sendUpdates($data) {
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;

		$response = $this->request->sendUpdates($data);
		return $response;
	}

	public function sendHealth($data) {
		$data['token'] = $this->token;
		$data['appId'] = $this->app_id;

		$response = $this->request->sendHealth($data);
		return $response;
	}

}
