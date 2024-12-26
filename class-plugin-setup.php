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

		// When running Playwright tests, we want to create dummy data quickly with this new page
		if ( 'production' !== wp_get_environment_type() && defined( 'DUMMY_DATA_GENERATOR' ) && DUMMY_DATA_GENERATOR ) {
			require_once __DIR__ . '/tests/class-create-dummy-data.php'; // Create Dummy Data Page
		}

		// Gutenberg related
		add_action( 'after_setup_theme', function (): void {
			add_theme_support( 'custom-spacing' );
		} );
	}
}

Plugin_Setup::init();
