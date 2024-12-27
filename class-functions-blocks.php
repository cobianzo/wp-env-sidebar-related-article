<?php
/**
 * Class Functions_Blocks
 *
 * Register the blocks on the php side.
 * Any block inside /src/blocks/<block-name>/block.js will be compiled into /build/blocks/<block-nams>
 * Check /src/blocks/*\/index.js for the registration in the js side.
 */

namespace Coco;

class Functions_Blocks {

	/**
	 * Constructor. Hooks call
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Registration of the blocks in the frontend.
	 *
	 * @return void
	 */
	public function register_blocks(): void {

		// Register custom blocks here
		if ( file_exists( __DIR__ . '/build/blocks/' ) ) {
			$block_json_files = glob( __DIR__ . '/build/blocks/*/block.json' );

			if ( false === $block_json_files ) {
				wp_die( 'error scanning folder ' . __DIR__ . '/build/blocks/*/block.json' );
			}

			// auto register all blocks that were found.
			foreach ( $block_json_files as $filename ) {
				$block_folder = dirname( $filename );
				register_block_type( $block_folder );
			}
		}
	}
}

new Functions_Blocks();
