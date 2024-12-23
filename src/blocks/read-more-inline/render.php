<?php
/**
 * Read More Block Block render.
 *
 * @package coco
 */

$header         = 'MyHEADER';
$pre_title      = '<span style="color: red;">➲➲</span>';
$short_title    = 'My Title';
$long_title     = 'this is the title or the post ok';
$image_src      = 'https://via.placeholder.com/150';
$cat_title      = 'Politics';
$cat_link       = '#';
$is_in_editor   = false;
$read_more_text = __( 'read more', 'coco' );
$is_opinion     = false;
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<div class="coco__relatedarticleinline__content">

		<?php if ( ! empty( $header ) ) : ?>
			<h2><?php echo esc_html( $header ); ?></h2>
		<?php endif; ?>

		<a class="coco__post-teaser__media" <?php echo wp_kses( $href, [ 'href' => [] ] ); ?> title="<?php echo esc_attr( $long_title ); ?>">
			<figure>
				<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $long_title ); ?>"></img>
			</figure>
		</a>

		<div class="coco__post-teaser__content">
			<a class="coco__post__category coco__post__badge" <?php
				echo ( $cat_link && ! $is_in_editor ) ? 'href="' . esc_url( $cat_link ) . '"' : ''
			?>>
				<?php echo esc_html( $cat_title ); ?>
			</a>
			<a <?php echo wp_kses( $href, ['href' => [] ] ); ?> title="<?php echo esc_attr( $long_title ); // phpcs:ignore  ?>"
				class="coco__post-teaser__content__headline">
				<?php echo ! empty( $is_opinion ) ? '⍘⍘' : ''; //phpcs:ignore ?>
				<?php echo wp_kses( $pre_title, array( 'span' => [ 'class' => [] ], 'style' => [] ) ); //phpcs:ignore ?>
				<h2><?php echo wp_kses_post( $short_title ); ?></h2>
			</a>
			<?php if ( ! empty( $read_more_text ) ) : ?>
				<a <?php echo wp_kses( $href, [ 'href' => [] ] ); ?>
					class="coco__post-teaser__content__readmore"
					title="<?php echo esc_attr( $long_title ); ?>">
					<?php echo esc_html( $read_more_text ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
