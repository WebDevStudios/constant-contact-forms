window.CTCTRequiredLists = {};

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
		app.initialDisabledPublish();
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.cache = () => {
		app.cache = {
			window       : window,
			publishButton: document.querySelector('#publish'),
			status: window.ctct_admin_required_lists,
			initialLists: document.querySelectorAll('#cmb2-metabox-ctct_0_list_metabox .attached-posts-wrap .retrieved li')
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.11.0
	 */
	app.bindEvents = () => {
		// Upon initial load of our builder page.
		if (app.initialDisabledPublish() ) {
			app.cache.publishButton.setAttribute('disabled', 'disabled');
		}

		let pluses = document.querySelectorAll('.retrieved-wrap .add-remove');
		if(pluses){
			Array.from(pluses).forEach( (plus) => {
				plus.addEventListener( 'click', (event) => {
					app.maybeEnablePublish();
				});
			});
		}
	};

	/**
	 * Maybe disable the publish button initially.
	 *
	 * @returns {boolean}
	 */
	app.initialDisabledPublish = () => {
		let should_disable = false;
		// We're not connected, so we do not risk losing anything.
		if (!app.cache.status.is_connected) {
			return should_disable;
		}

		if (
			app.cache.status.current_form_email_disabled ||
			app.cache.status.settings_email_disabled
		) {
			should_disable = true;
		}
		if (
			app.cache.initialLists.length === 0
		) {
			should_disable = true;
		}

		return should_disable;
	}

	app.maybeEnablePublish = () => {
		//not finding correct amount on initial click
		let newListCount = document.querySelectorAll('#cmb2-metabox-ctct_0_list_metabox .attached-posts-wrap .attached li');
	};

	app.init();

	/**
	 * TODO: enable if "Disable email notifications for this form?" is clicked and result is NOT CHECKED.
	 * TODO: enable if "Associated Lists" is not empty.
	 */

}(window, window.CTCTRequiredLists));
