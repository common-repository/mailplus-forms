<?php

abstract class SpotlerAbstract {
	/**
	 * @access   private
	 * @var      string $pluginName
	 */
	protected $pluginName;

	/**
	 * @access   private
	 * @var      string $version
	 */
	protected $version;

	protected $settings;

	public function __construct( $pluginName, $version ) {
		$this->pluginName = $pluginName;
		$this->version = $version;
		$this->settings = get_option( 'spotler_settings' );
	}

	/**
	 * @return static
	 */
	public static function getInstance(): SpotlerAbstract {
		global $spotlerPlugin;

		return new static( $spotlerPlugin->getPluginName(), $spotlerPlugin->getPluginVersion() );
	}
}