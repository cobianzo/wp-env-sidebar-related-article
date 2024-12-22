<?php

/**
 * Title: Example
 * Slug:
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      portfolio-theme 1.0.1
 */
$file_name = basename(__FILE__);
?>
<div <?php echo get_block_wrapper_attributes([
				'class' => basename($file_name, '.php') . ' container mx-auto p-4 shadow-lg'
			]); ?>>

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button">
			<a id="sample-button" class="wp-block-button__link wp-element-button">Sample button GB</a>
		</div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
