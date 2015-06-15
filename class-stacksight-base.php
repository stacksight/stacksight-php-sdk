<?php

abstract class StackSightBase {

	const INIT_APP_ENDPOINT_01 = 'https://network.mean.io/api/v0.1/app/init';
	const EVENTS_ENDPOINT_01 = 'https://network.mean.io/api/v0.1/index/events/events';

	public function error_log($message, $level = 'info') {
	    if (!$message) return;

	    $log_file = __DIR__.'/../'.$level.'.log';
	    // удалить лог если он превышает $logfile_limit
	    $logfile_limit = 1024000; // размер лог файла в килобайтах (102400 = 100 мб)
	    if (file_exists($log_file) && filesize($log_file) / 1024 > $logfile_limit) unlink($log_file);
	    
	    // $date = new Datetime(null, new DateTimeZone('Europe/Minsk'));
	    $date = new Datetime();
	    $date_format = $date->format('d.m.Y H:i:s');
	    error_log($date_format .' '. $message."\n", 3, $log_file);
	}
}