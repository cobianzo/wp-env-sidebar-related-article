<?php
/**
 * Functions to be used in the frontend. Generic. They are here because I didn't
 * find a better place to put them.
 *
 */
class Theme_Frontend {

	/**
	 * Initialize the Stock_Frontend class.
	 *
	 * This method sets up necessary hooks, actions, or filters to prepare the frontend
	 * functionality of the stock-related features.
	 */
	public static function init(): void {
		// define the ajax actions to call the render of the table from frontend.
		// Note: they work but I don't use them as ajax. I call them directly from php templates.
		add_action( 'wp_ajax_generate_table', array( __CLASS__, 'generate_table_html' ) );
		add_action( 'wp_ajax_nopriv_generate_table', array( __CLASS__, 'generate_table_html' ) );
	}

	/**
	 * Generate table, accepting ajax call (passing args as $_POST)
	 * $data structure is [ 2027 => [ 'Column1' => 'Value1', 'Column2' => 'Value2' ], 2028 ...  ]
	 *
	 * @param ?array $data Array of arrays. Each array is a row. Each row is an associative array.
	 * @param array $options Array of options. 'titles_map' is an associative array to map titles. 'append' is an associative array to append to each cell. 'id' the id of the table
	 * @return null|string
	 */
	public static function generate_table_html( array|null $data = null, array $options = [] ): ?string {

		// evaluate nonce if Ajax
		if ( null === $data ) {
			if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['data'] ) ) {
				exit;
			}
			$data  = json_decode( sanitize_text_field( wp_unslash( $_POST['data'] ) ), true );
			$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'dynamic_blocks_nonce_action' ) ) {
				wp_send_json_error( 'Error en la verificaci n de nonce.' );
			}

			if ( isset( $_POST['options'] ) ) {
				$options = json_decode( sanitize_text_field( wp_unslash( $_POST['options'] ) ), true );
			}
		}

		if ( ! is_array( $data ) ) {
			return null;
		}

		// apply options
		$titles_map = isset( $options['titles_map'] ) ? $options['titles_map'] : null;
		$append     = isset( $options['append'] ) ? $options['append'] : null;

		// Validate $data. It must be an associative array
		if ( ! is_array( $data ) ) {
			$data = [];
		}

		// Iniciar la tabla
		ob_start();
		?>
		<table class="table-auto w-full text-left border-collapse border border-gray-500 shadow-md"
			<?php echo isset( $options['id'] ) ? 'id="' . esc_attr( $options['id'] ) . '"' : ''; ?>>
			<thead>
				<tr class="bg-gray-100 border-b border-gray-500">
					<th class="px-4 py-2">
					<?php
						$title = isset( $titles_map['Title'] ) ? $titles_map['Title'] : 'Title';
						apply_filters( 'coco_generate_table_title', $title, $options );
						echo wp_kses_post( $title );
					?>
					</th>
					<?php
						$columns = array_keys( current( $data ) );
					foreach ( $columns as $column ) {
						$column_title = isset( $titles_map[ $column ] ) ? $titles_map[ $column ] : $column;
						?>
							<th class="px-4 py-2"><?php echo esc_html( $column_title ); ?></th>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $data as $key_cell => $row ) {
					?>
						<tr class="hover:bg-gray-200">
							<td class="px-4 py-2"><?php echo esc_html( $key_cell ); ?></td>
							<?php
							foreach ( $row as $title => $cell ) {
								?>
								<td class="px-4 py-2">
								<?php
									$cell = apply_filters( 'coco_generate_table_cell', $cell, $id, $options );
									echo wp_kses_post( $cell );
									echo isset( $append[ $title ] ) ? wp_kses_post( $append[ $title ] ) : '';
								?>
								</td>
									<?php
							}
							?>
						</tr>
						<?php
				}
				?>
			</tbody>
		</table>
		<?php
		$html = ob_get_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX
			&& isset( $_POST['action'] ) && 'generate_table' === $_POST['action'] ) {
			wp_send_json_success( $html );
		}

		return $html;
	}
}

Theme_Frontend::init();
