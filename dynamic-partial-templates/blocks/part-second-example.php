<div class="container mx-auto p-4 border border-gray-300 rounded-lg">
	<form onsubmit="window.dynamicPartials.handleSubmitFormAndLoadTemplateAjax(event)"
				data-dynamic-templates-reload="part-example-template-with-js"
				data-dynamic-subtemplates-reload=""
				class="border rounded flex flex-row gap-3">
				<!-- Fields to make the Dynamic Partials reload the template -->
		<input type="hidden" name="action" value="example_function" >
		<?php wp_nonce_field( 'nonce_part-example-template-with-js', 'nonce' ); ?>
		<input type="hidden" name="number" value="1737100" >

		<button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white text-2xl rounded-lg">
			Set the radius of the moon
		</button>
	</form>
</div>
