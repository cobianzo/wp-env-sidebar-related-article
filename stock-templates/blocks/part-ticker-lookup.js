import domReady from '@wordpress/dom-ready';
import { setupShowResultsButton } from './part-ticker-lookup/divsTable';
import { setupTickerSearch } from './part-ticker-lookup/lookupticker';
import { setupAddToPortfolio } from './part-ticker-lookup/addToPortfolio';
// GENERIC VARIABLES AND FUNCTIONS, MEANT TO BE USED BY OTHER COMPONENTS IN THE PAGE.
// ==================================
// ==================================

// @TODO: avoid using global vars
window.selectedTicker = null;

window.setSelectedTicker = ( tickerInfo ) => {
	window.selectedTicker = tickerInfo ?? null;

	// effects to update when it changes
	const resultsContainer = document.querySelector( '#ticker-search-results' );
	if ( ! window.selectedTicker ) {
		resultsContainer.innerHTML = `<div>Use the input to lookup for a ticker</div>`;
	} else {
		resultsContainer.innerHTML = `<div>Selected ${ tickerInfo.shortname }...</div>`;
	}

	window.dynamicPartials.loadTemplateAjax(
		'stock-templates/sub-templates/partial-show-ticker-info',
		'[data-dynamic-partial="stock-templates/sub-templates/partial-show-ticker-info"]',
		{ symbol: tickerInfo ? tickerInfo.symbol : null }
	);
};
window.clearSelectedTicker = () => window.setSelectedTicker( null );

// ==================================
// ==================================
// ==================================
// ==================================
// ==================================
// Inicializar cuando el DOM esté cargado
domReady( () => {
	setupTickerSearch( '#ticker-lookup' );
	setupShowResultsButton( '#ticker-lookup-form', '#ticker-search-results' );
	setupAddToPortfolio();

	// Test  @TODELETE
	const testButton = document.querySelector( '#my-test' );
	testButton.addEventListener( 'click', () => {
		window.dynamicPartials.log( 'testing:' );

		window.dynamicPartials.loadTemplateAjax(
			'stock-templates/sub-templates/partial-dividends-table',
			'#container-test',
			{
				data: { 2001: { title: 'a' }, 2003: { title: 'b' } },
				options: { append: { title: 'titlereforbs' } },
			}
		);
	} );
} );
