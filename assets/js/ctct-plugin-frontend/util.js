/**
 * General-purpose utility stuff for CC plugin.
 */
( function() {
	window.addEventListener('load', function () {
		let button = document.querySelector('.ctct-submitted');

		if (null !== button) {
			console.log('button 1');
			button.addEventListener('click', (e) => {
				setTimeout(() => {
					disableSendButton();
					setTimeout(enableSendButton, 3000);
				}, 100);
			});
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
	function disableSendButton() {
		let button = document.querySelector('.ctct-submitted');
		if (null !== button) {
			console.log('button 2');
			button.setAttribute('disabled', 'disabled');
		}
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
		let button = document.querySelector('.ctct-submitted');
		if (null !== button) {
			console.log('button 3');
			button.setAttribute('disabled', null);
		}
	}
} () );
