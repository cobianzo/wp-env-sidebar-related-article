<?php
/**
 * Class Various
 *
 * @package WP_Env_Portfolio_Backtrack_Theme
 */


class Various {

	/**
	 * Init function
	 */
	public static function init() {
		// Code to run on init
		add_action( 'wp_ajax_example_function', fn() => self::example_function() );
		add_action( 'wp_ajax_nopriv_example_function', fn() => self::example_function() );
	}

	/**
	 * Example function
	 */
	public static function example_function( ?int $input_number = 0 ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( 'example_function' === $action ) {
				if ( ! check_ajax_referer( 'nonce_part-example-template-with-js', 'nonce', false ) ) {
					wp_send_json_error( 'Invalid nonce' );
					exit;
				}
			}
			$input_number = isset( $_POST['number'] ) ? intval( $_POST['number'] ) : 0;
		}

		update_option( 'coco_example_todelete', $input_number );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && 'example_function' === $action ) {
			wp_send_json_success( [ 'updated_value' => $input_number ] );
		}

		return $input_number;
	}
}

// Initialize the class
Various::init();
