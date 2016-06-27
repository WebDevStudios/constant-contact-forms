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
				confirm( 'Are you sure you want to disconnect?' );
			});
        }

		// Engage!
		$( that.init );

})( window, jQuery, window.CTCTForms );
