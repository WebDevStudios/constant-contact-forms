window.CTCT_OptIns = {};

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
			optinNoConn: $( '#cmb2-metabox-ctct_1_optin_metabox #_ctct_opt_in_not_connected' ),
			list: $( '#cmb2-metabox-ctct_0_list_metabox #_ctct_list' ),
			title: $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id-email-optin-title' ),
			optin: $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in' ),
			instruct: $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in-instructions' )
		};
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.bindEvents = () => {

		// Only fire show/hide if we have the normal checkbox.
		if ( app.$c.optinNoConn.length ) {

			// Fire once to get our loaded state set up.
			app.toggleNoConnectionFields();

			// Bind to fire when needed.
			app.$c.optinNoConn.change( () => {
				app.toggleNoConnectionFields();
			} );
		}

		// Only fire show/hide if we have the normal checkbox.
		if ( app.$c.list.length ) {

			// Fire once to get our loaded state set up.
			app.toggleConnectionFields();

			// Bind to fire when needed.
			app.$c.list.change( () => {
				app.toggleConnectionFields();
			} );
		}
	};

	/**
	 * Toggle unnecessary, unconnected optin fields if we're not showing the opt-in.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	app.toggleNoConnectionFields = () => {

		if ( app.$c.optinNoConn.prop( 'checked' ) ) {
			app.$c.instruct.slideDown();
		} else {
			app.$c.instruct.slideUp();
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
		if ( '' !== app.$c.list.val() ) {
			app.$c.title.slideDown();
			app.$c.optin.slideDown();
			app.$c.instruct.slideDown();
		} else {
			app.$c.title.slideUp();
			app.$c.optin.slideUp();
			app.$c.instruct.slideUp();
		}
	};

	$( app.init );

} ( window, jQuery, window.CTCT_OptIns ) );
