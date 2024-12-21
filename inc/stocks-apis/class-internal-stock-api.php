<?php

/**
 * Create the endponts for js internal use, which call Yahoo API
 *
 */
class Internal_Stock_API {

	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_endpoints' ) );
	}

	public static function register_endpoints(): void {

		require_once 'class-external-api-calls.php';

		// wp-json/stock-api/search?ticker=AAPL[&unique=true]
		register_rest_route( 'stock-api', '/search',
			array(
				'methods'             => 'GET',
				'callback'            => function ( \WP_REST_Request $request ) {
					$stocks_external_api = External_API_Calls::get_instance();
					// TODO: use cache or transients.
					$tickers = $stocks_external_api->search_tickers( $request->get_param( 'ticker' ), $request->get_param( 'unique' ) );
					return $tickers;
				},
				'permission_callback' => '__return_true',
				'args'                => array(
					'ticker' => array(
						'required'          => true,
						'sanitize_callback' => fn( $ticker ) => strtoupper( sanitize_text_field( $ticker ) ),
					),
					'unique' => array(
						'required'          => false,
						'default'           => false,
						'sanitize_callback' => fn( $unique ) => filter_var( $unique, FILTER_VALIDATE_BOOLEAN ),
					),
				),
			)
		);

		// wp-json/stock-api/divs?ticker=AAPL
		// WHEN: when clicking on the Show results button.
		register_rest_route( 'stock-api', '/divs',
			array(
				'methods'             => 'GET',
				'callback'            => function ( \WP_REST_Request $request ) {
					$stocks_external_api = External_API_Calls::get_instance();
					$divs = $stocks_external_api->get_dividends_years_range( $request->get_param( 'ticker' ) );
					return $divs;
				},
				'permission_callback' => '__return_true',
				'args'                => array(
					'ticker' => array(
						'required'          => true,
						'sanitize_callback' => fn( $ticker ) => strtoupper( sanitize_text_field( $ticker ) ),
					),
				),
			)
		);
	}
}

Internal_Stock_API::init();
