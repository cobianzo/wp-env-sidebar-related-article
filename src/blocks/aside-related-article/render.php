<?php
/**
 * Aside Related Article Block render.
 *
 * Shows the Related Article based on the params passed as $query_args.
 * This is called on the frontend and
 * in the editor using <ServerSideRender />
 *
 * global params:
 * $attributes (see block.json)
 *
 * @package aside-related-article-block
 */

// Prepare the extra query params for the the WP Query to get the related article
$attributes = isset( $attributes ) ? $attributes : [];
$_source    = $attributes['source'] ?? null;
$_post_id   = $attributes['postID'] ?? null;
$_term_id   = $attributes['termID'] ?? null;

$query_args = [];

// extra vars we'll need later
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$is_in_editor   = isset( $_GET['context'] ) && 'edit' === sanitize_text_field( wp_unslash( $_GET['context'] ) );
$parent_post_id = get_the_ID();

// in production we don't want old posts. In dev we need a larger range 'cause data is more static.
$days_range = 'production' === wp_get_environment_type() ? 30 : 180;
$query_args = array_merge( array(
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
	// The params set up as block attributes
	'p'                      => 'postID' === $_source && $_post_id ? $_post_id : null,
	'cat'                    => 'category' === $_source && $_term_id ? $_term_id : null,
	'tag'                    => 'post_tag' === $_source && $_term_id ? $_term_id : null,
), $query_args );

$query = new \WP_Query( $query_args );

// Found no posts; exit
if ( ! $query->have_posts() ) {
	return;
}

$the_post = $query->posts[0];
// the found post is the same as the post where we are, so we take the second one.
if ( isset( $the_post->ID ) && $the_post->ID === $parent_post_id ) {
	if ( ! isset( $query->posts[1] ) ) {
		return;
	}
	$the_post = $query->posts[1];
}

if ( ! isset( $the_post->ID ) ) {
	return;
}

// if the source is category, get info about it.
$main_category = null;
if ( isset( $query_args['cat'] ) ) {
	$main_category = get_term( $query_args['cat'] );
} else {
	$categories = get_the_category( $the_post->ID );
	if ( ! empty( $categories ) ) {
		$main_category = $categories[0];
	}
}
$cat_title = ! empty( $main_category->name ) ? $main_category->name : '';

// The variables for the view.
$image_id  = get_post_thumbnail_id( $the_post->ID );
$image_src = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
$image_src = apply_filters( 'coco_relatedarticle_image_src', $image_src, $the_post, $parent_post_id );

$header         = __( 'Related Article', 'aside-related-article-block' );
$header         = apply_filters( 'coco_relatedarticle_header', $header, $the_post, $parent_post_id );
$href           = ' href="' . get_permalink( $the_post ) . '" ';
$headline       = get_the_title( $the_post );
$pre_headline   = '<span>⦿</span>'; // use an svg better
$pre_headline   = apply_filters( 'coco_relatedarticle_pre_headline', $pre_headline, $the_post, $parent_post_id );
$read_more_text = apply_filters( 'coco_relatedarticle_read_more', __( 'read more', 'aside-related-article-block' ), $the_post, $parent_post_id );
$the_excerpt    = wp_trim_words( get_the_excerpt( $the_post ), 20, '...' );
$the_excerpt    = apply_filters( 'coco_relatedarticle_excerpt', $the_excerpt, $the_post, $parent_post_id );

?>

<?php

/**
 * NOW, the HTML
 * ====================
 */

if ( ! $is_in_editor ) :
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div <?php echo get_block_wrapper_attributes( [ 'class' => 'is-frontend' ] ); ?>>
		<a class="link-wrapper-frontend" <?php echo wp_kses( $href, [ 'href' => [] ] ); ?> title="<?php echo esc_attr( $headline ); ?>">

	<?php
endif;

	$container_classes = isset( $main_category ) ? 'cat-' . $main_category->slug : '';
	$container_classes = apply_filters( 'coco_relatedarticle_classes', $container_classes, $the_post, $parent_post_id, $attributes );
?>
	<div class="coco-related-article <?php echo esc_attr( $container_classes ); ?>">

		<?php if ( ! empty( $header ) ) : ?>
			<p class="coco-related-article--padding coco-related-article__header"><?php echo esc_html( $header ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $image_src ) ) : ?>
			<div class="coco-related-article__media">
				<figure>
					<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $headline ); ?>"></img>
				</figure>
			</div>
		<?php endif; ?>

		<div class="coco-related-article__content coco-related-article--padding">

			<?php if ( ! empty( $cat_title ) ) : ?>
				<div class="coco-related-article__category coco__post__badge">
					<?php echo esc_html( $cat_title ); ?>
				</div>
			<?php endif; ?>

			<h2 class="coco-related-article__content__headline"><?php
			if ( ! empty( $pre_headline ) ) {
				echo wp_kses_post( $pre_headline );
			}
				echo wp_kses_post( $headline );
			?>
			</h2>

			<?php if ( ! empty( $the_excerpt ) ) : ?>
				<p class="coco-related-article__excerpt"><?php echo wp_kses_post( $the_excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $read_more_text ) ) : ?>
				<button
					class="coco-related-article__content__readmore"
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


