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
		let disconnect = document.querySelectorAll(that.cache.disconnect);
		if (disconnect) {
			disconnect.addEventListener('click', () => {
				return confirm(window.ctctTexts.disconnectconfirm);
			});
		}
		let redirectSetting = document.querySelectorAll(that.cache.redirectToConnectionSettings);
		if (redirectSetting) {
			redirectSetting.addEventListener('click', (e) => {
				e.preventDefault();
				window.open(e.target.href, '_blank');
				window.open(window.ctct_settings.account, '_self');
			});
		}
	};

	that.init();

} ( window, window.CTCTForms ) );
