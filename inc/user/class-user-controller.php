<?php

/**
 * Actions relative to the Model User, in wp_user data.
 * Including CRUD to add/remove portfolio for the user.
 */
class User_Controller {

	/**
	 * Init the class, hooking the actions.
	 *
	 * @return void
	 */
	public static function init(): void {

		// ticker associated to user
		add_action( 'wp_ajax_add_to_current_user_portfolio', [ __CLASS__, 'add_to_portfolio_ajax' ] );
		add_action( 'wp_ajax_nopriv_add_to_current_user_portfolio', [ __CLASS__, 'add_to_portfolio_ajax' ] );
		add_action( 'wp_ajax_remove_from_current_user_portfolio', [ __CLASS__, 'remove_from_portfolio_ajax' ] );
		add_action( 'wp_ajax_nopriv_remove_from_current_user_portfolio', [ __CLASS__, 'remove_from_portfolio_ajax' ] );

		// contributions for the user, by ticker and year.
		add_action( 'wp_ajax_add_contribution_year', [ __CLASS__, 'add_contribution_year' ] );
		add_action( 'wp_ajax_nopriv_add_contribution_year', [ __CLASS__, 'add_contribution_year' ] );
		add_action( 'wp_ajax_remove_contribution_year', [ __CLASS__, 'remove_contribution_year' ] );
		add_action( 'wp_ajax_nopriv_remove_contribution_year', [ __CLASS__, 'remove_contribution_year' ] );
	}

	/**
	 * Add the ticker to the current user's portfolio via AJAX.
	 *
	 * @return void
	 */
	public static function add_to_portfolio_ajax(): void {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'dynamic_blocks_nonce_action' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
		}

		$ticker = isset( $_POST['ticker'] ) ? sanitize_text_field( $_POST['ticker'] ) : '';
		if ( empty( $ticker ) ) {
			wp_send_json_error( [ 'message' => 'Invalid ticker' ] );
		}

