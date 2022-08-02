<?php

namespace Roel\WP\DevTools;

use Roel\WP\Settings\Group;
use Roel\WP\Settings\Elements\{
	Text,
};

class SettingsPage {
	/**
	 * The page slug.
	 *
	 * @since 0.1.0
	 *
	 * @var   string   $page   The page slug.
	 */
	protected string $page = 'wp-devtools';

	/**
	 * Initialize the hooks that will run the Giscus functionality.
	 *
	 * @since 0.1.0
	 */
	public function hooks() : void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register the submenu page.
	 * The submenu page will include the plugin's options page.
	 *
	 * @since 0.1.0
	 */
	public function register_menu() : void {
		add_submenu_page(
			'options-general.php',
			'WP DevTools',
			'WP DevTools',
			'manage_options',
			$this->page,
			array( $this, 'render' )
		);
	}

	/**
	 * Register the settings to render in the options page.
	 * Each setting must be a component instance.
	 *
	 * @since 0.1.0
	 */
	public function register_settings() : void {
		$setting     = 'wp_devtools';
		$option_name = $setting . '_settings';

		register_setting( $setting . '_group', $option_name );

		add_settings_section(
			$this->page,
			'Settings',
			null,
			$this->page
		);

		$group = new Group( array(
			new Text( 'api_token', array(
				'label'       => 'Torchlight API Token',
				'description' => 'Your Torchlight API Token.',
			) ),
		), $option_name );

		foreach ( $group->elements() as $setting ) {
			add_settings_field(
				$setting->id(),
				'<label for="' . $setting->id() . '">' . $setting->label() . '</label>',
				array( $setting, 'print' ),
				$this->page,
				$this->page
			);
		}
	}

	/**
	 * Render the plugin's options page.
	 * It contains all plugin's settings.
	 *
	 * @since 0.1.0
	 */
	public function render() : void {
		include_once dirname( __DIR__ ) . '/admin/views/settings-page.php';
	}
}
