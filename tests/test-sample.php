<?php
/**
 * Class SampleTest
 *
 * @package Wp_Env_Portfolio_Backtrack_Theme
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {
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
	 * A single example test.
	 */
	public function test_check_simple_assertion() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
}
