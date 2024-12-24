<?php
/**
 * Class Create_Dummy_Data
 *
 * This class is responsible for creating dummy data for testing purposes.
 * We created the page
 *
 * /wp-admin/options-general.php?page=create-dummy-data
 * optional query param to reset it:
 * /wp-admin/options-general.php?page=create-dummy-data&reset=1
 *
 * Adn if you visit this page, it will automatically ensure that we have one or more of:
 * tags / categories / media attachments / posts
 *
 * @package aside-related-article-block
 */

class Create_Dummy_Data {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Initialization code here.
	}

	public static function init() {

		// Creates /wp-admin/options-general.php?page=create-dummy-data
		// only visiting this page will create the dummy data. Useful when testing with Playwright
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
	}

	public static function create_dummy_media() {
		// Create a dummy image attachment.
		$upload_dir       = wp_upload_dir();
		$image_url        = 'https://via.placeholder.com/300x150';
		$image_name       = 'dummy-image.jpg';
		$image_slug       = sanitize_title( pathinfo( $image_name, PATHINFO_FILENAME ) );
		$image_data       = file_get_contents( $image_url );
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name );
		$filename         = basename( $unique_file_name );

		$existing_attachment = get_posts( array(
			'post_type'   => 'attachment',
			'post_name'   => $image_slug,
			'numberposts' => 1,
		) );
		if ( ! empty( $existing_attachment ) ) {
			echo "<br/>Attachment $image_slug already exists";
			return $existing_attachment[0]->ID;
		}

		// the attachment doesnt exist, let's create it.
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_name'      => $image_slug,
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file );

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	public static function create_dummy_terms() {
		// Create a category called Politics if it doesn't exist.
		if ( ! term_exists( 'Politics', 'category' ) ) {
			echo '<br/>creating Politics cat';
			wp_insert_term( 'Politics', 'category' );
		} else {
			echo '<br/>cat Politics exists';
		}

		// Create a tag called Barack Obama if it doesn't exist.
		if ( ! term_exists( 'Barack Obama', 'post_tag' ) ) {
			wp_insert_term( 'Barack Obama', 'post_tag' );
		} else {
			echo '<br/>tag barack-obama exists';
		}
	}
	/**
	 * Create dummy posts.
	 *
	 * @param int $count Number of posts to create.
	 * @return void
	 */
	public static function create_dummy_posts( $count = 10, $attachment_id = 0 ) {
		for ( $i = 0; $i < $count; $i++ ) {
			$post    = get_page_by_path( 'dummy-post-' . $i, OBJECT, 'post' );
			$post_id = null;
			if ( ! isset( $post->ID ) ) {
				$post_id = wp_insert_post( array(
					'post_title'   => 'Dummy Post ' . $i,
					'post_name'    => 'dummy-post-' . $i,
					'post_content' => str_repeat( '<!-- wp:paragraph -->
<p>Custom paragraph for Post: ' . $i . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque efficitur, massa luctus finibus dignissim.</p>
<!-- /wp:paragraph -->', 4 ),
					'post_status'  => 'publish',
					'post_author'  => 1,
				) );
				echo "<br/>Created post $i <a href='" . get_edit_post_link( $post_id ) . "' target='new'>" . get_the_title( $post_id ) . "</a>" ;
			} else echo "<br> - Post $i exists ($post->ID)";
			if ( $attachment_id && is_numeric( $post_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
	}

	/**
	 * Create dummy users.
	 *
	 * @param int $count Number of users to create.
	 * @return void
	 */
	public static function create_dummy_users( $count = 10 ) {
		for ( $i = 0; $i < $count; $i++ ) {
			wp_create_user( 'dummy_user_' . $i, 'password', 'dummy_user_' . $i . '@example.com' );
		}
	}

	public static function reset_dummy_data() {
		$all_posts = get_posts( array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'numberposts'    => -1,
			'suppress_filters' => false,
		) );
		foreach ( $all_posts as $post ) {
			echo "<br/><span style='color:#ee4455'>deleting $post->ID -> ($post->post_title)</span>";
			wp_delete_post( $post->ID, true );
		}
		echo '<br/><hr/>';
	}

	public static function add_admin_menu() {
		add_options_page(
			'Create Dummy Data',
			'Create Dummy Data',
			'manage_options',
			'create-dummy-data',
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'You do not have sufficient permissions to access this page.' );
				}

				if ( wp_get_environment_type() !== 'production' ) {

					if ( isset( $_GET['reset'] ) ) {
						echo '<h2>Resetting dummy data</h2>';
						Create_Dummy_Data::reset_dummy_data();
					}

					echo '<h3>Attempting to create dummy data</h3>';
					$att_ID = Create_Dummy_Data::create_dummy_media();
					Create_Dummy_Data::create_dummy_terms();
					Create_Dummy_Data::create_dummy_posts( 5, $att_ID );

					echo '<h2>Dummy Data Created</h2>';

					echo '<a href="' . esc_url( add_query_arg( 'reset', '1' ) ) . '" class="button button-link-delete">Reset Dummy Data</a>';
					echo '&nbsp;<a href="' . esc_url( remove_query_arg( 'reset' ) ) . '" class="button">Reload Page</a>';

					$attachments = get_posts( array(
						'post_type'   => 'attachment',
						'numberposts' => -1,
					) );

					echo '<h2>Summary</h2>';

					echo '<div style="display:flex; gap:1rem; border: 1px solid gray; padding: 1rem;">';
					echo '<div class="column">';

					echo '<h4>Attachments (' . count( $attachments ) . '):</h4>';
					echo '<ul>';
					foreach ( $attachments as $attachment ) {
						echo '<li>' . esc_html( $attachment->post_title ) . '<br>';
						echo wp_get_attachment_image(
							$attachment->ID,
							'medium',
							false,
							array( 'style' => 'max-height: 100px; width: auto;', 'data-check' => 'demo-attachment' )
						) . '</li>';
					}
					echo '</ul>';

					echo '</div>';
					echo '<div class="column">';

					echo '<h4>Posts:</h4>';
					$posts = get_posts( array( 'numberposts' => -1 ) );
					echo '<ul>';
					foreach ( $posts as $post ) {
						echo '<li><a class="test-post-link-' . esc_attr( $i ) . '" href="' . esc_url( get_edit_post_link( $post->ID ) ) . '" target="new">' .
							esc_html( $post->post_title ) .
						'</a></li>';
					}
					echo '</ul>';

					echo '</div>';
					echo '<div class="column">';

					echo '<h4>Terms:</h4>';
					$categories = get_categories( array( 'hide_empty' => false ) );
					echo '<h5>Categories:</h5>';
					echo '<ul>';
					foreach ( $categories as $category ) {
						echo '<li>' . esc_html( $category->name ) . '</li>';
					}
					echo '</ul>';

					$tags = get_tags( array( 'hide_empty' => false ) );
					echo '<h5>Tags:</h5>';
					echo '<ul>';
					foreach ( $tags as $tag ) {
						echo '<li>' . esc_html( $tag->name ) . '</li>';
					}
					echo '</ul>';
				} else {
					echo '<h2>Environment is production. Dummy data creation is disabled.</h2>';
				}
				echo '</div>';
				echo '</div>';

			}
		);
	}
}

Create_Dummy_Data::init();
