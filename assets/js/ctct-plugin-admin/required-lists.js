window.CTCTRequiredLists = {};

(function (window, app) {

	/**
	 * @constructor
	 */
	app.init = () => {
		app.cache();
		app.bindEvents();
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.cache = () => {
		app.cache = {
			publishButton: document.querySelector('#publish') ?? '',
			status       : ctct_admin_required_lists,
			noListMessage: ctctTexts.no_selected_list,
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.bindEvents = () => {
		if (app.cache.publishButton) {
			app.cache.publishButton.addEventListener('click', (event) => {
				if (!app.maybeAlert()) {
					return;
				}

				event.preventDefault();
				alert(app.cache.noListMessage);
			});
		}
	};

	/**
	 * Determine if we should show an alert.
	 *
	 * @returns {boolean}
	 */
	app.maybeAlert = () => {
		let should_alert = false;

		// Let it act like a basic contact form.
		if (!app.cache.status.is_connected) {
			return should_alert;
		}

		// If the current form has emails disabled or
		// the setting is disabling
		if (
			app.currentFormEmailDisabled() ||
			app.cache.status.settings_email_disabled
		) {
			// but only if we don't have a list already set.
			if (false === app.hasLists()) {
				should_alert = true;
			}
		}

		// We have a list, don't alert.
		if (true === app.hasLists()) {
			should_alert = false;
		}

		return should_alert;
	};

	/**
	 * Check if our disable emails checkbox is checked.
	 *
	 * @returns bool
	 */
	app.currentFormEmailDisabled = () => {
		return document.querySelector('#_ctct_disable_emails_for_form').checked;
	}

	/**
	 * Check if we have one to many lists chosen.
	 *
	 * @returns {boolean}
	 */
	app.hasLists = () => {
		let lists = document.querySelectorAll('#cmb2-metabox-ctct_0_list_metabox .attached-posts-wrap .attached li');

		return lists.length > 0;
	}

	/**
	 * 3...2...1...Contact Constantly!
	 */
	app.init();
}(window, window.CTCTRequiredLists));
