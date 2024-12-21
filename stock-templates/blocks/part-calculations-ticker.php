<?php

// Allow show the form for a ticker passed by $arg, or by
// the query arg of the current template (single page of a stock)
if ( is_singular( 'stock' ) ) {
	$symbol = strtoupper( get_post()->post_name );
}
// TO_DO: if we use ajax we can get the symbol from the $_POST
$symbol = isset( $args['symbol'] ) ? $args['symbol'] : $symbol;

if ( empty( $symbol ) ) {
	echo '<p class="text-red-600">Error: Symbol not provided</p>';
	return;
}


$calculations = Stock_Calculations::calculations_for_ticker( $symbol );
?>

<h3>Calculations</h3>
Calcualtions for the portfolio of the current us

<?php
$options = [
	'titles_map' => [
		'Title'              => 'year',
		'value_if_sell'      => 'Total Portfolio Value',
		'total_gain'         => 'Acc. gain',
		'total_contribution' => 'Total contributions',
	],

];
$table = Stock_Frontend::generate_table_html( $calculations, $options );
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $table;
?>
