<?php
// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound

namespace Coco;

/**
 * Class Various
 *
 * @package aside-related-article-block
 */
class Various {

	// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	/**
	 * Helper function used in the CMS editor,
	 * to display a message box at the place of the block.
	 * If we are not in the editor, returns void.
	 *
	 * @param string $message
	 * @return string
	 */
	public static function msg_editor_only( string $message ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
