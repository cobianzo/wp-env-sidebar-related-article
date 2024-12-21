<?php
/**
 * Title: Edit Ticker Contribution
 * Slug: portfolio-theme/part-edit-ticker-contribution
 * Description: A partial for editing an investment
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      portfolio-theme 1.0.1
 */

// Allow show the form for a ticker passed by $arg, or by
// the query arg of the current template (single page of a stock)
if ( is_singular( 'stock' ) ) {
	$symbol = strtoupper( get_post()->post_name );
}
$symbol = isset( $args['symbol'] ) ? $args['symbol'] : $symbol;

if ( empty( $symbol ) ) {
	echo '<p class="text-red-600">Error: Symbol not provided</p>';
	return;
}

// @TODO: evaluate ajax
// @TODO: evaluate if symbol does not exist
?>
<div class="bg-white rounded-lg shadow-lg p-8">

	<form
		data-dynamic-template-reload="part-edit-contribution-list"
		class="ticker-contribution-form flex flex-row items-end justify-between">

		<input type="hidden" name="action" value="add_contribution_year" />
		<input type="hidden" name="ticker" value="<?php echo esc_attr( $symbol ); ?>" />
		<?php wp_nonce_field( 'add_contribution_' . $symbol, 'nonce', false ); ?>

		<div class="flex flex-col mr-4 flex-1">
			<label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
			<input type="number" pattern="[0-9]*" inputmode="numeric" id="amount" name="amount"
				min="1"
			max="1000000"
				step="1"
				value="5000"  <?php // @TODO: use localStorage ?>
				required
				aria-required="true"
				aria-label="Between 1 and 1 million"
				aria-describedby="numeric-input-description"
				class="mt-1 rounded-lg border border-gray-300 p-2 pl-10 text-sm text-gray-700" />
		</div>
		<div class="flex flex-col mr-4 flex-1">
			<label for="year" class="block text-sm font-medium text-gray-700">Year</label>
			<input type="number" id="year" name="year" pattern="[0-9]*" inputmode="numeric"
				min="1999"
			max="<?php echo esc_attr( gmdate( 'Y' ) ); ?>"
				step="1"
				required
				value="2010"
				aria-required="true"
				aria-label="Select the year where the investment was made"
				aria-describedby="numeric-input-description"
				class="mt-1 rounded-lg border border-gray-300 p-2 pl-10 text-sm text-gray-700" />

		</div>
		<div class="flex flex-col mr-4 flex-none items-end h-full">
			<button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
				Add
			</button>
		</div>
	</form>

</div>

