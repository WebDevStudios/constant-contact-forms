window.CTCTForms = {};

( function( window, $, app ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.init = () => {
		app.cache();
		app.bindEvents();
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.cache = () => {
		app.$c = {
			window: $( window ),
			body: $( 'body' ),
			disconnect: '.ctct-disconnect'
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {

		$( app.$c.disconnect ).on( 'click', ( e ) => { // eslint-disable-line no-unused-vars
			confirm( window.ctctTexts.disconnectconfirm );
		} );
	};

	$( app.init );

} ( window, jQuery, window.CTCTForms ) );
