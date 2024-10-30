<?php

namespace Spotler\Admin;

use SpotlerAbstract;

class Plugins extends SpotlerAbstract {
	const DOCUMENTATION_EXTERNAL_LINK = 'https://helpcenter.spotler.com/hc/en-us/articles/360016176579-How-do-I-post-a-form-on-my-WordPress-website';

	private function isFileSpotlerPlugin(string $file): bool {
		return 'mailplus-forms/mailplus-forms.php' == $file;
	}

	public function changePluginMeta($pluginMeta, $pluginFile): array {
		if ( ! $this->isFileSpotlerPlugin( $pluginFile ) ) {
			return $pluginMeta;
		}

		$string = __( '%1$sDocumentation%2$s', 'mailplus-forms' );
		$pluginMeta[] = sprintf( $string, '<a target="_blank" href="' . self::DOCUMENTATION_EXTERNAL_LINK . '">', '</a>' );

		return $pluginMeta;
	}

	public function addHrefToPluginMeta($pluginMeta, $pluginFile): array {
		if ( ! $this->isFileSpotlerPlugin( $pluginFile ) ) {
			return $pluginMeta;
		}

		foreach( $pluginMeta as $metaKey => $metaValue ) {
			if ( strpos( $metaValue, 'href' ) ) {
				$pluginMeta[$metaKey] = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $metaValue);
			}
		}

		return $pluginMeta;
	}

	public function addSettingsLink($links, $pluginFile): array {
		if ( ! $this->isFileSpotlerPlugin( $pluginFile ) || ! current_user_can( 'manage_options' ) ) {
			return $links;
		}

		$links = (array) $links;
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=spotler_forms' ),
			__( 'Settings', 'classic-editor' )
		);

		return $links;
	}
}