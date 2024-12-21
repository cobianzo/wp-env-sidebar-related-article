// Public JS files loaded in every page.

// eslint-disable-next-line no-console
console.log( '%c Public Js loaded ... ', 'background:green;color:white' );

window.test = async function () {
	try {
		const formdata = new FormData();
		formdata.append( 'action', 'example_function' );
		formdata.append( 'number', 5 );

		const response = await fetch( myJS.ajaxurl, {
			method: 'POST',
			body: formdata,
		} );

		if ( ! response.ok ) {
			throw new Error( `HTTP error! status: ${ response.status }` );
		}

		const data = await response.json();
		console.log( 'Response:', data );
	} catch ( error ) {
		console.error( 'Error:', error );
	}
};
