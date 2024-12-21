<?php

/**
 * Singleton for making calls to external sources to retireve the data we need.
 * We connect to Yahoo
 * Usage: $stocks_api = External_API_Calls::get_instance();
 */
class External_API_Calls {

	/**
	 * The one true External_API_Calls instance
	 *
	 * @var External_API_Calls
	 */
	private static $instance;

	/**
	 * The base URL for the API
	 *
	 * @var string
	 */
	private $yahoo_base_url        = 'https://query2.finance.yahoo.com/v1/finance';
	private $alphavantage_base_url = 'https://www.alphavantage.co/query';

	/**
	 * Get the instance of External_API_Calls.
	 */
	public static function get_instance(): static {
		if ( ! self::$instance ) {
			self::$instance = new self();
			// use any init if we need to init things once the instance is gnerated.
		}

		return self::$instance;
	}

	/**
	 * Initialize the class.
	 */
	protected function __construct() {
	}

	/**
	 * Searches for tickers based on a search term
	 * Usage: External_API_Calls::get_instance()->search_tickers( 'GOOGL' );
	 * Type of ticker info: { "exchange": "NMS", "shortname": "Apple Inc.", "quoteType": "EQUITY",
	 * "symbol": "AAPL", "index": "quotes", "score": 2953800, "typeDisp": "Equity",
	 * "longname": "Apple Inc.","symbol": "AAPL" }
	 *
	 * @param string $search_term Término de búsqueda
	 * @param boolean $single Returns one ticket and only if is exactly the same as the term
	 * @return array|object|null|false of tickers in $single is false. Just one Object ticker if @single is true.
	 * if error returns null, if nothing found returns empty array if $single is false,or false if $single is true.
	 */
	public function search_tickers( string $search_term, bool $single = false ): array|object|null|false {
			$args = array(
				'q'           => $search_term,
				'quotesCount' => 100, // this can be important
				'newsCount'   => 0,
				// 'enableFuzzyQuery' => false,
				// 'quotesQueryId'    => 'tss_match_phrase_query',
			);

			$url = add_query_arg( $args, $this->yahoo_base_url . '/search' );

			// TODO: use cache or transients.
			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
			$response = wp_remote_get( $url, [
				'timeout' => 3,
				'headers' => array( 'Accept' => 'application/json' ),
			] );

		if ( is_wp_error( $response ) ) {
				return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		// Yahoo returns the list of tickers inside of 'quotes'. We only want the EQUITY, not options, futures...
		$results = ! empty( $data['quotes'] ) ? $data['quotes'] : array();
		$results = array_filter( $results, fn( $result ) => 'EQUITY' === $result['quoteType'] );
		$results = array_values( $results );

		if ( $single ) {
			$results = array_filter( $results, fn( $result ) => $result['symbol'] === $search_term );
			return count( $results ) ? $results[0] : false;
		}
		return $results;
	}

	// WIP. Can be improved. Include caching.
	public function get_dividends_years_range( string $symbol, bool $use_db = true ) {

		// Check if we have the info already in local, in the CPT 'stock'
		if ( $use_db ) {
			$data = Stock_Model::get_stock_historical( $symbol );
			if ( false !== $data && is_array( $data ) ) {
				return $data;
			}
		}

		$args = array(
			'function' => 'TIME_SERIES_MONTHLY_ADJUSTED',
			'symbol'   => $symbol,
			'apikey'   => 'TU_API_KEY', // @TODO: we can use a API key associated to the user.
		);
		$url  = add_query_arg( $args, $this->alphavantage_base_url );

		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
		$response = wp_remote_get( $url, [
			'timeout' => 3,
			'headers' => array( 'Accept' => 'application/json' ),
		] );

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		$summary = [];
		if ( isset( $data['Monthly Adjusted Time Series'] ) ) {
			$dates = $data['Monthly Adjusted Time Series'];
			// Reverse the dates to move from the oldest to the newest
			$dates = array_reverse( $dates, true );
			// most of stocks start in 2000
			foreach ( $dates as $date => $date_info ) {
				$dividend = (float) $date_info['7. dividend amount'];
				if ( $dividend > 0 ) {
					$timestamp = strtotime( $date );
					$year      = (int) gmdate( 'Y', $timestamp );
					if ( ! isset( $summary[ $year ] ) ) {
						// init
						$summary[ $year ] = [
							'divs'           => $dividend,
							'price_end'      => (float) $date_info['4. close'], // we'll update it on every new item of year
							'price_start'    => (float) $date_info['1. open'],
							'divs_increment' => 0,
							'last_div_date'  => 0,
						];
					} else {
						// update
						$summary[ $year ]['divs']         += $dividend;
						$summary[ $year ]['price_end']     = (float) $date_info['4. close'];
						$summary[ $year ]['last_div_date'] = $date;
					}

					// calculate increment in divs
					$previous_year                      = $year - 1;
					$increment                          = isset( $summary[ $previous_year ] )
						? round( ( ( $summary[ $year ]['divs'] - $summary[ $previous_year ]['divs'] ) / $summary[ $previous_year ]['divs'] ) * 100, 2 )
						: 0;
					$summary[ $year ]['divs_increment'] = (float) $increment;

					// calculate yield. % of divs respect the price at the beginning of the year.
					$summary[ $year ]['yield'] = round( ( $summary[ $year ]['divs'] / $summary[ $year ]['price_start'] ) * 100, 2 );
				}
			}
		}

		// with this hook we can update the db with the values.
		do_action( 'stock_historical_data_updated', $symbol, $summary );

		// keep this results, saving them in our DB @TODO: delete and apply in the hoook action.
		Stock_Model::create_stock_post( $symbol );
		Stock_Model::update_stock_historical( $symbol, $summary );

		return $summary;
	}
}
