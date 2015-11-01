<?php 

class SSHttpRequest {

	const INDEX_ENDPOINT_01 	= 'https://api.stacksight.io/v0.1/index';

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

	public function curlPublishEvent($data) {

		$data_string = json_encode($data);                                                                                   
                                                                                                                     
		$ch = curl_init(self::INDEX_ENDPOINT_01.'/'.$data['index'].'/'.$data['eType']);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	//	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	//	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    			'Content-Type: application/json',                                                                                
    			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
                                                                                                                     
		$response = curl_exec($ch);	

			
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

	public function sendUpdates($data) {
		$opts = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-type: application/json",
				'content' => json_encode($data)
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents(self::INDEX_ENDPOINT_01.'/updates/update', false, $context);

		// SSUtilities::error_log($response, 'http_update');

		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}

	public function curlSendUpdates($data) {
		$data_string = json_encode($data);                                                                                   
                                                                                                                     
		$ch = curl_init(self::INDEX_ENDPOINT_01.'/updates/update');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    			'Content-Type: application/json',                                                                                
    			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
                                                                                                                     
		$response = curl_exec($ch);	
			
		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}

	public function sendHealth($data) {
		$opts = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "Content-type: application/json",
				'content' => json_encode($data)
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents(self::INDEX_ENDPOINT_01.'/health/health', false, $context);

		SSUtilities::error_log($response, 'http_health');

		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}

	public function curlSendHealth($data) {
		$data_string = json_encode($data);                                                                                   
                                                                                                                     
		$ch = curl_init(self::INDEX_ENDPOINT_01.'/health/health');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    			'Content-Type: application/json',                                                                                
    			'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
                                                                                                                     
		$response = curl_exec($ch);	
			
		if ($response !== false) {
			return array('success' => true, 'message' => 'OK');
		} else {
			$error = error_get_last();
			return array('success' => false, 'message' => $error['message']);
		}
	}
}
