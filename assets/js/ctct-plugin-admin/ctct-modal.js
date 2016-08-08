window.Modal_Object = {};
( function( window, $, app ) {

    // Private variable
    var modal = document.getElementsByClassName('modal');

    // Constructor
    app.init = function() {
        app.cache();

        if ( app.meetsRequirements() ) {
            app.bindEvents();
        }
    };

    // Cache all the things
    app.cache = function() {
        app.$c = {
            window: $(window),
            modalSelector: $( '.modal' ),
        };
    };

    // Combine all events
    app.bindEvents = function() {
        app.$c.window.on( 'load', app.doModal );
    };

    // Do we meet the requirements?
    app.meetsRequirements = function() {
        return app.$c.modalSelector.length;
    };

    // Some function
    app.doModal = function() {
        $(".modal-close").click(function(){
	        $('.modal').removeClass('modal-open');
	    });
    };

    // Engage
    $( app.init );

})( window, jQuery, window.Modal_Object );
