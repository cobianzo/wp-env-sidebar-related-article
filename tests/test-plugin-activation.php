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
	protected static $admin_id;
	protected static $editor_id;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$admin_id  = $factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		self::$editor_id = $factory->user->create(
			array(
				'role' => 'editor',
			)
		);
	}

	/**
	 * Test to verify that the plugin activates correctly.
	 */
	public function test_plugin_activation() {
		echo PHP_EOL . '1) Test for plugin activation';

		// Path to the main plugin file.
		$plugin_file = WP_PLUGIN_DIR . '/aside-related-article-block/aside-related-article-block.php';

		// Ensure the plugin file exists.
		$this->assertFileExists( $plugin_file, 'The main plugin file does not exist.' );

		// Activate the plugin.
		activate_plugin( $plugin_file );

		// Verify the plugin is active.
		$this->assertTrue(
				is_plugin_active( 'aside-related-article-block/aside-related-article-block.php' ),
				'FAIL: The plugin did not activate correctly.'
			);

		echo PHP_EOL . 'OK: Plugin activated correctly';

	}

		/**
     * Test para verificar que el bloque "coco/aside-related-article" está registrado.
     */
    public function test_function_blocks_for_registration_of_aside_related_article_block() {
			echo PHP_EOL . '2) Test Functions_blocks, which register our main block';
			// Ruta al archivo principal del plugin.
			$plugin_file = WP_PLUGIN_DIR . '/aside-related-article-block/aside-related-article-block.php';

			// Activa el plugin.
			activate_plugin( $plugin_file );

			// $fb = new Coco\Functions_Blocks();
			// $fb->register_blocks();
			$this->assertTrue(
				WP_Block_Type_Registry::get_instance()->is_registered( 'coco/aside-related-article' ),
				'FAIL: Functions_Blocks doesnt register coco/aside-related-article.'
			);

			echo PHP_EOL . 'OK: Functions_Blocks registers coco/aside-related-article.';

	}
}
