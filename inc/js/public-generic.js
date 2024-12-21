// Public JS files loaded in every page.

// eslint-disable-next-line no-console
console.log( '%c Public Js loaded ... ', 'background:green;color:white' );

/** TODLETE */
window.test = async function () {
	window.dynamicPartials.loadTemplateAjaxInModal(
		'dynamic-partial-templates/blocks/part-example-template-with-js',
		{ number: 10, modalTitle: 'TEsT TiTle' }
	);

	document.querySelector( '#modal-dialog' ).showModal();
};
