<?php

namespace Coco;

/**
 * Class Plugin_Setup. The plugin setup.
 *
 * Setup of the plugin, enqueuing, reference to other files
 *
 * @package aside-related-article-block
 */
class Plugin_Setup {

	/**
	 * Initialize the plugin by setting up hooks and including necessary files.
	 *
	 * @return void
	 */
	public static function init(): void {
		// includes
		require_once __DIR__ . '/inc/class-various.php'; // Helpers
		require_once __DIR__ . '/class-functions-blocks.php'; // regular gutenberg blocks

		// Gutenberg related
		add_action( 'after_setup_theme', function (): void {
			add_theme_support( 'custom-spacing' );
		} );
	}
}

Plugin_Setup::init();
