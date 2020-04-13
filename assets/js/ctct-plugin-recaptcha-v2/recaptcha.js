var renderReCaptcha = function() {
    jQuery( '.g-recaptcha' ).each( function( index, el ) {
        grecaptcha.render( el, {
            'sitekey': jQuery( el ).attr( 'data-sitekey' ),
            'size': jQuery( el ).attr( 'data-size' ),
            'tabindex': jQuery( el ).attr( 'data-tabindex' ),
            'callback': jQuery( el ).attr( 'data-callback' ),
            'expired-callback': jQuery( el ).attr( 'data-expired-callback' ),
        } );
    } );
};
window.renderReCaptcha = renderReCaptcha;
