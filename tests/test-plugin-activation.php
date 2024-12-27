<?php
/**
 * Class PluginActivation
 *
 * @package Wp_Env_Portfolio_Backtrack_Theme
 */

/**
 * Sample test case.
 */
class PluginActivation extends WP_UnitTestCase {

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {

		echo PHP_EOL . PHP_EOL . 'TEST 1' . PHP_EOL . '=========' . PHP_EOL ;

		// Ensure all initial actions have been triggered in WordPress.

		$plugin_file = WP_PLUGIN_DIR . '/aside-related-article-block/aside-related-article-block.php';
		activate_plugin( $plugin_file );

		/*
			outside wp-env, for some reason, I need to specify this call, apparently it
			does not call automatically
		 */
		// do_action('init');
	}

	/**
	 * Test to verify that the plugin activates correctly.
	 */
	public function test_plugin_activation() {
		echo PHP_EOL . PHP_EOL . '1.1) ---- Test for plugin activation' . PHP_EOL;

		// Path to the main plugin file.
		$plugin_file = WP_PLUGIN_DIR . '/aside-related-article-block/aside-related-article-block.php';

		// Ensure the plugin file exists.
		$this->assertFileExists( $plugin_file, 'FAIL 1.1. The main plugin file does not exist.' );

		// Verify the plugin is active.
		$this->assertTrue(
				is_plugin_active( 'aside-related-article-block/aside-related-article-block.php' ),
				'FAIL 1.1: The plugin did not activate correctly.' . PHP_EOL . '---------' . PHP_EOL
			);

		echo PHP_EOL . 'OK: Plugin activated correctly' . PHP_EOL . '---------' . PHP_EOL;

	}

	/**
	 * Test para verificar que el bloque "coco/aside-related-article" está registrado.
	 */
	public function test_function_blocks_for_registration_of_aside_related_article_block() {

		echo PHP_EOL . PHP_EOL . '1.2) ---- Test Functions_blocks, which register our main block' . PHP_EOL;

		// if `init` did not triggered by now, we would need (new Coco\Functions_Blocks())->register_blocks()
		$this->assertTrue(
			WP_Block_Type_Registry::get_instance()->is_registered( 'coco/aside-related-article' ),
			'FAIL 1.2: Functions_Blocks doesnt register coco/aside-related-article.'
		);

		echo PHP_EOL . 'OK: Functions_Blocks registers coco/aside-related-article.' . PHP_EOL . '---------' . PHP_EOL;
	}
}
