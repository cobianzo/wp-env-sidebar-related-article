import { tickerExists, getTickerHistorical } from './stocksInfoAPI';

// Load the dividends by submitting the input with the ticker selected
// ==================================
export const setupShowResultsButton = ( formSelector, resultsSelector ) => {
	const form = document.querySelector( formSelector );

	// Show Results!
	form.addEventListener( 'submit', async ( e ) => {
		// dont do the default action
		e.preventDefault();

		// prepare data for the ajax
		const formData = new FormData( form );
		const tickerValue = formData.get( 'ticker-lookup' ).trim();

		// validation, check that the input has a symbol that exists
		const currentTicker = await tickerExists( tickerValue.trim() );
		if ( currentTicker ) {
			window.setSelectedTicker( currentTicker );
		} else window.clearSelectedTicker();

		// retrieve the data for the years, using our internal endpoint
		const historicalData = await getTickerHistorical( currentTicker.symbol );
		window.dynamicPartials.log(
			`%cTODELETE: historical data `,
			'font-size:2rem;',
			historicalData
		);
		// Load the table of dividends
		window.dynamicPartials.loadTemplateAjax(
			'stock-templates/sub-templates/partial-dividends-table',
			resultsSelector,
			{ data: historicalData, symbol: currentTicker.symbol }
		);
	} );
};
