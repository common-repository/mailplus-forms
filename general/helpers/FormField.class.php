<?php

namespace Spotler\General\Helpers;

use SpotlerAbstract;

class FormField extends SpotlerAbstract {
	public function textInput( array $args ) {
		$args['checked']     = ! empty ( $args['checked'] ) ? $args['checked'] : '';
		$args['value']       = ! empty ( $args['value'] ) ? $args['value'] : '';
		$args['placeholder'] = ! empty ( $args['placeholder'] ) ? $args['placeholder'] : '';
		$args['description'] = ! empty ( $args['description'] ) ? $args['description'] : '';

		echo '
			<input 
				type="'. esc_attr($args['type']) .'" 
				placeholder="'. esc_attr($args['placeholder']) .'"
				name="'. esc_attr($args['name']) .'" 
				value="'. esc_attr($args['value']) .'"
				id="'. esc_attr($args['name']) .'"
				'. esc_attr($args['checked']) .'
			/>
			<p>'. esc_html( $args['description'] ) .'</p>';
	}

	public function radioInput( array $args ) {
		$args['options'] = ! empty( $args['options'] ) ? $args['options'] : false;

		if ( empty( $args['options'] ) || empty( $args['name'] ) ) {
			return;
		}

		$inputHtml = '';

		foreach( $args['options'] as $optionValue => $optionLabel ) {
			$inputId = $args['name'].'_'.$optionValue;

			$inputHtml .= '<input type="radio" name="'. esc_attr( $args['name'] ) .'" id="'.esc_attr( $inputId ).'" value="'. esc_attr( $optionValue ) .'"'.( !empty( $args['value'] ) && $args['value'] == $optionValue ? 'checked="checked"' : '').'>';
			$inputHtml .= '<label for="'.esc_attr( $inputId ).'">'.esc_html( $optionLabel ).'</label>';
			$inputHtml .= '<br>';
		}

		print $inputHtml;
	}

	public static function submitButton( array $args ): void {
		$args['buttonClass'] = ! empty ( $args['buttonClass'] ) ? $args['buttonClass'] : '';
		$args['label']       = ! empty ( $args['label'] ) ? $args['label'] : '';
		$args['description'] = ! empty ( $args['description'] ) ? $args['description'] : '';

		echo '
			<button name="'. esc_attr($args['name']) .'" class="'. esc_attr($args['buttonClass']) .'">'. esc_html($args['label']) .'</button>
			<p>'. esc_html($args['description']) .'</p>';
	}
}