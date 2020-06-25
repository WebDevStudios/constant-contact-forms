var ctctEnableBtn = function( index ) {
    jQuery( jQuery( ".ctct-submit" )[ index ] ).attr( "disabled", false );
}
window.ctctEnableBtn = ctctEnableBtn;

var ctctDisableBtn = function( index ) {
    jQuery( jQuery( ".ctct-submit" )[ index ] ).attr( "disabled", "disabled" );
}
window.ctctDisableBtn = ctctDisableBtn;

var renderReCaptcha = function() {
    jQuery( '.g-recaptcha' ).each( function( index, el ) {
        grecaptcha.render( el, {
            'sitekey': jQuery( el ).attr( 'data-sitekey' ),
            'size': jQuery( el ).attr( 'data-size' ),
            'tabindex': jQuery( el ).attr( 'data-tabindex' ),
            'callback': function() {
                window.ctctEnableBtn( index );
            },
            'expired-callback': function() {
                window.ctctDisableBtn( index );
            },
            'isolated': true,
        } );
    } );
};
window.renderReCaptcha = renderReCaptcha;
