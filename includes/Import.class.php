<?php

namespace Spotler;

use Spotler\Admin\Gutenberg;
use Spotler\Admin\Plugins;
use Spotler\Admin\Setting;
use Spotler\Frontend\Form;
use Spotler\Frontend\Shortcode;
use Spotler\General\Ajax;
use Spotler\General\TinyMCE;

class Import {

	/**
	 * @access   protected
	 * @var      Loader $loader
	 */
	protected $loader;

	/**
	 * @var string $pluginName
	 * @var string $version
	 */
	protected $pluginName, $version;

	public function __construct() {
		$this->version    = defined( 'SPOTLER_VERSION' ) ? SPOTLER_VERSION : '1.2.6';
		$this->pluginName = defined( 'SPOTLER_PLUGIN_NAME' ) ? SPOTLER_PLUGIN_NAME : 'Spotler Mail+ Forms';

		$this->loadDependencies();
		$this->setLocale();
		$this->defineHooks();
	}

	public function run(): void {
		$this->loader->runHooks();
	}

	public function getPluginName(): string {
		return $this->pluginName;
	}

	public function getPluginVersion(): string {
		return $this->version;
	}

	private function loadDependencies(): void {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/SpotlerAbstract.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Loader.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/I18n.class.php';

		// General - helpers.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/helpers/FormField.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/helpers/Template.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/helpers/General.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/helpers/Assets.class.php';

		// General.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/classes/Api.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/classes/Ajax.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/classes/Form.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'general/classes/TinyMCE.class.php';

		// Admin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/Setting.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/Plugins.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/Gutenberg.class.php';

		// Frontend.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'frontend/classes/Form.class.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'frontend/classes/Shortcode.class.php';

		$this->loader = new Loader();
	}

	private function setLocale(): void {
		$i18n = new I18n();

		$this->loader->addAction( 'plugins_loaded', $i18n, 'loadPluginTextDomain' );
	}

	private function defineHooks(): void {
		$this->addActions();
		$this->addFilters();
	}

	private function addActions(): void {
		/**
		 * --- Admin actions. ---
		 */
		$adminSetting = new Setting( $this->getPluginName(), $this->getPluginVersion() );
		$adminGutenberg = new Gutenberg( $this->getPluginName(), $this->getPluginVersion() );

		$this->loader->addAction( 'admin_init', $adminSetting, 'maybeMigrateOldSettings', 9 );
		$this->loader->addAction( 'admin_menu', $adminSetting, 'addOptionsPage' );
		$this->loader->addAction( 'admin_init', $adminSetting, 'registerSettings' );
		$this->loader->addAction( 'admin_enqueue_scripts', $adminGutenberg, 'registerGutenbergBlock' );

		/**
		 * --- General actions. ---
		 */
		$generalTinyMCE = new TinyMCE( $this->getPluginName(), $this->getPluginVersion() );
		$generalAjax = new Ajax( $this->getPluginName(), $this->getPluginVersion() );

		$this->loader->addAction( 'init', $generalTinyMCE, 'init' );
		$this->loader->addAction( 'wp_ajax_spotler_get_form', $generalAjax, 'getForm' );
		$this->loader->addAction( 'wp_ajax_nopriv_spotler_get_form', $generalAjax, 'getForm' );


		/**
		 * --- Frontend actions. ---
		 */
		$frontendShortcode = new Shortcode( $this->getPluginName(), $this->getPluginVersion() );
		$frontendForm      = new Form( $this->getPluginName(), $this->getPluginVersion() );

		$this->loader->addAction( 'init', $frontendShortcode, 'addShortcodes' );
		$this->loader->addAction( 'send_headers', $frontendForm, 'handleHeaders' );
	}

	private function addFilters(): void {
		/**
		 * --- Admin filters. ---
		 */
		$adminSetting = new Setting( $this->getPluginName(), $this->getPluginVersion() );
		$adminPlugins = new Plugins( $this->getPluginName(), $this->getPluginVersion() );

		$this->loader->addFilter( 'plugin_row_meta', $adminPlugins, 'changePluginMeta', 10, 2 );
		$this->loader->addFilter( 'plugin_row_meta', $adminPlugins, 'addHrefToPluginMeta', 11, 2 );
		$this->loader->addFilter( 'pre_update_option', $adminSetting, 'preSettingUpdate', 10, 3 );
		$this->loader->addFilter( 'plugin_action_links', $adminPlugins, 'addSettingsLink', 10, 2 );
	}
}
