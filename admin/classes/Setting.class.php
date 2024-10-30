<?php

namespace Spotler\Admin;

use SpotlerAbstract;
use Spotler\General\Helpers\FormField;
use Spotler\General\Helpers\Template;
use Spotler\I18n;

class Setting extends SpotlerAbstract {
	const SETTINGS_PREFIX = 'spotler_',
		SETTINGS_PRIMARY_SLUG = self::SETTINGS_PREFIX . 'settings',
		SETTINGS_MIGRATED_SLUG = self::SETTINGS_PREFIX . 'settings_migrated',
		SETTINGS_EXTERNAL_LOGIN_LINK = 'https://login.spotler.com/web-login/login',
		SETTINGS_HIDDEN_CONSUMER_INPUT_VALUE = '******************************';

	const SETTINGS_OUTPUT_FORMAT_XHTML = 'XHTML1STRICT',
		SETTINGS_OUTPUT_FORMAT_HTML = 'HTML4STRICT';

	const SETTINGS_OUTPUT_MODE_DIVS = 'DIV',
		SETTINGS_OUTPUT_MODE_TABLES = 'TABLES';

	const SETTINGS_OLD_PRIMARY_SLUG = 'mpforms_plugin_options',
		SETTINGS_OLD_API_URL = 'mpforms_api_url',
		SETTINGS_OLD_CONSUMER_KEY = 'mpforms_consumer_key',
		SETTINGS_OLD_CONSUMER_SECRET = 'mpforms_consumer_secret',
		SETTINGS_OLD_OUTPUT_FORMAT = 'mpforms_htmlxhtml',
		SETTINGS_OLD_OUTPUT_MODE = 'mpforms_tablesdivs';

	protected $settings;

	public function registerSettings(): void {
		if ( \get_option( self::SETTINGS_PRIMARY_SLUG ) ) {
			\add_option( self::SETTINGS_PRIMARY_SLUG );
		}

		\register_setting( self::SETTINGS_PRIMARY_SLUG, self::SETTINGS_PRIMARY_SLUG );

		$this->apiConnectionSection();

		$this->integrationPreferencesSection();
	}

	public function getSettings( string $optionName = '' ) {
		if ( ! $this->settings ) {
			$this->settings = get_option( self::SETTINGS_PRIMARY_SLUG, true );
		}

		if ( ! empty( $optionName ) ) {
			return $this->settings[ $optionName ] ?? '';
		}

		return ! empty( $this->settings ) ? $this->settings : '';
	}

	public function addOptionsPage(): void {
		\add_options_page(
			__( 'Spotler Mail+ Forms', 'mailplus-forms' ),
			__( 'Spotler Mail+ Forms', 'mailplus-forms' ),
			'manage_options',
			'spotler_forms',
			[ $this, 'settingsPageOutput' ]
		);
	}

	public function preSettingUpdate( $values, $optionName, $oldValues ) {

		// Early exit.
		if ( ! $optionName || $optionName !== self::SETTINGS_PRIMARY_SLUG ) {
			return $values;
		}

		foreach ( $values as $valueKey => $value ) {
			switch ( $valueKey ) {
				case 'consumer_key':
				case 'consumer_secret':
					if ( $value === self::SETTINGS_HIDDEN_CONSUMER_INPUT_VALUE && ! empty( $oldValues[ $valueKey ] ) ) {
						$values[ $valueKey ] = $oldValues[ $valueKey ];
					}

					break;
			}
		}

		return $values;
	}

	private function apiConnectionSection(): void {
		$sectionId = self::SETTINGS_PREFIX . 'api_settings';
		$formField = FormField::getInstance();

		\add_settings_section(
			$sectionId,
			__( 'Authorization', 'mailplus-forms' ),
			[ $this, 'apiConnectionSectionDescription' ],
			self::SETTINGS_PRIMARY_SLUG
		);

		\add_settings_field(
			self::SETTINGS_PREFIX . 'api_url',
			__( 'Spotler Mail+ API URL', 'mailplus-forms' ),
			[ $formField, 'textInput' ],
			self::SETTINGS_PRIMARY_SLUG,
			$sectionId,
			[
				'name'        => self::SETTINGS_PRIMARY_SLUG . '[api_url]',
				'value'       => $this->getSettings( 'api_url' ),
				'type'        => 'text',
				'placeholder' => 'https://restapi.mailplus.nl/'
			]
		);

		\add_settings_field(
			self::SETTINGS_PREFIX . 'consumer_key',
			__( 'Consumer key', 'mailplus-forms' ),
			[ $formField, 'textInput' ],
			self::SETTINGS_PRIMARY_SLUG,
			$sectionId,
			[
				'name'  => self::SETTINGS_PRIMARY_SLUG . '[consumer_key]',
				'value' => ! empty( $this->getSettings( 'consumer_key' ) ) ? self::SETTINGS_HIDDEN_CONSUMER_INPUT_VALUE : '',
				'type'  => 'text',
			]
		);

		\add_settings_field(
			self::SETTINGS_PREFIX . 'consumer_secret',
			__( 'Consumer secret', 'mailplus-forms' ),
			[ $formField, 'textInput' ],
			self::SETTINGS_PRIMARY_SLUG,
			$sectionId,
			[
				'name'  => self::SETTINGS_PRIMARY_SLUG . '[consumer_secret]',
				'value' => ! empty( $this->getSettings( 'consumer_secret' ) ) ? self::SETTINGS_HIDDEN_CONSUMER_INPUT_VALUE : '',
				'type'  => 'text',
			]
		);
	}

