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
			form: '.ctct-form-wrapper form',
		};
		that.timeout = null;
	}

	that.setAllInputsValid = function() {
		$( that.$c.form + ' .ctct-invalid' ).removeClass( 'ctct-invalid' );
	}

	that.processError = function( error ) {
		// If we have an id property set
		if ( typeof( error.id ) !== 'undefined' ) {
			$( '#' + error.id ).addClass( 'ctct-invalid' );
		}

	}

	// Combine all events.
	that.bindEvents = function() {
		$( that.$c.form + ' input' ).keyup( function() {

			clearTimeout( that.timeout );

			that.timeout = setTimeout( function() {
				$.post(
				    ajaxurl,
				    {
				        'action': 'ctct_process_form',
				        'data':   $( that.$c.form ).serialize(),
				    },
				    function( response ) {

				    	// Make sure we got the 'status' attribut in our response
				    	if ( typeof( response.status ) !== 'undefined' ) {

				    		if ( 'success' == response.status ) {
				    			// Do nothing, we're golden
				    		} else {
				    			// Here we'll want to disable the submit button and
				    			// add some error classes
				    			if ( typeof( response.errors ) !== 'undefined' ) {
				    				that.setAllInputsValid();
				    				response.errors.forEach( that.processError );
				    			}

				    		}
				    	}
				    }
				);
			}, 500 )
		})
    }


	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTSupport );
