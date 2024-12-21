<?php
// =============== ================
// Helpers for debugging. Use this instead of print_r or wp_die
// use it only in development mode.
function dd( $var_arg ): void {
	if ( wp_get_environment_type() === 'production' ) {
		return;
	}
	echo '<pre>';
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
	print_r( $var_arg );
	echo '</pre>'; }
function ddie( $var_arg ): void {
	if ( wp_get_environment_type() === 'production' ) {
		return;
	}
	dd( $var_arg );
	wp_die();
}


// Test 1 - the API for Yahoo Finance
if ( isset( $_GET['w-test'] ) && $_GET['w-test'] === '1' ) :
	require_once get_template_directory() . '/inc/stocks-apis/class-external-api-calls.php';
	$p = External_API_Calls::get_instance()->search_tickers( 'MICRO', true );
	ddie( $p );
endif;

// Test scrap with
if ( isset( $_GET['w-test'] ) && $_GET['w-test'] === '2' ) :

	require_once get_template_directory() . '/inc/stocks-apis/class-external-api-calls.php';
	$p = External_API_Calls::get_instance()->get_dividends_years_range( 'JNJ' );
	ddie( $p );


endif;

// Test show tabulated data
if ( isset( $_GET['w-test'] ) && $_GET['w-test'] === '3' ) :

	$json_data = '{"2000":{"divs":1.04,"price_end":100,"price_start":85.439999999999998,"divs_increment":0,"last_div_date":0,"yield":1.22},"2001":{"divs":1.0399999999999998,"price_end":58.25,"price_start":93.129999999999995,"divs_increment":-0,"last_div_date":0,"yield":1.1200000000000001},"2002":{"divs":0.79499999999999993,"price_end":57.020000000000003,"price_start":58.329999999999998,"divs_increment":-23.559999999999999,"last_div_date":0,"yield":1.3600000000000001},"2003":{"divs":0.92499999999999993,"price_end":49.299999999999997,"price_start":53.5,"divs_increment":16.350000000000001,"last_div_date":0,"yield":1.73},"2004":{"divs":1.0949999999999998,"price_end":60.32,"price_start":53.409999999999997,"divs_increment":18.379999999999999,"last_div_date":0,"yield":2.0499999999999998},"2005":{"divs":1.2750000000000001,"price_end":61.75,"price_start":64.609999999999999,"divs_increment":16.440000000000001,"last_div_date":0,"yield":1.97},"2006":{"divs":1.4550000000000001,"price_end":65.909999999999997,"price_start":57.670000000000002,"divs_increment":14.119999999999999,"last_div_date":0,"yield":2.52},"2007":{"divs":1.6200000000000001,"price_end":67.739999999999995,"price_start":66.799999999999997,"divs_increment":11.34,"last_div_date":0,"yield":2.4300000000000002},"2008":{"divs":1.7949999999999999,"price_end":58.579999999999998,"price_start":63.310000000000002,"divs_increment":10.800000000000001,"last_div_date":0,"yield":2.8399999999999999},"2009":{"divs":1.9299999999999999,"price_end":62.840000000000003,"price_start":57.409999999999997,"divs_increment":7.5199999999999996,"last_div_date":0,"yield":3.3599999999999999},"2010":{"divs":2.1100000000000003,"price_end":61.549999999999997,"price_start":63.259999999999998,"divs_increment":9.3300000000000001,"last_div_date":0,"yield":3.3399999999999999},"2011":{"divs":2.2499999999999996,"price_end":64.719999999999999,"price_start":59.700000000000003,"divs_increment":6.6399999999999997,"last_div_date":0,"yield":3.77},"2012":{"divs":2.3999999999999999,"price_end":69.730000000000004,"price_start":65.969999999999999,"divs_increment":6.6699999999999999,"last_div_date":0,"yield":3.6400000000000001},"2013":{"divs":2.5900000000000003,"price_end":94.659999999999997,"price_start":74.140000000000001,"divs_increment":7.9199999999999999,"last_div_date":0,"yield":3.4900000000000002},"2014":{"divs":2.7599999999999998,"price_end":108.25,"price_start":88.75,"divs_increment":6.5599999999999996,"last_div_date":0,"yield":3.1099999999999999},"2015":{"divs":2.9500000000000002,"price_end":101.23999999999999,"price_start":100.48999999999999,"divs_increment":6.8799999999999999,"last_div_date":0,"yield":2.9399999999999999},"2016":{"divs":3.1500000000000004,"price_end":111.3,"price_start":103.61,"divs_increment":6.7800000000000002,"last_div_date":0,"yield":3.04},"2017":{"divs":3.3199999999999998,"price_end":139.33000000000001,"price_start":112.48,"divs_increment":5.4000000000000004,"last_div_date":0,"yield":2.9500000000000002},"2018":{"divs":3.54,"price_end":146.90000000000001,"price_start":137.53,"divs_increment":6.6299999999999999,"last_div_date":0,"yield":2.5699999999999998},"2019":{"divs":3.75,"price_end":137.49000000000001,"price_start":134.02000000000001,"divs_increment":5.9299999999999997,"last_div_date":0,"yield":2.7999999999999998},"2020":{"divs":3.9799999999999995,"price_end":144.68000000000001,"price_start":149.41999999999999,"divs_increment":6.1299999999999999,"last_div_date":0,"yield":2.6600000000000001},"2021":{"divs":4.1900000000000004,"price_end":155.93000000000001,"price_start":165.31,"divs_increment":5.2800000000000002,"last_div_date":0,"yield":2.5299999999999998},"2022":{"divs":4.4499999999999993,"price_end":178,"price_start":171.74000000000001,"divs_increment":6.21,"last_div_date":0,"yield":2.5899999999999999},"2023":{"divs":4.6999999999999993,"price_end":154.66,"price_start":162.99000000000001,"divs_increment":5.6200000000000001,"last_div_date":0,"yield":2.8799999999999999},"2024":{"divs":4.9100000000000001,"price_end":155.00999999999999,"price_start":158.16,"divs_increment":4.4699999999999998,"last_div_date":0,"yield":3.1000000000000001}}';

	$divs = json_decode( $json_data, true );

	$html = Stock_Frontend::generate_table( $divs );

	ddie( $html );
endif;

if ( isset( $_GET['w-test'] ) && $_GET['w-test'] === '4' ) :

	add_action('init', function() {
		$historical_jnj = Stock_Calculations::calculations_for_ticker( 'JNJ' );

		$html = Stock_Frontend::generate_table_html( $historical_jnj );
		ddie($historical_jnj);
		ddie( $html );
	});
endif;

