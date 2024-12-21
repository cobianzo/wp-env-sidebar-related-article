// @TODO: move this into a generic lib.

// Función de debounce (Helper)
export const debounce = ( func, delay ) => {
	let timeoutId;
	return ( ...args ) => {
		clearTimeout( timeoutId );
		timeoutId = setTimeout( () => func.apply( null, args ), delay );
	};
};

/**
 * Given data, shows it tabulated. Works ok but we are not using it.
 * @param {*} data.    keys are the first column, every item is an object where keys are the columns
 * @param     data     { "2001": { "title_1" : "ok", "title_2" : "not so good"}, "2002": : { "title_1" : "well" ...}
 * @param {*} selector the dom element where we load the table
 * @param {*} options  allows you to rename the heading and append text to the cells
 */
export const showTabulatedData = async ( data, selector, options = null ) => {
	const formdata = new FormData();
	formdata.append( 'action', 'generate_table' );
	formdata.append( 'nonce', myJS.nonce );
	formdata.append( 'data', JSON.stringify( data ) );

	if ( options ) {
		formdata.append( 'options', JSON.stringify( options ) );
	}

	try {
		const response = await fetch( myJS.ajaxurl, {
			method: 'POST',
			body: formdata,
		} );
		const result = await response.json();

		if ( result.success ) {
			document.querySelector( selector ).innerHTML = result.data;
			return;
		}
		console.error( 'Error en la respuesta:', result.data );
	} catch ( error ) {
		console.error( 'Error en la solicitud AJAX:', error );
	}
};
