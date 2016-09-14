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
