<?php

$calculations = Stock_Calculations::calculations_portfolio();

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
