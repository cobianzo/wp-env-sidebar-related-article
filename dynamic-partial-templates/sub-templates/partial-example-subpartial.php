<?php


$value = get_option( 'coco_example_todelete' );

$args = isset( $args ) ? $args : [];
print_r($args);
?>

<p> <?php echo (string) $value; ?> - This is a subpartial. We should receive the data from $args or $_POST</p>


<?php
echo 'fdfd ';

// echo Various::example_function( 10 );
