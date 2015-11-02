<?php

require_once('/Users/igor-lemon/work/wordpress/wp-content/plugins/stacksight/stacksight-php-sdk/requests/SSHttpInterface.php');

class SSHttpRequestSockets implements SShttpInterface {

  public $protocol = 'ssl';
  public $host = 'onliner.io';
  public $api_path = 'test.php';
  public $port = 443;

  private $_socket;

  public function __construct(){
    if(!$this->_socket)
      $this->_socket = fsockopen($this->protocol . "://" . $this->host, $this->port);
  }

  public function publishEvent($data) {
    $this->sendRequest(json_encode($data));
  }

  public function sendLog($data) {
    $this->sendRequest(json_encode($data));
  }

  public function sendUpdates($data) {
    $this->sendRequest(json_encode($data), $this->api_path.'/updates/update');
  }

  public function sendHealth($data) {
    $this->sendRequest(json_encode($data), $this->api_path.'/health/health');
  }

  public function sendRequest($data, $url = false){
    if($url === false)
      $url = $this->api_path.'/'.$data['index'].'/'.$data['eType'];

    fwrite($this->_socket, "POST /$url HTTP/1.1\r\n");
    fwrite($this->_socket, "Host: $this->host\r\n");
    fwrite($this->_socket, "Content-type: application/json\r\n");
    fwrite($this->_socket, "Content-Length: ".strlen($data)."\r\n");
    fwrite($this->_socket, "Connection: close\r\n");
    fwrite($this->_socket, "\r\n");
    fwrite($this->_socket, $data);
  }
}

$obj = new SSHttpRequestSockets();

for ($i = 0; $i < 10; ++$i){
  echo "OK: $i\r\n";
  $data = ['index' => 'test.php', 'eType' => '', 'data' => $i];
  $obj->sendHealth($data);
}