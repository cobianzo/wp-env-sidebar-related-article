/**
 * Handles the form submission and loads the template via AJAX.
 *
 * Usage:
 * <form data-dynamic-template-reload="part-edit-contribution-list">
 * 	must include input 'action', input 'nonce' + the args for the php template as inputs
 *
 * The ajax action in the input will be executed in php
 * After the action is finished, we can reload certain templates separated by comma in
 * the attr 'data-dynamic-template-reload'. The templates are dynamic-partial-templates/blocks
 * TODO: when this form is regenerated we need to make it work
 *
 */
import domReady from '@wordpress/dom-ready';

window.dynamicPartials = {
	// @BOOK:LOADTEMPLATEAJAX
	// Usage: see below
	loadTemplateAjax: async ( templateName, containerSelector, args = [] ) => {
		const containerAll = document.querySelectorAll( containerSelector );
		if ( ! containerAll ) {
			window.dynamicPartials.err(
				'loadTemplateAjax called but we didnt find the container:',
				containerSelector
			);
			return;
		}

		const formdata = new FormData();
		formdata.append( 'action', 'load_template_ajax' );
		formdata.append( 'template_name', templateName );
		formdata.append( 'args', JSON.stringify( args ) );
		formdata.append( 'nonce', window.myJS.nonce );

		try {
			const response = await fetch( window.myJS.ajaxurl, {
				method: 'POST',
				body: formdata,
			} );

			const result = await response.json();

			if ( result.success ) {
				containerAll.forEach( ( container ) => {
					container.innerHTML = result.data;
				} );
			} else {
				window.dynamicPartials.err(
					'Error en la respuesta loadTemplateAjax:',
					formdata,
					result.data
				);
			}
		} catch ( error ) {
			window.dynamicPartials.err(
				`Error en la solicitud AJAX loadTemplateAjax:`,
				formdata,
				templateName,
				error
			);
		} // end try/catch
	},

	/**
	 * // @BOOK:LOADTEMPLATEAJAX
	 * Usage:
	 * <form data-dynamic-templates-reload="part-edit-contribution-list">
	 * 	must include input 'action', input 'nonce' + the args for the php template as inputs
	 *
	 * The ajax action in the input will be executed in php
	 * After the action is finished, we can reload certain templates separated by comma in
	 * the attr 'data-dynamic-template-reload'. The templates are dynamic-partial-templates/blocks
	 * TODO: when this form is regenerated we need to make it work
	 *
	 * @param {Event} e - The form submit event.
	 */
	handleSubmitFormAndLoadTemplateAjax: async ( e ) => {
		e.preventDefault();
		const sform = e.target;
		const formdata = new FormData( sform );
		const params = {};
		for ( const [ key, value ] of formdata.entries() ) {
			params[ key ] = value;
		}

		window.dynamicPartials.log( 'Retrieved params:', params );
		let templateNames = sform.getAttribute( 'data-dynamic-templates-reload' );
		let subtemplateNames = sform.getAttribute( 'data-dynamic-subtemplates-reload' );
		if ( ! templateNames ) {
			// if the template names is not in the data attribute,
			// let's see if it's in the hidden input
			if ( params.template_names ) {
				templateNames = params.template_names;
			} else templateNames = '';
		}
		if ( ! subtemplateNames ) {
			if ( params.subtemplate_names ) {
				subtemplateNames = params.subtemplate_names;
			} else subtemplateNames = '';
		}
		const templateNamesArray = templateNames.split( ',' );
		const subtemplateNamesArray = subtemplateNames.split( ',' );
		window.dynamicPartials.log( 'TODELETE form DAta', formdata );

		// good point to debug if there are errors. check [...formdata.entries()];
		fetch( window.myJS.ajaxurl, {
			method: 'POST',
			body: formdata,
		} )
			.then( ( response ) => response.json() )
			.then( ( data ) => {
				window.dynamicPartials.log( `TODELETE ajax part 'handleSub...':`, data );
				if ( data.success ) {
					window.dynamicPartials.log( 'Ticker contribution added:', data.data );
					const ajaxParams = Object.fromEntries( formdata );
					templateNamesArray.forEach( ( templateName ) => {
						if ( ! templateName.length ) return;
						window.dynamicPartials.loadTemplateAjax(
							`dynamic-partial-templates/blocks/${ templateName.trim() }`,
							`[data-template-container="${ templateName.trim() }"]`,
							ajaxParams
						);
					} );
					subtemplateNamesArray.forEach( ( templateName ) => {
						if ( ! templateName.length ) return;
						window.dynamicPartials.loadTemplateAjax(
							`dynamic-partial-templates/sub-templates/${ templateName.trim() }`,
							`[data-subtemplate-container="${ templateName.trim() }"]`,
							ajaxParams
						);
					} );
				}
			} )
			.catch( ( error ) => {
				window.dynamicPartials.err(
					`Error in fetching information for the action: "${ params.action }":`,
					error,
					params
				);
			} );
	},

	// Apply the handler to all forms with the attr `data-dynamic-template-reload`
	submitFormAndLoadTemplateAjax: async () => {
		const forms = document.querySelectorAll( 'form[data-dynamic-template-reload]' );
		forms.forEach( ( form ) => {
			form.addEventListener( 'submit', async ( e ) => {
				await window.dynamicPartials.handleSubmitFormAndLoadTemplateAjax( e );
			} ); // end add event listener
		} ); // end loop forms
	},

	log: ( ...args ) => {
		// @TODO: log only if no prod
		// eslint-disable-next-line no-console
		console.log( 'Dynamic Partials:', ...args );
	},

	err: ( ...args ) => {
		// @TODO: log only if no prod
		// eslint-disable-next-line no-console
		console.error( 'Dynamic Partials:', ...args );
	},
};

domReady( () => {
	// async
	window.dynamicPartials.submitFormAndLoadTemplateAjax();
} );
