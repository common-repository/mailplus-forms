<?php
namespace Spotler\Frontend;

use Spotler\General\Api;
use Spotler\General\Helpers\General;
use SpotlerAbstract;

class Form extends SpotlerAbstract {
	public function handleHeaders() {
		if ( ! isset( $_POST['formEncId'] ) ) {
			return;
		}

		$formId = filter_input( INPUT_GET, 'formid' );

		if ( ! $formId ) {
			return;
		}

		global $spotlerFormData;

		$postUrl      = \Spotler\General\Form::getPostUrl( $formId );
		$postForm     = Api::getInstance()->postForm( $formId, $postUrl, $_POST );

		unset( $_POST['formEncId'] );

		if ( empty( $postForm ) ) {
			return;
		}

		$postForm = json_decode( $postForm, 1 );

		if ( ! empty( $postForm['url'] ) ) {
			wp_redirect( esc_url( $postForm['url'] ) );

			exit;
		}

		if ( empty( $postForm['html'] ) ) {
			return;
		}

		$spotlerFormData[$formId] = $postForm['html'];
	}

	/**
	 * This function is made to make sure that were not missing data when the $_POST variable is running to it's limit
	 * of 1024 vars.
	 */
	public static function repairPost( array $postData ): array {
		$rawPost = "&" . file_get_contents( "php://input" );
		$qForm   = [];

		foreach ( $postData as $key => $value ) {
			$pos = preg_match_all( "/&" . $key . "=([^&]*)/i", $rawPost, $regs, PREG_PATTERN_ORDER );
			if ( ( ! is_array( $value ) ) && ( $pos > 1 ) ) {
				$qForm[ $key ] = [];
				for ( $i = 0; $i < $pos; $i ++ ) {
					$qForm[ $key ][ $i ] = urldecode( $regs[1][ $i ] );
				}
			} else {
				$qForm[ $key ] = $value;
			}
		}

		return $qForm;
	}
}