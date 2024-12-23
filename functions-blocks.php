<?php

// Default blocks

class Funcions_Blocks {

	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	public function register_blocks() {
		// Register custom blocks here
		if ( file_exists( __DIR__ . '/build/blocks/' ) ) {
			$block_json_files = glob( __DIR__ . '/build/blocks/*/block.json' );

			// auto register all blocks that were found.
			foreach ( $block_json_files as $filename ) {
				$block_folder = dirname( $filename );
				register_block_type( $block_folder );
			}
		}
	}
}

new Funcions_Blocks();
