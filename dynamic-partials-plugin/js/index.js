import domReady from '@wordpress/dom-ready';

domReady( () => {
	const forms = document.querySelectorAll( 'form[data-dynamic-template-reload]' );
	forms.forEach( ( form ) => {
		form.addEventListener( 'submit', async ( e ) => {
			await window.dynamicPartials.handleSubmitFormAndLoadTemplateAjax( e );
		} ); // end add event listener
	} ); // end loop forms
} );
