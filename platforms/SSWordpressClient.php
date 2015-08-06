<?php 

class SSWordpressClient extends SSClientBase {

	protected function saveSettings($data) {
		return update_option('stacksight', $data);
	}

	protected function getSettings() {
		return get_option('stacksight');
	}

}