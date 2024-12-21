import domReady from '@wordpress/dom-ready';

domReady( () => {
	const blockSelector = '[data-template-container="part-example-template-with-js"]';

	const debounce = ( func, wait ) => {
		let timeout;
		return ( ...args ) => {
			clearTimeout( timeout );
			timeout = setTimeout( () => func.apply( this, args ), wait );
		};
	};

	const inputs = document.querySelectorAll( `${ blockSelector } input.radius-input` );

	inputs.forEach( ( input ) => {
		// input.addEventListener( 'change', () => {
		// 	if ( typeof input.form.onsubmit === 'function' ) {
		// 		input.form.onsubmit( input.form );
		// 	}
		// } );

		input.addEventListener(
			'input',
			debounce( () => {
				if ( typeof input.form.onsubmit === 'function' ) {
					input.form.onsubmit( input.form );
				}
			}, 500 )
		);
	} );
} );
