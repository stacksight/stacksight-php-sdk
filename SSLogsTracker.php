<?php

// ini_set('display_errors', 'On');
// error_reporting(E_ALL);

define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
//Custom error handling vars
define('ERROR_REPORTING', E_ALL | E_STRICT);

class SSLogsTracker {

    private $client;

    public function __construct($client, $platform = 'php') {
        $this->client = $client;

        register_shutdown_function(array(&$this, 'shutdown'));
        set_error_handler(array(&$this, 'handler'));
    }

    //Function to catch no user error handler function errors...
    public function shutdown() {
        $error = error_get_last();

        if($error && ($error['type'] & E_FATAL)){
            $this->handler($error['type'], $error['message'], $error['file'], $error['line']);
        }

    }

    public function handler($errno, $errstr, $errfile, $errline) {
        if(!($errno & ERROR_REPORTING)) return;

        $message = $errstr.' in '.$errfile.' on line '.$errline;

        switch ($errno) {
            case E_ERROR: // 1 //
                $this->client->sendLog('E_ERROR: ' . $message, 'error'); break;
            case E_WARNING: // 2 //
                $this->client->sendLog('E_WARNING: ' . $message, 'warn'); break;
            case E_PARSE: // 4 //
                $this->client->sendLog('E_PARSE: ' . $message, 'error'); break;
            case E_NOTICE: // 8 //
                $this->client->sendLog('E_NOTICE: ' . $message, 'info'); break;
            case E_CORE_ERROR: // 16 //
                $this->client->sendLog('E_CORE_ERROR: ' . $message, 'error'); break;
            case E_CORE_WARNING: // 32 //
                $this->client->sendLog('E_CORE_WARNING: ' . $message, 'warn'); break;
            case E_COMPILE_ERROR: // 64 //
                $this->client->sendLog('E_COMPILE_ERROR: ' . $message, 'error'); break;
            case E_COMPILE_WARNING: // 128 //
                $this->client->sendLog('E_COMPILE_WARNING: ' . $message, 'warn'); break;
            case E_USER_ERROR: // 256 //
                $this->client->sendLog('E_USER_ERROR: ' . $message, 'error'); break;
            case E_USER_WARNING: // 512 //
                $this->client->sendLog('E_USER_WARNING: ' . $message, 'warn'); break;
            case E_USER_NOTICE: // 1024 //
                $this->client->sendLog('E_USER_NOTICE: ' . $message, 'info'); break;
            case E_STRICT: // 2048 //
                $this->client->sendLog('E_STRICT: ' . $message, 'info'); break;
            case E_RECOVERABLE_ERROR: // 4096 //
                $this->client->sendLog('E_RECOVERABLE_ERROR: ' . $message, 'error'); break;
            case E_DEPRECATED: // 8192 //
                $this->client->sendLog('E_DEPRECATED: ' . $message, 'info'); break;
            case E_USER_DEPRECATED: // 16384 //
                $this->client->sendLog('E_USER_DEPRECATED: ' . $message, 'info'); break;
        }

        return false;
    }
}