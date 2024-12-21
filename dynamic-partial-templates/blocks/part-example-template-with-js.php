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
<div class="container mx-auto p-4 shadow-lg">
	<div class="grid grid-cols-2 gap-4">
		<div>
			<form onsubmit="window.dynamicPartials.handleSubmitFormAndLoadTemplateAjax(event)"
				data-dynamic-templates-reload=""
				data-dynamic-subtemplates-reload="partial-example-subpartial"
				class="w-full">
				<!-- Fields to make the Dynamic Partials reload the template -->
				<input type="hidden" name="action" value="example_function" >
				<?php wp_nonce_field( 'nonce_part-example-template-with-js', 'nonce' ); ?>
				<!-- end field for reloading templates after ajax -->

				<!-- ui -->
				<div class="mb-4">
					<label for="number" class="block text-gray-700 text-sm font-bold mb-2">Number:</label>
					<input type="number" id="number" name="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
				</div>
				<div class="flex items-center justify-between">
					<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
						Submit
					</button>
				</div>
			</form>
		</div>
		<div>
			<!-- Right column content goes here -->
			<?php
				$current_value = get_option( 'coco_example_todelete', 0 );
				Dynamic_Partials::get_dynamic_partial_template_part( 'partial-example-subpartial', [ 'number' => $current_value ] );
			?>
		</div>
	</div>
</div>

<button onclick="window.test();">Buttons TEST</button>
