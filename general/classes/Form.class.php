<?php
namespace Spotler\General;

use SpotlerAbstract;

class Form extends SpotlerAbstract {
	public static function getPostUrl( $formId = 0 ): string {
		global $wp;

		$pageUrl = home_url( $wp->request ) . '/';

		return add_query_arg( [
			'formid' => $formId
		], $pageUrl );
	}
}