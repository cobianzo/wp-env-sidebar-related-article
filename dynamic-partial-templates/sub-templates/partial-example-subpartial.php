<?php


// we load this template with ajax, so we need to prepare it for the ajax request
$args = Dynamic_Partials::get_postdata_as_args_in_template( [ 'number' ] );

$value = isset( $args['number'] ) ? $args['number'] : get_option( 'coco_example_todelete', 0 );

$diameter = $value * 2;

$args = isset( $args ) ? $args : [];

?>

<div class="bg-blue-500 text-white p-4 rounded-lg text-center">
	<div class="text-4xl font-bold">
		<?php echo esc_html( $diameter ); ?>
	</div>
	<div class="text-sm">
		Diameter
	</div>
</div>

<?php
