import domReady from '@wordpress/dom-ready';

domReady( () => {
	const button = document.getElementById( 'sample-button' );
	if ( button ) {
		button.addEventListener( 'click', ( e ) => {
			window.dynamicPartials.loadTemplateAjaxInModal(
				'dynamic-partial-templates/sub-templates/partial-example-subpartial',
				{ number: 6371000, modalTitle: `Diameter of the Earth (radius: 6371000)` }
			);

			document.querySelector( '#modal-dialog' ).showModal();
		} );
	}
} );
