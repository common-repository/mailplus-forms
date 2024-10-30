<?php

namespace Spotler\General;

use Spotler\General\Helpers\Assets;
use Spotler\General\Helpers\General;
use SpotlerAbstract;

class TinyMCE extends SpotlerAbstract {
	public function init(): void {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		\add_filter( 'mce_external_plugins', [ $this, 'addTinyMCEPlugin' ] );

		\add_filter( 'mce_buttons', [ $this, 'registerButton' ] );

		\add_action( 'wp_ajax_spotler_get_forms', [ $this, 'spotlerGetForms' ] );
	}

	public function addTinyMCEPlugin( $plugins ): array {
		if ( file_exists( Assets::getAssetsFolder( 'admin' ) . 'js' . DIRECTORY_SEPARATOR . 'editor_plugin.js' ) ) {
			$plugins['spotler'] = Assets::getAssetsUrl( 'admin' ) . 'js' . DIRECTORY_SEPARATOR . 'editor_plugin.js';
		}

		return $plugins;
	}

	public function registerButton( array $buttons ): array {
		$buttons[] = 'separator';
		$buttons[] = 'spotler';

		return $buttons;
	}

	public function spotlerGetForms() {
		$forms  = Api::getInstance()->getForms();
		$result = [];

		usort( $forms, function ( $a, $b ) {
			if ( empty( $a['name'] ) || empty( $b['name'] ) ) {
				return 0;
			}

			return strcmp( $a['name'], $b['name'] );
		} );

		foreach ( $forms as $form ) {
			if ( empty( $form['id'] ) || empty( $form['name'] ) ) {
				continue;
			}

			$result[] = [
				'id'   => (int) $form['id'],
				'name' => (string) $form['name']
			];
		}

		echo json_encode( $result );

		wp_die();
	}
}