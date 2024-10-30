<?php

namespace Spotler\General\Helpers;

class Assets extends \SpotlerAbstract {
	const ASSETS_DIR_NAME = 'assets';

	static public function getAssetsFolder( $view = 'public' ): string {
		return plugin_dir_path( dirname( __FILE__, 2 ) ) . $view . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME . DIRECTORY_SEPARATOR;
	}

	static public function getAssetsUrl( $view = 'public' ): string {
		return plugin_dir_url( dirname( __FILE__, 2 ) ) . $view . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME . DIRECTORY_SEPARATOR;
	}
}