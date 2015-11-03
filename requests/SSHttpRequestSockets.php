<?php

class SSHttpRequestSockets extends SSHttpRequest implements SShttpInterface {

    public $timeout = 10;
    private $_socket;

    public function __construct(){
        if(!$this->_socket)
            $this->_socket = @pfsockopen($this->protocol . "://" . $this->host, $this->port, $errno, $errstr, $this->timeout);
    }

    public function sendRequest($data, $url = false){
        if($url === false)
            $url = $this->api_path.'/'.$data['index'].'/'.$data['eType'];
        $content = json_encode($data);

        $req = "";
        $req.= "POST /$url HTTP/1.1\r\n";
        $req.= "Host: " . $this->host . "\r\n";
        $req.= "Content-Type: application/json\r\n";
        $req.= "Accept: application/json\r\n";
        $req.= "Content-length: " . strlen($content) . "\r\n";
        $req.= "\r\n";
        $req.= $content;

        $bytes_written = 0;
        $bytes_total = strlen($req);
        $closed = false;

        while (!$closed && $bytes_written < $bytes_total) {
            try {
                $written = @fwrite($this->_socket, substr($req, $bytes_written));
            } catch (Exception $e) {
                $this->handleError($e->getCode(), $e->getMessage());
                $closed = true;
            }
            if (!isset($written) || !$written) {
                $closed = true;
            } else {
                $bytes_written += $written;
            }
        }
    }
}
