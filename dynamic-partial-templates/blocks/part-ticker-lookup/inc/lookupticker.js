import { debounce } from './helpers';
import { searchYahooFinanceTickers, tickerExists } from '../../../../inc/js/lib/stocksInfoAPI';

// INPUT LOOKUP - Helper Función de búsqueda de tickers
// =============================================

// Setup lookup input actions to search tickers
export const setupTickerSearch = ( inputSelector ) => {
	const searchInput = document.querySelector( inputSelector );

	// The handler debounded for the input typing
	const handleSearch = debounce( async ( event ) => {
		const searchTerm = event.target.value.trim();

		window.clearSelectedTicker();

		if ( searchTerm.length < 2 ) return;

		try {
			const tickers = await searchYahooFinanceTickers( searchTerm );
			const datalist = document.getElementById( 'ticker-options' );
			datalist.innerHTML = '';
			window.dynamicPartials.log( '@TODELETE, handle search found: ', tickers );
			tickers.forEach( ( ticker ) => {
				const option = document.createElement( 'option' );
				option.value = ticker.symbol;
				datalist.appendChild( option );
			} );
		} catch ( error ) {
			window.dynamicPartials.error( error );
		}
	}, 300 );

	// bind the event when typing
	searchInput.addEventListener( 'input', handleSearch );
	searchInput.addEventListener( 'change', async function ( event ) {
		const tickerInInput = await tickerExists( event.target.value );
		window.setSelectedTicker( tickerInInput );
		window.dynamicPartials.log( 'TODELETE Selected value from datalist: ', event.target.value );
	} );
	// bind the event when selecting:
	searchInput.addEventListener( 'blur', async function ( event ) {
		const tickerInInput = await tickerExists( event.target.value );
		if ( ! tickerInInput ) {
			event.target.value = '';
		}
	} );
};
