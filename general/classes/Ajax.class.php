<?php

namespace Spotler\General;

use SpotlerAbstract;

class Ajax extends SpotlerAbstract {
	public function getForm() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'spotler_get_form' ) || empty( $_POST['form_id'] ) ) {
			\wp_die();
		}

		$encId   = filter_input( INPUT_POST, 'enc_id' ) ?: null;
		$formId  = filter_input( INPUT_POST, 'form_id' );

		$postUrl = Form::getPostUrl( $formId );
		$form    = Api::getInstance()->getForm( $formId, $postUrl, $encId );

		if ( empty( $form['html'] ) ) {
			\wp_die();
		}

		\wp_send_json_success( $form['html'] );
	}
}