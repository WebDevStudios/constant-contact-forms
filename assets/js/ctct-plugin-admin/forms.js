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
			disconnect: '.ctct-disconnect',
			redirectToConnectionSettings: '.connection-settings-redirect'
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
			return confirm( window.ctctTexts.disconnectconfirm );
		} );

		$( that.$c.redirectToConnectionSettings ).on( 'click', ( e ) => {
			e.preventDefault();
			window.open(e.target.href, '_blank' )
			window.open(window.ctct_settings.account, '_self' );
		} );
	};

	$( that.init );

} ( window, jQuery, window.CTCTForms ) );
