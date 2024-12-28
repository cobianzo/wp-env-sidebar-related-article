<?php
/**
 * Plugin Name: Aside Related Article Block
 * Description: A custom block for displaying a "Related Article" widget, and it will be displayed inline on the left or right of the content
 * Version: 1.1.0
 * Author: @cobianzo
 * Plugin URI: https://github.com/cobianzo/wp-env-sidebar-related-article
 * Author URI: https://cobianzo.com
 * License: GPLv2 or later
 * Text Domain: aside-related-article-block
 *
 * @package aside-related-article-block
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// for playwright and PHPUnit test we have our own factory data generator
$is_local = str_contains( get_option( 'siteurl' ), 'localhost' );
$is_prod  = 'production' === wp_get_environment_type();
define( 'DUMMY_DATA_GENERATOR', $is_local || ! $is_prod );

require_once plugin_dir_path( __FILE__ ) . 'inc/class-plugin-setup.php';
