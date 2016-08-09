window.CTCTModal = {};
( function( window, $, app ) {

    // Constructor
    app.init = function() {
        app.cache();
        app.bindEvents();
    };

    // Cache all the things
    app.cache = function() {
        app.$c = {
            window: $( window ),
            modalSelector: $( '.modal' ),
            modalClose: $( '.modal-close' ),
        };
    };

    // Combine all events
    app.bindEvents = function() {
       app.$c.modalClose.click( function() {
            app.$c.modalSelector.removeClass( 'modal-open' );
        });
    };

    // Engage
    $( app.init );

})( window, jQuery, window.CTCTModal );
