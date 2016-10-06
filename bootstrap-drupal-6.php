<?php

require_once('SSClientBase.php');
require_once('SSHttpRequest.php');
require_once('requests/SSHttpInterface.php');
require_once('requests/SSHttpRequestCurl.php');
require_once('requests/SSHttpRequestMultiCurl.php');
require_once('requests/SSHttpRequestSockets.php');
require_once('requests/SSHttpRequestThread.php');
require_once('SSLogsTracker.php');
require_once('SSUtilities.php');
require_once('platforms/SSDrupalClient.php');

global $ss_client;
define('STACKSIGHT_INIT_START', TRUE);

define('DOCS_URL', '#');

if(defined('STACKSIGHT_PRIVATE_KEY')){
    if(defined('STACKSIGHT_PUBLIC_KEY'))
        $ss_client = new SSDrupalClient(STACKSIGHT_PRIVATE_KEY, SSClientBase::PLATFORM_DRUPAL, STACKSIGHT_PUBLIC_KEY);
    else
        $ss_client = new SSDrupalClient(STACKSIGHT_PRIVATE_KEY, SSClientBase::PLATFORM_DRUPAL);
    $handle_errors = FALSE;
    $handle_fatal_errors = TRUE;
    if(defined('STACKSIGHT_INCLUDE_LOGS') && STACKSIGHT_INCLUDE_LOGS === true){
        new SSLogsTracker($ss_client, $handle_errors, $handle_fatal_errors);
    }
    define('STACKSIGHT_PHP_SDK_INCLUDE', TRUE);
}