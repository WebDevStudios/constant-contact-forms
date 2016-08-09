window.CTCT_OptIns = {};
( function( window, $, app ) {

	// Constructor
	app.init = function() {
		app.cache();
		app.bindEvents();
		app.toggleOptInFields();
	};

	// Cache all the things
	app.cache = function() {
		app.$c = {
			optin         : $( '#_ctct_opt_in' ),
			optin_no_conn : $( '#_ctct_opt_in_not_connected' ),
			list          : $( '.cmb2-id--ctct-list' ),
			instruct      : $( '.cmb2-id--ctct-opt-in-instructions' ),
		};
	};

	// Combine all events
	app.bindEvents = function() {
		app.$c.optin_no_conn.change( function() {
			app.toggleOptInFields();
		});
	};

	// Toggle un-needed optin fields if we're not showing the opt-in
	app.toggleOptInFields = function() {

		console.log( app.$c.optin_no_conn );

		// Only fire show/hide if we have the normal checkbox
		if ( app.$c.optin_no_conn.length ) {

			// If checked, show them
			if ( app.$c.optin_no_conn.prop( 'checked' ) ) {
				app.$c.instruct.slideDown();
			} else {
				app.$c.instruct.slideUp();
			}
		}
	}

	// Engage
	$( app.init );

})( window, jQuery, window.CTCT_OptIns );
