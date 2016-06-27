window.CTCTSupport = {};
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
			hide: '.answer',
		};
	}

	// Combine all events.
	that.bindEvents = function() {
		$( that.$c.hide ).hide();
        // Show fields based on selection
        $('.question').on( 'click', function(e) {
			that.metaShowHide( $(this).next('.answer') );
		});
    }

    // Function to handle which items should be showing/hiding
    that.metaShowHide = function(showem) {
        var hideThese = $( that.$c.hide ).not(showem);
        showem.slideDown('fast');
        hideThese.hide();
    }

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTSupport );
