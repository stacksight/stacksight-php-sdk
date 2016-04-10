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
require_once('platforms/SSWordpressClient.php');

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class WPBootstrap{

	public $options = array('stacksight_opt', 'stacksight_opt_features');

	private $multisite = false;
	private $blog_id = false;

	private $table_prefix = false;

	private $ready = false;

	private $connection;
	private $total_db;

	public $defaultDefines = array(
		'STACKSIGHT_INCLUDE_LOGS' => false,
		'STACKSIGHT_INCLUDE_HEALTH' => true,
		'STACKSIGHT_INCLUDE_INVENTORY' => true,
		'STACKSIGHT_INCLUDE_EVENTS' => true,
		'STACKSIGHT_INCLUDE_UPDATES' => true
	);

	const CONST_ENABLE_LOGS = 'logs';
	const CONST_ENABLE_INVENTORY = 'inventory';
	const CONST_ENABLE_HEALTH_SEO = 'health_seo';
	const CONST_ENABLE_HEALTH_SECURITY = 'health_security';
	const CONST_ENABLE_HEALTH_BACKUPS = 'health_backup';

	public function __construct($defined_prefix){
		if(defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_HOST')){
			if($this->connection = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)){
				if($this->total_db = @mysql_select_db(DB_NAME, $this->connection)){
					$this->setBlogId($defined_prefix);
					$this->ready = true;
				}
			}
		}
		if(file_exists(ABSPATH .'wp-content/plugins/aryo-activity-log/aryo-activity-log.php')){
			define('STACKSIGHT_DEPENDENCY_AAL', true);
		} else{
			// AAL doesn't exist
			define('STACKSIGHT_DEPENDENCY_AAL', false);
		}
		define('STACKSIGHT_PHP_SDK_INCLUDE', true);
	}

	public function init(){
		if($this->ready == true){
			$defines_from_db = $this->defineVars();
			if(is_array($defines_from_db) && !empty($defines_from_db)){
				foreach($defines_from_db as $key => $config_section){
					if(is_array($config_section) && !empty($config_section)){
//					General options
						if($key == 'stacksight_opt'){
							foreach($config_section as $key => $option){
								switch($key){
									case '_id':
										if(defined('STACKSIGHT_SETTINGS_IN_DB') && STACKSIGHT_SETTINGS_IN_DB === true) {
											if (!defined('STACKSIGHT_APP_ID') && $option) {
												define('STACKSIGHT_APP_ID', $option);
											}
										}
										break;
									case 'token':
										if(defined('STACKSIGHT_SETTINGS_IN_DB') && STACKSIGHT_SETTINGS_IN_DB === true) {
											if (!defined('STACKSIGHT_TOKEN') && $option) {
												define('STACKSIGHT_TOKEN', $option);
											}
										}
										break;
									case 'group':
										if(defined('STACKSIGHT_SETTINGS_IN_DB') && STACKSIGHT_SETTINGS_IN_DB === true) {
											if (!defined('STACKSIGHT_GROUP') && $option) {
												define('STACKSIGHT_GROUP', $option);
											}
										}
										break;
								}
							}
						}
//					Features integration options
						elseif($key == 'stacksight_opt_features'){
							foreach($config_section as $key => $option){
								switch($key){
									case 'include_logs':
										if(!defined('STACKSIGHT_INCLUDE_LOGS') && $option){
											define('STACKSIGHT_INCLUDE_LOGS', $option);
										}
										break;
									case 'include_health':
										if(!defined('STACKSIGHT_INCLUDE_HEALTH')){
											if($option == true){
												define('STACKSIGHT_INCLUDE_HEALTH', true);
											}
										}
										break;
									case 'include_inventory':
										if(!defined('STACKSIGHT_INCLUDE_INVENTORY')){
											if($option == true){
												define('STACKSIGHT_INCLUDE_INVENTORY', true);
											}
										}
										break;
									case 'include_events':
										if(!defined('STACKSIGHT_INCLUDE_EVENTS')){
											if($option == true){
												define('STACKSIGHT_INCLUDE_EVENTS', true);
											}
										}
										break;
									case 'include_updates':
										if(!defined('STACKSIGHT_INCLUDE_UPDATES')){
											if($option == true){
												define('STACKSIGHT_INCLUDE_UPDATES', true);
											}
										}
										break;
								}
							}
						}
					}
				}
			}

			// Define default values
			foreach($this->defaultDefines as $key => $default_define){
				if(!defined($key)){
					define($key, $default_define);
				}
			}

			if(defined('STACKSIGHT_TOKEN')){
				$app_id = (defined('STACKSIGHT_APP_ID')) ? STACKSIGHT_APP_ID : false;
				$group = (defined('STACKSIGHT_GROUP')) ? STACKSIGHT_GROUP : false;
//				Enable slack integration
				if(defined('STACKSIGHT_INCOMING_SLACK_URL') && (defined('STACKSIGHT_SLACK_NOTIFY_LOGS') && STACKSIGHT_SLACK_NOTIFY_LOGS == true) && defined('STACKSIGHT_SLACK_NOTIFY_LOGS_OPTIONS')){
					define('STACKSIGHT_SEND_TO_SLACK_EVENTS', STACKSIGHT_SLACK_NOTIFY_LOGS_OPTIONS);
				}

				$ss_client = new SSWordpressClient(STACKSIGHT_TOKEN, SSClientBase::PLATFORM_WORDPRESS, $app_id, $group);
				if(defined('STACKSIGHT_INCLUDE_LOGS') && STACKSIGHT_INCLUDE_LOGS === true) {
					new SSLogsTracker($ss_client);
				}

				define('STACKSIGHT_BOOTSTRAPED', TRUE);
			}
		}
	}

	private function defineVars(){
		$results = array();
		$where = 'option_name IN ("'.implode('","', $this->options).'")';
		$sql = 'SELECT * FROM '.$this->table_prefix.'options WHERE '.$where;
		if ($query =  mysql_query($sql)) {
			while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
				$results[$row['option_name']] = unserialize($row['option_value']);
			}
		}
		return $results;
	}

	private function setBlogId($defined_prefix){
		if($this->is_multisite()){
			if($blog_id = $this->getBlogId($defined_prefix)){
				if($blog_id == 1)
					$this->table_prefix = $defined_prefix;
				else
					$this->table_prefix = $defined_prefix.$blog_id.'_';
			} else{
				$this->table_prefix = $defined_prefix;
			}
		} else{
			$this->table_prefix = $defined_prefix;
		}
	}

	private function getBlogId($defined_prefix){
		$dm_domain = $_SERVER[ 'HTTP_HOST' ];
		if( ( $nowww = preg_replace( '|^www\.|', '', $dm_domain ) ) != $dm_domain )
			$where = 'domain IN ("'.$dm_domain.'","'.$nowww.'")';
		else
			$where = 'domain = "'.$dm_domain.'"';

		$sql = "SELECT blog_id FROM ".$defined_prefix."blogs WHERE $where ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1";
		if ($query =  mysql_query($sql)) {
			if($blog_id = @mysql_result($query, 0)){
				return $blog_id;
			} else{
				$sql = "SELECT blog_id FROM ".$defined_prefix."domain_mapping WHERE $where ORDER BY CHAR_LENGTH(domain) DESC LIMIT 1";
				if ($query =  mysql_query($sql)) {
					if($blog_id = @mysql_result($query, 0)){
						return $blog_id;
					} else{
						// Domain not found
					}
				}
			}
		}
		return false;
	}

	private function is_multisite() {
		if ( defined( 'MULTISITE' ) )
			return MULTISITE;
		if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) )
			return true;
		return false;
	}
}

$wp_stacksight = new WPBootstrap($table_prefix);
$wp_stacksight->init();