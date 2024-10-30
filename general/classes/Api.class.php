<?php

namespace Spotler\General;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Exception;
use SimpleXMLElement;
use Spotler\Admin\Setting;
use Spotler\General\Helpers\General;
use Spotler\I18n;
use SpotlerAbstract;

class Api extends SpotlerAbstract {
	const SPOTLER_DEFAULT_API_URL = 'https://restapi.mailplus.nl',
		SPOTLER_FORMS_LIST_API_PATH = '/integrationservice/form/list',
		SPOTLER_FORM_API_PATH = '/integrationservice/form/',
		SPOTLER_FORM_POST_API_PATH = '/integrationservice/form/result/';

	protected $client;

	public function getClient() {
		if ( ! empty( $this->client ) ) {
			return $this->client;
		}

		$settings = Setting::getInstance()->getSettings();

		try {
			if ( empty( $settings ) || empty( $settings['consumer_key'] ) || empty( $settings['consumer_secret'] ) ) {
				throw new Exception( 'Either the consumer_key or consumer_secret are not set within the plugin settings.' );
			}

			$handler    = new CurlHandler();
			$middleware = new Oauth1( [
				'consumer_key'     => esc_attr( $settings['consumer_key'] ),
				'consumer_secret'  => esc_attr( $settings['consumer_secret'] ),
				'signature_method' => Oauth1::SIGNATURE_METHOD_HMAC,
				'request_method'   => Oauth1::REQUEST_METHOD_QUERY
			] );

			$stack = HandlerStack::create( $handler );
			$stack->push( $middleware );

			$this->client = new Client( [
				'base_uri' => ! empty( $settings['api_url'] ) ? esc_attr( $settings['api_url'] ) : self::SPOTLER_DEFAULT_API_URL,
				'handler'  => $stack,
				'auth'     => 'oauth',
			] );
		} catch( GuzzleException $e ) {
			return __( 'Something went wrong while trying to log in to the Spotler Mail+ service.', 'mailplus-forms' );
		} catch ( Exception $e ) {
			return __( $e->getMessage(), 'mailplus-forms' );
		}

		return $this->client;
	}


	/**
	 * @return mixed|string|void
	 */
	public function getForms() {
		$client = $this->getClient();

		try {
			$forms  = $client->get( self::SPOTLER_FORMS_LIST_API_PATH, [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json'
				]
			] );

			if ( empty( $formsContent = $forms->getBody()->getContents() ) ) {
				throw new Exception( 'Could not retrieve forms, are you sure there are any present within the Spotler Mail+ account?' );
			}

			if ( empty( $formsContent = json_decode( $formsContent, true ) ) ) {
				throw new Exception( 'Something went wrong while retrieving the Spotler Mail+ forms.', 'mailplus-forms' );
			}
		} catch( GuzzleException $e ) {
			return __( 'Something went wrong while retrieving the Spotler Mail+ forms.', 'mailplus-forms' );
		} catch( Exception $e ) {
			return __( $e->getMessage(), 'mailplus-forms' );
		}

		return $formsContent;
	}


	/**
	 * @param $formId
	 * @param $postUrl
	 * @param null $encId
	 *
	 * @return mixed|string|void
	 */
	public function getForm( $formId, $postUrl, $encId = null ) {
		$settings     = Setting::getInstance()->getSettings();
		$outputFormat = $settings['output_format'] ?? Setting::SETTINGS_OUTPUT_FORMAT_XHTML;
		$outputMode   = $settings['output_mode'] ?? Setting::SETTINGS_OUTPUT_MODE_TABLES;
		$client       = $this->getClient();

		$getUrl = add_query_arg( [
			'postUrl'      => $postUrl,
			'outputFormat' => $outputFormat,
			'outputMode'   => $outputMode,
			'encId'        => $encId
		], self::SPOTLER_FORM_API_PATH . $formId );

		try {
			$form        = $client->get( $getUrl, [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json'
				],
			] );

			$formContent = $form->getBody()->getContents();

			if ( empty( $formContent ) ) {
				throw new Exception( 'Spotler Mail+ form content is empty, make sure your form has valid input fields.' );
			}

			$formContent = json_decode( $formContent, 1 );

			if ( empty( $formContent['html'] ) ) {
				throw new Exception( 'Spotler Mail+ form content does not contain HTML, make sure your form has valid input fields.' );
			}
		} catch( GuzzleException $e ) {
			return __( 'Something went wrong while retrieving the Spotler Mail+ form.', 'mailplus-forms' );
		} catch( Exception $e ) {
 			return __( $e->getMessage(), 'mailplus-forms' );
		}

		return $formContent;
	}


	/**
	 * @param $formId
	 * @param $postUrl
	 * @param $data
	 *
	 * @return string|void
	 */
	public function postForm( $formId, $postUrl, $data ) {
		$client      = $this->getClient();
		$xmlFormData = $this->prepareInputDataAsXML( $data, $postUrl );

		try {
			$postForm = $client->post( self::SPOTLER_FORM_POST_API_PATH . $formId, [
				'headers'     => [
					'Content-Type' => 'application/xml',
					'Accept'       => 'application/json'
				],
				'body' => $xmlFormData
			] );

			if ( empty( $contents = $postForm->getBody()->getContents() ) ) {
				throw new Exception( 'Could not successfully retrieve form confirmation.' );
			}
		} catch( GuzzleException $e ) {
			return __( 'Something went wrong while submitting your form entry.', 'mailplus-forms' );
		} catch( Exception $e ) {
			return __( $e->getMessage(), 'mailplus-forms' );
		}

		return $contents;
	}


	/**
	 * @param $data
	 * @param $postUrl
	 *
	 * @return string
	 */
	private function prepareInputDataAsXML( $data, $postUrl ): string {
		$settings     = Setting::getInstance()->getSettings();
		$outputFormat = $settings['output_format'] ?? Setting::SETTINGS_OUTPUT_FORMAT_XHTML;
		$outputMode   = $settings['output_mode'] ?? Setting::SETTINGS_OUTPUT_MODE_TABLES;

		$repairedData = \Spotler\Frontend\Form::repairPost( $data );
		$xmlData      = new SimpleXMLElement( '<params></params>' );

		$xmlData->postUrl      = $postUrl;
		$xmlData->outputMode   = $outputMode;
		$xmlData->outputFormat = $outputFormat;

		$params = $xmlData->addChild( "formParams" );

		foreach ( $repairedData as $key => $value ) {
			$entry      = $params->addChild( "entry" );
			$entry->key = $key;
			$value_xml  = $entry->addChild( "value" );
			if ( is_array( $value ) ) {
				foreach ( $value as $currentVal ) {
					$value_xml->addChild( "item", $currentVal );
				}
			} else {
				$value_xml->item = $repairedData[ $key ];
			}
		}

		return $xmlData->asXML();
	}
}