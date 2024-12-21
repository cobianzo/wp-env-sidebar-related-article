<?php
/**
 * Title: Example
 * Slug:
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      portfolio-theme 1.0.1
 */

?>
<div <?php echo get_block_wrapper_attributes( [
	'class' => 'part-example-template-with-js container mx-auto p-4 shadow-lg'
] ); ?>>
	<div class="flex flex-row gap-4">
		<div class="flex-grow">
			<form onsubmit="window.dynamicPartials.handleSubmitFormAndLoadTemplateAjax(event)"
				data-dynamic-templates-reload=""
				data-dynamic-subtemplates-reload="partial-example-subpartial"
				class="border rounded flex flex-row gap-3">
				<!-- Fields to make the Dynamic Partials reload the template -->
				<input type="hidden" name="action" value="example_function" >
				<?php wp_nonce_field( 'nonce_part-example-template-with-js', 'nonce' ); ?>
				<!-- end field for reloading templates after ajax -->

				<!-- ui -->
				<div class="mb-4 flex flex-col">
					<label for="number" class="block text-gray-700 text-sm font-bold mb-2">Radius:</label>
					<input type="number" id="number" name="number"
					class="radius-input shadow text-center appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-auto text-xl"
					value="<?php echo esc_attr( get_option( 'coco_example_todelete', 0 ) ); ?>">
				</div>
				<div class="flex items-center justify-between">
					<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
						Submit, save in the DB and automatically update the right column
					</button>
				</div>
			</form>
		</div>
		<!-- End Left column -->



		<!-- Right column content goes here -->
		<div class="flex-shrink-0">
			<?php
				$current_value = get_option( 'coco_example_todelete', 0 );
				Dynamic_Partials::get_dynamic_partial_template_part( 'partial-example-subpartial',
					[ 'class' => 'flex justify-center items-center h-full' ],
					[ 'number' => $current_value ]
				);
			?>
		</div>
	</div>
</div>

<!-- <button onclick="window.test();">Buttons TEST</button> -->
