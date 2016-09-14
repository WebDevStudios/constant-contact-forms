/**
 * General purpose utility stuff for CC plugin.
 */
(function( global, $ ){
	/**
	 * Temporarily prevent the submit button from being clicked.
	 */
	$( document ).ready( function() {
		$( '#ctct-submitted' ).on( 'click', function() { 
			setTimeout( function() {
				disable_send_button();
				setTimeout( enable_send_button, 3000 );
			}, 100 );
		} );
	} );
	
	function disable_send_button() {
		return $( '#ctct-submitted' ).attr( 'disabled', 'disabled' );
	}

	function enable_send_button() {
		return $( '#ctct-submitted' ).attr( 'disabled', null );
	}
})( window, jQuery );

window.CTCTSupport = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
		that.removePlaceholder();
	}

	that.removePlaceholder = function() {
		$( '.ctct-form-field input,textarea' ).focus( function() {
			$( this ).data( 'placeholder', $( this ).attr( 'placeholder' ) ).attr( 'placeholder','' );
		}).blur( function() {
			$( this ).attr( 'placeholder', $( this ).data( 'placeholder' ) );
		});
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
		$( that.$c.form ).on( 'click', 'input[type=submit]', function() {

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