		$success = self::add_to_current_user_portfolio( $ticker );
		if ( $success ) {
			wp_send_json_success( [ 'message' => 'Added to portfolio' ] );
		} else {
			wp_send_json_error( [ 'message' => 'User not logged in' ] );
		}
	}

	/**
	 * Remove the ticker from the current user's portfolio via AJAX.
	 *
	 * @return void
	 */
	public static function remove_from_portfolio_ajax(): void {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'dynamic_blocks_nonce_action' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
		}

		$ticker = isset( $_POST['ticker'] ) ? sanitize_text_field( $_POST['ticker'] ) : '';
		if ( empty( $ticker ) ) {
			wp_send_json_error( [ 'message' => 'Invalid ticker' ] );
		}

		$success = self::remove_from_current_user_portfolio( $ticker );
		if ( $success ) {
			wp_send_json_success( [ 'message' => 'Removed from portfolio' ] );
		} else {
			wp_send_json_error( [ 'message' => 'User not logged in' ] );
		}
	}

	/**
	 * Add a ticker to the current user's portfolio.
	 *
	 * @param string $ticker The ticker symbol to add.
	 * @return bool True on success, false on failure.
	 */
	public static function add_to_current_user_portfolio( string $ticker ): mixed {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}
		$portfolio   = get_user_meta( $user_id, 'portfolio', true );
		$portfolio   = $portfolio ? $portfolio : [];
		$portfolio[] = $ticker;
		$portfolio   = array_unique( $portfolio );
		$return      = update_user_meta( $user_id, 'portfolio', $portfolio );
		return $return;
	}

	/**
	 * Remove a ticker from the current user's portfolio.
	 *
	 * @param string $ticker The ticker symbol to remove.
	 * @return bool True on success, false on failure.
	 */
	public static function remove_from_current_user_portfolio( string $ticker ) {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}
		$portfolio = get_user_meta( $user_id, 'portfolio', true );
		$portfolio = array_values( array_filter( $portfolio, function ( $v ) use ( $ticker ) {
			return $v !== $ticker;
		} ) );
		if ( empty( $portfolio ) ) {
			return delete_user_meta( $user_id, 'portfolio' );
		} else {
			return update_user_meta( $user_id, 'portfolio', $portfolio );
		}
	}

	/**
	 * Helper: Check if a ticker is in the current user's portfolio.
	 *
	 * @param string $ticker The ticker symbol to check.
	 * @return bool|null True if in portfolio, false if not, null if not logged in.
	 */
	public static function is_in_current_user_portfolio( string $ticker ) {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return null;
		}

		$portfolio = get_user_meta( $user_id, 'portfolio', true );
		$portfolio = is_array( $portfolio ) ? $portfolio : [];
		return in_array( $ticker, $portfolio );
	}

	/**
	 * Retrieve the list of all tickers in the current user's portfolio.
	 *
	 * @return array|null An array of ticker symbols, or null if not logged in.
	 */
	public static function get_current_user_portfolio(): ?array {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return null;
		}

		$portfolio = get_user_meta( $user_id, 'portfolio', true );
		return is_array( $portfolio ) ? $portfolio : [];
	}

	// CRUD for the contributions of the user in a stock along time.
	// Every stock contributions is a new user meta, as an array where the keys are the year.
	public static function get_contribution_year( string $symbol, int $year, ?int $user_id = null ): ?int {
		$user_id = ( null === $user_id ) ? get_current_user_id() : (int) $user_id;
		if ( ! $user_id ) {
			return null;
		}

		$contributions = self::get_all_contributions_ticker( $symbol, $user_id );
		$contribution  = isset( $contributions[ $year ] ) ? $contributions[ $year ] : 0;

		return $contribution;
	}

	public static function get_all_contributions_ticker( string $ticker, ?int $user_id = null ): ?array {

		// we only consider the ticker if the user has saved it in his portfolio
		if ( ! self::is_in_current_user_portfolio( $ticker ) ) {
			return null;
		}

		$user_id = ( null === $user_id ) ? get_current_user_id() : (int) $user_id;
		if ( ! $user_id ) {
			return null;
		}

		// @TODO: use cache
		// phpcs:ignore WordPress.DB.RestrictedClasses
		$all_contributions = get_user_meta( $user_id, 'contributions_' . $ticker, true );
		if ( ! is_array( $all_contributions ) ) {
			$all_contributions = [];
		}

		return $all_contributions;
	}

	public static function set_contribution_year( string $ticker, int $year, int $amount, ?int $user_id = null ) {
		$user_id = ( null === $user_id ) ? get_current_user_id() : (int) $user_id;
		if ( ! $user_id ) {
			return null;
		}
		$current_contributions          = self::get_all_contributions_ticker( $ticker, $user_id );
		$current_contributions[ $year ] = $amount;

		ksort( $current_contributions );

		return update_user_meta( $user_id, 'contributions_' . $ticker, $current_contributions );
	}

	public static function add_contribution_year( string $symbol, ?int $year = null, ?int $amount = null, int|null $user_id = null ) {
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && 'add_contribution_year' === $_POST['action'];
		if ( $is_ajax ) {
			$ticker = isset( $_POST['ticker'] ) ? sanitize_text_field( wp_unslash( $_POST['ticker'] ) ) : null;
			if ( ! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( $_POST['nonce'], 'add_contribution_' . $ticker ) ) {
				wp_send_json_error( 'Nonce verification failed' );
			}
			$year    = isset( $_POST['year'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['year'] ) ) : $year;
			$amount  = isset( $_POST['amount'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['amount'] ) ) : $amount;
			$user_id = isset( $_POST['user_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : $user_id;
		}

		$user_id = ( ! $user_id ) ? get_current_user_id() : (int) $user_id;
		if ( ! $user_id ) {
			$return = null;
		} else {
			$contribution  = (int) self::get_contribution_year( $symbol, $year, $user_id );
			$contribution += $contribution ?? 0;
			$contribution += $amount;

			$return = self::set_contribution_year( $ticker, $year, $contribution, $user_id );
		}
		wp_send_json_success( 'udfds' . $user_id );
		if ( $is_ajax ) {
			wp_send_json_success( [
				'action' => 'add_contribution_year',
				'return' => $return,
			] );
			exit;
		}
		return $return;
	}

	public static function remove_contribution_year( string $ticker = null, $year = null, $user_id = null ) {
		// evaluate case of using ajax
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && 'remove_contribution_year' === $_POST['action'];
		if ( $is_ajax ) {
			$ticker = isset( $_POST['ticker'] ) ? sanitize_text_field( wp_unslash( $_POST['ticker'] ) ) : null;
			if ( ! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'remove_contribution_' . $ticker ) ) {
				wp_send_json_error( 'Nonce verification failed' );
			}
			$year    = isset( $_POST['year'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['year'] ) ) : null;
			$user_id = isset( $_POST['user_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : null;
		}

		$user_id = ( null === $user_id ) ? get_current_user_id() : (int) $user_id;
		if ( ! $user_id || ! $year || ! $ticker ) {
			if ( $is_ajax ) {
				wp_send_json_error( [
					'message' => 'Missing params',
					$year,
					$ticker,
					$user_id,
				] );
			}
			return null;
		}

		$contributions = self::get_all_contributions_ticker( $ticker, $user_id );
		if ( ! isset( $contributions[ $year ] ) ) {
			if ( $is_ajax ) {
				wp_send_json_success( true );
			}
			return true;
		}

		unset( $contributions[ $year ] );

		if ( empty( $contributions ) ) {
			$return = delete_user_meta( $user_id, 'contributions_' . $ticker );
		} else {
			$return = update_user_meta( $user_id, 'contributions_' . $ticker, $contributions );
		}
		if ( $is_ajax ) {
			wp_send_json_success( $return );
		}
		return $return;
	}
}

User_Controller::init();
