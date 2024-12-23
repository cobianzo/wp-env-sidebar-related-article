<?php

/**
 * Custom functions for this plugin
 */

class Plugin_Setup {

	public static function init(): void {

		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_generic_public_script' ] );

		// Gutenberg related
		add_action( 'after_setup_theme', function () {
			add_theme_support( 'custom-spacing' );
		} );

		require_once __DIR__ . '/inc/class-various.php'; // Helpers
		require_once __DIR__ . '/functions-blocks.php'; // regular gutenberg blocks

	}


	public static function enqueue_generic_public_script(): void {
		$asset_file = include plugin_dir_path( __FILE__ ) . 'build/public-generic.asset.php';
		wp_register_script(
			'plugin-public-script',
			plugins_url( '/build/public-generic.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_enqueue_script( 'plugin-public-script' );

	}
}
Plugin_Setup::init();
