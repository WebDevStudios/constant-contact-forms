/**
 * Enable submit button.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.8.3
 *
 * @param  {Object} submitBtn Submit DOM element.
 */
var ctctEnableBtn = function( submitBtn ) {
    jQuery( submitBtn ).attr( "disabled", false );
}
window.ctctEnableBtn = ctctEnableBtn;

/**
 * Disable submit button.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.8.3
 *
 * @param  {Object} submitBtn Submit DOM element.
 */
var ctctDisableBtn = function( submitBtn ) {
    jQuery( submitBtn ).attr( "disabled", "disabled" );
}
window.ctctDisableBtn = ctctDisableBtn;

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
