/**
 * Enable submit button.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.8.3
 *
 * @param  {Object} submitBtn Submit DOM element.
 */
window.ctctEnableBtn = function (submitBtn) {
    submitBtn.removeAttribute('disabled');
};

/**
 * Disable submit button.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.8.3
 *
 * @param  {Object} submitBtn Submit DOM element.
 */
window.ctctDisableBtn = function (submitBtn) {
    submitBtn.setAttribute('disabled', 'disabled');
}


window.renderReCaptcha = function () {
    let grecaptchas = document.querySelectorAll( '.g-recaptcha' );

    Array.from(grecaptchas).forEach(function (grecaptchaobj, index) {
        let submitBtn = '';
        const siblings = [...grecaptchaobj.parentElement.children];
        siblings.forEach(function(item){
            if ( item.classList.contains('ctct-form-field-submit') ) {
                submitBtn = document.querySelector("#" + item.children[0].id);
            }
        });
        grecaptcha.render(grecaptchaobj, {
            'sitekey'         : grecaptchaobj.getAttribute('data-sitekey', ''),
            'size'            : grecaptchaobj.getAttribute('data-size', ''),
            'tabindex'        : grecaptchaobj.getAttribute('data-tabindex', ''),
            'callback'        : function () {
                if ( submitBtn ) {
                    window.ctctEnableBtn(submitBtn);
                }
            },
            'expired-callback': function () {
                if ( submitBtn ) {
                    window.ctctDisableBtn(submitBtn);
                }
            },
            'isolated'        : true,
        });
    });
};
