<?php 

class SSHttpRequest {

	const INDEX_ENDPOINT_01 	= 'https://dev.stacksight.io/api/v0.1/index';

	public function publishEvent($data) {
		$opts = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-type: application/json",
				'content' => json_encode($data)
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents(self::INDEX_ENDPOINT_01.'/'.$data['index'].'/'.$data['eType'], false, $context);

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
		$response = file_get_contents(self::INDEX_ENDPOINT_01.'/'.$data['index'].'/'.$data['eType'], false, $context);

		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}

}