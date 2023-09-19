/**
 * Enable submit button.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.8.3
 *
 * @param  {Object} submitBtn Submit DOM element.
 */
window.ctctEnableBtn = function (submitBtn) {
    submitBtn.setAttribute('disabled', false);
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
    submitBtn.setAttribute('disabled', true);
}

var renderReCaptcha = function() {
    jQuery( '.g-recaptcha' ).each( function( index, el ) {
        const submitBtn = jQuery( el ).siblings( '.ctct-form-field-submit' ).find( '.ctct-submit' );

        grecaptcha.render( el, {
            'sitekey': jQuery( el ).attr( 'data-sitekey' ),
            'size': jQuery( el ).attr( 'data-size' ),
            'tabindex': jQuery( el ).attr( 'data-tabindex' ),
            'callback': function() {
                window.ctctEnableBtn( submitBtn );
            },
            'expired-callback': function() {
                window.ctctDisableBtn( submitBtn );
            },
            'isolated': true,
        } );
    } );
};
window.renderReCaptcha = renderReCaptcha;
