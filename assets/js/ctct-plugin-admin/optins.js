window.CTCT_OptIns = {};

(function (window, app) {

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
		app.cache = {
			optinNoConn: document.querySelectorAll('#cmb2-metabox-ctct_1_optin_metabox #_ctct_opt_in_not_connected'),
			list       : document.querySelectorAll('#cmb2-metabox-ctct_0_list_metabox .attached-posts-wrap .retrieved li'),
			title      : document.querySelectorAll('#cmb2-metabox-ctct_1_optin_metabox .cmb2-id-email-optin-title'),
			optin      : document.querySelectorAll('#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in'),
			instruct   : document.querySelectorAll('#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in-instructions')
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {

		if (app.cache.optinNoConn.length) {

			app.toggleNoConnectionFields();

			// Bind to fire when needed.
			Array.from(app.cache.optinNoConn).forEach((item) => {
				item.addEventListener('change', () => {
					app.toggleNoConnectionFields();
				});
			});
		} else {
			// Fire once to get our loaded state set up.
			app.toggleConnectionFields();

			// Bind to fire when needed.
			Array.from(app.cache.list).forEach((item) => {
				item.addEventListener('change', () => {
					app.toggleConnectionFields();
				});
			})
		}
	};

	/**
	 * Toggle unnecessary, unconnected optin fields if we're not showing the opt-in.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.toggleNoConnectionFields = () => {
		if (app.cache.optinNoConn.checked) {
			Array.from(app.cache.instruct).forEach((item) => {
				item.style.display = 'block';
			});
		} else {
			Array.from(app.cache.instruct).forEach((item) => {
				item.style.display = 'none';
			});
		}
	};

	/**
	 *  Toggle unnecessary, *connected* optin fields if we're not showing the opt-in.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.toggleConnectionFields = () => {

		// If checked, show them, else hide it.
		if (0 < app.cache.list.length) {
			Array.from(app.cache.title).forEach((item) => {
				item.style.display = 'block';
			});
			Array.from(app.cache.optin).forEach((item) => {
				item.style.display = 'block';
			});
			Array.from(app.cache.instruct).forEach((item) => {
				item.style.display = 'block';
			});
			//app.cache.instruct.slideDown();
		} else {
			Array.from(app.cache.title).forEach((item) => {
				item.style.display = 'none';
			});
			Array.from(app.cache.optin).forEach((item) => {
				item.style.display = 'none';
			});
			Array.from(app.cache.instruct).forEach((item) => {
				item.style.display = 'none';
			});
		}
	};

	app.init();
}(window, window.CTCT_OptIns));
