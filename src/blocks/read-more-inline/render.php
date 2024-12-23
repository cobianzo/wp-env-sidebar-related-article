<?php
/**
 * Read More Block Block render.
 *
 * @package coco
 */
$args = [];

$source = isset( $attributes['source'] ) ? $attributes['source'] : null;
if ( 'postID' === $source && isset( $attributes['postID'] ) ) {
	$args['p'] = $attributes['postID'];
} elseif ( 'category' === $source && isset( $attributes['termID'] ) ) {
	$args['cat'] = $attributes['termID'];
} elseif ( 'post_tag' === $source && isset( $attributes['termID'] ) ) {
	$args['tag_id'] = $attributes['termID'];
}

$is_editor      = isset( $_GET['context'] ) && 'edit' === sanitize_text_field( $_GET['context'] );
$parent_post_id = get_the_ID();

echo 'GET CURRENT ID: ' . $parent_post_id;

echo class_exists( 'Various' ) ? 'The class Various exists.' : 'not exists';
if ( empty( $source ) ) {
	echo wp_kses_post( Various::msg_editor_only( 'Please select a source' ) );
	return;
}

// in production we don't want old posts. In dev we need a larger range 'cause data is more static.
$days_range = ( ! defined( 'VIP_GO_APP_ENVIRONMENT' ) || 'production' === VIP_GO_APP_ENVIRONMENT ) ? 30 : 180;
$args       = array_merge( array(
	// Retrieve two posts, in case the first one is the same as the current edited post.
	'posts_per_page'         => 2,
	// Add some performance improvements on the query
	'ignore_sticky'          => true,
	'no_found_rows'          => true,
	'update_post_meta_cache' => false,
	'update_post_term_cache' => false,
	'lazy_load_term_meta'    => false,
	// get the newest first
	'orderby'                => 'date',
	'order'                  => 'desc',
	// Add a date filter to taxonomy queries
	'date_query'             => array( 'after' => "-$days_range days at midnight" ),
), $args );

echo '<br>';
print_r( $attributes );
echo '<pre>';
print_r( $args );
echo '</pre>';
$query = new \WP_Query( $args );

echo "<br>FOUMND:". count($query->posts);
// Found no posts; exit
if ( ! $query->have_posts() ) {
	echo wp_kses_post( Various::msg_editor_only( 'Sorry. There are no posts' ) );
	return;
}

$the_post = $query->posts[0];
if ( $the_post->ID === $parent_post_id ) {
	if ( ! isset( $query->posts[1] ) ) {
		echo wp_kses_post( Various::msg_editor_only( 'Sorry. The related article can\'t be the same as the post container' ) );
		return;
	}
	$the_post = $query->posts[1];
}

$header         = 'Related Article';
$pre_title      = '<span style="color: red;">➲➲</span>';
$short_title    = get_the_title( $the_post );
$long_title     = get_the_title( $the_post );
$image_src      = 'https://via.placeholder.com/150';
$cat_title      = 'Politics';
$cat_link       = '#';
$is_in_editor   = false;
$read_more_text = __( 'read more', 'coco' );
$is_opinion     = false;

echo ' GETTTT : ';
print_r( $_GET );
echo 'is editorr:: '. $is_editor;
?>


<div <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo get_block_wrapper_attributes(
		[ 'class' => ( $is_editor ? 'is-editor' : 'is-frontend' ) ]
	); ?>>
	<div class="coco__relatedarticleinline__content">

		<?php if ( ! empty( $header ) ) : ?>
			<h2><?php echo esc_html( $header ); ?></h2>
		<?php endif; ?>

		<a class="coco__relatedarticle__media" <?php echo wp_kses( $href, [ 'href' => [] ] ); ?> title="<?php echo esc_attr( $long_title ); ?>">
			<figure>
				<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $long_title ); ?>"></img>
			</figure>
		</a>

		<div class="coco__relatedarticle__content">
			<a class="coco__relatedarticle__category coco__post__badge" <?php
				echo ( $cat_link && ! $is_in_editor ) ? 'href="' . esc_url( $cat_link ) . '"' : ''
			?>>
				<?php echo esc_html( $cat_title ); ?>
			</a>
			<a <?php echo wp_kses( $href, ['href' => [] ] ); ?> title="<?php echo esc_attr( $long_title ); // phpcs:ignore  ?>"
				class="coco__relatedarticle__content__headline">
				<?php echo ! empty( $is_opinion ) ? '⍘⍘' : ''; //phpcs:ignore ?>
				<?php echo wp_kses( $pre_title, array( 'span' => [ 'class' => [] ], 'style' => [] ) ); //phpcs:ignore ?>
				<h2><?php echo wp_kses_post( $short_title ); ?></h2>
			</a>
			<?php if ( ! empty( $read_more_text ) ) : ?>
				<a <?php echo wp_kses( $href, [ 'href' => [] ] ); ?>
					class="coco__relatedarticle__content__readmore"
					title="<?php echo esc_attr( $long_title ); ?>">
					<?php echo esc_html( $read_more_text ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
