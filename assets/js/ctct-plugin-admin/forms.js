window.CTCTForms = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
	}

	// Cache all the things.
	that.cache = function() {
		that.$c = {
			window: $( window ),
			body: $( 'body' ),
			disconnect: '.ctct-disconnect',
		};
	}

	// Combine all events.
	that.bindEvents = function() {

        $( that.$c.disconnect ).on( 'click', function(e) {
			confirm( ctct_texts.disconnectconfirm );
		});
    }

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTForms );
