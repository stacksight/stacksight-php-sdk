<?php 

require_once('SSClientBase.php');
require_once('SSHttpRequest.php');
require_once('requests/SSHttpInterface.php');
require_once('requests/SSHttpRequestCurl.php');
require_once('requests/SSHttpRequestSockets.php');
require_once('requests/SSHttpRequestThread.php');
require_once('SSLogsTracker.php');
require_once('SSUtilities.php');
require_once('platforms/SSWordpressClient.php');
if(defined('STACKSIGHT_TOKEN')){
	$ss_client = new SSWordpressClient(STACKSIGHT_TOKEN, 'wordpress');
	new SSLogsTracker($ss_client);
	define('STACKSIGHT_BOOTSTRAPED', TRUE);
}