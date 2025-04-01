/**
 * Enable submit button.
 *
 * @since 2.9.0
 *
 * @param {Object} submitBtn Submit DOM element.
 */
window.ctcthCaptchaEnableBtn = function (submitBtn) {
	submitBtn.removeAttribute('disabled');
};

/**
 * Disable submit button.
 *
 * @since 2.9.0
 *
 * @param {Object} submitBtn Submit DOM element.
 */
window.ctcthCaptchaDisableBtn = function (submitBtn) {
	submitBtn.setAttribute('disabled', 'disabled');
}

/**
 * Render hCaptchas.
 *
 * @since 2.9.0
 *
 */
window.renderhCaptcha = function () {
	let hcaptchas = document.querySelectorAll( '.h-captcha' );

	Array.from(hcaptchas).forEach(function (hcaptchaobj) {
			let submitBtn = '';
			const siblings = [...hcaptchaobj.parentElement.children];
			siblings.forEach(function(item){
					if ( item.classList.contains('ctct-form-field-submit') ) {
							submitBtn = document.querySelector("#" + item.children[0].id);
					}
			});
			hcaptcha.render(hcaptchaobj, {
					'sitekey'  : hcaptchaobj.getAttribute('data-sitekey', ''),
					'size'     : hcaptchaobj.getAttribute('data-size', ''),
					'tabindex' : hcaptchaobj.getAttribute('data-tabindex', ''),
					'callback' : function () {
							if ( submitBtn ) {
								window.ctcthCaptchaEnableBtn(submitBtn);
							}
					},
					'expired-callback': function () {
							if ( submitBtn ) {
								window.ctcthCaptchaDisableBtn(submitBtn);
							}
					},
					'isolated'        : true,
			});
	});
};
