<?php

namespace Roel\WP\DevTools\DevTools;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Brick\VarExporter\VarExporter;

class Unserialize {
	/**
	 * Run the unserialize functionality.
	 * It expects the `serializedInput` value and must not be empty.
	 *
	 * @since  0.1.0
	 *
	 * @param  WP_REST_Request   $request   The API request.
	 * @return WP_REST_Response|WP_Error    The API response.
	 */
	public function run( WP_REST_Request $request ) {
		$body = $request->get_params();

		if ( empty( $body ) || empty( $body['serializedInput'] ) || empty( $body['outputMethod'] ) ) {
			return new WP_Error( 'empty-data', 'There is no data to unserialize.' );
		}

		$output = $body['outputMethod'];

		if ( ! method_exists( $this, $output ) ) {
			return new WP_Error( 'wrong-output', 'The selected output does not exist.' );
		}

		$unserialized = $this->{$output}( $body['serializedInput'] );

		if ( ! $unserialized ) {
			return new WP_Error( 'data-error', 'There is an error with the serialized data.' );
		}

		$data = array(
			'serialized'         => $body['serializedInput'],
			'unserialized'       => $unserialized,
			'syntax_highlighted' => $this->syntax_highlighting( $unserialized, $output, $body['options'] ?? array() ),
			'output_method'      => $output,
			'metadata'           => array(
				'php_version' => phpversion(),
			),
		);

		return new WP_REST_Response( $data );
	}

	/**
	 * Unserialize the data and transform it as JSON.
	 *
	 * @since  0.1.0
	 *
	 * @param  string   $serialized_input   The serialized data.
	 * @return false|string                 The unserialize data as JSON format.
	 */
	protected function json( string $serialized_input ) {
		$options = JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT;
		return wp_json_encode( maybe_unserialize( $serialized_input ), $options );
	}

	/**
	 * Unserialize the data and transform it as an array.
	 * Use the `VarExporter` package to get a pretty print for the array.
	 *
	 * @since  0.1.0
	 *
	 * @param  string   $serialized_input   The serialized data.
	 * @return false|string                 The unserialize data as array format.
	 *
	 * @throws \Brick\VarExporter\ExportException
	 */
	protected function array( string $serialized_input ) {
		$unserialized = $this->json( $serialized_input );
		return $unserialized ? VarExporter::export( json_decode( $unserialized, true ) ) : false;
	}

	/**
	 * Syntax highlight the unserialized data.
	 * It's a good way to show the user a good and readable data.
	 *
	 * @since  0.1.0
	 *
	 * @param  string   $unserialized   The unserialized data.
	 * @param  string   $output         The output method (json, array).
	 * @return mixed|string             The unserialized data with syntax highlighted.
	 */
	protected function syntax_highlighting( string $unserialized, string $output, array $options = array() ) {
		$syntax_highlighting = new SyntaxHighlighting();
		$syntax_request      = new WP_REST_Request();

		$languages = array(
			'json'  => 'json',
			'array' => 'php',
		);

		$syntax_request->set_param( 'language', $languages[ $output ] );
		$syntax_request->set_param( 'theme', $options['theme'] ?? 'github-light' );
		$syntax_request->set_param( 'code', $unserialized );
        $syntax_request->set_param( 'options', $options );

		$response = $syntax_highlighting->run( $syntax_request );

		return $response->get_data()['syntax_highlighted'] ?? $unserialized;
	}
}
