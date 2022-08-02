<?php

namespace Roel\WP\DevTools\DevTools;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class SyntaxHighlighting {
	/**
	 * The API URL.
	 *
	 * @since 0.1.0
	 */
	const API_URL = 'https://api.torchlight.dev/highlight';

	/**
	 * Run the syntax highlighting functionality.
	 *
	 * It will call to Torchlight API and return a syntax highlighted
	 * of the passed code.
	 *
	 * @since  0.1.0
	 *
	 * @param  WP_REST_Request   $request   The API request.
	 * @return WP_REST_Response|WP_Error    The API response.
	 */
	public function run( WP_REST_Request $request ) {
		$body = $request->get_params();

		if ( empty( $body ) ) {
			return new WP_Error( 'empty-data', 'There is no code to syntax highlight.' );
		}

		if ( empty( $body['language'] ) || empty( $body['theme'] ) || empty( $body['code'] ) ) {
			return new WP_Error( 'empty-data', 'There is no code to syntax highlight.' );
		}

		$args = $this->args( $body );

		if ( empty( $args ) ) {
			return new WP_Error( 'empty-args', 'There is no arguments to send to API.' );
		}

		$response = wp_remote_post( self::API_URL, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response, true );

		return new WP_REST_Response( array(
			'syntax_highlighted' => $response['blocks'][0]['wrapped'],
			'styles'             => $response['blocks'][0]['styles'],
		) );
	}

	/**
	 * Get the request arguments to send to Torchlight.
	 *
	 * @since  0.1.0
	 *
	 * @param  array   $body   The API data to send to Torchlight.
	 * @return array           The API request arguments.
	 */
	protected function args( array $body ) : array {
		$settings = get_option( 'wp_devtools_settings', array() );

		if ( empty( $settings ) || empty( $settings['api_token'] ) ) {
			return array();
		}

		return array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . trim( $settings['api_token'] ),
			),
			'body'    => wp_json_encode( array(
				'blocks'  => array(
					array(
						'language' => $body['language'],
						'theme'    => $body['theme'],
						'code'     => $body['code'],
					),
				),
				'options' => $body['options'] ?? array(),
			) ),
		);
	}
}
