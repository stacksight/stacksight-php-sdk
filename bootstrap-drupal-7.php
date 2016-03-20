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

//include_once DRUPAL_ROOT . '/includes/cache.inc';
include_once DRUPAL_ROOT . '/includes/cache.inc';
include_once DRUPAL_ROOT . '/includes/database/database.inc';
include_once DRUPAL_ROOT . '/includes/database/sqlite/database.inc';
include_once DRUPAL_ROOT . '/includes/database/mysql/database.inc';
include_once DRUPAL_ROOT . '/includes/database/pgsql/database.inc';

global $ss_client;

class DrupalBootstrap
{
    public $options = array(
        'stacksight_token',
        'stacksight_app_id',
        'stacksight_group',
        'stacksight_include_logs',
        'stacksight_include_health',
        'stacksight_include_inventory',
        'stacksight_include_events',
        'stacksight_include_updates'
    );
    private $ready = false;
    private $connection;
    private $data_options = array();

    protected $ss_client;

    public function __construct($db_options){
        global $ss_client;
        $this->ss_client =& $ss_client;

        switch ($db_options['default']['default']['driver']) {
            case 'mysql':
                $db_object = new DatabaseConnection_mysql($db_options['default']['default']);
                break;
            case 'pgsql':
                $db_object = new DatabaseConnection_pgsql($db_options['default']['default']);
                break;
            case 'sqlite':
                $db_object = new DatabaseConnection_sqlite($db_options['default']['default']);
                break;
        }

        $this->connection = $db_object;

        $query = $this->connection
            ->query('SELECT * FROM {' . $this->connection->escapeTable('variable') . '} WHERE name IN (:names)', array(':names' => $this->options))
            ->fetchAllAssoc('name', PDO::FETCH_ASSOC);

        if (isset($query['stacksight_token'])) {
            $this->data_options = $query;
            $this->ready = true;
        }
    }

    public function init(){
        if ($this->ready == true && !empty($this->data_options)) {
            if(is_array($this->data_options)){
                foreach($this->data_options as $key => $option_obkect){
                    $option = (isset($option_obkect['value']) && !empty($option_obkect['value'])) ? unserialize($option_obkect['value']) : false;
                    switch($key){
                        case 'stacksight_app_id':
                            if (!defined('STACKSIGHT_APP_ID') && $option) {
                                define('STACKSIGHT_APP_ID', $option);
                            }
                            break;
                        case 'stacksight_token':
                            if (!defined('STACKSIGHT_TOKEN') && $option) {
                                define('STACKSIGHT_TOKEN', $option);
                            }
                            break;
                        case 'stacksight_group':
                            if (!defined('STACKSIGHT_GROUP') && $option) {
                                define('STACKSIGHT_GROUP', $option);
                            }
                            break;
                        case 'stacksight_include_logs':
                            if (!defined('STACKSIGHT_INCLUDE_LOGS') && $option) {
                                define('STACKSIGHT_INCLUDE_LOGS', $option);
                            }
                            break;
                        case 'stacksight_include_health':
                            if (!defined('STACKSIGHT_INCLUDE_HEALTH')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_HEALTH', true);
                                }
                            }
                            break;
                        case 'stacksight_include_inventory':
                            if (!defined('STACKSIGHT_INCLUDE_INVENTORY')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_INVENTORY', true);
                                }
                            }
                            break;
                        case 'stacksight_include_events':
                            if (!defined('STACKSIGHT_INCLUDE_EVENTS')) {
                                if ($option == true) {
                                    define('STACKSIGHT_INCLUDE_EVENTS', true);
                                }
                            }
                            break;
                        case 'stacksight_include_updates':
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