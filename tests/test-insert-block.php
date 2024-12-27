<?php

/**
 * Test para verificar que el bloque se inserte correctamente en el contenido
 * de un post.
 */
class InsertBlockTest extends WP_UnitTestCase {

	// Factory class for creating dummy data.
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {

		echo PHP_EOL . PHP_EOL . 'TEST 2' . PHP_EOL . '=========' . PHP_EOL ;

		// if not running phpUnit in wp env, the plugin might not be activated by default
		$plugin_file = 'aside-related-article-block/aside-related-article-block.php';
		if ( ! is_plugin_active( $plugin_file ) ) {
			echo PHP_EOL . '>>> ⚠️ 2) Needed activation of plugin' . PHP_EOL . '=========' . PHP_EOL ;
			activate_plugin( $plugin_file );
		}

		// create dummy data with our own factory class
		Create_Dummy_Data::reset_dummy_data( ['echo' => false] );
		Create_Dummy_Data::create_dummy_terms( ['echo' => false] );
		Create_Dummy_Data::create_dummy_posts( [ 'count' => 5, 'echo' => false ] );

		// Create a test post.
		$factory->post->create([
			'post_title'   => 'Putin and Zelensky play chess in the Kremlin',
			'post_name'    => 'post-container-block-test',
			'post_content' => '',
			'post_status'  => 'publish',
		]);

	}

	/**
	 * Test to verify that the block is inserted correctly in the post content.
	 */
	public function test_insert_coco_aside_related_article_block() {

		echo PHP_EOL . PHP_EOL . '---- 2.1) Test to verify that the block is inserted correctly in the post content' . PHP_EOL;

		// Grab category "Politics" if it does not exist and get its term ID.
		$category = get_category_by_slug( 'politics' );
		$category_id = $category ? $category->term_id : 0;

		// GRab the psot with slug post-container-block-test
		$post = get_page_by_path( 'post-container-block-test', OBJECT, 'post' );
		$post_id = $post->ID;

		// Block attributes.
		$block_attributes = [
			// 'source' => 'category', 'category is the default value, so it SHOULD NOT be included!
			'termID' => $category_id,
			// 'postID' => 0,
		];

		// Create the block in serialized format.
		$block_name = 'coco/aside-related-article';
		$block_content = serialize_block([
			'blockName' => $block_name,
			'attrs'     => $block_attributes,
			'innerHTML' => '',
			'innerBlocks' => [],
			'innerContent' => [],
		]);

		// Insert the block in the post content.
		$current_content = $post->post_content;
		$updated_content = $current_content . "\n\n" . $block_content;
		$result = wp_update_post([
			'ID'           => $post_id,
			'post_content' => $updated_content,
		]);

		if ( is_wp_error( $result ) ) {
			echo '❌ FAIL 2.1: Could not update post ' . $post_id . '. Error: ' . $result->get_error_message() . PHP_EOL . '---------' . PHP_EOL;
			return $result;
		}
		$post = get_post( $result );

		// Get the post content after update.
		$content = trim( $post->post_content );

		// Check that the block is inserted correctly.
		// Parse the blocks from the post content
		$parsed_blocks = parse_blocks($content );

		$this->assertNotEmpty($parsed_blocks, '❌ FAIL 2.1 Parsed blocks should not be empty.');

		// Verify the block structure
		$block = $parsed_blocks[0];

		$this->assertEquals($block_name, $block['blockName'], '❌ FAIL 2.1 Block name should match.');
		$this->assertEquals($block_attributes, $block['attrs'], '❌ FAIL 2.1 Block attributes should match.');

		echo PHP_EOL . '✅ OK 2.1: block insterted correctly in post ' . $post->ID . ': ' . $post->post_title . PHP_EOL
		. $content . PHP_EOL . '---------' . PHP_EOL;
	}
}

