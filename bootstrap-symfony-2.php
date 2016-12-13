<?php
namespace {
    require_once('SSClientBase.php');
    require_once('SSHttpRequest.php');
    require_once('requests/SSHttpInterface.php');
    require_once('requests/SSHttpRequestCurl.php');
    require_once('requests/SSHttpRequestMultiCurl.php');
    require_once('requests/SSHttpRequestSockets.php');
    require_once('requests/SSHttpRequestThread.php');
    require_once('SSLogsTracker.php');
    require_once('SSUtilities.php');
    require_once('platforms/SSSymfony2Client.php');

    global $ss_client;

    define('DOCS_URL', 'http://stacksight.io/docs/#wordpress-installation');

    class SymfonyBootstrap{
        protected $ss_client;

        public function __construct(){
            global $ss_client;
            $this->ss_client = & $ss_client;
            if(defined('STACKSIGHT_PRIVATE_KEY')){
                if(defined('STACKSIGHT_PUBLIC_KEY'))
                    $this->ss_client = new \SSSymfony2Client(STACKSIGHT_PRIVATE_KEY, SSClientBase::PLATFORM_SYMFONY_2, STACKSIGHT_PUBLIC_KEY);
                else
                    $this->ss_client = new \SSSymfony2Client(STACKSIGHT_PRIVATE_KEY, SSClientBase::PLATFORM_SYMFONY_2);
                
                define('STACKSIGHT_BOOTSTRAPED', TRUE);
            }
        }

        public function getClient(){
            return $this->ss_client;
        }
    }
}

