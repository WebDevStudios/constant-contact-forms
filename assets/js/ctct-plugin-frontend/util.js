/**
 * General-purpose utility stuff for CC plugin.
 */
( function( global, $ ) {

	/**
	 * Temporarily prevent the submit button from being clicked.
	 */
	$( document ).ready( () => {

		$( '#ctct-submitted' ).on( 'click', () => {
			setTimeout( () => {
				disableSendButton();
				setTimeout( enableSendButton, 3000 );
			}, 100 );
		} );
	} );

	/**
	 * Disable form submit button.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @return {mixed} jQuery if attribute is set, undefined if not.
	 */
	function disableSendButton() {
		return $( '#ctct-submitted' ).attr( 'disabled', 'disabled' );
	}

	/**
	 * Re-enable form submit buttons.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @return {mixed} jQuery if attribute is set, undefined if not.
	 */
	function enableSendButton() {
		return $( '#ctct-submitted' ).attr( 'disabled', null );
	}

} ( window, jQuery ) );
