<?php
class SSHttpRequestMultiCurl extends SSHttpRequest implements SShttpInterface
{

    private $objects = array();
    private $ch = array();

    public function addObject($data, $url)
    {
        if (!empty($data)) {
            $this->objects[] = array(
                'data' => $data,
                'url' => $url
            );
        }
    }

    public function sendRequest($data = false, $url = false)
    {
        if (!empty($this->objects)) {
            $mh = curl_multi_init();
            $handles = array();

            foreach ($this->objects as $object) {
                $data = $object['data'];
                $url = $object['url'];
                $data_string = json_encode($data);
                $ch = ($url) ? curl_init(INDEX_ENDPOINT_01 . $url) : curl_init(INDEX_ENDPOINT_01 . '/' . $data['index'] . '/' . $data['eType']);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'api');
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                );
                curl_multi_add_handle($mh, $ch);
                $handles[] = $ch;
            }
            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            while ($active && $mrc == CURLM_OK) {
                while (curl_multi_exec($mh, $active) === CURLM_CALL_MULTI_PERFORM);
                if (curl_multi_select($mh) != -1) {
                    do {
                        $mrc = curl_multi_exec($mh, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }
            for ($i = 0; $i < count($handles); $i++) {
                curl_multi_remove_handle($mh, $handles[$i]);
            }
            curl_multi_close($mh);
        }
    }
}
