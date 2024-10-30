<?php

namespace Spotler\Frontend;

use Spotler\General\Helpers\General;
use Spotler\General\Api;
use Spotler\General\Form;
use SpotlerAbstract;

class Shortcode extends SpotlerAbstract {
	public function addShortcodes() {
		\add_shortcode( 'spotler', [ $this, 'addSpotlerShortcode' ] );

		// Fallback, to make sure the old shortcode still works.
		\add_shortcode( 'mailplusform', [ $this, 'addSpotlerShortcode' ] );
	}

	public function addSpotlerShortcode( $attributes ) {
		$shortcodeAttributes = shortcode_atts( [
			'formid' => 0,
			'ssl'    => 'false',
		], $attributes );

		if ( empty( $formId = esc_attr( $shortcodeAttributes['formid'] ) ) ) {
			return '';
		}

		global $spotlerFormData;

		nocache_headers();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-validate', '//static.mailplus.nl/jq/jquery.validate.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		if ( ! empty( $spotlerFormData[ $formId ] ) ) {
			return $spotlerFormData[ $formId ];
		}

		$postUrl = Form::getPostUrl( $formId );
		$encId   = filter_input( INPUT_GET, 'encId' ) ?: null;
		$form    = Api::getInstance()->getForm( $formId, $postUrl, $encId );

		if ( empty( $form['html'] ) ) {
			return '';
		}

		$uniqId = uniqid();

		$form['html'] = str_replace( '<form', '<form data-uniq-id="' . esc_attr( $uniqId ) . '"', $form['html'] );
		$form['html'] .= wp_nonce_field( 'spotler_get_form', '_form_nonce_' . esc_attr( $uniqId ), true, false );
		$form['html'] .= '<input type="hidden" name="_form_id_'. esc_attr( $uniqId ).'" value="'.esc_attr( $formId ).'"/>';
		$form['html'] .= '<input type="hidden" name="_form_enc_id_'. esc_attr( $uniqId ).'" value="'.esc_attr( $encId ).'"/>';

		if ( ! empty( $form['script'] ) ) {
			wp_enqueue_script( 'spotler-' . $formId, $form['script'], [
				'jquery',
				'jquery-validate',
				'jquery-ui-datepicker'
			] );
		}

		wp_enqueue_script(
			'spotler-form-additions',
			SPOTLER_PLUGIN_URL . 'public/js/form_additions.js',
			[],
			filemtime( SPOTLER_PLUGIN_DIR . 'public/js/form_additions.js' )
		);

		wp_enqueue_style(
			'spotler-form',
			SPOTLER_PLUGIN_URL . 'public/css/form.css',
			[],
			filemtime( SPOTLER_PLUGIN_DIR . 'public/css/form.css' )
		);

		return $form['html'];
	}
}