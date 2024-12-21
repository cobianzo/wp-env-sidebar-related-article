<?php
/**
 * Title: Show Ticker Info
 * Slug: dynamic-partial-templates/sub-templates/partial-show-ticker-info.php
 * Categories: partials
 * Description: Use the slug to include it as get_template_part.
 *  Show the name a small info of the ticker
 * Arguments ($args): symbol, options
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      portfolio-theme 1.0.1
 */

if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	$args = Dynamic_Partials::get_postdata_as_args_in_template( [ 'symbol', 'options' ] );
	// this gives us access to $data and $options and $symbol
}
$symbol  = isset( $args['symbol'] ) ? $args['symbol'] : '';
$options = isset( $args['options'] ) ? $args['options'] : [];

if ( empty( $symbol ) ) {
	echo '<p class="text-red-600">Symbol not provided</p>';
	return;
}

$in_portfolio = User_Controller::is_in_current_user_portfolio( $symbol );
?>

<div>
	<div class="selected-ticker-title"></div>
	<h2><?php echo esc_html( $symbol ); ?></h2>
	<div>
		<?php
		if ( $in_portfolio ) :
			?>
				<p>Currently in your portfolio</p>
			<?php
			else :
				?>
				<p>Not in your portfolio</p>
				<?php
			endif;
			?>
	</div>

	<div class="add-remove-button-wrapper pb-5"
		data-template-container="add-remove-button-wrapper-<?php echo esc_attr( $symbol ); ?>"
	>
	<?php
		get_template_part(
			'dynamic-partial-templates/sub-templates/partial-add-to-portfolio-button',
			'',
		[ 'symbol' => $symbol ] );
		?>
	</div>

</div>
