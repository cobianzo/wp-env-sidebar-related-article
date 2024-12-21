<?php


// @TODO: prepare for ajax

// Assignments and validations. We need to have the value of $ticker and $contributions.
$ticker = null;
if ( is_singular( 'stock' ) ) {
	$ticker = strtoupper( get_post()->post_name );
} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	$args = Dynamic_Partials::get_postdata_as_args_in_template( [ 'ticker' ] );
}
if ( isset( $args['ticker'] ) ) {
	$ticker = $args['ticker'];
}

if ( ! $ticker ) {
	echo '<p class="text-red-600">Error: ticker symbol not provided</p>';
}

if ( ! User_Controller::is_in_current_user_portfolio( $ticker ) ) {
	echo '<p class="text-red-600">This ticker is not in oyur portfolio</p>';
	return;
}

echo $ticker . 'todel';

$contributions = User_Controller::get_all_contributions_ticker( $ticker );

if ( ! is_array( $contributions ) ) {
	echo '<p class="text-red-600">Erroe retrieving contributions for ' . $ticker . '</p>';
	return;
}
if ( ! count( $contributions ) ) {
	echo '<p class="text-red-600">No contributions</p>';
}
?>
	<ul class="divide-y divide-gray-200">
		<?php foreach ( $contributions as $c_year => $contribution ) : ?>
			<li class="flex items-center justify-between py-4">
				<div class="flex items-center">
					<span class="text-lg font-bold"><?php echo esc_html( $c_year ); ?></span>
					<span class="ml-4 text-sm">Contribution: <?php echo esc_html( '$' . number_format( $contribution, 2 ) ); ?></span>
				</div>
				<form class="flex items-center"
					onsubmit="window.dynamicPartials.handleSubmitFormAndLoadTemplateAjax(event)"
				>
					<input type="hidden" name="action" value="remove_contribution_year" />
					<input type="hidden" name="ticker" value="<?php echo esc_attr( $ticker ); ?>" />
					<input type="hidden" name="year" value="<?php echo esc_attr( $c_year ); ?>" />
					<input type="hidden" name="template_names" value="part-edit-contribution-list" />
					<?php wp_nonce_field( 'remove_contribution_' . $ticker, 'nonce', false ); ?>

					<button class="remove-button bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-2"
						type="submit">
						X
					</button>
				</form>
			</li>
		<?php endforeach; ?>
	</ul>


	<p class="text-lg font-bold">Total: 
	<?php
		echo esc_html( '$' . number_format( array_sum( $contributions ), 2 ) );
	?>
	</p>
