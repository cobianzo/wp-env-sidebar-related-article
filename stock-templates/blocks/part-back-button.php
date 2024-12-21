<?php
$back_url = isset( $_GET['back'] ) ? esc_url( $_GET['back'] ) : '';

if ( ! empty( $back_url ) ) {
	?>
	<div class="wp-block-button has-custom-width wp-block-button__width-100">
		<a href="<?php echo $back_url; ?>" class="wp-block-button__link wp-element-button">
			&#8592; Back
		</a>
	</div>  
	<?php
}
