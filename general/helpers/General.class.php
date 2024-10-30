<?php

namespace Spotler\General\Helpers;

class General extends \SpotlerAbstract {
	static public function printr( $value, $exit = false ): void {
		print '<pre>';
			print_r( $value );
		print '</pre>';

		if ( $exit ) {
			exit;
		}
	}
}