<?php

class Stock_Calculations {

	// hooks
	public static function init() {
		// Initialize class properties
	}
	public static function get_first_year( $ticker ) {
		// for the current user, get the first year with contributions
		$valid = User_Controller::is_in_current_user_portfolio( $ticker );
		if ( ! $valid ) {
			return null;
		}
		$contributions = User_Controller::get_all_contributions_ticker( $ticker );
		if ( ! is_array( $contributions ) ) {
			return null;
		}

		return array_keys( $contributions )[0];
	}

	public static function calculations_for_ticker( $ticker, $options = [] ) {
		$options         = array_merge( [
			'reinvesting_divs' => true,
		], $options );
		$calculations    = [];
		$initial_year    = self::get_first_year( $ticker );
		$historical_data = Stock_Model::get_stock_historical( $ticker );
		$contributions   = User_Controller::get_all_contributions_ticker( $ticker );

		// Start from the initial year
		for ( $year = $initial_year; $year <= max( array_keys( $historical_data ) ); $year++ ) {

			// Initialize previous year's values or default to 0
			$prev_position           = isset( $calculations[ $year - 1 ]['total_position'] )
			? $calculations[ $year - 1 ]['total_position']
			: 0;
			$prev_total_contribution = isset( $calculations[ $year - 1 ]['total_contribution'] )
			? $calculations[ $year - 1 ]['total_contribution']
			: 0;
			$prev_total_gain         = isset( $calculations[ $year - 1 ]['total_gain'] )
			? $calculations[ $year - 1 ]['total_gain']
			: 0;

			// Check if data exists for this year
			if ( ! isset( $historical_data[ $year ] ) ) {
				// If no data, copy previous year's data
				$calculations[ $year ] = isset( $calculations[ $year - 1 ] ) ? $calculations[ $year - 1 ] : [];
				continue;
			}

			// Contribution for the current year
			$contribution = isset( $contributions[ $year ] ) ? $contributions[ $year ] : 0;
			if ( $options['reinvesting_divs'] ) {
				$prev_gain_in_divs = isset( $calculations[ $year - 1 ]['gain'] ) ? $calculations[ $year - 1 ]['gain'] : 0;
				$contribution      = $contribution + $prev_gain_in_divs;
			}

			// Calculate position (number of stocks bought this year)
			$price_start = $historical_data[ $year ]['price_start'];
			$price_end   = $historical_data[ $year ]['price_end'];
			$position    = $contribution > 0 ? floor( $contribution / $price_start ) : 0;

			// Total position (cumulative)
			$total_position = $prev_position + $position;

			// Total contribution (cumulative)
			$total_contribution = $prev_total_contribution + $contribution;

			// Gain from dividends this year
			$divs = $historical_data[ $year ]['divs'];
			$gain = $total_position * $divs;

			// Total gain (cumulative)
			$total_gain = $prev_total_gain + $gain;

				$value_if_sell = $price_end * $total_position;

			// Store calculations for this year.
				// Assuming the contribution has been made at the beginning of the year
			$calculations[ $year ] = [
				'position'           => $position,
				'contribution'       => $contribution,
				'total_position'     => $total_position,
				'total_contribution' => $total_contribution,
				'gain'               => $gain,
				'total_gain'         => $total_gain,
				'price_start'        => $price_start,
				'value_if_sell'      => $value_if_sell, // if sell at the end of the year
				'divs'               => $divs,
			];
		}

		return $calculations;
	}

	public static function calculations_portfolio( $user_id = null ) {
		$user_id   = ( null === $user_id ) ? get_current_user_id() : (int) $user_id;
		$portfolio = User_Controller::get_current_user_portfolio( $user_id );

		$calculations_per_ticker = [];
		$first_year              = gmdate( 'Y' );
		foreach ( $portfolio as $ticker ) {
			$calculations_per_ticker[ $ticker ] = self::calculations_for_ticker( $ticker );
			$first_year                         = min( $first_year, self::get_first_year( $ticker ) );
		}

		$calculations = [];
		for ( $year = $first_year; $year <= gmdate( 'Y' ); $year++ ) {
			$calculations[ $year ] = [
				'value_if_sell'      => 0,
				'total_gain'         => 0,
				'total_contribution' => 0,
			];
			foreach ( $portfolio as $ticker ) {
				if ( ! isset( $calculations_per_ticker[ $ticker ][ $year ] ) ) {
					continue;
				}
				$calculations[ $year ]['value_if_sell']      += $calculations_per_ticker[ $ticker ][ $year ]['value_if_sell'];
				$calculations[ $year ]['total_gain']         += $calculations_per_ticker[ $ticker ][ $year ]['total_gain'];
				$calculations[ $year ]['total_contribution'] += $calculations_per_ticker[ $ticker ][ $year ]['total_contribution'];
			}
		}

		return $calculations;
	}
}

Stock_Calculations::init();
