<?php
/**
 * Title: Ticker Lookup
 * Slug: portfolio-theme/part-ticker-lookup
 *
 * @package    WordPress
 * @subpackage Portfolio_Theme
 * @since      portfolio-theme 1.0.1
 */

?>
<div class="ticker-lookup-form-wrapper">
	<h1> Todo esto deberia ir </h1>

	<form id="ticker-lookup-form">
	<div class="wp-block-group is-style-default has-accent-5-background-color has-background"
	style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--30)">
	<div class="wp-block-columns">
		<div class="wp-block-column" style="flex-basis:100%">
		<div class="wp-block-group grid grid-cols-[1fr,minmax(100px,0.5fr)] gap-4">
			<div class="wp-block-group col-span-1">
				<h2 class="wp-block-heading">Ticker</h2>
				<input type="text" name="ticker-lookup" id="ticker-lookup" aria-label="Stock Ticker Lookup"
					style="width: 100%;"
					placeholder="Enter stock ticker" aria-required="true" autocomplete="off"
					value="TROW"
					class="ticker-lookup-input rounded-lg border border-gray-300 p-2 pl-10 text-sm text-gray-700"
					list="ticker-options" />
				<datalist id="ticker-options">
				</datalist>
			</div>
		</div>
		</div>

		<?php
			$partial_subtemplate = 'dynamic-partial-templates/sub-templates/partial-show-ticker-info';
		?>
		<div class="wp-block-column" style="flex-basis:100%"
				data-template-container="<?php echo esc_attr( $partial_subtemplate ); ?>">
			<?php
			get_template_part( $partial_subtemplate, '', [ 'symbol' => null ] );
			?>
		</div>
	</div>
	<div class="wp-block-group"
		style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)">
		<div class="wp-block-buttons">
		<div id="show-results-button" class="wp-block-button has-custom-width wp-block-button__width-100">
			<button type="submit" class="wp-block-button__link wp-element-button">Show results</button>
		</div>
		</div>
	</div>
	</div>

	</form>
	<div id="ticker-search-results" class="wp-block-group"
		style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
		<p class="is-style-text-annotation">results here</p>
	</div>
</div>

<button id="my-test"> TEST HERE </button>
<div id="container-test">put content here</div>

