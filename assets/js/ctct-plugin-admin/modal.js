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
            modalSelector: $( '.ctct-modal' ),
            modalClose: $( '.ctct-modal-close' ),
            textareaModal: $( '#ctct-custom-textarea-modal' ),
            textareaLink: $( '#ctct-open-textarea-info' ),
        };
    };

    // Combine all events
    app.bindEvents = function() {
        app.$c.modalClose.click( function() {
            app.$c.modalSelector.removeClass( 'ctct-modal-open' );
            if ( app.$c.modalSelector.hasClass('ctct-custom-textarea-modal') ) {
                return;
            }
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl,
                data : {
                    action: 'ctct_dismiss_first_modal',
                    'ctct_is_dismissed' : 'true',
                }
            });
        });
        app.$c.textareaLink.on('click', function(){
           app.$c.textareaModal.addClass( 'ctct-modal-open' );
        });
    };

    // Engage
    $( app.init );

})( window, jQuery, window.CTCTModal );
