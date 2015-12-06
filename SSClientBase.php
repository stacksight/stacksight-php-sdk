<?php

abstract class SSClientBase {

	private $_app_id = false;
	private $token;
	private $_platform;
	private $request_curl;
	private $request_socket;
	private $request_thread;

	private $socket_limit = 4096;

	const GROUP_PLATFORM_SH = 'platform';
	const GROUP_HEROKU = 'heroku';

	const PLATFORM_MEAN = 'mean';
	const PLATFORM_DRUPAL = 'drupal';
	const PLATFORM_WORDPRESS = 'wordpress';
	const PLATFORM_METEOR = 'meteor';
	const PLATFORM_NODEJS = 'nodejs';
	const PLATFORM_PHP = 'php';

	public function __construct($token, $platform, $app_id = false) {
		$this->token = $token;
		if($app_id)
			$this->_app_id = $app_id;

		switch($platform){
			case self::PLATFORM_MEAN:
				$this->_platform = self::PLATFORM_MEAN;
				break;
			case self::PLATFORM_DRUPAL:
				$this->_platform = self::PLATFORM_DRUPAL;
				break;
			case self::PLATFORM_WORDPRESS:
				$this->_platform = self::PLATFORM_WORDPRESS;
				break;
			case self::PLATFORM_METEOR:
				$this->_platform = self::PLATFORM_METEOR;
				break;
			case self::PLATFORM_NODEJS:
				$this->_platform = self::PLATFORM_NODEJS;
				break;
			case self::PLATFORM_PHP:
				$this->_platform = self::PLATFORM_PHP;
				break;
			default:
				$this->_platform = self::PLATFORM_MEAN;
				break;
		}

		$this->request_curl = new SSHttpRequestCurl();
		$this->request_socket = new SSHttpRequestSockets();
		$this->request_thread = new SSHttpRequestThread();
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

		if($this->_app_id){
			$data['appId'] = $this->_app_id;
		} else {
			$data['domain'] = $_SERVER['HTTP_HOST'];
			$data['platform'] = $this->_platform;
		}

		if(getenv('PLATFORM_ENVIRONMENT')){
			$data['group'] = self::GROUP_PLATFORM_SH;
		}
		$data['token'] = $this->token;
	}

}
