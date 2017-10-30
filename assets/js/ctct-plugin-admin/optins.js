window.CTCT_OptIns = {};
( function( window, $, app ) {

	// Constructor
	app.init = function() {
		app.cache();
		app.bindEvents();
	};

	// Cache all the things
	app.cache = function() {
		app.$c = {
			optin_no_conn : $( '#cmb2-metabox-ctct_1_optin_metabox #_ctct_opt_in_not_connected' ),
			list          : $( '#cmb2-metabox-ctct_0_list_metabox #_ctct_list' ),

			title         : $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id-email-optin-title' ),
			optin         : $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in' ),
			instruct      : $( '#cmb2-metabox-ctct_1_optin_metabox .cmb2-id--ctct-opt-in-instructions' ),
		};
	};

	// Combine all events
	app.bindEvents = function() {

		// Only fire show/hide if we have the normal checkbox
		if ( app.$c.optin_no_conn.length ) {

			// Fire once to get our loaded state set up
			app.toggleNoConnectionFields();

			// Bind to fire when needed
			app.$c.optin_no_conn.change( function() {
				app.toggleNoConnectionFields();
			});
		}

		// Only fire show/hide if we have the normal checkbox
		if ( app.$c.list.length ) {

			// Fire once to get our loaded state set up
			app.toggleConnectionFields();

			// Bind to fire when needed
			app.$c.list.change( function() {
				app.toggleConnectionFields();
			});
		}
	};

	// Toggle un-needed optin fields if we're not showing the opt-in.
	// this runs for the not-connected fields
	app.toggleNoConnectionFields = function() {

		// If checked, show them, else hide it.
		if ( app.$c.optin_no_conn.prop( 'checked' ) ) {
			app.$c.instruct.slideDown();
		} else {
			app.$c.instruct.slideUp();
		}
	}

	// Toggle un-needed optin fields if we're not showing the opt-in.
	// this runs for our connected fields
	app.toggleConnectionFields = function() {

		// If checked, show them, else hide it.
		if ( '' != app.$c.list.val() ) {
			app.$c.title.slideDown();
			app.$c.optin.slideDown();
			app.$c.instruct.slideDown();
		} else {
			app.$c.title.slideUp();
			app.$c.optin.slideUp();
			app.$c.instruct.slideUp();
		}
	}

	// Engage
	$( app.init );

})( window, jQuery, window.CTCT_OptIns );
