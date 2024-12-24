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
	 * Helper function used in the CMS editor,
	 * to display a message box at the place of the block.
	 * If we are not in the editor, returns void.
	 *
	 * @param string $message
	 * @return string
	 */
	public static function msg_editor_only( string $message ) : string {
		$is_editor = isset( $_GET['context'] ) && 'edit' === sanitize_text_field( $_GET['context'] );
		if ( ! $is_editor ) {
			return '';
		}
		return '<div class="notice wp-block-inews-related-article-inline__min-height">
					<p class="inews__shortcode-relatedarticleinline__handle">' . wp_kses( $message, array( 'em' => [], 'br' => [] ) ) . '</p>
				</div>
		';
	}

}

// Initialize the class
Various::init();
