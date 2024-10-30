<?php

namespace Spotler\Admin;

use Spotler\General\Helpers\Assets;
use SpotlerAbstract;

class Gutenberg extends SpotlerAbstract {
	public function registerGutenbergBlock(): void {
		wp_enqueue_script(
			'spotler-gutenberg',
			Assets::getAssetsUrl( 'admin' ) . 'js' . DIRECTORY_SEPARATOR . 'gutenberg_plugin.js',
			[],
			filemtime( Assets::getAssetsFolder( 'admin' ) . 'js' . DIRECTORY_SEPARATOR . 'gutenberg_plugin.js' ),
			true
		);
	}
}