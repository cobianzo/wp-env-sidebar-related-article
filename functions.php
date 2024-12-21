<?php

/**
 * Custom functions for this theme
 */

class Theme_Setup {

	public static function init(): void {
		// Tailwind related
		add_filter( 'body_class', fn( $classes ) => array( ...$classes, 'tailwind-wp-wrapper' ) );

		add_action( 'wp_enqueue_scripts', function() {
			self::tailwind_wp_theme_enqueue_frontend_styles();
			self::enqueue_generic_public_script();
		} );
		add_action( 'after_setup_theme', array( __CLASS__, 'tailwind_wp_theme_enqueue_editor_styles' ) );

		// Gutenberg related
		add_action( 'after_setup_theme', function () {
			add_theme_support( 'custom-spacing' );
		} );


		// default for any theme
		require_once __DIR__ . '/dynamic-partials-plugin/class-dynamic-partials.php';
		require_once __DIR__ . '/functions-blocks.php';


		require_once __DIR__ . '/inc/class-theme-frontend.php';
		require_once __DIR__ . '/inc/class-various.php';

	}

	/**
	 * Enqueue styles for the frontend.
	 *
	 * @since portfolio-theme 1.0
	 *
	 * @return void
	 */
	public static function tailwind_wp_theme_enqueue_frontend_styles(): void {
		wp_register_style(
			'portfolio-theme-style',
			get_template_directory_uri() . '/build/tailwind-style.css',
			array(),
			wp_get_theme()->get( 'Version' ),
			false
		);

		wp_enqueue_style( 'portfolio-theme-style' );
	}

	/**
	 * We want tailwind styles also in the editor
	 *
	 * @since portfolio-theme 1.0
	 *
	 * @return void
	 */
	public static function tailwind_wp_theme_enqueue_editor_styles(): void {
		add_editor_style( get_template_directory_uri() . '/build/tailwind-style.css' );
	}

	public static function enqueue_generic_public_script(): void {
		wp_register_script(
			'portfolio-theme-public-generic',
			get_template_directory_uri() . '/build/public-generic.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_enqueue_script( 'portfolio-theme-public-generic' );
	}

}
Theme_Setup::init();



add_action( 'wp_ajax_todelete_example_function', function() {
	wp_send_json_success( [ 'message' => 'Function executed successfully' ] );
} );
add_action( 'wp_ajax_nopriv_todelete_example_function', function() {
	wp_send_json_success( [ 'message' => 'Function executed successfully' ] );
} );

