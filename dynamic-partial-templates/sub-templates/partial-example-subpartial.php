<?php


$value    = get_option( 'coco_example_todelete' );
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