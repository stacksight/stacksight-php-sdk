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

use Drupal\Core\Database\Database;

global $ss_client;

class DrupalBootstrap
{
    public $options = array(
        'stacksight.features',
        'stacksight.settings'
    );
    private $ready = false;
    private $connection;
    private $data_options = array();

    protected $ss_client;

    protected $database;

    private $root;

    public function __construct($database){
        global $ss_client;
        $this->root = dirname(dirname(substr(__DIR__, 0, -strlen(__NAMESPACE__))));
        require_once DRUPAL_ROOT . '/core/includes/database.inc';
        Database::setMultipleConnectionInfo($database);
        $this->connection = Database::getConnection();
        $this->ss_client =& $ss_client;
        $this->database = $database;

        $query = db_select('config', 'n')->fields('n')->condition('name',$this->options, 'IN');
        $result = $query->execute();
        if($result && is_object($result)){
            foreach($result as $key => $row){
                $values = unserialize($row->data);
                foreach($values[key($values)] as $value_key => $value){
                    $this->data_options[$value_key] =  $value;
                }
            }
            if (isset($this->data_options['token'])) {
                $this->ready = true;
            }
        }
    }

    public function init(){
        if ($this->ready == true && !empty($this->data_options)) {
            if(is_array($this->data_options)){
                foreach($this->data_options as $key => $option_object){
                    $option = (isset($option_object) && !empty($option_object)) ? $option_object : false;
                    switch($key){
                        case 'app_id':
                            if (!defined('STACKSIGHT_APP_ID') && $option) {
                                define('STACKSIGHT_APP_ID', $option);
                            }
                            break;
                        case 'token':
                            if (!defined('STACKSIGHT_TOKEN') && $option) {
                                define('STACKSIGHT_TOKEN', $option);
                            }
                            break;
                        case 'group':
                            if (!defined('STACKSIGHT_GROUP') && $option) {
                                define('STACKSIGHT_GROUP', $option);
                            }
                            break;
                        case 'include_logs':
                            if (!defined('STACKSIGHT_INCLUDE_LOGS') && $option) {
                                define('STACKSIGHT_INCLUDE_LOGS', $option);
                            }
                            break;
                        case 'include_health':
                            if (!defined('STACKSIGHT_INCLUDE_HEALTH')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_HEALTH', true);
                                }
                            }
                            break;
                        case 'include_inventory':
                            if (!defined('STACKSIGHT_INCLUDE_INVENTORY')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_INVENTORY', true);
                                }
                            }
                            break;
                        case 'include_events':
                            if (!defined('STACKSIGHT_INCLUDE_EVENTS')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_EVENTS', true);
                                }
                            }
                            break;
                        case 'include_updates':
                            if (!defined('STACKSIGHT_INCLUDE_UPDATES')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_UPDATES', true);
                                }
                            }
                            break;
                    }
                }
            }

            if(defined('STACKSIGHT_TOKEN')){
                if(defined('STACKSIGHT_APP_ID'))
                    $this->ss_client = new SSDrupalClient(STACKSIGHT_TOKEN, SSClientBase::PLATFORM_DRUPAL, STACKSIGHT_APP_ID);
                else
                    $this->ss_client = new SSDrupalClient(STACKSIGHT_TOKEN, SSClientBase::PLATFORM_DRUPAL);

                $handle_errors = FALSE;
                $handle_fatal_errors = TRUE;
                if(defined('STACKSIGHT_INCLUDE_LOGS') && STACKSIGHT_INCLUDE_LOGS == true){
                    new SSLogsTracker($this->ss_client, $handle_errors, $handle_fatal_errors);
                }
                define('STACKSIGHT_BOOTSTRAPED', TRUE);
            }
        }
    }
}

$wp_stacksight = new DrupalBootstrap($databases);
$wp_stacksight->init();