	public function maybeMigrateOldSettings(): void {
		if ( ! empty( get_option( self::SETTINGS_MIGRATED_SLUG ) ) ) {
			return;
		}

		if ( empty( $oldSettings = get_option( self::SETTINGS_OLD_PRIMARY_SLUG ) ) ) {
			update_option( self::SETTINGS_MIGRATED_SLUG, true );

			return;
		}

		$settings = [];

		foreach ( $oldSettings as $oldSettingKey => $oldSettingValue ) {
			switch ( $oldSettingKey ) {
				case self::SETTINGS_OLD_API_URL:
					$settings['api_url'] = $oldSettingValue;

					break;

				case self::SETTINGS_OLD_CONSUMER_KEY:
					$settings['consumer_key'] = $oldSettingValue;

					break;

				case self::SETTINGS_OLD_CONSUMER_SECRET:
					$settings['consumer_secret'] = $oldSettingValue;

					break;

				case self::SETTINGS_OLD_OUTPUT_FORMAT:
					$settings['output_format'] = $oldSettingValue == 'xhtml' ?
						self::SETTINGS_OUTPUT_FORMAT_XHTML : self::SETTINGS_OUTPUT_FORMAT_HTML;

					break;

				case self::SETTINGS_OLD_OUTPUT_MODE:
					$settings['output_mode'] = $oldSettingValue == 'divs' ? self::SETTINGS_OUTPUT_MODE_DIVS : self::SETTINGS_OUTPUT_MODE_TABLES;

					break;
			}
		}

		update_option( self::SETTINGS_PRIMARY_SLUG, $settings );
		update_option( self::SETTINGS_MIGRATED_SLUG, true );
		delete_option( self::SETTINGS_OLD_PRIMARY_SLUG );
	}

	private function integrationPreferencesSection(): void {
		$sectionId = self::SETTINGS_PREFIX . 'integration_preferences_settings';
		$formField = FormField::getInstance();

		\add_settings_section(
			$sectionId,
			__( 'Form Integration Preferences', 'mailplus-forms' ),
			[ $this, 'integrationPreferencesSectionDescription' ],
			self::SETTINGS_PRIMARY_SLUG
		);

		\add_settings_field(
			self::SETTINGS_PREFIX . 'output_format',
			__( 'Publish forms in html or xhtml?', 'mailplus-forms' ),
			[ $formField, 'radioInput' ],
			self::SETTINGS_PRIMARY_SLUG,
			$sectionId,
			[
				'name'    => self::SETTINGS_PRIMARY_SLUG . '[output_format]',
				'value'   => $this->getSettings( 'output_format' ) ?: self::SETTINGS_OUTPUT_FORMAT_XHTML,
				'options' => [
					self::SETTINGS_OUTPUT_FORMAT_XHTML => __( 'xhtml', 'mailplus-forms' ),
					self::SETTINGS_OUTPUT_FORMAT_HTML  => __( 'html', 'mailplus-forms' )
				]
			]
		);

		\add_settings_field(
			self::SETTINGS_PREFIX . 'output_mode',
			__( 'Publish forms in tables or divs', 'mailplus-forms' ),
			[ $formField, 'radioInput' ],
			self::SETTINGS_PRIMARY_SLUG,
			$sectionId,
			[
				'name'    => self::SETTINGS_PRIMARY_SLUG . '[output_mode]',
				'value'   => $this->getSettings( 'output_mode' ) ?: self::SETTINGS_OUTPUT_MODE_TABLES,
				'options' => [
					self::SETTINGS_OUTPUT_MODE_TABLES => __( 'tables', 'mailplus-forms' ),
					self::SETTINGS_OUTPUT_MODE_DIVS    => __( 'divs', 'mailplus-forms' )
				]
			]
		);
	}

	public function apiConnectionSectionDescription(): void {
		$message = esc_html__( 'To get started, go to your %1$sSpotler Mail+ account%2$s to get your authorization codes and enter these below:', 'mailplus-forms' );
		$message = sprintf( $message, '<a href="' . self::SETTINGS_EXTERNAL_LOGIN_LINK . '" target="_blank">', '</a>' );
		
		echo "<p>". wp_kses($message, '<a>') ."</p>";
	}

	public function integrationPreferencesSectionDescription(): void {
		$message = __( 'Choose if you want to integrate your forms in ‘xhtml or html’ and choose if you want them to consist of ‘tables or divs’. Enter your personal preferences below:', 'mailplus-forms' );

		echo "<p>". wp_kses($message, '<a>') ."</p>";
	}

	public function settingsPageOutput(): void {
		Template::includeTemplate( 'spotler-admin-options', 'admin' );
	}
}