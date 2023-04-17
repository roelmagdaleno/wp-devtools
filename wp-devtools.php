<?php

/**
 * Plugin Name:       DevTools by Roel Magdaleno
 * Plugin URI:        https://roelmagdaleno.com
 * Description:       A set of tools for https://devtools.roelmagdaleno.com
 * Version:           0.1.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Roel Magdaleno
 * Author URI:        https://roelmagdaleno.com
 */

use Roel\WP\DevTools\DevTools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

( new DevTools() )->hooks();
