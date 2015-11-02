<?php 

class SSHttpRequestCurl extends SSHttpRequest implements SShttpInterface {
    public function sendRequest($data, $url = false){
        $data_string = json_encode($data);
        $ch = ($url) ? curl_init(INDEX_ENDPOINT_01.$url) : curl_init(INDEX_ENDPOINT_01.'/'.$data['index'].'/'.$data['eType']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
//        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
//        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10);
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
