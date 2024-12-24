<?php
/**
 * Read More Block Block render.
 *
 * @package coco
 */

// Prepare the extra query params for the the WP Query to get the related article
$args = [];
$source = isset( $attributes['source'] ) ? $attributes['source'] : null;
if ( 'postID' === $source ) {
	if ( isset( $attributes['postID'] ) && $attributes['postID'] ) {
		$args['p'] = $attributes['postID'];
	} else {
			echo wp_kses_post( Various::msg_editor_only( 'Select a post in the dropdown.' ) );
			return;
	}
} elseif ( 'category' === $source ) {
	if ( isset( $attributes['termID'] ) && $attributes['termID'] ) {
		$args['cat'] = $attributes['termID'];
	} else {
		echo wp_kses_post( Various::msg_editor_only( 'Select a category in the dropdown. <br/> The dropdown will allow to select only categories included in the current post' ) );
		return;
	}
} elseif ( 'post_tag' === $source ) {
	if ( isset( $attributes['termID'] ) && $attributes['termID'] ) {
		$args['tag_id'] = $attributes['termID'];
	} else {
		echo wp_kses_post( Various::msg_editor_only( 'Select a tag in the dropdown. <br/> The dropdown will allow to select only tags included in the current post' ) );
		return;
	}
}

$is_in_editor   = isset( $_GET['context'] ) && 'edit' === sanitize_text_field( $_GET['context'] );
$parent_post_id = get_the_ID();


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

$query = new \WP_Query( $args );

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


$cat_title = '';
if ( isset( $args['cat'] ) ) {
	$main_category = get_term( $args['cat'] );
	if ( ! is_wp_error( $main_category ) ) {
		$cat_title = $main_category->name;
	}
}
if ( empty( $cat_title ) ) {
	$categories = get_the_category( $the_post->ID );
	if ( ! empty( $categories ) ) {
		$main_category = $categories[0];
		$cat_title     = $main_category->name;
	}
}

$image_id  = get_post_thumbnail_id( $the_post->ID );
$image_src = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
$image_src = apply_filters( 'coco_relatedarticle_image_src', $image_src, $the_post, $parent_post_id );

$header         = __( 'Related Article', 'coco' );
$header         = apply_filters( 'coco_relatedarticle_header', $header, $the_post, $parent_post_id );
$href           = ! $is_in_editor ? ' href="' . get_permalink( $the_post ) . '" ' : '';
$headline       = get_the_title( $the_post );
$pre_headline   = '<span>⦿</span>'; // use an svg better
$read_more_text = apply_filters( 'coco_relatedarticle_header', __( 'read more', 'coco' ), $the_post, $parent_post_id );
$the_excerpt    = wp_trim_words( get_the_excerpt( $the_post ), 20, '...' );
$the_excerpt    = apply_filters( 'coco_relatedarticle_excerpt', $the_excerpt, $the_post, $parent_post_id );

?>

<?php
if ( ! $is_in_editor ) :
	?>
	<div <?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo get_block_wrapper_attributes( [ 'class' => ' alignleft is-frontend' ] ); ?>
	>
		<a class="link-wrapper-frontend" <?php echo wp_kses( $href, [ 'href' => [] ] ); ?> title="<?php echo esc_attr( $headline ); ?>">
	<?php
endif;
?>
	<div class="coco__relatedarticleinline <?php echo isset( $main_category ) ? esc_attr( 'cat-' . $main_category->slug ) : ''; ?>">

		<?php if ( ! empty( $header ) ) : ?>
			<p class="coco__relatedarticleinline--padding coco__relatedarticleinline__header"><?php echo esc_html( $header ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $image_src ) ) : ?>
			<div class="coco__relatedarticleinline__media">
				<figure>
					<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $headline ); ?>"></img>
				</figure>
			</div>
		<?php endif; ?>

		<div class="coco__relatedarticleinline__content coco__relatedarticleinline--padding">

			<?php if ( ! empty( $cat_title ) ) : ?>
				<div class="coco__relatedarticleinline__category coco__post__badge">
					<?php echo esc_html( $cat_title ); ?>
				</div>
			<?php endif; ?>

			<h2 class="coco__relatedarticleinline__content__headline"><?php
				if ( ! empty( $pre_headline ) ) {
					echo wp_kses_post( $pre_headline );
				}
				echo wp_kses_post( $headline );
				?>
			</h2>

			<?php if ( ! empty( $the_excerpt ) ) : ?>
				<p class="coco__relatedarticleinline__excerpt"><?php echo wp_kses_post( $the_excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $read_more_text ) ) : ?>
				<button
					class="coco__relatedarticleinline__content__readmore"
					title="<?php echo esc_attr( $headline ); ?>">
					<?php echo esc_html( $read_more_text ); ?>
				</button>
			<?php endif; ?>

		</div>
	</div>

<?php
if ( ! $is_in_editor ) :
	// close .idle-wrapper and .is-frontend
	?>
		</a>
	</div>
	<?php
endif;


