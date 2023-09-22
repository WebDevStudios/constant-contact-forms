/**
 * General-purpose utility stuff for CC plugin.
 */
( function() {
	window.addEventListener('load', function () {
		let buttons = document.querySelectorAll('.ctct-submitted');
		if (buttons) {
			buttons.forEach( (button, index ) => {
				button.addEventListener('click', (e) => {
					setTimeout(() => {
						disableSendButton( button );
						setTimeout(() => { enableSendButton(button) }, 3000);
					}, 100);
				});
			} );
		}
	} );

	/**
	 * Disable form submit button.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @return {mixed} jQuery if attribute is set, undefined if not.
	 */
	function disableSendButton( button ) {
		button.setAttribute('disabled', 'disabled');
	}

	/**
	 * Re-enable form submit buttons.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 *
	 * @return {mixed} jQuery if attribute is set, undefined if not.
	 */
	function enableSendButton( button ) {
		button.removeAttribute('disabled');
	}
} () );
