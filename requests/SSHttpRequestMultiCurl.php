<?php
class SSHttpRequestMultiCurl extends SSHttpRequest implements SShttpInterface
{

    private $objects = array();
    private $ch = array();

    public $type = 'multicurl';

    private $associate = array();

    private $max_retry = 3;

    public $stackSize = 50;

    public function addObject($data, $url, $type)
    {
        if (!empty($data)) {
            $this->objects[] = array(
                'data' => $data,
                'url' => $url,
                'type' => $type
            );
        }
    }

    public function sendRequest($data = false, $url = false, $id_handle = false, $retry = 0)
    {
        if($retry > $this->max_retry) {
            return false;
        }

        if (!empty($this->objects)) {
            $mh = curl_multi_init();
            $handles = array();
            $unworked = array();
            $associate_handlers = array();
            $associate_handlers_info = array();

            foreach ($this->objects as $object) {
                $data = $object['data'];
                $url = $object['url'];
                $data_string = json_encode($data);
                $ch = ($url) ? curl_init(INDEX_ENDPOINT_01 . $url) : curl_init(INDEX_ENDPOINT_01 . '/' . $data['index'] . '/' . $data['eType']);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'api');
                curl_setopt($ch, CURLINFO_HEADER_OUT, false);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);

                if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true) {
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                } else{
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                }

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                );

                $id_handle = (int) $ch;
                $this->associate[$id_handle] = $object['type'];
                $associate_handlers[$id_handle] = $object;
                $associate_handlers_info[$id_handle] = $object['type'];
                $handles[] = $ch;
            }


            $stacks = array_chunk($handles, $this->stackSize);

            foreach ($stacks as $requests) {
                foreach ($requests as $request) {
                    if (($status = curl_multi_add_handle($mh, $request)) !== CURLM_OK) {
                        throw new Exception("Unable to add request to cURL multi handle ($status)");
                    }
                }
                $active = null;
                do {
                    $code = curl_multi_exec($mh, $active);
                } while ($code == CURLM_CALL_MULTI_PERFORM);
                while ($active && $code == CURLM_OK) {
                    if (curl_multi_select($mh) === -1) {
                        usleep(300);
                    }
                    do {
                        $code = curl_multi_exec($mh, $active);
                    } while ($code == CURLM_CALL_MULTI_PERFORM);
                }
                $i = 0;
                foreach ($requests as $request) {
                    if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true) {
                            $id_handle  = (int) $request;
                            $info = curl_multi_getcontent($request);

                            if ($info) {
                                $curl_handle_info = curl_getinfo($request);
                                if(!isset($curl_info[$this->associate[$id_handle]])){
                                    $curl_info[$this->associate[$id_handle]] = $curl_handle_info;
                                }
                                elseif((int) $curl_handle_info['http_code'] == 200){
                                    $curl_info[$this->associate[$id_handle]] = $curl_handle_info;
                                }
                                $curl_info[$this->associate[$id_handle]]['response'] = $info;
                            }
                    }
                    curl_multi_remove_handle($mh, $request);
                }
                if ($code !== CURLM_OK) {
                    throw new Exception("Error executing multi request, exit code = " . $code);
                }
            }

            if((defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG === true) && defined('STACKSIGHT_DEBUG_MODE') && STACKSIGHT_DEBUG_MODE === true) {
                foreach($curl_info as $key => $info){
                    $_SESSION['stacksight_debug'][$key]['request_info'] = $info;
                }
            }

        }
    }
}