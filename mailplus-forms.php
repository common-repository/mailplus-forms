<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Spotler Mail+ Forms
 * Plugin URI:        https://nl.wordpress.org/plugins/mailplus-forms/
 * Description:       With the <strong>Spotler Mail+ Forms Plugin</strong> web masters can easily integrate web forms or online surveys created in <a href="https://spotler.com/email-marketing-software" target="_blank">Spotler Mail+</a> on pages and posts without any technical knowledge. Spotler Mail+ is an online marketing platform which contains a user-friendly form editor with a lot of features. For example, matrix questions, conditional questions, skip logic/branching, multi-paging, extensive features for validating answers from respondents, great e-mail confirmation possibilities and much more. <strong>To get started:</strong> 1) Click the “Activate” link to the left of this description, 2) Go to your <a href="http://login.spotler.com" target="_blank">Spotler Mail+</a> account to get your authorization codes, 3) Go to your <a href='options-general.php?page=spotler_forms'>plugin settings</a> and enter your API key and secret.
 * Version:           1.2.6
 * Author:            Spotler Software
 * Author URI:        https://www.spotler.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mailplus-forms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

use Spotler\Activate;
use Spotler\Deactivate;

define( 'SPOTLER_VERSION', '1.2.6' );
define( 'SPOTLER_PLUGIN_NAME', 'Spotler Mail+ Forms' );
define( 'SPOTLER_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define( 'SPOTLER_PLUGIN_URL', plugin_dir_url(__FILE__) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spotler-activator.php
 */
function activateSpotler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/Activate.class.php';
	Activate::handle();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spotler-deactivator.php
 */
function deactivateSpotler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/Deactivate.class.php';
	Deactivate::handle();
}

register_activation_hook( __FILE__, 'activateSpotler' );
register_deactivation_hook( __FILE__, 'deactivateSpotler' );

require plugin_dir_path( __FILE__ ) . 'includes/Import.class.php';

function runSpotler() {
	$plugin = new Spotler\Import();
	$plugin->run();

	return $plugin;
}

global $spotlerPlugin;

$spotlerPlugin = runSpotler();