<?php 

class SSUtilities {

	static function timeJSFormat() {
		$mct = explode(" ", microtime());
		return date("Y-m-d\TH:i:s",$mct[1]).substr((string)$mct[0],1,4).'Z';
	}

	static function error_log($message, $level = 'info') {
		if (!$message || (!defined('STACKSIGHT_DEBUG') || (defined('STACKSIGHT_DEBUG') && STACKSIGHT_DEBUG !== true))) return;

	    if (is_array($message) || is_object($message)) $message = print_r($message, true);

	    $log_file = dirname(__FILE__).'/../'.$level.'.log';
	    // delete logfile if filesize more than $logfile_limit
	    $logfile_limit = 1024000; // размер лог файла в килобайтах (102400 = 100 мб)
	    if (file_exists($log_file) && filesize($log_file) / 1024 > $logfile_limit) unlink($log_file);
	    
	    // $date = new Datetime(null, new DateTimeZone('Europe/Minsk'));
	    $date = new Datetime();
	    $date_format = $date->format('d.m.Y H:i:s');
	    error_log($date_format .' '. $message."\n", 3, $log_file);
	}

	static function t($str, $params = array()) {
		return str_replace(array_keys($params), $params, $str);
	}

	static function currentPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}