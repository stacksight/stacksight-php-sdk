<?php 

class SSHttpRequest {

	const INIT_APP_ENDPOINT_01 = 'https://network.mean.io/api/v0.1/app/init';
	const EVENTS_ENDPOINT_01 = 'https://network.mean.io/api/v0.1/index/events/events';
	const LOGS_ENDPOINT_01 = 'https://network.mean.io/api/v0.1/index/logs/console';

	public function createApp($name, $token, $platform = 'php') {
		if (!$name || !$token) return array('success' => false, 'message' => 'Empty name or token');

		$postdata = http_build_query(
			array('name' => urlencode($name), 'platform' => $platform)
		);
		$opts = array(
			'http' => array(
			'method'  => 'POST',
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
				         "authorization: " . $token,
			'content' => $postdata
			)
		);
		$context  = stream_context_create($opts);
		$response = @file_get_contents(self::INIT_APP_ENDPOINT_01, false, $context);

		if ($response !== false) {
			return array('success' => true, 'message' => 'OK', 'data' => json_decode($response, true));
		} else {
			$error = error_get_last();
			if ($error) $err_msg = $error['message'];
			else {
				if ($platform == 'drupal') $err_msg = t('Can not create an application. Check watchdog for more details.');
				else $err_msg = 'Can not create an application. Unknown error.';
			}

			return array('success' => false, 'message' => $err_msg);
		}

	}

	public function publishEvent($data) {
		$opts = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-type: application/json",
				'content' => json_encode($data)
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents(self::EVENTS_ENDPOINT_01, false, $context);

		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}

	public function sendLog($data) {
		$opts = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-type: application/json",
				'content' => json_encode($data)
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents(self::LOGS_ENDPOINT_01, false, $context);

		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}

}