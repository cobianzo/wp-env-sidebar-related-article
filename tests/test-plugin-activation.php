<?php
/**
 * Class PluginActivation
 *
 * @package Wp_Env_Portfolio_Backtrack_Theme
 */

/**
 * Sample test case.
 */
class Test_Plugin_Activation extends WP_UnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		echo PHP_EOL . PHP_EOL . 'TEST 1' . PHP_EOL . '=========' . PHP_EOL ;

		// Ensure all initial actions have been triggered in WordPress.

		// if not running phpUnit in wp env, the plugin might not be activated by default
		$plugin_file = 'aside-related-article-block/aside-related-article-block.php';
		if ( ! is_plugin_active( $plugin_file ) ) {
			echo PHP_EOL . '>>> ⚠️ 2) Needed activation of plugin' . PHP_EOL . '=========' . PHP_EOL ;
			activate_plugin( $plugin_file );
		}
	}

	/**
	 * Test to verify that the plugin activates correctly.
	 */
	public function test_plugin_activation() {
		echo PHP_EOL . PHP_EOL . '1.1) ---- Test for plugin activation' . PHP_EOL;

		// Path to the main plugin file.
		$plugin_name = 'aside-related-article-block/aside-related-article-block.php';
		$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_name;

		// Ensure the plugin file exists.
		$this->assertFileExists( $plugin_file, '❌ FAIL 1.1. The main plugin file does not exist.' );

		// Verify the plugin is active.
		$this->assertTrue(
				is_plugin_active( $plugin_name ),
				'❌ FAIL 1.1: The plugin did not activate correctly.' . PHP_EOL . '---------' . PHP_EOL
			);

		echo PHP_EOL . '✅ OK 1.1: Plugin activated correctly' . PHP_EOL . '---------' . PHP_EOL;

	}

	/**
	 * Test para verificar que el bloque "coco/aside-related-article" está registrado.
	 */
	public function test_function_blocks_for_registration_of_aside_related_article_block() {

		echo PHP_EOL . PHP_EOL . '1.2) ---- Test Functions_blocks, which register our main block' . PHP_EOL;

		// if `init` did not triggered by now, we would need (new Coco\Functions_Blocks())->register_blocks()
		$this->assertTrue(
			WP_Block_Type_Registry::get_instance()->is_registered( 'coco/aside-related-article' ),
			'❌ FAIL 1.2: Functions_Blocks doesnt register coco/aside-related-article.'
		);

		echo PHP_EOL . '✅ OK 1.2: Functions_Blocks registers coco/aside-related-article.' . PHP_EOL . '---------' . PHP_EOL;
	}
}
