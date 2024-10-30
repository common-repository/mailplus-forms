<?php

namespace Spotler;

class I18n {
	public function loadPluginTextDomain() {
		load_plugin_textdomain( 'mailplus-forms', false, dirname( dirname( plugin_basename( __FILE__) ) ) . DIRECTORY_SEPARATOR . 'languages' );
	}
}