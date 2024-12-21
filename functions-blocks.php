<?php 

// Default blocks

class Funcions_Blocks {
	
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	public function register_blocks() {
		// Register custom blocks here
		if ( file_exists( __DIR__ . '/build/blocks/' ) ) {
			$interactive_block_json_files     = glob( __DIR__ . '/build/blocks/interactive/*/block.json' );
			$non_interactive_block_json_files = glob( __DIR__ . '/build/blocks/non-interactive/*/block.json' );
			$block_json_files                 = array_merge( $interactive_block_json_files, $non_interactive_block_json_files );
	
			// auto register all blocks that were found.
			foreach ( $block_json_files as $filename ) {
				$block_folder = dirname( $filename );
				register_block_type( $block_folder );
			}
		}
	}
}

new Funcions_Blocks();
