<?php
// Clase para manejar el modal

class Modal {
	public static function init() {
		add_action( 'wp_footer', [ self::class, 'render_dialog' ] );
		add_action( 'wp_enqueue_scripts', [ self::class, 'enqueue_assets' ] );
	}

	public static function enqueue_assets() {
		// Encolar CSS
		wp_enqueue_style(
			'dialog-modal',
			get_template_directory_uri() . '/dynamic-partials-plugin/css/modal.css',
			[],
			'1.0.0'
		);

		// Encolar JavaScript
		wp_enqueue_script(
			'dialog-modal',
			get_template_directory_uri() . '/assets/js/dialog.js',
			[],
			'1.0.0',
			true
		);
	}

	public static function render_dialog() {
		?>
		<dialog id="modal-dialog" class="modal-dialog">
  		<button class="button close-button" onclick="this.closest('dialog').close();">close modal</button>
  		<h2>An interesting title</h2>
  		<div class="modal-content" method="dialog">
				Content here
			</div>
		</dialog>
		<?php
	}
}

// Inicializar
add_action( 'init', [ 'Modal', 'init' ] );
