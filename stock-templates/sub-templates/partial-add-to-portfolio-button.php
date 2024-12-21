<?php
/**
 * Title: Button to add the selected stock to portfolio
 * Slug: stock-templates/sub-templates/partial-add-to-portfolio-button.php
 * Categories: partials
 * Description: Use the slug to include it as get_template_part.
 * Arguments: $symbol
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      portfolio-theme 1.0.1
 */

if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	// @TODO: refactor this, we shouldnt use extract.`
	$args = Dynamic_Partials::get_postdata_as_args_in_template( [ 'symbol' ] );
} elseif ( ! isset( $args ) ) {
	$args = [];
}

$already_in_portfolio = User_Controller::is_in_current_user_portfolio( $args['symbol'] );

if ( ! $already_in_portfolio ) :
	?>
	<div class="wp-block-button has-custom-width wp-block-button__width-100 flex items-center justify-center">
			<button class="wp-block-button__link wp-element-button"
				data-ticker="<?php echo esc_attr( $args['symbol'] ); ?>"
				data-action="add"
				onclick="handleAddRemoveFromPortfolio(event)">
				Add <b><?php echo esc_html( $args['symbol'] ); ?></b> to portfolio
			</button>
	</div>
	<?php
else :
	?>

	<div class="wp-block-button has-custom-width wp-block-button__width-100 flex items-center justify-center">
			<button class="wp-block-button__link wp-element-button"
				data-ticker="<?php echo esc_attr( $args['symbol'] ); ?>"
				data-action="remove"
				onclick="handleAddRemoveFromPortfolio(event)">
				Remove <b><?php echo esc_html( $args['symbol'] ); ?></b> from portfolio
			</button>
	</div>
	<?php
endif;
