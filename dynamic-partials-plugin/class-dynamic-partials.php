<?php
/**
 * Class Dynamic_Partials.
 *
 * This class:
 * 1) Registers every php file as a block in the folder
 * 2) If there is also a js file with the same name, registers it as a view script.
 * 3) Provides the helper that allows us to load any tempalte part in the FE easily
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      1.0.0
 */

class Dynamic_Partials {

	public array $blocks  = [];
	const BLOCK_NAMESPACE = 'coco';

	/**
	 * Init the blocks with the name of every file inside ./blocks folder
	 */
	public function __construct() {
		// 1. Scan all blocks in the subfolder 'blocks'
		$config_file = file_get_contents( __DIR__ . '/config.json' );
		$config      = json_decode( $config_file, true );

		$blocks_dir = get_template_directory() . $config['php-partials-path'];
		if ( is_dir( $blocks_dir ) ) {
			$block_files = glob( $blocks_dir . '/*.php' );
			foreach ( (array) $block_files as $block_file ) {
				// For every template part, we will create a dynamic block for it.
				$this->blocks[] = pathinfo( (string) $block_file, PATHINFO_FILENAME );
			}
		} else {
			wp_die( 'error in dir: ' . esc_html( $blocks_dir ) );
		}

		$this->register_hooks();
	}

	/**
	 * Hooks calls
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'init', array( $this, 'register_blocks_as_template_parts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'register_editor_script_for_block' ) );
		add_action( 'enqueue_block_editor_assets', function () {
			// CSS for the placeholder for the block in the Editor.
			add_action( 'admin_enqueue_scripts',
				function ( $hook ) {
						$css = <<<CSS
						.coco-dynamic-block-wrapper {
							padding: var(--wp--preset--spacing--20);
							border-radius: 5px;
							border: 5px ridge blanchedalmond;
							background: var(--wp--preset--color--accent-5,rgb(188, 211, 186));
							min-height: 300px;
							place-items: center center;
						}
						CSS;
						wp_add_inline_style( 'wp-block-library', $css );
				}
			);
		} );

		// simply explose the myJS.ajaxurl and nonce variables.
		add_action( 'wp_enqueue_scripts', function () {
			// expose JS vars and generic methods
			$asset_file = include get_stylesheet_directory() . '/build/dynamic-partials-public-helpers.asset.php';
			wp_register_script( 'dynamic-partials-public-helpers',
				get_stylesheet_directory_uri() . '/build/dynamic-partials-public-helpers.js',
				$asset_file['dependencies'],
				$asset_file['version'],
			true );

			wp_add_inline_script( 'dynamic-partials-public-helpers', 'var myJS = {
					ajaxurl : "' . admin_url( 'admin-ajax.php' ) . '",
					nonce : "' . wp_create_nonce( 'dynamic_blocks_nonce_action' ) . '" }', 'before' );
			wp_enqueue_script( 'dynamic-partials-public-helpers' );
		} );

		// @BOOK:LOADTEMPLATEAJAX
		add_action( 'wp_ajax_load_template_ajax', array( $this, 'load_template_ajax' ) );
		add_action( 'wp_ajax_nopriv_load_template_ajax', array( $this, 'load_template_ajax' ) );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function register_blocks_as_template_parts(): void {

		foreach ( $this->blocks as $block_name ) {
			$namespaced_blockname = sprintf( '%s/%s', self::BLOCK_NAMESPACE, $block_name );
			$block_title          = ucwords( str_replace( '-', ' ', $block_name ) );

			$register_block_options = [
				'title'           => $block_title, // eg. Part Ticker Lookup
				'category'        => 'widgets',
				'render_callback' => function ( array $attributes ) use ( $block_name ): string {
					$config_file = file_get_contents( __DIR__ . '/config.json' );

					$config      = json_decode( $config_file, true );
					$blocks_dir  = get_template_directory() . $config['php-partials-path'];
					if ( ! file_exists( $blocks_dir . '/' . $block_name . '.php' ) ) {
						return 'One template for dynamic blocks is not present: ' . $block_name ;
					}
					ob_start();
						echo '<div data-dynamic-partial="' . esc_attr( $block_name ) . '">';
						include $blocks_dir . '/' . $block_name . '.php';
						echo '</div>';
					$html = ob_get_clean();
					return (string) $html;
				},
			];

			// Check if the view script exists and register it if it does.
			$config_file      = file_get_contents( __DIR__ . '/config.json' );
			$config           = json_decode( $config_file, true );
			$view_script_path = get_template_directory() . $config['js-path'] . "/$block_name.js";
			if ( file_exists( $view_script_path ) ) {
				$view_script_handle = "view-script-$block_name";
				$view_script_url    = get_stylesheet_directory_uri() . "/build/$block_name.js";
				$asset_file         = include get_stylesheet_directory() . "/build/$block_name.asset.php";
				$dependencies       = empty( $asset_file['dependencies'] ) ? [] : $asset_file['dependencies'];
				$dependencies[]     = 'dynamic-partials-public-helpers';
				$version            = empty( $asset_file['version'] ) ? [] : $asset_file['version'];
				wp_register_script( $view_script_handle, $view_script_url, $dependencies, $version, true );
				$register_block_options['view_script'] = $view_script_handle;
			} else {
				// There is no frontend script for this block.
			}

			register_block_type(
				$namespaced_blockname, // eg. coco/part-ticker-lookup
				$register_block_options
			);

			// Registrar un script vacío para evitar problemas.
			$script_name = "script-$block_name";
			$deps        = array( 'wp-blocks', 'wp-element', 'wp-editor' );
			wp_register_script( $script_name, '', $deps, '1.0.0', true );
		}
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function register_editor_script_for_block(): void {
		foreach ( $this->blocks as $block_name ) {
			$namespaced_blockname = sprintf( '%s/%s', self::BLOCK_NAMESPACE, $block_name );
			$block_title          = ucwords( str_replace( '-', ' ', $block_name ) );
			$script_name          = "script-$block_name";
			wp_enqueue_script( $script_name );

			$script_inline = <<<JS
	(function(blocks, element) {
			blocks.registerBlockType( '$namespaced_blockname', {
					title: '$block_title',
					icon: 'smiley',
					category: 'common',
					edit: function() {
							return element.createElement('div',
								{ className: 'coco-dynamic-block-wrapper' },
								'Partial: $block_title'
							);
					},
					save: function() {
							return null;
					}
			});
	})(window.wp.blocks, window.wp.element);
JS;

			wp_add_inline_script( $script_name, $script_inline, 'after' );
		}
	}



	public function load_template_ajax() {
		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['template_name'] ) ) {
			wp_send_json_error( 'error params load template ajax' );
			exit;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'dynamic_blocks_nonce_action' ) ) {
			wp_send_json_error( 'Error en la verificaci n de nonce.' );
		}

		$template_name = sanitize_text_field( wp_unslash( $_POST['template_name'] ) );
		if ( ! locate_template( $template_name . '.php', false, false ) ) {
				wp_send_json_error( 'Template not found: ' . $template_name );
				exit;
		}
		ob_start();
			get_template_part( $template_name, $_POST );
			$html = ob_get_clean();
		wp_send_json_success( $html );
		exit;
	}

	public static function get_postdata_as_args_in_template( array $var_names ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return;
		}
		if ( ! isset( $_POST['args'] ) ) {
			return;
		}

		$args = json_decode( stripslashes( $_POST['args'] ), true );
		$args = array_intersect_key( $args, array_flip( $var_names ) );
		return $args;
	}
}

$dynamic_partials = new Dynamic_Partials();
