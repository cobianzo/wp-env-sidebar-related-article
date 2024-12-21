// @TODO: this handle is not exclusive of this block. thats why
// The function to add/remove from to portfolio must be in a separated lib
// As a CRUD, then called in the handler

const handleAddRemoveFromPortfolio = async ( event ) => {
	// conditions - the partial must be wrapped in a container with id
	//	and the attrs in the button with the handler data-ticker and data-action are mandatory
	const buttonElement = event.currentTarget;
	const ticker = buttonElement.getAttribute( 'data-ticker' );
	const parentContainerSelector = `[data-template-container="add-remove-button-wrapper-${ ticker }"]`;
	const action = buttonElement.getAttribute( 'data-action' );

	const ajaxAction =
		action === 'add' ? 'add_to_current_user_portfolio' : 'remove_from_current_user_portfolio';
	const formdata = new FormData();
	formdata.append( 'action', ajaxAction );
	formdata.append( 'nonce', window.myJS.nonce );
	formdata.append( 'ticker', ticker );
	try {
		const response = await fetch( window.myJS.ajaxurl, { method: 'POST', body: formdata } );
		const result = await response.json();

		if ( result.success ) {
			window.dynamicPartials.log( 'Ticker added or removed to portfolio:', result.data );
		} else {
			window.dynamicPartials.err( 'Error en la respuesta setupAddToPortfolio:', result.data );
		}
	} catch ( error ) {
		window.dynamicPartials.err( 'Error en la solicitud AJAX setupAddToPortfolio:', error );
	} // end try/catch

	// reload the template part. This can be async
	window.dynamicPartials.log( 'TODEL reloading tempalte part.', ticker );
	window.dynamicPartials.loadTemplateAjax(
		'stock-templates/sub-templates/partial-add-to-portfolio-button',
		parentContainerSelector,
		{ symbol: ticker }
	);

	// Reload the portoflio tickers
	const parentPortfolioContainerSelector = `[data-template-container="portfolio-wrapper"]`;
	window.dynamicPartials.loadTemplateAjax(
		'stock-templates/blocks/part-portfolio-list',
		parentPortfolioContainerSelector,
		{}
	);
};

export const setupAddToPortfolio = () => {
	// expose the handle to the DOM so we can call it on onclick of the buttons, which is cleaner.
	// see the php view 'partial-add-to-portfolio-button.php'. The data attributes tot he button must be there
	window.handleAddRemoveFromPortfolio = handleAddRemoveFromPortfolio;
};
