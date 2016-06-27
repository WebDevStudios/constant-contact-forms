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

	// Combine all events.
	that.bindEvents = function() {
		$( that.$c.form + ' input' ).keyup( function() {
			clearTimeout( that.timeout )
			that.timeout = setTimeout( function() {
				$.post(
				    ajaxurl,
				    {
				        'action': 'ctct_process_form',
				        'data':   $( that.$c.form ).serialize(),
				    },
				    function(response){
				        console.log( response );
				    }
				);
			}, 500 )
		})
    }


	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTSupport );
