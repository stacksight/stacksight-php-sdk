<?php

class SSHttpRequestSockets extends SSHttpRequest implements SShttpInterface {

    public $timeout = 10;
    private $_socket;

    public function __construct(){
        $this->createSocket();
    }

    public function __destruct(){
        $this->closeSocket();
    }

    private function createSocket(){
        $flags = STREAM_CLIENT_ASYNC_CONNECT;
        if(!$this->_socket)
            $this->_socket = stream_socket_client($this->protocol . "://" . $this->host. ':' . $this->port, $errno, $errstr, $this->timeout, $flags);
        stream_set_blocking($this->_socket, false);
    }

    private function closeSocket(){
        fclose($this->_socket);
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
        fwrite($this->_socket, $req);
    }
}
