window.CTCTForms = {};

( function( window, that ) {

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
		that.cache = {
			window: window,
			disconnect: '.ctct-disconnect',
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.bindEvents = () => {
		let disconnect = document.querySelectorAll(that.cache.disconnect);
		if (disconnect) {
			disconnect.addEventListener('click', () => {
				return confirm(window.ctctTexts.disconnectconfirm);
			});
		}
	};

	that.init();

} ( window, window.CTCTForms ) );
