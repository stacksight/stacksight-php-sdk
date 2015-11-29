<?php

abstract class SSClientBase {

	private $app_id;
	private $token;
	private $app_saved;
	private $platform;
	private $request_curl;
	private $request_socket;
	private $request_thread;

	private $socket_limit = 4096;

	const GROUP_PLATFORM_SH = 'platform';
	const GROUP_HEROKU = 'heroku';

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
		$this->_setAppParams($data);
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();
		if (strlen(json_encode($data)) > $this->socket_limit)
			$response = $this->request_curl->publishEvent($data);
		else
			$response = $this->request_socket->publishEvent($data);
		return $response;
	}

	public function sendLog($message, $level = 'log') {
		$data['index'] = 'logs';
		$data['type'] = 'console';
		$data['eType'] = 'log';
		$data['method'] = $level;
		$data['content'] = $message;
		$this->_setAppParams($data);
		if (!isset($data['created'])) $data['created'] = SSUtilities::timeJSFormat();
		if (strlen(json_encode($data)) > $this->socket_limit)
			$response = $this->request_curl->publishEvent($data);
		else
			$response = $this->request_socket->publishEvent($data);
		return $response;
	}

	public function sendUpdates($data) {
		$this->_setAppParams($data);
		if (strlen(json_encode($data)) > $this->socket_limit)
			$response = $this->request_curl->sendUpdates($data);
		else
			$response = $this->request_socket->sendUpdates($data);
		return $response;
	}

	public function sendHealth($data) {
		$this->_setAppParams($data);
		if (strlen(json_encode($data)) > $this->socket_limit)
			$response = $this->request_curl->sendHealth($data);
		else
			$response = $this->request_socket->sendHealth($data);
		return $response;
	}

	private function _setAppParams(&$data = array()){
		if(getenv('PLATFORM_ENVIRONMENT')){
			$data['group'] = self::GROUP_PLATFORM_SH;
		}
		$data['domain'] = $_SERVER["SERVER_NAME"];
		$data['token'] = $this->token;
	}

}
