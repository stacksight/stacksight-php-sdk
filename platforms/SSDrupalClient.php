<?php 

require(__DIR__.'/../bootstrap.php');

class SSDrupalClient extends SSClientBase {

	protected function saveSettings($data) {
		return variable_set('stacksight', $data);
	}

	protected function getSettings() {
		return variable_get('stacksight');
	}

}