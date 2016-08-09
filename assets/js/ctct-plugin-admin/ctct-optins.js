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
			optin    : $( '#_ctct_opt_in' ),
			list     : $( '.cmb2-id--ctct-list' ),
			default  : $( '.cmb2-id--ctct-opt-in-default' ),
			hide     : $( '.cmb2-id--ctct-opt-in-hide' ),
			instruct : $( '.cmb2-id--ctct-opt-in-instructions' ),
		};
	};

	// Combine all events
	app.bindEvents = function() {
		$( '#_ctct_opt_in' ).change( function() {
			app.toggleOptInFields();
		});
	};

	// Toggle un-needed optin fields if we're not showing the opt-in
	app.toggleOptInFields = function() {

		//@TODO modify here for new opt in methods
		//Set up our optin selector

		// Only fire show/hide if we have the normal checkbox
		if ( app.$c.optin.length && false ) {
			// If checked, show them
			if ( app.$c.optin.prop( 'checked' ) ) {
				that.$c.optinfields.list.show();
				that.$c.optinfields.default.show();
				that.$c.optinfields.hide.show();
				that.$c.optinfields.instruct.show();
			} else {
				that.$c.optinfields.list.hide();
				that.$c.optinfields.default.hide();
				that.$c.optinfields.hide.hide();
				that.$c.optinfields.instruct.hide();
			}
		}
	}

	// Engage
	$( app.init );

})( window, jQuery, window.CTCT_OptIns );
