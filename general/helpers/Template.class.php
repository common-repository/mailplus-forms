<?php

namespace Spotler\General\Helpers;

class Template extends \SpotlerAbstract {
	public static function includeTemplate( $template, $view = 'public', $args = array() ): void {
		if ( ! empty ( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		if ( strpos( $template, '.' ) === false ) {
			$template .= '.phtml';
		}

		$template_folder = plugin_dir_path( dirname( __FILE__, 2 ) ) . $view. '/template-parts' . DIRECTORY_SEPARATOR;

		if ( file_exists( $template_folder . $template ) ) {
			include( $template_folder . $template );
		}
	}
}