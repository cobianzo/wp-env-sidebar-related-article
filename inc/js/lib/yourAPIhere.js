import apiFetch from '@wordpress/api-fetch';

// @TODO: this API must be moved to a generic folder as a lib, with other generic functions
export const getWhatever = async ( searchTerm ) => {
	// using internal call to our PHP
	try {
		const response = await apiFetch( {
			path: '/stock-api/search?ticker=' + searchTerm,
		} );
		window.dynamicPartials.log( 'TODELETE: API Response:', response );
		return response; // returns array of objects, each one a stock..
	} catch ( error ) {
		window.dynamicPartials.err( 'Internal WP API Error on custom endpoint:', error );
		throw error;
	}
};
