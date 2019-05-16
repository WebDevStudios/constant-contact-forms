window.CTCTForms = {};

( function( window, $, that ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {
		that.cache();
		that.bindEvents();
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.cache = () => {
		that.$c = {
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
	that.bindEvents = () => {

		$( that.$c.disconnect ).on( 'click', ( e ) => { // eslint-disable-line no-unused-vars
			confirm( window.ctctTexts.disconnectconfirm );
		} );
	};

	$( that.init );

} ( window, jQuery, window.CTCTForms ) );
