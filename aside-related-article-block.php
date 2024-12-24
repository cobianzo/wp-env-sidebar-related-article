<?php
/**
 * Plugin Name: Sidebar Related Article Block
 * Description: A custom block for displaying a "Related Article" widget, and it will be displayed inline on the left of the content
 * Version: 1.0.0
 * Author: @cobianzo
 * Plugin URI: https://github.com/cobianzo/wp-env-sidebar-related-article
 * Author URI: https://cobianzo.com
 * Text Domain: coco
 *
 * @package aside-related-article-block
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class-plugin-setup.php';
