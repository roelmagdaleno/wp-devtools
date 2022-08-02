<?php

namespace Roel\WP\DevTools;

use Roel\WP\DevTools\DevTools\{
	Unserialize,
	SyntaxHighlighting,
};

class DevTools {
	/**
	 * Initialize the hooks to run the functionality.
	 *
	 * @since 0.1.0
	 */
	public function hooks() : void {
		if ( is_admin() ) {
			( new SettingsPage() )->hooks();
		}

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for devtools.
	 * Every devtools will return a JSON response with the required data.
	 *
	 * @since 0.1.0
	 */
	public function register_routes() {
		$routes = array(
			'/unserialize'         => array(
				'methods'             => 'POST',
				'callback'            => array( new Unserialize(), 'run' ),
				'permission_callback' => '__return_true',
			),
			'/syntax-highlighting' => array(
				'methods'             => 'POST',
				'callback'            => array( new SyntaxHighlighting(), 'run' ),
				'permission_callback' => '__return_true',
			),
		);

		foreach ( $routes as $route => $args ) {
			register_rest_route( 'wp-devtools/v1', $route, $args );
		}
	}
}
