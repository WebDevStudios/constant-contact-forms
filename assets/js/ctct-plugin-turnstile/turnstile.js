/**
 * Enable submit button.
 *
 * @since 2.15.1
 *
 * @param {Object} submitBtn Submit DOM element.
 */
window.ctctTurnstileEnableBtn = function (submitBtn) {
	submitBtn.removeAttribute('disabled');
};

/**
 * Disable submit button.
 *
 * @since 2.15.1
 *
 * @param {Object} submitBtn Submit DOM element.
 */
window.ctctTurnstileDisableBtn = function (submitBtn) {
	submitBtn.setAttribute('disabled', 'disabled');
}

/**
 * Render turnstiles.
 *
 * @since 2.15.1
 *
 */
window.onload = function () {
	let turnstiles = document.querySelectorAll( '.turnstile' );

	Array.from(turnstiles).forEach(function (turnstileobj) {
			let submitBtn = '';
			const siblings = [...turnstileobj.parentElement.children];
			siblings.forEach(function(item){
					if ( item.classList.contains('ctct-form-field-submit') ) {
							submitBtn = document.querySelector("#" + item.children[0].id);
					}
			});
			turnstile.render(turnstileobj, {
					'sitekey'  : turnstileobj.getAttribute('data-sitekey', ''),
					'size'     : turnstileobj.getAttribute('data-size', ''),
					'tabindex' : turnstileobj.getAttribute('data-tabindex', ''),
					'callback' : function () {
							if ( submitBtn ) {
								window.ctctTurnstileEnableBtn(submitBtn);
							}
					},
					'expired-callback': function () {
							if ( submitBtn ) {
								window.ctctTurnstileDisableBtn(submitBtn);
							}
					},
					'isolated' : true,
			});
	});
};
